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
        Schema::create('fuel_cards', function (Blueprint $table) {
            $table->id();
            $table->foreignId('element_id')->nullable()->constrained()->nullOnDelete();
            $table->string('card_number')->nullable()->unique();
            $table->string('fuel_type')->nullable();
            $table->string('vehicle_type')->nullable();
            $table->decimal('capacity', 8, 2)->nullable();
            $table->string('classification')->nullable();
            $table->string('chassis_number')->nullable();
            $table->string('engine_capacity')->nullable();
            $table->integer('cylinders_count')->nullable();
            $table->text('notes')->nullable();
            $table->date('handover_date')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fuel_cards');
    }
};
