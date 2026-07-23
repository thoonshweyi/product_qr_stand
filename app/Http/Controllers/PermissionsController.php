<?php

namespace App\Http\Controllers;

use App\Models\Permission;
use App\Models\Status;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class PermissionsController extends Controller
{
    public function index(Request $request)
    {
        $permissions = Permission::query()
            ->with(['status', 'user'])
            ->when($request->filled('filtername'), function ($query) use ($request) {
                $query->where('name', 'like', '%'.$request->string('filtername')->trim().'%');
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        $statuses = Status::query()
            ->whereIn('id', [3, 4])
            ->orderBy('id')
            ->get();

        return view('permissions.index', compact('permissions', 'statuses'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100', 'unique:permissions,name'],
            'status_id' => ['required', Rule::in([3, 4]), 'exists:statuses,id'],
        ]);

        Permission::create([
            'name' => $validated['name'],
            'slug' => Str::slug($validated['name']),
            'status_id' => $validated['status_id'],
            'user_id' => Auth::id(),
        ]);

        return to_route('permissions.index')->with('success', 'Permission created successfully.');
    }

    public function update(Request $request, string $id)
    {
        $permission = Permission::findOrFail($id);
        $request->merge(['edit_permission_id' => $permission->id]);

        $validated = $request->validateWithBag('updatePermission', [
            'name' => [
                'required',
                'string',
                'max:100',
                Rule::unique('permissions', 'name')->ignore($permission->id),
            ],
            'status_id' => ['required', Rule::in([3, 4]), 'exists:statuses,id'],
        ]);

        $permission->update([
            'name' => $validated['name'],
            'slug' => Str::slug($validated['name']),
            'status_id' => $validated['status_id'],
            'user_id' => Auth::id(),
        ]);

        return to_route('permissions.index')->with('success', 'Permission updated successfully.');
    }

    public function destroy(string $id)
    {
        $permission = Permission::findOrFail($id);
        $permission->delete();

        return to_route('permissions.index')->with('success', 'Permission deleted successfully.');
    }
}
