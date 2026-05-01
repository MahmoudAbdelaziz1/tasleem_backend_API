{{-- resources/views/admin/orders/print.blade.php --}}
<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Print Order #{{ $order->order_id }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        @media print {
            .no-print { display: none; }
            body { padding: 20px; }
            .card { border: 1px solid #ddd; }
            .btn { display: none; }
        }
        .invoice-header {
            border-bottom: 2px solid #4e73df;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        .table-total {
            font-weight: bold;
            background-color: #f8f9fa;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="no-print mb-3">
            <button onclick="window.print()" class="btn btn-primary">
                <i class="fas fa-print"></i> Print
            </button>
            <button onclick="window.close()" class="btn btn-secondary">
                Close
            </button>
        </div>

        <div class="card">
            <div class="card-body">
                <div class="invoice-header text-center">
                    <h2>Order Confirmation #{{ $order->order_id }}</h2>
                    <p class="text-muted">Order Date: {{ $order->created_at->format('Y-m-d h:i A') }}</p>
                </div>

                <div class="row mb-4">
                    <div class="col-md-6">
                        <h5>Customer Information</h5>
                        <p>
                            <strong>{{ $order->user->name ?? 'Not specified' }}</strong><br>
                            {{ $order->user->email ?? '' }}<br>
                            {{ $order->user->phone ?? '—' }}<br>
                            {{ $order->user->address ?? '—' }}
                        </p>
                    </div>
                    <div class="col-md-6">
                        <h5>Order Information</h5>
                        <p>
                            Order Status: <strong>{{ $order->status }}</strong><br>
                            Payment Method: {{ $order->payment->payment_method ?? '—' }}<br>
                            Payment Status: {{ $order->payment->status ?? 'Not Paid' }}
                        </p>
                    </div>
                </div>

                <table class="table table-bordered">
                    <thead class="table-dark">
                        <tr>
                            <th>Product</th>
                            <th>Quantity</th>
                            <th>Unit Price</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>{{ $order->product->name ?? 'Not specified' }}</td>
                            <td>{{ $order->quantity }}</td>
                            <td>{{ number_format($order->unit_price, 2) }} EGP</td>
                            <td>{{ number_format($order->quantity * $order->unit_price, 2) }} EGP</td>
                        </tr>
                    </tbody>
                    <tfoot>
                        <tr class="table-total">
                            <th colspan="3" class="text-end">Grand Total:</th>
                            <th>{{ number_format($order->total_price, 2) }} EGP</th>
                        </tr>
                    </tfoot>
                </table>

                <div class="text-center mt-4">
                    <p>Thank you for shopping with us</p>
                    <p class="text-muted">This is an electronic confirmation that does not require a signature</p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>