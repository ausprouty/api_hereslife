<?php
error_log("Importing email que records...\n");

use App\Controllers\Data\EmailQueMigrationController;
use App\Services\Database\LegacyDatabaseService;
use App\Services\Database\NewDatabaseService;

// Instantiate the services and controller
$legacyDbService = new LegacyDatabaseService();
$newDbService = new NewDatabaseService();
$migrationController = new EmailQueMigrationController($legacyDbService, $newDbService);

// Define the batch size and initial offset
$batchSize = 100;
error_log($batchSize);
$offset = 0;

do {
    // Migrate a batch of email que records
    $result = $migrationController->migrateEmailQue($batchSize, $offset);
    
    // Increase the offset by the batch size
    $offset += $batchSize;
    
    // Check if there are more records to process
} while (count($result['records']) > 0);

echo "Migration completed.\n";
