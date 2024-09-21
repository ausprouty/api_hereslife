<?php

// Import necessary classes
use App\Services\Authorization\UserAuthorizationService;
use App\Services\Database\DatabaseService;
use App\Repositories\ChampionRepository;
use App\Services\Emails\EmailSubscriptionService;
use App\Controllers\Emails\EmailSubscriptionController;

/**
 * Initiator script for handling user unsubscription.
 * 
 * This script checks the provided user hash using the UserAuthorizationService.
 * If valid, it processes the unsubscribe request using the EmailSubscriptionController.
 */

// Instantiate necessary services
$databaseService = new DatabaseService();  // Database connection handler
$championRepository = new ChampionRepository($databaseService);  // Repository for user data
$userAuthorizationService = new UserAuthorizationService($championRepository);  // Service for authorization
$emailSubscriptionService = new EmailSubscriptionService();  // Service for managing email subscriptions
$emailSubscriptionController = new EmailSubscriptionController($emailSubscriptionService);  // Controller to handle subscription actions

// Checking user authorization and handling unsubscription
if ($userAuthorizationService->checkUserHash($postData['cid'], $postData['hash'])) {
    
    // Unsubscribe the user
    $emailSubscriptionController->unsubscribe($postData['cid']);
    
    // Prepare the success response
    $data = [
        'success' => 'TRUE',  // Indicates the unsubscription was successful
        'message' => 'Successfully removed from mailing lists'
    ]; 
} else {
    // Prepare the failure response
    $data = [
        'success' => 'FALSE',  // Indicates the link is invalid
        'message' => 'This link is invalid'
    ];
}

// Log the result of the operation
writeLog('UserUnsubscribe-21', $data);

// Set content type to JSON
header('Content-Type: application/json');

// Output the response in JSON format
echo json_encode($data);

