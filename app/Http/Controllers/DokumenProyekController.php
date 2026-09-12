<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DokumenProyek;
use App\Models\Proyek;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class DokumenProyekController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $query = DokumenProyek::with(['proyek', 'uploader'])->latest();

        // Filtering which proyeks can be viewed
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

        $dokumens = $query->get();

        return view('dokumen.index', compact('dokumens', 'proyeks'));
    }

    public function show($id)
    {
        $dokumen = DokumenProyek::findOrFail($id);
        
        $extension = pathinfo($dokumen->file_path, PATHINFO_EXTENSION);
        $isPdf = strtolower($extension) === 'pdf';
        $isImage = in_array(strtolower($extension), ['jpg', 'jpeg', 'png', 'gif']);
        
        return view('dokumen.show', compact('dokumen', 'isPdf', 'isImage'));
    }

    public function viewFile($id)
    {
        $dokumen = DokumenProyek::findOrFail($id);
        return $this->serveFile($dokumen->file_path);
    }

    public function viewLampiran($id)
    {
        $dokumen = DokumenProyek::findOrFail($id);
        if (!$dokumen->lampiran_tambahan) {
            abort(404, 'Lampiran opsional tidak tersedia.');
        }
        return $this->serveFile($dokumen->lampiran_tambahan);
    }

    private function serveFile($filePath)
    {
        $cleanPath = str_replace('\\', '/', $filePath);

        // 1. Cek storage/app/public/
        $direct = storage_path('app/public/' . $cleanPath);
        if (file_exists($direct) && !is_dir($direct)) {
            return response()->file($direct);
        }

        // 2. Cek disk public Laravel
        if (Storage::disk('public')->exists($cleanPath)) {
            return Storage::disk('public')->response($cleanPath);
        }

        // 3. Cek storage/app/
        $appPath = storage_path('app/' . $cleanPath);
        if (file_exists($appPath) && !is_dir($appPath)) {
            return response()->file($appPath);
        }

        // 4. Cek di storage_backup_*
        $backups = glob(public_path('storage_backup_*'));
        foreach ($backups as $b) {
            $backupPath = $b . '/' . $cleanPath;
            if (file_exists($backupPath) && !is_dir($backupPath)) {
                return response()->file($backupPath);
            }
        }

        // 5. Pencarian rekursif jika nama file cocok
        $filename = basename($cleanPath);
        foreach ([storage_path('app/public'), storage_path('app'), public_path()] as $sRoot) {
            if (is_dir($sRoot)) {
                $it = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($sRoot, \RecursiveDirectoryIterator::SKIP_DOTS));
                foreach ($it as $item) {
                    if ($item->isFile() && $item->getFilename() === $filename) {
                        return response()->file($item->getPathname());
                    }
                }
            }
        }

        abort(404, 'File dokumen tidak ditemukan di server.');
    }

    public function store(Request $request)
    {
        // PPTK has right to upload Kontrak / SPMK
        if (!Auth::user()->isPPTK()) abort(403, 'Hanya PPTK yang dapat mengunggah dokumen kontrak.');

        $request->validate([
            'proyek_id' => 'required|exists:proyeks,id',
            'tipe_dokumen' => 'required|in:Kontrak,SPMK,Kontrak Konsultan,SPMK Konsultan,Dokumen MC0,Lainnya',
            'nama_dokumen' => 'required|string|max:255',
            'file_dokumen' => 'required|file|mimes:pdf,doc,docx,jpg,jpeg,png,zip,rar|max:10240',
            'lampiran_tambahan' => 'nullable|file|mimes:pdf,doc,docx,zip,rar,jpg,jpeg,png|max:10240',
        ], [
            'proyek_id.required' => 'Pilih proyek terlebih dahulu.',
            'tipe_dokumen.required' => 'Tipe dokumen wajib dipilih.',
            'nama_dokumen.required' => 'Nama dokumen wajib diisi.',
            'file_dokumen.required' => 'File dokumen utama wajib diunggah.',
            'file_dokumen.mimes' => 'Format file dokumen tidak diizinkan.',
            'file_dokumen.max' => 'Ukuran file dokumen maksimal 10MB.',
            'lampiran_tambahan.mimes' => 'Format lampiran tambahan tidak diizinkan.',
            'lampiran_tambahan.max' => 'Ukuran lampiran tambahan maksimal 10MB.'
        ]);

        // check if proyek belongs to this PPTK
        $proyek = Proyek::findOrFail($request->proyek_id);
        if ($proyek->pptk_id !== Auth::id()) {
            abort(403, 'Anda tidak memiliki akses ke proyek ini.');
        }

        $path = $request->file('file_dokumen')->store('dokumen_proyeks', 'public');

        $lampiranPath = null;
        if ($request->hasFile('lampiran_tambahan')) {
            $lampiranPath = $request->file('lampiran_tambahan')->store('dokumen_proyeks', 'public');
        }

        DokumenProyek::create([
            'proyek_id' => $request->proyek_id,
            'tipe_dokumen' => $request->tipe_dokumen,
            'nama_dokumen' => $request->nama_dokumen,
            'file_path' => $path,
            'lampiran_tambahan' => $lampiranPath,
            'uploaded_by' => Auth::id(),
            'status' => 'menunggu_validasi',
        ]);

        return redirect()->route('dokumen.index')->with('success', 'Dokumen berhasil diunggah.');
    }

    public function approve(Request $request, DokumenProyek $dokuman)
    {
        if (!Auth::user()->isKontraktor()) abort(403, 'Hanya Kontraktor yang dapat memvalidasi dokumen.');

        $dokuman->update([
            'status' => 'disetujui',
            'catatan_validasi' => $request->catatan_validasi,
        ]);

        return redirect()->route('dokumen.index')->with('success', 'Dokumen Kontrak berhasil disetujui.');
    }

    public function reject(Request $request, DokumenProyek $dokuman)
    {
        if (!Auth::user()->isKontraktor()) abort(403, 'Hanya Kontraktor yang dapat memvalidasi dokumen.');

        $dokuman->update([
            'status' => 'ditolak',
            'catatan_validasi' => $request->catatan_validasi,
        ]);

        return redirect()->route('dokumen.index')->with('error', 'Dokumen Kontrak ditolak.');
    }

    public function destroy($id) 
    {
        $dokumen = DokumenProyek::findOrFail($id);
        if ($dokumen->uploaded_by !== Auth::id()) {
            abort(403, 'Tidak diizinkan.');
        }

        Storage::disk('public')->delete($dokumen->file_path);
        if ($dokumen->lampiran_tambahan) {
            Storage::disk('public')->delete($dokumen->lampiran_tambahan);
        }
        $dokumen->delete();

        return redirect()->route('dokumen.index')->with('success', 'Dokumen berhasil dihapus.');
    }
}
