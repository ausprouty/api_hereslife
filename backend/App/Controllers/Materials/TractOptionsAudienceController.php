<?php
Namespace App\Controllers\Materials;

use App\Services\Database\DatabaseService;
use PDO;


class TractOptionsAudienceController {
    private $databaseService;

    public function __construct(DatabaseService $databaseService) {
        $this->databaseService = $databaseService;
    }
    public function getDistinctAudienceBilingualBooks($lang1, $lang2){
        $query = "SELECT DISTINCT audience FROM hl_materials 
            WHERE active = :active
            AND category = :category
            AND format = :format
            AND lang1 = :lang1
            AND lang2 = :lang2
            ORDER BY audience ASC";
        $params = [':active' => 'YES', ':category' => 'Tracts', ':format' => 'BOOKLET',
            ':lang1' => $lang1, ':lang2' => $lang2];
        return $this->databaseService->executeQuery($query, $params)->fetchAll(PDO::FETCH_ASSOC);
    }
    public function getDistinctAudienceBilingualPages($lang1, $lang2){
        $query = "SELECT DISTINCT audience FROM hl_materials 
            WHERE active = :active
            AND category = :category
            AND format = :format
            AND lang1 = :lang1
            AND lang2 = :lang2
            ORDER BY audience ASC";
        $params = [':active' => 'YES', ':category' => 'Tracts', ':format' => 'PAGE',
            ':lang1' => $lang1, ':lang2' => $lang2];    
        return $this->databaseService->executeQuery($query, $params)->fetchAll(PDO::FETCH_ASSOC);
    }
    public function getDistinctAudienceMonolingualBooks($lang1){
        $query = "SELECT DISTINCT audience FROM hl_materials 
            WHERE active = :active
            AND category = :category
            AND format = :format
            AND lang2 = :lang2
            AND lang1 = :lang1
            ORDER BY audience ASC";
        $params = [':active' => 'YES', ':category' => 'Tracts', ':format' => 'BOOKLET', ':lang2' => 'NONE',
            ':lang1' => $lang1];
        return $this->databaseService->executeQuery($query, $params)->fetchAll(PDO::FETCH_ASSOC);
    }
    public function getDistinctAudienceMonolingualPages($lang1){
        $query = "SELECT DISTINCT audience FROM hl_materials 
            WHERE active = :active
            AND category = :category
            AND format = :format
            AND lang2 = :lang2
            AND lang1 = :lang1
            ORDER BY audience ASC";
        $params = [':active' => 'YES', ':category' => 'Tracts', ':format' => 'PAGE', ':lang2' => 'NONE', 
            ':lang1' => $lang1];
        return $this->databaseService->executeQuery($query, $params)->fetchAll(PDO::FETCH_ASSOC);
    }
    public function getDistinctAudience(){ 
        $query = "SELECT DISTINCT audience FROM hl_materials 
                WHERE active = :active
                AND category = :category
                ORDER BY audience ASC";
            $params = [':active' => 'YES', ':category' => 'Tracts'];
            return $this->databaseService->executeQuery($query, $params)->fetchAll(PDO::FETCH_ASSOC);
    }

    

    
}