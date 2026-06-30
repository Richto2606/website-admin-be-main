<?php

namespace App\Http\Controllers\Api;

use Illuminate\Routing\Controller as BaseController;
use App\Models\PendaftaranBaru;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class PendaftaranController extends BaseController
{
    public function index()
    {
        try {
            $pendaftar = PendaftaranBaru::orderBy('created_at', 'desc')->get();

            return response()->json([
                'success' => true,
                'status'  => 'success',
                'message' => 'Data pendaftar berhasil diambil',
                'data'    => $pendaftar
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'status'  => 'error',
                'message' => 'Gagal mengambil data: ' . $e->getMessage()
            ], 500);
        }
    }

 public function store(Request $request)
{
    $validator = Validator::make($request->all(), [
        'nama_lengkap'  => 'required|string|max:100',
        'nim'           => 'required|string|max:20',
        'universitas'   => 'required|string|max:100',
        'program_studi' => 'required|string|max:100',
        'jenis_kelamin' => 'required|in:Laki-laki,Perempuan',
        'no_hp'         => 'required|string|max:20',
        'email'         => 'nullable|email|max:100',
        'alamat_asal'   => 'required|string',
        'nama_wali'     => 'nullable|string|max:100',
        'semester'      => 'nullable|integer|min:1|max:14',
        'no_ortu_wali'  => 'nullable|string|max:20',
        'nama_ortu_wali'=> 'nullable|string|max:100',
        'file_berkas'   => 'nullable|file|mimes:doc,docx,jpg,jpeg,png,pdf|max:2048',
    ]);

    if ($validator->fails()) {
        return response()->json([
            'success' => false,
            'status'  => 'error',
            'message' => 'Validasi gagal',
            'errors'  => $validator->errors()
        ], 422);
    }

    try {
        // Buat folder jika belum ada
        $uploadPath = public_path('uploads/berkas');
        if (!file_exists($uploadPath)) {
            mkdir($uploadPath, 0777, true);
        }

        // Proses upload file
        $filePath = null;
        if ($request->hasFile('file_berkas')) {
            $file = $request->file('file_berkas');
            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move($uploadPath, $filename);
            $filePath = 'uploads/berkas/' . $filename;
        }

        // 🔥 SIMPAN SEMUA FIELD
        $pendaftaran = PendaftaranBaru::create([
            'user_id'           => $request->user()->id,
            'nama_lengkap'      => $request->nama_lengkap,
            'nim'               => $request->nim,
            'universitas'       => $request->universitas,
            'program_studi'     => $request->program_studi,
            'jenis_kelamin'     => $request->jenis_kelamin,
            'no_hp'             => $request->no_hp,
            'email'             => $request->email,
            'alamat_asal'       => $request->alamat_asal,
            'nama_wali'         => $request->nama_wali,
            'semester'          => $request->semester,
            'no_ortu_wali'      => $request->no_ortu_wali,
            'nama_ortu_wali'    => $request->nama_ortu_wali,
            'file_berkas'       => $filePath,
            'status_pendaftaran'=> 'Menunggu'
        ]);

        return response()->json([
            'success' => true,
            'status'  => 'success',
            'message' => 'Pendaftaran anggota baru berhasil dikirim!',
            'data'    => $pendaftaran
        ], 201);

    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'status'  => 'error',
            'message' => 'Terjadi kesalahan pada server: ' . $e->getMessage()
        ], 500);
    }
}

    public function updateStatus(Request $request, int $id)  // <-- SUDAH ADA int
    {
        try {
            $pendaftaran = PendaftaranBaru::find($id);
            
            if (!$pendaftaran) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data tidak ditemukan'
                ], 404);
            }

            $pendaftaran->status_pendaftaran = $request->status_pendaftaran;
            $pendaftaran->save();

            return response()->json([
                'success' => true,
                'message' => 'Status berhasil diubah'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengubah status: ' . $e->getMessage()
            ], 500);
        }
    }

    public function showFile(int $id)  // <-- TAMBAHKAN "int"
    {
        try {
            $pendaftaran = PendaftaranBaru::find($id);
            
            if (!$pendaftaran || !$pendaftaran->file_berkas) {
                return response()->json([
                    'success' => false,
                    'message' => 'File tidak ditemukan'
                ], 404);
            }

            $filePath = public_path($pendaftaran->file_berkas);
            
            if (!file_exists($filePath)) {
                return response()->json([
                    'success' => false,
                    'message' => 'File tidak ditemukan di server'
                ], 404);
            }

            return response()->file($filePath);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil file: ' . $e->getMessage()
            ], 500);
        }
    }
}