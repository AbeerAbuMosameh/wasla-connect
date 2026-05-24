@extends('layouts.app')
@section('title', 'Edit: ' . $meeting->title)

@section('content')
<div style="max-width:620px; margin:0 auto;">
  <div style="display:flex; align-items:center; gap:12px; margin-bottom:24px;">
    <a href="{{ route('meetings.show', $meeting) }}" style="color:var(--muted); text-decoration:none;">← Back</a>
    <h2 style="font-size:1.2rem;">{{ $meeting->title }}</h2>
  </div>

  {{-- Phase 1: Reschedule --}}
  <div class="card" style="margin-bottom:24px;">
    <h3 style="font-size:0.95rem; font-weight:700; margin-bottom:16px; color:var(--primary);">Meeting Details</h3>
    <form method="POST" action="{{ route('meetings.update', $meeting) }}">
      @csrf @method('PUT')
      <input type="hidden" name="reschedule" value="1" />
      <div class="form-grid-2">
        <div class="form-group">
          <label>Date *</label>
          <input type="date" name="date" value="{{ old('date', $meeting->date->format('Y-m-d')) }}" required />
        </div>
        <div class="form-group">
          <label>Time *</label>
          <input type="time" name="time" value="{{ old('time', $meeting->time) }}" required />
        </div>
        <div class="form-group">
          <label>Duration (minutes)</label>
          <input type="number" name="duration" value="{{ old('duration', $meeting->duration) }}" placeholder="e.g. 60" min="1" />
        </div>
        <div class="form-group">
          <label>Meeting Type</label>
          <select name="type">
            @foreach(['1-on-1','Workshop','Code Review','Planning','Feedback Session','Other'] as $type)
              <option value="{{ $type }}" {{ (old('type', $meeting->type) === $type) ? 'selected' : '' }}>{{ $type }}</option>
            @endforeach
          </select>
        </div>
      </div>
      <div class="form-group">
        <label>Session Title *</label>
        <input type="text" name="title" value="{{ old('title', $meeting->title) }}" required />
      </div>
      <div class="form-actions">
        <button class="btn btn-primary">Save Changes</button>
      </div>
    </form>
  </div>

  {{-- Phase 2: Post-meeting notes --}}
  <div class="card" id="notes">
    <h3 style="font-size:0.95rem; font-weight:700; margin-bottom:4px; color:var(--green);">Post-Meeting Notes</h3>
    <p style="font-size:0.82rem; color:var(--muted); margin-bottom:16px;">Fill this in after the meeting is done.</p>
    <form method="POST" action="{{ route('meetings.update', $meeting) }}">
      @csrf @method('PUT')
      <div class="form-group">
        <label>Topics Discussed</label>
        <textarea name="topics" placeholder="What subjects were covered?">{{ old('topics', $meeting->topics) }}</textarea>
      </div>
      <div class="form-group">
        <label>What We Accomplished</label>
        <textarea name="accomplished" placeholder="What was built, explained, or reviewed?">{{ old('accomplished', $meeting->accomplished) }}</textarea>
      </div>
      <div class="form-group">
        <label>Action Items / Next Steps</label>
        <textarea name="action_items" placeholder="Tasks to complete before next meeting…">{{ old('action_items', $meeting->action_items) }}</textarea>
      </div>
      <div class="form-group">
        <label>Notes & Key Takeaways</label>
        <textarea name="notes" placeholder="Important insights, links, resources…">{{ old('notes', $meeting->notes) }}</textarea>
      </div>
      <div class="form-actions">
        <a href="{{ route('meetings.show', $meeting) }}" class="btn btn-ghost">Cancel</a>
        <button class="btn btn-success">Save Notes & Mark Completed</button>
      </div>
    </form>
  </div>
</div>
@endsection
