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
                    <div class="prize-card" data-prize-id="{{ $prize->id }}" style="cursor: pointer;">
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
                                onsubmit="return confirm('Delete this prize?')" onclick="event.stopPropagation();">
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

            // Prize Edit Modal - defer until elements exist
            function initEditModal() {
                const modal = document.getElementById('editPrizeModal');
                const closeBtn = document.getElementById('closeModal');
                const prizeForm = document.getElementById('editPrizeForm');
                const photoEditInput = document.getElementById('photoEdit');
                const photoEditPreview = document.getElementById('photoEditPreview');
                const photoEditIcon = document.getElementById('photoEditIcon');

                if (!modal || !closeBtn || !prizeForm) return;

                // Open modal when prize card is clicked
                document.querySelectorAll('.prize-card').forEach(card => {
                    card.addEventListener('click', function() {
                        const prizeId = this.dataset.prizeId;
                        openEditModal(prizeId);
                    });
                });

                // Close modal
                closeBtn.addEventListener('click', closeModal);

                modal.addEventListener('click', function(e) {
                    if (e.target === modal) {
                        closeModal();
                    }
                });

                // Photo preview on edit
                if (photoEditInput) {
                    photoEditInput.addEventListener('change', function() {
                        const file = this.files && this.files[0];
                        if (!file || !file.type.startsWith('image/')) {
                            if (photoEditPreview) photoEditPreview.style.display = 'none';
                            if (photoEditIcon) photoEditIcon.style.display = '';
                            return;
                        }
                        const r = new FileReader();
                        r.onload = ev => {
                            if (photoEditPreview) {
                                photoEditPreview.src = ev.target.result;
                                photoEditPreview.style.display = 'block';
                            }
                            if (photoEditIcon) photoEditIcon.style.display = 'none';
                        };
                        r.readAsDataURL(file);
                    });
                }

                function openEditModal(prizeId) {
                    // Fetch prize data
                    fetch(`/admin/prizes/${prizeId}`)
                        .then(response => response.json())
                        .then(data => {
                            // Populate form fields
                            document.getElementById('editPrizeId').value = data.id;
                            document.getElementById('editName').value = data.name;
                            document.getElementById('editDescription').value = data.description || '';
                            document.getElementById('editQuantity').value = data.quantity;
                            document.getElementById('editOrder').value = data.order;
                            document.getElementById('editDrawId').value = data.draw_id || '';

                            // Populate draws dropdown
                            const drawSelect = document.getElementById('editDrawId');
                            drawSelect.innerHTML = '<option value="">(no draw)</option>';
                            data.draws.forEach(draw => {
                                const option = document.createElement('option');
                                option.value = draw.id;
                                option.textContent = draw.name + (draw.draw_date ? ' — ' + draw.draw_date : '') + (draw.active ? ' ⚡' : '');
                                drawSelect.appendChild(option);
                            });
                            drawSelect.value = data.draw_id || '';

                            // Display current photo
                            if (photoEditIcon) photoEditIcon.style.display = '';
                            if (photoEditPreview) photoEditPreview.style.display = 'none';
                            if (photoEditInput) photoEditInput.value = '';
                            if (data.photo_path && photoEditPreview) {
                                photoEditPreview.src = `/storage/${data.photo_path}`;
                                photoEditPreview.style.display = 'block';
                                if (photoEditIcon) photoEditIcon.style.display = 'none';
                            }

                            // Update form action
                            prizeForm.action = `/admin/prizes/${prizeId}`;

                            // Show modal
                            modal.style.display = 'flex';
                        })
                        .catch(error => {
                            console.error('Error fetching prize:', error);
                            alert('Error loading prize data');
                        });
                }

                function closeModal() {
                    modal.style.display = 'none';
                    prizeForm.reset();
                }
            }

            // Run when DOM is ready
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', initEditModal);
            } else {
                initEditModal();
            }
        })();
    </script>

    {{-- Edit Prize Modal --}}
    <div id="editPrizeModal" style="display:none; position:fixed; top:0; left:0; right:0; bottom:0; background:rgba(0,0,0,0.7); z-index:1000; align-items:center; justify-content:center;">
        <div style="background:var(--navy); border-radius:12px; padding:24px; max-width:500px; width:90%; max-height:90vh; overflow-y:auto; box-shadow:0 8px 32px rgba(0,0,0,0.3);">
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
                <h2 style="margin:0; font-size:1.5rem;">✏️ Edit Prize</h2>
                <button id="closeModal" style="background:none; border:none; font-size:1.5rem; cursor:pointer; color:var(--text); padding:0; width:30px; height:30px; display:flex; align-items:center; justify-content:center;">×</button>
            </div>

            <form id="editPrizeForm" method="POST" enctype="multipart/form-data">
                @csrf @method('PUT')
                <input type="hidden" id="editPrizeId" name="prize_id">

                <div style="margin-bottom:16px;">
                    <label for="editName">Prize Name</label>
                    <input id="editName" type="text" name="name" required style="width:100%; padding:8px 12px; border:1px solid var(--border); border-radius:6px; background:var(--navy2); color:var(--text);">
                </div>

                <div style="margin-bottom:16px;">
                    <label for="editDescription">Description</label>
                    <textarea id="editDescription" name="description" rows="2" style="width:100%; padding:8px 12px; border:1px solid var(--border); border-radius:6px; background:var(--navy2); color:var(--text); font-family:inherit;"></textarea>
                </div>

                <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px; margin-bottom:16px;">
                    <div>
                        <label for="editQuantity">Quantity</label>
                        <input id="editQuantity" type="number" name="quantity" required min="1" style="width:100%; padding:8px 12px; border:1px solid var(--border); border-radius:6px; background:var(--navy2); color:var(--text);">
                    </div>
                    <div>
                        <label for="editOrder">Draw Order</label>
                        <input id="editOrder" type="number" name="order" required min="1" style="width:100%; padding:8px 12px; border:1px solid var(--border); border-radius:6px; background:var(--navy2); color:var(--text);">
                    </div>
                </div>

                <div style="margin-bottom:16px;">
                    <label for="editDrawId">Assign to Draw</label>
                    <select id="editDrawId" name="draw_id" style="width:100%; padding:8px 12px; border:1px solid var(--border); border-radius:6px; background:var(--navy2); color:var(--text);">
                        <option value="">(no draw)</option>
                    </select>
                </div>

                <div style="margin-bottom:16px;">
                    <label for="photoEdit">Prize Photo</label>
                    <input id="photoEdit" type="file" name="photo" accept="image/*" style="display:block; margin-bottom:12px;">
                    <div style="display:flex; gap:12px; align-items:center;">
                        <div style="width:100px; height:100px; background:var(--navy3); border-radius:8px; display:flex; align-items:center; justify-content:center; overflow:hidden;">
                            <span id="photoEditIcon" style="font-size:2rem;">🖼</span>
                            <img id="photoEditPreview" src="" alt="Preview" style="display:none; max-width:100%; max-height:100%; object-fit:contain;">
                        </div>
                    </div>
                </div>

                <div style="display:flex; gap:12px; justify-content:flex-end;">
                    <button type="button" onclick="document.getElementById('editPrizeModal').style.display='none'" class="btn" style="background:var(--navy3); border:1px solid var(--border);">Cancel</button>
                    <button type="submit" class="btn btn-success">💾 Save Changes</button>
                </div>
            </form>
        </div>
    </div>
@endsection
