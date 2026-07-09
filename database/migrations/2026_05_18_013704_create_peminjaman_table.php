<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// database/migrations/xxxx_create_peminjaman_table.php
return new class extends Migration {
    public function up(): void
    {
        Schema::create('peminjaman', function (Blueprint $table) {
            $table->id('id_peminjaman');

            // FK ke tabel siswa
            $table->unsignedBigInteger('id_siswa');
            $table->foreign('id_siswa')
                  ->references('id_siswa')
                  ->on('siswa')
                  ->onDelete('cascade');

            // FK ke tabel alat
            $table->unsignedBigInteger('id_alat');
            $table->foreign('id_alat')
                  ->references('id_alat')
                  ->on('alat')
                  ->onDelete('cascade');

            // FK ke users (petugas yang memvalidasi)
            $table->foreignId('id_petugas')
                  ->nullable()
                  ->constrained('users')
                  ->onDelete('set null');

            $table->date('tanggal_pinjam');
            $table->date('tanggal_kembali');       // rencana kembali
            $table->integer('jumlah');
            $table->enum('status', ['pending', 'acc', 'ditolak', 'selesai'])
                  ->default('pending');
            $table->text('catatan_siswa')->nullable();
            $table->text('catatan_petugas')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('peminjaman');
    }
};