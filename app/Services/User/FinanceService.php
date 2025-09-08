<?php

namespace App\Services\User;

use App\Models\Finance;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class FinanceService
{
    public function getDatatableQuery()
    {
        return Finance::with('category_finance')
            ->where('users_uuid', Auth::id())
            ->orderBy('created_at', 'DESC');
    }

    public function create(array $data): Finance
    {
        $financeData = [
            'uuid' => (string) Str::uuid(),
            'users_uuid' => Auth::id(),
            'category_finances_uuid' => $data['category_finances_uuid'],
            'name_item' => $data['name_item'],
            'price' => $this->formatPriceForStorage($data['price']),
            'purchase_date' => $data['purchase_date'] ?? now(),
            'payment_methods_uuid' => $data['payment_methods_uuid'],
            'bukti_pembayaran' => null,
        ];

        return Finance::create($financeData);
    }

    public function getById(string $uuid): Finance
    {
        return Finance::findOrFail($uuid);
    }

    public function getByUser(string $uuid): Finance
    {
        return Finance::where('users_uuid', Auth::id())->findOrFail($uuid);
    }

    public function update(string $uuid, array $data): Finance
    {
        $finance = $this->getByUser($uuid);
        
        $updateData = [
            'users_uuid' => Auth::id(),
            'category_finances_uuid' => $data['category_finances_uuid'],
            'name_item' => $data['name_item'],
            'price' => $this->formatPriceForStorage($data['price']),
            'purchase_date' => $data['purchase_date'],
            'payment_methods_uuid' => $data['payment_methods_uuid'],
        ];

        $finance->update($updateData);
        
        return $finance;
    }

    public function delete(string $uuid): bool
    {
        $finance = $this->getByUser($uuid);
        return $finance->delete();
    }

    public function getSearchQuery(int $year)
    {
        return Finance::with('category_finance')
            ->where('users_uuid', Auth::id())
            ->whereYear('created_at', $year)
            ->orderBy('created_at', 'DESC');
    }

    public function getUserExpensesSummary(): array
    {
        $userId = Auth::id();
        $totalExpenses = Finance::where('users_uuid', $userId)->sum('price');
        $monthlyExpenses = Finance::where('users_uuid', $userId)
            ->whereMonth('purchase_date', now()->month)
            ->whereYear('purchase_date', now()->year)
            ->sum('price');

        return [
            'total_expenses' => $totalExpenses,
            'monthly_expenses' => $monthlyExpenses,
        ];
    }

    public function getExpensesByCategory(): array
    {
        return Finance::with('category_finance')
            ->where('users_uuid', Auth::id())
            ->selectRaw('category_finances_uuid, SUM(price) as total')
            ->groupBy('category_finances_uuid')
            ->get()
            ->map(function ($item) {
                return [
                    'category' => $item->category_finance->name_category_finances ?? 'Unknown',
                    'total' => $item->total
                ];
            })
            ->toArray();
    }

    public function getWeeklyTransactions(string $startDate, string $endDate)
    {
        return Finance::where('users_uuid', Auth::id())
            ->whereBetween('purchase_date', [$startDate, $endDate])
            ->get();
    }

    private function formatPriceForStorage(string $price): string
    {
        // Remove currency prefix and handle both Indonesian and international formats
        $cleaned = str_replace(['Rp. ', 'Rp ', 'IDR ', '$'], '', $price);
        $cleaned = trim($cleaned);
        
        // Indonesian format detection:
        // - If comma has 1-2 digits after it, it's decimal (123.456,50)
        // - If comma has 3+ digits after it, it's likely thousand separator error (699,115)
        if (strpos($cleaned, ',') !== false) {
            $parts = explode(',', $cleaned);
            $afterComma = $parts[1] ?? '';
            
            if (strlen($afterComma) <= 2) {
                // Decimal separator: 123.456,50 -> 123456
                $integerPart = str_replace('.', '', $parts[0]);
                $cleaned = $integerPart;
            } else {
                // Likely thousand separator error: 699,115 -> 699115
                $cleaned = str_replace(['.', ','], '', $cleaned);
            }
        } else {
            // No comma, remove dots (thousand separators): 123.456 -> 123456
            $cleaned = str_replace('.', '', $cleaned);
        }
        
        // Return clean integer string
        return preg_replace('/[^0-9]/', '', $cleaned);
    }
}