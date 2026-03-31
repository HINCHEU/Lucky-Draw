<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Admin - Lucky Draw</title>
    <link
        href="https://fonts.googleapis.com/css2?family=Battambang:wght@400;700&family=Exo+2:wght@300;400;600;700;900&display=swap"
        rel="stylesheet" />
    <style>
        :root {
            --blue-dark: #0d2b6b;
            --blue-mid: #1a4bbf;
            --gold: #f9c74f;
            --glass: rgba(255, 255, 255, 0.6);
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: 'Exo 2', sans-serif;
            background: linear-gradient(135deg, #f0f7ff 0%, #eaf6ff 100%);
            color: var(--blue-dark);
            min-height: 100vh;
        }

        header {
            background: linear-gradient(90deg, var(--blue-dark), var(--blue-mid));
            color: white;
            padding: 14px 20px;
            display: flex;
            align-items: center;
            gap: 12px;
            box-shadow: 0 6px 24px rgba(13, 43, 107, 0.15);
        }

        header h1 {
            font-family: 'Battambang', serif;
            margin: 0;
            font-size: 1.1rem;
        }

        .main-wrap {
            max-width: 1100px;
            margin: 28px auto;
            padding: 18px;
            display: grid;
            grid-template-columns: 1fr 340px;
            gap: 20px;
        }

        .gc {
            background: var(--glass);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.9);
            border-radius: 14px;
            padding: 20px;
            box-shadow: 0 8px 28px rgba(13, 43, 107, 0.08);
        }

        .stitle {
            font-family: 'Battambang', serif;
            color: var(--blue-dark);
            font-size: 1.2rem;
            margin-bottom: 12px;
        }

        form .row {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
        }

        form label {
            display: block;
            font-weight: 600;
            margin-bottom: 6px;
        }

        form input[type="text"],
        form input[type="number"],
        form input[type="file"],
        form textarea {
            width: 100%;
            padding: 10px 12px;
            border-radius: 10px;
            border: 1px solid rgba(26, 75, 191, 0.12);
            background: rgba(255, 255, 255, 0.9);
            font-family: inherit;
        }

        .btn {
            display: inline-block;
            padding: 10px 14px;
            border-radius: 999px;
            border: none;
            cursor: pointer;
            font-weight: 700;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--blue-dark), var(--blue-mid));
            color: white;
        }

        .btn-danger {
            background: #e02020;
            color: white;
        }

        .btn-success {
            background: linear-gradient(135deg, var(--gold), #f4a800);
            color: var(--blue-dark);
        }

        .prizes-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
            gap: 14px;
        }

        .prize-card {
            background: rgba(255, 255, 255, 0.9);
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 6px 18px rgba(13, 43, 107, 0.06);
        }

        .prize-card img {
            width: 100%;
            height: 150px;
            object-fit: cover;
            display: block;
        }

        .prize-body {
            padding: 12px;
        }

        .prize-title {
            font-weight: 800;
            font-family: 'Battambang', serif;
            margin-bottom: 6px;
        }

        .prize-meta {
            font-size: 0.9rem;
            color: var(--blue-mid);
            margin-bottom: 8px;
        }

        .right-panel {
            position: sticky;
            top: 20px;
            align-self: start;
        }

        @media(max-width:900px) {
            .main-wrap {
                grid-template-columns: 1fr;
                padding: 12px;
            }

            .right-panel {
                position: static;
            }
        }
    </style>
</head>

