<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('kerusakan', function (Blueprint $table) {
            $table->id('id_kerusakan');

            $table->unsignedBigInteger('id_pengembalian')->nullable();
            $table->foreign('id_pengembalian')
                  ->references('id_pengembalian')
                  ->on('pengembalian')
                  ->onDelete('set null');

            $table->unsignedBigInteger('id_alat');
            $table->foreign('id_alat')
                  ->references('id_alat')
                  ->on('alat')
                  ->onDelete('cascade');

            $table->text('deskripsi');
            $table->date('tanggal');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kerusakan');
    }
};
