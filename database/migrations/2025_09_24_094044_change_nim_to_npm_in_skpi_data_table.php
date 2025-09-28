<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('skpi_data', function (Blueprint $table) {
            $table->renameColumn('nim', 'npm');
        });
        
        Schema::table('skpi_data', function (Blueprint $table) {
            $table->string('npm', 9)->change();
        });
    }

    public function down(): void
    {
        Schema::table('skpi_data', function (Blueprint $table) {
            $table->renameColumn('npm', 'nim');
        });
        
        Schema::table('skpi_data', function (Blueprint $table) {
            $table->string('nim', 20)->change();
        });
    }
};