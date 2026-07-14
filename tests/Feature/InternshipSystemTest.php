<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Task;
use App\Models\Submission;
use App\Models\InternProfile;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class InternshipSystemTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test login with email or username (NIM).
     */
    public function test_user_can_login_with_email_or_username(): void
    {
        $user = User::create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'username' => '12345678', // NIM
            'password' => Hash::make('secret123'),
            'role' => 'intern',
            'is_first_login' => false,
        ]);

        // Try login with email
        $response1 = $this->post('/login', [
            'login' => 'john@example.com',
            'password' => 'secret123',
        ]);
        $response1->assertRedirect('/dashboard');
        $this->assertAuthenticatedAs($user);

        $this->post('/logout');

        // Try login with username (NIM)
        $response2 = $this->post('/login', [
            'login' => '12345678',
            'password' => 'secret123',
        ]);
        $response2->assertRedirect('/dashboard');
        $this->assertAuthenticatedAs($user);
    }

    public function test_intern_is_forced_to_change_password_on_first_login(): void
    {
        $intern = User::create([
            'name' => 'New Intern',
            'email' => 'intern@example.com',
            'username' => '88888888',
            'password' => Hash::make('88888888'),
            'role' => 'intern',
            'is_first_login' => true,
        ]);

        $this->actingAs($intern);

        // 1. GET dashboard is allowed
        $response = $this->get('/intern/dashboard');
        $response->assertStatus(200);

        $mentor = User::create([
            'name' => 'Mock Mentor',
            'email' => 'mentor@example.com',
            'username' => 'mentor_mock',
            'password' => Hash::make('password'),
            'role' => 'mentor',
            'is_first_login' => false,
        ]);

        // Create a task to prevent route model binding 404
        $task = Task::create([
            'intern_id' => $intern->id,
            'mentor_id' => $mentor->id,
            'title' => 'Mock Task',
            'description' => 'Mock Task Description',
            'due_date' => '2026-12-31 23:59:00',
            'status' => 'pending',
        ]);

        // 2. POST write action is blocked, redirects to /profile
        $responsePost = $this->post("/intern/tasks/{$task->id}/submit", [
            'link_url' => 'https://github.com',
        ]);
        $responsePost->assertRedirect('/profile');
        $responsePost->assertSessionHas('warning');

        // 3. Update password via standard Breeze /password PUT route
        $responseChange = $this->put('/password', [
            'current_password' => '88888888',
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123',
        ]);
        $responseChange->assertSessionHasNoErrors();

        // Check DB update
        $intern->refresh();
        $this->assertFalse($intern->is_first_login);
        $this->assertTrue(Hash::check('newpassword123', $intern->password));
    }

    /**
     * Test Admin CRUD and Intern profile setup.
     */
    public function test_admin_can_manage_users_and_assign_mentor(): void
    {
        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@test.com',
            'username' => 'admin',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'is_first_login' => false,
        ]);

        $mentor = User::create([
            'name' => 'Mentor User',
            'email' => 'mentor@test.com',
            'username' => 'mentor',
            'password' => Hash::make('password'),
            'role' => 'mentor',
            'is_first_login' => false,
        ]);

        $this->actingAs($admin);

        // Create Intern via Admin endpoint
        $response = $this->post('/admin/interns', [
            'name' => 'Ahmad Fauzi',
            'email' => 'ahmad@test.com',
            'nim' => '12121212',
            'university' => 'UI',
            'major' => 'TI',
            'start_date' => date('Y-m-d'),
            'end_date' => date('Y-m-d', strtotime('+3 months')),
            'mentor_id' => $mentor->id,
        ]);

        $response->assertRedirect('/admin/dashboard');

        // Verify user and profile exist in database
        $this->assertDatabaseHas('users', [
            'name' => 'Ahmad Fauzi',
            'username' => '12121212',
            'role' => 'intern',
            'is_first_login' => true,
        ]);

        $createdIntern = User::where('username', '12121212')->first();

        $this->assertDatabaseHas('intern_profiles', [
            'user_id' => $createdIntern->id,
            'university' => 'UI',
            'major' => 'TI',
            'mentor_id' => $mentor->id,
        ]);
    }

    /**
     * Test task assignment, submission, and grading lifecycle.
     */
    public function test_task_lifecycle_and_grading(): void
    {
        $mentor = User::create([
            'name' => 'Mentor Budi',
            'email' => 'budi@test.com',
            'username' => 'budi_mentor',
            'password' => Hash::make('password'),
            'role' => 'mentor',
            'is_first_login' => false,
        ]);

        $intern = User::create([
            'name' => 'Intern Ahmad',
            'email' => 'ahmad@test.com',
            'username' => '12345678',
            'password' => Hash::make('password'),
            'role' => 'intern',
            'is_first_login' => false,
        ]);

        InternProfile::create([
            'user_id' => $intern->id,
            'university' => 'UI',
            'major' => 'TI',
            'start_date' => '2026-06-01',
            'end_date' => '2026-09-01',
            'mentor_id' => $mentor->id,
        ]);

        // 1. Mentor creates a task
        $this->actingAs($mentor);
        $responseTask = $this->post('/mentor/tasks', [
            'intern_id' => $intern->id,
            'title' => 'Tugas Coding Laravel',
            'description' => 'Kerjakan CRUD sederhana',
            'due_date' => now()->addDays(5)->format('Y-m-d H:i:s'),
        ]);
        $responseTask->assertRedirect('/mentor/dashboard');

        $this->assertDatabaseHas('tasks', [
            'title' => 'Tugas Coding Laravel',
            'intern_id' => $intern->id,
            'status' => 'pending',
        ]);

        $task = Task::first();

        // 2. Intern submits task
        $this->actingAs($intern);
        $file = \Illuminate\Http\UploadedFile::fake()->create('tugas.pdf', 500);
        $responseSubmit = $this->post("/intern/tasks/{$task->id}/submit", [
            'file' => $file,
            'link_url' => 'https://github.com/intern/laravel-crud',
        ]);
        $responseSubmit->assertRedirect('/intern/dashboard');

        $this->assertDatabaseHas('submissions', [
            'task_id' => $task->id,
            'link_url' => 'https://github.com/intern/laravel-crud',
            'status' => 'submitted',
        ]);

        $submission = Submission::first();

        // 3. Mentor grades the task
        $this->actingAs($mentor);
        $responseGrade = $this->post("/mentor/submissions/{$submission->id}/grade", [
            'score' => 90,
            'feedback' => 'Bagus sekali!',
        ]);
        $responseGrade->assertRedirect('/mentor/dashboard');

        $this->assertDatabaseHas('submissions', [
            'id' => $submission->id,
            'score' => 90,
            'feedback' => 'Bagus sekali!',
            'status' => 'graded',
        ]);

        $task->refresh();
        $this->assertEquals('graded', $task->status);
    }

    /**
     * Test Master Admin protection (ID 1 cannot be deleted).
     */
    public function test_admin_cannot_delete_master_admin_id_1(): void
    {
        // Create master admin with ID 1
        $masterAdmin = User::create([
            'id' => 1,
            'name' => 'Master Admin',
            'email' => 'master@test.com',
            'username' => 'master',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'is_first_login' => false,
        ]);

        // Create another admin
        $otherAdmin = User::create([
            'id' => 2,
            'name' => 'Other Admin',
            'email' => 'other@test.com',
            'username' => 'other',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'is_first_login' => false,
        ]);

        $this->actingAs($otherAdmin);

        // Try deleting master admin (ID 1) via HTTP -> gets redirected back with error message
        $response = $this->delete('/admin/users/1');
        $response->assertRedirect();
        $response->assertSessionHas('error', 'Gagal: Akun Master Admin tidak boleh dihapus.');

        $this->assertDatabaseHas('users', ['id' => 1]);

        // Try deleting directly via Eloquent -> throws 403 HttpException
        try {
            $masterAdmin->delete();
            $this->fail('Expected HttpException not thrown.');
        } catch (\Symfony\Component\HttpKernel\Exception\HttpException $e) {
            $this->assertEquals(403, $e->getStatusCode());
            $this->assertEquals('Akses Ditolak: Akun Master Admin tidak dapat dihapus dari sistem.', $e->getMessage());
        }
    }

    /**
     * Test Mentor Group Task Assignment.
     */
    public function test_mentor_can_assign_group_tasks(): void
    {
        $mentor = User::create([
            'name' => 'Mentor Group',
            'email' => 'mentor_group@test.com',
            'username' => 'mentor_group',
            'password' => Hash::make('password'),
            'role' => 'mentor',
            'is_first_login' => false,
        ]);

        $intern1 = User::create([
            'name' => 'Intern A',
            'email' => 'interna@test.com',
            'username' => 'nim_a',
            'password' => Hash::make('password'),
            'role' => 'intern',
            'is_first_login' => false,
        ]);

        $intern2 = User::create([
            'name' => 'Intern B',
            'email' => 'internb@test.com',
            'username' => 'nim_b',
            'password' => Hash::make('password'),
            'role' => 'intern',
            'is_first_login' => false,
        ]);

        InternProfile::create([
            'user_id' => $intern1->id,
            'university' => 'UI',
            'major' => 'TI',
            'start_date' => '2026-06-01',
            'end_date' => '2026-09-01',
            'mentor_id' => $mentor->id,
        ]);

        InternProfile::create([
            'user_id' => $intern2->id,
            'university' => 'ITB',
            'major' => 'IF',
            'start_date' => '2026-06-01',
            'end_date' => '2026-09-01',
            'mentor_id' => $mentor->id,
        ]);

        $this->actingAs($mentor);

        $response = $this->post('/mentor/tasks', [
            'intern_ids' => [$intern1->id, $intern2->id],
            'title' => 'Tugas Bersama',
            'description' => 'Kerjakan modul login',
            'due_date' => now()->addDays(5)->format('Y-m-d H:i:s'),
        ]);

        $response->assertRedirect('/mentor/dashboard');

        // Check that 2 tasks were created
        $this->assertDatabaseHas('tasks', [
            'title' => 'Tugas Bersama',
            'intern_id' => $intern1->id,
        ]);

        $this->assertDatabaseHas('tasks', [
            'title' => 'Tugas Bersama',
            'intern_id' => $intern2->id,
        ]);

        // Check that task submissions are pre-created for each task in pending state
        $task1 = Task::where('intern_id', $intern1->id)->first();
        $task2 = Task::where('intern_id', $intern2->id)->first();

        $this->assertDatabaseHas('submissions', [
            'task_id' => $task1->id,
            'status' => 'pending',
        ]);

        $this->assertDatabaseHas('submissions', [
            'task_id' => $task2->id,
            'status' => 'pending',
        ]);
    }

    /**
     * Test optional application letter.
     */
    public function test_admin_can_create_intern_without_application_letter(): void
    {
        $admin = User::create([
            'name' => 'Admin Test',
            'email' => 'adm@test.com',
            'username' => 'adm',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'is_first_login' => false,
        ]);

        $this->actingAs($admin);

        $response = $this->post('/admin/interns', [
            'name' => 'Bambang',
            'email' => 'bambang@test.com',
            'nim' => '11223344',
            'university' => 'ITS',
            'major' => 'SI',
            'start_date' => date('Y-m-d'),
            'end_date' => date('Y-m-d', strtotime('+3 months')),
            'application_letter' => null, // No file
        ]);

        $response->assertRedirect('/admin/dashboard');
        
        $user = User::where('username', '11223344')->first();
        $this->assertNotNull($user);
        $this->assertNull($user->internProfile->application_letter_path);
    }

    /**
     * Test mentor cannot grade pending or null file_path submissions.
     */
    public function test_mentor_cannot_grade_pending_or_null_file_submissions(): void
    {
        $mentor = User::create([
            'name' => 'Mentor A',
            'email' => 'mentor_a@test.com',
            'username' => 'mentor_a',
            'password' => Hash::make('password'),
            'role' => 'mentor',
            'is_first_login' => false,
        ]);

        $intern = User::create([
            'name' => 'Intern A',
            'email' => 'intern_a@test.com',
            'username' => 'intern_a',
            'password' => Hash::make('password'),
            'role' => 'intern',
            'is_first_login' => false,
        ]);

        $task = Task::create([
            'title' => 'Tugas A',
            'description' => 'Deskripsi',
            'due_date' => now()->addDays(2),
            'intern_id' => $intern->id,
            'mentor_id' => $mentor->id,
        ]);

        $submission = Submission::create([
            'task_id' => $task->id,
            'intern_id' => $intern->id,
            'status' => 'pending',
            'file_path' => null,
        ]);

        $this->actingAs($mentor);

        // Grade attempt on pending/null file submission
        $response = $this->post("/mentor/submissions/{$submission->id}/grade", [
            'score' => 90,
            'feedback' => 'Bagus',
        ]);

        $response->assertSessionHasErrors(['score']);
        $this->assertEquals('pending', $submission->fresh()->status);
    }

    /**
     * Test auto-fail expired tasks.
     */
    public function test_auto_fail_expired_tasks(): void
    {
        $mentor = User::create([
            'name' => 'Mentor B',
            'email' => 'mentor_b@test.com',
            'username' => 'mentor_b',
            'password' => Hash::make('password'),
            'role' => 'mentor',
        ]);

        $intern = User::create([
            'name' => 'Intern B',
            'email' => 'intern_b@test.com',
            'username' => 'intern_b',
            'password' => Hash::make('password'),
            'role' => 'intern',
        ]);

        $task = Task::create([
            'title' => 'Expired Task',
            'description' => 'Due in past',
            'due_date' => now()->subDay(), // Past due date
            'intern_id' => $intern->id,
            'mentor_id' => $mentor->id,
        ]);

        $submission = Submission::create([
            'task_id' => $task->id,
            'intern_id' => $intern->id,
            'status' => 'pending',
        ]);

        // Triggering any controller index or autoFailExpiredTasks directly
        Submission::autoFailExpiredTasks();

        $submission = $submission->fresh();
        $this->assertEquals('expired', $submission->status);
        $this->assertEquals(0, $submission->score);
        $this->assertEquals('graded', $submission->task->status);
    }

    /**
     * Test strict date validation rules.
     */
    public function test_strict_date_validation_rules(): void
    {
        $admin = User::create([
            'name' => 'Admin X',
            'email' => 'adminx@test.com',
            'username' => 'adminx',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        $this->actingAs($admin);

        // Creating intern with past start_date should fail validation
        $response = $this->post('/admin/interns', [
            'name' => 'Past Intern',
            'email' => 'past_intern@test.com',
            'nim' => '99887766',
            'university' => 'ITS',
            'major' => 'SI',
            'start_date' => now()->subDay()->format('Y-m-d'), // Past date
            'end_date' => now()->addMonth()->format('Y-m-d'),
        ]);

        $response->assertSessionHasErrors(['start_date']);
    }

    /**
     * Test absolute master admin protection.
     */
    public function test_master_admin_protection(): void
    {
        // Find or create Master Admin (ID 1)
        $masterAdmin = User::find(1);
        if (!$masterAdmin) {
            $masterAdmin = User::create([
                'id' => 1,
                'name' => 'Master Admin',
                'email' => 'admin@admin.com',
                'username' => 'admin',
                'password' => Hash::make('admin123'),
                'role' => 'admin',
                'is_first_login' => false,
            ]);
        }

        // Create secondary admin
        $secondaryAdmin = User::create([
            'name' => 'Sec Admin',
            'email' => 'sec@test.com',
            'username' => 'secadmin',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'is_first_login' => false,
        ]);

        // Try to delete Master Admin as secondary admin -> redirect with error
        $this->actingAs($secondaryAdmin);
        $response = $this->delete("/admin/users/{$masterAdmin->id}");
        $response->assertRedirect();
        $response->assertSessionHas('error', 'Gagal: Akun Master Admin tidak boleh dihapus.');

        // Try to update Master Admin as secondary admin -> redirect with error
        $response = $this->put("/admin/users/{$masterAdmin->id}", [
            'name' => 'Hacked Admin',
            'email' => 'hacked@admin.com',
            'username' => 'hackedadmin',
        ]);
        $response->assertRedirect();
        $response->assertSessionHas('error', 'Gagal: Akun Master Admin tidak boleh diubah.');

        // Try to delete Master Admin as Master Admin themselves via HTTP -> redirect with error
        $this->actingAs($masterAdmin);
        $response = $this->delete("/admin/users/{$masterAdmin->id}");
        $response->assertRedirect();
        $response->assertSessionHas('error', 'Gagal: Akun Master Admin tidak boleh dihapus.');

        // Try to update Master Admin as Master Admin -> allowed
        $response = $this->put("/admin/users/{$masterAdmin->id}", [
            'name' => 'Updated Master Admin',
            'email' => 'admin_new@admin.com',
            'username' => 'admin',
        ]);
        $response->assertRedirect('/admin/dashboard');
        $this->assertEquals('Updated Master Admin', $masterAdmin->fresh()->name);

        // Try updating Master Admin as secondary admin directly via Eloquent -> throws 403 HttpException
        $this->actingAs($secondaryAdmin);
        try {
            $masterAdmin->update(['name' => 'Eloquent Hack']);
            $this->fail('Expected HttpException not thrown.');
        } catch (\Symfony\Component\HttpKernel\Exception\HttpException $e) {
            $this->assertEquals(403, $e->getStatusCode());
            $this->assertEquals('Akses Ditolak: Hanya Master Admin itu sendiri yang boleh mengubah profilnya.', $e->getMessage());
        }
    }

    /**
     * Test export report to Excel.
     */
    public function test_excel_export(): void
    {
        $admin = User::create([
            'name' => 'Admin Test',
            'email' => 'admintest@test.com',
            'username' => 'admintest',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'is_first_login' => false,
        ]);

        \Maatwebsite\Excel\Facades\Excel::fake();

        $response = $this->actingAs($admin)->get('/reports/export-excel');

        $response->assertStatus(200);
        \Maatwebsite\Excel\Facades\Excel::assertDownloaded('Laporan_Magang.xlsx', function (\App\Exports\InternReportExport $export) {
            return true;
        });
    }

    /**
     * Test admin can update an intern (even with a past start date).
     */
    public function test_admin_can_update_intern_with_past_start_date(): void
    {
        $admin = User::create([
            'name' => 'Admin Test',
            'email' => 'admintest@test.com',
            'username' => 'admintest',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'is_first_login' => false,
        ]);

        $intern = User::create([
            'name' => 'Original Intern',
            'email' => 'original@test.com',
            'username' => '11112222',
            'password' => Hash::make('password'),
            'role' => 'intern',
            'is_first_login' => false,
        ]);

        InternProfile::create([
            'user_id' => $intern->id,
            'university' => 'Original Univ',
            'major' => 'Original Major',
            'start_date' => '2026-01-01', // Past start date
            'end_date' => '2026-04-01',
            'status' => 'active',
        ]);

        $this->actingAs($admin);

        $response = $this->post("/admin/interns/{$intern->id}", [
            'name' => 'Updated Name',
            'email' => 'updated@test.com',
            'nim' => '11112222', // Keeping same NIM
            'university' => 'Updated Univ',
            'major' => 'Updated Major',
            'start_date' => '2026-01-01', // Keeps the past start date
            'end_date' => '2026-05-01',
            'status' => 'completed',
        ]);

        $response->assertRedirect('/admin/dashboard');

        $intern->refresh();
        $this->assertEquals('Updated Name', $intern->name);
        $this->assertEquals('updated@test.com', $intern->email);
        $this->assertEquals('Updated Univ', $intern->internProfile->university);
        $this->assertEquals('completed', $intern->internProfile->status);
    }

    /**
     * Test admin can delete a regular intern successfully.
     */
    public function test_admin_can_delete_intern(): void
    {
        $admin = User::create([
            'name' => 'Admin Test',
            'email' => 'admintest@test.com',
            'username' => 'admintest',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'is_first_login' => false,
        ]);

        $intern = User::create([
            'name' => 'Delete Intern',
            'email' => 'delete@test.com',
            'username' => '22223333',
            'password' => Hash::make('password'),
            'role' => 'intern',
            'is_first_login' => false,
        ]);

        InternProfile::create([
            'user_id' => $intern->id,
            'university' => 'Univ',
            'major' => 'Major',
            'start_date' => '2026-07-01',
            'end_date' => '2026-10-01',
        ]);

        $this->actingAs($admin);

        $response = $this->delete("/admin/users/{$intern->id}");
        $response->assertRedirect('/admin/dashboard');

        $this->assertDatabaseMissing('users', ['id' => $intern->id]);
        $this->assertDatabaseMissing('intern_profiles', ['user_id' => $intern->id]);
    }

    /**
     * Test admin can create intern and intern can log in with default NIM/NIP password.
     */
    public function test_intern_default_password_and_login_mapping(): void
    {
        $admin = User::create([
            'name' => 'Admin Test',
            'email' => 'admintest@test.com',
            'username' => 'admintest',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'is_first_login' => false,
        ]);

        $this->actingAs($admin);

        // 1. Create Intern using 'nim' field
        $response1 = $this->post('/admin/interns', [
            'name' => 'Intern One',
            'email' => 'intern1@test.com',
            'nim' => '33334444',
            'university' => 'Univ A',
            'major' => 'Major A',
            'start_date' => date('Y-m-d'),
            'end_date' => date('Y-m-d', strtotime('+3 months')),
        ]);
        $response1->assertRedirect('/admin/dashboard');

        // Logout admin
        $this->post('/logout');

        // Attempt login with NIM (using 'login' or 'username' or 'nim_nip')
        $responseLogin1 = $this->post('/login', [
            'login' => '33334444',
            'password' => '33334444',
        ]);
        $responseLogin1->assertRedirect('/dashboard');
        $this->assertAuthenticated();

        // Logout intern
        $this->post('/logout');

        // Log back in as Admin
        $this->actingAs($admin);

        // 2. Create Intern using 'nim_nip' field instead of 'nim'
        $response2 = $this->post('/admin/interns', [
            'name' => 'Intern Two',
            'email' => 'intern2@test.com',
            'nim_nip' => '55556666',
            'university' => 'Univ B',
            'major' => 'Major B',
            'start_date' => date('Y-m-d'),
            'end_date' => date('Y-m-d', strtotime('+3 months')),
        ]);
        $response2->assertRedirect('/admin/dashboard');

        // Logout admin
        $this->post('/logout');

        // Attempt login with NIM (using 'nim_nip' parameter)
        $responseLogin2 = $this->post('/login', [
            'nim_nip' => '55556666',
            'password' => '55556666',
        ]);
        $responseLogin2->assertRedirect('/dashboard');
        $this->assertAuthenticated();
    }

    /**
     * Test two-way document handling: upload letter by intern, display on dashboard.
     */
    public function test_two_way_document_handling(): void
    {
        \Illuminate\Support\Facades\Storage::fake('public');

        $intern = User::create([
            'name' => 'Document Intern',
            'email' => 'docintern@example.com',
            'username' => '77777777',
            'password' => Hash::make('password123'),
            'role' => 'intern',
            'is_first_login' => false,
        ]);

        $profile = InternProfile::create([
            'user_id' => $intern->id,
            'university' => 'Test University',
            'major' => 'Test Major',
            'start_date' => date('Y-m-d'),
            'end_date' => date('Y-m-d', strtotime('+3 months')),
            'status' => 'active',
        ]);

        $this->actingAs($intern);

        // Upload letter
        $file = \Illuminate\Http\UploadedFile::fake()->create('letter.pdf', 1000); // 1MB PDF
        $response = $this->post(route('intern.upload_letter'), [
            'letter' => $file,
        ]);

        $response->assertRedirect(route('intern.dashboard'));
        $response->assertSessionHas('success');

        // Verify file is stored in public disk under documents/letters/
        $profile->refresh();
        $this->assertNotNull($profile->application_letter_path);
        \Illuminate\Support\Facades\Storage::disk('public')->assertExists($profile->application_letter_path);

        // Verify letter is displayed on intern dashboard
        $dashboardResponse = $this->get(route('intern.dashboard'));
        $dashboardResponse->assertStatus(200);
        $dashboardResponse->assertSee('Lihat Surat');

        // Logout intern and login as admin
        $this->post('/logout');

        $admin = User::create([
            'name' => 'System Admin',
            'email' => 'admin_doc@example.com',
            'username' => 'admin_doc',
            'password' => Hash::make('password123'),
            'role' => 'admin',
            'is_first_login' => false,
        ]);

        $this->actingAs($admin);

        // Verify letter is visible on admin dashboard
        $adminDashboardResponse = $this->get(route('admin.dashboard'));
        $adminDashboardResponse->assertStatus(200);
        $adminDashboardResponse->assertSee('Lihat Surat');
    }
}

