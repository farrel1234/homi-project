<?php

$filePath = 'c:/laragon/www/homi-project/backup-backend-homi/backend-homi/database/seeders/LetterTypeSeeder.php';
$content = file_get_contents($filePath);

if ($content === false) {
    die("Failed to read file.");
}

// Pattern: Remove line number prefixes like "108: " or "109: 109: "
// The view_file tool sometimes adds line numbers, and my previous edit accidentally duplicated them.
$cleaned = preg_replace('/^\s*\d+:\s*/m', '', $content);
// Second pass for double-prefixed lines if they exist
$cleaned = preg_replace('/^\s*\d+:\s*/m', '', $cleaned);

// Specifically target the mess in template_html block if regex is too broad
// But usually ^\s*\d+:\s* is safe for these corrupted files.

file_put_contents($filePath, $cleaned);
echo "Cleaned $filePath\n";
