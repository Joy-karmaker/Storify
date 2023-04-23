<?php

use Illuminate\Support\Facades\Route;
use App\Http\Middleware\CheckAdmin;


Auth::routes();

//Route::get('/', 'Auth\LoginController@showLoginForm');

Route::middleware(['auth'])->group(function () {
    Route::resource('stories', 'StoriesController');
    Route::get('/edit-profile', 'ProfileController@edit')->name('profiles.edit');
    Route::put('/edit-profile/{user}', 'ProfileController@update')->name('profiles.update');
});

//clear cache
Route::get('/clear_cache', function () {
    $exitCode = Artisan::call('cache:clear');
    $exitCode = Artisan::call('route:clear');
    $exitCode = Artisan::call('view:clear');
    $exitCode = Artisan::call('config:cache');
    return '<h1>Clear Config cleared</h1>';
});

Route::get('/', 'DashboardController@index')->name('dashboard.index');
Route::get('/story/{activeStory:slug}', 'DashboardController@show')->name('dashboard.show');

Route::get('/email', 'DashboardController@email')->name('dashboard.email');

Route::namespace('Admin')->prefix('admin')->middleware(['auth',CheckAdmin::class])->group(function () {
Route::get('/deleted_stories', 'StoriesController@index')->name('admin.stories.index');
Route::put('/stories/restore/{id}', 'StoriesController@restore')->name('admin.stories.restore');
Route::delete('/stories/delete/{id}', 'StoriesController@delete')->name('admin.stories.delete');
});

Route::get('/image', function() {
    $image_path = public_path('storage/sample.jpg');
    $write_path = public_path('storage/thumbnail.jpg');
    $img = Image::make($image_path)->resize(255, 100);
    $img->save($write_path);
    return $img->response('jpg');
});
