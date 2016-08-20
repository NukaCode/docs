<?php

Route::get('docs/{version}/{name}', [
    'as' => 'docs.index',
    'uses' => 'DocumentationController@index'
]);

Route::get('docs/{version}/{name}/{section}', [
    'as' => 'docs.section',
    'uses' => 'DocumentationController@section'
]);
