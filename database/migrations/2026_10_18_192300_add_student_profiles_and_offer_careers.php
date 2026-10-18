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
        Schema::table('students', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->unique()->after('id_student')->constrained()->cascadeOnDelete();
            $table->foreignId('career_id')->nullable()->after('user_id')->constrained('careers')->nullOnDelete();
            $table->json('habilidades')->nullable();
        });

        Schema::create('career_offer', function (Blueprint $table) {
            $table->foreignId('career_id')->constrained()->cascadeOnDelete();
            $table->foreignId('offer_id')->constrained()->cascadeOnDelete();
            $table->primary(['career_id', 'offer_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('career_offer');
        Schema::table('students', function (Blueprint $table) {
            $table->dropConstrainedForeignId('career_id');
            $table->dropConstrainedForeignId('user_id');
            $table->dropColumn('habilidades');
        });
    }
};
