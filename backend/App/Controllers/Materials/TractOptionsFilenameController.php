<?php
Namespace App\Controllers\Materials;

use App\Services\Database\DatabaseService;
use PDO;


class TractOptionsFilenameController {
    private $databaseService;

    public function __construct(DatabaseService $databaseService) {
        $this->databaseService = $databaseService;
    }
    public function getDistinctFilenameBilingualBooks($lang1, $lang2, $audience, $pagesize , $contact){
        $query = "SELECT DISTINCT contact FROM hl_materials 
            WHERE active = :active
            AND category = :category
            AND format = :format
            AND lang1 = :lang1
            AND lang2 = :lang2
            AND audience = :audience
            AND paper_size = :pagesize
            AND contact = :contact
            ORDER BY contact ASC";
        $params = [':active' => 'YES', ':category' => 'Tracts', ':format' => 'BOOKLET',
            ':lang1' => $lang1, ':lang2' => $lang2, 
            ':audience' => $audience, ':pagesize' => $pagesize, ':contact' => $contact];
        return $this->databaseService->executeQuery($query, $params)->fetchAll(PDO::FETCH_ASSOC);
    }
    public function getDistinctFilenameBilingualPages($lang1, $lang2, $audience, $pagesize , $contact){
        $query = "SELECT DISTINCT contact FROM hl_materials 
            WHERE active = :active
            AND category = :category
            AND format = :format
            AND lang1 = :lang1
            AND lang2 = :lang2
            AND audience = :audience
            AND paper_size = :pagesize
            AND contact = :contact
            ORDER BY contact ASC";
        $params = [':active' => 'YES', ':category' => 'Tracts', ':format' => 'PAGE',
            ':lang1' => $lang1, ':lang2' => $lang2, 
            ':audience' => $audience, ':pagesize' => $pagesize, ':contact' => $contact];    
        return $this->databaseService->executeQuery($query, $params)->fetchAll(PDO::FETCH_ASSOC);
    }
    public function getDistinctFilenameMonolingualBooks($lang1, $audience, $pagesize , $contact){
        $query = "SELECT DISTINCT contact FROM hl_materials 
            WHERE active = :active
            AND category = :category
            AND format = :format
            AND lang2 = :lang2
            AND lang1 = :lang1
            AND audience = :audience
            AND paper_size = :pagesize
            AND contact = :contact
            ORDER BY contact ASC";
        $params = [':active' => 'YES', ':category' => 'Tracts', ':format' => 'BOOKLET', 
            ':lang2' => 'NONE', ':lang1' => $lang1, 
            ':audience' => $audience, ':pagesize' => $pagesize], ':contact' => $contact; 
        return $this->databaseService->executeQuery($query, $params)->fetchAll(PDO::FETCH_ASSOC);
    }
    public function getDistinctFilenameMonolingualPages($lang1, $audience, $pagesize , $contact){
        $query = "SELECT DISTINCT contact FROM hl_materials 
            WHERE active = :active
            AND category = :category
            AND format = :format
            AND lang2 = :lang2
            AND lang1 = :lang1
            AND audience = :audience
            AND paper_size = :pagesize
            AND contact = :contact
            ORDER BY contact ASC";
        $params = [':active' => 'YES', ':category' => 'Tracts', ':format' => 'PAGE', 
            ':lang2' => 'NONE', ':lang1' => $lang1, 
            ':audience' => $audience, ':pagesize' => $pagesize, ':contact' => $contact]; 
        return $this->databaseService->executeQuery($query, $params)->fetchAll(PDO::FETCH_ASSOC);
    }
    

    
}