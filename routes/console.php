<?php

use App\Jobs\RemindDTicketUpdate;
use App\Jobs\UpdateCineStarCardData;
use Illuminate\Support\Facades\Schedule;

Schedule::job(new RemindDTicketUpdate())
    ->lastDayOfMonth('20:00');

Schedule::job(new UpdateCineStarCardData())
    ->everyThirtyMinutes()->between('11:00', '23:00');
