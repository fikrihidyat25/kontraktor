<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Proyek;
use Illuminate\Support\Facades\Auth;

class TentangController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $proyeks = collect();

        if ($user->isKontraktor()) {
            $proyeks = Proyek::where('kontraktor_id', $user->id)->with(['konsultan', 'ppk'])->get();
        } elseif ($user->isKonsultan()) {
            $proyeks = Proyek::where('konsultan_id', $user->id)->with(['kontraktor', 'ppk'])->get();
        } elseif ($user->isPPK()) {
            $proyeks = Proyek::where('ppk_id', $user->id)->with(['kontraktor', 'konsultan'])->get();
        }

        return view('tentang.index', compact('proyeks'));
    }
}