<body>
    <header>
        <div style="font-weight:900; font-size:1.05rem;">Admin</div>
        <div style="flex:1"></div>
        <a href="/" class="btn btn-primary">Back to Draw</a>
    </header>

    <div class="main-wrap">
        <div class="gc">
            <div class="stitle">🎯 Create Draw</div>
            <form action="/admin/draws" method="POST" style="margin-bottom:18px">
                @csrf
                <div style="display:flex; gap:10px; flex-wrap:wrap; align-items:center">
                    <div style="flex:1 1 260px">
                        <label for="draw_name">Draw Name</label>
                        <input id="draw_name" name="name" type="text" required>
                    </div>
                    <div style="flex:1 1 160px">
                        <label for="draw_date">Date</label>
                        <input id="draw_date" name="draw_date" type="date">
                    </div>
                    <div style="flex:0 0 auto; display:flex; gap:8px; align-items:center">
                        <label style="font-weight:600; margin-right:6px">Active</label>
                        <input type="checkbox" name="active" value="1">
                    </div>
                    <div>
                        <button class="btn btn-primary" type="submit">Create Draw</button>
                    </div>
                </div>
            </form>

            <div style="margin-bottom:10px">
                <label style="font-weight:700; display:block; margin-bottom:6px">Select Draw for Prize</label>
                <div style="display:flex; gap:10px; align-items:center; flex-wrap:wrap">
                    <form id="activateDrawForm" action="" method="POST" style="margin:0">
                        @csrf
                    </form>
                    <select id="drawSelect"
                        style="padding:10px 12px; border-radius:10px; border:1px solid rgba(26,75,191,0.12);">
                        <option value="">(none)</option>
                        @if (isset($draws))
                            @foreach ($draws as $d)
                                <option value="{{ $d->id }}"
                                    {{ isset($activeDraw) && $activeDraw && $activeDraw->id === $d->id ? 'selected' : '' }}>
                                    {{ $d->name }} @if ($d->draw_date)
                                        - {{ $d->draw_date }}
                                        @endif @if ($d->active)
                                            (active)
                                        @endif
                                </option>
                            @endforeach
                        @endif
                    </select>
                    <button id="activateBtn" class="btn btn-primary" style="padding:8px 12px">Set Active</button>
                </div>
            </div>

            <div class="stitle">➕ Add New Prize</div>
            <form action="/admin/prizes" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="row">
                    <div style="flex:1 1 320px; min-width:200px">
                        <label for="name">Name</label>
                        <input id="name" type="text" name="name" required>
                    </div>
                    <div style="flex:1 1 140px; min-width:120px">
                        <label for="quantity">Quantity</label>
                        <input id="quantity" type="number" name="quantity" required min="1">
                    </div>
                    <div style="flex:1 1 140px; min-width:120px">
                        <label for="order">Order</label>
                        <input id="order" type="number" name="order" required min="1">
                    </div>
                </div>

                <div style="margin-top:12px">
                    <label for="description">Description</label>
                    <textarea id="description" name="description" rows="3"></textarea>
                </div>

                <div style="margin-top:12px; display:flex; gap:12px; align-items:center; flex-wrap:wrap">
                    <div style="flex:1; min-width:160px">
                        <label for="photo">Photo</label>
                        <input id="photo" type="file" name="photo" accept="image/*">
                    </div>
                    <div style="flex:1 1 180px; min-width:160px">
                        <label for="draw_id">Draw</label>
                        <select id="draw_id" name="draw_id"
                            style="width:100%; padding:10px 12px; border-radius:10px; border:1px solid rgba(26,75,191,0.12); background:rgba(255,255,255,0.95)">
                            <option value="">(none)</option>
                            @if (isset($draws))
                                @foreach ($draws as $d)
                                    <option value="{{ $d->id }}"
                                        {{ isset($activeDraw) && $activeDraw && $activeDraw->id === $d->id ? 'selected' : '' }}>
                                        {{ $d->name }} @if ($d->draw_date)
                                            - {{ $d->draw_date }}
                                        @endif
                                    </option>
                                @endforeach
                            @endif
                        </select>
                    </div>
                    <div style="width:120px;">
                        <label
                            style="display:block; font-weight:600; margin-bottom:6px; visibility:hidden">Preview</label>
                        <img id="photoPreview" src="" alt="Preview"
                            style="display:none; width:120px; height:120px; object-fit:cover; border-radius:8px; border:1px solid rgba(0,0,0,0.06)">
                    </div>
                    <div>
                        <button type="submit" class="btn btn-success">Add Prize</button>
                    </div>
                </div>
            </form>

            <div style="margin-top:20px">
                <div class="stitle">🏷️ Existing Prizes</div>
                <div class="prizes-grid">
                    @foreach ($prizes as $prize)
                        <div class="prize-card">
                            @if ($prize->photo_path)
                                <img src="/storage/{{ $prize->photo_path }}" alt="{{ $prize->name }}">
                            @endif
                            <div class="prize-body">
                                <div class="prize-title">{{ $prize->name }}</div>
                                <div class="prize-meta">Quantity: {{ $prize->quantity }} • Winners:
                                    {{ $prize->winners()->count() }}</div>
                                <div style="display:flex; gap:8px;">
                                    <form action="/admin/prizes/{{ $prize->id }}" method="POST"
                                        onsubmit="return confirm('Delete this prize?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger">Delete</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <aside class="right-panel">
            <div class="gc">
                <div class="stitle">📈 Quick Stats</div>
                <div style="display:grid; gap:10px;">
                    <div style="background:white; padding:12px; border-radius:10px; font-weight:700;">Total prizes:
                        {{ $prizes->count() }}</div>
                    <div style="background:white; padding:12px; border-radius:10px;">Total winners:
                        {{ \App\Models\Winner::count() }}</div>
                </div>
            </div>
        </aside>
    </div>

    <script>
        (function() {
            const input = document.getElementById('photo');
            const preview = document.getElementById('photoPreview');

            if (!input || !preview) return;

            input.addEventListener('change', function(e) {
                const file = this.files && this.files[0];
                if (!file) {
                    preview.src = '';
                    preview.style.display = 'none';
                    return;
                }

                if (!file.type.startsWith('image/')) {
                    preview.src = '';
                    preview.style.display = 'none';
                    return;
                }

                const reader = new FileReader();
                reader.onload = function(ev) {
                    preview.src = ev.target.result;
                    preview.style.display = 'block';
                };
                reader.readAsDataURL(file);
            });
        })();
        (function() {
            const select = document.getElementById('drawSelect');
            const activateBtn = document.getElementById('activateBtn');
            const form = document.getElementById('activateDrawForm');

            if (!select || !activateBtn || !form) return;

            activateBtn.addEventListener('click', function(e) {
                e.preventDefault();
                const val = select.value;
                if (!val) {
                    alert('Please select a draw to activate.');
                    return;
                }
                form.action = '/admin/draws/' + val + '/activate';
                form.submit();
            });
        })();
    </script>
</body>

</html>
