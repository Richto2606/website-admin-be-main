<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('pendaftaran_baru', function (Blueprint $table) {
            if (!Schema::hasColumn('pendaftaran_baru', 'umur')) {
                $table->unsignedTinyInteger('umur')->nullable()->after('no_hp');
            }

            if (!Schema::hasColumn('pendaftaran_baru', 'email')) {
                $table->string('email', 100)->nullable()->after('umur');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pendaftaran_baru', function (Blueprint $table) {
            if (Schema::hasColumn('pendaftaran_baru', 'email')) {
                $table->dropColumn('email');
            }

            if (Schema::hasColumn('pendaftaran_baru', 'umur')) {
                $table->dropColumn('umur');
            }
        });
    }
};
