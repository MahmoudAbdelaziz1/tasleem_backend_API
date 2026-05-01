{{-- resources/views/admin/logs/stats.blade.php --}}
@extends('admin.layouts.master')

@section('title', 'Activity Log Statistics')

@section('content')
<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex justify-content-between align-items-center">
        <h6 class="m-0 font-weight-bold text-primary">
            <i class="fas fa-chart-bar"></i> Activity Log Statistics
        </h6>
        <a href="{{ route('admin.logs.index') }}" class="btn btn-secondary btn-sm">
            <i class="fas fa-arrow-left"></i> Back to Logs
        </a>
    </div>
    <div class="card-body">
        <div class="row">
            <!-- Statistics by Module -->
            <div class="col-md-6 mb-4">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <i class="fas fa-cubes"></i> Logs by Module
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr><th>Module</th><th>Log Count</th><th>Percentage</th></tr>
                                </thead>
                                <tbody>
                                    @php
                                        $total = $moduleStats->sum('total');
                                    @endphp
                                    @foreach($moduleStats as $stat)
                                    <tr>
                                        <td><strong>{{ ucfirst($stat->module) }}</strong></td>
                                        <td>{{ number_format($stat->total) }}</td>
                                        <td>
                                            <div class="progress">
                                                @php $percentage = $total > 0 ? ($stat->total / $total) * 100 : 0; @endphp
                                                <div class="progress-bar" style="width: {{ $percentage }}%">
                                                    {{ round($percentage, 1) }}%
                                                </div>
                                            </div>
                                         </td>
                                     </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Statistics by Action Type -->
            <div class="col-md-6 mb-4">
                <div class="card">
                    <div class="card-header bg-success text-white">
                        <i class="fas fa-tasks"></i> Logs by Action Type
                    </div>
                    <div class="card-body">
                        <canvas id="actionChart" style="height: 300px;"></canvas>
                    </div>
                </div>
            </div>

            <!-- Statistics by Status -->
            <div class="col-md-6 mb-4">
                <div class="card">
                    <div class="card-header bg-info text-white">
                        <i class="fas fa-chart-pie"></i> Logs by Status
                    </div>
                    <div class="card-body">
                        <canvas id="statusChart" style="height: 300px;"></canvas>
                    </div>
                </div>
            </div>

            <!-- Daily Logs -->
            <div class="col-md-6 mb-4">
                <div class="card">
                    <div class="card-header bg-warning">
                        <i class="fas fa-calendar-alt"></i> Daily Logs (Last 30 Days)
                    </div>
                    <div class="card-body">
                        <canvas id="dailyChart" style="height: 300px;"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
$(document).ready(function() {
    // Action Types Chart
    var actionCtx = document.getElementById('actionChart').getContext('2d');
    var actionLabels = {!! json_encode($actionStats->pluck('action_type')->map(function($item) {
        return ucfirst($item);
    })) !!};
    var actionData = {!! json_encode($actionStats->pluck('total')) !!};
    
    new Chart(actionCtx, {
        type: 'bar',
        data: {
            labels: actionLabels,
            datasets: [{
                label: 'Log Count',
                data: actionData,
                backgroundColor: '#36b9cc',
                borderRadius: 5
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
                }
            }
        }
    });
    
    // Status Chart
    var statusCtx = document.getElementById('statusChart').getContext('2d');
    var statusLabels = {!! json_encode($statusStats->pluck('status')->map(function($item) {
        return $item == 'success' ? 'Success' : 'Failed';
    })) !!};
    var statusData = {!! json_encode($statusStats->pluck('total')) !!};
    var statusColors = statusLabels.map(label => label == 'Success' ? '#1cc88a' : '#e74a3b');
    
    new Chart(statusCtx, {
        type: 'pie',
        data: {
            labels: statusLabels,
            datasets: [{
                data: statusData,
                backgroundColor: statusColors,
                borderWidth: 0
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom'
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            var label = context.label || '';
                            var value = context.raw || 0;
                            var total = context.dataset.data.reduce((a, b) => a + b, 0);
                            var percentage = ((value / total) * 100).toFixed(1);
                            return `${label}: ${value} (${percentage}%)`;
                        }
                    }
                }
            }
        }
    });
    
    // Daily Chart
    var dailyCtx = document.getElementById('dailyChart').getContext('2d');
    var dailyLabels = {!! json_encode($dailyStats->pluck('date')) !!};
    var dailyData = {!! json_encode($dailyStats->pluck('total')) !!};
    
    new Chart(dailyCtx, {
        type: 'line',
        data: {
            labels: dailyLabels,
            datasets: [{
                label: 'Log Count',
                data: dailyData,
                borderColor: '#f6c23e',
                backgroundColor: 'rgba(246, 194, 62, 0.1)',
                tension: 0.3,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
                }
            }
        }
    });
});
</script>
@endpush