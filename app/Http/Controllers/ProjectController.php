<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Client;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    // Example: app/Http/Controllers/ClientController.php
    public function __construct()
    {
        $this->middleware(['auth','role:admin']); // only admin can hit this controller actions
    }

    public function index()
    {
        $projects = Project::with('client')->orderByDesc('id')->paginate(10);
        return view('projects.index', compact('projects'));
    }

    public function create()
    {
        $clients = Client::orderBy('name')->get();
        return view('projects.create', compact('clients'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'client_id'=>'required|exists:clients,id',
            'name'=>'required|string|max:191',
            'description'=>'nullable|string',
            'is_active'=>'nullable|boolean'
        ]);
        $data['is_active'] = $request->has('is_active') ? 1 : 0;
        Project::create($data);
        return redirect()->route('projects.index')->with('success','Project created.');
    }

    public function edit(Project $project)
    {
        $clients = Client::orderBy('name')->get();
        return view('projects.edit', compact('project','clients'));
    }

    public function update(Request $request, Project $project)
    {
        $data = $request->validate([
            'client_id'=>'required|exists:clients,id',
            'name'=>'required|string|max:191',
            'description'=>'nullable|string',
            'is_active'=>'nullable|boolean'
        ]);
        $data['is_active'] = $request->has('is_active') ? 1 : 0;
        $project->update($data);
        return redirect()->route('projects.index')->with('success','Project updated.');
    }

    public function destroy(Project $project)
    {
        $project->delete();
        return redirect()->route('projects.index')->with('success','Project deleted.');
    }
}
