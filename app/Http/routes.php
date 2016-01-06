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
  $app->get('/datasets', 'DatasetsController@index');
  $app->get('/datasets/{id}', 'DatasetsController@show');

  $app->get('/datasources/{id}/execute', 'DataSourcesController@execute');
  $app->get('/datasources/{id}', 'DataSourcesController@show');
  $app->put('/datasources/{id}', 'DataSourcesController@update');

  $app->post('/upload', 'UploadController@upload');
  $app->post('/register_data_source', 'DataSourcesController@register');
  $app->get('/get_entity_info', 'DataSourcesController@getEntityInfo');
  $app->get('/{cmd}', 'MiscController@catchall1');
  $app->get('/{cmd1}/{cmd2}', 'MiscController@catchall2');
  $app->get('/{cmd1}/{cmd2}/{cmd3}', 'MiscController@catchall3');
});

//Route::group(['prefix' => 'api/v1'], function () {
//  Route::resource('datasets/upload',   'API\v1\DatasetsController', []);
//});
