<?php
namespace App\Models\Emails;

use App\Services\Debugging;
use App\Models\BaseModel;
use PDO;

class EmailModel extends BaseModel{

    
    
    // This method sets the object's values and applies default values
    public function setValues($params) {
        // Define default values
        $defaults = [
            'subject' => 'Default Subject',
            'body' => 'No content available',
            'plain_text_only' => 0,
            'headers' => '',
            'template' => 'default_template',
            'series' => null,
            'sequence' => 1,
            'params' => json_encode([]), // Empty JSON by default
        ];

        // Merge provided data with defaults
        $params = array_merge($defaults, $params);

        // Assign values to the object's properties
        $this->subject = $params['subject'];
        $this->body = $params['body'];
        $this->plain_text_only = $params['plain_text_only'];
        $this->headers = $params['headers'];
        $this->template = $params['template'];
        $this->series = $params['series'];
        $this->sequence = $params['sequence'];
        $this->params = $params['params'];
    }

    // This method inserts the object's data into the database
    public function insert() {
        $query = "INSERT INTO hl_email_series (subject, body, plain_text_only, headers, template, series, sequence, params)
                  VALUES (:subject, :body, :plain_text_only, :headers, :template, :series, :sequence, :params)";
        
        $params = [
            ':subject' => $this->subject,
            ':body' => $this->body,
            ':plain_text_only' => $this->plain_text_only,
            ':headers' => $this->headers,
            ':template' => $this->template,
            ':series' => $this->series,
            ':sequence' => $this->sequence,
            ':params' => $this->params,
        ];

        // Execute the insert query with the parameters
        return $this->databaseService->executeUpdate($query, $params);
    }

    // This method combines setValues and insert to create a new record
    public function create($params) {
        // Set the object's values, applying default values where necessary
        $this->setValues($params);
        // Insert the data into the database
        return $this->insert();
    }
    // Specify the valid columns for the email  model //
    // used by BaseModel update method
    protected function getValidColumns() {
        return [
            'subject', 
            'body', 
            'plain_text_only', 
            'headers', 
            'template', 
            'series', 
            'sequence', 
            'params'
        ];
    }
    // Define the table name for this model
    protected function getTableName() {
        return 'hl_email';
    }

    public function delete($id) {
        $query = "DELETE FROM hl_email_series WHERE id = :id";
        $params = [':id' => $id];
        return $this->databaseService->executeUpdate($query, $params);
    }
    public function findById($id) {
        $query = "SELECT id, subject, body, plain_text_only, headers, template, series, sequence, params
                  FROM hl_email_series 
                  WHERE id = :id
                  LIMIT 1";
        $params = [':id' => $id];
        $results = $this->databaseService->executeQuery($query, $params);
        return $results->fetch(PDO::FETCH_ASSOC);
    }
    public function findAll() {
        $query = "SELECT id, subject, body, plain_text_only, headers, template, series, sequence, params
                  FROM hl_email_series";
        $results = $this->databaseService->executeQuery($query);
        return $results->fetchAll(PDO::FETCH_ASSOC);
    }
    public function findOneInSeries($series, $sequence) {
        $query = "SELECT id, subject, body, plain_text_only, headers, template, series, sequence, params
                  FROM hl_email_series 
                  WHERE series = :series
                  AND sequence = :sequence
                  LIMIT 1";
        $params = [':series' => $series, ':sequence' => $sequence];
        $results = $this->databaseService->executeQuery($query, $params);
        return $results->fetch(PDO::FETCH_ASSOC);
    }
    public function findIdForSeries($series, $sequence) {
        $query = "SELECT id
                  FROM hl_email_series 
                  WHERE series = :series
                  AND sequence = :sequence
                  LIMIT 1";
        $params = [':series' => $series, ':sequence' => $sequence];
        $results = $this->databaseService->executeQuery($query, $params);
        return $results->fetch(PDO::FETCH_COLUMN);
    }
    public function getRecentBlogTitles($number) {
        $int = (int)$number;
        $query = "SELECT subject, id
                  FROM hl_email_series
                  WHERE series = 'Blog'                 
                  ORDER BY id DESC
                  LIMIT $int";
        // Directly include the limit value
        $query = str_replace(':number', $int, $query);
        $results = $this->databaseService->executeQuery($query);
        return $results->fetchAll(PDO::FETCH_ASSOC);
    }
}
