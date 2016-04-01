<?php


Route::group(['middleware' => 'web'], function() {

    Route::get('/', function () {
        return redirect(route('sites'));
    });

    Route::get('logout', 'Auth\AuthController@logout');
    Route::get('login', 'Auth\AuthController@showLoginForm');

    if (in_array(env('ACCESS_TYPE', 'all'), ['all', 'email'])) {
        // Authentication Routes...
        Route::post('login', 'Auth\AuthController@login');

        // Registration Routes...
        Route::get('register', 'Auth\AuthController@showRegistrationForm');
        Route::post('register', 'Auth\AuthController@register');

        // Password Reset Routes...
        Route::get('password/reset/{token?}', 'Auth\PasswordController@showResetForm');
        Route::post('password/email', 'Auth\PasswordController@sendResetLinkEmail');
        Route::post('password/reset', 'Auth\PasswordController@reset');
    }


    if (in_array(env('ACCESS_TYPE', 'all'), ['all', 'github'])) {
        Route::get('/auth/github', 'Auth\GitHubController@redirectToProvider');
        Route::get('/auth/github/callback', 'Auth\GitHubController@handleProviderCallback');
    }


    Route::group(['middleware' => 'auth'], function() {

        Route::get('/sites', 'SitesController@index')->name('sites');
        Route::post('/sites', 'SitesController@store')->name('sites.store');
        Route::get('/sites/{id}', 'SitesController@show')->name('sites.show');
        Route::put('/sites/{id}', 'SitesController@update')->name('sites.update');

    });

    Route::get('/env/{name}', 'envController@get')->name('env.get');

});
