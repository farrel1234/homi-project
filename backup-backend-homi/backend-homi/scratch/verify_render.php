<?php

use App\Models\ServiceRequest;
use App\Services\ServiceRequestPdfService;
use Illuminate\Support\Facades\DB;

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Activate Hawaii Tenant
$tenant = \App\Models\Tenant::where('code', 'hawaii-garden')->first();
app(\App\Support\Tenancy\TenantManager::class)->activate($tenant);

$srId = 6;
$sr = ServiceRequest::find($srId);

if (!$sr) {
    echo "Service Request #$srId not found.\n";
    exit;
}

$pdfService = app(ServiceRequestPdfService::class);
$html = $pdfService->renderHtml($sr);

echo "--- RENDERED HTML SNIPPET ---\n";
// Cari bidang usaha di HTML
if (str_contains($html, '{{bidang_usaha}}')) {
    echo "❌ FAILED: {{bidang_usaha}} placeholder still exists!\n";
} else {
    echo "✅ SUCCESS: {{bidang_usaha}} has been replaced.\n";
    
    // Tampilkan bagian Bidang Usaha
    if (preg_match('/Bidang Usaha.*?[:].*?<\/td><td>[:]\s*(.*?)<\/td>/is', $html, $m)) {
        echo "Value found: " . trim($m[1]) . "\n";
    }
}
echo "---------------------------\n";
