<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>@yield('title', 'Wasla Connect') — Mentor Meetings</title>
  <style>
    :root {
      --primary: #6c63ff;
      --primary-dark: #574fd6;
      --bg: #f4f6fb;
      --card: #ffffff;
      --text: #1e1e2e;
      --muted: #6b7280;
      --border: #e5e7eb;
      --green: #10b981;
      --red: #ef4444;
      --yellow: #f59e0b;
    }
    * { box-sizing: border-box; margin: 0; padding: 0; }
    body { font-family: 'Segoe UI', system-ui, sans-serif; background: var(--bg); color: var(--text); min-height: 100vh; }

    header {
      background: linear-gradient(135deg, var(--primary), var(--primary-dark));
      color: white; padding: 20px 32px;
      display: flex; align-items: center; justify-content: space-between;
      box-shadow: 0 2px 12px rgba(108,99,255,0.3);
    }
    header a { color: white; text-decoration: none; }
    header h1 { font-size: 1.3rem; font-weight: 700; }
    header p  { font-size: 0.8rem; opacity: 0.8; margin-top: 2px; }

    .header-stats { display: flex; gap: 24px; text-align: center; }
    .stat-val { font-size: 1.5rem; font-weight: 700; }
    .stat-lbl { font-size: 0.72rem; opacity: 0.8; }

    .container { max-width: 900px; margin: 0 auto; padding: 32px 16px; }

    .btn {
      display: inline-flex; align-items: center; gap: 6px;
      padding: 10px 20px; border: none; border-radius: 8px;
      font-size: 0.88rem; font-weight: 600; cursor: pointer;
      text-decoration: none; transition: all 0.15s;
    }
    .btn-primary { background: var(--primary); color: white; }
    .btn-primary:hover { background: var(--primary-dark); transform: translateY(-1px); }
    .btn-success { background: var(--green); color: white; }
    .btn-success:hover { opacity: 0.85; }
    .btn-danger  { background: var(--red); color: white; font-size: 0.8rem; padding: 6px 14px; }
    .btn-danger:hover  { opacity: 0.85; }
    .btn-ghost   { background: var(--border); color: var(--text); }
    .btn-ghost:hover   { background: #d1d5db; }
    .btn-sm { padding: 6px 14px; font-size: 0.8rem; }

    .alert {
      padding: 12px 16px; border-radius: 8px; margin-bottom: 20px;
      font-size: 0.9rem; font-weight: 500;
    }
    .alert-success { background: #d1fae5; color: #065f46; }
    .alert-error   { background: #fee2e2; color: #991b1b; }

    .card {
      background: var(--card); border-radius: 12px;
      border: 1.5px solid var(--border); padding: 24px;
    }

    .badge {
      display: inline-block; padding: 3px 10px; border-radius: 20px;
      font-size: 0.75rem; font-weight: 600;
    }
    .badge-scheduled { background: #dbeafe; color: #1d4ed8; }
    .badge-completed  { background: #d1fae5; color: #065f46; }
    .badge-type  { background: #fef3c7; color: #92400e; }
    .badge-date  { background: #ede9fe; color: var(--primary-dark); }

    label { display: block; font-size: 0.8rem; font-weight: 600; color: var(--muted); margin-bottom: 4px; }
    input, textarea, select {
      width: 100%; padding: 9px 12px;
      border: 1.5px solid var(--border); border-radius: 8px;
      font-size: 0.9rem; font-family: inherit; outline: none;
      background: white; transition: border-color 0.15s;
    }
    input:focus, textarea:focus, select:focus { border-color: var(--primary); }
    textarea { resize: vertical; min-height: 90px; }
    .form-group { margin-bottom: 16px; }
    .form-grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
    .form-actions { display: flex; gap: 10px; margin-top: 24px; }

    .error-msg { color: var(--red); font-size: 0.8rem; margin-top: 4px; }
  </style>
</head>
<body>
<header>
  <a href="{{ route('meetings.index') }}">
    <h1>Wasla Connect</h1>
    <p>Mentor Meeting Notes</p>
  </a>
  @yield('header-stats')
</header>

<div class="container">
  @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
  @endif
  @if($errors->any())
    <div class="alert alert-error">{{ $errors->first() }}</div>
  @endif

  @yield('content')
</div>
</body>
</html>
