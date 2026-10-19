<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ofertas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('empresa_id')->constrained()->cascadeOnDelete();
            $table->string('titulo');
            $table->string('area')->nullable();
            $table->text('descripcion');
            $table->text('requisitos')->nullable();
            $table->string('modalidad', 20)->default('presencial');
            $table->unsignedSmallInteger('cupos')->default(1);
            $table->unsignedSmallInteger('horas')->default(320);
            $table->date('fecha_limite')->nullable();
            $table->string('estado', 20)->default('abierta')->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ofertas');
    }
};
