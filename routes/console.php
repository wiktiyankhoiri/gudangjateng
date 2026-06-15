<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Pruning tasks
Schedule::command('app:prune-audit-logs')->daily();
Schedule::command('app:cleanup-old-logs')->weekly();
Schedule::command('app:auto-backup')->dailyAt('16:00');
