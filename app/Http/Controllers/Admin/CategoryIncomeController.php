<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\AbstractCategoryController;
use App\Http\Requests\CategoryIncomeRequest;
use App\Models\CategoryIncome;
use App\Services\CategoryIncomeService;

class CategoryIncomeController extends AbstractCategoryController
{
    protected $categoryIncomeService;

    public function __construct(CategoryIncomeService $categoryIncomeService)
    {
        $this->categoryIncomeService = $categoryIncomeService;
    }

    protected function getModelClass(): string
    {
        return CategoryIncome::class;
    }

    protected function getService()
    {
        return $this->categoryIncomeService;
    }

    protected function getServiceUpdateMethod(): string
    {
        return 'updateOrCreateCategoryIncome';
    }

    protected function getIndexViewName(): string
    {
        return 'v2.admin.category.income.index';
    }

    protected function getCacheKeyPrefix(): string
    {
        return 'admin_categories_income';
    }

    protected function getCategoryNameField(): string
    {
        return 'name_category_incomes';
    }

    protected function getJavaScriptFunctions(): array
    {
        return [
            'update' => 'updateKategoriIncome',
            'delete' => 'deleteKategoriIncome'
        ];
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CategoryIncomeRequest $request)
    {
        return $this->handleStore($request->validated(), $request->uuid);
    }

    // Other CRUD methods (show, destroy) are handled by AbstractCategoryController
}
