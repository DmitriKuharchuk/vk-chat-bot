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

use Illuminate\Support\Facades\Artisan;

$router->post('api/vk/callback', ['uses'=>'VkApiCallbackController@execute']);




$router->get('/opentraining', ['uses'=>'openTrainningController@index']);
$router->post('/opentraining/delete/{$id}', ['uses'=>'openTrainningController@delete']);


$router->get('/clear-cache', function() {
    $exitCode = Artisan::call('cache:clear');
    return 'what you want';
});

$router->get('/view', function() {
    $exitCode = Artisan::call('view:clear');
    return 'what you want1';
});
$router->get('/config-clear', function() {
    $exitCode = Artisan::call('config:clear');
    return 'what you want2';
});