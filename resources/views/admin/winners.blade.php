@extends('admin.layout')

@section('content')

    {{-- ── LEFT: Main Content ── --}}
    <div style="display:flex; flex-direction:column; gap:20px;">

        {{-- All Winners --}}
        <div class="gc">
            <div class="stitle">🏅 All Winners</div>

            {{-- Draw Filter --}}
            <div style="margin-bottom:16px;">
                <label for="drawFilter" style="display:block; margin-bottom:8px; font-weight:500;">Filter by Draw:</label>
                <select id="drawFilter" style="padding:8px 12px; border:1px solid var(--border); border-radius:6px; background:var(--navy2); color:var(--text); min-width:200px;">
                    <option value="">All Winners</option>
                    @foreach ($draws as $drawOption)
                        <option value="{{ $drawOption->id }}" @if($drawOption->active) selected @endif>
                            {{ $drawOption->name }}
                            @if ($drawOption->draw_date) — {{ $drawOption->draw_date }} @endif
                            @if ($drawOption->active) ⚡ @endif
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="winners-table-container">
                <table class="winners-table" style="width:100%; border-collapse:collapse;">
                    <thead>
                        <tr style="background:var(--navy3);">
                            <th style="padding:12px 16px; text-align:left; border-bottom:1px solid var(--border); font-weight:600;">Winner Code</th>
                            <th style="padding:12px 16px; text-align:left; border-bottom:1px solid var(--border); font-weight:600;">Prize</th>
                            <th style="padding:12px 16px; text-align:left; border-bottom:1px solid var(--border); font-weight:600;">Draw</th>
                            <th style="padding:12px 16px; text-align:left; border-bottom:1px solid var(--border); font-weight:600;">Drawn At</th>
                            <th style="padding:12px 16px; text-align:left; border-bottom:1px solid var(--border); font-weight:600;">Winner Name</th>
                            <th style="padding:12px 16px; text-align:center; border-bottom:1px solid var(--border); font-weight:600;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($winners as $winner)
                            <tr class="winner-row" data-draw-id="{{ $winner->prize->draw_id ?? '' }}" data-winner-id="{{ $winner->id }}" data-prize-id="{{ $winner->prize_id }}" data-winner-code="{{ $winner->code }}" data-winner-drawn-at="{{ $winner->drawn_at }}" data-winner-name="{{ $winner->winner_name ?: '' }}" style="border-bottom:1px solid var(--border);">
                                <td style="padding:12px 16px;">
                                    <code style="background:var(--navy3); padding:4px 8px; border-radius:4px; font-family:monospace;">{{ $winner->code }}</code>
                                </td>
                                <td style="padding:12px 16px;">
                                    <div style="display:flex; align-items:center; gap:8px;">
                                        @if ($winner->prize->photo_path)
                                            <img src="/storage/{{ $winner->prize->photo_path }}" alt="{{ $winner->prize->name }}"
                                                style="width:32px; height:32px; object-fit:cover; border-radius:4px;">
                                        @else
                                            <span style="font-size:1.2em;">🎁</span>
                                        @endif
                                        <span>{{ $winner->prize->name }}</span>
                                    </div>
                                </td>
                                <td style="padding:12px 16px;">
                                    @if ($winner->prize->draw)
                                        <span style="color:var(--accent)">🏁 {{ $winner->prize->draw->name }}</span>
                                    @else
                                        <span style="opacity:.5">No draw</span>
                                    @endif
                                </td>
                                <td style="padding:12px 16px;">
                                    {{ $winner->drawn_at->format('M j, Y g:i A') }}
                                </td>
                                <td style="padding:12px 16px;">
                                    <span class="winner-name-cell" data-winner-id="{{ $winner->id }}" style="cursor:pointer; color:var(--accent);">
                                        {{ $winner->winner_name ?: '-' }}
                                    </span>
                                </td>
                                <td style="padding:12px 16px; text-align:center;">
                                    <button type="button" class="btn btn-danger btn-sm"
                                        onclick="openDeleteModal('{{ $winner->id }}', '{{ $winner->code }}', '/api/winners/{{ $winner->id }}', 'winner')">
                                        🗑
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" style="padding:32px; text-align:center; color:var(--text-dim);">
                                    No winners yet.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

                <div id="filteredEmptyMessage"
                    style="padding:32px; text-align:center; color:var(--text-dim);
                        background:var(--navy3); border-radius:10px; border:1px dashed var(--border); display:none; margin-top:16px;">
                    No winners found for the selected draw.
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
                    <div class="stat-icon gold">🏅</div>
                    <div>
                        <div class="stat-val">{{ $winners->count() }}</div>
                        <div class="stat-lbl">Total Winners</div>
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
                    <div class="stat-icon green">🎁</div>
                    <div>
                        <div class="stat-val">{{ $winners->unique('prize_id')->count() }}</div>
                        <div class="stat-lbl">Prizes Won</div>
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

    {{-- ── Delete Confirmation Modal ── (must be before <script>) --}}
    <div id="deleteConfirmModal" style="display:none; position:fixed; top:0; left:0; right:0; bottom:0; background:rgba(0,0,0,0.5); z-index:2000; align-items:center; justify-content:center; backdrop-filter:blur(2px);">
        <div style="background:var(--navy); border-radius:14px; padding:32px; max-width:420px; width:90%; box-shadow:0 20px 60px rgba(0,0,0,0.5); animation:modalSlideIn .3s cubic-bezier(.34,1.56,.64,1);">
            <div id="deleteConfirmTitle" style="font-size:1.5rem; font-weight:600; margin-bottom:12px; color:var(--danger, #ff4757);"></div>
            <div id="deleteConfirmMessage" style="color:var(--text-dim); margin-bottom:28px; line-height:1.6;"></div>
            <div style="display:flex; gap:12px; justify-content:flex-end;">
                <button id="deleteConfirmCancel" type="button" class="btn" style="background:var(--navy3); border:1px solid var(--border); min-width:100px;">
                    Cancel
                </button>
                <button id="deleteConfirmBtn" type="button" class="btn btn-danger" style="min-width:100px;">
                    🗑 Delete
                </button>
            </div>
        </div>
    </div>

    <style>
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

    {{-- Script is AFTER all modal HTML --}}
    <script>
        var _deleteUrl  = '';
        var _deleteType = '';
        var _deleteName = '';
        var _deleteId   = '';
        var _undoData   = null;
        var _undoTimer  = null;

        // ── Delete Modal ─────────────────────────────────────────────────────
        function openDeleteModal(id, name, url, type) {
            _deleteUrl  = url;
            _deleteType = type || 'winner';
            _deleteName = name;
            _deleteId   = id;

            document.getElementById('deleteConfirmTitle').innerHTML = _deleteType === 'winner'
                ? '🏅 Delete Winner?' : '🗑️ Delete Prize?';
            document.getElementById('deleteConfirmMessage').innerHTML = _deleteType === 'winner'
                ? 'Are you sure you want to delete winner <strong>' + name + '</strong>? This cannot be undone.'
                : 'Are you sure you want to delete prize <strong>' + name + '</strong>? This cannot be undone.';
            document.getElementById('deleteConfirmModal').style.display = 'flex';
        }

        function closeDeleteModal() {
            document.getElementById('deleteConfirmModal').style.display = 'none';
        }

        document.getElementById('deleteConfirmCancel').addEventListener('click', closeDeleteModal);
        document.getElementById('deleteConfirmModal').addEventListener('click', function(e) {
            if (e.target === this) closeDeleteModal();
        });

        document.getElementById('deleteConfirmBtn').addEventListener('click', function() {
            var csrfToken = document.querySelector('meta[name="csrf-token"]')
                ? document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                : document.querySelector('input[name="_token"]').value;

            // Winners use a real DELETE (api route); prizes use POST + _method=DELETE (web route)
            var fetchOptions;
            if (_deleteType === 'winner') {
                fetchOptions = {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    }
                };
            } else {
                var formData = new FormData();
                formData.append('_token', csrfToken);
                formData.append('_method', 'DELETE');
                fetchOptions = { method: 'POST', body: formData };
            }

            var deletedRow = document.querySelector('.winner-row[data-winner-id="' + _deleteId + '"]');
            var deletedWinner = null;
            if (deletedRow) {
                deletedWinner = {
                    prize_id: deletedRow.dataset.prizeId,
                    code: deletedRow.dataset.winnerCode,
                    drawn_at: deletedRow.dataset.winnerDrawnAt,
                    winner_name: deletedRow.dataset.winnerName || null,
                };
            }

            fetch(_deleteUrl, fetchOptions)
                .then(function(res) {
                    if (!res.ok) throw new Error('Server returned ' + res.status);
                    closeDeleteModal();

                    if (deletedRow) {
                        deletedRow.remove();
                    }

                    if (deletedWinner) {
                        showUndoNotification(deletedWinner);
                    } else {
                        showSuccessNotification(_deleteType, _deleteName);
                    }
                })
                .catch(function(err) {
                    console.error(err);
                    alert('Error deleting ' + _deleteType + '. Please try again.');
                });
        });

        function showSuccessNotification(type, name) {
            var msg = type === 'winner' ? '✓ Winner deleted!' : '✓ Prize deleted!';
            var el  = document.createElement('div');
            el.textContent = msg;
            el.style.cssText = 'position:fixed;top:20px;right:20px;background:#10b981;color:white;padding:16px 24px;border-radius:8px;z-index:3000;font-weight:500;animation:slideInRight .3s cubic-bezier(.34,1.56,.64,1);';
            document.body.appendChild(el);
            setTimeout(function() { el.remove(); }, 1800);
        }

        function showUndoNotification(deletedWinner) {
            if (_undoTimer) {
                clearTimeout(_undoTimer);
                _undoTimer = null;
            }

            _undoData = deletedWinner;

            var container = document.createElement('div');
            container.style.cssText = 'position:fixed;top:20px;right:20px;background:#1e3a8a;color:white;padding:12px 16px;border-radius:8px;z-index:3000;font-weight:500;display:flex;align-items:center;gap:12px;';
            container.id = 'undoNotification';
            container.innerHTML = 'Winner deleted. <strong>Undo</strong> in <span id="undoCountdown">10</span>s';

            var undoBtn = document.createElement('button');
            undoBtn.textContent = 'Undo';
            undoBtn.style.cssText = 'background:#fff;color:#1e3a8a;border:0;border-radius:6px;padding:6px 10px;cursor:pointer;font-weight:700;';
            container.appendChild(undoBtn);

            document.body.appendChild(container);

            var countdown = 10;
            var countdownEl = container.querySelector('#undoCountdown');
            _undoTimer = setInterval(function() {
                countdown -= 1;
                countdownEl.textContent = countdown;
                if (countdown <= 0) {
                    clearInterval(_undoTimer);
                    _undoTimer = null;
                    _undoData = null;
                    container.remove();
                }
            }, 1000);

            undoBtn.addEventListener('click', function() {
                if (!_undoData) return;

                fetch('/api/winners/restore', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify(_undoData)
                })
                .then(function(res) {
                    if (!res.ok) throw new Error('Failed to restore');
                    return res.json();
                })
                .then(function() {
                    if (_undoTimer) {
                        clearInterval(_undoTimer);
                        _undoTimer = null;
                    }
                    _undoData = null;
                    container.remove();
                    showSuccessNotification('winner', 'Restored');
                    location.reload();
                })
                .catch(function(err) {
                    console.error(err);
                    alert('Could not undo deletion. Please reload and try again.');
                });
            });
        }

        // ── Draw Filter ──────────────────────────────────────────────────────
        var drawFilter = document.getElementById('drawFilter');
        drawFilter.addEventListener('change', function() {
            var selected = this.value;
            var rows     = document.querySelectorAll('.winner-row');
            var visible  = 0;
            rows.forEach(function(row) {
                var match = selected === '' || row.dataset.drawId === selected;
                row.style.display = match ? '' : 'none';
                if (match) visible++;
            });
            var empty = document.getElementById('filteredEmptyMessage');
            if (empty) empty.style.display = (visible === 0 && rows.length > 0) ? 'block' : 'none';
        });
        drawFilter.dispatchEvent(new Event('change'));

        // ── Winner Name Editing ──────────────────────────────────────────────
        document.addEventListener('click', function(e) {
            if (e.target.classList.contains('winner-name-cell')) {
                var cell = e.target;
                var winnerId = cell.dataset.winnerId;
                var currentName = cell.textContent.trim() === '-' ? '' : cell.textContent.trim();

                // Replace span with input
                var input = document.createElement('input');
                input.type = 'text';
                input.value = currentName;
                input.style.cssText = 'width:100%; padding:4px 8px; border:1px solid var(--border); border-radius:4px; background:var(--navy2); color:var(--text);';
                input.maxLength = 255;

                cell.innerHTML = '';
                cell.appendChild(input);
                input.focus();
                input.select();

                // Function to save
                function saveName() {
                    var newName = input.value.trim();
                    var csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

                    fetch('/api/winners/' + winnerId, {
                        method: 'PUT',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken
                        },
                        body: JSON.stringify({ winner_name: newName })
                    })
                    .then(function(res) {
                        if (!res.ok) throw new Error('Failed to update');
                        return res.json();
                    })
                    .then(function(data) {
                        cell.innerHTML = newName || '-';
                        showSuccessNotification('winner', 'Name updated');
                    })
                    .catch(function(err) {
                        console.error(err);
                        alert('Error updating winner name. Please try again.');
                        cell.innerHTML = currentName || '-';
                    });
                }

                // Save on enter or blur
                input.addEventListener('keydown', function(e) {
                    if (e.key === 'Enter') {
                        e.preventDefault();
                        saveName();
                    } else if (e.key === 'Escape') {
                        cell.innerHTML = currentName || '-';
                    }
                });

                input.addEventListener('blur', function() {
                    saveName();
                });
            }
        });
    </script>

@endsection