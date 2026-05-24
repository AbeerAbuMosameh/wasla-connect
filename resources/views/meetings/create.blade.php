@extends('layouts.app')
@section('title', 'Schedule Meeting')

@section('content')
<div style="max-width:560px; margin:0 auto;">
  <div style="display:flex; align-items:center; gap:12px; margin-bottom:24px;">
    <a href="{{ route('meetings.index') }}" style="color:var(--muted); text-decoration:none;">← Back</a>
    <h2 style="font-size:1.2rem;">Schedule a Meeting</h2>
  </div>

  <div class="card">
    <form method="POST" action="{{ route('meetings.store') }}">
      @csrf
      <div class="form-grid-2">
        <div class="form-group">
          <label>Date *</label>
          <input type="date" name="date" value="{{ old('date', date('Y-m-d')) }}" required />
          @error('date')<div class="error-msg">{{ $message }}</div>@enderror
        </div>
        <div class="form-group">
          <label>Time *</label>
          <input type="time" name="time" value="{{ old('time') }}" required />
          @error('time')<div class="error-msg">{{ $message }}</div>@enderror
        </div>
        <div class="form-group">
          <label>Duration (minutes)</label>
          <input type="number" name="duration" value="{{ old('duration') }}" placeholder="e.g. 60" min="1" />
        </div>
        <div class="form-group">
          <label>Meeting Type</label>
          <select name="type">
            @foreach(['1-on-1','Workshop','Code Review','Planning','Feedback Session','Other'] as $type)
              <option value="{{ $type }}" {{ old('type') === $type ? 'selected' : '' }}>{{ $type }}</option>
            @endforeach
          </select>
        </div>
      </div>
      <div class="form-group">
        <label>Session Title *</label>
        <input type="text" name="title" value="{{ old('title') }}" placeholder="e.g. Introduction to APIs" required />
        @error('title')<div class="error-msg">{{ $message }}</div>@enderror
      </div>
      <div class="form-actions">
        <a href="{{ route('meetings.index') }}" class="btn btn-ghost">Cancel</a>
        <button class="btn btn-primary">Schedule Meeting</button>
      </div>
    </form>
  </div>
</div>
@endsection
