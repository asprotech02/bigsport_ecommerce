<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule; // 🌟 IMPORT INI
use App\Jobs\CleanupExpiredOrders;       // 🌟 IMPORT JOB LU

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// 🌟 DAFTARKAN BACKGROUND JOB LU DI SINI
Schedule::job(new CleanupExpiredOrders)->hourly();