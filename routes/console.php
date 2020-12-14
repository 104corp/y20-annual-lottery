<?php

use App\Actions\Init;
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
})->describe('Display an inspiring quote');

Artisan::command('program:start {--type=*}', function () {
    $type = $this->option('type')[0] ?? Init::CSV_TYPE_REAL;

    Artisan::call('migrate:fresh');
    $this->comment('database has been droped and then migrated again...');
    Artisan::call("program:init --type=$type");
    $this->comment('The data has been initialized sucessfully');
    $this->comment('server is about to start...');
    Artisan::call('serve');
});
