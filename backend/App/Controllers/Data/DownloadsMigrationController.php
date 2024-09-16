<?php
namespace App\Controllers\Data;

use App\Services\Database\LegacyDatabaseService;
use App\Services\Database\NewDatabaseService;
use App\Services\TimeService;

class DownloadsMigrationController {
    protected $legacyDbService;
    protected $newDbService;

    public function __construct(LegacyDatabaseService $legacyDbService, NewDatabaseService $newDbService) {
        $this->legacyDbService = $legacyDbService;
        $this->newDbService = $newDbService;
    }

    public function migrateDownloads($batchSize, $offset) {
        // Query the legacy database for a batch of records
        $query = "SELECT * FROM hl_downloads LIMIT " . (int)$batchSize . " OFFSET " . (int)$offset;
        $legacyData = $this->legacyDbService->executeQuery($query)->fetchAll(\PDO::FETCH_ASSOC);

        foreach ($legacyData as &$download) {
            $this->convertTimestamps($download);

            // Insert the converted data into the new database
            $this->newDbService->executeUpdate(
                "INSERT INTO hl_downloads (id, champion_id, file_name, download_date, requested_tips, sent_tips, file_id, tip, tip_detail) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)",
                [
                    $download['id'],
                    $download['champion_id'],
                    $download['file_name'],
                    $download['download_date'],
                    $download['requested_tips'],
                    $download['sent_tips'],
                    $download['file_id'],
                    $download['tip'],
                    $download['tip_detail']
                ]
            );
        }

        // Return the fetched records to check batch size in the loop
        return ['success' => true, 'records' => $legacyData, 'offset' => $offset];
    }

    protected function convertTimestamps(&$download) {
        $download['download_date'] = $download['download_date'] ? TimeService::timestampToDate($download['download_date']) : NULL;
        $download['requested_tips'] = $download['requested_tips'] ? TimeService::timestampToDate($download['requested_tips']) : NULL;
        $download['sent_tips'] = $download['sent_tips'] ? TimeService::timestampToDate($download['sent_tips']) : NULL;
    }
}
