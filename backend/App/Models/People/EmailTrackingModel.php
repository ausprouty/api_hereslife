<?php

namespace App\Models\People;

class EmailSeriesMemberModel {
    private $id;
    private $tid;
    private $cid;
    private $sequence;
    private $sent;


    // Set values for the model, applying default values
    public function setValues(array $data) {
        $defaults = [
            'id' => null,
            'tid' => null,
            'cid' => null,
            'sequence' => null,
            'sent' => null,
        ];

        $data = array_merge($defaults, $data);

        $this->id = $data['id'];
        $this->tid = $data['tid'];
        $this->cid = $data['cid'];
        $this->sequence = $data['sequence'];
        $this->sent = $data['sent'];
    }

    // Insert a new record into the database
    public function insert() {
        $query = "INSERT INTO hl_email_series_members 
                  (tid, cid, sequence, sent)
                  VALUES 
                  (:tid, :cid, :sequence, :sent)";

        $params = [
            ':tid' => $this->tid,
            ':cid' => $this->cid,
            ':sequence' => $this->sequence,
            ':sent' => $this->sent,
        ];

        $this->databaseService->executeUpdate($query, $params);
        $this->id = $this->databaseService->getLastInsertId();
    }

    // Update an existing record in the database
    // Define the valid columns for this table
    protected function getValidColumns()
    {
        return [
            'id',
            'tid', 
            'cid', 
            'sequence', 
            'sent'
        ];
    }

    // Define the table name for this model
    protected function getTableName()
    {
        return 'hl_email_series_members';
    }


    // Create a new record or update if it exists
    public function create(array $data) {
        $this->setValues($data);

        if ($this->id) {
            $this->update(); // Update if an ID exists
        } else {
            $this->insert(); // Insert if it's a new record
        }
    }

    // Getters for accessing properties
    public function getId() { return $this->id; }
    public function getTid() { return $this->tid; }
    public function getCid() { return $this->cid; }
    public function getSequence() { return $this->sequence; }
    public function getSent() { return $this->sent; }
}
