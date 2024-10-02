<?php
Namespace App\Controllers\Materials;

use App\Services\Database\DatabaseService;
use PDO;


class TractOptionsPageSizeController {
    private $databaseService;

    public function __construct(DatabaseService $databaseService) {
        $this->databaseService = $databaseService;
    }
    public function getDistinctPageSizeBilingualBooks($lang1, $lang2, $audience){
        $query = "SELECT DISTINCT paper_size FROM hl_materials 
            WHERE active = :active
            AND category = :category
            AND format = :format
            AND lang1 = :lang1
            AND lang2 = :lang2
            AND audience = :audience
            ORDER BY paper_size ASC";
        $params = [':active' => 'YES', ':category' => 'Tracts', ':format' => 'BOOKLET',
            ':lang1' => $lang1, ':lang2' => $lang2, ':audience' => $audience];
        return $this->databaseService->executeQuery($query, $params)->fetchAll(PDO::FETCH_ASSOC);
    }
    public function getDistinctPageSizeBilingualPages($lang1, $lang2, $audience){
        $query = "SELECT DISTINCT paper_size FROM hl_materials 
            WHERE active = :active
            AND category = :category
            AND format = :format
            AND lang1 = :lang1
            AND lang2 = :lang2
            AND audience = :audience
            ORDER BY lang1 ASC";
        $params = [':active' => 'YES', ':category' => 'Tracts', ':format' => 'PAGE',
            ':lang1' => $lang1, ':lang2' => $lang2, ':audience' => $audience];
        return $this->databaseService->executeQuery($query, $params)->fetchAll(PDO::FETCH_ASSOC);
    }
    public function getDistinctPageSizeMonolingualBooks($lang1, $audience){
        $query = "SELECT DISTINCT paper_size FROM hl_materials 
            WHERE active = :active
            AND category = :category
            AND format = :format
            AND lang2 = :lang2
            AND lang1 = :lang1
            AND audience = :audience
            ORDER BY paper_size ASC";
        $params = [':active' => 'YES', ':category' => 'Tracts', ':format' => 'BOOKLET', ':lang2' => 'NONE',
            ':lang1' => $lang1, ':audience' => $audience];
        return $this->databaseService->executeQuery($query, $params)->fetchAll(PDO::FETCH_ASSOC);
    }
    public function getDistinctPageSizeMonolingualPages($lang1, $audience){
        $query = "SELECT DISTINCT paper_size FROM hl_materials 
            WHERE active = :active
            AND category = :category
            AND format = :format
            AND lang2 = :lang2
            AND lang1 = :lang1
            AND audience = :audience
            ORDER BY paper_size ASC";
        $params = [':active' => 'YES', ':category' => 'Tracts', ':format' => 'PAGE', ':lang2' => 'NONE',
            ':lang1' => $lang1, ':audience' => $audience];
        return $this->databaseService->executeQuery($query, $params)->fetchAll(PDO::FETCH_ASSOC);
    }
    public function getDistinctPageSize(){ 
        $query = "SELECT DISTINCT paper_size FROM hl_materials 
                WHERE active = :active
                AND category = :category";
            $params = [':active' => 'YES', ':category' => 'Tracts'];
            return $this->databaseService->executeQuery($query, $params)->fetchAll(PDO::FETCH_ASSOC);
    }
}