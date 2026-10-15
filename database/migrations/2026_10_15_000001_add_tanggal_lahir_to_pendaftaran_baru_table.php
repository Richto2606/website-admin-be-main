<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pendaftaran_baru', function (Blueprint $table) {
            $table->date('tanggal_lahir')->nullable()->after('no_hp');
        });
    }

    public function down(): void
    {
        Schema::table('pendaftaran_baru', function (Blueprint $table) {
            $table->dropColumn('tanggal_lahir');
        });
    }
};
