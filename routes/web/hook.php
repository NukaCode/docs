<?php

Route::any('hook', [
    'as'   => 'hook.index',
    'uses' => 'HookController@index',
]);
