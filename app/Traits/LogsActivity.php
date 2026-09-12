<?php

namespace App\Traits;

use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;

trait LogsActivity
{
    protected static function bootLogsActivity()
    {
        static::created(function ($model) {
            self::logAction($model, 'Tambah');
        });

        static::updated(function ($model) {
            self::logAction($model, 'Edit');
        });

        static::deleted(function ($model) {
            self::logAction($model, 'Hapus');
        });
    }

    protected static function logAction($model, $action)
    {
        if (Auth::check() && Auth::user()->role === 'kontraktor') {
            $modelName = class_basename($model);
            
            // Format deskripsi agar lebih rapi, misal: 'Tambah LaporanHarian (ID: 1)'
            $description = "{$action} data {$modelName}";
            
            // Coba ambil judul atau nama jika ada untuk deskripsi yang lebih baik
            if (isset($model->judul)) {
                $description .= " '{$model->judul}'";
            } elseif (isset($model->nama)) {
                $description .= " '{$model->nama}'";
            } else {
                $description .= " (ID: {$model->id})";
            }

            ActivityLog::create([
                'user_id' => Auth::id(),
                'action' => $action,
                'description' => $description,
            ]);
        }
    }
}
