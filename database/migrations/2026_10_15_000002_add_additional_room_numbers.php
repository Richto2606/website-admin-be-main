<?php

use App\Models\RoomNumber;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        foreach (['D1', 'D2', 'E1', 'E2', 'F1', 'F2', 'G1', 'G2', 'H1', 'H2'] as $name) {
            RoomNumber::firstOrCreate(
                ['name' => $name],
                ['description' => 'Kamar tambahan']
            );
        }
    }

    public function down(): void
    {
        RoomNumber::whereIn('name', [
            'D1', 'D2', 'E1', 'E2', 'F1', 'F2', 'G1', 'G2', 'H1', 'H2',
        ])->delete();
    }
};
