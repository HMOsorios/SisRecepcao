<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Crachás temporários emitidos para visitantes (Módulo 2.3).
     */
    public function up(): void
    {
        Schema::create('crachas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('visitante_id')->constrained('visitantes')->cascadeOnDelete();
            $table->string('codigo', 20)->unique();
            $table->string('status', 15)->default('ativo');
            $table->timestamp('emitido_em')->useCurrent();
            $table->timestamp('expira_em')->nullable();
            $table->timestamp('devolvido_em')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('crachas');
    }
};
