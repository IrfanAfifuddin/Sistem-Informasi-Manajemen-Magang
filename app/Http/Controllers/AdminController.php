<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\InternProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class AdminController extends Controller
{
    /**
     * Display the Admin dashboard / User list.
     */
    public function index()
    {
        $users = User::with('internProfile.mentor')->orderBy('id', 'desc')->get();
        $mentors = User::where('role', 'mentor')->get();
        
        $internsCount = User::where('role', 'intern')->count();
        $mentorsCount = User::where('role', 'mentor')->count();
        $adminsCount = User::where('role', 'admin')->count();

        return view('admin.dashboard', compact('users', 'mentors', 'internsCount', 'mentorsCount', 'adminsCount'));
    }

    /**
     * Store a newly created Admin or Mentor.
     */
    public function storeUser(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'username' => ['required', 'string', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:6'],
            'role' => ['required', Rule::in(['admin', 'mentor'])],
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'username' => $request->username,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'is_first_login' => false, // Only Interns get forced password reset
        ]);

        return redirect()->route('admin.dashboard')->with('success', 'User berhasil dibuat!');
    }

    /**
     * Store a newly created Intern (and their profile).
     */
    public function storeIntern(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'string', 'email', 'max:255', 'unique:users'],
            'nim' => ['required', 'string', 'max:50', 'unique:users,username'],
            'university' => ['required', 'string', 'max:255'],
            'major' => ['required', 'string', 'max:255'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'mentor_id' => ['nullable', 'exists:users,id'],
        ]);

        // Default password is NIM
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'username' => $request->nim,
            'password' => Hash::make($request->nim),
            'role' => 'intern',
            'is_first_login' => true,
        ]);

        InternProfile::create([
            'user_id' => $user->id,
            'university' => $request->university,
            'major' => $request->major,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'mentor_id' => $request->mentor_id,
        ]);

        return redirect()->route('admin.dashboard')->with('success', 'Intern account created with default password same as NIM!');
    }

    /**
     * Update an existing Admin or Mentor.
     */
    public function updateUser(Request $request, User $user)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'username' => ['required', 'string', 'max:255', Rule::unique('users')->ignore($user->id)],
            'password' => ['nullable', 'string', 'min:6'],
        ]);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'username' => $request->username,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return redirect()->route('admin.dashboard')->with('success', 'User updated successfully!');
    }

    /**
     * Update an existing Intern (and profile).
     */
    public function updateIntern(Request $request, User $user)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'nim' => ['required', 'string', 'max:50', Rule::unique('users', 'username')->ignore($user->id)],
            'university' => ['required', 'string', 'max:255'],
            'major' => ['required', 'string', 'max:255'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'mentor_id' => ['nullable', 'exists:users,id'],
            'password' => ['nullable', 'string', 'min:6'],
        ]);

        $userData = [
            'name' => $request->name,
            'email' => $request->email,
            'username' => $request->nim,
        ];

        if ($request->filled('password')) {
            $userData['password'] = Hash::make($request->password);
        }

        $user->update($userData);

        $user->internProfile->update([
            'university' => $request->university,
            'major' => $request->major,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'mentor_id' => $request->mentor_id,
        ]);

        return redirect()->route('admin.dashboard')->with('success', 'Intern profile updated successfully!');
    }

    /**
     * Delete a User.
     */
    public function destroy(User $user)
    {
        $user->delete(); // Automatically cascade deletes profiles, tasks, and submissions due to DB keys.
        return redirect()->route('admin.dashboard')->with('success', 'User deleted successfully!');
    }
}
