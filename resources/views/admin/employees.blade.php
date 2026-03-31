@extends('admin.layout')

@section('content')
    <div style="display:flex; flex-direction:column; gap:20px;">
        <div class="gc">
            <div class="stitle">👥 Employee Tickets</div>
            <form action="/admin/employees" method="POST" style="display:grid; gap:16px;">
                @csrf
                <div class="row">
                    <div style="flex:1 1 220px; min-width:180px">
                        <label for="draw_id">Select Draw</label>
                        <select id="draw_id" name="draw_id" required>
                            <option value="">Choose draw...</option>
                            @foreach ($draws as $draw)
                                <option value="{{ $draw->id }}" {{ $draw->active ? 'selected' : '' }}>{{ $draw->name }} @if($draw->draw_date) — {{ $draw->draw_date }}@endif</option>
                            @endforeach
                        </select>
                    </div>
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

            @if(session('import_success'))
                <div class="alert alert-success" style="margin-top:16px; padding:14px; border-radius:14px;">{{ session('import_success') }}</div>
            @endif
            @if($errors->any())
                <div class="alert alert-danger" style="margin-top:16px; padding:14px; border-radius:14px;">
                    <strong>Import error:</strong>
                    <ul style="margin:8px 0 0 18px; padding:0; list-style:disc;">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            @if(session('import_errors'))
                <div class="alert alert-warning" style="margin-top:16px; padding:14px; border-radius:14px;">
                    <strong>Import completed with warnings:</strong>
                    <ul style="margin:8px 0 0 18px; padding:0; list-style:disc;">
                        @foreach(session('import_errors') as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div style="display:flex; justify-content:flex-end; margin-top:24px;">
                <button type="button" class="btn btn-primary" onclick="openImportModal()">Import Tickets</button>
            </div>

            <div id="importModal" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.72); backdrop-filter:blur(6px); z-index:1000; align-items:center; justify-content:center; padding:24px;">
                <div style="background:linear-gradient(160deg,#1e2535 0%,#161c2c 100%); border-radius:20px; width:100%; max-width:700px; padding:32px; box-shadow:0 32px 80px rgba(0,0,0,0.6), 0 0 0 1px rgba(255,255,255,0.07); position:relative;">
                    <button type="button" onclick="closeImportModal()" style="position:absolute; top:16px; right:16px; border:none; background:rgba(255,255,255,0.07); width:32px; height:32px; border-radius:8px; font-size:1.2rem; cursor:pointer; color:#94a3b8; display:flex; align-items:center; justify-content:center; line-height:1; transition:background .15s;" onmouseover="this.style.background='rgba(255,255,255,0.12)'" onmouseout="this.style.background='rgba(255,255,255,0.07)'">&times;</button>

                    <div style="margin-bottom:28px;">
                        <div style="display:flex; align-items:center; gap:10px; margin-bottom:8px;">
                            <div style="width:36px; height:36px; background:linear-gradient(135deg,#6366f1,#8b5cf6); border-radius:10px; display:flex; align-items:center; justify-content:center; font-size:1.1rem;">📥</div>
                            <div style="font-size:1.3rem; font-weight:700; color:#f1f5f9; letter-spacing:-.01em;">Import Employee Tickets</div>
                        </div>
                        <div style="color:#64748b; font-size:.9rem; line-height:1.6; padding-left:46px;">Select the active draw and upload a CSV/TSV or Excel file. A preview will appear below so you can verify the first rows before importing.</div>
                    </div>

                    <form action="/admin/employees/import" method="POST" enctype="multipart/form-data" style="display:grid; gap:20px;">
                        @csrf
                        <div style="display:grid; gap:8px;">
                            <label for="import_draw_id" style="font-size:.75rem; font-weight:700; color:#64748b; text-transform:uppercase; letter-spacing:.08em;">Select Draw</label>
                            <select id="import_draw_id" name="draw_id" required style="width:100%; min-height:44px; padding:11px 14px; border:1px solid rgba(255,255,255,0.1); border-radius:12px; background:rgba(255,255,255,0.05); font-size:.95rem; color:#e2e8f0; outline:none; cursor:pointer; appearance:none; -webkit-appearance:none; background-image:url('data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 width=%2212%22 height=%2212%22 viewBox=%220 0 12 12%22><path fill=%22%2394a3b8%22 d=%22M6 8L1 3h10z%22/></svg>'); background-repeat:no-repeat; background-position:right 14px center;">
                                <option value="" style="background:#1e2535;">Choose draw...</option>
                                @foreach ($draws as $draw)
                                    <option value="{{ $draw->id }}" {{ $draw->active ? 'selected' : '' }} style="background:#1e2535;">{{ $draw->name }} @if($draw->draw_date) — {{ $draw->draw_date }}@endif</option>
                                @endforeach
                            </select>
                        </div>

                        <div style="display:grid; gap:8px;">
                            <label for="employees_file" style="font-size:.75rem; font-weight:700; color:#64748b; text-transform:uppercase; letter-spacing:.08em;">Excel / CSV File</label>
                            <label for="employees_file" style="display:flex; align-items:center; gap:12px; padding:12px 16px; border:1.5px dashed rgba(99,102,241,0.35); border-radius:12px; background:rgba(99,102,241,0.05); cursor:pointer; transition:border-color .2s, background .2s;" onmouseover="this.style.borderColor='rgba(99,102,241,0.6)';this.style.background='rgba(99,102,241,0.1)'" onmouseout="this.style.borderColor='rgba(99,102,241,0.35)';this.style.background='rgba(99,102,241,0.05)'">
                                <span style="font-size:1.4rem;">📂</span>
                                <span id="fileLabel" style="color:#94a3b8; font-size:.9rem;">Choose file or drag here&hellip;</span>
                            </label>
                            <input id="employees_file" name="employees_file" type="file" accept=".csv,.txt,.xls,.xlsx" required style="display:none;" />
                        </div>

                        <div id="importError" style="display:none; color:#fca5a5; background:rgba(239,68,68,0.12); border:1px solid rgba(239,68,68,0.25); border-radius:12px; padding:12px 16px; font-size:.9rem; line-height:1.5;"></div>

                        <div id="importPreview" style="display:none; max-height:240px; overflow:auto; border:1px solid rgba(255,255,255,0.08); border-radius:12px; background:rgba(0,0,0,0.25);"></div>
                        <textarea id="employees_text" name="employees_text" style="display:none;"></textarea>

                        <div style="color:#475569; font-size:.85rem; line-height:1.6; background:rgba(255,255,255,0.03); border:1px solid rgba(255,255,255,0.06); border-radius:10px; padding:10px 14px;">
                            Expected columns: <strong style="color:#94a3b8;">Registration Number of the Employee</strong> and <strong style="color:#94a3b8;">Employee Name</strong>.
                        </div>

                        <div style="display:flex; justify-content:flex-end; gap:10px; margin-top:4px; padding-top:16px; border-top:1px solid rgba(255,255,255,0.07);">
                            <button type="button" onclick="closeImportModal()" style="min-width:100px; padding:10px 20px; border-radius:10px; border:1px solid rgba(255,255,255,0.12); background:rgba(255,255,255,0.06); color:#94a3b8; font-size:.9rem; font-weight:600; cursor:pointer; transition:background .15s;" onmouseover="this.style.background='rgba(255,255,255,0.1)'" onmouseout="this.style.background='rgba(255,255,255,0.06)'">Cancel</button>
                            <button type="submit" style="min-width:140px; padding:10px 24px; border-radius:10px; border:none; background:linear-gradient(135deg,#6366f1,#8b5cf6); color:white; font-size:.9rem; font-weight:700; cursor:pointer; box-shadow:0 4px 15px rgba(99,102,241,0.35); transition:opacity .15s;" onmouseover="this.style.opacity='.88'" onmouseout="this.style.opacity='1'">Import Tickets</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="gc">
            <div class="stitle">📋 Registered Employee Tickets</div>
            <div style="overflow-x:auto;">
                <table>
                    <thead>
                        <tr>
                            <th>Draw</th>
                            <th>Registration Number</th>
                            <th>Employee Name</th>
                            <th>Created</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($employees as $employee)
                            <tr>
                                <td>
                                    <div style="font-weight:600;">{{ $employee->draw->name ?? 'N/A' }}</div>
                                    @if($employee->draw && $employee->draw->draw_date)
                                        <div style="font-size:.78rem; color:var(--text-dim);">{{ $employee->draw->draw_date }}</div>
                                    @endif
                                </td>
                                <td style="font-family:'DM Mono',monospace;">{{ $employee->registration_number }}</td>
                                <td>{{ $employee->employee_name }}</td>
                                <td style="color:var(--text-dim); font-size:.85rem;">{{ $employee->created_at->format('Y-m-d H:i') }}</td>
                                <td style="text-align:center;">
                                    <button type="button" class="btn btn-danger btn-sm" data-employee-id="{{ $employee->id }}" data-employee-code="{{ $employee->registration_number }}" data-employee-name="{{ $employee->employee_name }}" onclick="openDeleteModal(this)">Remove</button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" style="padding:24px; text-align:center; color:var(--text-dim);">No employee tickets yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div id="deleteConfirmModal" style="display:none; position:fixed; inset:0; background:rgba(15,23,42,0.55); z-index:1100; align-items:center; justify-content:center; padding:20px;">
        <div style="background:#0f172a; border-radius:22px; width:100%; max-width:520px; padding:26px; box-shadow:0 24px 72px rgba(15,23,42,0.35); position:relative; color:#f8fafc;">
            <button type="button" onclick="closeDeleteModal()" style="position:absolute; top:18px; right:18px; border:none; background:transparent; font-size:1.4rem; cursor:pointer; color:#cbd5e1;">&times;</button>
            <div style="margin-bottom:20px;">
                <div style="font-size:1.3rem; font-weight:800; color:#f8fafc; margin-bottom:8px;">Confirm delete</div>
                <div style="color:#cbd5e1; line-height:1.6;">Are you sure you want to remove this employee ticket? This action cannot be undone.</div>
            </div>
            <div style="background:rgba(148,163,184,0.08); border:1px solid rgba(148,163,184,0.18); border-radius:16px; padding:16px; margin-bottom:22px;">
                <div style="font-size:.95rem; color:#94a3b8; margin-bottom:10px;">Employee ticket</div>
                <div id="deleteTargetText" style="font-size:1rem; color:#f8fafc; font-weight:700;"></div>
            </div>
            <div style="display:flex; justify-content:flex-end; gap:12px;">
                <button type="button" onclick="closeDeleteModal()" style="min-width:100px; padding:10px 18px; border-radius:12px; border:1px solid rgba(255,255,255,0.12); background:transparent; color:#cbd5e1; cursor:pointer;">Cancel</button>
                <button type="button" onclick="confirmDeleteEmployee()" style="min-width:120px; padding:10px 18px; border-radius:12px; border:none; background:#ef4444; color:white; font-weight:700; cursor:pointer;">Delete</button>
            </div>
        </div>
    </div>

    <form id="deleteEmployeeForm" method="POST" style="display:none;">
        @csrf
        @method('DELETE')
    </form>

    <script src="https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js"></script>
    <script>
        const importErrorEl = document.getElementById('importError');
        const importPreviewEl = document.getElementById('importPreview');
        const importTextEl = document.getElementById('employees_text');
        const importFileInput = document.getElementById('employees_file');

        function openImportModal() {
            const modal = document.getElementById('importModal');
            if (modal) {
                modal.style.display = 'flex';
            }
        }

        function closeImportModal() {
            const modal = document.getElementById('importModal');
            if (modal) {
                modal.style.display = 'none';
            }
            clearImportMessages();
        }

        function clearImportMessages() {
            if (importErrorEl) {
                importErrorEl.style.display = 'none';
                importErrorEl.textContent = '';
            }
            if (importPreviewEl) {
                importPreviewEl.style.display = 'none';
                importPreviewEl.innerHTML = '';
            }
            if (importTextEl) {
                importTextEl.value = '';
            }
        }

        function showImportError(message) {
            if (importErrorEl) {
                importErrorEl.style.display = 'block';
                importErrorEl.textContent = message;
            }
            if (importPreviewEl) {
                importPreviewEl.style.display = 'none';
                importPreviewEl.innerHTML = '';
            }
            if (importTextEl) {
                importTextEl.value = '';
            }
        }

        function openDeleteModal(button) {
            const modal = document.getElementById('deleteConfirmModal');
            const form = document.getElementById('deleteEmployeeForm');
            const targetText = document.getElementById('deleteTargetText');
            if (!modal || !form || !targetText) return;
            const employeeId = button.dataset.employeeId;
            const employeeCode = button.dataset.employeeCode || '';
            const employeeName = button.dataset.employeeName || '';
            form.action = '/admin/employees/' + employeeId;
            targetText.textContent = `${employeeCode} — ${employeeName}`;
            modal.style.display = 'flex';
        }

        function closeDeleteModal() {
            const modal = document.getElementById('deleteConfirmModal');
            if (modal) {
                modal.style.display = 'none';
            }
        }

        function confirmDeleteEmployee() {
            const form = document.getElementById('deleteEmployeeForm');
            if (form) {
                form.submit();
            }
        }

        function setImportPreview(rows) {
            if (!importPreviewEl) return;
            if (!Array.isArray(rows) || rows.length === 0) {
                importPreviewEl.style.display = 'none';
                importPreviewEl.innerHTML = '';
                return;
            }

            const maxRows = Math.min(rows.length, 7);
            const headers = rows[0] || [];
            const bodyRows = rows.slice(1, maxRows);

            const table = document.createElement('table');
            table.style.width = '100%';
            table.style.borderCollapse = 'collapse';
            table.style.fontSize = '0.88rem';
            table.innerHTML = `
                <thead>
                    <tr>${headers.map(cell => `<th style="padding:10px 14px; border-bottom:1px solid rgba(255,255,255,0.08); text-align:left; color:#64748b; font-size:.72rem; text-transform:uppercase; letter-spacing:.07em; font-weight:700;">${escapeHtml(cell || '')}</th>`).join('')}</tr>
                </thead>
                <tbody>
                    ${bodyRows.map((row, i) => `<tr style="background:${i%2===0?'transparent':'rgba(255,255,255,0.02)'}">${row.map(cell => `<td style="padding:9px 14px; border-bottom:1px solid rgba(255,255,255,0.05); color:#cbd5e1;">${escapeHtml(cell || '')}</td>`).join('')}</tr>`).join('')}
                </tbody>
            `;

            importPreviewEl.style.display = 'block';
            importPreviewEl.innerHTML = '';
            importPreviewEl.appendChild(table);
        }

        function escapeHtml(text) {
            return String(text)
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;');
        }

        function serializeRowsToCsv(rows) {
            return rows.map(row => row.map(value => {
                const valueStr = String(value ?? '');
                if (/[,"\n\r]/.test(valueStr)) {
                    return '"' + valueStr.replace(/"/g, '""') + '"';
                }
                return valueStr;
            }).join(',')).join('\n');
        }

        function parseTextRows(text) {
            const normalized = text.replace(/\r\n/g, '\n').replace(/\r/g, '\n');
            const lines = normalized.split('\n').map(line => line.trim()).filter(line => line.length > 0);
            if (lines.length === 0) {
                return [];
            }
            const delimiter = detectDelimiter(lines[0]);
            return lines.map(line => parseCsvLine(line, delimiter));
        }

        function detectDelimiter(line) {
            const tabCount = (line.match(/\t/g) || []).length;
            const semicolonCount = (line.match(/;/g) || []).length;
            const commaCount = (line.match(/,/g) || []).length;
            if (tabCount > commaCount) return '\t';
            if (semicolonCount > commaCount) return ';';
            return ',';
        }

        function parseCsvLine(line, delimiter) {
            const result = [];
            let current = '';
            let insideQuotes = false;
            for (let i = 0; i < line.length; i++) {
                const char = line[i];
                if (insideQuotes) {
                    if (char === '"') {
                        if (line[i + 1] === '"') {
                            current += '"';
                            i++;
                        } else {
                            insideQuotes = false;
                        }
                    } else {
                        current += char;
                    }
                } else {
                    if (char === '"') {
                        insideQuotes = true;
                    } else if (char === delimiter) {
                        result.push(current);
                        current = '';
                    } else {
                        current += char;
                    }
                }
            }
            result.push(current);
            return result;
        }

        function readTextFile(file) {
            return new Promise((resolve, reject) => {
                const reader = new FileReader();
                reader.onload = () => resolve(parseTextRows(reader.result));
                reader.onerror = () => reject('Unable to read the file.');
                reader.readAsText(file, 'UTF-8');
            });
        }

        function readXlsxFile(file) {
            return new Promise((resolve, reject) => {
                if (typeof XLSX === 'undefined' || !XLSX.read) {
                    reject('Excel preview requires the SheetJS library.');
                    return;
                }

                const reader = new FileReader();
                reader.onload = () => {
                    try {
                        const data = new Uint8Array(reader.result);
                        const workbook = XLSX.read(data, { type: 'array' });
                        const sheetName = workbook.SheetNames[0];
                        if (!sheetName) {
                            reject('No worksheet found in the Excel file.');
                            return;
                        }
                        const worksheet = workbook.Sheets[sheetName];
                        const rows = XLSX.utils.sheet_to_json(worksheet, { header: 1, raw: false });
                        resolve(rows);
                    } catch (error) {
                        reject('Unable to parse Excel file.');
                    }
                };
                reader.onerror = () => reject('Unable to read the file.');
                reader.readAsArrayBuffer(file);
            });
        }

        function onImportFileChanged() {
            clearImportMessages();
            const file = importFileInput?.files?.[0];
            const fileLabel = document.getElementById('fileLabel');
            if (fileLabel) {
                fileLabel.textContent = file ? file.name : 'Choose file or drag here\u2026';
                fileLabel.style.color = file ? '#e2e8f0' : '#94a3b8';
            }
            if (!file) {
                return;
            }
            const extension = file.name.split('.').pop().toLowerCase();
            if (['csv', 'txt'].includes(extension)) {
                readTextFile(file)
                    .then(rows => {
                        if (rows.length === 0) {
                            showImportError('The selected file is empty or not valid.');
                            return;
                        }
                        setImportPreview(rows);
                        if (importTextEl) {
                            importTextEl.value = serializeRowsToCsv(rows);
                        }
                    })
                    .catch(showImportError);
            } else if (['xls', 'xlsx'].includes(extension)) {
                readXlsxFile(file)
                    .then(rows => {
                        if (rows.length === 0) {
                            showImportError('The selected Excel file contains no data.');
                            return;
                        }
                        setImportPreview(rows);
                        if (importTextEl) {
                            importTextEl.value = serializeRowsToCsv(rows);
                        }
                    })
                    .catch(showImportError);
            } else {
                showImportError('Unsupported file type. Please upload .xlsx, .xls, .csv, or .txt.');
            }
        }

        if (importFileInput) {
            importFileInput.addEventListener('change', onImportFileChanged);
        }

        document.addEventListener('DOMContentLoaded', function () {
            @if ($errors->any())
                openImportModal();
            @endif
        });
    </script>
@endsection