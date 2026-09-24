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
        Schema::create('elements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('agency_id')->constrained();
            $table->foreignId('sub_agency_id')->nullable()->constrained();
            $table->foreignId('job_role_id')->nullable()->constrained()->nullOnDelete();
            $table->string('work_location')->nullable();
            $table->date('join_date')->nullable();
            $table->string('military_number')->nullable();
            $table->string('self_number')->unique();
            $table->string('course_number')->nullable();
            $table->string('full_name');
            $table->date('birth_date')->nullable();
            $table->string('grandfather_name')->nullable();
            $table->string('mother_name')->nullable();
            $table->string('nationality')->default('سوري');
            $table->string('religion')->nullable();
            $table->string('sect')->nullable();
            $table->string('national_id')->nullable()->unique();
            $table->enum('gender', ['ذكر', 'أنثى'])->nullable();
            $table->string('registry_number')->nullable();
            $table->string('registry_place')->nullable();
            $table->string('residence_place')->nullable();
            $table->string('residence_address')->nullable();
            $table->string('phone_number')->nullable();
            $table->enum('marital_status', ['أعزب', 'متزوج/ة', 'أرمل/ة', 'مطلق/ة'])->nullable();
            $table->integer('wives_count')->nullable();
            $table->integer('children_count')->nullable();
            $table->boolean('food_allowance')->default(false);
            $table->boolean('transport_allowance')->default(false);
            $table->boolean('is_active')->default(false);
            $table->enum('work_nature', ['إداري', 'مبيت', 'إداري مقيم', 'ميداني'])->nullable();
            $table->string('bank_account_number', 16)->nullable();
            $table->decimal('height', 5, 2)->nullable();
            $table->decimal('weight', 5, 2)->nullable();
            $table->string('skin_color')->nullable();
            $table->string('eye_color')->nullable();
            $table->text('distinguishing_marks')->nullable();
            $table->integer('shoe_size')->nullable();
            $table->string('suit_size')->nullable();
            $table->enum('education_level', ['أمي', 'ابتدائي', 'إعدادي', 'ثانوي', 'معهد متوسط', 'جامعي', 'دراسات عليا'])->nullable();
            $table->string('university_major')->nullable();
            $table->string('study_year')->nullable();
            $table->text('cv_summary')->nullable();
            $table->text('experience_summary')->nullable();
            $table->text('academic_courses')->nullable();
            $table->text('military_courses')->nullable();
            $table->string('status')->nullable()->default('نشط');
            $table->string('health_status')->nullable()->default('سليم');
            $table->decimal('residence_lat', 10, 7)->nullable()->comment('خط عرض السكن');
            $table->decimal('residence_lng', 10, 7)->nullable()->comment('خط طول السكن');
            $table->decimal('work_lat', 10, 7)->nullable()->comment('خط عرض العمل');
            $table->decimal('work_lng', 10, 7)->nullable()->comment('خط طول العمل');
            $table->decimal('distance_km', 8, 2)->nullable()->comment('المسافة بالكيلومتر');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('elements');
    }
};
