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

$router->group(['prefix' => 'api/v1'], function () use ($router) {
  $router->get('tasks',  ['uses' => 'api\v1\TasksController@index']);
  $router->get('tasks/{id}', ['uses' => 'api\v1\TasksController@get']);
  $router->post('tasks', ['uses' => 'api\v1\TasksController@create']);
  $router->delete('tasks/{id}', ['uses' => 'api\v1\TasksController@delete']);
  $router->put('tasks/{id}', ['uses' => 'api\v1\TasksController@update']);
});