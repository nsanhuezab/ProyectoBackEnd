<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('practicas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('estudiante_id')->constrained()->cascadeOnDelete();
            $table->foreignId('empresa_id')->constrained()->restrictOnDelete();
            $table->foreignId('oferta_id')->nullable()->constrained()->nullOnDelete();
            $table->string('supervisor');
            $table->date('fecha_inicio');
            $table->date('fecha_termino');
            $table->unsignedSmallInteger('horas_totales')->default(320);
            $table->string('estado', 20)->default('pendiente')->index();
            $table->text('observaciones')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('practicas');
    }
};
