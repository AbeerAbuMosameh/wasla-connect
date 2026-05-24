<?php

namespace App\Http\Controllers;

use App\Models\Meeting;
use Illuminate\Http\Request;

class MeetingController extends Controller
{
    public function index()
    {
        $meetings = Meeting::orderBy('date', 'desc')->orderBy('time', 'desc')->get();
        $totalMinutes = Meeting::whereNotNull('duration')->sum('duration');

        return view('meetings.index', compact('meetings', 'totalMinutes'));
    }

    public function create()
    {
        return view('meetings.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'date'     => 'required|date',
            'time'     => 'required',
            'title'    => 'required|string|max:255',
            'type'     => 'required|string',
            'duration' => 'nullable|integer|min:1',
        ]);

        Meeting::create($request->only('date', 'time', 'title', 'type', 'duration'));

        return redirect()->route('meetings.index')->with('success', 'Meeting scheduled successfully.');
    }

    public function show(Meeting $meeting)
    {
        return view('meetings.show', compact('meeting'));
    }

    public function edit(Meeting $meeting)
    {
        return view('meetings.edit', compact('meeting'));
    }

    public function update(Request $request, Meeting $meeting)
    {
        // Phase 1: reschedule (date/time/title)
        if ($request->has('reschedule')) {
            $request->validate([
                'date'     => 'required|date',
                'time'     => 'required',
                'title'    => 'required|string|max:255',
                'type'     => 'required|string',
                'duration' => 'nullable|integer|min:1',
            ]);
            $meeting->update($request->only('date', 'time', 'title', 'type', 'duration'));
            return redirect()->route('meetings.show', $meeting)->with('success', 'Meeting updated.');
        }

        // Phase 2: add notes after meeting
        $request->validate([
            'topics'      => 'nullable|string',
            'accomplished'=> 'nullable|string',
            'action_items'=> 'nullable|string',
            'notes'       => 'nullable|string',
        ]);

        $meeting->update(array_merge(
            $request->only('topics', 'accomplished', 'action_items', 'notes'),
            ['status' => 'completed']
        ));

        return redirect()->route('meetings.show', $meeting)->with('success', 'Notes saved successfully.');
    }

    public function destroy(Meeting $meeting)
    {
        $meeting->delete();
        return redirect()->route('meetings.index')->with('success', 'Meeting deleted.');
    }
}
