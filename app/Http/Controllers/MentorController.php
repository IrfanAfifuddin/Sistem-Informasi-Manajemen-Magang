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
        
        $request->validate([
            'intern_id' => ['required', 'exists:users,id'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'due_date' => ['required', 'date', 'after:now'],
        ]);

        // Verify the intern is indeed connected to this mentor
        $isMyIntern = User::where('id', $request->intern_id)
            ->whereHas('internProfile', function ($query) use ($mentor) {
                $query->where('mentor_id', $mentor->id);
            })->exists();

        if (!$isMyIntern) {
            return back()->withErrors(['intern_id' => 'Intern ini tidak ditugaskan kepada Anda.']);
        }

        Task::create([
            'mentor_id' => $mentor->id,
            'intern_id' => $request->intern_id,
            'title' => $request->title,
            'description' => $request->description,
            'due_date' => $request->due_date,
            'status' => 'pending',
        ]);

        return redirect()->route('mentor.dashboard')->with('success', 'Tugas berhasil diberikan!');
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
            'due_date' => ['required', 'date'],
        ]);

        $task->update([
            'title' => $request->title,
            'description' => $request->description,
            'due_date' => $request->due_date,
        ]);

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
}
