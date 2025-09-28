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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('nim')->nullable()->unique(); // untuk mahasiswa
            $table->string('nip')->nullable()->unique(); // untuk admin/superadmin
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->enum('role', ['user', 'admin', 'superadmin'])->default('user');
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->unsignedBigInteger('jurusan_id')->nullable();
            $table->string('phone')->nullable();
            $table->text('address')->nullable();
            $table->rememberToken();
            $table->timestamps();
            
            // Index untuk jurusan_id, foreign key akan ditambahkan setelah tabel jurusans dibuat
            $table->index('jurusan_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};