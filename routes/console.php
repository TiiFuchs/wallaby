<?php

use Illuminate\Support\Facades\Schedule;

Schedule::job(new \App\Jobs\RemindDTicketUpdate())
    ->lastDayOfMonth('20:00');

Schedule::command(\App\Console\Commands\CineStarCard\Update::class, [
    '--all',
])->everyThirtyMinutes()->between('11:00', '23:00');
