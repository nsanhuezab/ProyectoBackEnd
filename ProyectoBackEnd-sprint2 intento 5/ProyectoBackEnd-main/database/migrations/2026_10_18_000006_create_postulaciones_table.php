<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('postulaciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('oferta_id')->constrained()->cascadeOnDelete();
            $table->foreignId('estudiante_id')->constrained()->cascadeOnDelete();
            $table->text('mensaje')->nullable();
            $table->string('estado', 20)->default('pendiente')->index();
            $table->timestamps();

            $table->unique(['oferta_id', 'estudiante_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('postulaciones');
    }
};
