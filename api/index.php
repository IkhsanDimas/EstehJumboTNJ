<?php

// Mode Uji Coba Database
if (isset($_GET['test_db'])) {
    header('Content-Type: text/plain');
    try {
        $dsn = "pgsql:host=" . getenv('DB_HOST') . ";port=" . getenv('DB_PORT') . ";dbname=" . getenv('DB_DATABASE');
        $pdo = new PDO($dsn, getenv('DB_USERNAME'), getenv('DB_PASSWORD'), [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        ]);
        echo "PDO Connection SUCCESS! Koneksi ke database PostgreSQL Supabase berhasil.";
    } catch (\Exception $e) {
        echo "PDO Connection FAILED: " . $e->getMessage();
    }
    exit;
}

// Forward request to the public index.php of Laravel
require __DIR__ . '/../public/index.php';
