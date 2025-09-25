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

        // About Office child routes
        $config = config('modules.about_office');
        Route::prefix($config['route'])->name($config['route'] . '.')->controller($config['controller'])->group(function () use ($config) {
            Route::match(['get', 'post'], '/', 'index')->name('index')->middleware('permission:' . $config['permissions']['view']);
            Route::post('/' . $config['singular_key'], 'addedit')->name('addedit')->middleware('permission:' . $config['permissions']['create']);
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
        // why choose us child routes
        $config = config('modules.why_choose_us');
        Route::prefix($config['route'])->name($config['route'] . '.')->controller($config['controller'])->group(function () use ($config) {
            Route::get('/export', 'export')->name('export')->middleware('permission:' . $config['permissions']['view']);
            Route::match(['get', 'post'], '/', 'index')->name('index')->middleware('permission:' . $config['permissions']['view']);
            Route::get('/create', 'create')->name('create')->middleware('permission:' . $config['permissions']['create']);
            Route::get('/{_model}/edit', 'edit')->name('edit')->middleware('permission:' . $config['permissions']['edit']);
            Route::get('/{_model}/details', 'details')->name('details')->middleware('permission:' . $config['permissions']['edit']);
            Route::delete('{_model}/delete', 'delete')->name('delete')->middleware('permission:' . $config['permissions']['delete']);
            Route::post('/' . $config['singular_key'] . '/{Id?}', 'addedit')->name('addedit')->middleware('permission:' . $config['permissions']['create']);
        });
        // customer rates child routes
        $config = config('modules.customer_rates');
        Route::prefix($config['route'])->name($config['route'] . '.')->controller($config['controller'])->group(function () use ($config) {
            Route::get('/export', 'export')->name('export')->middleware('permission:' . $config['permissions']['view']);
            Route::match(['get', 'post'], '/', 'index')->name('index')->middleware('permission:' . $config['permissions']['view']);
            Route::get('/create', 'create')->name('create')->middleware('permission:' . $config['permissions']['create']);
            Route::get('/{_model}/edit', 'edit')->name('edit')->middleware('permission:' . $config['permissions']['edit']);
            Route::get('/{_model}/details', 'details')->name('details')->middleware('permission:' . $config['permissions']['edit']);
            Route::delete('{_model}/delete', 'delete')->name('delete')->middleware('permission:' . $config['permissions']['delete']);
            Route::post('/' . $config['singular_key'] . '/{Id?}', 'addedit')->name('addedit')->middleware('permission:' . $config['permissions']['create']);
        });
        // how we works child routes
        $config = config('modules.how_we_works');
        Route::prefix($config['route'])->name($config['route'] . '.')->controller($config['controller'])->group(function () use ($config) {
            Route::get('/export', 'export')->name('export')->middleware('permission:' . $config['permissions']['view']);
            Route::match(['get', 'post'], '/', 'index')->name('index')->middleware('permission:' . $config['permissions']['view']);
            Route::get('/create', 'create')->name('create')->middleware('permission:' . $config['permissions']['create']);
            Route::get('/{_model}/edit', 'edit')->name('edit')->middleware('permission:' . $config['permissions']['edit']);
            Route::get('/{_model}/details', 'details')->name('details')->middleware('permission:' . $config['permissions']['edit']);
            Route::delete('{_model}/delete', 'delete')->name('delete')->middleware('permission:' . $config['permissions']['delete']);
            Route::post('/' . $config['singular_key'] . '/{Id?}', 'addedit')->name('addedit')->middleware('permission:' . $config['permissions']['create']);
        });
        $config = config('modules.articles_types');
        Route::prefix($config['route'])->name($config['route'] . '.')->controller($config['controller'])->group(function () use ($config) {
            Route::get('/export', 'export')->name('export')->middleware('permission:' . $config['permissions']['view']);
            Route::match(['get', 'post'], '/', 'index')->name('index')->middleware('permission:' . $config['permissions']['view']);
            Route::get('/create', 'create')->name('create')->middleware('permission:' . $config['permissions']['create']);
            Route::get('/{_model}/edit', 'edit')->name('edit')->middleware('permission:' . $config['permissions']['edit']);
            Route::get('/{_model}/details', 'details')->name('details')->middleware('permission:' . $config['permissions']['edit']);
            Route::delete('{_model}/delete', 'delete')->name('delete')->middleware('permission:' . $config['permissions']['delete']);
            Route::post('/' . $config['singular_key'] . '/{Id?}', 'addedit')->name('addedit')->middleware('permission:' . $config['permissions']['create']);
        });
        $config = config('modules.articles');
        Route::prefix($config['route'])->name($config['route'] . '.')->controller($config['controller'])->group(function () use ($config) {
            Route::get('/export', 'export')->name('export')->middleware('permission:' . $config['permissions']['view']);
            Route::match(['get', 'post'], '/', 'index')->name('index')->middleware('permission:' . $config['permissions']['view']);
            Route::get('/create', 'create')->name('create')->middleware('permission:' . $config['permissions']['create']);
            Route::get('/{_model}/edit', 'edit')->name('edit')->middleware('permission:' . $config['permissions']['edit']);
            Route::get('/{_model}/details', 'details')->name('details')->middleware('permission:' . $config['permissions']['edit']);
            Route::delete('{_model}/delete', 'delete')->name('delete')->middleware('permission:' . $config['permissions']['delete']);
            Route::post('/' . $config['singular_key'] . '/{Id?}', 'addedit')->name('addedit')->middleware('permission:' . $config['permissions']['create']);
        });
        // services child routes
        $config = config('modules.services');
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
