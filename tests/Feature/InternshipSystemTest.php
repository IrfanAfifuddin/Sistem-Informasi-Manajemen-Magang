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
            'start_date' => '2026-06-01',
            'end_date' => '2026-09-01',
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
            'due_date' => '2026-06-20 23:59:00',
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
        $responseSubmit = $this->post("/intern/tasks/{$task->id}/submit", [
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
}
