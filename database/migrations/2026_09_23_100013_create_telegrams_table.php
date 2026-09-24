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
        Schema::create('telegrams', function (Blueprint $table) {
            $table->id();
            $table->foreignId('element_id')->nullable()->constrained()->cascadeOnDelete();
            $table->string('telegram_number');
            $table->date('telegram_date');
            $table->text('telegram_type');
            $table->foreignId('old_agency_id')->nullable()->constrained('agencies');
            $table->foreignId('old_sub_agency_id')->nullable()->constrained('sub_agencies');
            $table->foreignId('new_agency_id')->nullable()->constrained('agencies');
            $table->foreignId('new_sub_agency_id')->nullable()->constrained('sub_agencies');
            $table->string('document_path')->nullable();
            $table->text('notes')->nullable();
            $table->text('additional_notes')->nullable()->comment('ملاحظات إضافية');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('telegrams');
    }
};
