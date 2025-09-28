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
        Schema::create('skpi_data', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('nama_lengkap');
            $table->string('nim');
            $table->string('tempat_lahir');
            $table->date('tanggal_lahir');
            $table->string('nomor_ijazah');
            $table->date('tanggal_lulus');
            $table->string('gelar');
            $table->string('program_studi');
            $table->unsignedBigInteger('jurusan_id');
            $table->decimal('ipk', 3, 2);
            $table->text('prestasi_akademik')->nullable();
            $table->text('prestasi_non_akademik')->nullable();
            $table->text('organisasi')->nullable();
            $table->text('pengalaman_kerja')->nullable();
            $table->text('sertifikat_kompetensi')->nullable();
            $table->text('catatan_khusus')->nullable();
            $table->enum('status', ['draft', 'submitted', 'reviewed', 'approved', 'rejected'])->default('draft');
            $table->text('catatan_reviewer')->nullable();
            $table->unsignedBigInteger('reviewed_by')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            $table->unsignedBigInteger('approved_by')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();
            
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('jurusan_id')->references('id')->on('jurusans')->onDelete('restrict');
            $table->foreign('reviewed_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('approved_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('skpi_data');
    }
};