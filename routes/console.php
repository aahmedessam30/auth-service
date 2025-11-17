<?php

use App\Contracts\Repositories\RefreshTokenRepositoryContract;
use App\Contracts\Repositories\TokenBlacklistRepositoryContract;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Cleanup expired tokens daily at 2:00 AM
Schedule::call(function () {
    $blacklistRepo = app(TokenBlacklistRepositoryContract::class);
    $refreshTokenRepo = app(RefreshTokenRepositoryContract::class);

    $expiredBlacklist = $blacklistRepo->cleanupExpired();
    $expiredRefresh = $refreshTokenRepo->deleteExpired();

    logger()->info('Token cleanup completed', [
        'expired_blacklist' => $expiredBlacklist,
        'expired_refresh_tokens' => $expiredRefresh,
    ]);
})->daily()->at('02:00')->name('cleanup:expired-tokens');
