@extends('admin.layout')

@section('content')
    {{-- ── LEFT: Main ── --}}
    <div style="display:flex; flex-direction:column; gap:20px;">

        {{-- Draw Edit --}}
        <div class="gc">
            <div class="stitle">🎯 Edit Draw</div>
            <form action="/admin/draws/{{ $draw->id }}" method="POST">
                @csrf @method('PUT')
                <div class="row">
                    <div style="flex:1 1 260px; min-width:180px">
                        <label for="draw_name">Draw Name</label>
                        <input id="draw_name" name="name" type="text" value="{{ $draw->name }}" required>
                    </div>
                    <div style="flex:1 1 150px; min-width:130px">
                        <label for="draw_date">Date</label>
                        <input id="draw_date" name="draw_date" type="date"
                            value="{{ optional($draw->draw_date)->format('Y-m-d') }}">
                    </div>
                    <div style="flex:0 0 auto; display:flex; align-items:flex-end; padding-bottom:1px">
                        <div class="toggle-wrap">
                            <input type="checkbox" name="active" id="active_chk" value="1"
                                {{ $draw->active ? 'checked' : '' }}>
                            <label for="active_chk">Active</label>
                        </div>
                    </div>
                </div>

                <div class="mt-sm">
                    <label for="description">Description</label>
                    <textarea id="description" name="description" rows="3" placeholder="Optional notes about this draw…">{{ $draw->description }}</textarea>
                </div>

                <div class="mt flex-row">
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                    <a href="/admin/draws" class="btn btn-secondary">← All Draws</a>
                </div>
            </form>
        </div>

        {{-- Add Prize --}}
        <div class="gc">
            <div class="stitle">➕ Add Prize to this Draw</div>
            <form id="drawPrizeForm" action="/admin/draws/{{ $draw->id }}/prizes" method="POST"
                enctype="multipart/form-data">
                @csrf
                <div class="row">
                    <div style="flex:1 1 240px; min-width:180px">
                        <label for="prize_name">Prize Name</label>
                        <input id="prize_name" type="text" name="name" placeholder="e.g. OPPO A5" required>
                    </div>
                    <div style="flex:1 1 110px; min-width:100px">
                        <label for="quantity">Quantity</label>
                        <input id="quantity" type="number" name="quantity" placeholder="1" required min="1">
                    </div>
                    <div style="flex:1 1 110px; min-width:100px">
                        <label for="order">Draw Order</label>
                        <input id="order" type="number" name="order" placeholder="1" required min="1">
                    </div>
                </div>

                <div class="mt-sm">
                    <label for="prize_desc">Description</label>
                    <textarea id="prize_desc" name="description" rows="2" placeholder="Optional prize description…"></textarea>
                </div>

                <div class="mt-sm flex-row">
                    <div style="flex:1 1 200px">
                        <label for="photo">Prize Photo</label>
                        <input id="photo" type="file" name="photo" accept="image/*">
                    </div>
                    <div class="photo-preview-wrap" id="previewWrap">
                        <span id="previewIcon">🖼</span>
                        <img id="photoPreview" src="" alt="Preview">
                    </div>
                    <div style="display:flex; align-items:flex-end">
                        <button type="submit" class="btn btn-success">Add Prize</button>
                    </div>
                </div>
            </form>
        </div>

        {{-- Employees / Tickets --}}
        <div class="gc">
            <div class="stitle">👥 Employee Tickets</div>
            <form action="/admin/draws/{{ $draw->id }}/employees" method="POST" style="display:grid; gap:16px;">
                @csrf
                <div class="row">
                    <div style="flex:1 1 220px; min-width:180px">
                        <label for="registration_number">Registration Number</label>
                        <input id="registration_number" name="registration_number" type="text" placeholder="e.g. 12345" required>
                    </div>
                    <div style="flex:1 1 220px; min-width:180px">
                        <label for="employee_name">Employee Name</label>
                        <input id="employee_name" name="employee_name" type="text" placeholder="e.g. Sok Dara" required>
                    </div>
                    <div style="flex:0 0 auto; display:flex; align-items:flex-end">
                        <button type="submit" class="btn btn-success">Add Employee</button>
                    </div>
                </div>
            </form>

            <div style="margin-top:18px; overflow-x:auto;">
                <table style="width:100%; border-collapse:collapse;">
                    <thead>
                        <tr style="background:var(--navy3);">
                            <th style="padding:10px; text-align:left; border-bottom:1px solid var(--border);">Reg. Number</th>
                            <th style="padding:10px; text-align:left; border-bottom:1px solid var(--border);">Employee Name</th>
                            <th style="padding:10px; text-align:center; border-bottom:1px solid var(--border);">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($employees as $employee)
                            <tr>
                                <td style="padding:10px; font-family:'DM Mono', monospace;">{{ $employee->registration_number }}</td>
                                <td style="padding:10px;">{{ $employee->employee_name }}</td>
                                <td style="padding:10px; text-align:center;">
                                    <form action="/admin/employees/{{ $employee->id }}" method="POST" onsubmit="return confirm('Remove this employee ticket?');">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm">Remove</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" style="padding:20px; text-align:center; color:var(--text-dim);">No employees added yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Prizes in this draw --}}
        <div class="gc">
            <div class="stitle">🏆 Prizes in this Draw</div>
            <div class="prizes-grid">
                @forelse ($prizes as $prize)
                    <div class="prize-card">
                        @if ($prize->photo_path)
                            <img src="/storage/{{ $prize->photo_path }}" alt="{{ $prize->name }}">
                        @else
                            <div class="prize-card-placeholder">🎁</div>
                        @endif
                        <div class="prize-body">
                            <div class="prize-title">{{ $prize->name }}</div>
                            <div class="prize-meta">
                                Qty: <strong>{{ $prize->quantity }}</strong>
                                &nbsp;·&nbsp; Winners: <strong>{{ $prize->winners()->count() }}</strong>
                                &nbsp;·&nbsp; Order: <strong>{{ $prize->order }}</strong>
                                {{-- &nbsp;·&nbsp; Draws: <strong>{{ $prize-> }}</strong> --}}
                            </div>
                            <form action="/admin/prizes/{{ $prize->id }}" method="POST"
                                onsubmit="return confirm('Delete this prize?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm"
                                    style="width:100%; justify-content:center">
                                    🗑 Delete
                                </button>
                            </form>
                        </div>
                    </div>
                @empty
                    <div
                        style="grid-column:1/-1; padding:28px; text-align:center; color:var(--text-dim);
                            background:var(--navy3); border-radius:10px; border:1px dashed var(--border)">
                        No prizes added yet. Use the form above to add one.
                    </div>
                @endforelse
            </div>
        </div>

        {{-- All Winners in this Draw --}}
        <div class="gc">
            <div class="stitle">🏅 All Winners ({{ $winners->count() }})</div>
            @if ($winners->count() > 0)
                <div style="overflow-x:auto">
                    <table style="width:100%; border-collapse:collapse">
                        <thead>
                            <tr
                                style="text-align:left; border-bottom:2px solid rgba(13,43,107,0.15); background:var(--navy3)">
                                <th style="padding:10px; font-weight:700; color:var(--text-main)">Code</th>
                                <th style="padding:10px; font-weight:700; color:var(--text-main)">Prize</th>
                                <th style="padding:10px; font-weight:700; color:var(--text-main)">Drawn At</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($winners as $winner)
                                <tr style="border-bottom:1px solid rgba(13,43,107,0.06)">
                                    <td
                                        style="padding:12px 10px; font-family:'DM Mono',monospace; font-weight:700; color:var(--red)">
                                        {{ str_pad($winner->code, 4, '0', STR_PAD_LEFT) }}
                                    </td>
                                    <td style="padding:12px 10px; color:var(--text-main)">
                                        {{ $winner->prize->name }}
                                    </td>
                                    <td style="padding:12px 10px; color:var(--text-dim); font-size:.85rem">
                                        {{ \Carbon\Carbon::parse($winner->drawn_at)->format('M d, Y · H:i') }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div
                    style="padding:28px; text-align:center; color:var(--text-dim); background:var(--navy3); border-radius:10px; border:1px dashed var(--border)">
                    No winners yet for this draw.
                </div>
            @endif
        </div>

    </div>

    {{-- ── RIGHT: Summary ── --}}
    <aside class="right-panel">
        <div class="gc">
            <div class="stitle">📋 Draw Summary</div>
            <div class="summary-box">
                <div class="row-item">
                    <span>Name</span>
                    <span>{{ $draw->name }}</span>
                </div>
                <div class="row-item">
                    <span>Date</span>
                    <span>{{ $draw->draw_date ?? '—' }}</span>
                </div>
                <div class="row-item">
                    <span>Total Tickets</span>
                    <span><span class="badge badge-dim">{{ $employees->count() }}</span></span>
                </div>
                <div class="row-item">
                    <span>Prizes</span>
                    <span><span class="badge badge-dim">{{ $prizes->count() }}</span></span>
                </div>
                <div class="row-item">
                    <span>Status</span>
                    <span>
                        @if ($draw->active)
                            <span class="badge badge-green">✓ Active</span>
                        @else
                            <span class="badge badge-dim">Inactive</span>
                        @endif
                    </span>
                </div>
            </div>
        </div>

        <div class="gc">
            <div class="stitle">📊 Prize Stats</div>
            <div style="display:flex; flex-direction:column; gap:8px">
                @forelse($prizes as $prize)
                    <div class="stat-card" style="gap:10px">
                        <div class="stat-icon gold">🏅</div>
                        <div style="flex:1; min-width:0">
                            <div
                                style="font-size:.82rem; font-weight:600; white-space:nowrap; overflow:hidden; text-overflow:ellipsis">
                                {{ $prize->name }}
                            </div>
                            <div style="font-size:.73rem; color:var(--text-dim)">
                                {{ $prize->winners()->count() }} / {{ $prize->quantity }} won
                            </div>
                        </div>
                    </div>
                @empty
                    <p style="color:var(--text-dim); font-size:.85rem; text-align:center; padding:8px 0">No prizes yet</p>
                @endforelse
            </div>
        </div>
    </aside>

    <script>
        (function() {
            const input = document.getElementById('photo');
            const preview = document.getElementById('photoPreview');
            const icon = document.getElementById('previewIcon');
            if (!input || !preview) return;
            input.addEventListener('change', function() {
                const file = this.files && this.files[0];
                if (!file || !file.type.startsWith('image/')) {
                    preview.style.display = 'none';
                    icon.style.display = '';
                    return;
                }
                const r = new FileReader();
                r.onload = ev => {
                    preview.src = ev.target.result;
                    preview.style.display = 'block';
                    icon.style.display = 'none';
                };
                r.readAsDataURL(file);
            });
        })();
    </script>
@endsection
