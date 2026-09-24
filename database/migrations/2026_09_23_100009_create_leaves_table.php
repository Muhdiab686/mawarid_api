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
        Schema::create('leaves', function (Blueprint $table) {
            $table->id();
            $table->foreignId('element_id')->constrained()->cascadeOnDelete();
            $table->enum('leave_type', ['إجازة طبية', 'إجازة إدارية', 'اجازة بدون راتب', 'اجازة دراسية', 'اجازة خارجية', 'اجازة امومة']);
            $table->string('leave_number');
            $table->date('start_date');
            $table->date('end_date');
            $table->string('document_path')->nullable();
            $table->boolean('is_completed')->default(false);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('leaves');
    }
};
