<?php
// app/Http/Controllers/Api/ProductImageController.php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ProductImage;
use App\Models\Product;
use App\Http\Resources\ProductImageResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use App\Models\Log;

class ProductImageController extends BaseController
{
    /**
     * Display all images for a product
     */
    public function index(Request $request, $productId)
    {
        $product = Product::find($productId);
        
        if (!$product) {
            return $this->sendError('Product not found');
        }

        $images = ProductImage::where('product_id', $productId)
            ->orderBy('created_at', 'desc')
            ->get();

        return $this->sendResponse(
            ProductImageResource::collection($images),
            'Product images retrieved successfully'
        );
    }

    /**
     * Upload new images for a product
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'product_id' => 'required|exists:products,id',
            'images' => 'required|array|min:1|max:5',
            'images.*' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'alt_text' => 'nullable|regex:/^[^<>{}]*$/|string|max:255',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error', $validator->errors(), 422);
        }

        $productId = $request->product_id;
        $uploadedImages = [];

        foreach ($request->file('images') as $image) {
            // Store image
            $path = $image->store('products/' . $productId, 'public');

            // Create image record
            $productImage = ProductImage::create([
                'product_id' => $productId,
                'image_url' => $path,
                'alt_text' => $request->alt_text ?? $image->getClientOriginalName(),
            ]);

            $uploadedImages[] = $productImage;
        }

        // Log the action
        LogController::addLog(
            auth()->id(),
            'CREATE',
            'upload_product_images',
            'products',
            'product_image',
            $productId,
            null,
            ['uploaded_count' => count($uploadedImages)],
            $request->ip(),
            $request->userAgent(),
            'success',
            count($uploadedImages) . ' images uploaded for product #' . $productId
        );

        return $this->sendResponse(
            ProductImageResource::collection($uploadedImages),
            count($uploadedImages) . ' images uploaded successfully',
            201
        );
    }

    /**
     * Upload single image
     */
    public function uploadSingle(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'product_id' => 'required|exists:products,id',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'alt_text' => 'nullable|regex:/^[^<>{}]*$/|string|max:255',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error', $validator->errors(), 422);
        }

        $productId = $request->product_id;

        // Store image
        $path = $request->file('image')->store('products/' . $productId, 'public');

        // Create image record
        $productImage = ProductImage::create([
            'product_id' => $productId,
            'image_url' => $path,
            'alt_text' => $request->alt_text ?? $request->file('image')->getClientOriginalName(),
        ]);

        // Log the action
        LogController::addLog(
            auth()->id(),
            'CREATE',
            'upload_product_image',
            'products',
            'product_image',
            $productImage->image_id,
            null,
            $productImage->toArray(),
            $request->ip(),
            $request->userAgent(),
            'success',
            'Image uploaded for product #' . $productId
        );

        return $this->sendResponse(
            new ProductImageResource($productImage),
            'Image uploaded successfully',
            201
        );
    }

    /**
     * Display specific image
     */
    public function show($id)
    {
        $image = ProductImage::find($id);

        if (!$image) {
            return $this->sendError('Image not found');
        }

        return $this->sendResponse(
            new ProductImageResource($image),
            'Image retrieved successfully'
        );
    }

    /**
     * Update image details
     */
    public function update(Request $request, $id)
    {
        $image = ProductImage::find($id);

        if (!$image) {
            return $this->sendError('Image not found');
        }

        $validator = Validator::make($request->all(), [
            'alt_text' => 'nullable|regex:/^[^<>{}]*$/|string|max:255',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error', $validator->errors(), 422);
        }

        $oldData = $image->toArray();
        $image->update($request->only(['alt_text']));

        // Log the action
        LogController::addLog(
            auth()->id(),
            'UPDATE',
            'update_product_image',
            'products',
            'product_image',
            $image->image_id,
            $oldData,
            $image->toArray(),
            $request->ip(),
            $request->userAgent(),
            'success',
            'Image #' . $id . ' updated'
        );

        return $this->sendResponse(
            new ProductImageResource($image),
            'Image updated successfully'
        );
    }

    /**
     * Delete image
     */
    public function destroy(Request $request, $id)
    {
        $image = ProductImage::find($id);

        if (!$image) {
            return $this->sendError('Image not found');
        }

        $oldData = $image->toArray();

        // Delete file from storage
        if ($image->image_url && Storage::disk('public')->exists($image->image_url)) {
            Storage::disk('public')->delete($image->image_url);
        }

        $image->delete();

        // Log the action
        LogController::addLog(
            auth()->id(),
            'DELETE',
            'delete_product_image',
            'products',
            'product_image',
            $id,
            $oldData,
            null,
            $request->ip(),
            $request->userAgent(),
            'success',
            'Image #' . $id . ' deleted'
        );

        return $this->sendResponse(null, 'Image deleted successfully');
    }

    /**
     * Delete multiple images
     */
    public function destroyMultiple(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'image_ids' => 'required|array|min:1',
            'image_ids.*' => 'required|integer|exists:product_images,image_id',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error', $validator->errors(), 422);
        }

        $deletedCount = 0;
        $productId = null;

        foreach ($request->image_ids as $imageId) {
            $image = ProductImage::find($imageId);
            if ($image) {
                $productId = $image->product_id;
                
                // Delete file
                if ($image->image_url && Storage::disk('public')->exists($image->image_url)) {
                    Storage::disk('public')->delete($image->image_url);
                }
                
                $image->delete();
                $deletedCount++;
            }
        }

        // Log the action
        LogController::addLog(
            auth()->id(),
            'DELETE',
            'delete_multiple_images',
            'products',
            'product_image',
            $productId,
            null,
            ['deleted_count' => $deletedCount, 'image_ids' => $request->image_ids],
            $request->ip(),
            $request->userAgent(),
            'success',
            $deletedCount . ' images deleted'
        );

        return $this->sendResponse(
            ['deleted_count' => $deletedCount],
            $deletedCount . ' images deleted successfully'
        );
    }
}