<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

/*
|--------------------------------------------------------------------------
| Console Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of your Closure based console
| commands. Each Closure is bound to a command instance allowing a
| simple approach to interacting with each command's IO methods.
|
*/

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('reset-password {phone} {password}', function ($phone,$password) {
    $user = \DB::table('users')->where('phone', $phone)->update(
        [
            'password' => Hash::make($password)
        ]
    );
})->purpose('Display an inspiring quote');
