<?php
// Ensure writable storage directories exist for Vercel serverless functions
if (isset($_ENV['VERCEL']) || isset($_SERVER['VERCEL'])) {
    $storage = '/tmp/storage';
    if (!is_dir($storage)) {
        mkdir($storage.'/framework/views', 0777, true);
        mkdir($storage.'/framework/cache/data', 0777, true);
        mkdir($storage.'/framework/sessions', 0777, true);
        mkdir($storage.'/logs', 0777, true);
    }
}

// Forward Vercel requests to normal Laravel public/index.php
require __DIR__ . '/../public/index.php';
