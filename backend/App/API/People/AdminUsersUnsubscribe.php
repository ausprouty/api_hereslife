<?php

// Import necessary classes
use App\Services\Security\AdminAuthorizationService;
use App\Services\Emails\EmailSubscriptionService;
use App\Services\Database\DatabaseService;
use App\Repositories\ChampionRepository;
use App\Utilities\RequestValidator;

/**
 * Initiator script for handling admin user unsubscription.
 */

// Instantiate necessary services
$databaseService = new DatabaseService();  // Database connection handler
$championRepository = new ChampionRepository($databaseService);  // Repository for user data
$emailSubscriptionService = new EmailSubscriptionService($databaseService);  // Service for managing email subscriptions

// Retrieve POST data (assuming emails are sent as an array in POST request)


// Validate that the request comes from an admin
RequestValidator::validateAdmin($postData, 'AdminUsersUnsubscribe');  // Ensure only admins can perform this action

// Retrieve the list of emails from the POST data
$emails = $postData['emails'] ?? [];

if (empty($emails)) {
    // Prepare a response if no emails were provided
    $data = [
        'success' => false,
        'message' => 'No email addresses provided'
    ];
} else {
    // Arrays to track success and failures
    $unsubscribedUsers = [];
    $failedUsers = [];

    // Loop through each email and process the unsubscription
    foreach ($emails as $email) {
        // Lookup the cid for the given email in the hl_champions table

        $cid = $championRepository->getCidByEmail($email);

        if ($cid) {
            // Attempt to unsubscribe the user
            if ($emailSubscriptionService->unsubscribeUser($cid)) {
                $unsubscribedUsers[] = $email;
            } else {
                $failedUsers[] = $email;
            }
        } else {
             // we still need to add to emailBlocked table
            $emailSubscriptionService->addToBlockList($cid = NULL, $email);
            $failedUsers[] = $email;  // No matching cid found for this email
        }
    }

    // Prepare the success response
    $data = [
        'success' => true,
        'unsubscribed_emails' => $unsubscribedUsers,
        'failed_emails' => $failedUsers
    ];
}
writeLog('AdminUsersUnsubscribe-62', $data);
// Set the response content type to JSON
header('Content-Type: application/json');

// Return the response as JSON
echo json_encode($data);
