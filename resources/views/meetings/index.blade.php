@extends('layouts.app')

@section('title', 'All Meetings')

@section('header-stats')
<div class="header-stats">
  <div><div class="stat-val">{{ $meetings->count() }}</div><div class="stat-lbl">Meetings</div></div>
  <div><div class="stat-val">{{ number_format($meetings->where('status','completed')->count()) }}</div><div class="stat-lbl">Completed</div></div>
  <div><div class="stat-val">{{ number_format($totalMinutes / 60, 1) }}</div><div class="stat-lbl">Hours</div></div>
</div>
@endsection

@section('content')
<div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:24px; flex-wrap:wrap; gap:12px;">
  <h2 style="font-size:1.2rem;">All Meetings</h2>
  <a href="{{ route('meetings.create') }}" class="btn btn-primary">+ Schedule Meeting</a>
</div>

@forelse($meetings as $meeting)
  <div class="card" style="margin-bottom:16px; transition: box-shadow 0.15s;"
       onmouseover="this.style.boxShadow='0 4px 20px rgba(0,0,0,0.08)'"
       onmouseout="this.style.boxShadow='none'">
    <div style="display:flex; justify-content:space-between; align-items:flex-start; gap:12px; flex-wrap:wrap;">
      <div>
        <div style="display:flex; gap:8px; flex-wrap:wrap; margin-bottom:8px;">
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
        <div style="font-size:1.05rem; font-weight:700;">{{ $meeting->title }}</div>
        @if($meeting->topics)
          <div style="font-size:0.85rem; color:var(--muted); margin-top:4px;">{{ Str::limit($meeting->topics, 100) }}</div>
        @endif
      </div>
      <div style="display:flex; gap:8px; flex-shrink:0;">
        <a href="{{ route('meetings.show', $meeting) }}" class="btn btn-ghost btn-sm">View</a>
        @if(!$meeting->isCompleted())
          <a href="{{ route('meetings.edit', $meeting) }}#notes" class="btn btn-success btn-sm">Add Notes</a>
        @else
          <a href="{{ route('meetings.edit', $meeting) }}#notes" class="btn btn-ghost btn-sm">Edit Notes</a>
        @endif
        <form method="POST" action="{{ route('meetings.destroy', $meeting) }}" onsubmit="return confirm('Delete this meeting?')">
          @csrf @method('DELETE')
          <button class="btn btn-danger btn-sm">Delete</button>
        </form>
      </div>
    </div>
  </div>
@empty
  <div style="text-align:center; padding:60px 0; color:var(--muted);">
    <div style="font-size:3rem; margin-bottom:12px;">📋</div>
    <p>No meetings yet. <a href="{{ route('meetings.create') }}" style="color:var(--primary);">Schedule your first one.</a></p>
  </div>
@endforelse
@endsection
