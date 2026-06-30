<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PendaftaranBaru extends Model
{
    use HasFactory;

    protected $table = 'pendaftaran_baru';
    protected $primaryKey = 'id_pendaftaran';

    protected $fillable = [
        'user_id', 
        'nama_lengkap', 
        'nim', 
        'universitas', 
        'program_studi', 
        'jenis_kelamin', 
        'no_hp',
        'email',
        'alamat_asal',
        'nama_wali',
        'semester',
        'no_ortu_wali',
        'nama_ortu_wali',
        'file_berkas',
        'status_pendaftaran'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}