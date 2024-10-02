<?php

namespace App\Models\Materials;

use App\Models\BaseModel;
use PDO;

class MaterialModel extends BaseModel {

    // Class properties corresponding to the hl_materials table
    protected $id;
    protected $title;
    protected $tips;
    protected $foreign_title_1;
    protected $foreign_title_2;
    protected $lang1;
    protected $lang2;
    protected $format;
    protected $audience;
    protected $contact;
    protected $filename;
    protected $category;
    protected $downloads;
    protected $active;
    protected $active_date;
    protected $size;
    protected $print_size;
    protected $paper_size;
    protected $ordered;

    /**
     * Specify the valid columns for the materials table.
     *
     * @return array List of valid columns
     */
    protected function getValidColumns()
    {
        return [
            'title', 
            'tips', 
            'foreign_title_1', 
            'foreign_title_2',
            'lang1', 
            'lang2', 
            'format', 
            'audience', 
            'contact', 
            'filename', 
            'category', 
            'downloads', 
            'active', 
            'active_date', 
            'size', 
            'print_size', 
            'paper_size', 
            'ordered'
        ];
    }

    /**
     * Get the table name for this model.
     *
     * @return string Table name
     */
    protected function getTableName()
    {
        return 'hl_materials';
    }

    /**
     * Set values for the MaterialModel, applying default values if necessary.
     *
     * @param array $params Associative array containing the data to set.
     */
    public function setValues(array $params) {
        $defaults = [
            'id' => null,
            'title' => '',
            'tips' => null,
            'foreign_title_1' => null,
            'foreign_title_2' => null,
            'lang1' => '',
            'lang2' => '',
            'format' => '',
            'audience' => '',
            'contact' => '',
            'filename' => '',
            'category' => null,
            'downloads' => 0,
            'active' => '',
            'active_date' => null,
            'size' => null,
            'print_size' => null,
            'paper_size' => null,
            'ordered' => 0
        ];

        // Merge provided data with defaults
        $params = array_merge($defaults, $params);

        // Assign values to properties
        foreach ($params as $key => $value) {
            $this->$key = $value;
        }
    }

    /**
     * Insert a new material record into the database.
     *
     * @return int|null The ID of the newly inserted record.
     */
    public function insert() {
        $query = "INSERT INTO hl_materials 
                  (title, tips, foreign_title_1, foreign_title_2, lang1, lang2, format, audience, contact, filename, category, downloads, active, active_date, size, print_size, paper_size, ordered) 
                  VALUES 
                  (:title, :tips, :foreign_title_1, :foreign_title_2, :lang1, :lang2, :format, :audience, :contact, :filename, :category, :downloads, :active, :active_date, :size, :print_size, :paper_size, :ordered)";

        $params = [
            ':title' => $this->title,
            ':tips' => $this->tips,
            ':foreign_title_1' => $this->foreign_title_1,
            ':foreign_title_2' => $this->foreign_title_2,
            ':lang1' => $this->lang1,
            ':lang2' => $this->lang2,
            ':format' => $this->format,
            ':audience' => $this->audience,
            ':contact' => $this->contact,
            ':filename' => $this->filename,
            ':category' => $this->category,
            ':downloads' => $this->downloads,
            ':active' => $this->active,
            ':active_date' => $this->active_date,
            ':size' => $this->size,
            ':print_size' => $this->print_size,
            ':paper_size' => $this->paper_size,
            ':ordered' => $this->ordered
        ];

        // Execute the query and return the inserted ID
        $this->databaseService->executeUpdate($query, $params);
        return $this->databaseService->getLastInsertId();
    }

    /**
     * Alias of insert for creating a new material.
     *
     * @param array $data Data to insert.
     * @return int|null The ID of the newly created record.
     */
    public function create($data)
    {
        $this->setValues($data);
        return $this->insert();
    }

    /**
     * Delete a material by its ID.
     *
     * @param int $id Material ID
     * @return int Number of affected rows
     */
    public function delete($id)
    {
        $query = "DELETE FROM " . $this->getTableName() . " WHERE id = :id";
        $params = [':id' => $id];
        return $this->databaseService->executeUpdate($query, $params);
    }

    /**
     * Find a material by its filename.
     *
     * @param string $filename Material filename
     * @return array|null Material data or null if not found
     */
    public function findByFileName($filename) {
        $query = "SELECT *
                  FROM hl_materials 
                  WHERE filename = :filename
                  LIMIT 1";
        $params = [':filename' => $filename];
        $results = $this->databaseService->executeQuery($query, $params);
        return $results->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Find a material by its ID.
     *
     * @param int $id Material ID
     * @return array|null Material data or null if not found
     */
    public function findById($id) {
        $query = "SELECT *
                  FROM hl_materials 
                  WHERE id = :id
                  LIMIT 1";
        $params = [':id' => $id];
        $results = $this->databaseService->executeQuery($query, $params);
        return $results->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Increment the download count for a material by 1.
     *
     * @param int $id Material ID
     * @return int Number of affected rows
     */
    public function incrementDownloads($id) {
        $query = "UPDATE hl_materials 
                  SET downloads = downloads + 1 
                  WHERE id = :id";
        $params = [':id' => $id];
        return $this->databaseService->executeUpdate($query, $params);
    }

    // Additional getter methods for specific fields can be implemented as needed
}
