<?php

namespace App\Services;

use App\Models\AppNotification;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Mail\DelinquencyWarningMail;

class HomiNotificationService
{
    protected $firebase;

    public function __construct(FirebaseService $firebase)
    {
        $this->firebase = $firebase;
    }

    /**
     * Send notification to a specific user across multiple channels.
     */
    public function notify(User $user, string $title, string $message, string $type = 'info', array $data = []): void
    {
        // 1. In-App Notification (Database)
        $this->sendToInApp($user, $title, $message, $type, $data);

        // 2. Push Notification (FCM)
        if ($user->fcm_token) {
            $this->firebase->sendNotification($user->fcm_token, $title, $message, $data);
        }

        // 3. Email
        if ($user->email) {
            $this->sendToEmail($user, $title, $message, $data);
        }

        // 4. WhatsApp (Mock/Placeholder)
        $this->sendToWhatsApp($user, $message);
    }

    /**
     * Store notification in database for the Homi mobile app list.
     */
    protected function sendToInApp(User $user, string $title, string $message, string $type, array $data): void
    {
        AppNotification::create([
            'user_id' => $user->id,
            'title'   => $title,
            'message' => $message,
            'type'    => $type,
            'data'    => $data,
        ]);
        
        Log::info("In-App Notification saved for User ID: {$user->id}");
    }

    /**
     * Send email notification.
     */
    protected function sendToEmail(User $user, string $title, string $message, array $data): void
    {
        try {
            Mail::to($user->email)->send(new DelinquencyWarningMail(
                $user->full_name ?? $user->name ?? 'Warga',
                $title,
                $message,
                $data
            ));
            Log::info("Email sent to: {$user->email}");
        } catch (\Throwable $e) {
            Log::error("Failed to send email to {$user->email}: " . $e->getMessage());
        }
    }

    /**
     * Mock WhatsApp sending logic.
     */
    protected function sendToWhatsApp(User $user, string $message): void
    {
        $phone = $user->phone ?? $user->residentProfile->phone ?? 'Unknown';
        
        // Simulating WhatsApp API call
        Log::info("[MOCK WHATSAPP] Sending to {$phone}: {$message}");
        
        // In a real implementation, you would use an API like Twilio, Fonnte, or WooWA here.
    }
}
