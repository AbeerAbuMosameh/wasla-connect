@extends('layouts.app')
@section('title', $meeting->title)

@section('content')
<div style="max-width:700px; margin:0 auto;">
  <div style="display:flex; align-items:center; gap:12px; margin-bottom:24px;">
    <a href="{{ route('meetings.index') }}" style="color:var(--muted); text-decoration:none;">← Back</a>
    <h2 style="font-size:1.2rem;">Meeting Details</h2>
  </div>

  <div class="card" style="margin-bottom:20px;">
    <div style="display:flex; justify-content:space-between; align-items:flex-start; flex-wrap:wrap; gap:12px;">
      <div>
        <div style="display:flex; gap:8px; flex-wrap:wrap; margin-bottom:10px;">
          <span class="badge badge-date">{{ $meeting->formattedDate() }}</span>
          <span class="badge" style="background:#dbeafe;color:#1d4ed8;">{{ $meeting->time }}</span>
          @if($meeting->duration)
            <span class="badge" style="background:#f3f4f6;color:#374151;">{{ $meeting->duration }} min</span>
          @endif
          <span class="badge badge-type">{{ $meeting->type }}</span>
          <span class="badge {{ $meeting->isCompleted() ? 'badge-completed' : 'badge-scheduled' }}">
            {{ ucfirst($meeting->status) }}
          </span>
        </div>
        <h3 style="font-size:1.3rem; font-weight:700;">{{ $meeting->title }}</h3>
      </div>
      <div style="display:flex; gap:8px;">
        <a href="{{ route('meetings.edit', $meeting) }}" class="btn btn-ghost btn-sm">Edit</a>
        <form method="POST" action="{{ route('meetings.destroy', $meeting) }}" onsubmit="return confirm('Delete this meeting?')">
          @csrf @method('DELETE')
          <button class="btn btn-danger btn-sm">Delete</button>
        </form>
      </div>
    </div>
  </div>

  @if(!$meeting->isCompleted())
    <div class="alert" style="background:#fef3c7; color:#92400e; display:flex; justify-content:space-between; align-items:center;">
      <span>This meeting hasn't been completed yet. Add discussion notes after the meeting.</span>
      <a href="{{ route('meetings.edit', $meeting) }}#notes" class="btn btn-success btn-sm" style="margin-left:12px; flex-shrink:0;">Add Notes</a>
    </div>
  @else
    @if($meeting->topics)
      <div class="card" style="margin-bottom:16px;">
        <div style="font-size:0.75rem; font-weight:700; color:var(--muted); text-transform:uppercase; letter-spacing:0.5px; margin-bottom:8px;">Topics Covered</div>
        <p style="font-size:0.92rem; line-height:1.7; white-space:pre-line;">{{ $meeting->topics }}</p>
      </div>
    @endif
    @if($meeting->accomplished)
      <div class="card" style="margin-bottom:16px;">
        <div style="font-size:0.75rem; font-weight:700; color:var(--muted); text-transform:uppercase; letter-spacing:0.5px; margin-bottom:8px;">What We Accomplished</div>
        <p style="font-size:0.92rem; line-height:1.7; white-space:pre-line;">{{ $meeting->accomplished }}</p>
      </div>
    @endif
    @if($meeting->action_items)
      <div class="card" style="margin-bottom:16px;">
        <div style="font-size:0.75rem; font-weight:700; color:var(--muted); text-transform:uppercase; letter-spacing:0.5px; margin-bottom:8px;">Action Items</div>
        <p style="font-size:0.92rem; line-height:1.7; white-space:pre-line;">{{ $meeting->action_items }}</p>
      </div>
    @endif
    @if($meeting->notes)
      <div class="card" style="margin-bottom:16px;">
        <div style="font-size:0.75rem; font-weight:700; color:var(--muted); text-transform:uppercase; letter-spacing:0.5px; margin-bottom:8px;">Notes & Takeaways</div>
        <p style="font-size:0.92rem; line-height:1.7; white-space:pre-line;">{{ $meeting->notes }}</p>
      </div>
    @endif
    <a href="{{ route('meetings.edit', $meeting) }}#notes" class="btn btn-ghost btn-sm">Edit Notes</a>
  @endif
</div>
@endsection
