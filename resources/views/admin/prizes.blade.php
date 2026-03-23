@extends('admin.layout')

@section('content')

    {{-- ── LEFT: Main Content ── --}}
    <div style="display:flex; flex-direction:column; gap:20px;">

        {{-- All Prizes --}}
        <div class="gc">
            <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:16px; gap:12px;">
                <div class="stitle" style="margin-bottom:0;">🏷️ All Prizes</div>
                <div style="display:flex; gap:8px;">
                    <button onclick="openImportModal()" class="btn btn-primary">📄 Import From Excel</button>
                    <button onclick="openAddModal()" class="btn btn-success">＋ Add New Prize</button>
                </div>
            </div>

            {{-- Draw Filter --}}
            <div style="margin-bottom:16px;">
                <label for="drawFilter" style="display:block; margin-bottom:8px; font-weight:500;">Filter by Draw:</label>
                <select id="drawFilter" style="padding:8px 12px; border:1px solid var(--border); border-radius:6px; background:var(--navy2); color:var(--text); min-width:200px;">
                    <option value="">All Prizes</option>
                    @foreach ($draws as $drawOption)
                        <option value="{{ $drawOption->id }}" @if($drawOption->active) selected @endif>
                            {{ $drawOption->name }}
                            @if ($drawOption->draw_date) — {{ $drawOption->draw_date }} @endif
                            @if ($drawOption->active) ⚡ @endif
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="prizes-grid">
                @forelse ($prizes as $prize)
                    <div class="prize-card" data-prize-id="{{ $prize->id }}" data-draw-id="{{ $prize->draw_id ?? '' }}" style="cursor:pointer;">
                        @if ($prize->photo_path)
                            <div style="width:100%; height:180px; background:var(--navy3); display:flex; align-items:center; justify-content:center; overflow:hidden; border-radius:12px 12px 0 0; padding:8px;">
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
                                &nbsp;·&nbsp; Order: <strong>{{ $prize->order }}</strong>
                                @if ($prize->draw)
                                    <br><span style="color:var(--accent)">🏁 {{ $prize->draw->name }}</span>
                                @else
                                    <br><span style="opacity:.5">No draw assigned</span>
                                @endif
                            </div>

                            @php
                                $pct = $prize->quantity > 0
                                    ? min(100, round(($prize->winners()->count() / $prize->quantity) * 100))
                                    : 0;
                            @endphp
                            <div style="height:4px; background:var(--navy2); border-radius:99px; margin-bottom:10px; overflow:hidden">
                                <div style="height:100%; width:{{ $pct }}%; background:linear-gradient(90deg, var(--blue), var(--blue-lt)); border-radius:99px; transition:width .5s;"></div>
                            </div>

                            <button type="button" class="btn btn-danger btn-sm"
                                style="width:100%; justify-content:center"
                                onclick="openDeleteModal('{{ $prize->id }}', '{{ addslashes($prize->name) }}', '/admin/prizes/{{ $prize->id }}', 'prize'); event.stopPropagation();">
                                🗑 Delete
                            </button>
                        </div>
                    </div>
                @empty
                    <div style="grid-column:1/-1; padding:32px; text-align:center; color:var(--text-dim);
                        background:var(--navy3); border-radius:10px; border:1px dashed var(--border)">
                        No prizes yet. Click "Add New Prize" to get started!
                    </div>
                @endforelse

                <div id="filteredEmptyMessage"
                    style="grid-column:1/-1; padding:32px; text-align:center; color:var(--text-dim);
                        background:var(--navy3); border-radius:10px; border:1px dashed var(--border); display:none;">
                    No prizes found for the selected draw.
                </div>
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

    {{-- ── Add Prize Modal ── --}}
    <div id="addPrizeModal" style="display:none; position:fixed; top:0; left:0; right:0; bottom:0; background:rgba(0,0,0,0.7); z-index:1000; align-items:center; justify-content:center;">
        <div style="background:var(--navy); border-radius:12px; padding:24px; max-width:500px; width:90%; max-height:90vh; overflow-y:auto; box-shadow:0 8px 32px rgba(0,0,0,0.3); animation:modalPop .25s cubic-bezier(.34,1.56,.64,1);">
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
                <h2 style="margin:0; font-size:1.5rem;">🎁 Add New Prize</h2>
                <button onclick="closeAddModal()" style="background:none; border:none; font-size:1.5rem; cursor:pointer; color:var(--text); padding:0; width:30px; height:30px; display:flex; align-items:center; justify-content:center;">×</button>
            </div>
            <form id="addPrizeForm" action="/admin/prizes" method="POST" enctype="multipart/form-data">
                @csrf
                <div id="addFormErrors" style="display:none; background:rgba(230,57,70,.1); border:1px solid rgba(230,57,70,.25); border-radius:8px; padding:12px; margin-bottom:16px; color:var(--red);">
                    <div style="font-weight:600; margin-bottom:8px;">Please fix the following errors:</div>
                    <ul id="addErrorList" style="margin:0; padding-left:20px;"></ul>
                </div>
                <div style="margin-bottom:16px;">
                    <label for="addName">Prize Name</label>
                    <input id="addName" type="text" name="name" placeholder="e.g. OPPO A5, iPhone 16…" required
                        style="width:100%; padding:8px 12px; border:1px solid var(--border); border-radius:6px; background:var(--navy2); color:var(--text);">
                </div>
                <div style="margin-bottom:16px;">
                    <label for="addDescription">Description</label>
                    <textarea id="addDescription" name="description" rows="2" placeholder="Optional notes about this prize…"
                        style="width:100%; padding:8px 12px; border:1px solid var(--border); border-radius:6px; background:var(--navy2); color:var(--text); font-family:inherit;"></textarea>
                </div>
                <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px; margin-bottom:16px;">
                    <div>
                        <label for="addQuantity">Quantity</label>
                        <input id="addQuantity" type="number" name="quantity" placeholder="1" required min="1"
                            style="width:100%; padding:8px 12px; border:1px solid var(--border); border-radius:6px; background:var(--navy2); color:var(--text);">
                    </div>
                    <div>
                        <label for="addOrder">Draw Order</label>
                        <input id="addOrder" type="number" name="order" placeholder="1" required min="1"
                            style="width:100%; padding:8px 12px; border:1px solid var(--border); border-radius:6px; background:var(--navy2); color:var(--text);">
                    </div>
                </div>
                <div style="margin-bottom:16px;">
                    <label for="addDrawId">Assign to Draw</label>
                    <select id="addDrawId" name="draw_id"
                        style="width:100%; padding:8px 12px; border:1px solid var(--border); border-radius:6px; background:var(--navy2); color:var(--text);">
                        <option value="">(no draw)</option>
                        @foreach ($draws as $drawOption)
                            <option value="{{ $drawOption->id }}" @if($drawOption->active) selected @endif>
                                {{ $drawOption->name }}
                                @if ($drawOption->draw_date) — {{ $drawOption->draw_date }} @endif
                                @if ($drawOption->active) ⚡ @endif
                            </option>
                        @endforeach
                    </select>
                </div>
                <div style="margin-bottom:20px;">
                    <label for="addPhoto">Prize Photo</label>
                    <input id="addPhoto" type="file" name="photo" accept="image/*" style="display:block; margin-bottom:12px;">
                    <div style="width:100px; height:100px; background:var(--navy3); border-radius:8px; display:flex; align-items:center; justify-content:center; overflow:hidden;">
                        <span id="addPreviewIcon" style="font-size:2rem;">🖼</span>
                        <img id="addPhotoPreview" src="" alt="Preview" style="display:none; max-width:100%; max-height:100%; object-fit:contain;">
                    </div>
                </div>
                <div style="display:flex; gap:12px; justify-content:flex-end;">
                    <button type="button" onclick="closeAddModal()" class="btn" style="background:var(--navy3); border:1px solid var(--border);">Cancel</button>
                    <button type="submit" class="btn btn-success">＋ Add Prize</button>
                </div>
            </form>
        </div>
    </div>

    {{-- ── Edit Prize Modal ── --}}
    <div id="editPrizeModal" style="display:none; position:fixed; top:0; left:0; right:0; bottom:0; background:rgba(0,0,0,0.7); z-index:1000; align-items:center; justify-content:center;">
        <div style="background:var(--navy); border-radius:12px; padding:24px; max-width:500px; width:90%; max-height:90vh; overflow-y:auto; box-shadow:0 8px 32px rgba(0,0,0,0.3); animation:modalPop .25s cubic-bezier(.34,1.56,.64,1);">
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
                <h2 style="margin:0; font-size:1.5rem;">✏️ Edit Prize</h2>
                <button id="closeEditModal" style="background:none; border:none; font-size:1.5rem; cursor:pointer; color:var(--text); padding:0; width:30px; height:30px; display:flex; align-items:center; justify-content:center;">×</button>
            </div>
            <form id="editPrizeForm" method="POST" enctype="multipart/form-data">
                @csrf @method('PUT')
                <div id="editFormErrors" style="display:none; background:rgba(230,57,70,.1); border:1px solid rgba(230,57,70,.25); border-radius:8px; padding:12px; margin-bottom:16px; color:var(--red);">
                    <div style="font-weight:600; margin-bottom:8px;">Please fix the following errors:</div>
                    <ul id="editErrorList" style="margin:0; padding-left:20px;"></ul>
                </div>
                <input type="hidden" id="editPrizeId" name="prize_id">
                <div style="margin-bottom:16px;">
                    <label for="editName">Prize Name</label>
                    <input id="editName" type="text" name="name" required
                        style="width:100%; padding:8px 12px; border:1px solid var(--border); border-radius:6px; background:var(--navy2); color:var(--text);">
                </div>
                <div style="margin-bottom:16px;">
                    <label for="editDescription">Description</label>
                    <textarea id="editDescription" name="description" rows="2"
                        style="width:100%; padding:8px 12px; border:1px solid var(--border); border-radius:6px; background:var(--navy2); color:var(--text); font-family:inherit;"></textarea>
                </div>
                <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px; margin-bottom:16px;">
                    <div>
                        <label for="editQuantity">Quantity</label>
                        <input id="editQuantity" type="number" name="quantity" required min="1"
                            style="width:100%; padding:8px 12px; border:1px solid var(--border); border-radius:6px; background:var(--navy2); color:var(--text);">
                    </div>
                    <div>
                        <label for="editOrder">Draw Order</label>
                        <input id="editOrder" type="number" name="order" required min="1"
                            style="width:100%; padding:8px 12px; border:1px solid var(--border); border-radius:6px; background:var(--navy2); color:var(--text);">
                    </div>
                </div>
                <div style="margin-bottom:16px;">
                    <label for="editDrawId">Assign to Draw</label>
                    <select id="editDrawId" name="draw_id"
                        style="width:100%; padding:8px 12px; border:1px solid var(--border); border-radius:6px; background:var(--navy2); color:var(--text);">
                        <option value="">(no draw)</option>
                    </select>
                </div>
                <div style="margin-bottom:16px;">
                    <label for="photoEdit">Prize Photo</label>
                    <input id="photoEdit" type="file" name="photo" accept="image/*" style="display:block; margin-bottom:12px;">
                    <div style="width:100px; height:100px; background:var(--navy3); border-radius:8px; display:flex; align-items:center; justify-content:center; overflow:hidden;">
                        <span id="photoEditIcon" style="font-size:2rem;">🖼</span>
                        <img id="photoEditPreview" src="" alt="Preview" style="display:none; max-width:100%; max-height:100%; object-fit:contain;">
                    </div>
                </div>
                <div style="display:flex; gap:12px; justify-content:flex-end;">
                    <button type="button" onclick="closeEditModal()" class="btn" style="background:var(--navy3); border:1px solid var(--border);">Cancel</button>
                    <button type="submit" class="btn btn-success">💾 Save Changes</button>
                </div>
            </form>
        </div>
    </div>

    {{-- ── Delete Confirmation Modal ── --}}
    <div id="deleteConfirmModal" style="display:none; position:fixed; top:0; left:0; right:0; bottom:0; background:rgba(0,0,0,0.5); z-index:2000; align-items:center; justify-content:center; backdrop-filter:blur(2px);">
        <div style="background:var(--navy); border-radius:14px; padding:32px; max-width:420px; width:90%; box-shadow:0 20px 60px rgba(0,0,0,0.5); animation:modalSlideIn .3s cubic-bezier(.34,1.56,.64,1);">
            <div id="deleteConfirmTitle" style="font-size:1.5rem; font-weight:600; margin-bottom:12px; color:var(--danger, #ff4757);"></div>
            <div id="deleteConfirmMessage" style="color:var(--text-dim); margin-bottom:28px; line-height:1.6;"></div>
            <div style="display:flex; gap:12px; justify-content:flex-end;">
                <button id="deleteConfirmCancel" type="button" class="btn" style="background:var(--navy3); border:1px solid var(--border); min-width:100px;">Cancel</button>
                <button id="deleteConfirmBtn" type="button" class="btn btn-danger" style="min-width:100px;">🗑 Delete</button>
            </div>
        </div>
    </div>

    {{-- ── Import Excel Modal ── --}}
    <div id="importPrizeModal" style="display:none; position:fixed; top:0; left:0; right:0; bottom:0; background:rgba(0,0,0,0.7); z-index:1000; align-items:center; justify-content:center;">
        <div style="background:var(--navy); border-radius:12px; padding:24px; max-width:600px; width:90%; max-height:90vh; overflow-y:auto; box-shadow:0 8px 32px rgba(0,0,0,0.3); animation:modalPop .25s cubic-bezier(.34,1.56,.64,1);">
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
                <h2 style="margin:0; font-size:1.5rem;">📥 Import Prizes from Excel</h2>
                <button onclick="closeImportModal()" style="background:none; border:none; font-size:1.5rem; cursor:pointer; color:var(--text); padding:0; width:30px; height:30px; display:flex; align-items:center; justify-content:center;">×</button>
            </div>

            <div style="margin-bottom:16px; padding:12px 16px; background:rgba(74,144,226,.08); border:1px solid rgba(74,144,226,.2); border-radius:8px; font-size:.875rem; color:var(--text-dim); line-height:1.6;">
                Required columns: <strong>name</strong>, <strong>quantity</strong>, <strong>order</strong><br>
                Optional columns: <strong>description</strong>, <strong>draw_id</strong><br>
                Row 1 must be the header row. All subsequent rows are imported.
            </div>

            <div style="margin-bottom:16px;">
                <label for="importFile" style="display:block; margin-bottom:8px; font-weight:600;">Upload Excel or CSV file (.xlsx, .xls, .csv)</label>
                <input id="importFile" type="file" accept=".csv,.xlsx,.xls" style="width:100%;" />
            </div>

            <div id="importPreview" style="display:none; margin-bottom:16px; max-height:300px; overflow:auto;">
                <div id="importRowCount" style="font-size:.85rem; color:var(--text-dim); margin-bottom:8px;"></div>
                <table style="width:100%; border-collapse:collapse;">
                    <thead>
                        <tr>
                            <th style="border:1px solid var(--border); padding:8px; text-align:left;">#</th>
                            <th style="border:1px solid var(--border); padding:8px; text-align:left;">Name</th>
                            <th style="border:1px solid var(--border); padding:8px; text-align:left;">Description</th>
                            <th style="border:1px solid var(--border); padding:8px; text-align:left;">Quantity</th>
                            <th style="border:1px solid var(--border); padding:8px; text-align:left;">Order</th>
                            <th style="border:1px solid var(--border); padding:8px; text-align:left;">Draw ID</th>
                        </tr>
                    </thead>
                    <tbody id="importPreviewBody"></tbody>
                </table>
            </div>

            <div style="display:flex; justify-content:flex-end; gap:8px;">
                <button type="button" class="btn" style="background:var(--navy3);" onclick="closeImportModal()">Cancel</button>
                <button id="importSubmitBtn" type="button" class="btn btn-success" disabled>Import</button>
            </div>
        </div>
    </div>

    <style>
        @keyframes modalPop {
            from { transform: scale(.88); opacity: 0; }
            to   { transform: scale(1);   opacity: 1; }
        }
        @keyframes modalSlideIn {
            from { opacity: 0; transform: scale(0.95) translateY(-20px); }
            to   { opacity: 1; transform: scale(1) translateY(0); }
        }
        @keyframes slideInRight {
            from { opacity: 0; transform: translateX(100%); }
            to   { opacity: 1; transform: translateX(0); }
        }
        @keyframes slideOutRight {
            from { opacity: 1; transform: translateX(0); }
            to   { opacity: 0; transform: translateX(100%); }
        }
    </style>

    {{-- SheetJS from CDN --}}
    <script src="https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js"></script>

    {{-- ALL modals above — script runs last, getElementById always finds elements --}}
    <script>
        var _deleteUrl  = '';
        var _deleteType = '';
        var _deleteName = '';

        // ── Delete Modal ─────────────────────────────────────────────────────
        function openDeleteModal(id, name, url, type) {
            _deleteUrl  = url;
            _deleteType = type || 'prize';
            _deleteName = name;
            document.getElementById('deleteConfirmTitle').innerHTML   = _deleteType === 'winner' ? '🏅 Delete Winner?' : '🗑️ Delete Prize?';
            document.getElementById('deleteConfirmMessage').innerHTML = _deleteType === 'winner'
                ? 'Are you sure you want to delete winner <strong>' + name + '</strong>? This cannot be undone.'
                : 'Are you sure you want to delete prize <strong>' + name + '</strong>? This cannot be undone.';
            document.getElementById('deleteConfirmModal').style.display = 'flex';
        }

        function closeDeleteModal() {
            document.getElementById('deleteConfirmModal').style.display = 'none';
        }

        document.getElementById('deleteConfirmCancel').addEventListener('click', closeDeleteModal);
        document.getElementById('deleteConfirmModal').addEventListener('click', function(e) { if (e.target === this) closeDeleteModal(); });

        document.getElementById('deleteConfirmBtn').addEventListener('click', function() {
            var csrfToken = document.querySelector('meta[name="csrf-token"]')
                ? document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                : document.querySelector('input[name="_token"]').value;

            var fetchOptions;
            if (_deleteType === 'winner') {
                fetchOptions = { method: 'DELETE', headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' } };
            } else {
                var fd = new FormData();
                fd.append('_token', csrfToken);
                fd.append('_method', 'DELETE');
                fetchOptions = { method: 'POST', body: fd };
            }

            fetch(_deleteUrl, fetchOptions)
                .then(function(res) {
                    if (!res.ok) throw new Error('Server returned ' + res.status);
                    closeDeleteModal();
                    showSuccessNotification(_deleteType === 'winner' ? 'Winner deleted!' : 'Prize deleted!');
                    setTimeout(function() { location.reload(); }, 1500);
                })
                .catch(function(err) {
                    console.error(err);
                    alert('Error deleting ' + _deleteType + '. Please try again.');
                });
        });

        // ── Add Modal ────────────────────────────────────────────────────────
        function openAddModal() {
            document.getElementById('addPrizeModal').style.display = 'flex';
        }

        function closeAddModal() {
            document.getElementById('addPrizeModal').style.display = 'none';
            document.getElementById('addPrizeForm').reset();
            document.getElementById('addFormErrors').style.display = 'none';
            var prev = document.getElementById('addPhotoPreview');
            var icon = document.getElementById('addPreviewIcon');
            if (prev) { prev.style.display = 'none'; prev.src = ''; }
            if (icon)   icon.style.display = '';
        }

        document.getElementById('addPrizeModal').addEventListener('click', function(e) { if (e.target === this) closeAddModal(); });

        document.getElementById('addPhoto').addEventListener('change', function() {
            var prev = document.getElementById('addPhotoPreview');
            var icon = document.getElementById('addPreviewIcon');
            var file = this.files && this.files[0];
            if (!file || !file.type.startsWith('image/')) { prev.style.display = 'none'; icon.style.display = ''; return; }
            var r = new FileReader();
            r.onload = function(ev) { prev.src = ev.target.result; prev.style.display = 'block'; icon.style.display = 'none'; };
            r.readAsDataURL(file);
        });

        document.getElementById('addPrizeForm').addEventListener('submit', function(e) {
            e.preventDefault();
            fetch(this.action, { method: 'POST', body: new FormData(this), headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                .then(function(res) { return res.json().then(function(d) { if (!res.ok) throw d; return d; }); })
                .then(function() { closeAddModal(); showSuccessNotification('Prize added successfully!'); setTimeout(function() { location.reload(); }, 1500); })
                .catch(function(err) {
                    if (err.errors) { showFormErrors('add', err.errors); }
                    else { alert('Error adding prize: ' + (err.message || 'Unknown error')); }
                });
        });

        // ── Edit Modal ───────────────────────────────────────────────────────
        function closeEditModal() {
            document.getElementById('editPrizeModal').style.display = 'none';
            document.getElementById('editPrizeForm').reset();
            document.getElementById('editFormErrors').style.display = 'none';
        }

        document.getElementById('closeEditModal').addEventListener('click', closeEditModal);
        document.getElementById('editPrizeModal').addEventListener('click', function(e) { if (e.target === this) closeEditModal(); });

        document.getElementById('photoEdit').addEventListener('change', function() {
            var prev = document.getElementById('photoEditPreview');
            var icon = document.getElementById('photoEditIcon');
            var file = this.files && this.files[0];
            if (!file || !file.type.startsWith('image/')) { prev.style.display = 'none'; icon.style.display = ''; return; }
            var r = new FileReader();
            r.onload = function(ev) { prev.src = ev.target.result; prev.style.display = 'block'; icon.style.display = 'none'; };
            r.readAsDataURL(file);
        });

        function openEditModal(prizeId) {
            fetch('/admin/prizes/' + prizeId)
                .then(function(r) { return r.json(); })
                .then(function(data) {
                    document.getElementById('editPrizeId').value     = data.id;
                    document.getElementById('editName').value        = data.name;
                    document.getElementById('editDescription').value = data.description || '';
                    document.getElementById('editQuantity').value    = data.quantity;
                    document.getElementById('editOrder').value       = data.order;

                    var drawSelect = document.getElementById('editDrawId');
                    drawSelect.innerHTML = '<option value="">(no draw)</option>';
                    data.draws.forEach(function(draw) {
                        var opt = document.createElement('option');
                        opt.value = draw.id;
                        opt.textContent = draw.name + (draw.draw_date ? ' — ' + draw.draw_date : '') + (draw.active ? ' ⚡' : '');
                        drawSelect.appendChild(opt);
                    });
                    drawSelect.value = data.draw_id || '';

                    var photoIcon    = document.getElementById('photoEditIcon');
                    var photoPreview = document.getElementById('photoEditPreview');
                    var photoInput   = document.getElementById('photoEdit');
                    photoIcon.style.display    = '';
                    photoPreview.style.display = 'none';
                    photoInput.value           = '';
                    if (data.photo_path) {
                        photoPreview.src           = '/storage/' + data.photo_path;
                        photoPreview.style.display = 'block';
                        photoIcon.style.display    = 'none';
                    }

                    document.getElementById('editPrizeForm').action         = '/admin/prizes/' + prizeId;
                    document.getElementById('editPrizeModal').style.display = 'flex';
                })
                .catch(function() { alert('Error loading prize data'); });
        }

        document.getElementById('editPrizeForm').addEventListener('submit', function(e) {
            e.preventDefault();
            fetch(this.action, { method: 'POST', body: new FormData(this), headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                .then(function(res) { return res.json().then(function(d) { if (!res.ok) throw d; return d; }); })
                .then(function() { closeEditModal(); showSuccessNotification('Prize updated successfully!'); setTimeout(function() { location.reload(); }, 1500); })
                .catch(function(err) {
                    if (err.errors) { showFormErrors('edit', err.errors); }
                    else { alert('Error updating prize: ' + (err.message || 'Unknown error')); }
                });
        });

        document.querySelectorAll('.prize-card').forEach(function(card) {
            card.addEventListener('click', function() { openEditModal(this.dataset.prizeId); });
        });

        // ── Draw Filter ──────────────────────────────────────────────────────
        var drawFilter = document.getElementById('drawFilter');
        drawFilter.addEventListener('change', function() {
            var selected = this.value;
            var cards    = document.querySelectorAll('.prize-card');
            var visible  = 0;
            cards.forEach(function(card) {
                var match = selected === '' || card.dataset.drawId === selected;
                card.style.display = match ? '' : 'none';
                if (match) visible++;
            });
            var empty = document.getElementById('filteredEmptyMessage');
            if (empty) empty.style.display = (visible === 0 && cards.length > 0) ? 'block' : 'none';
        });
        drawFilter.dispatchEvent(new Event('change'));

        // ── Shared helpers ───────────────────────────────────────────────────
        function showFormErrors(formType, errors) {
            var errorDiv  = document.getElementById(formType + 'FormErrors');
            var errorList = document.getElementById(formType + 'ErrorList');
            errorList.innerHTML = '';
            for (var field in errors) {
                if (errors.hasOwnProperty(field)) {
                    errors[field].forEach(function(msg) {
                        var li = document.createElement('li');
                        li.textContent = msg;
                        errorList.appendChild(li);
                    });
                }
            }
            errorDiv.style.display = 'block';
            errorDiv.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        }

        function showSuccessNotification(message) {
            var el = document.createElement('div');
            el.textContent = '✓ ' + message;
            el.style.cssText = 'position:fixed;top:20px;right:20px;background:linear-gradient(135deg,#10b981,#059669);color:white;padding:16px 24px;border-radius:8px;box-shadow:0 10px 30px rgba(16,185,129,.3);z-index:3000;font-weight:500;animation:slideInRight .3s cubic-bezier(.34,1.56,.64,1);';
            document.body.appendChild(el);
            setTimeout(function() {
                el.style.animation = 'slideOutRight .3s cubic-bezier(.34,1.56,.64,1)';
                setTimeout(function() { el.remove(); }, 300);
            }, 2000);
        }

        // ── Import Modal ─────────────────────────────────────────────────────
        var _importRows = [];

        function openImportModal() {
            document.getElementById('importPrizeModal').style.display = 'flex';
            document.getElementById('importFile').value = '';
            document.getElementById('importPreview').style.display = 'none';
            document.getElementById('importPreviewBody').innerHTML = '';
            document.getElementById('importSubmitBtn').disabled = true;
            _importRows = [];
        }

        function closeImportModal() {
            document.getElementById('importPrizeModal').style.display = 'none';
        }

        document.getElementById('importPrizeModal').addEventListener('click', function(e) { if (e.target === this) closeImportModal(); });

        function parseFile(file) {
            return new Promise(function(resolve, reject) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    try {
                        var data     = new Uint8Array(e.target.result);
                        var workbook = XLSX.read(data, { type: 'array' });
                        var sheet    = workbook.Sheets[workbook.SheetNames[0]];
                        var json     = XLSX.utils.sheet_to_json(sheet, { header: 1, defval: '' });

                        if (json.length <= 1) {
                            reject('File is empty or has no data rows.');
                            return;
                        }

                        // Normalise headers: lowercase + trim
                        var headers = json[0].map(function(h) {
                            return String(h === null || h === undefined ? '' : h).toLowerCase().trim();
                        });

                        // Map every data row — safely handle missing/undefined cells
                        var rows = json.slice(1)
                            .filter(function(row) {
                                // Drop fully empty rows
                                return row.some(function(cell) {
                                    return cell !== undefined && cell !== null && String(cell).trim() !== '';
                                });
                            })
                            .map(function(row) {
                                var obj = {};
                                headers.forEach(function(header, i) {
                                    var cell = row[i];
                                    // undefined / null → empty string; numbers → string; strings → trim
                                    obj[header] = (cell === undefined || cell === null) ? '' : String(cell).trim();
                                });
                                return obj;
                            });

                        if (rows.length === 0) {
                            reject('File contains no valid data rows.');
                            return;
                        }

                        resolve(rows);
                    } catch (err) {
                        reject('Error parsing file: ' + err.message);
                    }
                };
                reader.onerror = function() { reject('Error reading file.'); };
                reader.readAsArrayBuffer(file);
            });
        }

        document.getElementById('importFile').addEventListener('change', function() {
            var file = this.files[0];
            if (!file) return;

            var name = file.name.toLowerCase();
            if (!['.csv', '.xlsx', '.xls'].some(function(ext) { return name.endsWith(ext); })) {
                alert('Please upload a CSV or Excel file (.csv, .xlsx, .xls).');
                this.value = '';
                return;
            }

            parseFile(file)
                .then(function(rows) {
                    _importRows = rows;

                    var body = document.getElementById('importPreviewBody');
                    body.innerHTML = '';
                    rows.forEach(function(r, idx) {
                        var tr = document.createElement('tr');
                        tr.innerHTML =
                            '<td style="border:1px solid var(--border);padding:8px;">' + (idx + 1) + '</td>' +
                            '<td style="border:1px solid var(--border);padding:8px;">' + (r.name        || '') + '</td>' +
                            '<td style="border:1px solid var(--border);padding:8px;">' + (r.description || '') + '</td>' +
                            '<td style="border:1px solid var(--border);padding:8px;">' + (r.quantity    || '') + '</td>' +
                            '<td style="border:1px solid var(--border);padding:8px;">' + (r.order       || '') + '</td>' +
                            '<td style="border:1px solid var(--border);padding:8px;">' + (r.draw_id     || '') + '</td>';
                        body.appendChild(tr);
                    });

                    document.getElementById('importRowCount').textContent = rows.length + ' row(s) ready to import.';
                    document.getElementById('importPreview').style.display = 'block';
                    document.getElementById('importSubmitBtn').disabled = false;
                })
                .catch(function(err) {
                    alert('Error processing file: ' + err);
                    document.getElementById('importPreview').style.display = 'none';
                    document.getElementById('importSubmitBtn').disabled = true;
                    _importRows = [];
                });
        });

        document.getElementById('importSubmitBtn').addEventListener('click', function() {
            if (!_importRows.length) return;

            var csrfToken = document.querySelector('meta[name="csrf-token"]')
                ? document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                : document.querySelector('input[name="_token"]').value;

            this.disabled = true;
            this.textContent = 'Importing…';

            fetch('/admin/prizes/import', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({ prizes: _importRows })
            })
            .then(function(res) { return res.json(); })
            .then(function(data) {
                if (!data.success) {
                    alert('Import failed: ' + (data.message || 'Unknown error'));
                    document.getElementById('importSubmitBtn').disabled = false;
                    document.getElementById('importSubmitBtn').textContent = 'Import';
                    return;
                }

                var msg = data.created + ' prize(s) imported successfully!';
                if (data.errors && data.errors.length > 0) {
                    msg += '\n\n' + data.errors.length + ' row(s) skipped:\n';
                    data.errors.forEach(function(e) {
                        msg += '  Row ' + e.row + ': ' + e.errors.join(', ') + '\n';
                    });
                }

                closeImportModal();
                showSuccessNotification(data.created + ' prize(s) imported!');
                if (data.errors && data.errors.length > 0) {
                    setTimeout(function() { alert(msg); }, 400);
                }
                setTimeout(function() { location.reload(); }, 1500);
            })
            .catch(function(err) {
                console.error('Import error', err);
                alert('Import failed. Please try again.');
                document.getElementById('importSubmitBtn').disabled = false;
                document.getElementById('importSubmitBtn').textContent = 'Import';
            });
        });
    </script>

@endsection