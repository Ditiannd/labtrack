<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('alat', function (Blueprint $table) {
            $table->string('kategori', 100)->nullable()->after('nama_alat');
        });
    }
    public function down(): void
    {
        Schema::table('alat', function (Blueprint $table) {
            $table->dropColumn('kategori');
        });
    }
};
