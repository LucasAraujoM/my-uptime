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
        Schema::table('monitors', function (Blueprint $table) {
            $table->decimal('uptime_12h', 5, 2)->default(0)->comment('Uptime percentage for the last 12 hours');
            $table->decimal('uptime_24h', 5, 2)->default(0)->comment('Uptime percentage for the last 24 hours');
            $table->decimal('uptime_7d', 5, 2)->default(0)->comment('Uptime percentage for the last 7 days');
            $table->decimal('uptime_30d', 5, 2)->default(0)->comment('Uptime percentage for the last 30 days');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('monitors', function (Blueprint $table) {
            $table->dropColumn(['uptime_12h', 'uptime_24h', 'uptime_7d', 'uptime_30d']);
        });
    }
};
