<?php

namespace App\Services\Emails;

use App\Services\Database\DatabaseService;

/**
 * Class EmailSubscriptionService
 *
 * This service handles the process of unsubscribing users from email communication. 
 * It manages multiple tasks such as blocking the user's email, removing them from email queues, 
 * and updating their email subscription status.
 */
class EmailSubscriptionService
{
    /**
     * @var DatabaseService $databaseService The database service for executing queries
     */
    private $databaseService;

    /**
     * EmailSubscriptionService constructor.
     *
     * Initializes the service with the required database service.
     * 
     * @param DatabaseService $databaseService The database service for executing database operations
     */
    public function __construct(DatabaseService $databaseService)
    {
        $this->databaseService = $databaseService;
    }

    /**
     * Unsubscribe a user from all email communications.
     *
     * This method performs several operations:
     * 1. Adds the user's email to the block list.
     * 2. Erases the user's email from the Champion table.
     * 3. Deletes the user's queued emails.
     * 4. Unsubscribes the user from any email subscriptions.
     *
     * @param int $cid The unique identifier for the champion/user
     * @return void
     */
    public function unsubscribeUser($cid)
    {
        $email = $this->addToBlockList($cid);
        $this->deleteEmailAddress($cid);
        $this->deleteFromEmailQue($cid);
        $this->unsubscribeFromEmailSubscription($cid);
    }

    /**
     * Remove the user's email from the Champion table.
     *
     * This sets the `email` field to an empty string and updates the `unsubscribed_date` to the current timestamp.
     *
     * @param int $cid The unique identifier for the champion/user
     * @return void
     */
    private function deleteEmailAddress($cid)
    {
        $data = [
            'email' => '',  // Set the email to an empty string
            'unsubscribed_date' => 'NOW()'  // Use NOW() to set the current timestamp
        ];

        // Update the specific champion's email and unsubscribed_date by cid
        $query = "UPDATE hl_champion SET email = :email, unsubscribed_date = NOW() WHERE cid = :cid";
        $params = [':email' => $data['email'], ':cid' => $cid];

        $this->databaseService->executeUpdate($query, $params);
    }

    /**
     * Delete any emails in the queue for the given user (by cid).
     *
     * This removes all records from the `hl_email_que` table associated with the specified `cid`.
     *
     * @param int $cid The unique identifier for the champion/user
     * @return int|bool Number of affected rows or false on failure
     */
    private function deleteFromEmailQue($cid)
    {
        $query = "DELETE FROM hl_email_que WHERE cid = :cid";
        $params = [':cid' => $cid];

        return $this->databaseService->executeUpdate($query, $params);
    }

    /**
     * Unsubscribe the user from all email subscriptions.
     *
     * This updates the `unsubscribed_date` for the user in the `hl_series_members` table.
     *
     * @param int $cid The unique identifier for the champion/user
     * @return int|bool Number of affected rows or false on failure
     */
    private function unsubscribeFromEmailSubscription($cid)
    {
        $query = "UPDATE hl_series_members SET unsubscribed_date = NOW() WHERE cid = :cid";
        $params = [':cid' => $cid];

        return $this->databaseService->executeUpdate($query, $params);
    }

    /**
     * Add the user's email to the email block list.
     *
     * Retrieves the user's email based on the `cid` and inserts it into the `hl_email_blocklist` table.
     *
     * @param int $cid The unique identifier for the champion/user
     * @return string The email address added to the block list
     */
    private function addToBlockList($cid)
    {
        // Retrieve the email associated with the given cid
        $query = "SELECT email FROM hl_champion WHERE cid = :cid";
        $params = [':cid' => $cid];

        $email = $this->databaseService->fetchOne($query, $params);

        // Insert the email into the blocklist
        $insertQuery = "INSERT INTO hl_email_blocklist (email) VALUES (:email)";
        $insertParams = [':email' => $email];

        $this->databaseService->executeUpdate($insertQuery, $insertParams);

        return $email;
    }
}
