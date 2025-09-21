<?php

use App\Http\Controllers\Authentication\LoginController;
use App\Http\Controllers\CP\DashboardController;
use App\Http\Controllers\CP\UserManagement\UserProfileController;
use App\Http\Controllers\CP\UserManagement\UsersController;
use App\Http\Controllers\LanguageSwitcherController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'dashboard'], function () {

    Route::get('/login', [LoginController::class, 'signIn'])->name('login');
    Route::post('/authenticate', [LoginController::class, 'authenticate'])->name('authenticate');

    Route::post('getSelect', [\App\Http\Controllers\Controller::class, 'getSelect'])->name('getSelect');
    Route::post('getSelect2Details', [DashboardController::class, 'getSelect2Details'])->name('getSelect2Details'); // this is a v2 of getSelect (we should update the getSelect later to be like getSelect2)
    Route::post('getSelect2', [DashboardController::class, 'getSelect2'])->name('getSelect2'); // this is a v2 of getSelect (we should update the getSelect later to be like getSelect2)
    Route::post('getSelect2WithoutSearchOrPaginate', [DashboardController::class, 'getSelect2WithoutSearchOrPaginate'])->name('getSelect2WithoutSearchOrPaginate'); // this is a v2 of getSelect (we should update the getSelect later to be like getSelect2)
    Route::post('/store-objective', [DashboardController::class, 'storeObjective'])->name('store-objective');
    Route::post('/get-objectives', [DashboardController::class, 'getObjectives'])->name('get-objectives');
    Route::delete('attachments/{attachment}', [DashboardController::class, 'remove_attachment'])->name('remove-attachment');

    Route::middleware(['auth'])->group(function () {
        // User Profile Routes
        Route::prefix('user')->name('user.')->group(function () {
            Route::get('/profile', [UserProfileController::class, 'index'])->name('profile');
            Route::post('/profile', [UserProfileController::class, 'update'])->name('profile.update');
        });

        Route::get('/setDashboardLanguage/{language}', [LanguageSwitcherController::class, 'setDashboardLanguage'])->name('setDashboardLanguage');
        Route::get('/logout', [LoginController::class, 'logout'])->name('logout');
        Route::get('/', [DashboardController::class, 'index'])->name('home');
        Route::impersonate();
        Route::prefix('dashboard')->name('dashboard.')->group(function () {
            Route::get('/', [DashboardController::class, 'index'])->name('index');
        });

        Route::prefix('users')->name('users.')->middleware(['permission:user_management_access'])->controller(UsersController::class)->group(function () {
            Route::get('/export', 'export')->name('export');
            Route::match(['get', 'post'], '/', 'index')->name('index');
            Route::get('/create', 'create')->name('create');
            Route::get('/{_model}/edit', 'edit')->name('edit');
            Route::delete('{_model}/delete', 'delete')->name('delete');
            Route::post('/addedit', 'addedit')->name('addedit');
        });

        // Get settings module configuration
        $config = config('modules.settings');

        Route::prefix($config['route'])->name($config['route'] . '.')->middleware('permission:' . $config['permissions']['view'])->group(function () use ($config) {

            // General Settings
            $generalConfig = $config['children']['general'];
            Route::prefix($generalConfig['route'])->name($generalConfig['route'] . '.')->controller($generalConfig['controller'])->group(function () use ($generalConfig) {
                Route::match(['get', 'post'], '/', 'index')->name('index')->middleware('permission:' . $generalConfig['permissions']['view']);
                Route::post('/update', 'update')->name('update')->middleware('permission:' . $generalConfig['permissions']['edit']);
            });
        });

        // Categories child routes
        $config = config('modules.categories');
        Route::prefix($config['route'])->name($config['route'] . '.')->controller($config['controller'])->group(function () use ($config) {
            Route::get('/export', 'export')->name('export')->middleware('permission:' . $config['permissions']['view']);
            Route::match(['get', 'post'], '/', 'index')->name('index')->middleware('permission:' . $config['permissions']['view']);
            Route::get('/create', 'create')->name('create')->middleware('permission:' . $config['permissions']['create']);
            Route::get('/{_model}/edit', 'edit')->name('edit')->middleware('permission:' . $config['permissions']['edit']);
            Route::get('/{_model}/details', 'details')->name('details')->middleware('permission:' . $config['permissions']['edit']);
            Route::delete('{_model}/delete', 'delete')->name('delete')->middleware('permission:' . $config['permissions']['delete']);
            Route::post('/' . $config['singular_key'] . '/{Id?}', 'addedit')->name('addedit')->middleware('permission:' . $config['permissions']['create']);
            Route::post('/update-order', 'updateOrder')->name('update-order')->middleware('permission:' . $config['permissions']['edit']);
        });

        // sliders child routes
        $config = config('modules.sliders');
        Route::prefix($config['route'])->name($config['route'] . '.')->controller($config['controller'])->group(function () use ($config) {
            Route::get('/export', 'export')->name('export')->middleware('permission:' . $config['permissions']['view']);
            Route::match(['get', 'post'], '/', 'index')->name('index')->middleware('permission:' . $config['permissions']['view']);
            Route::get('/create', 'create')->name('create')->middleware('permission:' . $config['permissions']['create']);
            Route::get('/{_model}/edit', 'edit')->name('edit')->middleware('permission:' . $config['permissions']['edit']);
            Route::get('/{_model}/details', 'details')->name('details')->middleware('permission:' . $config['permissions']['edit']);
            Route::delete('{_model}/delete', 'delete')->name('delete')->middleware('permission:' . $config['permissions']['delete']);
            Route::post('/' . $config['singular_key'] . '/{Id?}', 'addedit')->name('addedit')->middleware('permission:' . $config['permissions']['create']);
        });
        // sucess stories child routes
        $config = config('modules.sucess_stories');
        Route::prefix($config['route'])->name($config['route'] . '.')->controller($config['controller'])->group(function () use ($config) {
            Route::get('/export', 'export')->name('export')->middleware('permission:' . $config['permissions']['view']);
            Route::match(['get', 'post'], '/', 'index')->name('index')->middleware('permission:' . $config['permissions']['view']);
            Route::get('/create', 'create')->name('create')->middleware('permission:' . $config['permissions']['create']);
            Route::get('/{_model}/edit', 'edit')->name('edit')->middleware('permission:' . $config['permissions']['edit']);
            Route::get('/{_model}/details', 'details')->name('details')->middleware('permission:' . $config['permissions']['edit']);
            Route::delete('{_model}/delete', 'delete')->name('delete')->middleware('permission:' . $config['permissions']['delete']);
            Route::post('/' . $config['singular_key'] . '/{Id?}', 'addedit')->name('addedit')->middleware('permission:' . $config['permissions']['create']);
        });
        // videos child routes
        $config = config('modules.videos');
        Route::prefix($config['route'])->name($config['route'] . '.')->controller($config['controller'])->group(function () use ($config) {
            Route::get('/export', 'export')->name('export')->middleware('permission:' . $config['permissions']['view']);
            Route::match(['get', 'post'], '/', 'index')->name('index')->middleware('permission:' . $config['permissions']['view']);
            Route::get('/create', 'create')->name('create')->middleware('permission:' . $config['permissions']['create']);
            Route::get('/{_model}/edit', 'edit')->name('edit')->middleware('permission:' . $config['permissions']['edit']);
            Route::get('/{_model}/details', 'details')->name('details')->middleware('permission:' . $config['permissions']['edit']);
            Route::delete('{_model}/delete', 'delete')->name('delete')->middleware('permission:' . $config['permissions']['delete']);
            Route::post('/' . $config['singular_key'] . '/{Id?}', 'addedit')->name('addedit')->middleware('permission:' . $config['permissions']['create']);
        });
    });
});
