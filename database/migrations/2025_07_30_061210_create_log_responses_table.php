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
        Schema::create('log_responses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('log_id')->constrained()->cascadeOnDelete();
            $table->longText('response_content')->nullable();   // puede contener JSON o HTML pesado
            $table->timestamps();

            $table->index('created_at'); // para borrado por fecha
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('log_responses');
    }
};
