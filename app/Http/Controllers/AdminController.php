<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Office;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\AttendanceExport;

class AdminController extends Controller
{
    public function dashboard()
    {
        $totalKaryawan = User::where('role', 'karyawan')->count();
        $totalHadir = Attendance::whereDate('checked_at', today())->where('type', 'masuk')->count();
        $totalTerlambat = Attendance::whereDate('checked_at', today())->where('status', 'terlambat')->count();
        $rekapBulanan = Attendance::selectRaw('MONTH(checked_at) as bulan, COUNT(*) as total')
            ->whereYear('checked_at', now()->year)
            ->groupBy('bulan')
            ->get();

        return view('admin.dashboard', compact('totalKaryawan', 'totalHadir', 'totalTerlambat', 'rekapBulanan'));
    }

    public function karyawan()
    {
        $karyawan = User::where('role', 'karyawan')->get();
        return view('admin.karyawan', compact('karyawan'));
    }

    public function karyawanStore(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'karyawan',
        ]);

        return back()->with('success', 'Karyawan berhasil ditambahkan.');
    }

    public function karyawanUpdate(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
        ]);

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
        ]);

        if ($request->password) {
            $user->update(['password' => Hash::make($request->password)]);
        }

        return back()->with('success', 'Data karyawan berhasil diupdate.');
    }

    public function karyawanDestroy(User $user)
    {
        $user->delete();
        return back()->with('success', 'Karyawan berhasil dihapus.');
    }

    public function rekap(Request $request)
    {
        $bulan = $request->bulan ?? now()->month;
        $tahun = $request->tahun ?? now()->year;

        $absensi = Attendance::with('user')
            ->whereMonth('checked_at', $bulan)
            ->whereYear('checked_at', $tahun)
            ->orderBy('checked_at', 'desc')
            ->get();

        return view('admin.rekap', compact('absensi', 'bulan', 'tahun'));
    }

    public function export(Request $request)
    {
        $bulan = $request->bulan ?? now()->month;
        $tahun = $request->tahun ?? now()->year;
        return Excel::download(new AttendanceExport($bulan, $tahun), 'rekap-absensi.xlsx');
    }

    public function settings()
    {
        $office = Office::first();
        return view('admin.settings', compact('office'));
    }

    public function settingsUpdate(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'radius' => 'required|integer|min:10',
        ]);

        Office::first()->update($request->only('name', 'latitude', 'longitude', 'radius'));

        return back()->with('success', 'Pengaturan kantor berhasil disimpan.');
    }
}