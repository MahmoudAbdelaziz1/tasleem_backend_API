<?php
// app/Http/Controllers/Api/CategoryController.php

namespace App\Http\Controllers\Api;

use App\Models\Category;
use App\Http\Resources\CategoryResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Api\LogController;  

class CategoryController extends BaseController
{
    public function index(Request $request)
    {
        $query = Category::withCount('products');

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $categories = $query->paginate($request->get('per_page', 15));

    
        LogController::addLog(
            userId: auth()->id(),
            actionType: 'VIEW',
            actionName: 'view_categories',
            module: 'categories',
            entityId: null,
            oldData: null,
            newData: ['filters' => $request->only(['status'])],
            ipAddress: $request->ip(),
            userAgent: $request->userAgent(),
            status: 'success',
            message: 'User viewed categories list'
        );

        return $this->sendPaginated(
            $categories,
            CategoryResource::collection($categories),
            'Categories retrieved successfully'
        );
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|regex:/^[^<>{}]*$/|unique:categories',
            'photo' => 'nullable|image|max:2048',
            'status' => 'required|in:1,0',
        ]);

        if ($validator->fails()) {
            
            LogController::addLog(
                userId: auth()->id(),
                actionType: 'ERROR',
                actionName: 'category_create_failed',
                module: 'categories',
                entityId: null,
                oldData: null,
                newData: $request->all(),
                ipAddress: $request->ip(),
                userAgent: $request->userAgent(),
                status: 'failed',
                message: 'Validation failed: ' . json_encode($validator->errors()),
                errorCode: 422
            );
            return $this->sendError('Validation Error', $validator->errors(), 422);
        }

        $data = $request->all();
        
        if ($request->hasFile('photo')) {
            $data['photo'] = $request->file('photo')->store('categories', 'public');
        }

        $category = Category::create($data);

       
        LogController::addLog(
            userId: auth()->id(),
            actionType: 'CREATE',
            actionName: 'category_create',
            module: 'categories',
            entityId: $category->category_id,
            oldData: null,
            newData: $category->toArray(),
            ipAddress: $request->ip(),
            userAgent: $request->userAgent(),
            status: 'success',
            message: 'Category created: ' . $category->name
        );

        return $this->sendResponse(
            new CategoryResource($category),
            'Category created successfully',
            201
        );
    }

    public function show($id)
    {
        $category = Category::with('products')->find($id);

        if (!$category) {
          
            LogController::addLog(
                userId: auth()->id(),
                actionType: 'ERROR',
                actionName: 'category_not_found',
                module: 'categories',
                entityId: $id,
                oldData: null,
                newData: null,
                ipAddress: request()->ip(),
                userAgent: request()->userAgent(),
                status: 'failed',
                message: 'Attempted to view non-existent category #' . $id,
                errorCode: 404
            );
            return $this->sendError('Category not found');
        }

     
        LogController::addLog(
            userId: auth()->id(),
            actionType: 'VIEW',
            actionName: 'view_category_details',
            module: 'categories',
            entityId: $category->category_id,
            oldData: null,
            newData: null,
            ipAddress: request()->ip(),
            userAgent: request()->userAgent(),
            status: 'success',
            message: 'User viewed category: ' . $category->name
        );

        return $this->sendResponse(
            new CategoryResource($category),
            'Category retrieved successfully'
        );
    }

    public function update(Request $request, $id)
    {
        $category = Category::find($id);

        if (!$category) {
           
            LogController::addLog(
                userId: auth()->id(),
                actionType: 'ERROR',
                actionName: 'category_update_failed',
                module: 'categories',
                entityId: $id,
                oldData: null,
                newData: null,
                ipAddress: $request->ip(),
                userAgent: $request->userAgent(),
                status: 'failed',
                message: 'Attempted to update non-existent category #' . $id,
                errorCode: 404
            );
            return $this->sendError('Category not found');
        }

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|regex:/^[^<>{}]*$/|string|max:255|unique:categories,name,' . $id . ',category_id',
            'photo' => 'nullable|image|max:2048',
            'status' => 'sometimes|in:1,0',
        ]);

        if ($validator->fails()) {
            
            LogController::addLog(
                userId: auth()->id(),
                actionType: 'ERROR',
                actionName: 'category_update_validation_failed',
                module: 'categories',
                entityId: $category->category_id,
                oldData: null,
                newData: $request->all(),
                ipAddress: $request->ip(),
                userAgent: $request->userAgent(),
                status: 'failed',
                message: 'Validation failed: ' . json_encode($validator->errors()),
                errorCode: 422
            );
            return $this->sendError('Validation Error', $validator->errors(), 422);
        }

        $oldData = $category->toArray();
        $data = $request->all();
        
        if ($request->hasFile('photo')) {
            $data['photo'] = $request->file('photo')->store('categories', 'public');
        }

        $category->update($data);

       
        LogController::addLog(
            userId: auth()->id(),
            actionType: 'UPDATE',
            actionName: 'category_update',
            module: 'categories',
            entityId: $category->category_id,
            oldData: $oldData,
            newData: $category->fresh()->toArray(),
            ipAddress: $request->ip(),
            userAgent: $request->userAgent(),
            status: 'success',
            message: 'Category updated: ' . $category->name
        );

        return $this->sendResponse(
            new CategoryResource($category),
            'Category updated successfully'
        );
    }

    public function destroy($id)
    {
        $category = Category::find($id);

        if (!$category) {
       
            LogController::addLog(
                userId: auth()->id(),
                actionType: 'ERROR',
                actionName: 'category_delete_failed',
                module: 'categories',
                entityId: $id,
                oldData: null,
                newData: null,
                ipAddress: request()->ip(),
                userAgent: request()->userAgent(),
                status: 'failed',
                message: 'Attempted to delete non-existent category #' . $id,
                errorCode: 404
            );
            return $this->sendError('Category not found');
        }

        if ($category->products()->count() > 0) {
            
            LogController::addLog(
                userId: auth()->id(),
                actionType: 'ERROR',
                actionName: 'category_delete_failed',
                module: 'categories',
                entityId: $category->category_id,
                oldData: null,
                newData: ['products_count' => $category->products()->count()],
                ipAddress: request()->ip(),
                userAgent: request()->userAgent(),
                status: 'failed',
                message: 'Cannot delete category #' . $category->category_id . ' - has ' . $category->products()->count() . ' products',
                errorCode: 400
            );
            return $this->sendError('Cannot delete category with associated products');
        }

        $oldData = $category->toArray();
        $categoryName = $category->name;
        $category->delete();

     
        LogController::addLog(
            userId: auth()->id(),
            actionType: 'DELETE',
            actionName: 'category_delete',
            module: 'categories',
            entityId: $id,
            oldData: $oldData,
            newData: null,
            ipAddress: request()->ip(),
            userAgent: request()->userAgent(),
            status: 'success',
            message: 'Category deleted: ' . $categoryName
        );

        return $this->sendResponse(null, 'Category deleted successfully');
    }
}