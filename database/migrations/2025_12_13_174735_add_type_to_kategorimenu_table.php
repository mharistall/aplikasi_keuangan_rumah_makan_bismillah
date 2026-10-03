<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('kategori', function (Blueprint $table) {
            $table->enum('type', ['pemasukan', 'pengeluaran'])
                ->default('pemasukan')
                ->after('name')
                ->comment('Tipe kategori: pemasukan (menu) atau pengeluaran (biaya)');
        });
    }

    public function down(): void
    {
        Schema::table('kategori', function (Blueprint $table) {
            $table->dropColumn('type');
        });
    }
};
