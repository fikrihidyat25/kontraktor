<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BeritaAcaraKonsultan;
use App\Models\Proyek;
use Illuminate\Support\Facades\Auth;

class BeritaAcaraKonsultanController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        $query = BeritaAcaraKonsultan::with(['proyek', 'konsultan'])->latest();

        if ($user->isKonsultan()) {
            $query->where('konsultan_id', $user->id);
            $proyeks = Proyek::where('konsultan_id', $user->id)->get();
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
        } elseif ($user->isKontraktor()) {
            $query->whereHas('proyek', function ($q) use ($user) {
                $q->where('kontraktor_id', $user->id);
            });
            $proyeks = collect();
        } else {
            $proyeks = collect();
            $query->where('id', -1);
        }

        $beritaAcaras = $query->get();

        return view('berita_acara_konsultan.index', compact('beritaAcaras', 'proyeks'));
    }

    public function store(Request $request)
    {
        if (!Auth::user()->isKonsultan()) abort(403, 'Hanya konsultan yang dapat membuat Berita Acara Konsultan.');

        $request->validate([
            'proyek_id' => 'required|exists:proyeks,id',
            'judul' => 'required|string|max:255',
            'tanggal' => 'required|date',
            'pembahasan' => 'required|string',
            'file_berita_acara' => 'required|file|mimes:pdf,doc,docx|max:10240',
            'dokumentasi' => 'nullable|array',
            'dokumentasi.*' => 'file|mimes:jpg,jpeg,png,pdf|max:5120',
        ]);

        $path = $request->file('file_berita_acara')->store('berita_acara_konsultan_docs', 'public');

        $dokumentasiPaths = [];
        if ($request->hasFile('dokumentasi')) {
            foreach ($request->file('dokumentasi') as $file) {
                $dokumentasiPaths[] = $file->store('berita_acara_konsultan_docs', 'public');
            }
        }

        BeritaAcaraKonsultan::create([
            'proyek_id' => $request->proyek_id,
            'konsultan_id' => Auth::id(),
            'judul' => $request->judul,
            'tanggal' => $request->tanggal,
            'pembahasan' => $request->pembahasan,
            'file_berita_acara' => $path,
            'dokumentasi' => empty($dokumentasiPaths) ? null : $dokumentasiPaths,
        ]);

        return redirect()->route('berita-acara-konsultan.index')->with('success', 'Berita Acara Konsultan berhasil ditambahkan.');
    }

    public function destroy($id)
    {
        $ba = BeritaAcaraKonsultan::findOrFail($id);
        if ($ba->konsultan_id !== Auth::id()) {
            abort(403);
        }

        $ba->delete();
        return redirect()->route('berita-acara-konsultan.index')->with('success', 'Berita Acara dihapus.');
    }
}
