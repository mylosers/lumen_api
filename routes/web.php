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

$router->get('/', function () use ($router) {
    return $router->app->version();
});

$router->post('/foo','base\baseController@base' );
$router->post('/rsaNo','base\baseController@rsaNo');
$router->post('/sign','base\baseController@sign');
$router->post('/request','Login\RequestController@request');//注册
$router->get('/login','Login\LoginController@login');//注册
