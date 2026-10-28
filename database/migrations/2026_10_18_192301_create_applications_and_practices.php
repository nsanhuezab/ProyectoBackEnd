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
        if (! Schema::hasTable('applications')) {
            Schema::create('applications', function (Blueprint $table) {
                $table->id();
                $table->foreignId('student_id')->constrained()->cascadeOnDelete();
                $table->foreignId('offer_id')->constrained()->cascadeOnDelete();
                $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
                $table->string('status')->default('Postulado');
                $table->string('cv_path')->nullable();
                $table->text('motivation');
                $table->decimal('matching_score', 5, 2)->default(0);
                $table->timestamp('decision_at')->nullable();
                $table->timestamps();
                $table->unique(['student_id', 'offer_id']);
            });
        } else {
            Schema::table('applications', function (Blueprint $table) {
                if (! Schema::hasColumn('applications', 'reviewed_by')) {
                    $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
                }
                if (! Schema::hasColumn('applications', 'matching_score')) {
                    $table->decimal('matching_score', 5, 2)->default(0);
                }
                if (! Schema::hasColumn('applications', 'decision_at')) {
                    $table->timestamp('decision_at')->nullable();
                }
            });
        }

        if (! Schema::hasTable('practices')) {
            Schema::create('practices', function (Blueprint $table) {
                $table->id();
                $table->foreignId('application_id')->unique()->constrained()->cascadeOnDelete();
                $table->foreignId('assigned_by')->nullable()->constrained('users')->nullOnDelete();
                $table->date('fecha_inicio')->nullable();
                $table->date('fecha_termino')->nullable();
                $table->string('estado')->default('Asignado');
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('practices');
    }
};
