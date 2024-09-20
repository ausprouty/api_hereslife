<?php

namespace App\Controllers\Emails;
use App\Services\DatabaseService;
use App\Services\Emails\MailerService;
use PDO;

class EmailQueueController
{
    private $databaseService;
    private $mailer;
    
    public function __construct(DatabaseService $databaseService, MailerService $mailer)
    {
        $this->databaseService = $databaseService;
        $this->mailer = $mailer;
    }

    public function processQueue()
    {
        // Fetch emails from queue where delay_until is NULL or in the past
        $query = "SELECT id, champion_id, email_content, bcc FROM hl_email_que 
                  WHERE delay_until IS NULL OR delay_until <= NOW() 
                  ORDER BY id ASC 
                  LIMIT :emails_per_que";

        $params = [':emails_per_que' => EMAILS_PER_QUE_CALL];

        $results = $this->databaseService->executeQuery($query, $params);
        $queueItems = $results->fetchAll(PDO::FETCH_ASSOC);

        foreach ($queueItems as $queueItem) {
            $this->processQueueItem($queueItem);
        }
    }

    private function processQueueItem($queueItem)
    {
        // Check if enough time has passed since the last email to this champion
        $trackingQuery = "SELECT date_sent FROM hl_email_tracking 
                          WHERE champion_id = :champion_id 
                          ORDER BY date_sent DESC LIMIT 1";
        
        $trackingParams = [':champion_id' => $queueItem['champion_id']];
        $trackingResult = $this->databaseService->executeQuery($trackingQuery, $trackingParams);
        $lastSent = $trackingResult->fetch(PDO::FETCH_ASSOC);

        // If the champion received an email recently, update delay_until
        if ($lastSent && strtotime($lastSent['date_sent']) > strtotime('-' . DAYS_BETWEEN_TIP_EMAILS . ' days')) {
            $newDelay = date('Y-m-d H:i:s', strtotime($lastSent['date_sent'] . ' + ' . DAYS_BETWEEN_TIP_EMAILS . ' days'));

            $updateDelayQuery = "UPDATE hl_email_que SET delay_until = :new_delay WHERE id = :id";
            $this->databaseService->executeUpdate($updateDelayQuery, [
                ':new_delay' => $newDelay,
                ':id' => $queueItem['id']
            ]);

            return; // Skip this email for now
        }

        // Send the email using MailerService
        $email_response = $this->mailer->sendEmail(
            $queueItem['email_content']['address'],  // Recipient address
            $queueItem['email_content']['name'],                             // Recipient name
            $queueItem['email_content']['subject'],  // Email subject
            $queueItem['email_content']['body'],     // Email body
            $queueItem['bcc']                        // BCC address
        );

        // If the email is sent successfully
        if ($email_response) {
            $this->updateEmailTracking($queueItem['champion_id']);
            $this->removeFromQueue($queueItem['id']);
        }
    }

    private function updateEmailTracking($championId)
    {
        $insertTrackingQuery = "INSERT INTO hl_email_tracking (champion_id, date_sent) VALUES (:champion_id, NOW())";
        $this->databaseService->executeUpdate($insertTrackingQuery, [':champion_id' => $championId]);
    }

    private function removeFromQueue($id)
    {
        $removeQuery = "DELETE FROM hl_email_que WHERE id = :id";
        $this->databaseService->executeUpdate($removeQuery, [':id' => $id]);
    }
}
