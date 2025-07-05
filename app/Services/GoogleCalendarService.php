<?php

namespace App\Services;

use Google_Client;
use Google_Service_Calendar;
use Google_Service_Calendar_Event;
use App\Models\User;
use App\Models\Task;

class GoogleCalendarService
{
    public function createEvent(User $user, Task $task): void
    {
        $client = new Google_Client();
        $client->setClientId(config('services.google.client_id'));
        $client->setClientSecret(config('services.google.client_secret'));
        $client->setAccessToken([
            'access_token' => $user->google_token,
            'refresh_token' => $user->google_refresh_token,
        ]);
        $client->addScope(Google_Service_Calendar::CALENDAR_EVENTS);

        // トークン更新処理（必要に応じて）
        if ($client->isAccessTokenExpired() && $client->getRefreshToken()) {
            $newToken = $client->fetchAccessTokenWithRefreshToken($client->getRefreshToken());
            // 必要なら新しい token を $user に保存
            $user->google_token = $newToken['access_token'];
            $user->save();
        }

        $calendar = new Google_Service_Calendar($client);

        $event = new Google_Service_Calendar_Event([
            'summary' => $task->title,
            'description' => $task->memo ?? '',
            'start' => [
                'dateTime' => $task->due_date->setTime(10, 0)->toRfc3339String(),
                'timeZone' => 'Asia/Tokyo',
            ],
            'end' => [
                'dateTime' => $task->due_date->setTime(11, 0)->toRfc3339String(),
                'timeZone' => 'Asia/Tokyo',
            ],
        ]);

        $calendar->events->insert('primary', $event);
    }
}
