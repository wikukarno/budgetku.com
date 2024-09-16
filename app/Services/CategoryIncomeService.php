<?php

namespace App\Services;

use App\Repositories\CategoryIncomeRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

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
            $id = $validated['id'] ?? null;
            $data = $this->categoryIncomeRepository->updateOrCreate(
                ['id' => $id],
                [
                    'users_id' => Auth::id(),
                    'name_category_incomes' => $validated['name_category_incomes'],
                ]
            );

            if ($data->wasRecentlyCreated) {
                DB::commit();
                return ['status' => 'success', 'message' => 'Data berhasil ditambahkan'];
            } elseif ($data->wasChanged()) {
                DB::commit();
                return ['status' => 'success', 'message' => 'Data berhasil diubah'];
            } else {
                DB::rollBack();
                return ['status' => 'error', 'message' => 'Data tidak ada yang berubah'];
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }
}