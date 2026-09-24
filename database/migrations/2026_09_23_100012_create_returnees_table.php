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
        Schema::create('returnees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('element_id')->constrained()->cascadeOnDelete();
            $table->enum('status_at_defect', ['مجند', 'متطوع'])->nullable()->comment('الحالة عند الانشقاق');
            $table->date('defect_date');
            $table->date('regime_volunteer_date')->nullable()->comment('تاريخ التطوع لدى النظام البائد');
            $table->string('rank_at_defect');
            $table->string('id_at_defect');
            $table->string('defect_agency')->nullable()->comment('الجهة التي انشق عنها');
            $table->string('last_place_before_defect')->nullable()->comment('آخر مكان قبل الانشقاق');
            $table->string('last_promotion_degree')->nullable()->comment('آخر درجة ترفيع');
            $table->date('last_promotion_date')->nullable()->comment('آخر تاريخ ترفيع');
            $table->date('hts_volunteer_date')->nullable()->comment('تاريخ التطوع بالجناح العسكري للهيئة');
            $table->string('hts_old_workplace')->nullable()->comment('مكان العمل القديم التابع للهيئة');
            $table->date('ssg_interior_volunteer_date')->nullable()->comment('تاريخ التطوع لدى حكومة الإنقاذ (الداخلية)');
            $table->string('ssg_military_number')->nullable()->comment('الرقم العسكري في حكومة الإنقاذ');
            $table->string('ssg_old_workplace')->nullable()->comment('مكان العمل القديم في حكومة الإنقاذ');
            $table->date('sig_police_volunteer_date')->nullable()->comment('تاريخ التطوع في الحكومة المؤقتة (الشرطة الحرة)');
            $table->string('sig_old_workplace')->nullable()->comment('مكان العمل القديم في الحكومة المؤقتة');
            $table->date('return_date');
            $table->string('rank_after_return');
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('returnees');
    }
};
