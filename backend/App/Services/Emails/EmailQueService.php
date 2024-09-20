<?php

Namespace App\Services\Emails;

use App\Services\Database\DatabaseService;
use App\Services\Emails\EmailFormatService;
use App\Services\Emails\MailerService;
use PDO;

class EmailQueService



{
    /**
     * @var DatabaseService
     */
    private $databaseService;
    private $emailFormatService;
    private $mailer;

    /**
     * EmailQueueService constructor.
     * 
     * @param DatabaseService $databaseService The database service used for executing queries.
     * @param EmailFormatService $emailFormatService Service for formatting emails.
     */
    public function __construct(DatabaseService $databaseService, EmailFormatService $emailFormatService, MailerService $mailer)
    {
        $this->databaseService = $databaseService;
        $this->emailFormatService = $emailFormatService;
        $this->mailer = $mailer;
    }


    /**
     * Process a batch of email queue items, ensuring champions are eligible for email based on last_email_date.
     * 
     * The method retrieves emails from the queue where delay_until is NULL or in the past and 
     * only processes champions whose last_email_date is more than DAYS_BETWEEN_EMAILS ago, or NULL.
     * 
     * It processes emails in batches defined by EMAILS_PER_QUE_CALL and ensures each batch 
     * waits for the next cron job before processing the next set of emails.
     */
    public function processQueue()
    {
       /*
        * Query to retrieve pending email queue records (`hl_email_que`) along with champion information (`hl_champions`)
        * based on specific conditions.
        *
        * - Retrieves all rows from `hl_email_que` where:
        *   - The `delay_until` field is either `NULL` or contains a date/time less than or equal to the current timestamp.
        *   - Optionally filters champions based on the last time they received an email. If a champion has a `last_email_date`
        *     value, it must be older than a specified number of days (`:days_between_emails`).
        *
        * - Joins the `hl_email_que` with `hl_champions` using a `LEFT JOIN` to ensure all records from `hl_email_que` are
        *   retrieved even if there is no matching champion record.
        *
        * - The results are ordered by `q.id` in ascending order and limited by the `:emails_per_que` parameter.
        *
        * Parameters:
        * - :days_between_emails: The minimum number of days between emails for a champion.
        * - :emails_per_que: The maximum number of records to return in the result set.
        *
        * Columns returned:
        * - All columns from `hl_email_que` (`q.*`).
        * - From `hl_champions`: `first_name`, `email`, `gender`, and `last_email_date` (may be `NULL` if no matching champion).
        *
        * Notes:
        * - If no matching record is found in `hl_champions` for a `champion_id` in `hl_email_que`, the champion details 
        *   (e.g., `first_name`, `email`) will be `NULL`, but the queue record from `hl_email_que` will still be returned.
        */
        $query = "
            SELECT q.*, 
            c.first_name, c.email AS champion_email, c.gender, c.last_email_date
            FROM hl_email_que q
            LEFT JOIN hl_champions c ON q.champion_id = c.cid
            WHERE (q.delay_until IS NULL OR q.delay_until <= NOW())
            AND (c.last_email_date IS NULL OR c.last_email_date <= NOW() - INTERVAL :days_between_emails DAY)
            ORDER BY q.id ASC 
            LIMIT :emails_per_que";
    
        
        // Parameters: batch size and the minimum days between emails, cast as integers
        $params = [
            ':emails_per_que' => (int)EMAILS_PER_QUE_CALL,
            ':days_between_emails' => (int)DAYS_BETWEEN_EMAILS
        ];
        error_log('Query: ' . $query);
        error_log('Params: ' . print_r($params, true));
        // Execute the query and fetch the results
        $results = $this->databaseService->executeQuery($query, $params);
        $queueItems = $results->fetchAll(PDO::FETCH_ASSOC);

        // Loop through each queue item and process it
        foreach ($queueItems as $queueItem) {
            /**
             * processQueueItem:
             * This method will handle the logic for processing each email in the queue.
             * It could involve formatting the email, setting recipients, and finally sending the email.
             */
            $this->processQueueItem($queueItem);
        }
    }

   /**
     * Process an individual queue item.
     * 
     * @param array $queueItem The queue item to process.
     */
    private function processQueueItem(array $queueItem)
    {
        writeLog('EmailQueService::processQueueItem', $queueItem);
        // Use the EmailFormatService to format the email content
        $formattedEmail = $this->emailFormatService->setValues($queueItem);

        // Send the formatted email (the actual sending logic can be implemented separately)
        $this->sendEmail($formattedEmail);
    }

    /**
     * Send the email (Placeholder for actual email sending logic).
     * 
     * @param array $emailData The formatted email data.
     */
    private function sendEmail(array $emailData)
    {
        // Implement email sending logic here
    }
}

