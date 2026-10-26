<?php

use App\Http\Controllers\Admin\AuditLogController;
use App\Http\Controllers\Admin\EventController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MembershipApplicationController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PublicEventController;
use App\Http\Controllers\PublicPageController;
use Illuminate\Support\Facades\Route;

Route::get('/', [PublicPageController::class, 'home'])->name('public.home');

Route::get('events', [PublicEventController::class, 'index'])->name('public.events.index');
Route::get('events/{event}', [PublicEventController::class, 'show'])->name('public.events.show');
Route::get('join', [MembershipApplicationController::class, 'create'])->name('public.join');
Route::post('join', [MembershipApplicationController::class, 'store'])
    ->middleware('throttle:5,1')
    ->name('public.join.store');

Route::middleware('guest')->group(function () {
    Route::get('login', [LoginController::class, 'create'])->name('login');
    Route::post('login', [LoginController::class, 'store'])->name('login.store');

    Route::get('forgot-password', [ForgotPasswordController::class, 'create'])->name('password.request');
    Route::post('forgot-password', [ForgotPasswordController::class, 'store'])->name('password.email');

    Route::get('reset-password/{token}', [ResetPasswordController::class, 'create'])->name('password.reset');
    Route::post('reset-password', [ResetPasswordController::class, 'store'])->name('password.store');
});

Route::post('logout', [LoginController::class, 'destroy'])
    ->middleware('auth')
    ->name('logout');

Route::middleware(['auth', 'active', 'password.changed'])
    ->prefix('portal')
    ->group(function () {
        Route::get('/', DashboardController::class)->name('dashboard');

        Route::get('profile', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('profile', [ProfileController::class, 'update'])->name('profile.update');
        Route::put('profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password.update');

        Route::prefix('admin')->name('admin.')->group(function () {
            Route::prefix('events')->name('events.')->group(function () {
                Route::get('/', [EventController::class, 'index'])
                    ->middleware('permission:events.manage')
                    ->name('index');

                Route::middleware('permission:events.manage')->group(function () {
                    Route::get('create', [EventController::class, 'create'])->name('create');
                    Route::post('/', [EventController::class, 'store'])->name('store');
                    Route::get('{event}/edit', [EventController::class, 'edit'])->name('edit');
                    Route::patch('{event}', [EventController::class, 'update'])->name('update');
                    Route::post('{event}/publish', [EventController::class, 'publish'])->name('publish');
                    Route::post('{event}/unpublish', [EventController::class, 'unpublish'])->name('unpublish');
                    Route::delete('{event}', [EventController::class, 'destroy'])->name('destroy');
                });
            });

            Route::get('users', [UserController::class, 'index'])
                ->middleware('permission:users.view')
                ->name('users.index');
            Route::get('users/create', [UserController::class, 'create'])
                ->middleware('permission:users.manage')
                ->name('users.create');
            Route::post('users', [UserController::class, 'store'])
                ->middleware('permission:users.manage')
                ->name('users.store');
            Route::get('users/{user}/edit', [UserController::class, 'edit'])
                ->middleware('permission:users.manage')
                ->name('users.edit');
            Route::patch('users/{user}', [UserController::class, 'update'])
                ->middleware('permission:users.manage')
                ->name('users.update');
            Route::post('users/{user}/activate', [UserController::class, 'activate'])
                ->middleware('permission:users.manage')
                ->name('users.activate');
            Route::post('users/{user}/deactivate', [UserController::class, 'deactivate'])
                ->middleware('permission:users.manage')
                ->name('users.deactivate');

            Route::get('roles', [RoleController::class, 'index'])
                ->middleware('permission:roles.view')
                ->name('roles.index');
            Route::put('roles/{role}', [RoleController::class, 'update'])
                ->middleware('permission:roles.manage')
                ->name('roles.update');

            Route::get('audit', [AuditLogController::class, 'index'])
                ->middleware('permission:audit.view')
                ->name('audit.index');

            Route::get('settings', [SettingController::class, 'index'])
                ->middleware('permission:settings.view')
                ->name('settings.index');
            Route::put('settings', [SettingController::class, 'update'])
                ->middleware('permission:settings.manage')
                ->name('settings.update');
        });
    });

Route::get('/{page}', [PublicPageController::class, 'page'])
    ->whereIn('page', array_keys(config('public_site.pages', [])))
    ->name('public.page');
