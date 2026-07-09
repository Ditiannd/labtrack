<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('pengembalian', function (Blueprint $table) {
            $table->id('id_pengembalian');

            $table->unsignedBigInteger('id_peminjaman');
            $table->foreign('id_peminjaman')
                  ->references('id_peminjaman')
                  ->on('peminjaman')
                  ->onDelete('cascade');

            $table->date('tanggal_kembali_aktual');
            $table->enum('kondisi', ['baik', 'rusak'])->default('baik');
            $table->text('catatan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pengembalian');
    }
};
