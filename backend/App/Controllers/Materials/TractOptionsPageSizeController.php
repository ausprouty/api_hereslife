<?php
Namespace App\Controllers\Materials;

use App\Services\Database\DatabaseService;
use PDO;


class TractOptionsLanguage1Controller {
    private $databaseService;

    public function __construct(DatabaseService $databaseService) {
        $this->databaseService = $databaseService;
    }
    public function getDistinctPageSizeBilingualBooks($lang1, $lang2, $audience){
        $query = "SELECT DISTINCT page_size FROM hl_materials 
            WHERE active = :active
            AND category = :category
            AND format = :format
            ORDER BY lang1 ASC";
        $params = [':active' => 'YES', ':category' => 'Tracts', ':format' => 'BOOKLET'];
        return $this->databaseService->executeQuery($query, $params)->fetchAll(PDO::FETCH_ASSOC);
    }
    public function getDistinctPageSizeBilingualPages($lang1, $lang2, $audience){
        $query = "SELECT DISTINCT page_size FROM hl_materials 
            WHERE active = :active
            AND category = :category
            AND format = :format
            ORDER BY lang1 ASC";
        $params = [':active' => 'YES', ':category' => 'Tracts', ':format' => 'PAGE'];
        return $this->databaseService->executeQuery($query, $params)->fetchAll(PDO::FETCH_ASSOC);
    }
    public function getDistinctPageSizeMonolingualBooks($lang1, $audience){
        $query = "SELECT DISTINCT page_size FROM hl_materials 
            WHERE active = :active
            AND category = :category
            AND format = :format
            AND lang2 = :lang2
            ORDER BY lang1 ASC";
        $params = [':active' => 'YES', ':category' => 'Tracts', ':format' => 'BOOKLET', :lang2 => 'NONE'];
        return $this->databaseService->executeQuery($query, $params)->fetchAll(PDO::FETCH_ASSOC);
    }
    public function getDistinctPageSizeMonolingualPages($lang1, $audience){
        $query = "SELECT DISTINCT page_size FROM hl_materials 
            WHERE active = :active
            AND category = :category
            AND format = :format
            AND lang2 = :lang2
            ORDER BY lang1 ASC";
        $params = [':active' => 'YES', ':category' => 'Tracts', ':format' => 'PAGE', :lang2 => 'NONE'];
        return $this->databaseService->executeQuery($query, $params)->fetchAll(PDO::FETCH_ASSOC);
    }
    public function getDistinctPageSize(){ 
        $query = "SELECT DISTINCT page_size FROM hl_materials 
                WHERE active = :active
                AND category = :category
                ORDER BY lang1 ASC";
            $params = [':active' => 'YES', ':category' => 'Tracts'];
            return $this->databaseService->executeQuery($query, $params)->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getDistinctBilingualLang1() {
        $query = "SELECT DISTINCT page_size FROM hl_materials 
            WHERE active = :active
            AND category = :category
            ORDER BY lang1 ASC";
        $params = [':active' => 'YES', ':category' => 'Tracts'];
        return $this->databaseService->executeQuery($query, $params)->fetchAll(PDO::FETCH_ASSOC);
    }
}