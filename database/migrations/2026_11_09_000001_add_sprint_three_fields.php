<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // RF-05 / RF-06: año que cursa, promedio y validación del perfil por el coordinador.
        Schema::table('students', function (Blueprint $table) {
            $table->unsignedTinyInteger('year_level')->nullable()->after('nivel_avance');
            $table->decimal('gpa', 2, 1)->nullable()->after('year_level');
            $table->timestamp('validated_at')->nullable()->after('habilidades');
            $table->foreignId('validated_by')->nullable()->after('validated_at')->constrained('users')->nullOnDelete();
        });

        // RF-07: promedio mínimo exigido por la oferta (opcional).
        Schema::table('offers', function (Blueprint $table) {
            $table->decimal('min_gpa', 2, 1)->nullable()->after('nivel_requerido');
        });

        // Punto 4 y 5: nota final ponderada y si el estudiante fue contratado (empleabilidad).
        Schema::table('practices', function (Blueprint $table) {
            $table->decimal('final_grade', 2, 1)->nullable()->after('estado');
            $table->boolean('hired')->nullable()->after('final_grade');
        });
    }

    public function down(): void
    {
        Schema::table('practices', function (Blueprint $table) {
            $table->dropColumn(['final_grade', 'hired']);
        });

        Schema::table('offers', function (Blueprint $table) {
            $table->dropColumn('min_gpa');
        });

        Schema::table('students', function (Blueprint $table) {
            $table->dropConstrainedForeignId('validated_by');
            $table->dropColumn(['year_level', 'gpa', 'validated_at']);
        });
    }
};
