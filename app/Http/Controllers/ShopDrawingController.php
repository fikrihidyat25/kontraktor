<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ShopDrawing;
use App\Models\Proyek;
use Illuminate\Support\Facades\Auth;

class ShopDrawingController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        $query = ShopDrawing::with(['proyek', 'kontraktor', 'approver'])->latest();

        if ($user->isKontraktor()) {
            $query->where('kontraktor_id', $user->id);
            $proyeks = Proyek::where('kontraktor_id', $user->id)->get();
        } elseif ($user->isKonsultan()) {
            $query->whereHas('proyek', function ($q) use ($user) {
                $q->where('konsultan_id', $user->id);
            });
            $proyeks = collect();
        } elseif ($user->isPPK()) {
            $query->whereHas('proyek', function ($q) use ($user) {
                $q->where('ppk_id', $user->id);
            });
            $proyeks = collect();
        } elseif ($user->isPPTK()) {
            $query->whereHas('proyek', function ($q) use ($user) {
                $q->where('pptk_id', $user->id);
            });
            $proyeks = collect();
        } else {
            $proyeks = collect();
        }

        $shopDrawings = $query->get();

        return view('shop_drawing.index', compact('shopDrawings', 'proyeks'));
    }

    public function store(Request $request)
    {
        if (!Auth::user()->isKontraktor()) {
            abort(403, 'Hanya kontraktor yang dapat mengajukan Shop Drawing.');
        }

        $request->validate([
            'proyek_id' => 'required|exists:proyeks,id',
            'judul' => 'required|string|max:255',
            'keterangan' => 'nullable|string',
            'file_gambar' => 'required|file|mimes:pdf,jpg,jpeg,png|max:15360',
        ], [
            'file_gambar.required' => 'File gambar/dokumen wajib diunggah.',
            'file_gambar.max' => 'Ukuran file gambar maksimal 15MB.'
        ]);

        $proyek = Proyek::findOrFail($request->proyek_id);
        if ($proyek->kontraktor_id !== Auth::id()) {
            abort(403, 'Anda tidak ditugaskan pada proyek ini.');
        }

        $path = $request->file('file_gambar')->store('shop_drawings', 'public');

        ShopDrawing::create([
            'proyek_id' => $request->proyek_id,
            'kontraktor_id' => Auth::id(),
            'judul' => $request->judul,
            'keterangan' => $request->keterangan,
            'file_gambar' => $path,
            'status' => 'diajukan',
        ]);

        return redirect()->route('shop-drawing.index')->with('success', 'Shop Drawing berhasil diajukan.');
    }

        public function approve(Request $request, ShopDrawing $shopDrawing)
    {
        if (!Auth::user()->isPPTK() && !Auth::user()->isKonsultan()) {
            abort(403, 'Hanya PPTK atau Konsultan yang dapat menyetujui Shop Drawing.');
        }

        if (Auth::user()->isKonsultan()) {
            $shopDrawing->update([
                'status' => 'diverifikasi_konsultan',
                'catatan_konsultan' => $request->catatan_pptk,
            ]);
        } elseif (Auth::user()->isPPTK()) {
            $shopDrawing->update([
                'status' => 'disetujui',
                'catatan_pptk' => $request->catatan_pptk,
                'approved_by' => Auth::id(),
                'approved_at' => now(),
            ]);
        }

        return redirect()->route('shop-drawing.index')->with('success', 'Shop Drawing disetujui.');
    }

        public function reject(Request $request, ShopDrawing $shopDrawing)
    {
        if (!Auth::user()->isPPTK() && !Auth::user()->isKonsultan()) {
            abort(403, 'Hanya PPTK atau Konsultan yang dapat menolak Shop Drawing.');
        }

        if (Auth::user()->isKonsultan()) {
            $shopDrawing->update([
                'status' => 'ditolak',
                'catatan_konsultan' => $request->catatan_pptk,
            ]);
        } elseif (Auth::user()->isPPTK()) {
            $shopDrawing->update([
                'status' => 'ditolak',
                'catatan_pptk' => $request->catatan_pptk,
                'approved_by' => Auth::id(),
                'approved_at' => now(),
            ]);
        }

        return redirect()->route('shop-drawing.index')->with('error', 'Shop Drawing ditolak.');
    }
}
