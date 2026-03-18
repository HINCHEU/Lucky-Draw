@extends('admin.layout')

@section('content')

    {{-- ── LEFT: Main Content ── --}}
    <div style="display:flex; flex-direction:column; gap:20px;">

        {{-- Add Prize --}}
        <div class="gc">
            <div class="stitle">🎁 Add New Prize</div>
            <form action="/admin/prizes" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="row">
                    <div style="flex:1 1 280px; min-width:180px">
                        <label for="name">Prize Name</label>
                        <input id="name" type="text" name="name" placeholder="e.g. OPPO A5, iPhone 16…" required>
                    </div>
                    <div style="flex:1 1 110px; min-width:100px">
                        <label for="quantity">Quantity</label>
                        <input id="quantity" type="number" name="quantity" placeholder="1" required min="1">
                    </div>
                    <div style="flex:1 1 110px; min-width:100px">
                        <label for="order">Draw Order</label>
                        <input id="order" type="number" name="order" placeholder="1" required min="1">
                    </div>
                    <div style="flex:1 1 200px; min-width:170px">
                        <label for="draw_id">Assign to Draw</label>
                        <select id="draw_id" name="draw_id">
                            <option value="">(no draw)</option>
                            @foreach ($draws as $drawOption)
                                <option value="{{ $drawOption->id }}">
                                    {{ $drawOption->name }}
                                    @if ($drawOption->draw_date)
                                        — {{ $drawOption->draw_date }}
                                    @endif
                                    @if ($drawOption->active)
                                        ⚡
                                    @endif
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="mt-sm">
                    <label for="description">Description</label>
                    <textarea id="description" name="description" rows="2" placeholder="Optional notes about this prize…"></textarea>
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
                        <button type="submit" class="btn btn-success">＋ Add Prize</button>
                    </div>
                </div>
            </form>
        </div>

        {{-- Existing Prizes --}}
        <div class="gc">
            <div class="stitle">🏷️ All Prizes</div>
            <div class="prizes-grid">
                @forelse ($prizes as $prize)
                    <div class="prize-card">
                        @if ($prize->photo_path)
                            <div
                                style="width:100%; height:180px; background:var(--navy3); display:flex; align-items:center; justify-content:center; overflow:hidden; border-radius:12px 12px 0 0; padding:8px;">
                                <img src="/storage/{{ $prize->photo_path }}" alt="{{ $prize->name }}"
                                    style="max-width:100%; max-height:164px; width:auto; height:auto; object-fit:contain; display:block; border-radius:6px;">
                            </div>
                        @else
                            <div class="prize-card-placeholder">🎁</div>
                        @endif
                        <div class="prize-body">
                            <div class="prize-title">{{ $prize->name }}</div>
                            <div class="prize-meta">
                                Qty: <strong>{{ $prize->quantity }}</strong>
                                &nbsp;·&nbsp; Winners: <strong>{{ $prize->winners()->count() }}</strong>
                                @if ($prize->draw)
                                    <br>
                                    <span style="color:var(--accent)">🏁 {{ $prize->draw->name }}</span>
                                @else
                                    <br><span style="opacity:.5">No draw assigned</span>
                                @endif
                            </div>

                            {{-- mini progress bar --}}
                            @php
                                $pct =
                                    $prize->quantity > 0
                                        ? min(100, round(($prize->winners()->count() / $prize->quantity) * 100))
                                        : 0;
                            @endphp
                            <div
                                style="height:4px; background:var(--navy2); border-radius:99px; margin-bottom:10px; overflow:hidden">
                                <div
                                    style="height:100%; width:{{ $pct }}%; background:linear-gradient(90deg, var(--blue), var(--blue-lt)); border-radius:99px; transition:width .5s;">
                                </div>
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
                        style="grid-column:1/-1; padding:32px; text-align:center; color:var(--text-dim);
                            background:var(--navy3); border-radius:10px; border:1px dashed var(--border)">
                        No prizes yet. Add your first prize above!
                    </div>
                @endforelse
            </div>
        </div>

    </div>

    {{-- ── RIGHT: Sidebar ── --}}
    <aside class="right-panel">
        <div class="gc">
            <div class="stitle">📊 Stats</div>
            <div style="display:flex; flex-direction:column; gap:10px">
                <div class="stat-card">
                    <div class="stat-icon gold">🎁</div>
                    <div>
                        <div class="stat-val">{{ $prizes->count() }}</div>
                        <div class="stat-lbl">Total Prizes</div>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon blue">🏁</div>
                    <div>
                        <div class="stat-val">{{ $draws->count() }}</div>
                        <div class="stat-lbl">Active Draws</div>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon green">🏅</div>
                    <div>
                        <div class="stat-val">{{ $prizes->sum(fn($p) => $p->winners()->count()) }}</div>
                        <div class="stat-lbl">Total Winners</div>
                    </div>
                </div>
            </div>
        </div>

        @if ($draws->count() > 0)
            <div class="gc">
                <div class="stitle">🏁 Draws</div>
                <div style="display:flex; flex-direction:column; gap:6px">
                    @foreach ($draws->take(6) as $d)
                        <a href="/admin/draws/{{ $d->id }}"
                            style="display:flex; align-items:center; justify-content:space-between;
                          padding:9px 12px; background:var(--navy3); border-radius:8px;
                          border:1px solid var(--border); text-decoration:none; color:var(--text);
                          transition:background .12s"
                            onmouseover="this.style.background='rgba(255,255,255,0.06)'"
                            onmouseout="this.style.background='var(--navy3)'">
                            <span style="font-size:.85rem; font-weight:500">{{ $d->name }}</span>
                            @if ($d->active)
                                <span class="badge badge-green" style="font-size:.67rem">Active</span>
                            @else
                                <span style="color:var(--text-dim); font-size:.78rem">→</span>
                            @endif
                        </a>
                    @endforeach
                </div>
            </div>
        @endif
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
