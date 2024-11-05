<?php

use App\Console\Commands\DeleteExpiredChalets;
use App\Models\Chalet;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;



Schedule::command(DeleteExpiredChalets::class)->daily();
Schedule::command('model:prune', ['--model' => Chalet::class])->daily();

Schedule::command('tour:start')->everyFiveMinutes()->withoutOverlapping();
Schedule::command('tour:end')->everyFiveMinutes()->withoutOverlapping();
