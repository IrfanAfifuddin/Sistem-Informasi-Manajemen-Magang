<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\Submission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class InternController extends Controller
{
    /**
     * Display Intern Workspace Dashboard.
     */
    public function index()
    {
        \App\Models\Submission::autoFailExpiredTasks();

        $intern = Auth::user();
        
        // Load profile and mentor details
        $profile = $intern->internProfile;
        $mentor = $profile ? $profile->mentor : null;

        // Fetch tasks assigned to the intern
        $tasks = Task::where('intern_id', $intern->id)
            ->with('latestSubmission')
            ->orderBy('due_date', 'asc')
            ->get();

        // Calculate average score of graded tasks
        $gradedSubmissions = Submission::where('intern_id', $intern->id)
            ->where('status', 'graded')
            ->whereNotNull('score')
            ->get();

        $averageScore = $gradedSubmissions->isNotEmpty() 
            ? round($gradedSubmissions->avg('score'), 2) 
            : 0;

        // General progress metrics
        $tasksCount = $tasks->count();
        $gradedCount = $gradedSubmissions->count();
        $submittedCount = Submission::where('intern_id', $intern->id)
            ->where('status', 'submitted')
            ->count();
        $pendingCount = $tasksCount - $gradedCount - $submittedCount;

        return view('intern.dashboard', compact(
            'profile', 
            'mentor', 
            'tasks', 
            'averageScore', 
            'tasksCount', 
            'gradedCount', 
            'submittedCount', 
            'pendingCount'
        ));
    }

    /**
     * Submit work for a specific task.
     */
    public function submitTask(Request $request, Task $task)
    {
        // Ensure the task belongs to this intern
        if ($task->intern_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'file' => ['nullable', 'file', 'mimes:pdf,zip,rar,doc,docx,jpg,jpeg,png', 'max:10240'], // Max 10MB
            'link_url' => ['nullable', 'url', 'max:255'],
        ]);

        // At least one submission type is required
        if (!$request->hasFile('file') && !$request->filled('link_url')) {
            return back()->withErrors(['submission' => 'Anda harus mengunggah file atau mencantumkan link URL.']);
        }

        $filePath = null;
        if ($request->hasFile('file')) {
            // Store file inside local public disk
            $file = $request->file('file');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('submissions', $fileName, 'public');
        }

        // Create or update the submission for this task
        $submission = Submission::updateOrCreate(
            [
                'task_id' => $task->id,
                'intern_id' => Auth::id(),
            ],
            [
                'file_path' => $filePath ?? $task->latestSubmission->file_path ?? null,
                'link_url' => $request->link_url,
                'status' => 'submitted',
                // Keep the old score/feedback if they are resubmitting, or reset them if they want a re-grade.
                // Resetting or keeping is fine; let's reset score/feedback to re-grade!
                'score' => null,
                'feedback' => null,
            ]
        );

        // Update task status to submitted
        $task->update([
            'status' => 'submitted',
        ]);

        return redirect()->route('intern.dashboard')->with('success', 'Tugas berhasil dikumpulkan!');
    }

    /**
     * Upload application letter.
     */
    public function uploadLetter(Request $request)
    {
        $request->validate([
            'letter' => ['required', 'file', 'mimes:pdf,jpg,png', 'max:2048'],
        ]);

        $intern = Auth::user();
        $profile = $intern->internProfile;

        if (!$profile) {
            return back()->withErrors(['letter' => 'Profil magang tidak ditemukan.']);
        }

        // Store file inside local public disk in documents/letters directory
        $path = $request->file('letter')->store('documents/letters', 'public');

        // Delete old file if exists
        if ($profile->application_letter_path) {
            Storage::disk('public')->delete($profile->application_letter_path);
        }

        // Update profile in DB
        $profile->update([
            'application_letter_path' => $path,
        ]);

        return redirect()->route('intern.dashboard')->with('success', 'Surat Permohonan berhasil diunggah!');
    }
}
