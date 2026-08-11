<?php

namespace App\Http\Controllers;

use App\Models\Status;
use App\Models\Workflow;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class WorkflowsController extends Controller
{
    public function index(Request $request)
    {
        $keyword = trim((string) $request->query('keyword', ''));

        $workflows = Workflow::with(['status', 'user'])
            ->when($keyword !== '', function ($query) use ($keyword) {
                $query->where(function ($query) use ($keyword) {
                    $query->where('name', 'like', '%'.$keyword.'%')
                        ->orWhere('slug', 'like', '%'.$keyword.'%');
                });
            })
            ->latest('id')
            ->paginate(15)
            ->withQueryString();

        $statuses = Status::whereIn('id', [1, 2])->orderBy('id')->get(['id', 'name']);

        if ($statuses->isEmpty()) {
            $statuses = Status::orderBy('id')->get(['id', 'name']);
        }

        return view('workflows.index', compact('workflows', 'statuses', 'keyword'));
    }

    public function store(Request $request)
    {
        $validated = $this->validateWorkflow($request);
        $validated['slug'] = Str::slug($validated['slug'] ?: $validated['name']);
        $validated['user_id'] = $request->user()->id;

        $workflow = Workflow::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Workflow created successfully.',
            'data' => $workflow,
        ]);
    }

    public function show(Workflow $workflow)
    {
        return response()->json([
            'success' => true,
            'data' => $workflow->load(['status', 'user']),
        ]);
    }

    public function update(Request $request, Workflow $workflow)
    {
        $validated = $this->validateWorkflow($request, $workflow);
        $validated['slug'] = Str::slug($validated['slug'] ?: $validated['name']);

        $workflow->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Workflow updated successfully.',
            'data' => $workflow,
        ]);
    }

    public function destroy(Workflow $workflow)
    {
        $workflow->delete();

        return response()->json([
            'success' => true,
            'message' => 'Workflow deleted successfully.',
        ]);
    }

    private function validateWorkflow(Request $request, ?Workflow $workflow = null): array
    {
        $request->merge([
            'slug' => Str::slug($request->input('slug') ?: $request->input('name', '')),
        ]);

        return $request->validate([
            'name' => [
                'required',
                'string',
                'max:100',
                Rule::unique('workflows', 'name')->ignore($workflow?->id),
            ],
            'slug' => [
                'required',
                'string',
                'max:100',
                Rule::unique('workflows', 'slug')->ignore($workflow?->id),
            ],
            'status_id' => ['required', 'exists:statuses,id'],
        ]);
    }
}
