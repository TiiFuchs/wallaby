<?php

use App\Jobs\RemindDTicketUpdate;
use Illuminate\Support\Facades\Schedule;

Schedule::job(new RemindDTicketUpdate())
    ->lastDayOfMonth('20:00');

Schedule::command('cinestarcard:update', [
    '--all' => true,
])->everyThirtyMinutes()->between('11:00', '23:00');
