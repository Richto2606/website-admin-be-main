<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Resident extends Model
{
    use HasFactory;
    public $incrementing = false;
    protected $keyType = 'string';

    protected $table = 'residents';

    protected $fillable = [
        'user_id',
        'pendaftaran_id',
        'name',
        'age',
        'birth_date',
        'address',
        'origin_city_id',
        'origin_campus_id',
        'phone_number',
        'room_number_id',
        'status',
        'tanggal_masuk',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function pendaftaran()
    {
        return $this->belongsTo(PendaftaranBaru::class, 'pendaftaran_id', 'id_pendaftaran');
    }

    public function originCities()
    {
        return $this->belongsTo(OriginCity::class, 'origin_city_id');
    }

    public function originCampuses()
    {
        return $this->belongsTo(OriginCampus::class, 'origin_campus_id');
    }

    public function roomNumbers()
    {
        return $this->belongsTo(RoomNumber::class, 'room_number_id');
    }

    // 🔥 TAMBAHKAN TYPE HINT: \Illuminate\Database\Eloquent\Builder
    public function scopeByStatus(\Illuminate\Database\Eloquent\Builder $query, string $status)
    {
        if ($status === 'active') {
            return $query->where('status', 'active');
        }
        
        if ($status === 'inactive') {
            return $query->where('status', 'inactive');
        }
        
        return $query;
    }

    // 🔥 TAMBAHKAN TYPE HINT: \Illuminate\Database\Eloquent\Builder
    public function scopeByName(\Illuminate\Database\Eloquent\Builder $query, string $name)
    {
        return $query->where('name', 'like', "%{$name}%");
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (!$model->id) {
                $model->id = (string) Str::uuid();
            }
        });
    }
}