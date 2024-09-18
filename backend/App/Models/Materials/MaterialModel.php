<?php
namespace App\Models\Materials;

use App\Services\Database\DatabaseService;
use App\Models\BaseModel;
use PDO;

class MaterialModel extends BaseModel {

    private $databaseService;
    protected $id;
    protected $material_name;
    protected $material_type;
    protected $created_date;
    protected $updated_date;
    protected $status;

    public function __construct($database = 'standard') {
        $this->databaseService = new DatabaseService($database);
    }
    
    // Specify the valid columns for the materials table
    protected function getValidColumns()
    {
        return [
            'material_name', 
            'material_type', 
            'created_date', 
            'updated_date', 
            'status'
        ];
    }

    // Define the table name for this model
    protected function getTableName()
    {
        return 'hl_materials';
    }

    // Method to set values from an array
    public function setValues($data)
    {
        foreach ($data as $key => $value) {
            if (property_exists($this, $key)) {
                $this->$key = $value;
            }
        }
    }

    // Insert method for creating a new material
    public function insert($data)
    {
        $this->setValues($data);

        // Prepare query and parameters
        $query = "INSERT INTO " . $this->getTableName() . " 
                  (material_name, material_type, created_date, status) 
                  VALUES (:material_name, :material_type, :created_date, :status)";

        $params = [
            ':material_name' => $this->material_name,
            ':material_type' => $this->material_type,
            ':created_date' => $this->created_date,
            ':status' => $this->status
        ];

        return $this->databaseService->executeUpdate($query, $params);
    }

    // Create method for adding a new material (alias of insert for this case)
    public function create($data)
    {
        return $this->insert($data);
    }

    
    // Delete method to remove a material by its ID
    public function delete($id)
    {
        $query = "DELETE FROM " . $this->getTableName() . " WHERE id = :id";
        $params = [':id' => $id];
        return $this->databaseService->executeUpdate($query, $params);
    }
}


    public function findByFileName($filename) {
        $query = "SELECT id, title, tips, foreign_title_1, foreign_title_2, lang1, lang2, format, audience, contact, filename, category, downloads, active, active_date, size, print_size, ordered
                  FROM hl_materials 
                  WHERE filename = :filename
                  LIMIT 1";
        $params = [':filename' => $filename];
        $results = $this->databaseService->executeQuery($query, $params);
        return $results->fetch(PDO::FETCH_ASSOC);
    }
    public function findById($id) {
        $query = "SELECT id, title, tips, foreign_title_1, foreign_title_2, lang1, lang2, format, audience, contact, filename, category, downloads, active, active_date, size, print_size, ordered
                  FROM hl_materials 
                  WHERE id = :id
                  LIMIT 1";
        $params = [':id' => $id];
        $results = $this->databaseService->executeQuery($query, $params);
        return $results->fetch(PDO::FETCH_ASSOC);
    }
    
    public function incrementDownloads($id) {
        $query = "UPDATE hl_materials 
                  SET downloads = downloads + 1 
                  WHERE id = :id";
        $params = [':id' => $id];
        return $this->databaseService->executeUpdate($query, $params);
    }
    // Example getter methods for specific fields can be implemented as needed
}
