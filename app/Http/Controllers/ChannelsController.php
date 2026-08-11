<?php

namespace App\Http\Controllers;

use App\Models\Channel;
use App\Models\Status;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class ChannelsController extends Controller
{
    public function index(Request $request)
    {
        $keyword = trim((string) $request->query('keyword', ''));

        $channels = Channel::with(['status', 'user'])
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

        return view('channels.index', compact('channels', 'statuses', 'keyword'));
    }

    public function store(Request $request)
    {
        $validated = $this->validateChannel($request);
        $validated['slug'] = Str::slug($validated['slug'] ?: $validated['name']);
        $validated['user_id'] = $request->user()->id;

        $channel = Channel::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Channel created successfully.',
            'data' => $channel,
        ]);
    }

    public function show(Channel $channel)
    {
        return response()->json([
            'success' => true,
            'data' => $channel->load(['status', 'user']),
        ]);
    }

    public function update(Request $request, Channel $channel)
    {
        $validated = $this->validateChannel($request, $channel);
        $validated['slug'] = Str::slug($validated['slug'] ?: $validated['name']);

        $channel->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Channel updated successfully.',
            'data' => $channel,
        ]);
    }

    public function destroy(Channel $channel)
    {
        $channel->delete();

        return response()->json([
            'success' => true,
            'message' => 'Channel deleted successfully.',
        ]);
    }

    private function validateChannel(Request $request, ?Channel $channel = null): array
    {
        $request->merge([
            'slug' => Str::slug($request->input('slug') ?: $request->input('name', '')),
        ]);

        return $request->validate([
            'name' => [
                'required',
                'string',
                'max:100',
                Rule::unique('channels', 'name')->ignore($channel?->id),
            ],
            'slug' => [
                'required',
                'string',
                'max:100',
                Rule::unique('channels', 'slug')->ignore($channel?->id),
            ],
            'status_id' => ['required', 'exists:statuses,id'],
        ]);
    }
}
