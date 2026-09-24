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
        Schema::create('technologies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('element_id')->nullable()->constrained()->nullOnDelete();
            $table->string('category_type')->nullable();
            $table->string('readiness_status')->nullable();
            $table->string('asset_name')->nullable();
            $table->string('manufacturer')->nullable();
            $table->string('origin_country')->nullable();
            $table->string('model')->nullable();
            $table->string('manufacturing_year')->nullable();
            $table->string('source')->nullable();
            $table->string('evaluation')->nullable();
            $table->string('origin_number')->nullable()->unique();
            $table->string('color')->nullable();
            $table->string('cpu_type')->nullable();
            $table->string('ram_size')->nullable();
            $table->string('storage_details')->nullable();
            $table->string('screen_size')->nullable();
            $table->string('gpu_details')->nullable();
            $table->text('accessories')->nullable();
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
        Schema::dropIfExists('technologies');
    }
};
