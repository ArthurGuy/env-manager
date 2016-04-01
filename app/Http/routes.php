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

    Route::get('kms', function() {

        //return openssl_encrypt('my string to encrypt', 'AES-256-CBC', 0xabc, false, '0000000000000000');
        //return \Crypt::encrypt('my string to encrypt');


        $kms = \App::make('aws')->createClient('kms');
        //$keys = $kms->listKeys();
        //dd($keys);

        $key = $kms->encrypt([
            'KeyId' => '7593d6d3-f4af-4733-90be-77e0f70a35dc',
            'Plaintext' => 'secret-key',
        ]);
        return response($key->get('CiphertextBlob'))->header('content-type', 'plain');
    });


    Route::get('/env/{name}', 'envController@get')->name('env.get');


});
