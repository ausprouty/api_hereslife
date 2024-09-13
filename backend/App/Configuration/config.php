<?php

// Detect environment

if ($_SERVER['SERVER_NAME'] === 'localhost' || strpos($_SERVER['SERVER_NAME'], '127.0.0.1') !== false) {
    // Local environment
    require_once __DIR__ . '/.env.local.php';
  
} else {
    // Production environment
    require_once __DIR__ . '/.env.remote.php';
}
