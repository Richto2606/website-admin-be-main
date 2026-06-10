<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pendaftaran_baru', function (Blueprint $table) {
            $table->id('id_pendaftaran');
            $table->string('nama_lengkap', 100);
            $table->string('nim', 20);
            $table->string('universitas', 100);
            $table->string('program_studi', 100);
            $table->enum('jenis_kelamin', ['Laki-laki', 'Perempuan']);
            $table->string('no_hp', 20);
            $table->text('alamat_asal');
            $table->string('file_berkas', 255)->nullable();
            $table->enum('status_pendaftaran', ['Menunggu', 'Diterima', 'Ditolak'])->default('Menunggu');
            $table->timestamps(); // Ini otomatis membuat kolom created_at dan updated_at
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pendaftaran_baru');
    }
};