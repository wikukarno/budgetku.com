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
            $id = $validated['uuid'] ?? null;

            // Cek apakah data lama ada (update) atau baru (create)
            $category = $id
                ? $this->categoryIncomeRepository->find($id)
                : new \App\Models\CategoryIncome();

            // Set data
            $category->users_uuid = Auth::id();
            $category->name_category_Incomes = $validated['name_category_incomes'];
            if (!empty($validated['name_category_incomes_pgp'])) {
                $category->name_category_incomes_pgp = $validated['name_category_incomes_pgp'];
                // Tag content with user's current key version for lazy re-encryption later
                if (Auth::user() && isset(Auth::user()->key_version)) {
                    $category->content_key_version = Auth::user()->key_version;
                } else {
                    $category->content_key_version = 1;
                }
            }

            $isNew = !$category->exists;
            $wasChanged = $category->isDirty(); // Cek apakah ada perubahan

            // Simpan data
            $category->save();

            Cache::forget('user_categories_income_' . Auth::id());

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
