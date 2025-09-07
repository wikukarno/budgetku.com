<?php

namespace App\Http\Controllers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

abstract class AbstractCategoryController extends Controller
{
    /**
     * The service instance for CRUD operations
     */
    protected $service;

    /**
     * Get the model class for this category type
     */
    abstract protected function getModelClass(): string;

    /**
     * Get the service instance
     */
    abstract protected function getService();

    /**
     * Get the service method name for updateOrCreate operation
     */
    abstract protected function getServiceUpdateMethod(): string;

    /**
     * Get the view name for index page
     */
    abstract protected function getIndexViewName(): string;

    /**
     * Get the cache key prefix for this category type
     */
    abstract protected function getCacheKeyPrefix(): string;

    /**
     * Get the category name field (e.g., 'name_category_incomes', 'name_category_finances')
     */
    abstract protected function getCategoryNameField(): string;

    /**
     * Get JavaScript function names for actions
     */
    abstract protected function getJavaScriptFunctions(): array;

    /**
     * Display a listing of the resource with DataTables support
     */
    public function index()
    {
        if (request()->ajax()) {
            $modelClass = $this->getModelClass();
            $query = $modelClass::where('users_uuid', Auth::id())->orderBy('created_at', 'DESC');

            return datatables()->of($query)
                ->addIndexColumn()
                ->editColumn('created_at', function ($item) {
                    return $item->created_at->isoFormat('D MMMM Y');
                })
                ->editColumn('updated_at', function ($item) {
                    return $item->updated_at->isoFormat('D MMMM Y');
                })
                ->editColumn('action', function ($item) {
                    $functions = $this->getJavaScriptFunctions();
                    return $this->generateActionButtons($item->uuid, $functions);
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        
        return view($this->getIndexViewName());
    }

    /**
     * Generate action buttons HTML
     */
    protected function generateActionButtons(string $uuid, array $functions): string
    {
        return '
            <a href="javascript:void(0)" class="btn btn-sm btn-warning text-white" onclick="' . $functions['update'] . '(\'' . $uuid . '\')">
                Edit
            </a>
            
            <a href="javascript:void(0)" class="btn btn-sm btn-danger text-white" onclick="' . $functions['delete'] . '(\'' . $uuid . '\')">
                Delete
            </a>
        ';
    }

    /**
     * Store a newly created resource in storage or update existing one
     * This method is designed to be called from child controllers with validated data
     */
    protected function handleStore(array $validated, ?string $uuid = null)
    {
        try {
            $modelClass = $this->getModelClass();
            $category = $uuid ? $modelClass::find($uuid) : null;

            if ($category) {
                $this->authorize('updateOrCreate', $category);
            } else {
                // Check if user has permission to create new categories
                $this->authorize('create', $modelClass);
            }
            
            $service = $this->getService();
            $method = $this->getServiceUpdateMethod();
            $data = $service->$method($validated);
            return response()->json($data);
            
        } catch (\Exception $e) {
            Log::error('Category store error: ' . $e->getMessage(), [
                'user_id' => Auth::id(),
                'validated_data' => $validated,
                'uuid' => $uuid
            ]);
            
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to save category. Please try again.'
            ], 500);
        }
    }

    /**
     * Display the specified resource
     */
    public function show(Request $request)
    {
        try {
            $modelClass = $this->getModelClass();
            $data = $modelClass::where('uuid', $request->uuid)
                ->where('users_uuid', Auth::id())
                ->firstOrFail();

            $this->authorize('view', $data);

            return response()->json($data);
            
        } catch (\Exception $e) {
            Log::error('Category show error: ' . $e->getMessage(), [
                'user_id' => Auth::id(),
                'uuid' => $request->uuid
            ]);
            
            return response()->json([
                'status' => 'error',
                'message' => 'Category not found or access denied.'
            ], 404);
        }
    }

    /**
     * Remove the specified resource from storage
     */
    public function destroy(Request $request)
    {
        try {
            $modelClass = $this->getModelClass();
            $data = $modelClass::where('uuid', $request->uuid)
                ->where('users_uuid', Auth::id())
                ->firstOrFail();

            $this->authorize('delete', $data);
            $data->delete();
            
            // Clear cache
            $this->clearCache();
            
            return response()->json([
                'status' => 'success',
                'message' => 'Category deleted successfully'
            ]);
            
        } catch (\Exception $e) {
            Log::error('Category delete error: ' . $e->getMessage(), [
                'user_id' => Auth::id(),
                'uuid' => $request->uuid
            ]);
            
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to delete category. Please try again.'
            ], 500);
        }
    }

    /**
     * Clear cache for this category type
     */
    protected function clearCache(): void
    {
        $prefix = $this->getCacheKeyPrefix();
        Cache::forget($prefix . '_' . Auth::id());
    }
}