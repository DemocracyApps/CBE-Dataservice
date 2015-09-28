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

$app->get('/', function () use ($app) {
    return $app->welcome();
});

$app->get('/doit', function () use ($app) {
  return "Howdoo";
});

$app->post('/doit', 'UploadController@upload');

//$app->post('user/{id}', 'UserController@showProfile');