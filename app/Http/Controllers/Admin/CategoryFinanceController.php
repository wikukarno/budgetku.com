<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\AbstractCategoryController;
use App\Http\Requests\CategoryFinanceRequest;
use App\Models\CategoryFinance;
use App\Services\CategoryFinanceService;

class CategoryFinanceController extends AbstractCategoryController
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
        return 'v2.admin.category.expense.index';
    }

    protected function getCacheKeyPrefix(): string
    {
        return 'admin_categories_finance';
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
    public function store(CategoryFinanceRequest $request)
    {
        return $this->handleStore($request->validated(), $request->uuid);
    }

    // Other CRUD methods (show, destroy) are handled by AbstractCategoryController
}
