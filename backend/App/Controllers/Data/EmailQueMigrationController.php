<?php
namespace App\Controllers\Data;

use App\Services\Database\LegacyDatabaseService;
use App\Services\Database\NewDatabaseService;
use App\Services\TimeService;

class EmailQueMigrationController {
    protected $legacyDbService;
    protected $newDbService;

    public function __construct(LegacyDatabaseService $legacyDbService, NewDatabaseService $newDbService) {
        $this->legacyDbService = $legacyDbService;
        $this->newDbService = $newDbService;
    }

    public function migrateEmailQue($batchSize, $offset) {
        // Query the legacy database for a batch of records
        $query = "SELECT * FROM hl_email_que LIMIT " . (int)$batchSize . " OFFSET " . (int)$offset;
        $legacyData = $this->legacyDbService->executeQuery($query)->fetchAll(\PDO::FETCH_ASSOC);

        foreach ($legacyData as &$emailQue) {
            // Convert delay_until from a timestamp to a date (Y-m-d)
            $delay_until = $emailQue['delay_until'] > 0 ? date('Y-m-d', $emailQue['delay_until']) : NULL;

            try {
                // Insert the converted data into the new database
                $this->newDbService->executeUpdate(
                    "INSERT INTO hl_email_que (id, delay_until, email_from, email_to, email_id, champion_id, subject, body, plain_text_only, headers, plain_text_body, template, params) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)",
                    [
                        $emailQue['id'],
                        $delay_until,
                        $emailQue['email_from'],
                        $emailQue['email_to'],
                        $emailQue['email_id'],
                        $emailQue['champion_id'],
                        $emailQue['subject'],
                        $emailQue['body'],
                        $emailQue['plain_text_only'],
                        $emailQue['headers'],
                        $emailQue['plain_text_body'],
                        $emailQue['template'],
                        $emailQue['params']
                    ]
                );
            } catch (\Exception $e) {
                // Log the error
                echo "Error inserting record: " . $e->getMessage() . "\n";
            }
        }

        // Return the fetched records and the batch size to track the migration process
        return ['success' => true, 'records' => $legacyData, 'offset' => $offset];
    }
}
