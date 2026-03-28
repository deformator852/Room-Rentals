<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\HomeController;
use App\Livewire\Register;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/register', Register::class)->name('register.index');
Route::get('/login', [LoginController::class, 'index'])->name('login.index');
