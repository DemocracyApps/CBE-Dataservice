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

Log::info("Top of routes with URI " . \Request::server('REQUEST_URI') .
          " and method " .\Request::server('REQUEST_METHOD'));

$app->get('/', function () use ($app) {
    return $app->welcome();
});
$app->group(['namespace' => 'CBEDataService\Http\Controllers', 'prefix'=>'api/v1'], function ($app) {
  $app->get('/doit', function () use ($app) {
    return "Howdoo";
  });

  $app->post('/upload', 'UploadController@upload');
});

//Route::group(['prefix' => 'api/v1'], function () {
//  Route::resource('datasets/upload',   'API\v1\DatasetsController', []);
//});
