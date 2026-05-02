<?php

namespace App\Exports;

use App\Models\Attendance;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class AttendanceExport implements FromCollection, WithHeadings, WithMapping
{
    protected $bulan;
    protected $tahun;

    public function __construct($bulan, $tahun)
    {
        $this->bulan = $bulan;
        $this->tahun = $tahun;
    }

    public function collection()
    {
        return Attendance::with('user')
            ->whereMonth('checked_at', $this->bulan)
            ->whereYear('checked_at', $this->tahun)
            ->orderBy('checked_at', 'desc')
            ->get();
    }

    public function headings(): array
    {
        return [
            'No',
            'Nama Karyawan',
            'Email',
            'Tipe',
            'Status',
            'Tanggal & Jam',
            'Latitude',
            'Longitude',
        ];
    }

    public function map($row): array
    {
        static $no = 1;
        return [
            $no++,
            $row->user->name,
            $row->user->email,
            ucfirst($row->type),
            ucfirst(str_replace('_', ' ', $row->status)),
            $row->checked_at->format('d/m/Y H:i'),
            $row->latitude,
            $row->longitude,
        ];
    }
}