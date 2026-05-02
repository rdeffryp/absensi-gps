<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Office;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class KaryawanController extends Controller
{
    public function dashboard()
    {
        $absenHariIni = Attendance::where('user_id', Auth::id())
            ->whereDate('checked_at', today())
            ->get();

        $sudahMasuk = $absenHariIni->where('type', 'masuk')->first();
        $sudahPulang = $absenHariIni->where('type', 'pulang')->first();
        $office = Office::first();

        return view('karyawan.dashboard', compact('sudahMasuk', 'sudahPulang', 'office'));
    }

    public function absen(Request $request)
    {
        $request->validate([
            'type' => 'required|in:masuk,pulang',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'photo' => 'required|string',
        ]);

        $office = Office::first();
        $jarak = $this->hitungJarak(
            $request->latitude,
            $request->longitude,
            $office->latitude,
            $office->longitude
        );

        if ($jarak > $office->radius) {
            return response()->json([
                'success' => false,
                'message' => 'Kamu berada di luar radius kantor. Jarak kamu: ' . round($jarak) . ' meter.',
            ]);
        }

        $jam = now()->hour;

        if ($request->type === 'masuk') {
            if ($jam < 7) {
                return response()->json([
                    'success' => false,
                    'message' => 'Belum bisa absen masuk. Absen dibuka mulai jam 07.00.',
                ]);
            }

            $sudahMasuk = Attendance::where('user_id', Auth::id())
                ->whereDate('checked_at', today())
                ->where('type', 'masuk')
                ->exists();

            if ($sudahMasuk) {
                return response()->json([
                    'success' => false,
                    'message' => 'Kamu sudah absen masuk hari ini.',
                ]);
            }

            $status = $jam < 8 ? 'tepat_waktu' : 'terlambat';
        } else {
            $sudahMasuk = Attendance::where('user_id', Auth::id())
                ->whereDate('checked_at', today())
                ->where('type', 'masuk')
                ->exists();

            if (!$sudahMasuk) {
                return response()->json([
                    'success' => false,
                    'message' => 'Kamu belum absen masuk hari ini.',
                ]);
            }

            $sudahPulang = Attendance::where('user_id', Auth::id())
                ->whereDate('checked_at', today())
                ->where('type', 'pulang')
                ->exists();

            if ($sudahPulang) {
                return response()->json([
                    'success' => false,
                    'message' => 'Kamu sudah absen pulang hari ini.',
                ]);
            }

            $status = 'tepat_waktu';
        }

        // Simpan foto
        $photoData = $request->photo;
        $photoData = str_replace('data:image/png;base64,', '', $photoData);
        $photoData = str_replace(' ', '+', $photoData);
        $photoName = 'absen/' . Auth::id() . '_' . time() . '.png';
        \Storage::disk('public')->put($photoName, base64_decode($photoData));

        Attendance::create([
            'user_id' => Auth::id(),
            'office_id' => $office->id,
            'type' => $request->type,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'photo' => $photoName,
            'status' => $status,
            'checked_at' => now(),
        ]);

        $pesan = $request->type === 'masuk'
            ? 'Absen masuk berhasil! Status: ' . ($status === 'tepat_waktu' ? 'Tepat Waktu' : 'Terlambat')
            : 'Absen pulang berhasil!';

        return response()->json([
            'success' => true,
            'message' => $pesan,
        ]);
    }

    public function riwayat()
    {
        $riwayat = Attendance::where('user_id', Auth::id())
            ->orderBy('checked_at', 'desc')
            ->paginate(15);

        return view('karyawan.riwayat', compact('riwayat'));
    }

    private function hitungJarak($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371000;
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);
        $a = sin($dLat / 2) * sin($dLat / 2) +
            cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
            sin($dLon / 2) * sin($dLon / 2);
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        return $earthRadius * $c;
    }
}