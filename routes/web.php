<?php

use App\Http\Controllers\Admin;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\Frontend\BookingPageController;
use App\Http\Controllers\Frontend\HomeController;
use App\Http\Controllers\Frontend\PageController;
use App\Http\Controllers\Frontend\PropertyListingController;
use App\Http\Controllers\LocationSearchController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\Owner;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Tenant;
use Illuminate\Support\Facades\Route;

Route::get('/', HomeController::class)->name('home');
Route::get('/kost', [PropertyListingController::class, 'index'])->name('kost.index');
Route::get('/kost/{slug}', [PropertyListingController::class, 'show'])->name('kost.show');
Route::get('/cara-kerja', [PageController::class, 'howItWorks'])->name('pages.how');
Route::get('/syarat-ketentuan', [PageController::class, 'terms'])->name('pages.terms');
Route::get('/kebijakan-privasi', [PageController::class, 'privacy'])->name('pages.privacy');
Route::get('/api/locations', [LocationSearchController::class, 'search'])->name('api.locations');
Route::get('/api/cities', [LocationSearchController::class, 'cities'])->name('api.cities');
Route::get('/api/campuses', [LocationSearchController::class, 'campuses'])->name('api.campuses');
Route::get('/api/kost/nearby', [PropertyListingController::class, 'nearby'])->name('api.kost.nearby');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/login', [AuthenticatedSessionController::class, 'store']);
    Route::get('/register', [RegisteredUserController::class, 'create'])->name('register');
    Route::post('/register', [RegisteredUserController::class, 'store']);
    Route::get('/forgot-password', [PasswordResetLinkController::class, 'create'])->name('password.request');
    Route::post('/forgot-password', [PasswordResetLinkController::class, 'store'])->name('password.email');
    Route::get('/reset-password/{token}', [NewPasswordController::class, 'create'])->name('password.reset');
    Route::post('/reset-password', [NewPasswordController::class, 'store'])->name('password.update');
});

Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->middleware('auth')->name('logout');

