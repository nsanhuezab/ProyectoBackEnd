<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('empresas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->unique()->constrained()->nullOnDelete();
            $table->string('razon_social');
            $table->string('rut', 12)->unique();
            $table->string('giro')->nullable();
            $table->string('contacto_nombre')->nullable();
            $table->string('email');
            $table->string('telefono', 20)->nullable();
            $table->string('direccion')->nullable();
            $table->string('estado', 20)->default('activa');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('empresas');
    }
};
