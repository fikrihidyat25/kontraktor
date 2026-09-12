<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SiteMeeting;
use App\Models\Proyek;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class SiteMeetingController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $query = SiteMeeting::with(['proyek', 'pembuat'])->latest();

        if ($user->isKontraktor()) {
            $query->whereHas('proyek', function ($q) use ($user) {
                $q->where('kontraktor_id', $user->id);
            });
            $proyeks = Proyek::where('kontraktor_id', $user->id)->get();
        } elseif ($user->isKonsultan()) {
            $query->whereHas('proyek', function ($q) use ($user) {
                $q->where('konsultan_id', $user->id);
            });
            $proyeks = Proyek::where('konsultan_id', $user->id)->get();
        } elseif ($user->isPPK()) {
            $query->whereHas('proyek', function ($q) use ($user) {
                $q->where('ppk_id', $user->id);
            });
            $proyeks = Proyek::where('ppk_id', $user->id)->get();
        } elseif ($user->isPPTK()) {
            $query->whereHas('proyek', function ($q) use ($user) {
                $q->where('pptk_id', $user->id);
            });
            $proyeks = Proyek::where('pptk_id', $user->id)->get();
        } else {
            $proyeks = collect();
        }

        $meetings = $query->get();

        return view('site_meeting.index', compact('meetings', 'proyeks'));
    }

    public function store(Request $request)
    {
        if (!Auth::user()->isKonsultan()) {
            abort(403, 'Hanya konsultan yang dapat mengupload Berita Acara.');
        }

        $request->validate([
            'proyek_id' => 'required|exists:proyeks,id',
            'jenis_rapat' => 'required|in:Site Meeting,PCM',
            'tanggal_rapat' => 'required|date',
            'agenda_rapat' => 'required|string',
            'dokumen_berita_acara' => 'required|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:10240',
            'dokumentasi' => 'nullable|array',
            'dokumentasi.*' => 'file|mimes:pdf,doc,docx,jpg,jpeg,png,zip,rar|max:10240',
            'lampiran_tambahan' => 'nullable|file|mimes:pdf,doc,docx,zip,rar,jpg,jpeg,png|max:10240',
        ]);

        $proyek = Proyek::findOrFail($request->proyek_id);
        $user = Auth::user();
        if (
            ($user->isKontraktor() && $proyek->kontraktor_id !== $user->id) ||
            ($user->isKonsultan() && $proyek->konsultan_id !== $user->id) ||
            ($user->isPPK() && $proyek->ppk_id !== $user->id) ||
            ($user->isPPTK() && $proyek->pptk_id !== $user->id)
        ) {
            abort(403, 'Anda tidak ditugaskan di proyek ini.');
        }

        // Calculate pertemuan ke berapa based on jenis_rapat
        $pertemuanKe = SiteMeeting::where('proyek_id', $proyek->id)
                                    ->where('jenis_rapat', $request->jenis_rapat)
                                    ->count() + 1;

        $path = $request->file('dokumen_berita_acara')->store('site_meetings', 'public');

        $dokumentasiPaths = [];
        if ($request->hasFile('dokumentasi')) {
            foreach ($request->file('dokumentasi') as $file) {
                $dokumentasiPaths[] = $file->store('site_meetings', 'public');
            }
        }

        $lampiranPath = null;
        if ($request->hasFile('lampiran_tambahan')) {
            $lampiranPath = $request->file('lampiran_tambahan')->store('site_meetings', 'public');
        }

        SiteMeeting::create([
            'proyek_id' => $request->proyek_id,
            'created_by' => Auth::id(),
            'jenis_rapat' => $request->jenis_rapat,
            'pertemuan_ke' => $pertemuanKe,
            'tanggal_rapat' => $request->tanggal_rapat,
            'agenda_rapat' => $request->agenda_rapat,
            'dokumen_berita_acara' => $path,
            'dokumentasi' => empty($dokumentasiPaths) ? null : $dokumentasiPaths,
            'lampiran_tambahan' => $lampiranPath,
        ]);

        return redirect()->route('site-meeting.index')->with('success', 'Berita Acara berhasil ditambahkan.');
    }

    public function destroy($id)
    {
        $meeting = SiteMeeting::findOrFail($id);
        if ($meeting->created_by !== Auth::id()) {
            abort(403, 'Hanya pembuat yang dapat menghapus.');
        }

        Storage::disk('public')->delete($meeting->dokumen_berita_acara);
        $meeting->delete();

        return redirect()->route('site-meeting.index')->with('success', 'Site Meeting berhasil dihapus.');
    }
}
