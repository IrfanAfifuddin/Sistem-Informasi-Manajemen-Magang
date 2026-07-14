<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Task;
use App\Models\Submission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MentorController extends Controller
{
    /**
     * Mentor Dashboard: view mapped interns, recent tasks, and submissions.
     */
    public function index()
    {
        \App\Models\Submission::autoFailExpiredTasks();

        $mentor = Auth::user();

        // Get interns assigned to this mentor
        $interns = User::whereHas('internProfile', function ($query) use ($mentor) {
            $query->where('mentor_id', $mentor->id);
        })->with('internProfile')->get();

        // Get tasks created by this mentor
        $tasks = Task::where('mentor_id', $mentor->id)
            ->with(['intern', 'submissions'])
            ->orderBy('id', 'desc')
            ->get();

        // Submissions pending review
        $submissions = Submission::whereHas('task', function ($query) use ($mentor) {
            $query->where('mentor_id', $mentor->id);
        })->with(['task', 'intern'])->orderBy('created_at', 'desc')->get();

        return view('mentor.dashboard', compact('interns', 'tasks', 'submissions'));
    }

    /**
     * Store a newly created task.
     */
    public function storeTask(Request $request)
    {
        $mentor = Auth::user();

        if ($request->has('intern_id') && !$request->has('intern_ids')) {
            $request->merge([
                'intern_ids' => [$request->input('intern_id')]
            ]);
        }
        
        $request->validate([
            'intern_ids' => ['required', 'array', 'min:1'],
            'intern_ids.*' => ['exists:users,id'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'due_date' => ['required', 'date', 'after_or_equal:today'],
            'attachment' => ['nullable', 'file', 'max:20480'],
        ]);

        $attachmentPath = null;
        if ($request->hasFile('attachment')) {
            $attachmentPath = $request->file('attachment')->store('task_attachments', 'public');
        }

        foreach ($request->intern_ids as $internId) {
            // Verify the intern is indeed connected to this mentor
            $isMyIntern = User::where('id', $internId)
                ->whereHas('internProfile', function ($query) use ($mentor) {
                    $query->where('mentor_id', $mentor->id);
                })->exists();

            if (!$isMyIntern) {
                return back()->withErrors(['intern_ids' => 'Salah satu anak magang tidak ditugaskan kepada Anda.']);
            }

            $task = Task::create([
                'mentor_id' => $mentor->id,
                'intern_id' => $internId,
                'title' => $request->title,
                'description' => $request->description,
                'attachment_path' => $attachmentPath,
                'due_date' => $request->due_date,
                'status' => 'pending',
            ]);

            Submission::create([
                'task_id' => $task->id,
                'intern_id' => $internId,
                'status' => 'pending',
            ]);
        }

        return redirect()->route('mentor.dashboard')->with('success', 'Tugas berhasil diberikan ke semua anak magang yang dipilih!');
    }

    /**
     * Update an existing task.
     */
    public function updateTask(Request $request, Task $task)
    {
        // Authorize
        if ($task->mentor_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'due_date' => ['required', 'date', 'after_or_equal:today'],
            'attachment' => ['nullable', 'file', 'max:20480'],
        ]);

        $data = [
            'title' => $request->title,
            'description' => $request->description,
            'due_date' => $request->due_date,
        ];

        if ($request->hasFile('attachment')) {
            $data['attachment_path'] = $request->file('attachment')->store('task_attachments', 'public');
        }

        $task->update($data);

        return redirect()->route('mentor.dashboard')->with('success', 'Tugas berhasil diperbarui!');
    }

    /**
     * Delete a task.
     */
    public function destroyTask(Task $task)
    {
        if ($task->mentor_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $task->delete();

        return redirect()->route('mentor.dashboard')->with('success', 'Tugas berhasil dihapus!');
    }

    /**
     * Grade a specific submission.
     */
    public function gradeSubmission(Request $request, Submission $submission)
    {
        // Ensure the submission belongs to a task created by this mentor
        if ($submission->task->mentor_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        if ($submission->status === 'pending' || is_null($submission->file_path)) {
            return back()->withErrors(['score' => 'Tugas belum dikerjakan/dikumpulkan atau file lampiran tidak ada.']);
        }

        $request->validate([
            'score' => ['required', 'integer', 'min:0', 'max:100'],
            'feedback' => ['nullable', 'string'],
        ]);

        // Update submission status, score and feedback
        $submission->update([
            'score' => $request->score,
            'feedback' => $request->feedback,
            'status' => 'graded',
        ]);

        // Update task status to graded
        $submission->task->update([
            'status' => 'graded',
        ]);

        return redirect()->route('mentor.dashboard')->with('success', 'Tugas berhasil dinilai!');
    }

    /**
     * Upload completion certificate for an assigned intern.
     */
    public function uploadCertificate(Request $request, User $user)
    {
        $mentor = Auth::user();

        // Check if this intern is assigned to this mentor
        if (!$user->internProfile || $user->internProfile->mentor_id !== $mentor->id) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'certificate' => ['required', 'file', 'mimes:pdf,zip,rar,doc,docx,jpg,jpeg,png', 'max:10240'],
        ]);

        if ($request->hasFile('certificate')) {
            $path = $request->file('certificate')->store('certificates', 'public');
            $user->internProfile->update([
                'certificate_path' => $path,
            ]);
        }

        return redirect()->route('mentor.dashboard')->with('success', 'Sertifikat kelulusan berhasil diunggah!');
    }
}
