<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\ClientRentalController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\Owner\BookingRequestController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PropertyController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/properties/{property}', [PropertyController::class, 'show'])->name('property.show');
Route::get('/register', [RegisterController::class, 'index'])->name('register');
Route::get('/login', [LoginController::class, 'index'])->name('login');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::post('/properties/{property}/book', [PropertyController::class, 'book'])->name('property.book');
    Route::post('/properties/{property}/favorite', [FavoriteController::class, 'store'])->name('favorites.store');
    Route::delete('/properties/{property}/favorite', [FavoriteController::class, 'destroy'])->name('favorites.destroy');
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllAsRead'])->name('notifications.read-all');
    Route::delete('/notifications/{notification}', [NotificationController::class, 'destroy'])->name('notifications.destroy');
    Route::delete('/notifications', [NotificationController::class, 'clear'])->name('notifications.clear');
    Route::get('/owner/booking-requests', [BookingRequestController::class, 'index'])->name('owner.booking-requests');
    Route::post('/owner/booking-requests/{booking}/approve', [BookingRequestController::class, 'approve'])
        ->name('owner.booking-requests.approve');
    Route::post('/owner/booking-requests/{booking}/reject', [BookingRequestController::class, 'reject'])
        ->name('owner.booking-requests.reject');
    Route::post('/owner/booking-requests/{booking}/cancel-confirmed', [BookingRequestController::class, 'cancelConfirmed'])
        ->name('owner.booking-requests.cancel-confirmed');
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::get('/my-rentals', [ClientRentalController::class, 'index'])->name('profile.my-rentals');
    Route::get('/favorites', [FavoriteController::class, 'index'])->name('profile.favorites');
    Route::post('/my-rentals/{booking}/request-cancellation', [ClientRentalController::class, 'requestCancellation'])
        ->name('profile.my-rentals.request-cancellation');
    Route::post('/my-rentals/{booking}/review', [ClientRentalController::class, 'submitReview'])
        ->name('profile.my-rentals.review');
    Route::get('/create-property', [PropertyController::class, 'create'])->name('property.create');
    Route::get('/edit-property/{property}', [PropertyController::class, 'edit'])
        ->name('property.edit');
    Route::get('/my-properties', [PropertyController::class, 'myProperties'])
        ->name('profile.my-properties');
});
