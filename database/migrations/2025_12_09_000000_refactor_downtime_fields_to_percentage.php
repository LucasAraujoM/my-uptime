<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('monitors', function (Blueprint $table) {
            // Drop the old downtime_12h column (it was in hours)
            $table->dropColumn('downtime_12h');
        });

        Schema::table('monitors', function (Blueprint $table) {
            // Add downtime percentage fields for all periods
            $table->decimal('downtime_12h', 5, 2)->default(0)->comment('Downtime percentage for the last 12 hours');
            $table->decimal('downtime_24h', 5, 2)->default(0)->comment('Downtime percentage for the last 24 hours');
            $table->decimal('downtime_7d', 5, 2)->default(0)->comment('Downtime percentage for the last 7 days');
            $table->decimal('downtime_30d', 5, 2)->default(0)->comment('Downtime percentage for the last 30 days');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('monitors', function (Blueprint $table) {
            $table->dropColumn(['downtime_12h', 'downtime_24h', 'downtime_7d', 'downtime_30d']);
        });

        Schema::table('monitors', function (Blueprint $table) {
            // Restore the old downtime_12h column (in hours)
            $table->decimal('downtime_12h', 5, 2)->default(0)->comment('Downtime hours for the last 12 hours');
        });
    }
};
