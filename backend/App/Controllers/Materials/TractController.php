<?php
Namespace App\Controllers\Materials;

use App\Services\Database\DatabaseService;
use PDO;


class TractController {
    private $databaseService;

    public function __construct(DatabaseService $databaseService) {
        $this->databaseService = $databaseService;
    }
    public function getTractsToView() {
        $query = "SELECT lang1 FROM hl_materials
            WHERE active = :active 
            AND category = :category
            AND format = :format
            AND lang2 = :lang2
            ORDER BY lang1 ASC";
         $params = [
            ':active' => 'YES', 
            ':category' => 'Tracts', 
            ':format' => 'VIEW', 
            ':lang2' => 'English']; 
        $results = $this->databaseService->executeQuery($query, $params);
        $data =  $results->fetchAll(PDO::FETCH_ASSOC);
        return $data;
    }
    public function getTractsMonolingual() {
        $query = "SELECT distinct title, foreign_title_1 FROM hl_materials
            WHERE active LIKE :active 
            AND filename LIKE :filename
            ORDER BY title ASC";
         $params = [':active' => 'YES', 
            ':active' => 'YES', 
            ':filename' => 'tracts-monolingual%'];
        $results = $this->databaseService->executeQuery($query, $params);
        $data =  $results->fetchAll(PDO::FETCH_ASSOC);
        return $data;
    }
    public function getTractsBilingualEnglish() {
        $query = "SELECT distinct title  FROM hl_materials
            WHERE active = :active 
            AND category = :category
            AND lang2 = :lang2
            ORDER BY title ASC";
         $params = [':active' => 'YES', 
            ':active' => 'YES', 
            ':category' => 'Tracts',
            ':lang2' => 'English']; 
        $results = $this->databaseService->executeQuery($query, $params);
        $data =  $results->fetchAll(PDO::FETCH_ASSOC);
        return $data;
    }

    public function getDistinctBilingualLang1() {
        $query = "SELECT DISTINCT lang1 FROM hl_materials 
            WHERE active = :active
            AND category = :category";
        $params = [':active' => 'YES', ':category' => 'Tracts'];
        return $this->databaseService->executeQuery($query, $params)->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getDistinctBilingualLang2($lang1) {
        $query = "SELECT DISTINCT lang2 FROM hl_materials 
            WHERE lang1 = :lang1 
            AND active = :active AND category = :category";
        $params = [':lang1' => $lang1, 
            ':active' => 'YES', ':category' => 'Tracts'];
        return $this->databaseService->executeQuery($query, $params)->fetchAll(PDO::FETCH_ASSOC);
    }
    public function getDistinctBilingualAudience($lang1, $lang2) {
        $query = "SELECT DISTINCT format FROM hl_materials 
            WHERE lang1 = :lang1 AND lang2 = :lang2 
            AND active = :active AND category = :category";
        $params = [':lang1' => $lang1, ':lang2' => $lang2, 
            ':active' => 'YES', ':category' => 'Tracts'];
        return $this->databaseService->executeQuery($query, $params)->fetchAll(PDO::FETCH_ASSOC);
    }
    public function getDistinctBilingualFormat($lang1, $lang2, $audience) {
        $query = "SELECT DISTINCT format FROM hl_materials 
            WHERE lang1 = :lang1 AND lang2 = :lang2 AND audience = :audience
            AND active = :active AND category = :category";
        $params = [':lang1' => $lang1, ':lang2' => $lang2, ':audience' => $audience,
            ':active' => 'YES', ':category' => 'Tracts'];
        return $this->databaseService->executeQuery($query, $params)->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getDistinctBilingualContacts($lang1, $lang2, $audience, $format) {
        $query = "SELECT DISTINCT contacts FROM hl_materials 
            WHERE lang1 = :lang1 AND lang2 = :lang2 AND audience = :audience AND format = :format
             AND active = :active AND category = :category";
        $params = [':lang1' => $lang1, ':lang2' => $lang2, ':audience' => $audience, ':format' => $format, 
            ':active' => 'YES', ':category' => 'Tracts'];
        return $this->databaseService->executeQuery($query, $params)->fetchAll(PDO::FETCH_ASSOC);
      
}

