<?php

Route::get('test', [
    'as'         => 'test',
    'uses'       => 'HomeController@test',
    'middleware' => 'active:test',
]);

Route::get('/', [
    'as'         => 'home',
    'uses'       => 'HomeController@index',
    'middleware' => 'active:home',
]);
