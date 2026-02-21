<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class PreventNewData
{
    private const BLOCKED_ROUTES = [
        'admin.category.income.store',
        'admin.category.expense.store',
        'admin.income.store',
        'admin.income.create',
        'admin.expense.store',
        'admin.expense.create',
        'customer.category.income.store',
        'customer.category.expense.store',
        'customer.income.store',
        'customer.income.create',
        'customer.expense.store',
        'customer.expense.create',
    ];

    private const MESSAGE = 'Budgetku akan dihentikan pada 1 Maret 2026. Penambahan data baru tidak lagi diperbolehkan. Silakan download data Anda sebelum tanggal tersebut.';

    public function handle(Request $request, Closure $next)
    {
        $routeName = $request->route()?->getName();

        if ($routeName && in_array($routeName, self::BLOCKED_ROUTES)) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'status' => false,
                    'message' => self::MESSAGE,
                ], 403);
            }

            return redirect()->back()->with('error', self::MESSAGE);
        }

        return $next($request);
    }
}
