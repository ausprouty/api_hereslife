<?php

namespace App\Repositories;

use Exception;

abstract class BaseRepository
{
    protected $databaseService;

    public function __construct($databaseService)
    {
        $this->databaseService = $databaseService;
    }

    // Update method that can be reused across all repositories
    public function update($id, $data)
    {
        // Get valid columns for this model from the child class
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
        $query = "UPDATE " . $this->getTableName() . " 
                  SET " . implode(', ', $fields) . " 
                  WHERE id = :id";

        return $this->databaseService->executeUpdate($query, $params);
    }

    // Abstract method to define the table name for each repository
    abstract protected function getTableName();

    // Abstract method to define the valid columns for each repository
    abstract protected function getValidColumns();
}
