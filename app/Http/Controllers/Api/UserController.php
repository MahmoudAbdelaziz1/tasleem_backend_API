<?php
// app/Http/Controllers/Api/UserController.php

namespace App\Http\Controllers\Api;

use App\Models\User;
use App\Http\Resources\UserResource;
use App\Http\Resources\ProductResource;
use App\Http\Resources\OrderResource;
use App\Http\Resources\RentalResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends BaseController
{
    /**
     * Display a listing of users
     */
    public function index(Request $request)
    {
        $query = User::query();

        if ($request->has('role')) {
            $query->where('role', $request->role);
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('email', 'LIKE', "%{$search}%");
            });
        }

        $users = $query->paginate($request->get('per_page', 15));

        LogController::addLog(
            auth()->id(),
            'VIEW',
            'list_users',
            'users',
            null,
            null,
            null,
            $request->ip(),
            $request->userAgent(),
            'success',
            'Admin viewed users list'
        );

        return $this->sendPaginated(
            $users,
            UserResource::collection($users),
            'Users retrieved successfully'
        );
    }

    /**
     * Store a newly created user
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'      => 'required|string|regex:/^[^<>{}]*$/|max:255',
            'email'     => 'required|string|email|max:255|unique:users',
            'password'  => 'required|string|min:8',
            'phone'     => 'nullable|regex:/^[^<>{}]*$/|string|max:20',
            'address'   => 'nullable|string|regex:/^[^<>{}]*$/|max:255',
            'city'      => 'nullable|string|regex:/^[^<>{}]*$/|max:100',
            'post_code' => 'nullable|regex:/^[^<>{}]*$/|string|max:20',
            'role'      => 'sometimes|in:admin,user',
            'status'    => 'sometimes|in:1,0',
        ]);

        if ($validator->fails()) {
            LogController::addLog(
                auth()->id(),
                'CREATE',
                'create_user',
                'users',
                null,
                null,
                null,
                $request->ip(),
                $request->userAgent(),
                'failed',
                'Validation failed: ' . json_encode($validator->errors())
            );

            return $this->sendError('Validation Error', $validator->errors(), 422);
        }

        $user = User::create([
            'name'      => $request->name,
            'email'     => $request->email,
            'password'  => Hash::make($request->password),
            'phone'     => $request->phone,
            'address'   => $request->address,
            'city'      => $request->city,
            'post_code' => $request->post_code,
            'role'      => $request->role ?? 'user',
            'status'    => $request->status ?? '1',
        ]);

        LogController::addLog(
            auth()->id(),
            'CREATE',
            'create_user',
            'users',
            $user->id,
            null,
            $user->toArray(),
            $request->ip(),
            $request->userAgent(),
            'success',
            'User #' . $user->id . ' created: ' . $user->name
        );

        return $this->sendResponse(
            new UserResource($user),
            'User created successfully',
            201
        );
    }

    /**
     * Display the specified user
     */
    public function show(Request $request, $id)
    {
        $user = User::with(['products', 'rentals', 'orders'])->find($id);

        if (!$user) {
            LogController::addLog(
                auth()->id(),
                'VIEW',
                'view_user',
                'users',
                $id,
                null,
                null,
                $request->ip(),
                $request->userAgent(),
                'failed',
                'User #' . $id . ' not found'
            );

            return $this->sendError('User not found');
        }

        LogController::addLog(
            auth()->id(),
            'VIEW',
            'view_user',
            'users',
            $user->id,
            null,
            null,
            $request->ip(),
            $request->userAgent(),
            'success',
            'Viewed user #' . $user->id . ': ' . $user->name
        );

        return $this->sendResponse(
            new UserResource($user),
            'User retrieved successfully'
        );
    }

    /**
     * Update the specified user
     */
    public function update(Request $request, $id)
    {
        $user = User::find($id);

        if (!$user) {
            LogController::addLog(
                auth()->id(),
                'UPDATE',
                'update_user',
                'users',
                $id,
                null,
                null,
                $request->ip(),
                $request->userAgent(),
                'failed',
                'User #' . $id . ' not found for update'
            );

            return $this->sendError('User not found');
        }

        $validator = Validator::make($request->all(), [
            'name'      => 'sometimes|string|regex:/^[^<>{}]*$/|max:255',
            'email'     => 'sometimes|string|email|max:255|unique:users,email,' . $id,
            'password'  => 'sometimes|string|min:8',
            'phone'     => 'nullable|string|max:20',
            'address'   => 'nullable|string|regex:/^[^<>{}]*$/|max:255',
            'city'      => 'nullable|string|regex:/^[^<>{}]*$/|max:100',
            'post_code' => 'nullable|regex:/^[^<>{}]*$/|string|max:20',
            'role'      => 'sometimes|in:admin,user',
            'status'    => 'sometimes|in:1,0',
        ]);

        if ($validator->fails()) {
            LogController::addLog(
                auth()->id(),
                'UPDATE',
                'update_user',
                'users',
                $id,
                null,
                null,
                $request->ip(),
                $request->userAgent(),
                'failed',
                'Validation failed: ' . json_encode($validator->errors())
            );

            return $this->sendError('Validation Error', $validator->errors(), 422);
        }

        $oldData = $user->toArray();

        $data = $request->except('password');
        if ($request->has('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        LogController::addLog(
            auth()->id(),
            'UPDATE',
            'update_user',
            'users',
            $user->id,
            $oldData,
            $user->toArray(),
            $request->ip(),
            $request->userAgent(),
            'success',
            'User #' . $user->id . ' updated'
        );

        return $this->sendResponse(
            new UserResource($user),
            'User updated successfully'
        );
    }

    /**
     * Remove the specified user
     */
    public function destroy(Request $request, $id)
    {
        $user = User::find($id);

        if (!$user) {
            LogController::addLog(
                auth()->id(),
                'DELETE',
                'delete_user',
                'users',
                $id,
                null,
                null,
                $request->ip(),
                $request->userAgent(),
                'failed',
                'User #' . $id . ' not found for deletion'
            );

            return $this->sendError('User not found');
        }

        $oldData = $user->toArray();
        $user->delete();

        LogController::addLog(
            auth()->id(),
            'DELETE',
            'delete_user',
            'users',
            $id,
            $oldData,
            null,
            $request->ip(),
            $request->userAgent(),
            'success',
            'User #' . $id . ' deleted'
        );

        return $this->sendResponse(null, 'User deleted successfully');
    }

    /**
     * Get user's products
     */
    public function products(Request $request, $id)
    {
        $user = User::find($id);

        if (!$user) {
            return $this->sendError('User not found');
        }

        $products = $user->products()->paginate(15);

        LogController::addLog(
            auth()->id(),
            'VIEW',
            'view_user_products',
            'users',
            $id,
            null,
            null,
            $request->ip(),
            $request->userAgent(),
            'success',
            'Viewed products of user #' . $id
        );

        return $this->sendPaginated(
            $products,
            ProductResource::collection($products),
            'User products retrieved successfully'
        );
    }

    /**
     * Get user's orders
     */
    public function orders(Request $request, $id)
    {
        $user = User::find($id);

        if (!$user) {
            return $this->sendError('User not found');
        }

        $orders = $user->orders()->with('product')->paginate(15);

        LogController::addLog(
            auth()->id(),
            'VIEW',
            'view_user_orders',
            'users',
            $id,
            null,
            null,
            $request->ip(),
            $request->userAgent(),
            'success',
            'Viewed orders of user #' . $id
        );

        return $this->sendPaginated(
            $orders,
            OrderResource::collection($orders),
            'User orders retrieved successfully'
        );
    }

    /**
     * Get user's rentals
     */
    public function rentals(Request $request, $id)
    {
        $user = User::find($id);

        if (!$user) {
            return $this->sendError('User not found');
        }

        $rentals = $user->rentals()->with('product')->paginate(15);

        LogController::addLog(
            auth()->id(),
            'VIEW',
            'view_user_rentals',
            'users',
            $id,
            null,
            null,
            $request->ip(),
            $request->userAgent(),
            'success',
            'Viewed rentals of user #' . $id
        );

        return $this->sendPaginated(
            $rentals,
            RentalResource::collection($rentals),
            'User rentals retrieved successfully'
        );
    }
}