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
        if (! Schema::hasTable('companies')) {
            Schema::create('companies', function (Blueprint $table) {
                $table->id();
                $table->foreignId('owner_user_id')->nullable()->constrained('users')->nullOnDelete();
                $table->string('razon_social');
                $table->string('rut', 15)->unique();
                $table->string('rubro');
                $table->string('direccion');
                $table->string('sitio_web')->nullable();
                $table->string('contacto_nombre');
                $table->string('contacto_email');
                $table->string('telefono', 30)->nullable();
                $table->timestamps();
            });
        } elseif (! Schema::hasColumn('companies', 'owner_user_id')) {
            Schema::table('companies', function (Blueprint $table) {
                $table->foreignId('owner_user_id')->nullable()->constrained('users')->nullOnDelete();
            });
        }

        if (! Schema::hasColumn('offers', 'empresa_id')) {
            Schema::table('offers', function (Blueprint $table) {
                $table->foreignId('empresa_id')->nullable()->after('id')->constrained('companies')->nullOnDelete();
            });
        }
        Schema::table('offers', function (Blueprint $table) {
            $table->foreignId('created_by')->nullable()->after('empresa_id')->constrained('users')->nullOnDelete();
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
            $table->dropColumn([
                'area', 'perfil', 'modalidad', 'duracion_semanas', 'horario',
                'nivel_requerido', 'remuneracion', 'habilidades', 'approved_at', 'ubicacion',
            ]);
            $table->string('estado')->default('Disponible')->change();
        });

    }
};
