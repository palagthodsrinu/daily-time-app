<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\Request;

class ClientController extends Controller
{
    // Example: app/Http/Controllers/ClientController.php
    public function __construct()
    {
        $this->middleware(['auth','role:admin']); // only admin can hit this controller actions
    }

    public function index(Request $request)
    {
        $query = Client::query();
        
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                ->orWhere('description', 'like', "%{$search}%");
            });
        }
        
        $clients = $query->orderByDesc('id')->paginate(10)->withQueryString();
        
        return view('clients.index', compact('clients'));
    }

    public function create()
    {
        return view('clients.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'=>'required|string|max:191',
            'description'=>'nullable|string',
            'is_active'=>'nullable|boolean'
        ]);
        $data['is_active'] = $request->has('is_active') ? 1 : 0;
        Client::create($data);
        return redirect()->route('clients.index')->with('success','Client created.');
    }

    public function edit(Client $client)
    {
        return view('clients.edit', compact('client'));
    }

    public function update(Request $request, Client $client)
    {
        $data = $request->validate([
            'name'=>'required|string|max:191',
            'description'=>'nullable|string',
            'is_active'=>'nullable|boolean'
        ]);
        $data['is_active'] = $request->has('is_active') ? 1 : 0;
        $client->update($data);
        return redirect()->route('clients.index')->with('success','Client updated.');
    }

    public function destroy(Client $client)
    {
        $client->delete();
        return redirect()->route('clients.index')->with('success','Client deleted.');
    }
}
