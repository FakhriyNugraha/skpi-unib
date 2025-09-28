<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->renameColumn('nim', 'npm');
        });
        
        // Update panjang maksimal NPM
        Schema::table('users', function (Blueprint $table) {
            $table->string('npm', 9)->nullable()->unique()->change();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->renameColumn('npm', 'nim');
        });
        
        Schema::table('users', function (Blueprint $table) {
            $table->string('nim', 20)->nullable()->unique()->change();
        });
    }
};