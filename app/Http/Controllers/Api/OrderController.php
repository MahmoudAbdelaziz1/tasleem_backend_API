<?php
// app/Http/Controllers/Api/OrderController.php

namespace App\Http\Controllers\Api;

use App\Models\Order;
use App\Http\Resources\OrderResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Api\LogController;  

class OrderController extends BaseController
{
    public function index(Request $request)
    {
        $query = Order::with(['user', 'product', 'payment']);

        if ($request->has('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $orders = $query->paginate($request->get('per_page', 15));

    
        LogController::addLog(
            userId: auth()->id(),
            actionType: 'VIEW',
            actionName: 'view_orders',
            module: 'orders',
            entityId: null,
            oldData: null,
            newData: ['filters' => $request->only(['user_id', 'status'])],
            ipAddress: $request->ip(),
            userAgent: $request->userAgent(),
            status: 'success',
            message: 'User viewed orders list'
        );

        return $this->sendPaginated(
            $orders,
            OrderResource::collection($orders),
            'Orders retrieved successfully'
        );
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
            'unit_price' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
           
            LogController::addLog(
                userId: auth()->id() ?? $request->user_id,
                actionType: 'ERROR',
                actionName: 'order_create_failed',
                module: 'orders',
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

        $order = Order::create([
            'user_id' => $request->user_id,
            'product_id' => $request->product_id,
            'quantity' => $request->quantity,
            'unit_price' => $request->unit_price,
            'total_price' => $request->quantity * $request->unit_price,
            'status' => 'pending',
        ]);

     
        $product = $order->product;
        $product->increment('pay_count', $request->quantity);
        $product->decrement('quantity', $request->quantity);

        
        LogController::addLog(
            userId: $order->user_id,
            actionType: 'CREATE',
            actionName: 'order_create',
            module: 'orders',
            entityId: $order->id,
            oldData: null,
            newData: $order->toArray(),
            ipAddress: $request->ip(),
            userAgent: $request->userAgent(),
            status: 'success',
            message: 'Order created: #' . $order->id . ' for product: ' . ($product->name ?? 'Unknown')
        );

        return $this->sendResponse(
            new OrderResource($order->load(['user', 'product'])),
            'Order created successfully',
            201
        );
    }

    public function show($id)
    {
        $order = Order::with(['user', 'product', 'payment'])->find($id);

        if (!$order) {
           
            LogController::addLog(
                userId: auth()->id(),
                actionType: 'ERROR',
                actionName: 'order_not_found',
                module: 'orders',
                entityId: $id,
                oldData: null,
                newData: null,
                ipAddress: request()->ip(),
                userAgent: request()->userAgent(),
                status: 'failed',
                message: 'Attempted to view non-existent order #' . $id,
                errorCode: 404
            );
            return $this->sendError('Order not found');
        }

    
        LogController::addLog(
            userId: auth()->id(),
            actionType: 'VIEW',
            actionName: 'view_order_details',
            module: 'orders',
            entityId: $order->id,
            oldData: null,
            newData: null,
            ipAddress: request()->ip(),
            userAgent: request()->userAgent(),
            status: 'success',
            message: 'User viewed order #' . $order->id
        );

        return $this->sendResponse(
            new OrderResource($order),
            'Order retrieved successfully'
        );
    }

    public function update(Request $request, $id)
    {
        $order = Order::find($id);

        if (!$order) {
            LogController::addLog(
                userId: auth()->id(),
                actionType: 'ERROR',
                actionName: 'order_update_failed',
                module: 'orders',
                entityId: $id,
                oldData: null,
                newData: null,
                ipAddress: $request->ip(),
                userAgent: $request->userAgent(),
                status: 'failed',
                message: 'Attempted to update non-existent order #' . $id,
                errorCode: 404
            );
            return $this->sendError('Order not found');
        }

        $validator = Validator::make($request->all(), [
            'status' => 'sometimes|in:pending,confirmed,shipped,delivered,cancelled,returned',
            'quantity' => 'sometimes|integer|min:1',
            'unit_price' => 'sometimes|numeric|min:0',
        ]);

        if ($validator->fails()) {
            LogController::addLog(
                userId: auth()->id(),
                actionType: 'ERROR',
                actionName: 'order_update_validation_failed',
                module: 'orders',
                entityId: $order->id,
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

        $oldData = $order->toArray();
        
        $order->update($request->only(['status', 'quantity', 'unit_price']));
        
        if ($request->has('quantity') || $request->has('unit_price')) {
            $order->total_price = $order->quantity * $order->unit_price;
            $order->save();
        }

     
        LogController::addLog(
            userId: auth()->id(),
            actionType: 'UPDATE',
            actionName: 'order_update',
            module: 'orders',
            entityId: $order->id,
            oldData: $oldData,
            newData: $order->fresh()->toArray(),
            ipAddress: $request->ip(),
            userAgent: $request->userAgent(),
            status: 'success',
            message: 'Order #' . $order->id . ' updated'
        );

        return $this->sendResponse(
            new OrderResource($order),
            'Order updated successfully'
        );
    }

    public function destroy($id)
    {
        $order = Order::find($id);

        if (!$order) {
            LogController::addLog(
                userId: auth()->id(),
                actionType: 'ERROR',
                actionName: 'order_delete_failed',
                module: 'orders',
                entityId: $id,
                oldData: null,
                newData: null,
                ipAddress: request()->ip(),
                userAgent: request()->userAgent(),
                status: 'failed',
                message: 'Attempted to delete non-existent order #' . $id,
                errorCode: 404
            );
            return $this->sendError('Order not found');
        }

        if ($order->status !== 'pending') {
            LogController::addLog(
                userId: auth()->id(),
                actionType: 'ERROR',
                actionName: 'order_delete_failed',
                module: 'orders',
                entityId: $order->id,
                oldData: null,
                newData: ['status' => $order->status],
                ipAddress: request()->ip(),
                userAgent: request()->userAgent(),
                status: 'failed',
                message: 'Cannot delete order #' . $order->id . ' with status: ' . $order->status,
                errorCode: 400
            );
            return $this->sendError('Cannot delete order that is not pending');
        }

        $oldData = $order->toArray();
        $order->delete();

        
        LogController::addLog(
            userId: auth()->id(),
            actionType: 'DELETE',
            actionName: 'order_delete',
            module: 'orders',
            entityId: $id,
            oldData: $oldData,
            newData: null,
            ipAddress: request()->ip(),
            userAgent: request()->userAgent(),
            status: 'success',
            message: 'Order #' . $id . ' deleted successfully'
        );

        return $this->sendResponse(null, 'Order deleted successfully');
    }
}