<?php

namespace App\Http\Controllers;

use App\Models\Prize;
use App\Models\Winner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LuckyDrawController extends Controller
{
    public function index()
    {
        $currentPrize = Prize::where('order', '>', 0)
            ->orderBy('order')
            ->whereRaw('(quantity - (SELECT COUNT(*) FROM winners WHERE winners.prize_id = prizes.id)) > 0')
            ->first();

        $prizes = Prize::orderBy('order')->get();
        $winners = Winner::with('prize')->orderBy('drawn_at', 'desc')->get();

        return view('welcome', compact('currentPrize', 'prizes', 'winners'));
    }

    public function getCurrentPrize()
    {
        $prize = Prize::where('order', '>', 0)
            ->orderBy('order')
            ->whereRaw('(quantity - (SELECT COUNT(*) FROM winners WHERE winners.prize_id = prizes.id)) > 0')
            ->first();

        if (!$prize) {
            return response()->json(['error' => 'No prizes available']);
        }

        return response()->json([
            'id' => $prize->id,
            'name' => $prize->name,
            'description' => $prize->description,
            'photo_path' => $prize->photo_path,
            'remaining' => $prize->availableQuantity(),
            'total' => $prize->quantity,
            'won' => $prize->quantity - $prize->availableQuantity()
        ]);
    }

    public function draw(Request $request)
    {
        $prize = Prize::where('order', '>', 0)
            ->orderBy('order')
            ->whereRaw('(quantity - (SELECT COUNT(*) FROM winners WHERE winners.prize_id = prizes.id)) > 0')
            ->first();

        if (!$prize) {
            return response()->json(['error' => 'No prizes available']);
        }

        // Get remaining codes (not drawn for any prize)
        $drawnCodes = Winner::pluck('code')->toArray();
        $allCodes = range(1, 2000);
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

    // Return winners only for the current prize
    public function getWinners()
    {
        $prize = Prize::where('order', '>', 0)
            ->orderBy('order')
            ->whereRaw('(quantity - (SELECT COUNT(*) FROM winners WHERE winners.prize_id = prizes.id)) > 0')
            ->first();

        if (! $prize) {
            // No active prize — return empty array
            return response()->json([]);
        }

        $winners = Winner::where('prize_id', $prize->id)
            ->orderBy('drawn_at', 'desc')
            ->get()
            ->map(function ($w) use ($prize) {
                return [
                    'code' => $w->code,
                    'prize_name' => $prize->name,
                    'drawn_at' => (string) $w->drawn_at,
                ];
            });

        return response()->json($winners);
    }

    // Return all prizes with their winners (grouped)
    public function getAllWinners()
    {
        $prizes = Prize::orderBy('order')->get();

        $result = $prizes->map(function ($p) {
            $w = $p->winners()->orderBy('drawn_at', 'desc')->get()->map(function ($win) {
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
        $prizes = Prize::orderBy('order')->get();
        return view('admin', compact('prizes'));
    }

    public function storePrize(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'photo' => 'nullable|image|max:2048',
            'quantity' => 'required|integer|min:1',
            'order' => 'required|integer|min:1'
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
            'order' => $request->order
        ]);

        return redirect('/admin')->with('success', 'Prize added successfully');
    }

    public function deletePrize(Prize $prize)
    {
        if ($prize->winners()->count() > 0) {
            return redirect('/admin')->with('error', 'Cannot delete prize with winners');
        }
        $prize->delete();
        return redirect('/admin')->with('success', 'Prize deleted successfully');
    }

    public function getStats()
    {
        return response()->json([
            'totalPrizes' => Prize::sum('quantity'),
            'totalWinners' => Winner::count(),
            'remainingCodes' => 2000 - Winner::count()
        ]);
    }
}
