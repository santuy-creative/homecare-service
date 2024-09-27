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
        Schema::create('transaction_details', function (Blueprint $table) {
            $table->uuid('uuid')->primary();
            $table->uuid('transaction_uuid');
            $table->string('name');
            $table->string('status');
            $table->integer('quantity');
            $table->double('unit_price');
            $table->double('sub_total');
            $table->string('item_type');
            $table->uuid('item_uuid');
            $table->timestamps();

            $table->foreign('transaction_uuid')
                ->references('uuid')
                ->on('transactions')
                ->onUpdate('cascade')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transaction_details');
    }
};
