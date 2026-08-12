<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\Status;
use App\Models\Workflow;
use App\Models\WorkflowStep;
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
                'steps_count' => WorkflowStep::selectRaw('count(*)')
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

        $roles = Role::where('status_id', 3)->orderBy('name')->get(['id', 'name']);

        return view('workflows.index', compact('workflows', 'statuses', 'roles', 'keyword'));
    }

    public function store(Request $request)
    {
        $validated = $this->validateWorkflow($request);
        $workflow = DB::transaction(function () use ($validated, $request) {
            $workflow = Workflow::create([
                'name' => $validated['name'],
                'description' => $validated['description'] ?? null,
                'slug' => Str::slug($validated['slug'] ?: $validated['name']),
                'status_id' => $validated['status_id'],
                'user_id' => $request->user()->id,
            ]);

            $this->syncSteps($workflow, $validated['steps']);

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
            'steps',
            WorkflowStep::where('workflow_id', $workflow->id)->orderBy('step_no')->get(),
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
                'description' => $validated['description'] ?? null,
                'slug' => Str::slug($validated['slug'] ?: $validated['name']),
                'status_id' => $validated['status_id'],
            ]);

            $this->syncSteps($workflow, $validated['steps']);
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
            WorkflowStep::where('workflow_id', $workflow->id)->delete();
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
            'description' => ['nullable', 'string', 'max:1000'],
            'slug' => [
                'required',
                'string',
                'max:100',
                Rule::unique('workflows', 'slug')->ignore($workflow?->id),
            ],
            'status_id' => ['required', 'exists:statuses,id'],
            'steps' => ['required', 'array', 'min:1'],
            'steps.*.id' => ['nullable', 'integer', 'exists:workflow_steps,id'],
            'steps.*.name' => ['required', 'string', 'max:100', 'distinct'],
            'steps.*.action' => ['required', 'string', 'max:100', 'regex:/^[a-z0-9_-]+$/'],
            'steps.*.role_id' => ['nullable', 'exists:roles,id'],
            'steps.*.status_id' => ['nullable', 'exists:statuses,id'],
        ]);
    }

    private function syncSteps(Workflow $workflow, array $steps): void
    {
        $keptIds = [];

        foreach (array_values($steps) as $index => $stepData) {
            $stepId = $stepData['id'] ?? null;

            if ($stepId) {
                $step = WorkflowStep::where('workflow_id', $workflow->id)
                    ->whereKey($stepId)
                    ->firstOrFail();
            } else {
                $step = new WorkflowStep;
                $step->workflow_id = $workflow->id;
            }

            $step->name = $stepData['name'];
            $step->action = Str::slug($stepData['action'], '_');
            $step->role_id = $stepData['role_id'] ?? null;
            $step->status_id = $stepData['status_id'] ?? null;
            $step->step_no = $index + 1;
            $step->save();

            $keptIds[] = $step->id;
        }

        WorkflowStep::where('workflow_id', $workflow->id)
            ->whereNotIn('id', $keptIds)
            ->delete();
    }
}
