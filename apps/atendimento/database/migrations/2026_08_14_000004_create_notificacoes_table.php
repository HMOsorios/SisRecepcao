<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Notificações de chegada de visitante ao setor de destino (Seção 2.6).
     * Alimentadas pela ponte Mercure → console do setor.
     */
    public function up(): void
    {
        Schema::create('notificacoes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('atendimento_id')->nullable()->index();
            $table->unsignedBigInteger('departamento_id')->nullable()->index();
            $table->string('tipo', 30)->default('visita');
            $table->json('payload')->nullable();
            $table->timestamp('lida_em')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notificacoes');
    }
};
