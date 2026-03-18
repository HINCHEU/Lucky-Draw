<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Admin — Lucky Draw</title>
    <link href="https://fonts.googleapis.com/css2?family=Battambang:wght@400;700&family=DM+Sans:ital,opsz,wght@0,9..40,300;0,9..40,400;0,9..40,500;0,9..40,600;0,9..40,700;1,9..40,400&family=DM+Mono:wght@400;500&display=swap" rel="stylesheet"/>
    <style>
        :root {
            --navy:       #0a1628;
            --navy2:      #0f2040;
            --navy3:      #162d56;
            --blue:       #1d5fd4;
            --blue-lt:    #4d8ef0;
            --accent:     #f9c74f;
            --red:        #e63946;
            --green:      #2ec97e;
            --text:       #e8edf6;
            --text-dim:   #7a93bb;
            --border:     rgba(255,255,255,0.07);
            --glass:      rgba(255,255,255,0.04);
            --glass2:     rgba(255,255,255,0.08);
            --radius:     14px;
            --radius-sm:  9px;
            --shadow:     0 4px 24px rgba(0,0,0,0.35);
            --shadow-sm:  0 2px 12px rgba(0,0,0,0.2);
        }
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'DM Sans', sans-serif;
            background: var(--navy);
            color: var(--text);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* ── TOPBAR ── */
        header {
            position: sticky; top: 0; z-index: 100;
            background: rgba(10,22,40,0.92);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border-bottom: 1px solid var(--border);
            padding: 0 24px;
            height: 60px;
            display: flex; align-items: center; gap: 20px;
        }
        .brand {
            font-weight: 700; font-size: .95rem;
            letter-spacing: .04em;
            color: var(--text);
            display: flex; align-items: center; gap: 10px;
        }
        .brand-icon {
            width: 32px; height: 32px;
            background: linear-gradient(135deg, var(--blue), var(--blue-lt));
            border-radius: 8px;
            display: flex; align-items: center; justify-content: center;
            font-size: .9rem;
        }
        .brand-sep {
            width: 1px; height: 24px;
            background: var(--border);
            margin: 0 4px;
        }
        nav { display: flex; align-items: center; gap: 4px; flex: 1; }
        nav a {
            display: flex; align-items: center; gap: 6px;
            padding: 6px 14px;
            border-radius: 8px;
            font-size: .875rem; font-weight: 500;
            color: var(--text-dim);
            text-decoration: none;
            transition: all .15s;
            border: 1px solid transparent;
        }
        nav a:hover { color: var(--text); background: var(--glass2); }
        nav a.active {
            color: var(--text);
            background: var(--glass2);
            border-color: var(--border);
        }
        .nav-spacer { flex: 1; }
        .btn-back {
            display: flex; align-items: center; gap: 6px;
            padding: 7px 16px;
            background: linear-gradient(135deg, var(--blue), var(--blue-lt));
            color: white; border: none; border-radius: 8px;
            font-size: .82rem; font-weight: 600;
            text-decoration: none; cursor: pointer;
            box-shadow: 0 2px 12px rgba(29,95,212,.4);
            transition: all .15s;
        }
        .btn-back:hover { transform: translateY(-1px); box-shadow: 0 4px 16px rgba(29,95,212,.5); }

        /* ── PAGE LAYOUT ── */
        .page-wrap {
            flex: 1;
            max-width: 1200px; width: 100%;
            margin: 0 auto;
            padding: 28px 24px;
            display: grid;
            grid-template-columns: 1fr 320px;
            gap: 22px;
            align-items: start;
        }
        .page-wrap.full { grid-template-columns: 1fr; }

        /* ── CARDS ── */
        .gc {
            background: var(--navy2);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            padding: 24px;
            box-shadow: var(--shadow-sm);
        }
        .gc + .gc { margin-top: 0; }

        .stitle {
            font-family: 'DM Sans', sans-serif;
            font-size: 1rem; font-weight: 700;
            color: var(--text);
            margin-bottom: 20px;
            display: flex; align-items: center; gap: 8px;
            letter-spacing: .01em;
        }
        .stitle::after {
            content: '';
            flex: 1;
            height: 1px;
            background: var(--border);
            margin-left: 8px;
        }

        /* ── FORM ELEMENTS ── */
        .row { display: flex; gap: 12px; flex-wrap: wrap; }

        form label {
            display: block;
            font-size: .78rem; font-weight: 600;
            color: var(--text-dim);
            text-transform: uppercase; letter-spacing: .07em;
            margin-bottom: 6px;
        }
        form input[type="text"],
        form input[type="number"],
        form input[type="date"],
        form input[type="file"],
        form textarea,
        form select {
            width: 100%;
            padding: 10px 13px;
            background: var(--navy3);
            border: 1px solid var(--border);
            border-radius: var(--radius-sm);
            color: var(--text);
            font-family: 'DM Sans', sans-serif;
            font-size: .9rem;
            transition: border-color .15s, box-shadow .15s;
            outline: none;
        }
        form input:focus,
        form textarea:focus,
        form select:focus {
            border-color: var(--blue-lt);
            box-shadow: 0 0 0 3px rgba(77,142,240,.15);
        }
        form input[type="file"] { cursor: pointer; color: var(--text-dim); }
        form textarea { resize: vertical; }
        form select { appearance: none; cursor: pointer; }
        form input[type="date"]::-webkit-calendar-picker-indicator { filter: invert(.7); }

        /* checkbox toggle */
        .toggle-wrap {
            display: flex; align-items: center; gap: 10px;
        }
        .toggle-wrap label {
            font-size: .82rem; font-weight: 600;
            color: var(--text-dim); text-transform: uppercase;
            letter-spacing: .06em; margin: 0;
            cursor: pointer;
        }
        input[type="checkbox"] {
            appearance: none; width: 38px; height: 22px;
            background: var(--navy3);
            border: 1px solid var(--border);
            border-radius: 99px; cursor: pointer;
            position: relative; transition: background .2s;
        }
        input[type="checkbox"]::after {
            content: '';
            position: absolute;
            top: 3px; left: 3px;
            width: 14px; height: 14px;
            background: var(--text-dim);
            border-radius: 50%;
            transition: transform .2s, background .2s;
        }
        input[type="checkbox"]:checked { background: var(--blue); border-color: var(--blue); }
        input[type="checkbox"]:checked::after { transform: translateX(16px); background: white; }

        /* ── BUTTONS ── */
        .btn {
            display: inline-flex; align-items: center; justify-content: center; gap: 6px;
            padding: 9px 18px;
            border-radius: 8px; border: 1px solid transparent;
            cursor: pointer; font-family: 'DM Sans', sans-serif;
            font-size: .84rem; font-weight: 600;
            text-decoration: none;
            white-space: nowrap;
            transition: all .15s;
        }
        .btn:hover { transform: translateY(-1px); }
        .btn:active { transform: translateY(0); }

        .btn-primary {
            background: linear-gradient(135deg, var(--blue), var(--blue-lt));
            color: white;
            box-shadow: 0 2px 10px rgba(29,95,212,.35);
        }
        .btn-primary:hover { box-shadow: 0 4px 18px rgba(29,95,212,.5); }

        .btn-success {
            background: linear-gradient(135deg, #1aad65, var(--green));
            color: white;
            box-shadow: 0 2px 10px rgba(46,201,126,.25);
        }
        .btn-success:hover { box-shadow: 0 4px 16px rgba(46,201,126,.4); }

        .btn-danger {
            background: rgba(230,57,70,.15);
            color: var(--red);
            border-color: rgba(230,57,70,.2);
        }
        .btn-danger:hover { background: var(--red); color: white; border-color: var(--red); }

        .btn-secondary {
            background: var(--glass2);
            color: var(--text);
            border-color: var(--border);
        }
        .btn-secondary:hover { background: rgba(255,255,255,.12); }
        .btn-secondary[style*="cursor: default"] { opacity: .55; }

        .btn-sm { padding: 6px 12px; font-size: .78rem; }

        /* ── TABLE ── */
        table { width: 100%; border-collapse: collapse; }
        thead tr { border-bottom: 1px solid var(--border); }
        thead th {
            padding: 10px 14px;
            font-size: .72rem; font-weight: 600;
            text-transform: uppercase; letter-spacing: .1em;
            color: var(--text-dim); text-align: left;
        }
        tbody tr {
            border-bottom: 1px solid var(--border);
            transition: background .12s;
        }
        tbody tr:last-child { border-bottom: none; }
        tbody tr:hover { background: var(--glass); }
        tbody td {
            padding: 13px 14px;
            font-size: .875rem;
            vertical-align: middle;
        }
        .td-actions { display: flex; gap: 8px; align-items: center; flex-wrap: wrap; }

        /* badge */
        .badge {
            display: inline-flex; align-items: center; gap: 4px;
            padding: 3px 9px; border-radius: 99px;
            font-size: .72rem; font-weight: 600;
        }
        .badge-green { background: rgba(46,201,126,.15); color: var(--green); border: 1px solid rgba(46,201,126,.25); }
        .badge-dim   { background: var(--glass); color: var(--text-dim); border: 1px solid var(--border); }

        /* ── PRIZE GRID ── */
        .prizes-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 14px;
            margin-top: 4px;
        }
        .prize-card {
            background: var(--navy3);
            border: 1px solid var(--border);
            border-radius: 12px;
            overflow: hidden;
            transition: transform .15s, box-shadow .15s;
        }
        .prize-card:hover { transform: translateY(-2px); box-shadow: 0 8px 28px rgba(0,0,0,.4); }
        .prize-card img {
            width: 100%; height: 140px;
            object-fit: cover; display: block;
        }
        .prize-card-placeholder {
            width: 100%; height: 140px;
            background: linear-gradient(135deg, var(--navy3), var(--navy2));
            display: flex; align-items: center; justify-content: center;
            font-size: 2.5rem;
        }
        .prize-body { padding: 13px; }
        .prize-title {
            font-weight: 700; font-size: .9rem;
            margin-bottom: 5px; color: var(--text);
        }
        .prize-meta {
            font-size: .78rem; color: var(--text-dim);
            margin-bottom: 10px; line-height: 1.6;
        }

        /* ── STAT CARD ── */
        .stat-card {
            background: var(--navy3);
            border: 1px solid var(--border);
            border-radius: 10px;
            padding: 14px 16px;
            display: flex; align-items: center; gap: 12px;
        }
        .stat-icon {
            width: 36px; height: 36px; border-radius: 8px;
            display: flex; align-items: center; justify-content: center;
            font-size: 1rem; flex-shrink: 0;
        }
        .stat-icon.blue  { background: rgba(29,95,212,.2); }
        .stat-icon.green { background: rgba(46,201,126,.2); }
        .stat-icon.gold  { background: rgba(249,199,79,.2); }
        .stat-val  { font-size: 1.4rem; font-weight: 800; line-height: 1; }
        .stat-lbl  { font-size: .75rem; color: var(--text-dim); margin-top: 2px; }

        /* ── SUMMARY BOX ── */
        .summary-box {
            background: var(--navy3);
            border: 1px solid var(--border);
            border-radius: 10px;
            padding: 14px 16px;
        }
        .summary-box .row-item {
            display: flex; justify-content: space-between; align-items: center;
            padding: 7px 0;
            border-bottom: 1px solid var(--border);
            font-size: .85rem;
        }
        .summary-box .row-item:last-child { border-bottom: none; }
        .summary-box .row-item span:first-child { color: var(--text-dim); }
        .summary-box .row-item span:last-child  { font-weight: 600; }

        /* ── PHOTO PREVIEW ── */
        .photo-preview-wrap {
            width: 100px; height: 100px;
            border-radius: 10px;
            border: 1px dashed var(--border);
            overflow: hidden;
            display: flex; align-items: center; justify-content: center;
            background: var(--navy3);
            font-size: 1.8rem; color: var(--text-dim);
            flex-shrink: 0;
        }
        .photo-preview-wrap img {
            width: 100%; height: 100%;
            object-fit: cover; display: none;
        }

        /* ── RIGHT PANEL ── */
        .right-panel {
            position: sticky; top: 80px;
            align-self: start;
            display: flex; flex-direction: column; gap: 18px;
        }

        /* ── ALERTS ── */
        .alert {
            padding: 12px 14px; border-radius: 10px;
            font-size: .875rem; font-weight: 600;
            margin-bottom: 16px;
        }
        .alert-success { background: rgba(46,201,126,.1); border: 1px solid rgba(46,201,126,.25); color: #2ec97e; }
        .alert-error   { background: rgba(230,57,70,.1);  border: 1px solid rgba(230,57,70,.25);  color: var(--red); }

        /* ── TOAST ── */
        .toast {
            position: fixed; top: 72px; right: 20px; z-index: 9999;
            min-width: 220px; max-width: 320px;
            padding: 13px 16px; border-radius: 12px;
            font-size: .875rem; font-weight: 600;
            box-shadow: 0 12px 32px rgba(0,0,0,.45);
            opacity: 0; pointer-events: none;
            transform: translateY(-8px) scale(.97);
            transition: opacity .18s, transform .18s;
        }
        .toast.show { opacity: 1; pointer-events: auto; transform: none; }
        .toast-success { background: var(--green); color: #092b1a; }
        .toast-error   { background: var(--red);   color: white; }

        /* ── DIVIDER ── */
        .divider {
            height: 1px; background: var(--border);
            margin: 20px 0;
        }

        /* ── SECTION GAP ── */
        .mt { margin-top: 18px; }
        .mt-sm { margin-top: 10px; }
        .flex-row { display: flex; gap: 10px; align-items: center; flex-wrap: wrap; }

        @media(max-width:900px) {
            .page-wrap { grid-template-columns: 1fr; padding: 16px; }
            .right-panel { position: static; }
        }
        @media(max-width:560px) {
            header { padding: 0 14px; gap: 10px; }
            nav a span.nav-label { display: none; }
        }
    </style>
</head>
<body>

<header>
    <div class="brand">
        <div class="brand-icon">🎯</div>
        <span>Lucky Draw</span>
        <span style="font-size:.7rem; color:var(--text-dim); font-weight:400; letter-spacing:.05em; margin-top:1px">ADMIN</span>
    </div>
    <div class="brand-sep"></div>
    <nav>
        <a href="/admin/draws" class="{{ request()->is('admin/draws*') ? 'active' : '' }}">
            <span>🏁</span><span class="nav-label">Draws</span>
        </a>
        <a href="/admin/prizes" class="{{ request()->is('admin/prizes*') ? 'active' : '' }}">
            <span>🎁</span><span class="nav-label">Prizes</span>
        </a>
        <div class="nav-spacer"></div>
        <a href="/" class="btn-back">← Back to Draw</a>
    </nav>
</header>

<div class="page-wrap">
    @yield('content')
</div>

@if(session('success') || session('error'))
    <div id="flashToast" class="toast {{ session('success') ? 'toast-success' : 'toast-error' }}">
        {{ session('success') ?? session('error') }}
    </div>
@endif

<script>
(function(){
    const t = document.getElementById('flashToast');
    if(!t) return;
    requestAnimationFrame(()=>{
        t.classList.add('show');
        setTimeout(()=> t.classList.remove('show'), 2800);
    });
})();
</script>
</body>
</html>