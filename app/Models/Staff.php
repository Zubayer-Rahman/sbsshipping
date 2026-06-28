<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Staff extends Model
{
    protected $table = 'staff';
    protected $fillable = ['name', 'position', 'per_day_salary', 'is_active'];

    public function attendance()
    {
        return $this->hasMany(Attendance::class);
    }

    public function salaryRecords()
    {
        return $this->hasMany(SalaryRecord::class);
    }

    /**
     * Count absent days for a given year/month.
     * absent + half_day counts as 0.5 each.
     */
    public function absentDaysForMonth(int $year, int $month): float
    {
        $records = $this->attendance()
            ->whereYear('date', $year)
            ->whereMonth('date', $month)
            ->whereIn('status', ['absent', 'half_day'])
            ->get();

        $days = 0;
        foreach ($records as $r) {
            $days += ($r->status === 'half_day') ? 0.5 : 1;
        }
        return $days;
    }

    /**
     * Total working days in a month (Mon–Thu + Sun — excluding Fri & Sat).
     * In Bangladesh context: Fri & Sat are weekends.
     */
    public function workingDaysInMonth(int $year, int $month): int
    {
        $start = Carbon::createFromDate($year, $month, 1);
        $end   = $start->copy()->endOfMonth();
        $count = 0;
        for ($d = $start->copy(); $d->lte($end); $d->addDay()) {
            // 5 = Friday, 6 = Saturday in Carbon (ISO: 5=Fri, 6=Sat)
            if (!in_array($d->dayOfWeek, [])) { // 5=Fri,6=Sat
                $count++;
            }
        }
        return $count;
    }
}
