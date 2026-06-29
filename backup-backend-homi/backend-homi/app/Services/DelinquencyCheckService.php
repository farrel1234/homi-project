<?php

namespace App\Services;

use App\Models\FeeInvoice;
use App\Models\User;
use Illuminate\Support\Carbon;

class DelinquencyCheckService
{
    /**
     * Check if a user has "Hard Arrears" (unpaid invoices past due date).
     * 
     * @param User $user
     * @return array [is_delinquent, message, unpaid_count]
     */
    public function checkHardArrears(User $user): array
    {
        $now = now();

        $unpaidInvoices = FeeInvoice::where('user_id', $user->id)
            ->whereIn('status', ['unpaid', 'pending'])
            ->whereDate('due_date', '<', $now->toDateString())
            ->get();

        $count = $unpaidInvoices->count();

        if ($count > 0) {
            return [
                'is_delinquent' => true,
                'message' => 'Mohon maaf, pengajuan layanan ditangguhkan sementara karena terdapat ' . $count . ' tunggakan iuran yang belum dilunasi. Silakan lakukan pembayaran terlebih dahulu.',
                'unpaid_count' => $count
            ];
        }

        return [
            'is_delinquent' => false,
            'message' => '',
            'unpaid_count' => 0
        ];
    }

    /**
     * Optional: Get risk status using Naive Bayes.
     * Use this for warnings or admin flagging.
     */
    public function getPredictionRisk(User $user): array
    {
        try {
            $nb = app(DelinquencyNaiveBayes::class);
            $nextMonth = now()->addMonth()->format('Y-m');
            $result = $nb->predict($user->id, $nextMonth);

            return [
                'is_at_risk' => (bool) ($result['label'] ?? false),
                'risk_probability' => (float) ($result['prob'] ?? 0),
                'features' => $result['features'] ?? []
            ];
        } catch (\Throwable $e) {
            return [
                'is_at_risk' => false,
                'risk_probability' => 0,
                'error' => $e->getMessage()
            ];
        }
    }
}
