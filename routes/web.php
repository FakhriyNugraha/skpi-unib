<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\SkpiController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\SuperAdminController;
use App\Http\Controllers\DocumentController;
use Illuminate\Support\Facades\Route;

// Landing Page
Route::get('/', [HomeController::class, 'index'])->name('home');

// Auth
require __DIR__.'/auth.php';

// Authenticated routes
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [HomeController::class, 'dashboard'])->name('dashboard');

    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Documents
    Route::post('/skpi/{skpi}/documents', [DocumentController::class, 'upload'])->name('documents.upload');
    Route::get('/documents/{document}/download', [DocumentController::class, 'download'])->name('documents.download');
    Route::delete('/documents/{document}', [DocumentController::class, 'delete'])->name('documents.delete');
});

// User (Mahasiswa)
Route::middleware(['auth', 'role:user'])->group(function () {
    Route::get('/skpi', [SkpiController::class, 'index'])->name('skpi.index');
    Route::get('/skpi/create', [SkpiController::class, 'create'])->name('skpi.create');
    Route::post('/skpi', [SkpiController::class, 'store'])->name('skpi.store');
    Route::get('/skpi/{skpi}', [SkpiController::class, 'show'])->name('skpi.show');
    Route::get('/skpi/{skpi}/edit', [SkpiController::class, 'edit'])->name('skpi.edit');
    Route::put('/skpi/{skpi}', [SkpiController::class, 'update'])->name('skpi.update');
    Route::post('/skpi/{skpi}/submit', [SkpiController::class, 'submit'])->name('skpi.submit');
    Route::get('/skpi/{skpi}/print', [SkpiController::class, 'print'])->name('skpi.print');
});

// Admin
Route::middleware(['auth', 'role:admin,superadmin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/skpi', [AdminController::class, 'skpiList'])->name('skpi-list');
    Route::get('/skpi/{skpi}/review', [AdminController::class, 'reviewSkpi'])->name('review-skpi');
    Route::post('/skpi/{skpi}/approve', [AdminController::class, 'approveSkpi'])->name('approve-skpi');
    Route::get('/skpi/{skpi}/print', [AdminController::class, 'printSkpi'])->name('print-skpi');
});

// SuperAdmin
Route::middleware(['auth', 'role:superadmin'])->prefix('superadmin')->name('superadmin.')->group(function () {
    Route::get('/dashboard', [SuperAdminController::class, 'dashboard'])->name('dashboard');

    // Users
    Route::get('/users', [SuperAdminController::class, 'users'])->name('users');
    Route::get('/users/create', [SuperAdminController::class, 'createUser'])->name('create-user');
    Route::post('/users', [SuperAdminController::class, 'storeUser'])->name('store-user');
    Route::get('/users/{user}/edit', [SuperAdminController::class, 'editUser'])->name('edit-user');
    Route::put('/users/{user}', [SuperAdminController::class, 'updateUser'])->name('update-user');
    Route::delete('/users/{user}', [SuperAdminController::class, 'deleteUser'])->name('delete-user');

    // Jurusans
    Route::get('/jurusans', [SuperAdminController::class, 'jurusans'])->name('jurusans');
    Route::get('/jurusans/create', [SuperAdminController::class, 'createJurusan'])->name('create-jurusan');
    Route::post('/jurusans', [SuperAdminController::class, 'storeJurusan'])->name('store-jurusan');
    Route::get('/jurusans/{jurusan}/edit', [SuperAdminController::class, 'editJurusan'])->name('edit-jurusan');
    Route::put('/jurusans/{jurusan}', [SuperAdminController::class, 'updateJurusan'])->name('update-jurusan');
    Route::patch('/jurusans/{jurusan}/toggle-status', [SuperAdminController::class, 'toggleJurusanStatus'])->name('toggle-jurusan-status');
    Route::delete('/jurusans/{jurusan}', [SuperAdminController::class, 'destroyJurusan'])->name('delete-jurusan');
    Route::delete('/jurusan/{jurusan}', [SuperAdminController::class, 'destroyJurusan']); // kompatibilitas
    Route::get('/jurusans/{jurusan}/detail', [SuperAdminController::class, 'jurusanDetail'])->name('jurusan-detail');

    // SKPI monitoring & laporan
    Route::get('/all-skpi', [SuperAdminController::class, 'allSkpi'])->name('all-skpi');
    Route::get('/reports', [SuperAdminController::class, 'reports'])->name('reports');

    // SKPI detail & print (versi superadmin)
    Route::get('/skpi/{skpi}', [SuperAdminController::class, 'showSkpi'])->name('skpi.show');
    Route::get('/skpi/{skpi}/print', [SuperAdminController::class, 'printSkpi'])->name('skpi.print');

    // Approve/Reject oleh superadmin
    Route::post('/skpi/{skpi}/approve', [SuperAdminController::class, 'approveSkpi'])->name('approve-skpi');
    Route::post('/skpi/{skpi}/reject', [SuperAdminController::class, 'rejectSkpi'])->name('reject-skpi');
});
