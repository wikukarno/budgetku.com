<?php

namespace App\Services;

use App\Repositories\CategoryIncomeRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class CategoryIncomeService
{
    protected $categoryIncomeRepository;

    public function __construct(CategoryIncomeRepositoryInterface $categoryIncomeRepository)
    {
        $this->categoryIncomeRepository = $categoryIncomeRepository;
    }

    public function updateOrCreateCategoryIncome(array $validated)
    {
        DB::beginTransaction();
        try {
            $id = $validated['uuid'] ?? $validated['id'] ?? null;

            // Cek apakah data lama ada (update) atau baru (create)
            $category = $id
                ? $this->categoryIncomeRepository->find($id)
                : new \App\Models\CategoryIncome();

            // Set data - now all users use UUID system
            $category->users_uuid = Auth::id();
            $category->name_category_incomes = $validated['name_category_incomes'];

            $isNew = !$category->exists;
            $wasChanged = $category->isDirty(); // Cek apakah ada perubahan

            // Simpan data
            $category->save();

            // Clear cache for both admin and customer
            Cache::forget('user_categories_income_' . Auth::id());
            Cache::forget('admin_categories_income_' . Auth::id());

            DB::commit();

            if ($isNew) {
                return ['status' => 'success', 'message' => 'Data added successfully'];
            }

            if ($wasChanged) {
                return ['status' => 'success', 'message' => 'Data updated successfully'];
            }

            return ['status' => 'error', 'message' => 'No changes have been made'];
        } catch (\Exception $e) {
            DB::rollBack();
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }
}