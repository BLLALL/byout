<?php

use App\Console\Commands\DeleteExpiredChalets;
use App\Models\Chalet;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

Schedule::command(DeleteExpiredChalets::class)->daily();
Schedule::command('model:prune', ['--model' => Chalet::class])->daily();

Schedule::command('tour:start')->everyFiveMinutes()->withoutOverlapping();
Schedule::command('tour:end')->everyFiveMinutes()->withoutOverlapping();
