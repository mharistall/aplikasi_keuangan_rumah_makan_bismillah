<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transaksi', function (Blueprint $table) {
            $table->id();
            $table->dateTime('transaction_date');
            $table->string('customer_number', 50)->nullable();
            $table->decimal('total_amount', 12, 2);
            $table->foreignId('user_id')->constrained('users')->onDelete('restrict');
            $table->timestamps();
        });

        Schema::create('detailtransaksi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('transaction_id')->constrained('transaksi')->onDelete('cascade');
            $table->foreignId('menu_id')->constrained('menu')->onDelete('restrict');
            $table->integer('quantity');
            $table->decimal('subtotal', 10, 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('detailtransaksi');
        Schema::dropIfExists('transaksi');
    }
};
