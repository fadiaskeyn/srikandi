<?php

namespace App\Repositories;

use App\Enums\PatientWayout;
use App\Models\PatientEntry;
use App\Models\PatientMove;
use App\Models\Payment;
use App\Models\Room;
use App\Services\MedicService;
use Carbon\Carbon;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;

class ReportRepository
{

    private function getAvailDates()
    {
        $entries = PatientEntry::select('date')->groupBy('date')
            ->pluck('date');

        $outs = PatientEntry::select('out_date')->whereNotNull('out_date')->groupBy('out_date')
            ->pluck('out_date');

        $moves = PatientMove::select('date')->groupBy('date')
            ->pluck('date');

        $dates = $entries->merge($outs);
        $dates = $dates->merge($moves);

        return $dates
            ->map(fn($date) => $date->format('Y-m-d'))
            ->unique()
            ->sortBy(null);
    }

    public function getCensusRecapitulations($startDate, $endDate,$roomID)
{
    $query = PatientEntry::query();

    // Validasi dan filter date range
    if (!empty($startDate) && !empty($endDate)) {
        $query->whereBetween('date', [$startDate, $endDate]);
    }

    if (!empty($roomID)) {
        $query->where('room_id', $roomID);
    }

    return $this->getAvailDates()->map(function ($date) use ($query) {
        $entries = $query->whereDate('date', $date)->get();
        $moves = PatientMove::whereDate('date', $date)->get();
        $outs = PatientEntry::whereNotNull('out_date')->whereDate('out_date', $date)->get();

        $longCares = $outs->reduce(
            fn (?int $carry, PatientEntry $entry) => $carry + MedicService::getLongCare($entry),
            0
        );

        return (object)[
            'long_cares' => $longCares,

            'date' => Carbon::createFromFormat('Y-m-d', $date)->format('d-m-Y'),
            'entries' => (object)[
                'total_entries' => $entries->count(),
                'total_moves' => $moves->count(),
                'total' => ($entries->count() + $moves->count()),
            ],
            'exits' => (object)$this->getOutPatients($date),
            'payments' => $this->getTotalPayments($entries),
            'nursing_class' => [
                1 => $entries->filter(fn($entry) => $entry->nursing_class == 1)->count(),
                2 => $entries->filter(fn($entry) => $entry->nursing_class == 2)->count(),
                3 => $entries->filter(fn($entry) => $entry->nursing_class == 3)->count(),
            ],
        ];
    });
}
public function getHospitalIndicators($month, $year, $roomID)
{
    $query = PatientEntry::query();

    if (!empty($month) && !empty($year)) {
        // Sesuaikan dengan format grouping yang sesuai dengan query MySQL Anda
        $query->selectRaw('MONTH(date) as month, YEAR(date) as year')
            ->groupBy(DB::raw('YEAR(date), MONTH(date)'))
            ->whereYear('date', $year)
            ->whereMonth('date', $month);
    }

    // Saring berdasarkan roomID jika disediakan
    if (!empty($roomID)) {
        $query->where('room_id', $roomID);
    }

    // Dapatkan hasil query
    $periods = $query->get();

    return $periods;
}



public function getHospitalVisitors($startDate, $endDate)
{

   $query = PatientEntry::query();
    $dates = $query->select(DB::raw('DAY(date) as day, MONTH(date) as month, YEAR(date) AS year'))
        ->groupBy('day', 'year', 'month')
        ->get();
    // Hitung jumlah pasien laki-laki dan perempuan untuk setiap tanggal
    return $dates->map(function ($date) use ($startDate, $endDate) {
        $malePatients = PatientEntry::whereDay('date', $date->day)
            ->whereMonth('date', $date->month)
            ->whereYear('date', $date->year)
            ->whereIn('patient_id', function (Builder $query) {
                $query->select('id')->from('patients')->where('gender', 'L');
            });

        $femalePatients = PatientEntry::whereDay('date', $date->day)
            ->whereMonth('date', $date->month)
            ->whereYear('date', $date->year)
            ->whereIn('patient_id', function (Builder $query) {
                $query->select('id')->from('patients')->where('gender', 'P');
            });

        // Jika startDate dan endDate tidak kosong, tambahkan filter untuk kedua jenis kelamin
        if (!empty($startDate) && !empty($endDate)) {
            $malePatients->whereBetween('date', [$startDate, $endDate]);
            $femalePatients->whereBetween('date', [$startDate, $endDate]);
        }

        // Hitung jumlah pasien laki-laki dan perempuan
        $maleCount = $malePatients->count();
        $femaleCount = $femalePatients->count();

        return (object) [
            'date' => Carbon::createFromDate($date->year, $date->month, $date->day)->format('d-m-Y'),
            'male' => $maleCount,
            'female' => $femaleCount,
            'total' => $maleCount + $femaleCount,
        ];
    });
}


    private function getOutPatients(string $date)
    {
        $counts = PatientEntry::whereIn('way_out', [
            PatientWayout::SELF->value,
            PatientWayout::DOCTOR->value,
            PatientWayout::REFERRED->value
        ])->whereDate('out_date', $date)
            ->selectRaw('way_out, COUNT(*) as count')
            ->groupBy('way_out')
            ->pluck('count', 'way_out');

        $self = $counts[PatientWayout::SELF->value] ?? 0;
        $doctor = $counts[PatientWayout::DOCTOR->value] ?? 0;
        $referred = $counts[PatientWayout::REFERRED->value] ?? 0;

        $countQuery = PatientEntry::whereDate('out_date', $date)
            ->where('way_out', PatientWayout::DIED)
            ->selectRaw('SUM(CASE WHEN TIMESTAMPDIFF(HOUR, date, out_date) <= 48 THEN 1 ELSE 0 END) as died_less,
                        SUM(CASE WHEN TIMESTAMPDIFF(HOUR, date, out_date) > 48 THEN 1 ELSE 0 END) as died_more')
            ->first();

        $diedLess = intval($countQuery->died_less) ?? 0;
        $diedMore = intval($countQuery->died_more) ?? 0;

        $total = ($self + $doctor + $referred + $diedLess + $diedMore);

        return compact('self', 'doctor', 'referred', 'diedLess', 'diedMore', 'total');
    }

    private function getTotalPayments($entries)
    {
        $totals = [];
        $payments = Payment::select('id', 'name')->get();

        foreach($payments as $payment) {
            $total = $entries->filter(fn($entry) => $entry->payment_id == $payment->id)->count();
            $totals[$payment->name] = $total;
        }

        return $totals;
    }


}
