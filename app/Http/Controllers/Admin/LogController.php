<?php
// app/Http/Controllers/Admin/LogController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Log;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LogController extends Controller
{
    public function index(Request $request)
    {
        $query = Log::with('user')->latest();

        // فلترة حسب الوحدة
        if ($request->filled('module')) {
            $query->where('module', $request->module);
        }

        // فلترة حسب نوع الإجراء
        if ($request->filled('action_type')) {
            $query->where('action_type', $request->action_type);
        }

        // فلترة حسب المستخدم
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        // فلترة حسب الحالة
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // فلترة حسب التاريخ
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // فلترة حسب الكيان
        if ($request->filled('entity_type')) {
            $query->where('entity_type', $request->entity_type);
        }

        $logs = $query->paginate(50);
        
        // إحصائيات السجلات
        $stats = [
            'total' => Log::count(),
            'today' => Log::whereDate('created_at', today())->count(),
            'week' => Log::whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count(),
            'month' => Log::whereMonth('created_at', now()->month)->count(),
            'success' => Log::where('status', 'success')->count(),
            'failed' => Log::where('status', 'failed')->count(),
        ];
        
        // قائمة الوحدات المتاحة
        $modules = Log::select('module')->distinct()->whereNotNull('module')->pluck('module');
        $actionTypes = Log::select('action_type')->distinct()->pluck('action_type');
        $entityTypes = Log::select('entity_type')->distinct()->whereNotNull('entity_type')->pluck('entity_type');
        $users = User::whereHas('logs')->get();

        return view('admin.logs.index', compact('logs', 'modules', 'actionTypes', 'entityTypes', 'users', 'stats'));
    }

    public function show(Log $log)
    {
        $log->load('user');
        
        // جلب السجلات السابقة واللاحقة لنفس الكيان
        $previousLog = null;
        $nextLog = null;
        
        if ($log->entity_type && $log->entity_id) {
            $previousLog = Log::where('entity_type', $log->entity_type)
                ->where('entity_id', $log->entity_id)
                ->where('log_id', '<', $log->log_id)
                ->latest()
                ->first();
                
            $nextLog = Log::where('entity_type', $log->entity_type)
                ->where('entity_id', $log->entity_id)
                ->where('log_id', '>', $log->log_id)
                ->oldest()
                ->first();
        }
        
        return view('admin.logs.show', compact('log', 'previousLog', 'nextLog'));
    }

    public function clear(Request $request)
    {
        $request->validate([
            'days' => 'required|integer|min:1|max:365',
            'module' => 'nullable|string',
        ]);

        $query = Log::where('created_at', '<', now()->subDays($request->days));
        
        if ($request->filled('module')) {
            $query->where('module', $request->module);
        }
        
        $deleted = $query->delete();

        return redirect()->route('admin.logs.index')
            ->with('success', "تم حذف {$deleted} سجل قديم بنجاح");
    }
    
    public function export(Request $request)
    {
        $query = Log::with('user');
        
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }
        
        $logs = $query->get();
        
        $filename = 'logs_export_' . date('Y-m-d_His') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename={$filename}",
        ];
        
        $callback = function() use ($logs) {
            $file = fopen('php://output', 'w');
            
            // Add BOM for Arabic support
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            
            // Headers
    fputcsv($file, [
    'ID', 'User', 'Action Type', 'Action Name', 'Unit',
    'Entity Type', 'Entity ID', 'Status', 'Message', 'IP Address',
    'Date'
    ]);
            
            // Data
            foreach ($logs as $log) {
                fputcsv($file, [
                    $log->log_id,
                    $log->user->name ?? 'Unregistered',
                    $log->action_type,
                    $log->action_name,
                    $log->module,
                    $log->entity_type,
                    $log->entity_id,
                    $log->status,
                    $log->message,
                    $log->ip_address,
                    $log->created_at,
                ]);
            }
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }
    
    public function stats()
    {
       
        $moduleStats = Log::select('module', DB::raw('count(*) as total'))
            ->groupBy('module')
            ->orderBy('total', 'desc')
            ->get();
        
        
        $actionStats = Log::select('action_type', DB::raw('count(*) as total'))
            ->groupBy('action_type')
            ->orderBy('total', 'desc')
            ->get();
        
     
        $statusStats = Log::select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->get();
        
       
        $dailyStats = Log::select(DB::raw('DATE(created_at) as date'), DB::raw('count(*) as total'))
            ->where('created_at', '>=', now()->subDays(30))
            ->groupBy(DB::raw('DATE(created_at)'))
            ->orderBy('date', 'desc')
            ->get();
        
        return view('admin.logs.stats', compact('moduleStats', 'actionStats', 'statusStats', 'dailyStats'));
    }
}