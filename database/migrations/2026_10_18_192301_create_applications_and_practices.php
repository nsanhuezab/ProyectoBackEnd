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
        Schema::create('applications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->foreignId('offer_id')->constrained()->cascadeOnDelete();
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('estado')->default('Postulado');
            $table->string('cv_path');
            $table->text('carta_motivacion');
            $table->decimal('matching_score', 5, 2)->default(0);
            $table->timestamp('decision_at')->nullable();
            $table->timestamps();
            $table->unique(['student_id', 'offer_id']);
        });

        Schema::create('practices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('application_id')->unique()->constrained()->cascadeOnDelete();
            $table->foreignId('assigned_by')->nullable()->constrained('users')->nullOnDelete();
            $table->date('fecha_inicio')->nullable();
            $table->date('fecha_termino')->nullable();
            $table->string('estado')->default('Asignado');
            $table->decimal('calificacion_final', 4, 2)->nullable();
            $table->boolean('contratado')->default(false);
            $table->timestamp('finalizada_at')->nullable();
            $table->timestamps();
        });

        Schema::create('practice_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('practice_id')->constrained()->cascadeOnDelete();
            $table->foreignId('author_id')->constrained('users')->cascadeOnDelete();
            $table->date('fecha');
            $table->text('avance');
            $table->json('competencias')->nullable();
            $table->timestamps();
            $table->unique(['practice_id', 'fecha']);
        });

        Schema::create('evaluations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('practice_id')->constrained()->cascadeOnDelete();
            $table->foreignId('evaluator_id')->constrained('users')->cascadeOnDelete();
            $table->string('tipo', 20);
            $table->decimal('calificacion', 4, 2);
            $table->json('competencias')->nullable();
            $table->text('comentarios')->nullable();
            $table->timestamps();
            $table->unique(['practice_id', 'tipo']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('evaluations');
        Schema::dropIfExists('practice_logs');
        Schema::dropIfExists('practices');
        Schema::dropIfExists('applications');
    }
};
