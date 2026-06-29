<?php

use App\Models\Tenant;
use App\Models\ServiceRequest;
use App\Services\ServiceRequestPdfService;
use App\Support\Tenancy\TenantManager;

$manager = app(TenantManager::class);
$tenant = Tenant::first();

if (!$tenant) {
    die("No tenant found\n");
}

$manager->activate($tenant);

$sr = ServiceRequest::latest()->first();
if (!$sr) {
    die("No service request found for tenant {$tenant->name}\n");
}

echo "Testing PDF generation for Service Request ID: {$sr->id}\n";
try {
    $pdfService = app(ServiceRequestPdfService::class);
    $path = $pdfService->generate($sr);
    echo "SUCCESS: PDF saved to {$path}\n";
} catch (\Throwable $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString() . "\n";
}
