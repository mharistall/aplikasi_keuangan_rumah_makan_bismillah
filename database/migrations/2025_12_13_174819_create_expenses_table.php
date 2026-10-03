<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('expenses', function (Blueprint $table) {
            $table->id();
            $table->dateTime('expense_date')->comment('Tanggal pengeluaran dicatat');
            $table->string('description', 255)->comment('Deskripsi rinci pengeluaran');
            $table->decimal('amount', 12, 2)->comment('Jumlah uang yang dikeluarkan');

            $table->foreignId('category_id')
                ->constrained('kategori')
                ->onDelete('restrict')
                ->comment('FK ke kategori pengeluaran');

            $table->foreignId('user_id')
                ->constrained('users')
                ->onDelete('restrict')
                ->comment('User yang mencatat pengeluaran');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('expenses');
    }
};
