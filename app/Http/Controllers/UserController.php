<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    // Example: app/Http/Controllers/ClientController.php
    public function __construct()
    {
        $this->middleware(['auth','role:admin']); // only admin can hit this controller actions
    }

    public function index()
    {
        $users = User::with('roles')->orderByDesc('id')->paginate(12);
        return view('users.index', compact('users'));
    }

    public function create()
    {
        $roles = Role::orderBy('name')->get();
        return view('users.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'first_name'=>'required|string|max:191',
            'last_name'=>'required|string|max:191',
            'email'=>'required|email|unique:users,email',
            'password'=>'required|min:6|confirmed',
            'employee_type'=>'required|in:fulltime,contract',
            'doj'=>'nullable|date',
            'is_active'=>'nullable|boolean',
            'roles'=>'array'
        ]);
        $data['password'] = Hash::make($data['password']);
        $data['is_active'] = $request->has('is_active') ? 1 : 0;
        $roles = $data['roles'] ?? [];
        unset($data['roles']);

        $user = User::create($data);
        if ($roles) $user->roles()->sync($roles);
        return redirect()->route('users.index')->with('success','User created.');
    }

    public function edit(User $user)
    {
        $roles = Role::orderBy('name')->get();
        $user->load('roles');
        return view('users.edit', compact('user','roles'));
    }

   public function update(Request $request, User $user)
{
    $data = $request->validate([
        'first_name'=>'required|string|max:191',
        'last_name'=>'required|string|max:191',
        'email'=>"required|email|unique:users,email,{$user->id}",
        'password'=>'nullable|min:6|confirmed',
        'employee_type'=>'required|in:fulltime,contract',
        'doj'=>'nullable|date',
        'is_active'=>'nullable|boolean',
        'roles'=>'array'
    ]);
   
    $data['is_active'] = $request->has('is_active') ? 1 : 0;
    
    // Update password only if provided
    if ($request->filled('password')) {
        $data['password'] = Hash::make($data['password']);
    } else {
        unset($data['password']);
    }

    $user->update($data);
    
    // Sync roles
    $roles = $request->input('roles', []);
    $user->roles()->sync($roles);

    return redirect()->route('users.index')->with('success','User updated.');
}

    public function destroy(User $user)
    {
        $user->delete();
        return redirect()->route('users.index')->with('success','User deleted.');
    }
}
