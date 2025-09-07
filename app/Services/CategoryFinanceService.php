<?php

namespace App\Services;

use App\Repositories\CategoryFinanceRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class CategoryFinanceService
{
    protected $categoryFinanceRepository;

    public function __construct(CategoryFinanceRepositoryInterface $categoryFinanceRepository)
    {
        $this->categoryFinanceRepository = $categoryFinanceRepository;
    }

    public function updateOrCreateCategoryFinance(array $validated)
    {
        DB::beginTransaction();
        try {
            $id = $validated['uuid'] ?? null;

            // Cek apakah data lama ada (update) atau baru (create)
            $category = $id
                ? $this->categoryFinanceRepository->find((string)$id)
                : new \App\Models\CategoryFinance();

            // Set data - now all users use UUID system
            $category->users_uuid = Auth::id();
            $category->name_category_finances = $validated['name_category_finances'];

            $isNew = !$category->exists;
            $wasChanged = $category->isDirty(); // Cek apakah ada perubahan

            // Simpan data
            $category->save();

            // Clear cache more efficiently 
            $userId = Auth::id();
            Cache::forget('user_categories_finance_' . $userId);
            Cache::forget('admin_categories_finance_' . $userId);
            
            // Clear specific item cache if updating existing record
            if ($id) {
                Cache::forget('user_categories_finance_show_' . $id . '_' . $userId);
                Cache::forget('admin_categories_finance_show_' . $id . '_' . $userId);
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