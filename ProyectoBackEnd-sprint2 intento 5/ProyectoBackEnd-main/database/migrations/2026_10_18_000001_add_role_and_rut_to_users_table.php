<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('rut', 12)->nullable()->unique()->after('name');
            $table->string('role', 20)->default('estudiante')->index()->after('email');
            $table->string('telefono', 20)->nullable()->after('role');
            $table->boolean('activo')->default(true)->after('telefono');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropUnique(['rut']);
            $table->dropIndex(['role']);
            $table->dropColumn(['rut', 'role', 'telefono', 'activo']);
        });
    }
};
