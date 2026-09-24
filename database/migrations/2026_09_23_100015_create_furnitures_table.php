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
        Schema::create('furnitures', function (Blueprint $table) {
            $table->id();
            $table->foreignId('element_id')->nullable()->constrained()->nullOnDelete();
            $table->string('level_2')->nullable();
            $table->string('level_3')->nullable();
            $table->string('level_4')->nullable();
            $table->string('category_type')->nullable();
            $table->string('asset_name')->nullable();
            $table->decimal('current_value', 15, 2)->nullable();
            $table->string('technical_condition')->nullable();
            $table->string('color')->nullable();
            $table->string('manufacturer')->nullable();
            $table->string('model')->nullable();
            $table->integer('quantity')->default(1);
            $table->string('origin_country')->nullable();
            $table->string('dimensions')->nullable();
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
        Schema::dropIfExists('furnitures');
    }
};