Route::middleware(['auth', 'active'])->group(function () {
    Route::get('/favorit', [FavoriteController::class, 'index'])->name('favorites.index');
    Route::post('/kost/{property}/favorit', [FavoriteController::class, 'toggle'])->name('favorites.toggle');
    Route::get('/profil', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profil', [ProfileController::class, 'update'])->name('profile.update');

    // Notifications
    Route::get('/notifikasi', [NotificationController::class, 'index'])->name('notifications.index');
    Route::get('/notifikasi/{id}/baca', [NotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::post('/notifikasi/baca-semua', [NotificationController::class, 'markAllAsRead'])->name('notifications.read-all');

    // Dedicated Booking Page
    Route::get('/kost/{property:slug}/booking', [BookingPageController::class, 'create'])
        ->middleware('role:tenant')
        ->name('kost.booking.create');
    Route::post('/kost/{property}/booking', [Tenant\BookingController::class, 'store'])
        ->middleware('role:tenant')
        ->name('bookings.store');
    Route::post('/kost/{property:slug}/reviews', [Tenant\ReviewController::class, 'storeForProperty'])
        ->middleware('role:tenant')
        ->name('kost.reviews.store');
    Route::post('/kost/{property}/lapor', [Tenant\ReportController::class, 'store'])->name('reports.store');

    // Chat System
    Route::get('/chat', [ChatController::class, 'index'])->name('chat.index');
    Route::get('/chat/{conversation}', [ChatController::class, 'show'])->name('chat.show');
    Route::post('/chat/{property}/start', [ChatController::class, 'start'])->name('chat.start');
    Route::post('/chat/{conversation}/messages', [ChatController::class, 'sendMessage'])->name('chat.messages.send');
    Route::get('/api/chat/{conversation}/messages', [ChatController::class, 'getMessages'])->name('api.chat.messages');

    Route::prefix('dashboard')->middleware('role:tenant')->name('tenant.')->group(function () {
        Route::get('/', Tenant\DashboardController::class)->name('dashboard');
        Route::get('/bookings', [Tenant\BookingController::class, 'index'])->name('bookings.index');
        Route::get('/bookings/{booking}', [Tenant\BookingController::class, 'show'])->name('bookings.show');
        Route::post('/bookings/{booking}/cancel', [Tenant\BookingController::class, 'cancel'])->name('bookings.cancel');
        Route::get('/favorit', [FavoriteController::class, 'index'])->name('favorites.index');
        Route::get('/reviews', [Tenant\ReviewController::class, 'index'])->name('reviews.index');
        Route::post('/bookings/{booking}/review', [Tenant\ReviewController::class, 'store'])->name('reviews.store');
        Route::get('/profile', [ProfileController::class, 'edit'])->name('profile');
    });

    Route::prefix('owner')->middleware('role:owner')->name('owner.')->group(function () {
        Route::get('/', Owner\DashboardController::class)->name('dashboard');
        Route::get('/kost', [Owner\PropertyController::class, 'index'])->name('kost.index');
        Route::get('/kost/create', [Owner\PropertyController::class, 'create'])->name('kost.create');
        Route::post('/kost', [Owner\PropertyController::class, 'store'])->name('kost.store');
        Route::get('/kost/{kost}/edit', [Owner\PropertyController::class, 'edit'])->name('kost.edit');
        Route::put('/kost/{kost}', [Owner\PropertyController::class, 'update'])->name('kost.update');
        Route::delete('/kost/{kost}', [Owner\PropertyController::class, 'destroy'])->name('kost.destroy');
        Route::delete('/kost/{kost}/images/{image}', [Owner\PropertyController::class, 'destroyImage'])->name('kost.images.destroy');
        Route::post('/kost/{kost}/images/reorder', [Owner\PropertyController::class, 'reorderImages'])->name('kost.images.reorder');
        Route::get('/bookings', [Owner\BookingController::class, 'index'])->name('bookings.index');
        Route::get('/bookings/{booking}', [Owner\BookingController::class, 'show'])->name('bookings.show');
        Route::post('/bookings/{booking}/approve', [Owner\BookingController::class, 'approve'])->name('bookings.approve');
        Route::post('/bookings/{booking}/reject', [Owner\BookingController::class, 'reject'])->name('bookings.reject');
        Route::post('/bookings/{booking}/complete', [Owner\BookingController::class, 'complete'])->name('bookings.complete');
        Route::get('/tenants', [Owner\TenantController::class, 'index'])->name('tenants.index');
        Route::get('/reviews', [Owner\ReviewController::class, 'index'])->name('reviews.index');
        Route::get('/profile', [ProfileController::class, 'edit'])->name('profile');
    });

    Route::prefix('admin')->middleware('role:admin')->name('admin.')->group(function () {
        Route::get('/', Admin\DashboardController::class)->name('dashboard');
        Route::get('/kost', [Admin\PropertyController::class, 'index'])->name('kost.index');
        Route::get('/kost/{property}', [Admin\PropertyController::class, 'show'])->name('kost.show');
        Route::get('/kost/{property}/edit', [Admin\PropertyController::class, 'edit'])->name('kost.edit');
        Route::put('/kost/{property}', [Admin\PropertyController::class, 'update'])->name('kost.update');
        Route::delete('/kost/{property}', [Admin\PropertyController::class, 'destroy'])->name('kost.destroy');
        Route::post('/kost/{property}/suspend', [Admin\PropertyController::class, 'suspend'])->name('kost.suspend');
        Route::post('/kost/{property}/activate', [Admin\PropertyController::class, 'activate'])->name('kost.activate');
        Route::post('/kost/{property}/archive', [Admin\PropertyController::class, 'archive'])->name('kost.archive');
        Route::get('/verifikasi', [Admin\VerificationController::class, 'index'])->name('verifikasi.index');
        Route::get('/verifikasi/{property}', [Admin\VerificationController::class, 'show'])->name('verifikasi.show');
        Route::post('/verifikasi/{property}/approve', [Admin\VerificationController::class, 'approve'])->name('verifikasi.approve');
        Route::post('/verifikasi/{property}/reject', [Admin\VerificationController::class, 'reject'])->name('verifikasi.reject');
        Route::post('/verifikasi/{property}/revision', [Admin\VerificationController::class, 'requestRevision'])->name('verifikasi.revision');
        Route::get('/users', [Admin\UserController::class, 'index'])->name('users.index');
        Route::get('/users/{user}', [Admin\UserController::class, 'show'])->name('users.show');
        Route::post('/users/{user}/toggle', [Admin\UserController::class, 'toggle'])->name('users.toggle');
        Route::get('/bookings', [Admin\BookingController::class, 'index'])->name('bookings.index');
        Route::get('/bookings/{booking}', [Admin\BookingController::class, 'show'])->name('bookings.show');
        Route::post('/bookings/{booking}/note', [Admin\BookingController::class, 'note'])->name('bookings.note');
        Route::get('/reviews', [Admin\ReviewController::class, 'index'])->name('reviews.index');
        Route::post('/reviews/{review}/toggle', [Admin\ReviewController::class, 'toggle'])->name('reviews.toggle');
        Route::get('/reports', [Admin\ReportController::class, 'index'])->name('reports.index');
        Route::put('/reports/{report}', [Admin\ReportController::class, 'update'])->name('reports.update');
        Route::get('/categories', [Admin\CategoryController::class, 'index'])->name('categories.index');
        Route::post('/categories', [Admin\CategoryController::class, 'store'])->name('categories.store');
        Route::delete('/categories/{category}', [Admin\CategoryController::class, 'destroy'])->name('categories.destroy');
        Route::get('/facilities', [Admin\FacilityController::class, 'index'])->name('facilities.index');
        Route::post('/facilities', [Admin\FacilityController::class, 'store'])->name('facilities.store');
        Route::delete('/facilities/{facility}', [Admin\FacilityController::class, 'destroy'])->name('facilities.destroy');
        Route::get('/audit-logs', [Admin\AuditLogController::class, 'index'])->name('audit-logs.index');
        Route::get('/settings', [Admin\SettingController::class, 'edit'])->name('settings.edit');
        Route::put('/settings', [Admin\SettingController::class, 'update'])->name('settings.update');
        Route::get('/profile', [ProfileController::class, 'edit'])->name('profile');
    });
});
