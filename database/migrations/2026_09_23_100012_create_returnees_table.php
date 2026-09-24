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
            $table->enum('status_at_defect', ['conscript', 'volunteer'])->nullable()->comment('Status at the time of defection');
            $table->date('defect_date');
            $table->date('regime_volunteer_date')->nullable()->comment('Volunteer date with the former regime');
            $table->string('rank_at_defect');
            $table->string('id_at_defect');
            $table->string('defect_agency')->nullable()->comment('Agency defected from');
            $table->string('last_place_before_defect')->nullable()->comment('Last place before defection');
            $table->string('last_promotion_degree')->nullable()->comment('Last promotion degree');
            $table->date('last_promotion_date')->nullable()->comment('Last promotion date');
            $table->date('hts_volunteer_date')->nullable()->comment('HTS military wing volunteer date');
            $table->string('hts_old_workplace')->nullable()->comment('Former HTS workplace');
            $table->date('ssg_interior_volunteer_date')->nullable()->comment('SSG (Interior) volunteer date');
            $table->string('ssg_military_number')->nullable()->comment('SSG military number');
            $table->string('ssg_old_workplace')->nullable()->comment('Former SSG workplace');
            $table->date('sig_police_volunteer_date')->nullable()->comment('SIG (Free Police) volunteer date');
            $table->string('sig_old_workplace')->nullable()->comment('Former SIG workplace');
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
