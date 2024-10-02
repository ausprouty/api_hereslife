<?php
Namespace App\Controllers\Materials;

use App\Services\Database\DatabaseService;
use PDO;


class TractOptionsLanguage1Controller {
    private $databaseService;

    public function __construct(DatabaseService $databaseService) {
        $this->databaseService = $databaseService;
    }
    public function getDistinctLang2BilingualBooks($lang1)){
        $query = "SELECT DISTINCT lang2 FROM hl_materials 
            WHERE active = :active
            AND category = :category
            AND format = :format
            AND lang1 = :lang1
            ORDER BY lang2 ASC";
        $params = [':active' => 'YES', ':category' => 'Tracts', ':format' => 'BOOKLET', ':lang1' => $lang1];
        return $this->databaseService->executeQuery($query, $params)->fetchAll(PDO::FETCH_ASSOC);
    }
    public function getDistinctLang2BilingualPages($lang1)){
        $query = "SELECT DISTINCT lang2 FROM hl_materials 
            WHERE active = :active
            AND category = :category
            AND format = :format
            AND lang1 = :lang1
            ORDER BY lang2 ASC";
        $params = [':active' => 'YES', ':category' => 'Tracts', ':format' => 'PAGE', ':lang1' => $lang1];
        return $this->databaseService->executeQuery($query, $params)->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getDistinctLang2(){ 
        $query = "SELECT DISTINCT lang2 FROM hl_materials 
                WHERE active = :active
                AND category = :category
                AND lang1 = :lang1
                ORDER BY lang2 ASC";
            $params = [':active' => 'YES', ':category' => 'Tracts', ':lang1' => $lang1];
            return $this->databaseService->executeQuery($query, $params)->fetchAll(PDO::FETCH_ASSOC);
    }

    
}