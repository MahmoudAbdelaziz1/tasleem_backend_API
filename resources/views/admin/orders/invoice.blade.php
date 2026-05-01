{{-- resources/views/admin/orders/invoice.blade.php --}}
@extends('admin.layouts.master')

@section('title', 'Invoice for Order #' . $order->order_id)

@section('content')
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <div class="d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-file-invoice"></i> Invoice for Order #{{ $order->order_id }}
            </h6>
            <div>
                <button onclick="window.print()" class="btn btn-primary btn-sm">
                    <i class="fas fa-print"></i> Print
                </button>
                <a href="{{ route('admin.orders.show', $order) }}" class="btn btn-secondary btn-sm">
                    <i class="fas fa-arrow-left"></i> Back
                </a>
            </div>
        </div>
    </div>
    <div class="card-body">
        <div class="invoice-wrapper p-4">
            <!-- Company Header -->
            <div class="text-center mb-4">
                <h2 class="text-primary">Tasleem</h2>
                <p class="text-muted">Buying, Selling, and Rental Platform</p>
                <hr>
            </div>

            <div class="row mb-4">
                <div class="col-md-6">
                    <h5>Company Information</h5>
                    <p>
                        <strong>Tasleem</strong><br>
                        Email: info@tasleem.com<br>
                        Phone: +123 456 7890
                    </p>
                </div>
                <div class="col-md-6 text-end">
                    <h5>Invoice Information</h5>
                    <p>
                        <strong>Invoice Number:</strong> INV-{{ $order->order_id }}<br>
                        <strong>Invoice Date:</strong> {{ $order->created_at->format('Y-m-d') }}<br>
                        <strong>Invoice Status:</strong> 
                        <span class="badge bg-{{ $order->status == 'delivered' ? 'success' : 'warning' }}">
                            {{ $order->status }}
                        </span>
                    </p>
                </div>
            </div>

            <div class="row mb-4">
                <div class="col-md-6">
                    <h5>Customer Information</h5>
                    <p>
                        <strong>{{ $order->user->name ?? 'Not specified' }}</strong><br>
                        {{ $order->user->email ?? '' }}<br>
                        {{ $order->user->phone ?? '—' }}<br>
                        {{ $order->user->address ?? '—' }}<br>
                        {{ $order->user->city ?? '—' }}
                    </p>
                </div>
                <div class="col-md-6 text-end">
                    <h5>Shipping Information</h5>
                    <p>
                        Shipping Method: Standard Shipping<br>
                        Shipping Address: {{ $order->user->address ?? '—' }}<br>
                        Expected Date: {{ $order->created_at->addDays(5)->format('Y-m-d') }}
                    </p>
                </div>
            </div>

            <!-- Order Items Table -->
            <table class="table table-bordered">
                <thead class="table-dark">
                    <tr>
                        <th>#</th>
                        <th>Product</th>
                        <th>Quantity</th>
                        <th>Unit Price</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>1</td>
                        <td>
                            <strong>{{ $order->product->name ?? 'Not specified' }}</strong><br>
                            <small class="text-muted">{{ $order->product->category->name ?? '' }}</small>
                         </td>
                         <td>{{ $order->quantity }}</td>
                         <td>{{ number_format($order->unit_price, 2) }} EGP</td>
                         <td>{{ number_format($order->quantity * $order->unit_price, 2) }} EGP</td>
                     </tr>
                </tbody>
                <tfoot>
                    <tr class="table-active">
                        <th colspan="4" class="text-end">Subtotal:</th>
                        <th>{{ number_format($order->quantity * $order->unit_price, 2) }} EGP</th>
                     </tr>
                    <tr class="table-active">
                        <th colspan="4" class="text-end">Delivery Fee:</th>
                        <th>0.00 EGP</th>
                     </tr>
                    <tr class="table-total">
                        <th colspan="4" class="text-end h5">Grand Total:</th>
                        <th class="h5 text-success">{{ number_format($order->total_price, 2) }} EGP</th>
                     </tr>
                </tfoot>
             </table>

            <!-- Payment Information -->
            <div class="row mt-4">
                <div class="col-md-6">
                    <h5>Payment Information</h5>
                    <p>
                        <strong>Payment Method:</strong> {{ $order->payment->payment_method ?? '—' }}<br>
                        <strong>Payment Status:</strong> 
                        <span class="badge bg-{{ $order->payment->status == 'completed' ? 'success' : 'warning' }}">
                            {{ $order->payment->status ?? 'Not Paid' }}
                        </span><br>
                        <strong>Transaction ID:</strong> {{ $order->payment->transaction_id ?? '—' }}
                    </p>
                </div>
                <div class="col-md-6 text-end">
                    <div class="border p-3 rounded">
                        <h5>Payment Summary</h5>
                        <p class="mb-0">Total: {{ number_format($order->total_price, 2) }} EGP</p>
                        <p class="mb-0">Paid: {{ number_format($order->payment->amount ?? 0, 2) }} EGP</p>
                        <p class="mb-0 text-danger">Remaining: {{ number_format(($order->total_price - ($order->payment->amount ?? 0)), 2) }} EGP</p>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <div class="text-center mt-5 pt-4 border-top">
                <p class="text-muted">Thank you for shopping with us. This is a legally valid electronic invoice.</p>
                <p class="text-muted small">Tasleem - Buying, Selling, and Rental Platform © {{ date('Y') }}</p>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    @media print {
        .btn, .card-header .btn, .no-print {
            display: none !important;
        }
        .card {
            border: none !important;
            box-shadow: none !important;
        }
        .card-header {
            display: none;
        }
        body {
            padding: 0;
            margin: 0;
        }
    }
    .table-total {
        background-color: #e8f4f8;
        font-size: 1.2em;
    }
    .invoice-wrapper {
        background: white;
    }
</style>
@endpush