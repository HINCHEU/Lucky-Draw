<?php

namespace App\Http\Controllers;

use App\Models\Prize;
use App\Models\Winner;
use App\Models\Draw;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
            $prizes = Prize::orderBy('order')->get();
            $winners = Winner::with('prize')->orderBy('drawn_at', 'desc')->get();
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
            'id' => $prize->id,
            'name' => $prize->name,
            'description' => $prize->description,
            'photo_path' => $prize->photo_path,
            'remaining' => $prize->availableQuantity(),
            'total' => $prize->quantity,
            'won' => $prize->quantity - $prize->availableQuantity(),
            'start_code' => $activeDraw->start_code,
            'end_code' => $activeDraw->end_code
        ]);
    }

    public function draw(Request $request)
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

        $drawnCodes = Winner::whereHas('prize', function ($q) use ($activeDraw) {
            $q->where('draw_id', $activeDraw->id);
        })->pluck('code')->toArray();
        $start = $activeDraw->start_code;
        $end = $activeDraw->end_code;

        // Ensure start and end codes are valid
        if ($start === null || $end === null || $start > $end) {
            return response()->json(['error' => 'Invalid code range for the active draw']);
        }

        $allCodes = range((int)$start, (int)$end);
        $availableCodes = array_diff($allCodes, $drawnCodes);

        if (empty($availableCodes)) {
            return response()->json(['error' => 'No codes available']);
        }

        $randomCode = $availableCodes[array_rand($availableCodes)];
        $code = str_pad($randomCode, 4, '0', STR_PAD_LEFT);

        // Save winner
        Winner::create([
            'prize_id' => $prize->id,
            'code' => $code,
            'drawn_at' => now()
        ]);

        return response()->json([
            'code' => $code,
            'prize' => $prize->name,
            'remaining' => $prize->availableQuantity() - 1
        ]);
    }
    public function drawAll(Request $request)
{
    $activeDraw = Draw::where('active', true)->first();
    if (! $activeDraw) {
        return response()->json(['error' => 'No active draw']);
    }

    // Get only the CURRENT prize (same logic as getCurrentPrize)
    $prize = Prize::where('draw_id', $activeDraw->id)
        ->where('order', '>', 0)
        ->orderBy('order')
        ->whereRaw('(quantity - (SELECT COUNT(*) FROM winners WHERE winners.prize_id = prizes.id)) > 0')
        ->first();

    if (! $prize) {
        return response()->json(['error' => 'No prizes available for active draw']);
    }

    // Get all already-drawn codes for this draw
    $drawnCodes = Winner::whereHas('prize', function ($q) use ($activeDraw) {
        $q->where('draw_id', $activeDraw->id);
    })->pluck('code')->toArray();

    $start = $activeDraw->start_code;
    $end   = $activeDraw->end_code;

    if ($start === null || $end === null || $start > $end) {
        return response()->json(['error' => 'Invalid code range for the active draw']);
    }

    $results = [];

    // Draw all remaining slots for the current prize
    while ($prize->availableQuantity() > 0) {
        $allCodes       = range((int) $start, (int) $end);
        $availableCodes = array_values(array_diff($allCodes, $drawnCodes));

        if (empty($availableCodes)) {
            break; // No more codes in range
        }

        $randomCode = $availableCodes[array_rand($availableCodes)];
        $code       = str_pad($randomCode, 4, '0', STR_PAD_LEFT);

        Winner::create([
            'prize_id' => $prize->id,
            'code'     => $code,
            'drawn_at' => now(),
        ]);

        $drawnCodes[] = $randomCode; // Track in-memory so next iteration sees it

        $prize->refresh(); // Refresh so availableQuantity() is accurate

        $results[] = [
            'code'      => $code,
            'prize'     => $prize->name,
            'remaining' => $prize->availableQuantity(),
        ];
    }

    return response()->json($results);
}
    // Return winners only for the current prize
    public function getWinners()
    {
        $activeDraw = Draw::where('active', true)->first();
        if (! $activeDraw) {
            return response()->json([]);
        }

        $prize = Prize::where('draw_id', $activeDraw->id)
            ->where('order', '>', 0)
            ->orderBy('order')
            ->whereRaw('(quantity - (SELECT COUNT(*) FROM winners WHERE winners.prize_id = prizes.id)) > 0')
            ->first();

        if (! $prize) {
            return response()->json([]);
        }

        $winners = Winner::where('prize_id', $prize->id)
            ->orderBy('drawn_at', 'asc')
            ->get()
            ->map(function ($w) use ($prize) {
                return [
                    'id' => $w->id,
                    'code' => $w->code,
                    'prize_name' => $prize->name,
                    'drawn_at' => (string) $w->drawn_at,
                ];
            });

        return response()->json($winners);
    }

    // Return all prizes with their winners (grouped) for the active draw only
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
                    'code' => $win->code,
                    'drawn_at' => (string) $win->drawn_at,
                ];
            });

            return [
                'id' => $p->id,
                'name' => $p->name,
                'quantity' => $p->quantity,
                'photo_path' => $p->photo_path,
                'winners' => $w,
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
        $draws = Draw::withCount('prizes')->orderBy('draw_date', 'desc')->get();
        $activeDraw = Draw::where('active', true)->first();
        return view('admin.draws', compact('draws', 'activeDraw'));
    }

    public function showDraw(Draw $draw)
    {
        $prizes = $draw->prizes()->orderBy('order')->get();
        $winners = Winner::whereHas('prize', function ($q) use ($draw) {
            $q->where('draw_id', $draw->id);
        })->with('prize')->orderBy('drawn_at', 'desc')->get();
        $activeDraw = Draw::where('active', true)->first();
        return view('admin.draw', compact('draw', 'prizes', 'winners', 'activeDraw'));
    }

    public function updateDraw(Request $request, Draw $draw)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'draw_date' => 'nullable|date',
            'active' => 'nullable|boolean',
            'start_code' => 'nullable|integer|min:0',
            'end_code' => 'nullable|integer|min:0'
        ]);

        $start = $request->input('start_code');
        $end = $request->input('end_code');
        if ($start !== null && $end !== null && $start > $end) {
            return redirect()->back()->with('error', 'Start code must be less than or equal to end code');
        }

        if ($request->has('active') && $request->boolean('active')) {
            Draw::where('id', '!=', $draw->id)->update(['active' => false]);
            $draw->active = true;
        } else {
            $draw->active = false;
        }

        $draw->update([
            'name' => $request->name,
            'description' => $request->description,
            'draw_date' => $request->draw_date ?: null,
            'start_code' => $start ?: 1,
            'end_code' => $end ?: 2000,
        ]);

        return redirect("/admin/draws/{$draw->id}")->with('success', 'Draw updated');
    }

    public function storePrizeForDraw(Request $request, Draw $draw)
    {
        $request->merge(['draw_id' => $draw->id]);
        return $this->storePrize($request);
    }

    public function prizesIndex()
    {
        $prizes = Prize::with('draw')->orderBy('order')->get();
        $draws = Draw::orderBy('draw_date', 'desc')->get();
        return view('admin.prizes', compact('prizes', 'draws'));
    }

    public function storePrize(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'photo' => 'nullable|image|max:2048',
            'quantity' => 'required|integer|min:1',
            'order' => 'required|integer|min:1',
            'draw_id' => 'nullable|exists:draws,id'
        ]);

        $photoPath = null;
        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('prizes', 'public');
        }

        Prize::create([
            'name' => $request->name,
            'description' => $request->description,
            'photo_path' => $photoPath,
            'quantity' => $request->quantity,
            'order' => $request->order,
            'draw_id' => $request->draw_id
        ]);

        return redirect()->back()->with('success', 'Prize added successfully');
    }

    public function storeDraw(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'draw_date' => 'nullable|date',
            'active' => 'nullable|boolean',
            'start_code' => 'nullable|integer|min:0',
            'end_code' => 'nullable|integer|min:0'
        ]);

        // enforce start <= end if both provided
        $start = $request->input('start_code');
        $end = $request->input('end_code');
        if ($start !== null && $end !== null && $start > $end) {
            return redirect('/admin')->with('error', 'Start code must be less than or equal to end code');
        }

        $draw = Draw::create([
            'name' => $request->name,
            'description' => $request->description,
            'draw_date' => $request->draw_date ?: null,
            'active' => $request->has('active') ? (bool)$request->active : false,
            'start_code' => $start ?: 1,
            'end_code' => $end ?: 2000,
        ]);

        return redirect('/admin/draws')->with('success', 'Draw created');
    }

    public function activateDraw(Draw $draw)
    {
        // deactivate others
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
            $totalWinners = Winner::whereHas('prize', function ($q) use ($activeDraw) {
                $q->where('draw_id', $activeDraw->id);
            })->count();

            $totalPrizes = Prize::where('draw_id', $activeDraw->id)->sum('quantity');

            // Calculate remaining codes based on the code range (start_code to end_code)
            $totalCodesInRange = ($activeDraw->end_code - $activeDraw->start_code) + 1;
            $remainingCodes = max($totalCodesInRange - $totalWinners, 0);

            return response()->json([
                'active_draw' => true,
                'draw_id' => $activeDraw->id,
                'draw_name' => $activeDraw->name,
                'totalPrizes' => $totalPrizes,
                'totalWinners' => $totalWinners,
                'remainingCodes' => $remainingCodes
            ]);
        }

        return response()->json([
            'active_draw' => false,
            'totalPrizes' => Prize::sum('quantity'),
            'totalWinners' => Winner::count(),
            'remainingCodes' => 2000 - Winner::count()
        ]);
    }

    public function showPrize(Prize $prize)
    {
        $draws = Draw::orderBy('draw_date', 'desc')->get();
        return response()->json([
            'id' => $prize->id,
            'name' => $prize->name,
            'description' => $prize->description,
            'photo_path' => $prize->photo_path,
            'quantity' => $prize->quantity,
            'order' => $prize->order,
            'draw_id' => $prize->draw_id,
            'winners_count' => $prize->winners()->count(),
            'draws' => $draws
        ]);
    }
    public function deleteWinner(Winner $winner)
    {
        $winner->delete();
        return response()->json(['success' => true]);
    }

    public function updatePrize(Request $request, Prize $prize)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'photo' => 'nullable|image|max:2048',
            'quantity' => 'required|integer|min:1',
            'order' => 'required|integer|min:1',
            'draw_id' => 'nullable|exists:draws,id'
        ]);

        $prize->name = $request->name;
        $prize->description = $request->description;
        $prize->quantity = $request->quantity;
        $prize->order = $request->order;
        $prize->draw_id = $request->draw_id ?: null;

        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('prizes', 'public');
            $prize->photo_path = $photoPath;
        }

        $prize->save();

        return redirect()->back()->with('success', 'Prize updated successfully');
    }
}
