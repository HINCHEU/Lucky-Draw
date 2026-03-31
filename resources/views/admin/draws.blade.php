@extends('admin.layout')

@section('content')
    {{-- ── LEFT: Main Content ── --}}
    <div style="display:flex; flex-direction:column; gap:20px;">

        {{-- Create Draw --}}
        <div class="gc">
            <div class="stitle">🏁 Create New Draw</div>
            <form action="/admin/draws" method="POST">
                @csrf
                <div class="row">
                    <div style="flex:1 1 260px; min-width:180px">
                        <label for="draw_name">Draw Name</label>
                        <input id="draw_name" name="name" type="text" placeholder="e.g. Monthly Lucky Draw Q1" required>
                    </div>
                    <div style="flex:1 1 150px; min-width:130px">
                        <label for="draw_date">Draw Date</label>
                        <input id="draw_date" name="draw_date" type="date">
                    </div>
                    <div style="flex:0 0 auto; display:flex; align-items:flex-end; padding-bottom:1px">
                        <div class="toggle-wrap">
                            <input type="checkbox" name="active" id="active_check" value="1">
                            <label for="active_check">Active</label>
                        </div>
                    </div>
                    <div style="flex:0 0 auto; display:flex; align-items:flex-end">
                        <button class="btn btn-primary" type="submit">＋ Create Draw</button>
                    </div>
                </div>
            </form>
        </div>

        {{-- Existing Draws --}}
        <div class="gc">
            <div class="stitle">🏁 All Draws</div>
            <div style="overflow-x:auto; margin: -4px -4px 0">
                <table>
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Date</th>
                            <th>Tickets</th>
                            <th>Prizes</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($draws as $d)
                            <tr>
                                <td>
                                    <span style="font-weight:600">{{ $d->name }}</span>
                                </td>
                                <td style="color:var(--text-dim); font-size:.83rem; font-family:'DM Mono', monospace">
                                    {{ $d->draw_date ?? '—' }}
                                </td>
                                <td style="font-family:'DM Mono', monospace; font-size:.82rem; color:var(--text-dim)">
                                    {{ $d->employees_count ?? 0 }} tickets
                                </td>
                                <td>
                                    <span class="badge badge-dim">{{ $d->prizes_count }} prizes</span>
                                </td>
                                <td>
                                    @if ($d->active)
                                        <span class="badge badge-green">✓ Active</span>
                                    @else
                                        <span class="badge badge-dim">Inactive</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="td-actions">
                                        <a href="/admin/draws/{{ $d->id }}" class="btn btn-secondary btn-sm">View</a>

                                        @if (!$d->active)
                                            <form action="/admin/draws/{{ $d->id }}/activate" method="POST"
                                                style="margin:0">
                                                @csrf
                                                <button class="btn btn-success btn-sm" type="submit">Set Active</button>
                                            </form>
                                        @endif

                                        <form action="/admin/draws/{{ $d->id }}" method="POST" style="margin:0"
                                            onsubmit="return confirm('Delete this draw? Prizes must be removed first.')">
                                            @csrf @method('DELETE')
                                            <button class="btn btn-danger btn-sm" type="submit">Delete</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" style="text-align:center; padding:32px; color:var(--text-dim)">
                                    No draws yet. Create one above.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>

    {{-- ── RIGHT: Sidebar ── --}}
    <aside class="right-panel">
        <div class="gc">
            <div class="stitle">📈 Quick Stats</div>
            <div style="display:flex; flex-direction:column; gap:10px">
                <div class="stat-card">
                    <div class="stat-icon blue">🏁</div>
                    <div>
                        <div class="stat-val">{{ $draws->count() }}</div>
                        <div class="stat-lbl">Total Draws</div>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon green">✅</div>
                    <div>
                        <div class="stat-val" style="font-size:1rem; padding-top:2px">
                            {{ $activeDraw ? $activeDraw->name : 'None' }}
                        </div>
                        <div class="stat-lbl">Active Draw</div>
                    </div>
                </div>
            </div>
        </div>

        @if ($activeDraw)
            <div class="gc">
                <div class="stitle">🔴 Active Draw</div>
                <div class="summary-box">
                    <div class="row-item">
                        <span>Name</span><span>{{ $activeDraw->name }}</span>
                    </div>
                    <div class="row-item">
                        <span>Date</span><span>{{ $activeDraw->draw_date ?? '—' }}</span>
                    </div>
                    <div class="row-item">
                        <span>Tickets</span>
                        <span style="font-family:'DM Mono',monospace; font-size:.8rem">
                            {{ $activeDraw->employees_count ?? 0 }}
                        </span>
                    </div>
                </div>
                <div class="mt-sm">
                    <a href="/admin/draws/{{ $activeDraw->id }}" class="btn btn-primary"
                        style="width:100%; justify-content:center">
                        Manage Active Draw →
                    </a>
                </div>
            </div>
        @endif
    </aside>
@endsection
