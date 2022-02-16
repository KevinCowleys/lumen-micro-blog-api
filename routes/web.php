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

$router->get('/version', function () use ($router) {
    return $router->app->version();
});

$router->group(['prefix' => 'api/v1'], function() use ($router) {
    $router->post('register', 'v1\AuthController@register');
    $router->post('authenticate', 'v1\AuthController@login');

    $router->group(['prefix' => 'conversations'], function() use ($router) {
        $router->get('/', 'v1\ConversationController@index');
        $router->post('/', 'v1\ConversationController@create');

        $router->get('{conversation_id}/messages', 'v1\MessageController@index');
        $router->post('{conversation_id}/messages', 'v1\MessageController@create');
    });

    $router->get('posts', 'v1\PostController@index');
    $router->post('posts', 'v1\PostController@create');
    $router->post('posts/{post_id}', 'v1\PostController@destroy');

    $router->get('likes/{username}', 'v1\LikeController@index');
    $router->post('like/{post_id}', 'v1\LikeController@toggleLike');

    $router->get('saves/{username}', 'v1\SaveController@index');
    $router->post('save/{post_id}', 'v1\SaveController@toggleSave');

    $router->get('blocked', 'v1\BlockController@index');
    $router->post('block/{username}', 'v1\BlockController@toggleBlock');
    
    $router->get('muted', 'v1\MuteController@index');
    $router->post('mute/{username}', 'v1\MuteController@toggleMute');

    $router->get('following/{username}', 'v1\FollowerController@showFollowing');
    $router->get('followers/{username}', 'v1\FollowerController@showFollowers');
    $router->post('follow/{username}', 'v1\FollowerController@toggleFollow');

    // Profile routes
    $router->get('{username}', 'v1\ProfileController@index');
    $router->get('profile/settings', 'v1\ProfileController@settings');
    $router->post('profile/settings', 'v1\ProfileController@updateSettings');
});