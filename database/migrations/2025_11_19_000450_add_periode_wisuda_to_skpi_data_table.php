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
        if (!Schema::hasColumn('skpi_data', 'periode_wisuda')) {
            Schema::table('skpi_data', function (Blueprint $table) {
                $table->integer('periode_wisuda')->nullable()->after('tanggal_lulus')->index();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('skpi_data', function (Blueprint $table) {
            $table->dropIndex(['periode_wisuda']); // Drop index first
            $table->dropColumn('periode_wisuda');
        });
    }
};
