<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Outbox de senhas provisórias — operação resiliente/off-line (Seção 9.1).
     * Quando o NovoSGA está indisponível, o totem emite senha local e um job
     * assíncrono reenvia via POST /api/distribui ao voltar a conectividade.
     */
    public function up(): void
    {
        Schema::create('senha_outbox', function (Blueprint $table) {
            $table->id();
            $table->string('senha_provisoria', 20)->unique();
            $table->unsignedBigInteger('unidade_id')->nullable();
            $table->unsignedBigInteger('servico_id')->nullable();
            $table->unsignedBigInteger('prioridade_id')->nullable();
            $table->json('cliente')->nullable();
            $table->json('metadata')->nullable();
            $table->string('status', 15)->default('pendente');
            $table->unsignedInteger('tentativas')->default(0);
            $table->timestamp('data_chegada')->useCurrent();
            $table->timestamp('enviado_em')->nullable();
            $table->string('erro', 255)->nullable();
            $table->timestamps();

            $table->index(['status', 'data_chegada']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('senha_outbox');
    }
};
