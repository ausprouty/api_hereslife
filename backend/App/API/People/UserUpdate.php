<?php

// Import necessary classes
use App\Services\Authorization\UserAuthorizationService;
use App\Services\Database\DatabaseService;
use App\Repositories\ChampionRepository;


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
 // Controller to handle subscription actions

// Checking user authorization and handling unsubscription
if ($userAuthorizationService->checkUserHash($postData['cid'], $postData['hash'])) {
    
    // Update the user
    $ChampionRepository->update($postData);
    
    // Prepare the success response
    $data = [
        'success' => 'TRUE',  // Indicates the unsubscription was successful
        'message' => 'Details successfully updated'
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

