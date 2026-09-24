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
        Schema::create('disciplinary_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('element_id')->constrained()->cascadeOnDelete();
            $table->enum('record_type', ['punishment', 'reward', 'warning']);
            $table->string('decision_number');
            $table->string('action_type');
            $table->date('decision_date');
            $table->text('notes')->nullable();
            $table->string('document_path')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('disciplinary_records');
    }
};
