<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
{
    Schema::table('pendaftaran_baru', function (Blueprint $table) {
        // 1. Membuat kolom user_id yang diletakkan setelah id_pendaftaran
        // unsignedBigInteger digunakan karena primary key tabel users bawaan laravel adalah BigInteger
        $table->unsignedBigInteger('user_id')->after('id_pendaftaran');

        // 2. Mendefinisikan Foreign Key yang menghubungkan ke id di tabel users
        // onDelete('cascade') artinya jika akun user dihapus, data pendaftarannya ikut terhapus
        $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
    });
}

public function down(): void
{
    Schema::table('pendaftaran_baru', function (Blueprint $table) {
        // Menghapus Foreign Key dan kolomnya jika migrasi di-rollback
        $table->dropForeign(['user_id']);
        $table->dropColumn('user_id');
    });
}
};
