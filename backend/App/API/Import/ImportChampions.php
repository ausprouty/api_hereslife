<?php
// Ensure the correct namespace usage
use App\Controllers\Data\ChampionsMigrationController;
use App\Services\Database\LegacyDatabaseService;
use App\Services\Database\NewDatabaseService;

$migrationController = new ChampionsMigrationController(new LegacyDatabaseService(), new NewDatabaseService());
$batchSize = 100; // Set the batch size
$offset = 0; // Initialize the offset to 0

do {
    $result = $migrationController->migrateChampions($batchSize, $offset);
    
    // Stop if the number of records fetched is less than the batch size
    if (count($result['records']) < $batchSize) {
        break;
    }
    
    $offset += $batchSize; // Increase the offset for the next batch
} while (true);

// Add a final message
echo "Migration completed successfully!";
