<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Role;
use App\Models\Status;
use App\Models\Workflow;
use App\Models\WorkflowProcess;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class WorkflowsController extends Controller
{
    public function index(Request $request)
    {
        $keyword = trim((string) $request->query('keyword', ''));

        $workflows = Workflow::with(['status', 'user'])
            ->addSelect([
                'processes_count' => WorkflowProcess::selectRaw('count(*)')
                    ->whereColumn('workflow_id', 'workflows.id'),
            ])
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

        $departments = Department::where('status_id', 3)->orderBy('name')->get(['id', 'name']);
        $roles = Role::where('status_id', 3)->orderBy('name')->get(['id', 'name']);

        return view('workflows.index', compact('workflows', 'statuses', 'departments', 'roles', 'keyword'));
    }

    public function store(Request $request)
    {
        $validated = $this->validateWorkflow($request);
        $workflow = DB::transaction(function () use ($validated, $request) {
            $workflow = Workflow::create([
                'name' => $validated['name'],
                'slug' => Str::slug($validated['slug'] ?: $validated['name']),
                'status_id' => $validated['status_id'],
                'user_id' => $request->user()->id,
            ]);

            $this->syncProcesses($workflow, $validated['processes']);

            return $workflow;
        });

        return response()->json([
            'success' => true,
            'message' => 'Workflow created successfully.',
            'data' => $workflow,
        ]);
    }

    public function show(Workflow $workflow)
    {
        $workflow->setRelation(
            'processes',
            WorkflowProcess::where('workflow_id', $workflow->id)->orderBy('step_order')->get(),
        );

        return response()->json([
            'success' => true,
            'data' => $workflow->load(['status', 'user']),
        ]);
    }

    public function update(Request $request, Workflow $workflow)
    {
        $validated = $this->validateWorkflow($request, $workflow);
        DB::transaction(function () use ($validated, $workflow) {
            $workflow->update([
                'name' => $validated['name'],
                'slug' => Str::slug($validated['slug'] ?: $validated['name']),
                'status_id' => $validated['status_id'],
            ]);

            $this->syncProcesses($workflow, $validated['processes']);
        });

        return response()->json([
            'success' => true,
            'message' => 'Workflow updated successfully.',
            'data' => $workflow,
        ]);
    }

    public function destroy(Workflow $workflow)
    {
        DB::transaction(function () use ($workflow) {
            WorkflowProcess::where('workflow_id', $workflow->id)->delete();
            $workflow->delete();
        });

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
            'processes' => ['required', 'array', 'min:1'],
            'processes.*.id' => ['nullable', 'integer', 'exists:workflow_processes,id'],
            'processes.*.name' => ['required', 'string', 'max:100', 'distinct'],
            'processes.*.department_id' => ['nullable', 'exists:departments,id'],
            'processes.*.role_id' => ['nullable', 'exists:roles,id'],
        ]);
    }

    private function syncProcesses(Workflow $workflow, array $processes): void
    {
        $keptIds = [];

        foreach (array_values($processes) as $index => $processData) {
            $processId = $processData['id'] ?? null;

            if ($processId) {
                $process = WorkflowProcess::where('workflow_id', $workflow->id)
                    ->whereKey($processId)
                    ->firstOrFail();
            } else {
                $process = new WorkflowProcess;
                $process->workflow_id = $workflow->id;
            }

            $process->name = $processData['name'];
            $process->department_id = $processData['department_id'] ?? null;
            $process->role_id = $processData['role_id'] ?? null;
            $process->step_order = $index + 1;
            $process->save();

            $keptIds[] = $process->id;
        }

        WorkflowProcess::where('workflow_id', $workflow->id)
            ->whereNotIn('id', $keptIds)
            ->delete();
    }
}
