<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class CacheHelper
{
    /**
     * Clear all dashboard related caches for current user
     */
    public static function clearDashboardCaches($userId = null): void
    {
        $userId = $userId ?? Auth::id();
        
        $cacheKeys = [
            'gaji_bulan_ini_user_' . $userId,
            'gaji_bulan_lalu_user_' . $userId,
            'pengeluaran_bulan_ini_user_' . $userId,
            'pengeluaran_bulan_lalu_user_' . $userId,
            'laporan_tahunan_user_' . $userId,
            'total_saldo_user_' . $userId,
            'user_categories_income_' . $userId,
        ];

        foreach ($cacheKeys as $key) {
            Cache::forget($key);
        }
    }

    /**
     * Clear specific cache by key pattern
     */
    public static function clearCachePattern(string $pattern): void
    {
        // For Redis driver, you might want to implement pattern deletion
        // For now, this is a placeholder for specific cache clearing
    }
}