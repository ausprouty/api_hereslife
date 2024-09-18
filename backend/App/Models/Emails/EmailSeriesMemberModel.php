<?php
namespace App\Models\Emails;

use App\Services\Debugging;
use App\Models\BaseModel;
use PDO;

class EmailSeriesMemberModel extends BaseModel{

  

    // This method sets the object's values and applies default values
    public function setValues($data) {
        // Define default values
        $defaults = [
            'list_id' => null,
            'list_name' => null,
            'champion_id' => null,
            'subscribed_date' => 0, // Default: not subscribed
            'last_tip_sent' => 0,
            'last_tip_sent_date' => null,
            'finished_all_tips' => 0,
            'unsubscribed_date' => 0
        ];

        // Merge provided data with defaults
        $data = array_merge($defaults, $data);

        // Set the object properties
        $this->list_id = $data['list_id'];
        $this->list_name = $data['list_name'];
        $this->champion_id = $data['champion_id'];
        $this->subscribed_date = $data['subscribed_date'];
        $this->last_tip_sent = $data['last_tip_sent'];
        $this->last_tip_sent_date = $data['last_tip_sent_date'];
        $this->finished_all_tips_date = $data['finished_all_tips_date'];
        $this->unsubscribed_date = $data['unsubscribed_date'];
    }

    // This method inserts the object's values into the database
    public function insert() {
        $query = "INSERT INTO hl_email_list_members 
                    (list_id, list_name, champion_id, subscribed_date, last_tip_sent, last_tip_sent_date, finished_all_tips, unsubscribed_date)
                  VALUES 
                    (:list_id, :list_name, :champion_id, :subscribed_date, :last_tip_sent, :last_tip_sent_date, :finished_all_tips, :unsubscribed_date)";

        $params = [
            ':list_id' => $this->list_id,
            ':list_name' => $this->list_name,
            ':champion_id' => $this->champion_id,
            ':subscribed_date' => $this->subscribed_date
            ':last_tip_sent' => $this->last_tip_sent,
            ':last_tip_sent_date' => $this->last_tip_sent_date,
            ':finished_all_tips' => $this->finished_all_tips,
            ':unsubscribed_date' => $this->unsubscribed_date
        ];

        // Execute the query
        return $this->databaseService->executeUpdate($query, $params);
    }

    // This method combines setValues and insert for creating a new record
    public function create($data) {
        // Set the object's values
        $this->setValues($data);

        // Insert the data into the database
        return $this->insert();
    }

    // This method updates the object's values in the database  
    // Specify the valid columns for the email list members
    protected function getValidColumns()
    {
        return [
            'list_id', 
            'list_name', 
            'champion_id', 
            'subscribed_date', 
            'last_tip_sent', 
            'last_tip_sent_date', 
            'finished_all_tips', 
            'unsubscribed_date'
        ];
    }

    // Define the table name for this model
    protected function getTableName()
    {
        return 'hl_email_list_members';
    }

    
    
    // Delete a record
    public function delete($id) {
        $query = "DELETE FROM hl_email_list_members WHERE id = :id";
        $params = [':id' => $id];
        return $this->databaseService->executeUpdate($query, $params);
    }

    // Find a record by its ID
    public function findById($id) {
        $query = "SELECT id, list_id, list_name, champion_id, subscribed_date, last_tip_sent, last_tip_sent_date, finished_all_tips, unsubscribed_date
                  FROM hl_email_list_members 
                  WHERE id = :id
                  LIMIT 1";
        $params = [':id' => $id];
        $results = $this->databaseService->executeQuery($query, $params);
        return $results->fetch(PDO::FETCH_ASSOC);
    }

    // Find all records
    public function findAll() {
        $query = "SELECT id, list_id, list_name, champion_id, subscribed_date, last_tip_sent, last_tip_sent_date, finished_all_tips, unsubscribed_date
                  FROM hl_email_list_members";
        $results = $this->databaseService->executeQuery($query);
        return $results->fetchAll(PDO::FETCH_ASSOC);
    }

    // Find all members of a specific list
    public function findByListId($list_id) {
        $query = "SELECT id, list_id, list_name, champion_id, subscribed_date, last_tip_sent, last_tip_sent_date, finished_all_tips, unsubscribed_date
                  FROM hl_email_list_members 
                  WHERE list_id = :list_id";
        $params = [':list_id' => $list_id];
        $results = $this->databaseService->executeQuery($query, $params);
        return $results->fetchAll(PDO::FETCH_ASSOC);
    }

    // Find all active (subscribed) members
    public function findSubscribedMembers() {
        $query = "SELECT id, list_id, list_name, champion_id, subscribed_date, last_tip_sent, last_tip_sent_date, finished_all_tips, unsubscribed_date
                  FROM hl_email_list_members 
                  WHERE unsubscribed_date IS NULL";
        $results = $this->databaseService->executeQuery($query);
        return $results->fetchAll(PDO::FETCH_ASSOC);
    }
    public function findNewRequestsForTips() {
        $query = "SELECT id, list_id, list_name, champion_id, subscribed_date, last_tip_sent, last_tip_sent_date, finished_all_tips, unsubscribed_date
                  FROM hl_email_list_members
                  WHERE unsubscribed_date IS NULL
                  AND subscribed <= NOW() - INTERVAL 30 MINUTE
                  AND last_tip_sent = 0";
        
        $results = $this->databaseService->executeQuery($query);
        return $results->fetchAll(PDO::FETCH_ASSOC);
    }
    public function findNextRequestsForTips() {
        // Define the variable
        $daysBetweenTipEmails = DAYS_BETWEEN_TIP_EMAILS; // Assuming this constant is defined somewhere

        // Update query to use a placeholder for the interval
        $query = "SELECT id, list_id, list_name, champion_id, subscribed_date, last_tip_sent, last_tip_sent_date, finished_all_tips, unsubscribed_date
                FROM hl_email_list_members
                WHERE unsubscribed_date IS NULL
                AND last_tip_sent_date <= NOW() - INTERVAL :days DAY
                AND finished_all_tips IS NULL";

        // Set up the params array in your preferred format
        $params = [
            ':days' => $daysBetweenTipEmails
        ];
        // Execute the query with the parameters
        $results = $this->databaseService->executeQuery($query, $params);
        // Fetch and return the results
        return $results->fetchAll(PDO::FETCH_ASSOC);
    }
    
}
