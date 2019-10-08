<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$router->group(['middleware' => 'cors'], function() use ($router) {
    // add OPTIONS route to fire cors middleware for preflight
    $router->options('/{route:.*}/', function (){return ['status' => 'ok'];});

    $router->get('/', function () use ($router) {
        return view('root');
    });

    /*
    ######################################
    # Login
    ######################################
    */

    $router->post('auth/login', ['as' => 'auth.login', 'uses' => 'AuthController@authenticate']);

    /*
    ######################################
    # JWT protected routes for logged users
    ######################################
    */
    $router->group(['middleware' => 'jwt.auth'], function () use ($router) {
        /*
        ######################################
        # Admin auth verify
        ######################################
        */
        $router->post('auth/verify', ['as' => 'auth.verify', 'uses' => 'AuthController@verify']);

        /*
        ######################################
        # Company Data
        ######################################
        */
        $router->get('/company_data', ['as' => 'company_data', 'uses' => 'DashboardController@companyData']);
        $router->post('/company_data', ['as' => 'company_data.update', 'uses' => 'DashboardController@companyDataUpdate']);
    });
});