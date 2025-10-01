<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\AbstractCategoryController;
use App\Http\Requests\StoreCategoryIncomeRequest;
use App\Services\CategoryIncomeService;
use App\Models\CategoryIncome;

class UserCategoryIncomeController extends AbstractCategoryController
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
        return 'v2.user.category.income.index';
    }

    protected function getCacheKeyPrefix(): string
    {
        return 'user_categories_income';
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
    public function store(StoreCategoryIncomeRequest $request)
    {
        return $this->handleStore($request->validated(), $request->uuid);
    }

    // Other CRUD methods (show, destroy) are handled by AbstractCategoryController
}
