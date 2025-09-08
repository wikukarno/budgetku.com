<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Log;
use App\Models\User;

class NotificationHelper
{
    /**
     * Check if user has notifications enabled
     *
     * @param User $user
     * @return bool
     */
    public static function isNotificationEnabled(User $user): bool
    {
        return $user->notifications == 1;
    }

    /**
     * Dispatch email job only if notifications are enabled
     *
     * @param User $user
     * @param callable $dispatchCallback
     * @param string $emailType
     * @return void
     */
    public static function dispatchEmailIfEnabled(User $user, callable $dispatchCallback, string $emailType = 'notification'): void
    {
        if (!self::isNotificationEnabled($user)) {
            Log::info("Email notifications are disabled for user: {$user->email}. Skipping {$emailType} email dispatch.");
            return;
        }

        Log::info("Dispatching {$emailType} email for user: {$user->email}");
        $dispatchCallback();
    }

    /**
     * Log notification status
     *
     * @param User $user
     * @param string $action
     * @return void
     */
    public static function logNotificationStatus(User $user, string $action): void
    {
        $status = self::isNotificationEnabled($user) ? 'enabled' : 'disabled';
        Log::info("Notification status for user {$user->email} is {$status} during {$action}");
    }
}