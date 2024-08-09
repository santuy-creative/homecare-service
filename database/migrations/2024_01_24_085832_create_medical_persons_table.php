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
        Schema::create('medical_persons', function (Blueprint $table) {
            $table->uuid('uuid')->primary();
            $table->uuid('user_uuid');
            $table->string('nik');
            $table->string('firstname');
            $table->string('lastname');
            $table->date('birthdate');
            $table->string('phone', 50);
            $table->string('bio');
            $table->string('specialization');
            $table->string('license_number');
            $table->timestamps();

            $table->foreign('user_uuid')
                ->references('uuid')
                ->on('users')
                ->onUpdate('cascade')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('medical_persons');
    }
};
