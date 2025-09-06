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

            // Set data
            $category->users_uuid = Auth::id();
            $category->name_category_finances = $validated['name_category_finances'];
            if (!empty($validated['name_category_finances_pgp'])) {
                $category->name_category_finances_pgp = $validated['name_category_finances_pgp'];
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

            Cache::forget('user_categories_finance_' . Auth::id());

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
