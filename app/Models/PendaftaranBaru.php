<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PendaftaranBaru extends Model
{
    use HasFactory;

    // Arahkan ke nama tabel yang benar
    protected $table = 'pendaftaran_baru';

    // Tambahkan user_id ke dalam array fillable agar bisa disimpan
    protected $fillable = [
        'user_id', 
        'nama_lengkap', 
        'nim', 
        'universitas', 
        'program_studi', 
        'jenis_kelamin', 
        'no_hp', 
        'alamat_asal', 
        'file_berkas', 
        'status_pendaftaran'
    ];

    // Buat relasi (Satu form pendaftaran dimiliki oleh satu User)
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}