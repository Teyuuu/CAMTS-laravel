<?php
// app/Http/Controllers/AttendanceController.php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\AttendanceLog;
use App\Models\Employee;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AttendanceController extends Controller
{
    public function index()
    {
        // Get all employees (you can replace this with actual database query)
        $employees = [
            ['id' => 1, 'name' => 'John Doe', 'position' => 'Manager'],
            ['id' => 2, 'name' => 'Jane Smith', 'position' => 'Driver'],
            ['id' => 3, 'name' => 'Mike Johnson', 'position' => 'Worker'],
            ['id' => 4, 'name' => 'Sarah Williams', 'position' => 'Supervisor'],
        ];
        
        // Get today's logs
        $todays_logs = AttendanceLog::whereDate('created_at', today())
            ->latest()
            ->get()
            ->map(function ($log) use ($employees) {
                $employee = collect($employees)->firstWhere('id', $log->employee_id);
                return [
                    'employee_name' => $employee['name'] ?? 'Unknown',
                    'action' => $log->action,
                    'timestamp' => $log->created_at,
                ];
            });
        
        // Get attendance records for the week
        $attendance_records = $this->getWeeklyAttendance($employees);
        
        // Calculate weekly summary
        $weekly_summary = $this->calculateWeeklySummary($attendance_records);
        
        return view('attendance', compact(
            'employees',
            'todays_logs',
            'attendance_records',
            'weekly_summary'
        ));
    }
    
    public function timeIn(Request $request)
    {
        $validated = $request->validate([
            'employee_id' => 'required|integer',
            'timestamp' => 'required|string',
        ]);
        
        try {
            $timestamp = Carbon::parse($validated['timestamp']);
            $today = $timestamp->toDateString();
            
            DB::transaction(function () use ($validated, $timestamp, $today) {
                // Check if already timed in today
                $existingAttendance = Attendance::where('employee_id', $validated['employee_id'])
                    ->whereDate('date', $today)
                    ->first();
                
                if ($existingAttendance && $existingAttendance->time_in) {
                    throw new \Exception('Already timed in today');
                }
                
                // Create or update attendance record
                if ($existingAttendance) {
                    $existingAttendance->update([
                        'time_in' => $timestamp,
                    ]);
                } else {
                    Attendance::create([
                        'employee_id' => $validated['employee_id'],
                        'date' => $today,
                        'time_in' => $timestamp,
                    ]);
                }
                
                // Log the action
                AttendanceLog::create([
                    'employee_id' => $validated['employee_id'],
                    'action' => 'Time In',
                    'timestamp' => $timestamp,
                ]);
            });
            
            return response()->json([
                'success' => true,
                'message' => 'Time in recorded successfully'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }
    
    public function timeOut(Request $request)
    {
        $validated = $request->validate([
            'employee_id' => 'required|integer',
            'timestamp' => 'required|string',
        ]);
        
        try {
            $timestamp = Carbon::parse($validated['timestamp']);
            $today = $timestamp->toDateString();
            
            DB::transaction(function () use ($validated, $timestamp, $today) {
                // Find today's attendance record
                $attendance = Attendance::where('employee_id', $validated['employee_id'])
                    ->whereDate('date', $today)
                    ->first();
                
                if (!$attendance || !$attendance->time_in) {
                    throw new \Exception('Please time in first');
                }
                
                if ($attendance->time_out) {
                    throw new \Exception('Already timed out today');
                }
                
                // Calculate hours worked
                $timeIn = Carbon::parse($attendance->time_in);
                $hoursWorked = $timeIn->diffInMinutes($timestamp) / 60;
                
                // Update attendance record
                $attendance->update([
                    'time_out' => $timestamp,
                    'hours_worked' => round($hoursWorked, 2),
                ]);
                
                // Log the action
                AttendanceLog::create([
                    'employee_id' => $validated['employee_id'],
                    'action' => 'Time Out',
                    'timestamp' => $timestamp,
                ]);
            });
            
            return response()->json([
                'success' => true,
                'message' => 'Time out recorded successfully'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }
    
    private function getWeeklyAttendance($employees)
    {
        $startOfWeek = Carbon::now()->startOfWeek();
        $endOfWeek = Carbon::now()->endOfWeek();
        
        $attendances = Attendance::whereBetween('date', [$startOfWeek, $endOfWeek])
            ->get();
        
        return $attendances->map(function ($attendance) use ($employees) {
            $employee = collect($employees)->firstWhere('id', $attendance->employee_id);
            
            // Check if late (assuming work starts at 8:00 AM)
            $isLate = false;
            if ($attendance->time_in) {
                $timeIn = Carbon::parse($attendance->time_in);
                $standardTime = Carbon::parse($attendance->date . ' 08:00:00');
                $isLate = $timeIn->gt($standardTime);
            }
            
            return [
                'date' => Carbon::parse($attendance->date),
                'employee_name' => $employee['name'] ?? 'Unknown',
                'time_in' => $attendance->time_in ? Carbon::parse($attendance->time_in) : null,
                'time_out' => $attendance->time_out ? Carbon::parse($attendance->time_out) : null,
                'hours_worked' => $attendance->hours_worked,
                'is_late' => $isLate,
                'notes' => $attendance->notes,
            ];
        })->toArray();
    }
    
    private function calculateWeeklySummary($records)
    {
        $totalHours = collect($records)->sum('hours_worked');
        $daysPresent = collect($records)->filter(function ($record) {
            return $record['time_in'] !== null;
        })->count();
        
        $avgHours = $daysPresent > 0 ? round($totalHours / $daysPresent, 1) : 0;
        
        $overtime = collect($records)->reduce(function ($carry, $record) {
            if ($record['hours_worked'] && $record['hours_worked'] > 8) {
                return $carry + ($record['hours_worked'] - 8);
            }
            return $carry;
        }, 0);
        
        $lateCount = collect($records)->filter(function ($record) {
            return $record['is_late'];
        })->count();
        
        return [
            'total_hours' => round($totalHours, 1),
            'days_present' => $daysPresent,
            'avg_hours' => $avgHours,
            'overtime' => round($overtime, 1),
            'late_count' => $lateCount,
        ];
    }
}