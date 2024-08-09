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
        Schema::create('payments', function (Blueprint $table) {
            $table->uuid('uuid')->primary();
            $table->uuid('transaction_uuid');
            $table->uuid('payment_method_uuid');
            $table->double('amount');
            $table->string('status');
            $table->timestamps();

            $table->foreign('transaction_uuid')
                ->references('uuid')
                ->on('transactions')
                ->onUpdate('cascade')
                ->onDelete('cascade');

            $table->foreign('payment_method_uuid')
                ->references('uuid')
                ->on('payment_methods')
                ->onUpdate('cascade')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
