<?php

/** @var \Laravel\Lumen\Routing\Router $router */

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

$router->get('/', function () use ($router) {
    return $router->app->version();
});
$router->get('/products', 'ProductController@index');
$router->get('/product/{product_id}', 'ProductController@show');

$router->group(['prefix' => 'auth'], function () use ($router) {
    $router->post('/register', 'AuthController@register');
    $router->post('/login', 'AuthController@login');
   
});

Route::group(['middleware' => ['auth']], function($router) {
    $router->post('/products', 'ProductController@store');
    $router->put('/product/{product_id}', 'ProductController@update');
    $router->delete('/product/{product_id}', 'ProductController@destroy');
    $router->get('/orders', 'OrderController@index');
    $router->post('/orders', 'OrderController@store');
    $router->put('/order/{order_id}', 'OrderController@update');
    $router->delete('order/{order_id}', 'OrderController@destroy');
});


$router->get('/order/{order_id}', 'OrderController@show');