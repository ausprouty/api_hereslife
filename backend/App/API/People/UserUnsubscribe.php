<?php

// Import necessary classes
use App\Services\Security\UserAuthorizationService;
use App\Services\Emails\EmailSubscriptionService;
use App\Services\Database\DatabaseService;
use App\Repositories\ChampionRepository;

/**
 * Initiator script for handling user unsubscription.
 * 
 * This script checks the provided user hash using the UserAuthorizationService.
 * If valid, it processes the unsubscribe request using the EmailSubscriptionService.
 */

// Instantiate necessary services
$databaseService = new DatabaseService();  // Database connection handler
$championRepository = new ChampionRepository($databaseService);  // Repository for user data
$userAuthorizationService = new UserAuthorizationService($championRepository);  // Service for authorization$emailSubscriptionService = new EmailSubscriptionService($databaseService);  // Service for managing email subscriptions
$emailSubscriptionService = new EmailSubscriptionService($databaseService);  // Service for managing email subscriptions


// Check user authorization using cid and hash from postData
if ($userAuthorizationService->checkUserHash($postData['cid'], $postData['hash'])) {
    // Unsubscribe the user using the EmailSubscriptionService
    $emailSubscriptionService->unsubscribeUser($postData['cid']);
    // Prepare the success response
    $data = [
        'success' => true,  // Unsubscription was successful
        'message' => 'Successfully removed from mailing lists'
    ];
} else {
    // Prepare the failure response
    $data = [
        'success' => false,  // Authorization failed
        'message' => 'Either you have already unsubscribed or the link is invalid'
    ];
}

// Set the response content type to JSON
header('Content-Type: application/json');

// Return the response as JSON
echo json_encode($data);
