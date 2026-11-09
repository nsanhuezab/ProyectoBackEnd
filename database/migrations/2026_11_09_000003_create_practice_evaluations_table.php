<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Rúbricas de evaluación del supervisor y del coordinador (enunciado, punto 4).
     */
    public function up(): void
    {
        Schema::create('practice_evaluations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('practice_id')->constrained()->cascadeOnDelete();
            $table->string('evaluator_role', 20); // supervisor | coordinador
            $table->foreignId('evaluator_id')->nullable()->constrained('users')->nullOnDelete();
            $table->json('scores');
            $table->decimal('grade', 2, 1);
            $table->text('comments')->nullable();
            $table->timestamps();
            $table->unique(['practice_id', 'evaluator_role']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('practice_evaluations');
    }
};
