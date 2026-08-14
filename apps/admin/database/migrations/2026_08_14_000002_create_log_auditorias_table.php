<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Auditoria de ações administrativas sensíveis (Seção 8.8 — Auditoria).
     */
    public function up(): void
    {
        Schema::create('log_auditorias', function (Blueprint $table) {
            $table->id();
            $table->string('usuario_id', 100)->nullable()->index();
            $table->string('usuario_nome', 150)->nullable();
            $table->string('acao', 100)->index();
            $table->json('detalhes')->nullable();
            $table->string('ip', 45)->nullable();
            $table->string('user_agent', 255)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('log_auditorias');
    }
};
