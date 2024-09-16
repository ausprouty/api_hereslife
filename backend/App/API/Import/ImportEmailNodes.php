<?php
use App\Controllers\Data\EmailNodeMigrationController;
use App\Services\Database\LegacyDatabaseService;
use App\Services\Database\NewDatabaseService;

// Instantiate the services and controller
$legacyDbService = new LegacyDatabaseService();
$newDbService = new NewDatabaseService();
$migrationService = new EmailNodeMigrationController($legacyDbService, $newDbService);

// Define the batch size and initial offset
$batchSize = 100;
$offset = 0;

do {
    // Migrate a batch of email node records
    $result = $migrationService->migrateEmailNodes($batchSize, $offset);
    
    // Increase the offset by the batch size
    $offset += $batchSize;
    
    // Check if there are more records to process
} while (count($result['records']) > 0);

echo "Migration completed.\n";
