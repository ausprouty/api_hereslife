<?php

// Import necessary classes
use App\Services\Security\UserAuthorizationService;
use App\Services\Database\DatabaseService;
use App\Repositories\ChampionRepository;


/**
 * Initiator script verifying that user exists 
 * so we can determine which fields to show on forms.
 *  
 * */

// Instantiate necessary services
$databaseService = new DatabaseService();  // Database connection handler
$championRepository = new ChampionRepository($databaseService);  // Repository for user data
$userAuthorizationService = new UserAuthorizationService($championRepository);  // Service for authorization
 // Controller to handle subscription actions
writeLog('UserVerify-20', $postData);
if (!$userAuthorizationService->verifyWordpressNonce($postData['wpnonce'], $postData['action'])) {
    // If the nonce is invalid, return an error
    $response = [
        'status' => 'error',
        'message' => 'Invalid nonce.'
    ];
    echo json_encode($response);
    exit;
}
$cid = $championRepository->getCidByEmail($postData['email']);
// Set content type to JSON
header('Content-Type: application/json');
$response = [
    'status' => 'success',
    'cid' => $cid
];
// Output the response in JSON format
echo json_encode($response);

