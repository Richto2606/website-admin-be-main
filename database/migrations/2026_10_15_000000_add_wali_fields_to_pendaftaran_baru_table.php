<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pendaftaran_baru', function (Blueprint $table) {
            $table->string('nama_wali', 100)->nullable()->after('alamat_asal');
            $table->unsignedTinyInteger('semester')->nullable()->after('nama_wali');
            $table->string('no_ortu_wali', 20)->nullable()->after('semester');
            $table->string('nama_ortu_wali', 100)->nullable()->after('no_ortu_wali');
        });
    }

    public function down(): void
    {
        Schema::table('pendaftaran_baru', function (Blueprint $table) {
            $table->dropColumn([
                'nama_wali',
                'semester',
                'no_ortu_wali',
                'nama_ortu_wali',
            ]);
        });
    }
};
