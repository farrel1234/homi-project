<?php

namespace App\Services;

use Google\Client as GoogleClient;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FirebaseService
{
    /**
     * Mengirim pesan via Firebase Cloud Messaging (FCM) HTTP v1 API.
     *
     * @param string $fcmToken
     * @param string $title
     * @param string $body
     * @param array $data
     * @return bool
     */
    public function sendNotification(string $fcmToken, string $title, string $body, array $data = []): bool
    {
        try {
            $credentialsFilePath = env('FCM_SERVICE_ACCOUNT_PATH');
            $fullPath = base_path($credentialsFilePath);
            if (!$credentialsFilePath || !file_exists($fullPath)) {
                Log::error("Firebase Credentials JSON tidak ditemukan di " . $fullPath);
                return false;
            }

            $client = new GoogleClient();
            $client->setAuthConfig($fullPath);
            $client->addScope('https://www.googleapis.com/auth/firebase.messaging');

            $client->fetchAccessTokenWithAssertion();
            $token = $client->getAccessToken();
            $accessToken = $token['access_token'];

            $projectId = json_decode(file_get_contents($fullPath))->project_id;
            
            // FCM v1 data values must be strings
            $stringData = array_map('strval', $data);

            $message = [
                'message' => [
                    'token' => $fcmToken,
                    'notification' => [
                        'title' => $title,
                        'body' => $body,
                    ],
                    'data' => $stringData,
                ],
            ];

            $response = Http::withToken($accessToken)
                ->post("https://fcm.googleapis.com/v1/projects/{$projectId}/messages:send", $message);

            if ($response->successful()) {
                Log::info("FCM Notification sent successfully to {$fcmToken}");
                return true;
            }

            Log::error("FCM Notification failed: " . $response->body());
            return false;
        } catch (\Exception $e) {
            Log::error("FCM Exception: " . $e->getMessage());
            return false;
        }
    }
}
