<?php

namespace App\Models\Materials;

use App\Models\BaseModel;   

/**
 * DownloadModel
 *
 * This class represents a download event for a material. It stores information such as the champion who downloaded it,
 * the file details, tips sent/requested, and the elapsed time since download.
 */
class DownloadModel extends BaseModel {

   

    public $id;
    public $champion_id;
    public $file_name;
    public $download_date;
    public $requested_tips;
    public $sent_tips;
    public $file_id;
    public $elapsed_months;
    public $tip;
    public $tip_detail;

   

    /**
     * Set values for the DownloadModel, applying default values if necessary.
     *
     * @param array $params Associative array containing the data to set.
     */
    public function setValues(array $params) {
        $defaults = [
            'id' => null,
            'champion_id' => null,
            'file_name' => '',
            'download_date' => Now(),
            'requested_tips' => null,
            'sent_tips' => null,
            'file_id' => 0,
            'elapsed_months' => 0,
            'tip' => null,
            'tip_detail' => null
        ];

        // Merge provided data with defaults
        $params = array_merge($defaults, $params);

        // Assign values to properties
        foreach ($params as $key => $value) {
            $this->$key = $value;
        }
    }

    /**
     * Insert a new download record into the database.
     *
     * @return int|null The ID of the newly inserted record.
     */
    public function insert() {
        $query = "INSERT INTO hl_downloads 
                  (champion_id, file_name, download_date, requested_tips, sent_tips, file_id, elapsed_months, tip, tip_detail) 
                  VALUES 
                  (:champion_id, :file_name, :download_date, :requested_tips, :sent_tips, :file_id, :elapsed_months, :tip, :tip_detail)";

        $params = [
            ':champion_id' => $this->champion_id,
            ':file_name' => $this->file_name,
            ':download_date' => $this->download_date,
            ':requested_tips' => $this->requested_tips,
            ':sent_tips' => $this->sent_tips,
            ':file_id' => $this->file_id,
            ':elapsed_months' => $this->elapsed_months,
            ':tip' => $this->tip,
            ':tip_detail' => $this->tip_detail
        ];

        // Execute the query and return the inserted ID
        $this->databaseService->executeUpdate($query, $params);
        return $this->databaseService->getLastInsertId();
    }

   
    // Specify the valid columns for the downloads table
    protected function getValidColumns()
    {
        return [
            'champion_id', 
            'file_name', 
            'download_date', 
            'requested_tips', 
            'sent_tips', 
            'file_id', 
            'elapsed_months', 
            'tip', 
            'tip_detail'
        ];
    }

    // Define the table name for this model
    protected function getTableName()
    {
        return 'hl_downloads';
    }

    

    /**
     * Create a new download record or update an existing one.
     *
     * @param array $params Data to create or update the download record.
     * @return int|null The ID of the created or updated download record.
     */
    public function create(array $params) {
        $this->setValues($params);

        if ($this->id) {
            $this->update();
        } else {
            return $this->insert();
        }

        return $this->id;
    }

    // Getters for accessing properties

    /**
     * Get the download ID.
     *
     * @return int|null
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Get the champion ID who downloaded the material.
     *
     * @return int|null
     */
    public function getChampionId() {
        return $this->champion_id;
    }

    /**
     * Get the file name of the downloaded material.
     *
     * @return string
     */
    public function getFileName() {
        return $this->file_name;
    }

    /**
     * Get the date when the material was downloaded.
     *
     * @return int The timestamp of the download date.
     */
    public function getDownloadDate() {
        return $this->download_date;
    }

    /**
     * Get the requested tips associated with the download.
     *
     * @return string|null
     */
    public function getRequestedTips() {
        return $this->requested_tips;
    }

    /**
     * Get the sent tips associated with the download.
     *
     * @return string|null
     */
    public function getSentTips() {
        return $this->sent_tips;
    }

    /**
     * Get the file ID of the downloaded material.
     *
     * @return int
     */
    public function getFileId() {
        return $this->file_id;
    }

    /**
     * Get the number of months elapsed since the download.
     *
     * @return int
     */
    public function getElapsedMonths() {
        return $this->elapsed_months;
    }

    /**
     * Get the tip associated with the download.
     *
     * @return string|null
     */
    public function getTip() {
        return $this->tip;
    }

    /**
     * Get the details of the tip associated with the download.
     *
     * @return string|null
     */
    public function getTipDetail() {
        return $this->tip_detail;
    }
}
