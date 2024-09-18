<?php

namespace App\Repositories;

use App\Models\Emails\EmailQueModel;
use Exception;

class EmailQueueRepository extends BaseRepository
{
    

    /**
     * Queue an email by inserting it into the database.
     *
     * @param EmailQueModel $emailQueModel
     * @return int Last inserted ID
     */
    public function queueEmail(EmailQueModel $emailQueModel) {
        $query = "INSERT INTO " . $this->getTableName() . " 
                  (delay_until, email_from, email_to, email_id, champion_id, subject, body, plain_text_only, headers, plain_text_body, template, params) 
                  VALUES 
                  (:delay_until, :email_from, :email_to, :email_id, :champion_id, :subject, :body, :plain_text_only, :headers, :plain_text_body, :template, :params)";
        
        $params = $this->getParams($emailQueModel);

        $this->databaseService->executeUpdate($query, $params);
        return $this->databaseService->getLastInsertId();
    }

    /**
     * Delete an email from the queue.
     *
     * @param int $id
     * @return bool
     */
    public function deleteEmail($id) {
        $query = "DELETE FROM " . $this->getTableName() . " WHERE id = :id";
        $params = [':id' => $id];
        return $this->databaseService->executeUpdate($query, $params);
    }

    /**
     * Track that an email was sent.
     *
     * @param int $emailId
     * @param int $championId
     */
    public function trackEmailSent($emailId, $championId) {
        $query = "INSERT INTO hl_email_tracking (email_id, champion_id, sent_at) VALUES (:email_id, :champion_id, NOW())";
        $params = [':email_id' => $emailId, ':champion_id' => $championId];
        $this->databaseService->executeUpdate($query, $params);
    }

    /**
     * Get the valid columns for the email queue table.
     *
     * @return array
     */
    protected function getValidColumns() {
        return [
            'delay_until', 
            'email_from', 
            'email_to', 
            'email_id', 
            'champion_id', 
            'subject', 
            'body', 
            'plain_text_only', 
            'headers', 
            'plain_text_body', 
            'template', 
            'params'
        ];
    }

    /**
     * Define the table name for this repository.
     *
     * @return string
     */
    protected function getTableName() {
        return 'hl_email_que';
    }

    /**
     * Get the parameter array from EmailQueModel.
     *
     * @param EmailQueModel $emailQueModel
     * @return array
     */
    private function getParams(EmailQueModel $emailQueModel): array {
        return [
            ':delay_until' => $emailQueModel->getDelayUntil(),
            ':email_from' => $emailQueModel->getEmailFrom(),
            ':email_to' => $emailQueModel->getEmailTo(),
            ':email_id' => $emailQueModel->getEmailId(),
            ':champion_id' => $emailQueModel->getChampionId(),
            ':subject' => $emailQueModel->getSubject(),
            ':body' => $emailQueModel->getBody(),
            ':plain_text_only' => $emailQueModel->getPlainTextOnly(),
            ':headers' => $emailQueModel->getHeaders(),
            ':plain_text_body' => $emailQueModel->getPlainTextBody(),
            ':template' => $emailQueModel->getTemplate(),
            ':params' => $emailQueModel->getParams()
        ];
    }
}
