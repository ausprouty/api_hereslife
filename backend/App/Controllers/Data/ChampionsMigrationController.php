<?php
namespace App\Controllers\Data;

use App\Services\Database\LegacyDatabaseService;
use App\Services\Database\NewDatabaseService;
use App\Services\TimeService;

class ChampionsMigrationController {
    protected $legacyDbService;
    protected $newDbService;

    public function __construct(LegacyDatabaseService $legacyDbService, NewDatabaseService $newDbService) {
        $this->legacyDbService = $legacyDbService;
        $this->newDbService = $newDbService;
    }

    public function migrateChampions($batchSize, $offset) {
        // Query the legacy database for a batch of records
        $query = "SELECT * FROM hl_champions LIMIT " . (int)$batchSize . " OFFSET " . (int)$offset;
        $legacyData = $this->legacyDbService->executeQuery($query)->fetchAll(\PDO::FETCH_ASSOC);
    
        foreach ($legacyData as &$champion) {
            $this->convertTimestamps($champion);
    
            // Insert the converted data into the new database
            $this->newDbService->executeUpdate(
                "INSERT INTO hl_champions (cid, first_name, surname, title, organization, address, suburb, state, postcode, country, phone, sms, email, gender, double_opt_in_date, first_email_date, last_open_date, consider_dropping_date, first_download_date, last_download_date, last_email_date) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)",
                [
                    $champion['cid'],
                    $champion['first_name'],
                    $champion['surname'],
                    $champion['title'],
                    $champion['organization'],
                    $champion['address'],
                    $champion['suburb'],
                    $champion['state'],
                    $champion['postcode'],
                    $champion['country'],
                    $champion['phone'],
                    $champion['sms'],
                    $champion['email'],
                    $champion['gender'],
                    $champion['double_opt_in_date'],
                    $champion['first_email_date'],
                    $champion['last_open_date'],
                    $champion['consider_dropping_date'],
                    $champion['first_download_date'],
                    $champion['last_download_date'],
                    $champion['last_email_date'],
                ]
            );
        }
    
        // Return the fetched records to check batch size in the loop
        return ['success' => true, 'records' => $legacyData, 'offset' => $offset];
    }
    
    

    protected function convertTimestamps(&$champion) {
        $champion['double_opt_in_date'] = $champion['double_opt_in_date'] ? TimeService::timestampToDate($champion['double_opt_in_date']) : NULL;
        $champion['first_email_date'] = $champion['first_email_date'] ? TimeService::timestampToDate($champion['first_email_date']) : NULL;
        $champion['last_open_date'] = $champion['last_open_date'] ? TimeService::timestampToDate($champion['last_open_date']) : NULL;
        $champion['consider_dropping_date'] = $champion['consider_dropping_date'] ? TimeService::timestampToDate($champion['consider_dropping_date']) : NULL;
        $champion['first_download_date'] = $champion['first_download_date'] ? TimeService::timestampToDate($champion['first_download_date']) : NULL;
        $champion['last_download_date'] = $champion['last_download_date'] ? TimeService::timestampToDate($champion['last_download_date']) : NULL;
        $champion['last_email_date'] = $champion['last_email_date'] ? TimeService::timestampToDate($champion['last_email_date']) : NULL;
    }
}
