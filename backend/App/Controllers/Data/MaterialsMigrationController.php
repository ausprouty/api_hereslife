<?php
namespace App\Controllers\Data;

use App\Services\Database\LegacyDatabaseService;
use App\Services\Database\NewDatabaseService;
use App\Services\TimeService;

class MaterialsMigrationController {
    protected $legacyDbService;
    protected $newDbService;

    public function __construct(LegacyDatabaseService $legacyDbService, NewDatabaseService $newDbService) {
        $this->legacyDbService = $legacyDbService;
        $this->newDbService = $newDbService;
    }

    public function migrateMaterials($batchSize, $offset) {
        // Query the legacy database for a batch of records
        $query = "SELECT * FROM hl_materials LIMIT " . (int)$batchSize . " OFFSET " . (int)$offset;
        $legacyData = $this->legacyDbService->executeQuery($query)->fetchAll(\PDO::FETCH_ASSOC);

        foreach ($legacyData as &$material) {
            $this->convertTimestamps($material);
            $paper_size = $this->getPaperSize($material);
            $location = $this->getLocationName($material['contact']);
            $audience = $this->updateAudience($material['audience']);

            // Insert the converted data into the new database
            $this->newDbService->executeUpdate(
                "INSERT INTO hl_materials (id, title, tips, foreign_title_1, foreign_title_2, lang1, lang2, format, audience, contact, filename, category, downloads, active, active_date, size, print_size, paper_size, ordered) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?,?, ?)",
                [
                    $material['id'],
                    $material['title'],
                    $material['tips'],
                    $material['foreign_title_1'],
                    $material['foreign_title_2'],
                    $material['lang1'],
                    $material['lang2'],
                    $material['format'],
                    $audience,
                    $location,
                    $material['filename'],
                    $material['category'],
                    $material['downloads'],
                    $material['active'],
                    $material['active_date'],
                    $material['size'],
                    $material['print_size'],
                    $paper_size,
                    $material['ordered']
                ]
            );
        }

        // Return the fetched records to check batch size in the loop
        return ['success' => true, 'records' => $legacyData, 'offset' => $offset];
    }

    protected function convertTimestamps(&$material) {
        $material['active_date'] = $material['active_date'] ? TimeService::timestampToDate($material['active_date']) : NULL;
    }

    protected function getPaperSize($material) {
        if ($material['category'] !== 'Tracts') {
            return NULL;
        }
        if ($material['format'] != 'BOOKLET') {
            return NULL;
        }
        if (in_array($material['contact'], ['CA', 'UC', 'US', 'UW'])) {
            return 'Letter';
        }
        else {
            return 'A4';
        }
        
    }
    protected function updateAudience($audience) {
       if($audience == 'Traditional'){
         return 'Standard';
       }
       else{
        return $audience;
       }
    }

   protected function getLocationName($location) {
        $new = [
            '' => 'None',
            'AF' => 'Africa',
            'AU' => 'Australia',
            'BR' => 'Brazil',
            'CA' => 'Canada',
            'CH' => 'China',
            'CM' => 'Cambodia',
            'CY' => 'Cyprus',
            'EU' => 'Europe',
            'FR' => 'France',
            'GE' => 'Germany',
            'ID' => 'Indonesia',
            'KO' => 'South Korea',
            'NO' => 'None',
            'NZ' => 'New Zealand',
            'PH' => 'Philippines',
            'PN' => 'Pakistan',
            'RS' => 'South Africa',
            'SA' => 'South America',
            'TH' => 'Thailand',
            'UC' => 'Campus Ministry (US)',
            'UK' => 'United Kingdom',
            'UP' => 'Peace Project (US)',
            'US' => 'United States',
            'UW' => 'US (something)',  // Unsure, placeholder
            'WB' => 'International'    // WB for International
        ];
        if (!array_key_exists($location, $new)) {
            return 'None';
        }
        return $new[$location];
   }
        
}
