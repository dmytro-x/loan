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
        Schema::create('credits', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->ulid('client_id');
            $table->string('name');
            $table->decimal('amount', 15, 2);
            $table->decimal('rate', 5, 2);
            $table->date('start_date');
            $table->date('end_date');
            $table->tinyInteger('is_approved')->nullable()->default(0);
            $table->json('rejection_reasons')->nullable();
            $table->timestamps();

            $table->foreign('client_id')
                ->references('id')
                ->on('clients')
                ->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('credits');
    }
};
