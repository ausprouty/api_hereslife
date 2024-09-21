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

    public function update($id, $data)
    {
        // Get valid columns for this model from the child class
        $validColumns = $this->getValidColumns();

        // Get the primary key from the child class (or default to 'id')
        $primaryKey = $this->getPrimaryKey();

        // Initialize fields array and params for the query
        $fields = [];
        $params = [":$primaryKey" => $id];

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
                WHERE $primaryKey = :$primaryKey";

        return $this->databaseService->executeUpdate($query, $params);
    }


    // Abstract method to define the table name for each repository
    abstract protected function getTableName();

    // Abstract method to define the valid columns for each repository
    abstract protected function getValidColumns();

    public function getPrimaryKey()
    {
        // Default primary key is 'id'. Child classes can override this if needed.
        return 'id';
    }
}
