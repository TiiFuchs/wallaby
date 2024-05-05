<?php

use App\Jobs\RemindDTicketUpdate;
use Illuminate\Support\Facades\Schedule;

Schedule::job(new RemindDTicketUpdate())
    ->lastDayOfMonth('20:00');
