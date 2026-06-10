<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\PendaftaranBaru;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PendaftaranController  // Pastikan extends Controller
{
    // FUNGSI BARU: Untuk menampilkan data di tabel Dashboard Admin
    public function index()
    {
        try {
            // Mengambil semua data pendaftar, urutkan dari yang terbaru
            $pendaftar = PendaftaranBaru::orderBy('created_at', 'desc')->get();

            return response()->json([
                'status'  => 'success',
                'message' => 'Data pendaftar berhasil diambil',
                'data'    => $pendaftar
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Gagal mengambil data: ' . $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request)
    {
        // ... (fungsi store kamu tetap sama, tidak perlu diubah)
        $validator = Validator::make($request->all(), [
            'nama_lengkap'  => 'required|string|max:100',
            'nim'           => 'required|string|max:20',
            'universitas'   => 'required|string|max:100',
            'program_studi' => 'required|string|max:100',
            'jenis_kelamin' => 'required|in:Laki-laki,Perempuan',
            'no_hp'         => 'required|string|max:20',
            'alamat_asal'   => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Validasi gagal',
                'errors'  => $validator->errors()
            ], 422);
        }

        try {
            $pendaftaran = PendaftaranBaru::create([
                'user_id'           => $request->user()->id, 
                'nama_lengkap'      => $request->nama_lengkap,
                'nim'               => $request->nim,
                'universitas'       => $request->universitas,
                'program_studi'     => $request->program_studi,
                'jenis_kelamin'     => $request->jenis_kelamin,
                'no_hp'             => $request->no_hp,
                'alamat_asal'       => $request->alamat_asal,
                'status_pendaftaran'=> 'Menunggu'
            ]);

            return response()->json([
                'status'  => 'success',
                'message' => 'Pendaftaran anggota baru berhasil dikirim!',
                'data'    => $pendaftaran
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Terjadi kesalahan pada server: ' . $e->getMessage()
            ], 500);
        }
    }

    public function updateStatus(Request $request, $id)
    {
        $pendaftaran = \App\Models\PendaftaranBaru::find($id);
        
        if (!$pendaftaran) {
            return response()->json(['message' => 'Data tidak ditemukan'], 404);
        }

        $pendaftaran->status_pendaftaran = $request->status_pendaftaran;
        $pendaftaran->save();

        return response()->json(['message' => 'Status berhasil diubah'], 200);
    }
}