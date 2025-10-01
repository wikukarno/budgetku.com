<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\AbstractCategoryController;
use App\Http\Requests\StoreCategoryFinanceRequest;
use App\Models\CategoryFinance;
use App\Services\CategoryFinanceService;

class UserCategoryFinancesController extends AbstractCategoryController
{

    protected $categoryFinanceService;

    public function __construct(CategoryFinanceService $categoryFinanceService)
    {
        $this->categoryFinanceService = $categoryFinanceService;
    }

    protected function getModelClass(): string
    {
        return CategoryFinance::class;
    }

    protected function getService()
    {
        return $this->categoryFinanceService;
    }

    protected function getServiceUpdateMethod(): string
    {
        return 'updateOrCreateCategoryFinance';
    }

    protected function getIndexViewName(): string
    {
        return 'v2.user.category.expense.index';
    }

    protected function getCacheKeyPrefix(): string
    {
        return 'user_categories_finance';
    }

    protected function getCategoryNameField(): string
    {
        return 'name_category_finances';
    }

    protected function getJavaScriptFunctions(): array
    {
        return [
            'update' => 'updateKategoriFinance',
            'delete' => 'deleteKategoriFinance'
        ];
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCategoryFinanceRequest $request)
    {
        return $this->handleStore($request->validated(), $request->uuid);
    }

    // Other CRUD methods (show, destroy) are handled by AbstractCategoryController
}
