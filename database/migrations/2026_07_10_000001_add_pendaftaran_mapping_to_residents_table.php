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
        if (!Schema::hasColumn('residents', 'user_id')) {
            Schema::table('residents', function (Blueprint $table) {
                $table->foreignId('user_id')->nullable()->after('id')->constrained('users')->nullOnDelete();
            });
        }

        if (!Schema::hasColumn('residents', 'pendaftaran_id')) {
            Schema::table('residents', function (Blueprint $table) {
                $table->unsignedBigInteger('pendaftaran_id')->nullable()->after('user_id');
                $table->foreign('pendaftaran_id')->references('id_pendaftaran')->on('pendaftaran_baru')->nullOnDelete();
            });
        }

        if (!Schema::hasColumn('residents', 'tanggal_masuk')) {
            Schema::table('residents', function (Blueprint $table) {
                $table->date('tanggal_masuk')->nullable()->after('status');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('residents', 'pendaftaran_id')) {
            Schema::table('residents', function (Blueprint $table) {
                $table->dropForeign(['pendaftaran_id']);
                $table->dropColumn('pendaftaran_id');
            });
        }

        if (Schema::hasColumn('residents', 'user_id')) {
            Schema::table('residents', function (Blueprint $table) {
                $table->dropForeign(['user_id']);
                $table->dropColumn('user_id');
            });
        }

        if (Schema::hasColumn('residents', 'tanggal_masuk')) {
            Schema::table('residents', function (Blueprint $table) {
                $table->dropColumn('tanggal_masuk');
            });
        }
    }
};
