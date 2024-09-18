<?php

namespace App\Models;

use App\Services\Database\DatabaseService;
use Exception;

class BaseModel
{
    /**
     * Instance of the DatabaseService for executing queries.
     *
     * @var DatabaseService
     */
    protected $databaseService;

    /**
     * Constructor method to inject DatabaseService and initialize the model.
     *
     * This constructor allows the `BaseModel` to manage interactions with the database
     * by utilizing the provided `DatabaseService`. All models that extend `BaseModel`
     * will automatically have access to the database service, reducing the need to 
     * manually pass the service around in every model.
     *
     * @param DatabaseService $databaseService The database service used for executing
     *                                         queries, updates, and database-related
     *                                         operations within the model.
     */
    public function __construct(DatabaseService $databaseService)
    {
        $this->databaseService = $databaseService;
    }

    /**
     * Method to get valid columns for the model.
     * This method should be overridden by child classes to define which columns
     * are valid for update operations in the database.
     *
     * @return array An array of valid column names for the model.
     */
    protected function getValidColumns()
    {
        return [];
    }

    /**
     * Method to get the table name for the model.
     * This method should be overridden by child classes to define the table
     * associated with the model for database operations.
     *
     * @return string The name of the table associated with the model.
     */
    protected function getTableName()
    {
        return '';
    }

    /**
     * Update a record in the database using provided data.
     *
     * This method dynamically constructs an SQL UPDATE query based on the provided
     * data and valid columns defined in the model. It checks that only valid columns
     * are included in the update, ensuring data integrity. The table and valid columns
     * must be defined in child classes via `getTableName()` and `getValidColumns()`.
     *
     * @param int   $id   The ID of the record to update.
     * @param array $data The data to update, as an associative array where the keys 
     *                    are column names and values are the new values for those columns.
     *
     * @return bool Returns true if the update was successful.
     * @throws Exception If no valid columns are defined or no fields are provided for update.
     */
    public function update($id, $data)
    {
        // Get valid columns for this model
        $validColumns = $this->getValidColumns();
        if (empty($validColumns)) {
            throw new Exception('No valid columns defined for this model.');
        }

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
            throw new Exception('No valid fields to update.');
        }

        // Construct the query using the dynamically built fields
        $query = "UPDATE {$this->getTableName()} 
                  SET " . implode(', ', $fields) . " 
                  WHERE id = :id";

        return $this->databaseService->executeUpdate($query, $params);
    }
}
