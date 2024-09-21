<?php
namespace App\Repositories;

use App\Models\People\ChampionModel;

use Exception;

class ChampionRepository extends BaseRepository
{
   

    // Find a champion by email
    public function findByEmail(string $email): ?ChampionModel
    {
        $query = "SELECT * FROM " . $this->getTableName() . " WHERE email = :email LIMIT 1";
        $params = [':email' => $email];

        try {
            $results = $this->databaseService->executeQuery($query, $params);
            $data = $results->fetch(\PDO::FETCH_ASSOC);

            if ($data) {
                return new ChampionModel($data); // Instantiate ChampionModel with the fetched data
            }

            return null;
        } catch (Exception $e) {
            writeLogError('ChampionRepository-findByEmail', $e->getMessage());
            return null;
        }
    }
     // Find email by user ID
     public function findEmailById($user_id)
     {
         // Logic to query the database for the email address based on user ID
         $query = "SELECT email FROM champions WHERE id = :user_id";
         $params = [':user_id' => $user_id];
         return $this->databaseService->fetchSingleValue($query, $params);
     }

    // Find a champion by cid
    public function findByCid(int $cid): ?ChampionModel
    {
        $query = "SELECT * FROM " . $this->getTableName() . " WHERE cid = :cid LIMIT 1";
        $params = [':cid' => $cid];

        try {
            $results = $this->databaseService->executeQuery($query, $params);
            $data = $results->fetch(\PDO::FETCH_ASSOC);

            if ($data) {
                return new ChampionModel($data); // Instantiate ChampionModel with the fetched data
            }

            return null;
        } catch (Exception $e) {
            writeLogError('ChampionRepository-findByCid', $e->getMessage());
            return null;
        }
    }

    // Save the champion model (insert or update based on whether cid exists)
    public function save(ChampionModel $champion)
    {
        if ($champion->getCid()) {
            $this->update($champion);
        } else {
            $this->insert($champion);
        }
    }

    // Insert a new champion
    private function insert(ChampionModel $champion)
    {
        $query = "INSERT INTO " . $this->getTableName() . " 
                  (first_name, surname, title, organization, address, suburb, state, postcode, country, phone, sms, email, gender, 
                  double_opt_in_date, first_email_date, last_open_date, consider_dropping_date, first_download_date, last_download_date, last_email_date)
                  VALUES 
                  (:first_name, :surname, :title, :organization, :address, :suburb, :state, :postcode, :country, :phone, :sms, :email, :gender, 
                  :double_opt_in_date, :first_email_date, :last_open_date, :consider_dropping_date, :first_download_date, :last_download_date, :last_email_date)";

        $params = $this->getParams($champion);

        try {
            $this->databaseService->executeUpdate($query, $params);
            $champion->setCid($this->databaseService->getLastInsertId());
        } catch (Exception $e) {
            writeLogError('ChampionRepository-insert', $e->getMessage());
        }
    }

    
    // Define the valid columns for this repository
    protected function getValidColumns()
    {
        return [
            'first_name', 
            'surname', 
            'title', 
            'organization', 
            'address', 
            'suburb', 
            'state', 
            'postcode', 
            'country', 
            'phone', 
            'sms', 
            'email', 
            'gender', 
            'double_opt_in_date', 
            'first_email_date', 
            'last_open_date', 
            'consider_dropping_date', 
            'first_download_date', 
            'last_download_date', 
            'last_email_date'
        ];
    }

    // Define the table name for this repository
    protected function getTableName()
    {
        return 'hl_champions';
    }
    
      
    // Helper method to get the parameter array from ChampionModel
    private function getParams(ChampionModel $champion): array
    {
        return [
            ':first_name' => $champion->getFirstName(),
            ':surname' => $champion->getSurname(),
            ':title' => $champion->getTitle(),
            ':organization' => $champion->getOrganization(),
            ':address' => $champion->getAddress(),
            ':suburb' => $champion->getSuburb(),
            ':state' => $champion->getState(),
            ':postcode' => $champion->getPostcode(),
            ':country' => $champion->getCountry(),
            ':phone' => $champion->getPhone(),
            ':sms' => $champion->getSms(),
            ':email' => $champion->getEmail(),
            ':gender' => $champion->getGender(),
            ':double_opt_in_date' => $champion->getDoubleOptInDate(),
            ':first_email_date' => $champion->getFirstEmailDate(),
            ':last_open_date' => $champion->getLastOpenDate(),
            ':consider_dropping_date' => $champion->getConsiderDroppingDate(),
            ':first_download_date' => $champion->getFirstDownloadDate(),
            ':last_download_date' => $champion->getLastDownloadDate(),
            ':last_email_date' => $champion->getLastEmailDate(),
        ];
    }

    // Define the table name for this model
    protected function getTableName()
    {
        return 'hl_champions';
    }
}
