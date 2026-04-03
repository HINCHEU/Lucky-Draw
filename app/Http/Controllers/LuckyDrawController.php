<?php

namespace App\Http\Controllers;

use App\Models\Prize;
use App\Models\Winner;
use App\Models\Draw;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class LuckyDrawController extends Controller
{
    public function index()
    {
        $activeDraw = Draw::where('active', true)->first();

        if ($activeDraw) {
            $currentPrize = Prize::where('draw_id', $activeDraw->id)
                ->where('order', '>', 0)
                ->orderBy('order')
                ->whereRaw('(quantity - (SELECT COUNT(*) FROM winners WHERE winners.prize_id = prizes.id)) > 0')
                ->first();

            $prizes = Prize::where('draw_id', $activeDraw->id)->orderBy('order')->get();

            $winners = Winner::with('prize')
                ->whereHas('prize', function ($q) use ($activeDraw) {
                    $q->where('draw_id', $activeDraw->id);
                })
                ->orderBy('drawn_at', 'desc')
                ->get();
        } else {
            $currentPrize = null;
            $prizes       = Prize::orderBy('order')->get();
            $winners      = Winner::with('prize')->orderBy('drawn_at', 'desc')->get();
        }

        return view('welcome', compact('currentPrize', 'prizes', 'winners'));
    }

    public function getCurrentPrize()
    {
        $activeDraw = Draw::where('active', true)->first();
        if (! $activeDraw) {
            return response()->json(['error' => 'No active draw']);
        }

        $prize = Prize::where('draw_id', $activeDraw->id)
            ->where('order', '>', 0)
            ->orderBy('order')
            ->whereRaw('(quantity - (SELECT COUNT(*) FROM winners WHERE winners.prize_id = prizes.id)) > 0')
            ->first();

        if (! $prize) {
            return response()->json(['error' => 'No prizes available for active draw']);
        }

        return response()->json([
            'id'                 => $prize->id,
            'name'               => $prize->name,
            'description'        => $prize->description,
            'photo_path'         => $prize->photo_path,
            'remaining'          => $prize->availableQuantity(),
            'total'              => $prize->quantity,
            'won'                => $prize->quantity - $prize->availableQuantity(),
            'total_tickets'      => Employee::where('draw_id', $activeDraw->id)->count(),
            'remaining_tickets'  => max(
                Employee::where('draw_id', $activeDraw->id)->count()
                - Winner::whereHas('prize', function ($q) use ($activeDraw) {
                    $q->where('draw_id', $activeDraw->id);
                })->count(),
                0
            ),
        ]);
    }

    public function draw(Request $request)
    {
        return DB::transaction(function () {
            $activeDraw = Draw::where('active', true)->lockForUpdate()->first();
            if (! $activeDraw) {
                return response()->json(['error' => 'No active draw']);
            }

            $prize = Prize::where('draw_id', $activeDraw->id)
                ->where('order', '>', 0)
                ->orderBy('order')
                ->whereRaw('(quantity - (SELECT COUNT(*) FROM winners WHERE winners.prize_id = prizes.id)) > 0')
                ->lockForUpdate()
                ->first();

            if (! $prize) {
                return response()->json(['error' => 'No prizes available for active draw']);
            }

            $drawnRegistrationNumbers = Winner::whereHas('prize', fn($q) => $q->where('draw_id', $activeDraw->id))
                ->pluck('code')->toArray();

            $availableEmployees = Employee::where('draw_id', $activeDraw->id)
                ->when(!empty($drawnRegistrationNumbers), function ($query) use ($drawnRegistrationNumbers) {
                    return $query->whereNotIn('registration_number', $drawnRegistrationNumbers);
                })
                ->get();

            if ($availableEmployees->isEmpty()) {
                return response()->json(['error' => 'No employees available for active draw']);
            }

            $employee = $availableEmployees->random();

            Winner::create([
                'prize_id'    => $prize->id,
                'code'        => $employee->registration_number,
                'winner_name' => $employee->employee_name,
                'drawn_at'    => now(),
            ]);

            return response()->json([
                'code'        => $employee->registration_number,
                'winner_name' => $employee->employee_name,
                'prize'       => $prize->name,
                'remaining'   => $prize->availableQuantity() - 1,
            ]);
        });
    }

    public function drawAll(Request $request)
    {
        $activeDraw = Draw::where('active', true)->first();
        if (! $activeDraw) {
            return response()->json(['error' => 'No active draw']);
        }

        $prize = Prize::where('draw_id', $activeDraw->id)
            ->where('order', '>', 0)
            ->orderBy('order')
            ->whereRaw('(quantity - (SELECT COUNT(*) FROM winners WHERE winners.prize_id = prizes.id)) > 0')
            ->first();

        if (! $prize) {
            return response()->json(['error' => 'No prizes available for active draw']);
        }

        $drawnRegistrationNumbers = Winner::whereHas('prize', function ($q) use ($activeDraw) {
            $q->where('draw_id', $activeDraw->id);
        })->pluck('code')->toArray();

        $availableEmployees = Employee::where('draw_id', $activeDraw->id)
            ->when(!empty($drawnRegistrationNumbers), function ($query) use ($drawnRegistrationNumbers) {
                return $query->whereNotIn('registration_number', $drawnRegistrationNumbers);
            })
            ->get();

        if ($availableEmployees->isEmpty()) {
            return response()->json(['error' => 'No employees available for active draw']);
        }

        $results = [];

        while ($prize->availableQuantity() > 0 && $availableEmployees->isNotEmpty()) {
            $employee = $availableEmployees->random();

            Winner::create([
                'prize_id'    => $prize->id,
                'code'        => $employee->registration_number,
                'winner_name' => $employee->employee_name,
                'drawn_at'    => now(),
            ]);

            $results[] = [
                'code'        => $employee->registration_number,
                'winner_name' => $employee->employee_name,
                'prize'       => $prize->name,
                'remaining'   => max($prize->availableQuantity() - 1, 0),
            ];

            $availableEmployees = $availableEmployees->reject(function ($item) use ($employee) {
                return $item->registration_number === $employee->registration_number;
            })->values();

            $prize->refresh();
        }

        return response()->json($results);
    }

    public function getWinners(Request $request)
    {
        $activeDraw = Draw::where('active', true)->first();
        if (! $activeDraw) {
            return response()->json([]);
        }

        $prizeId = $request->query('prize_id');
        if ($prizeId) {
            $prize = Prize::where('draw_id', $activeDraw->id)
                ->where('id', $prizeId)
                ->first();
        } else {
            $prize = Prize::where('draw_id', $activeDraw->id)
                ->where('order', '>', 0)
                ->orderBy('order')
                ->whereRaw('(quantity - (SELECT COUNT(*) FROM winners WHERE winners.prize_id = prizes.id)) > 0')
                ->first();
        }

        if (! $prize) {
            return response()->json([]);
        }

        $winners = Winner::where('prize_id', $prize->id)
            ->orderBy('drawn_at', 'asc')
            ->get()
            ->map(function ($w) use ($prize) {
                return [
                    'id'          => $w->id,
                    'code'        => $w->code,
                    'winner_name' => $w->winner_name,
                    'prize_name'  => $prize->name,
                    'drawn_at'    => (string) $w->drawn_at,
                ];
            });

        return response()->json($winners);
    }

    public function getAllWinners()
    {
        $activeDraw = Draw::where('active', true)->first();
        if (! $activeDraw) {
            return response()->json([]);
        }

        $prizes = Prize::where('draw_id', $activeDraw->id)->orderBy('order')->get();

        $result = $prizes->map(function ($p) {
            $w = $p->winners()->orderBy('drawn_at', 'asc')->get()->map(function ($win) {
                return [
                    'id'          => $win->id,
                    'code'        => $win->code,
                    'winner_name' => $win->winner_name,
                    'drawn_at'    => (string) $win->drawn_at,
                ];
            });

            return [
                'id'         => $p->id,
                'name'       => $p->name,
                'quantity'   => $p->quantity,
                'photo_path' => $p->photo_path,
                'winners'    => $w,
            ];
        });

        return response()->json($result);
    }

    public function admin()
    {
        return redirect('/admin/draws');
    }

    public function drawsIndex()
    {
        $draws      = Draw::withCount(['prizes', 'employees'])->orderBy('draw_date', 'desc')->get();
        $activeDraw = Draw::withCount('employees')->where('active', true)->first();
        return view('admin.draws', compact('draws', 'activeDraw'));
    }

    public function showDraw(Draw $draw)
    {
        $prizes     = $draw->prizes()->orderBy('order')->get();
        $winners    = Winner::whereHas('prize', function ($q) use ($draw) {
            $q->where('draw_id', $draw->id);
        })->with('prize')->orderBy('drawn_at', 'desc')->get();
        $employees  = $draw->employees()->orderBy('registration_number')->get();
        $activeDraw = Draw::where('active', true)->first();
        return view('admin.draw', compact('draw', 'prizes', 'winners', 'employees', 'activeDraw'));
    }

    public function updateDraw(Request $request, Draw $draw)
    {
        $request->validate([
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string',
            'draw_date'   => 'nullable|date',
            'active'      => 'nullable|boolean',
        ]);

        $isActive = $request->has('active') && $request->boolean('active');

        if ($isActive) {
            // Deactivate all other draws when setting this one to active
            Draw::where('id', '!=', $draw->id)->update(['active' => false]);
        }

        $draw->update([
            'name'        => $request->name,
            'description' => $request->description,
            'draw_date'   => $request->draw_date ?: null,
            'active'      => $isActive,
        ]);

        return redirect("/admin/draws/{$draw->id}")->with('success', 'Draw updated');
    }

    public function storePrizeForDraw(Request $request, Draw $draw)
    {
        $request->merge(['draw_id' => $draw->id]);
        return $this->storePrize($request);
    }

    public function storeEmployee(Request $request, Draw $draw)
    {
        $request->validate([
            'registration_number' => [
                'required', 'string', 'max:255',
                Rule::unique('employees', 'registration_number')->where(function ($query) use ($draw) {
                    return $query->where('draw_id', $draw->id);
                }),
            ],
            'employee_name' => 'required|string|max:255',
        ]);

        Employee::create([
            'draw_id'             => $draw->id,
            'registration_number' => trim($request->registration_number),
            'employee_name'       => trim($request->employee_name),
        ]);

        return redirect()->back()->with('success', 'Employee ticket added');
    }

    public function storeEmployeeGeneral(Request $request)
    {
        $request->validate([
            'draw_id'             => 'required|exists:draws,id',
            'registration_number' => [
                'required', 'string', 'max:255',
                Rule::unique('employees', 'registration_number')->where(function ($query) use ($request) {
                    return $query->where('draw_id', $request->draw_id);
                }),
            ],
            'employee_name' => 'required|string|max:255',
        ]);

        Employee::create([
            'draw_id'             => $request->draw_id,
            'registration_number' => trim($request->registration_number),
            'employee_name'       => trim($request->employee_name),
        ]);

        return redirect()->back()->with('success', 'Employee ticket added');
    }

    public function importEmployees(Request $request)
    {
        $request->validate([
            'draw_id' => 'required|exists:draws,id',
        ]);

        if (! $request->filled('employees_text')) {
            $request->validate([
                'employees_file' => 'required|file|mimes:csv,txt,xls,xlsx',
            ]);
        }

        $importedCount         = 0;
        $skippedRows           = [];
        $existingRegistrations = Employee::where('draw_id', $request->draw_id)
            ->pluck('registration_number')
            ->map(fn($v) => strtoupper(trim($v)))
            ->toArray();
        $seenRegistrations = [];

        if ($request->filled('employees_text')) {
            $rows = $this->parseTextRows($request->input('employees_text'));
        } else {
            $file      = $request->file('employees_file');
            $extension = strtolower($file->getClientOriginalExtension());
            $path      = $file->path();

            if (in_array($extension, ['xls', 'xlsx'])) {
                if (! class_exists(\ZipArchive::class)) {
                    return redirect()->back()->withErrors([
                        'employees_file' => 'Excel import requires the PHP Zip extension. Please upload a CSV file or enable ext-zip.',
                    ]);
                }

                try {
                    $rows = $this->readXlsxRows($path);
                } catch (\Exception $e) {
                    return redirect()->back()->withErrors([
                        'employees_file' => 'Unable to parse the Excel file: ' . $e->getMessage(),
                    ]);
                }
            } else {
                if (($handle = fopen($path, 'r')) === false) {
                    return redirect()->back()->withErrors(['employees_file' => 'Unable to read uploaded file.']);
                }

                $firstLine = fgets($handle);
                rewind($handle);
                $delimiter = $this->detectDelimiter($firstLine);
                $rows      = [];
                while (($row = fgetcsv($handle, 0, $delimiter)) !== false) {
                    $rows[] = $row;
                }
                fclose($handle);
            }
        }

        $rowIndex = 0;
        foreach ($rows as $row) {
            $rowIndex++;

            if ($rowIndex === 1) {
                $header = array_map(function ($column) {
                    return trim(preg_replace('/[^a-z0-9]+/', ' ', strtolower((string) $column)));
                }, $row);

                if (count($header) >= 2 && str_contains($header[0], 'registration') && str_contains($header[1], 'employee')) {
                    continue;
                }
            }

            if (count($row) < 2) {
                continue;
            }

            $registration = trim((string) ($row[0] ?? ''));
            $employeeName = trim((string) ($row[1] ?? ''));
            $key          = strtoupper($registration);

            if ($registration === '' || $employeeName === '') {
                $skippedRows[] = "Row {$rowIndex}: missing registration number or employee name.";
                continue;
            }

            if (in_array($key, $existingRegistrations, true) || isset($seenRegistrations[$key])) {
                $skippedRows[] = "Row {$rowIndex}: duplicate registration number {$registration}.";
                continue;
            }

            try {
                Employee::create([
                    'draw_id'             => $request->draw_id,
                    'registration_number' => $registration,
                    'employee_name'       => $employeeName,
                ]);
                $importedCount++;
                $seenRegistrations[$key] = true;
            } catch (\Exception $e) {
                $skippedRows[] = "Row {$rowIndex}: could not import {$registration}.";
            }
        }

        $message = "Imported {$importedCount} employee ticket(s).";
        if (count($skippedRows) > 0) {
            $message .= ' ' . count($skippedRows) . ' row(s) skipped.';
        }

        return redirect()->back()
            ->with('import_success', $message)
            ->with('import_errors', $skippedRows);
    }

    private function parseTextRows(string $text): array
    {
        $text = str_replace(["\r\n", "\r"], "\n", $text);
        $lines = array_filter(array_map('trim', explode("\n", trim($text))));

        if (empty($lines)) {
            return [];
        }

        $delimiter = $this->detectDelimiter($lines[0]);
        $rows = [];
        foreach ($lines as $line) {
            $rows[] = str_getcsv($line, $delimiter);
        }

        return $rows;
    }

    private function detectDelimiter(string $line): string
    {
        if (substr_count($line, "\t") > substr_count($line, ',')) {
            return "\t";
        }
        if (substr_count($line, ';') > substr_count($line, ',')) {
            return ';';
        }
        return ',';
    }

    private function readXlsxRows(string $path): array
    {
        $zip = new \ZipArchive();
        if ($zip->open($path) !== true) {
            throw new \Exception('Unable to open XLSX file.');
        }

        // ── Load shared strings ──────────────────────────────────────────────
        $sharedStrings = [];
        if (($index = $zip->locateName('xl/sharedStrings.xml')) !== false) {
            $sharedXml = $zip->getFromIndex($index);
            if ($sharedXml !== false) {
                $doc = new \DOMDocument();
                $doc->loadXML($sharedXml);
                foreach ($doc->getElementsByTagName('t') as $t) {
                    $sharedStrings[] = $t->nodeValue;
                }
            }
        }

        // ── Load sheet ───────────────────────────────────────────────────────
        $sheetIndex = $zip->locateName('xl/worksheets/sheet1.xml');
        if ($sheetIndex === false) {
            $zip->close();
            throw new \Exception('Unable to find worksheet data in XLSX file.');
        }

        $sheetXml = $zip->getFromIndex($sheetIndex);
        $zip->close();

        if ($sheetXml === false) {
            throw new \Exception('Unable to extract worksheet from XLSX file.');
        }

        $doc = new \DOMDocument();
        $doc->loadXML($sheetXml);

        $rows = [];
        foreach ($doc->getElementsByTagName('row') as $rowNode) {
            $rowData = [];

            foreach ($rowNode->getElementsByTagName('c') as $cellNode) {
                // Convert column reference (e.g. "A", "B", "AA") to 0-based index
                $ref = $cellNode->getAttribute('r'); // e.g. "A1", "B2", "AA3"
                preg_match('/^([A-Z]+)/', $ref, $colMatch);
                $colIndex = 0;
                if (! empty($colMatch[1])) {
                    foreach (str_split($colMatch[1]) as $char) {
                        $colIndex = $colIndex * 26 + (ord($char) - ord('A') + 1);
                    }
                    $colIndex--; // convert to 0-based
                }

                $value = '';
                $t     = $cellNode->getAttribute('t');
                $vNode = $cellNode->getElementsByTagName('v')->item(0);
                if ($vNode) {
                    $rawValue = $vNode->nodeValue;
                    $value    = ($t === 's' && is_numeric($rawValue) && isset($sharedStrings[(int) $rawValue]))
                        ? $sharedStrings[(int) $rawValue]
                        : $rawValue;
                }

                $rowData[$colIndex] = $value;
            }

            // Sort by column index and re-index from 0
            ksort($rowData);
            $rows[] = array_values($rowData);
        }

        return $rows;
    }

    public function deleteEmployee(Employee $employee)
    {
        $isDrawn = Winner::whereHas('prize', function ($q) use ($employee) {
            $q->where('draw_id', $employee->draw_id);
        })->where('code', $employee->registration_number)->exists();

        if ($isDrawn) {
            return redirect()->back()->with('error', 'Cannot remove an employee who has already been drawn.');
        }

        $employee->delete();
        return redirect()->back()->with('success', 'Employee ticket removed');
    }

    public function prizesIndex()
    {
        $prizes = Prize::with('draw')->orderBy('order')->get();
        $draws  = Draw::orderBy('draw_date', 'desc')->get();
        return view('admin.prizes', compact('prizes', 'draws'));
    }

    public function employeesIndex()
    {
        $employees = Employee::with('draw')->orderBy('created_at', 'desc')->get();
        $draws     = Draw::orderBy('draw_date', 'desc')->get();
        return view('admin.employees', compact('employees', 'draws'));
    }

    public function winnersIndex()
    {
        $winners = Winner::with(['prize.draw'])->orderBy('drawn_at', 'desc')->get();
        $draws   = Draw::orderBy('draw_date', 'desc')->get();
        return view('admin.winners', compact('winners', 'draws'));
    }

    public function storePrize(Request $request)
    {
        $drawId = $request->draw_id ?: null;

        $request->validate([
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string',
            'photo'       => 'nullable|image|max:2048',
            'quantity'    => 'required|integer|min:1',
            'order'       => [
                'required', 'integer', 'min:1',
                Rule::unique('prizes', 'order')->where(function ($query) use ($drawId) {
                    return $drawId
                        ? $query->where('draw_id', $drawId)
                        : $query->whereNull('draw_id');
                }),
            ],
            'draw_id' => 'nullable|exists:draws,id',
        ]);

        $photoPath = null;
        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('prizes', 'public');
        }

        Prize::create([
            'name'        => $request->name,
            'description' => $request->description,
            'photo_path'  => $photoPath,
            'quantity'    => $request->quantity,
            'order'       => $request->order,
            'draw_id'     => $drawId,
        ]);

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Prize added successfully']);
        }

        return redirect()->back()->with('success', 'Prize added successfully');
    }

    public function importPrizes(Request $request)
    {
        $data = $request->input('prizes');
        if (! is_array($data)) {
            return response()->json(['success' => false, 'message' => 'Invalid data format'], 422);
        }

        $created    = [];
        $errors     = [];
        $usedOrders = [];

        foreach ($data as $index => $row) {
            $name        = trim($row['name']        ?? '');
            $description = trim($row['description'] ?? '');
            $quantity    = intval($row['quantity']   ?? 0);
            $order       = intval($row['order']      ?? 0);
            $drawId      = trim($row['draw_id']      ?? '');
            $drawId      = $drawId === '' ? null : intval($drawId);

            $validator = \Validator::make([
                'name'        => $name,
                'description' => $description,
                'quantity'    => $quantity,
                'order'       => $order,
            ], [
                'name'        => 'required|string|max:255',
                'description' => 'nullable|string',
                'quantity'    => 'required|integer|min:1',
                'order'       => 'required|integer|min:1',
            ]);

            if ($validator->fails()) {
                $errors[] = ['row' => $index + 1, 'errors' => $validator->errors()->all()];
                continue;
            }

            if ($drawId !== null && ! Draw::where('id', $drawId)->exists()) {
                $errors[] = ['row' => $index + 1, 'errors' => ['Invalid draw_id: draw does not exist']];
                continue;
            }

            if ($drawId) {
                $existingInDb = Prize::where('draw_id', $drawId)->where('order', $order)->exists();
            } else {
                $existingInDb = Prize::whereNull('draw_id')->where('order', $order)->exists();
            }

            $batchKey        = ($drawId ?? 'null') . ':' . $order;
            $existingInBatch = isset($usedOrders[$batchKey]);

            if ($existingInDb || $existingInBatch) {
                $errors[] = ['row' => $index + 1, 'errors' => ['Order ' . $order . ' already exists for this draw']];
                continue;
            }

            $usedOrders[$batchKey] = true;

            $prize = Prize::create([
                'name'        => $name,
                'description' => $description,
                'photo_path'  => null,
                'quantity'    => $quantity,
                'order'       => $order,
                'draw_id'     => $drawId,
            ]);

            $created[] = $prize;
        }

        return response()->json([
            'success' => true,
            'created' => count($created),
            'errors'  => $errors,
        ]);
    }

    public function storeDraw(Request $request)
    {
        $request->validate([
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string',
            'draw_date'   => 'nullable|date',
            'active'      => 'nullable|boolean',
        ]);

        $isActive = $request->has('active') && (bool) $request->active;

        if ($isActive) {
            // Deactivate all other draws when creating a new active draw
            Draw::query()->update(['active' => false]);
        }

        Draw::create([
            'name'        => $request->name,
            'description' => $request->description,
            'draw_date'   => $request->draw_date ?: null,
            'active'      => $isActive,
        ]);

        return redirect('/admin/draws')->with('success', 'Draw created');
    }

    public function activateDraw(Draw $draw)
    {
        Draw::where('id', '!=', $draw->id)->update(['active' => false]);
        $draw->active = true;
        $draw->save();
        return redirect('/admin/draws')->with('success', 'Active draw set');
    }

    public function deleteDraw(Draw $draw)
    {
        if ($draw->prizes()->count() > 0) {
            return redirect('/admin/draws')->with('error', 'Cannot delete draw with prizes. Remove its prizes first.');
        }
        $draw->delete();
        return redirect('/admin/draws')->with('success', 'Draw deleted');
    }

    public function deletePrize(Prize $prize)
    {
        if ($prize->winners()->count() > 0) {
            return redirect()->back()->with('error', 'Cannot delete prize with winners');
        }
        $prize->delete();
        return redirect()->back()->with('success', 'Prize deleted successfully');
    }

    public function getStats()
    {
        $activeDraw = Draw::where('active', true)->first();

        if ($activeDraw) {
            $totalWinners     = Winner::whereHas('prize', function ($q) use ($activeDraw) {
                $q->where('draw_id', $activeDraw->id);
            })->count();
            $totalPrizes      = Prize::where('draw_id', $activeDraw->id)->sum('quantity');
            $totalTickets     = Employee::where('draw_id', $activeDraw->id)->count();
            $remainingTickets = max($totalTickets - $totalWinners, 0);

            return response()->json([
                'active_draw'      => true,
                'draw_id'          => $activeDraw->id,
                'draw_name'        => $activeDraw->name,
                'totalPrizes'      => $totalPrizes,
                'totalWinners'     => $totalWinners,
                'remainingTickets' => $remainingTickets,
                'totalTickets'     => $totalTickets,
            ]);
        }

        $totalTickets = Employee::count();

        return response()->json([
            'active_draw'      => false,
            'totalPrizes'      => Prize::sum('quantity'),
            'totalWinners'     => Winner::count(),
            'remainingTickets' => max($totalTickets - Winner::count(), 0),
            'totalTickets'     => $totalTickets,
        ]);
    }

    public function getRemainingCodes()
    {
        $activeDraw = Draw::where('active', true)->first();
        if (! $activeDraw) {
            return response()->json([]);
        }

        $drawnRegistrationNumbers = Winner::whereHas('prize', function ($q) use ($activeDraw) {
            $q->where('draw_id', $activeDraw->id);
        })->pluck('code')->toArray();

        $availableEmployees = Employee::where('draw_id', $activeDraw->id)
            ->when(!empty($drawnRegistrationNumbers), function ($query) use ($drawnRegistrationNumbers) {
                return $query->whereNotIn('registration_number', $drawnRegistrationNumbers);
            })
            ->orderBy('registration_number')
            ->pluck('registration_number');

        return response()->json($availableEmployees);
    }

    public function showPrize(Prize $prize)
    {
        $draws = Draw::orderBy('draw_date', 'desc')->get();
        return response()->json([
            'id'            => $prize->id,
            'name'          => $prize->name,
            'description'   => $prize->description,
            'photo_path'    => $prize->photo_path,
            'quantity'      => $prize->quantity,
            'order'         => $prize->order,
            'draw_id'       => $prize->draw_id,
            'winners_count' => $prize->winners()->count(),
            'draws'         => $draws,
        ]);
    }

    public function updateWinner(Request $request, Winner $winner)
    {
        $request->validate(['winner_name' => 'nullable|string|max:255']);
        $winner->update(['winner_name' => $request->winner_name]);
        return response()->json(['success' => true]);
    }

    public function deleteWinner(Winner $winner)
    {
        $winner->delete();
        return response()->json(['success' => true]);
    }

    public function restoreWinner(Request $request)
    {
        $request->validate([
            'prize_id'    => 'required|exists:prizes,id',
            'code'        => 'required|string|max:20',
            'drawn_at'    => 'required|date',
            'winner_name' => 'nullable|string|max:255',
        ]);

        $winner = Winner::create([
            'prize_id'    => $request->prize_id,
            'code'        => $request->code,
            'drawn_at'    => $request->drawn_at,
            'winner_name' => $request->winner_name,
        ]);

        return response()->json(['success' => true, 'winner' => $winner]);
    }

    public function updatePrize(Request $request, Prize $prize)
    {
        $drawId = $request->draw_id ?: null;

        $request->validate([
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string',
            'photo'       => 'nullable|image|max:2048',
            'quantity'    => 'required|integer|min:1',
            'order'       => [
                'required', 'integer', 'min:1',
                Rule::unique('prizes', 'order')
                    ->ignore($prize->id)
                    ->where(function ($query) use ($drawId) {
                        return $drawId
                            ? $query->where('draw_id', $drawId)
                            : $query->whereNull('draw_id');
                    }),
            ],
            'draw_id' => 'nullable|exists:draws,id',
        ]);

        $prize->name        = $request->name;
        $prize->description = $request->description;
        $prize->quantity    = $request->quantity;
        $prize->order       = $request->order;
        $prize->draw_id     = $drawId;

        if ($request->hasFile('photo')) {
            $prize->photo_path = $request->file('photo')->store('prizes', 'public');
        }

        $prize->save();

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Prize updated successfully']);
        }

        return redirect()->back()->with('success', 'Prize updated successfully');
    }
}