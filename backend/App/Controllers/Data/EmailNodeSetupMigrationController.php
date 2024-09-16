<?php

namespace App\Controllers\Data;

class EmailNodeSetupMigrationController
{
    private $legacyDbService;
    private $newDbService;

    public function __construct($legacyDbService, $newDbService)
    {
        $this->legacyDbService = $legacyDbService;
        $this->newDbService = $newDbService;
    }

    public function migrateEmailNodes($batchSize, $offset)
    {
        // Step 1: Get distinct email IDs from the legacy database in batches
        $emailIds = $this->getDistinctEmailIds($batchSize, $offset);

        // Step 2: Insert email IDs into the new database if they don't already exist
        foreach ($emailIds as $emailId) {
            $this->saveEmailNodeIfNotExists($emailId);
        }

        return ['records' => $emailIds];
    }

    private function getDistinctEmailIds($batchSize, $offset)
    {
        // Query to get distinct email_id with limit and offset for batching
        $query = "SELECT DISTINCT email_id FROM hl_email_tracking LIMIT " . (int)$batchSize . " OFFSET " . (int)$offset;
        
        // No need for parameter binding here, since LIMIT and OFFSET are already cast to integers
        return $this->legacyDbService->fetchColumn($query);
    }

    private function saveEmailNodeIfNotExists($emailId)
    {
        // Check if the email_id already exists in the new database
        $query = "SELECT COUNT(*) FROM hl_email_nodes WHERE email_id = :email_id";
        $exists = $this->newDbService->fetchSingleValue($query, ['email_id' => $emailId]);

        // If the email_id doesn't exist, insert it into hl_email_nodes
        if ($exists == 0) {
            $insertQuery = "INSERT INTO hl_email_nodes (email_id) VALUES (:email_id)";
            $this->newDbService->executeQuery($insertQuery, ['email_id' => $emailId]);
        }
    }
}
