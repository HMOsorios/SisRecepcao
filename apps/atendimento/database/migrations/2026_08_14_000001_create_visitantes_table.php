<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Visitantes registrados na recepção (Módulo 2.3 — Crachás).
     */
    public function up(): void
    {
        Schema::create('visitantes', function (Blueprint $table) {
            $table->id();
            $table->string('nome');
            $table->string('documento', 30)->index();
            $table->string('email', 120)->nullable();
            $table->string('telefone', 25)->nullable();
            $table->string('genero', 1)->nullable();
            $table->date('data_nascimento')->nullable();
            $table->string('tipo_documento', 10)->default('CPF');
            $table->string('setor_destino')->nullable();
            $table->string('sala_destino')->nullable();
            $table->string('foto', 255)->nullable();
            $table->text('observacao')->nullable();
            $table->timestamp('purged_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('visitantes');
    }
};
