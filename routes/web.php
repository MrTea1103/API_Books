<?php

/** @var \Laravel\Lumen\Routing\Router $router */

use App\Http\Controllers\BookController;
use Illuminate\Support\Facades\Auth;

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
// Xác thực user có trong hệ thống
$router->group(['prefix'=>'admin','middleware' => 'auth:api'], function ($router) {
    $router->get('/book', 'BookController@index');
    $router->post('/book/create', 'BookController@create');
    $router->put('/book/update/{id}', 'BookController@update');
    $router->get('/book/search', 'BookController@search');
    $router->delete('/book/delete/{id}', 'BookController@delete');
});
// Đăng nhập
$router->post('/login', 'LoginController@login');
$router->post('/register', 'LoginController@register');
$router->post('/password/reset/request', 'ResetPassword@sendResetLink');
$router->post('/password/reset', 'ResetPassword@resetPassword');

$router->get('/me', 'LoginController@me');
$router->post('password/email', 'PasswordResetController@sendResetLinkEmail');
$router->post('password/reset', 'PasswordResetController@reset');

$router->get('/book', 'BookController@index');
 