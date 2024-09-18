<?php

Namespace App\Models;

class BaseModel {
    
    protected $databaseService;

    public function __construct($databaseService) {
        $this->databaseService = $databaseService;
    }

    // Abstract method to be defined in each child model
    protected function getValidColumns() {
        return [];
    }

    public function update($id, $data) {
        // Get valid columns for this model
        $validColumns = $this->getValidColumns();

        // Initialize fields array and params for the query
        $fields = [];
        $params = [':id' => $id];

        // Filter the data to only include valid columns
        foreach ($data as $key => $value) {
            if (in_array($key, $validColumns)) {
                $fields[] = "$key = :$key";
                $params[":$key"] = $value;
            }
        }

        // Check if there are any valid fields to update
        if (empty($fields)) {
            throw new Exception('No valid fields to update');
        }

        // Construct the query using the dynamically built fields
        $query = "UPDATE {$this->getTableName()} 
                  SET " . implode(', ', $fields) . " 
                  WHERE id = :id";

        return $this->databaseService->executeUpdate($query, $params);
    }

    // Abstract method to define the table name for each model
    protected function getTableName() {
        return '';
    }
}
