<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Traits\ApiResponseTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

abstract class AbstractCategoryController extends Controller
{
    use ApiResponseTrait;
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
            
            // Optimize query with proper indexing and selective columns
            $query = $modelClass::select([
                    'uuid',
                    $this->getCategoryNameField(),
                    'created_at',
                    'updated_at'
                ])
                ->where('users_uuid', Auth::id())
                ->orderBy('created_at', 'DESC');

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
            $serviceResponse = $service->$method($validated);
            
            return $this->transformServiceResponse($serviceResponse);
            
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            Log::warning('Category authorization error: ' . $e->getMessage(), [
                'user_id' => Auth::id(),
                'uuid' => $uuid,
                'action' => $uuid ? 'update' : 'create'
            ]);
            
            return $this->forbiddenResponse('You are not authorized to perform this action.');
            
        } catch (\Exception $e) {
            Log::error('Category store error: ' . $e->getMessage(), [
                'user_id' => Auth::id(),
                'validated_data' => $validated,
                'uuid' => $uuid
            ]);
            
            return $this->serverErrorResponse('Failed to save category. Please try again.');
        }
    }

    /**
     * Display the specified resource
     */
    public function show(Request $request)
    {
        try {
            $cacheKey = $this->getCacheKeyPrefix() . '_show_' . $request->uuid . '_' . Auth::id();
            
            // Try to get from cache first (5 minutes cache)
            $data = Cache::remember($cacheKey, 300, function () use ($request) {
                $modelClass = $this->getModelClass();
                return $modelClass::select([
                        'uuid', 
                        'users_uuid',  // IMPORTANT: Include for authorization
                        $this->getCategoryNameField(), 
                        'created_at', 
                        'updated_at'
                    ])
                    ->where('uuid', $request->uuid)
                    ->where('users_uuid', Auth::id())
                    ->firstOrFail();
            });

            $this->authorize('view', $data);

            return $this->successResponse($data, 'Category retrieved successfully.');
            
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            Log::info('Category not found: ' . $e->getMessage(), [
                'user_id' => Auth::id(),
                'uuid' => $request->uuid
            ]);
            
            return $this->notFoundResponse('Category not found.');
            
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            Log::warning('Category view authorization error: ' . $e->getMessage(), [
                'user_id' => Auth::id(),
                'uuid' => $request->uuid
            ]);
            
            return $this->forbiddenResponse('You are not authorized to view this category.');
            
        } catch (\Exception $e) {
            Log::error('Category show error: ' . $e->getMessage(), [
                'user_id' => Auth::id(),
                'uuid' => $request->uuid
            ]);
            
            return $this->serverErrorResponse('Failed to retrieve category. Please try again.');
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
            $uuid = $data->uuid;
            $data->delete();
            
            // Clear cache with specific UUID
            $this->clearCache($uuid);
            
            return $this->successResponse(null, 'Category deleted successfully.');
            
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            Log::info('Category not found for deletion: ' . $e->getMessage(), [
                'user_id' => Auth::id(),
                'uuid' => $request->uuid
            ]);
            
            return $this->notFoundResponse('Category not found.');
            
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            Log::warning('Category delete authorization error: ' . $e->getMessage(), [
                'user_id' => Auth::id(),
                'uuid' => $request->uuid
            ]);
            
            return $this->forbiddenResponse('You are not authorized to delete this category.');
            
        } catch (\Exception $e) {
            Log::error('Category delete error: ' . $e->getMessage(), [
                'user_id' => Auth::id(),
                'uuid' => $request->uuid
            ]);
            
            return $this->serverErrorResponse('Failed to delete category. Please try again.');
        }
    }

    /**
     * Clear cache for this category type
     */
    protected function clearCache(?string $uuid = null): void
    {
        $prefix = $this->getCacheKeyPrefix();
        $userId = Auth::id();
        
        // Clear main cache
        Cache::forget($prefix . '_' . $userId);
        
        // Clear specific item cache if uuid provided
        if ($uuid) {
            Cache::forget($prefix . '_show_' . $uuid . '_' . $userId);
        }
        
        // Clear related caches (both admin and user variants)
        $basePrefix = str_replace(['admin_', 'user_'], '', $prefix);
        Cache::forget('admin_' . $basePrefix . '_' . $userId);
        Cache::forget('user_' . $basePrefix . '_' . $userId);
    }

    /**
     * Warm up cache with frequently accessed data
     */
    protected function warmUpCache(): void
    {
        $modelClass = $this->getModelClass();
        $cacheKey = $this->getCacheKeyPrefix() . '_list_' . Auth::id();
        
        // Cache most recent categories for quick access
        Cache::remember($cacheKey, 600, function () use ($modelClass) {
            return $modelClass::select([
                    'uuid',
                    $this->getCategoryNameField(),
                    'created_at'
                ])
                ->where('users_uuid', Auth::id())
                ->orderBy('created_at', 'DESC')
                ->limit(20)
                ->get();
        });
    }
}