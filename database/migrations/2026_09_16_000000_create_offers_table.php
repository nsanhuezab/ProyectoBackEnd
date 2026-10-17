<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// NOTA: tabla provisoria para que el datatable de Ofertas funcione.
// Si otro compañero define la tabla real, reemplazar esta migración por la suya.
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('offers', function (Blueprint $table) {
            $table->id();
            $table->string('titulo');
            $table->string('empresa');
            $table->unsignedInteger('vacantes')->default(1);
            $table->string('estado')->default('Disponible');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('offers');
    }
};
