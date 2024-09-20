<?php

use App\Services\Emails\EmailQueService;
use App\Services\Emails\EmailFormatService;
use App\Services\Database\DatabaseService;
use App\Services\Emails\MailerService;
use App\Models\Emails\EmailModel;

/**
 * This script handles the processing of the email queue, formatting the emails, 
 * sending them, and returning a JSON response indicating the result of the process.
 *
 * Flow:
 * 1. Services are instantiated (DatabaseService, EmailFormatService, MailerService).
 * 2. The EmailQueueService is used to process the queue.
 * 3. A JSON response is returned with success status and a message.
 */

// Instantiate the necessary services
$databaseService = new DatabaseService();
$emailModel = new EmailModel($databaseService);

$emailFormatService = new EmailFormatService($databaseService, $emailModel);
$mailer = new MailerService(DEFAULT_EMAIL_ADDRESS, DEFAULT_EMAIL_SENDER);

// Instantiate the EmailQueueService to handle email queue processing
$queController = new EmailQueService($databaseService, $emailFormatService, $mailer);

// Process the email queue and capture the result
$email_response = $queController->processQueue();

// Prepare the response array based on the result of email queue processing
$response = [
    'success' => $email_response,  // Indicates whether the emails were processed successfully
    'data' => $email_response ? 'Emails processed successfully' : 'Failed to process emails' // Response message
];

// Set content type to JSON and output the response
header('Content-Type: application/json');
echo json_encode($response);
return;
