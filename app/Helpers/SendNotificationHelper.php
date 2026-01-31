<?php

namespace App\Helpers;


use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;
use Kreait\Firebase\Exception\MessagingException;


class SendNotificationHelper
{
    public function sendNotification($data, array $tokens)
    {
        try {
            $filteredTokens = array_filter($tokens, function ($token) {
                return !empty($token);
            });
            $factory = (new Factory)
                ->withServiceAccount(storage_path(env('FIREBASE_CREDENTIALS')))
                ->createMessaging();

            $locale = app()->getLocale(); 

            $title = $data["title_{$locale}"] ?? $data['title'] ?? 'Notification';
            $body  = $data["body_{$locale}"]  ?? $data['body'] ?? '';

            $notification = Notification::create($title, $body);
            if (!empty($filteredTokens)) {
                $message = CloudMessage::new()
                    ->withNotification($notification)
                    ->withData(array_map('strval', $data));

                $report = $factory->sendMulticast($message, $filteredTokens);
            } else {
                $report = null;
            }
            return response()->json([
                'success' => true,
                'message' => 'Notification processed successfully',
                'report' => $report
            ]);
        } catch (MessagingException $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }
}
