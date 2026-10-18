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
        Schema::create('companies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('owner_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('razon_social');
            $table->string('rut', 12)->unique();
            $table->string('rubro');
            $table->string('direccion');
            $table->string('sitio_web')->nullable();
            $table->string('contacto_nombre');
            $table->string('contacto_correo');
            $table->string('contacto_telefono', 30);
            $table->timestamps();
        });

        Schema::table('offers', function (Blueprint $table) {
            $table->foreignId('company_id')->nullable()->after('id')->constrained()->nullOnDelete();
            $table->foreignId('created_by')->nullable()->after('company_id')->constrained('users')->nullOnDelete();
            $table->foreignId('approved_by')->nullable()->after('created_by')->constrained('users')->nullOnDelete();
            $table->string('area')->nullable();
            $table->text('perfil')->nullable();
            $table->string('nivel_requerido')->nullable();
            $table->string('modalidad', 20)->nullable();
            $table->unsignedSmallInteger('duracion_semanas')->nullable();
            $table->string('horario')->nullable();
            $table->decimal('remuneracion', 10, 2)->nullable();
            $table->json('habilidades')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->string('ubicacion')->nullable();
            $table->string('estado')->default('Pendiente')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('offers', function (Blueprint $table) {
            $table->dropConstrainedForeignId('approved_by');
            $table->dropConstrainedForeignId('created_by');
            $table->dropConstrainedForeignId('company_id');
            $table->dropColumn([
                'area', 'perfil', 'modalidad', 'duracion_semanas', 'horario',
                'nivel_requerido', 'remuneracion', 'habilidades', 'approved_at', 'ubicacion',
            ]);
            $table->string('estado')->default('Disponible')->change();
        });

        Schema::dropIfExists('companies');
    }
};
