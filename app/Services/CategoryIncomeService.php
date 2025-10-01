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

            // Clear cache more efficiently 
            $userId = Auth::id();
            Cache::forget('user_categories_income_' . $userId);
            Cache::forget('admin_categories_income_' . $userId);
            
            // Clear specific item cache if updating existing record
            if ($id) {
                Cache::forget('user_categories_income_show_' . $id . '_' . $userId);
                Cache::forget('admin_categories_income_show_' . $id . '_' . $userId);
            }

            DB::commit();

            if ($isNew) {
                return ['status' => 'success', 'message' => 'Category created successfully.'];
            }

            if ($wasChanged) {
                return ['status' => 'success', 'message' => 'Category updated successfully.'];
            }

            return ['status' => 'error', 'message' => 'No changes were made to the category.'];
        } catch (\Exception $e) {
            DB::rollBack();
            return ['status' => 'error', 'message' => 'Failed to save category. Please try again.'];
        }
    }
}