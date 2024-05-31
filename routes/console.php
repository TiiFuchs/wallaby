<?php

use Illuminate\Support\Facades\Schedule;

// Reminder to renew D-Ticket
Schedule::job(new \App\Jobs\RemindDTicketUpdate())
    ->lastDayOfMonth('20:00');
Schedule::job(new \App\Jobs\RemindDTicketUpdate())
    ->monthlyOn(1, '8:00');
Schedule::job(new \App\Jobs\RemindDTicketUpdate())
    ->monthlyOn(1, '18:00');

    // Update CineStar Card Data
Schedule::command(\App\Console\Commands\CineStarCard\Update::class, [
    '--all',
])->everyThirtyMinutes()->between('11:00', '23:00');
