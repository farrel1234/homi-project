<?php

namespace App\Console\Commands;

use App\Models\MlNbModel;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class MlTrainDelinquencyNb extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ml:train-delinquency-nb 
        {--name=delinquency_nb_v1 : Nama model yang disimpan}
        {--months=12 : Berapa bulan histori data yang digunakan}
        {--grace=7 : Batas hari toleransi keterlambatan}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Melatih (Training) model Naive Bayes menggunakan data historis warga';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info("=== Memulai Proses Training AI (Naive Bayes) ===");
        
        $name = $this->option('name');
        $months = (int) $this->option('months');
        $grace = (int) $this->option('grace');

        // 1. Ambil Data Historis (Dataset)
        // Kita bandingkan warga yang pernah menunggak vs yang tertib
        $this->info("Mengambil data historis dari {$months} bulan terakhir...");
        
        $startDate = now()->subMonths($months)->startOfMonth();
        $invoices = DB::table('fee_invoices')
            ->whereDate('period', '>=', $startDate->toDateString())
            ->whereDate('period', '<', now()->startOfMonth()->toDateString())
            ->get();

        if ($invoices->isEmpty()) {
            $this->error("Data historis tidak cukup untuk melakukan training.");
            return 1;
        }

        $this->info("Ditemukan " . $invoices->count() . " record iuran.");

        // 2. Ekstraksi Fitur & Label (Preprocessing)
        // Label (Y): 1 = Delinquent (Menunggak), 0 = Tertib
        // Fitur (X): Pola bayar sebelumnya
        $dataset = [];
        $counts = [0 => 0, 1 => 0]; // Counts for priors

        foreach ($invoices as $inv) {
            $isDelinquent = $this->determineIfDelinquent($inv, $grace);
            $label = $isDelinquent ? 1 : 0;
            
            // Contoh fitur sederhana: nominal iuran
            $features = [
                'amount_bucket' => $inv->amount <= 50000 ? '<=50k' : ($inv->amount > 150000 ? '>150k' : '50-150k'),
            ];

            $dataset[] = ['x' => $features, 'y' => $label];
            $counts[$label]++;
        }

        // 3. Hitung Priors (P(Y))
        $total = count($dataset);
        $priors = [
            0 => $counts[0] / $total,
            1 => $counts[1] / $total
        ];

        // 4. Hitung Likelihood (P(X|Y))
        // Kita hitung probabilitas setiap fitur muncul di setiap kelas (0/1)
        $likelihood = [
            'amount_bucket' => [
                0 => [], // Probabilities for class 0
                1 => []  // Probabilities for class 1
            ]
        ];

        foreach (['<=50k', '50-150k', '>150k'] as $bucket) {
            foreach ([0, 1] as $label) {
                $countMatches = collect($dataset)
                    ->where('y', $label)
                    ->where('x.amount_bucket', $bucket)
                    ->count();
                
                // Laplace Smoothing (agar tidak 0)
                $likelihood['amount_bucket'][$label][$bucket] = ($countMatches + 1) / ($counts[$label] + 3);
            }
        }

        // 5. Simpan Model ke Database
        $modelJson = [
            'name' => $name,
            'priors' => [$priors[0], $priors[1]],
            'likelihood' => $likelihood,
            'trained_at' => now()->toDateTimeString(),
            'grace_days' => $grace,
            'metadata' => [
                'total_samples' => $total,
                'distribution' => $counts
            ]
        ];

        MlNbModel::updateOrCreate(
            ['name' => $name],
            [
                'model_json' => $modelJson,
                'trained_at' => now()
            ]
        );

        $this->info("Training Selesai! Model '{$name}' telah disimpan.");
        $this->table(['Class', 'Count', 'Prior'], [
            ['Tertib (0)', $counts[0], number_format($priors[0], 4)],
            ['Menunggak (1)', $counts[1], number_format($priors[1], 4)],
        ]);

        return 0;
    }

    private function determineIfDelinquent($inv, $grace)
    {
        if ($inv->status === 'unpaid' && now()->isAfter(Carbon::parse($inv->due_date ?? $inv->period)->addDays($grace))) {
            return true;
        }
        
        // Cek jika dibayar tapi lewat jatuh tempo
        $payment = DB::table('fee_payments')
            ->where('invoice_id', $inv->id)
            ->where('review_status', 'approved')
            ->first();
            
        if ($payment) {
            $paidAt = Carbon::parse($payment->created_at);
            $due = Carbon::parse($inv->due_date ?? $inv->period)->addDays($grace);
            return $paidAt->isAfter($due);
        }

        return false;
    }
}
