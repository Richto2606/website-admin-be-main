<?php

namespace App\Http\Controllers\Api;

use App\Http\Responses\ApiResponse;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return ApiResponse::success(null, User::all());
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    /**
     * 🔥 TAMBAHKAN METHOD INI: Get authenticated user profile
     */
    public function profile(Request $request)
    {
        try {
            $user = $request->user();
            
            if (!$user) {
                return ApiResponse::error('User tidak ditemukan', 404);
            }

            return ApiResponse::success('Data user berhasil diambil', [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                // Tambahkan field lain jika ada
                // 'nomor_hp' => $user->nomor_hp,
                // 'alamat' => $user->alamat,
            ]);

        } catch (\Exception $e) {
            return ApiResponse::error('Gagal mengambil data user: ' . $e->getMessage(), 500);
        }
    }
}