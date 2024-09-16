<?php

namespace App\Services;

use App\Repositories\CategoryFinanceRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

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
            $id = $validated['id'] ?? null;
            $data = $this->categoryFinanceRepository->updateOrCreate(
                ['id' => $id],
                [
                    'users_id' => Auth::id(),
                    'name_category_finances' => $validated['name_category_finances'],
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