<?php

namespace App\Http\Controllers;

use App\Models\Staff;
use App\Models\Attendance;
use App\Models\SalaryRecord;
use Carbon\Carbon;
use Illuminate\Http\Request;

class SalaryController extends Controller
{
    // ── Staff Management ──────────────────────────────────────────────────────

    public function staffIndex()
    {
        $staff = Staff::orderBy('name')->get();
        return view('salary.staff-index', compact('staff'));
    }

    public function staffStore(Request $request)
    {
        $data = $request->validate([
            'name'           => 'required|string|max:100',
            'position'       => 'required|string|max:100',
            'per_day_salary' => 'required|numeric|min:0',
        ]);
        Staff::create($data);
        return back()->with('success', 'Staff member added successfully.');
    }

    public function staffUpdate(Request $request, Staff $staff)
    {
        $data = $request->validate([
            'name'           => 'required|string|max:100',
            'position'       => 'required|string|max:100',
            'per_day_salary' => 'required|numeric|min:0',
        ]);
        $staff->update($data);
        return back()->with('success', 'Staff updated.');
    }

    public function staffDestroy(Staff $staff)
    {
        $staff->delete();
        return back()->with('success', 'Staff removed.');
    }

    // ── Attendance Sheet ──────────────────────────────────────────────────────

    public function attendance(Request $request)
    {
        $year  = (int) ($request->year  ?? now()->year);
        $month = (int) ($request->month ?? now()->month);

        $startOfMonth = Carbon::createFromDate($year, $month, 1);
        $endOfMonth   = $startOfMonth->copy()->endOfMonth();

        // Build list of weekdays only (skip Fri=5, Sat=6)
        $weekdays = [];
        for ($d = $startOfMonth->copy(); $d->lte($endOfMonth); $d->addDay()) {
            if (!in_array($d->dayOfWeek, [6])) {
                $weekdays[] = $d->copy();
            }
        }

        $staffList = Staff::where('is_active', true)->orderBy('name')->get();

        // Load existing attendance as [staff_id][date] => status
        $existing = Attendance::where(function ($q) use ($year, $month) {
                $q->whereYear('date', $year)->whereMonth('date', $month);
            })
            ->whereIn('staff_id', $staffList->pluck('id'))
            ->get()
            ->groupBy('staff_id')
            ->map(fn($group) => $group->keyBy(fn($r) => $r->date->format('Y-m-d')));

        return view('salary.attendance', compact('staffList', 'weekdays', 'year', 'month', 'existing'));
    }

    /**
     * AJAX — mark a single attendance cell.
     */
    public function attendanceMark(Request $request)
    {
        $data = $request->validate([
            'staff_id' => 'required|exists:staff,id',
            'date'     => 'required|date',
            'status'   => 'required|in:present,absent,late,half_day,leave',
        ]);

        Attendance::updateOrCreate(
            ['staff_id' => $data['staff_id'], 'date' => $data['date']],
            ['status'   => $data['status']]
        );

        return response()->json(['ok' => true]);
    }

    // ── Salary Sheet ──────────────────────────────────────────────────────────

    public function salarySheet(Request $request)
    {
        $year  = (int) ($request->year  ?? now()->year);
        $month = (int) ($request->month ?? now()->month);

        $staffList = Staff::where('is_active', true)->orderBy('name')->get();

        // Load salary records for this month
        $salaryRecords = SalaryRecord::where('year', $year)->where('month', $month)
            ->get()->keyBy('staff_id');

        $rows = [];
        foreach ($staffList as $i => $s) {
            $workingDays     = $s->workingDaysInMonth($year, $month);
            $absentDays      = $s->absentDaysForMonth($year, $month);
            $rec             = $salaryRecords->get($s->id);
            $advanceCut      = $rec ? $rec->advance_cut : 0;
            $remarks         = $rec ? $rec->remarks     : '';
            $absentDeduction = $absentDays * $s->per_day_salary;
            $grossSalary     = $workingDays * $s->per_day_salary;
            $netPayable      = $grossSalary - $absentDeduction - $advanceCut;

            $rows[] = [
                'sl'               => $i + 1,
                'staff'            => $s,
                'working_days'     => $workingDays,
                'absent_days'      => $absentDays,
                'per_day_salary'   => $s->per_day_salary,
                'advance_cut'      => $advanceCut,
                'absent_deduction' => $absentDeduction,
                'gross_salary'     => $grossSalary,
                'net_payable'      => $netPayable,
                'remarks'          => $remarks,
            ];
        }

        return view('salary.salary-sheet', compact('rows', 'year', 'month', 'staffList'));
    }

    /**
     * AJAX — update advance/cut and remarks for a staff in a given month.
     */
    public function salaryUpdate(Request $request)
    {
        $data = $request->validate([
            'staff_id'   => 'required|exists:staff,id',
            'year'       => 'required|integer',
            'month'      => 'required|integer|min:1|max:12',
            'advance_cut'=> 'required|numeric',
            'remarks'    => 'nullable|string|max:255',
        ]);

        SalaryRecord::updateOrCreate(
            ['staff_id' => $data['staff_id'], 'year' => $data['year'], 'month' => $data['month']],
            ['advance_cut' => $data['advance_cut'], 'remarks' => $data['remarks'] ?? '']
        );

        // Return updated net payable
        $staff           = Staff::findOrFail($data['staff_id']);
        $absentDays      = $staff->absentDaysForMonth($data['year'], $data['month']);
        $workingDays     = $staff->workingDaysInMonth($data['year'], $data['month']);
        $absentDeduction = $absentDays * $staff->per_day_salary;
        $grossSalary     = $workingDays * $staff->per_day_salary;
        $netPayable      = $grossSalary - $absentDeduction - $data['advance_cut'];

        return response()->json([
            'ok'               => true,
            'absent_days'      => $absentDays,
            'absent_deduction' => number_format($absentDeduction, 2),
            'gross_salary'     => number_format($grossSalary, 2),
            'net_payable'      => number_format($netPayable, 2),
        ]);
    }
}