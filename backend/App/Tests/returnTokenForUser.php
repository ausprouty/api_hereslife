<?php

// Import necessary classes
use App\Services\Security\UserAuthorizationService;
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

$hash = $userAuthorizationService->generateUserHash($cid);  // Generate a hash for the user

// Log the result of the operation
writeLog('returnTokenForUser-21', $hash);

// Set content type to JSON
header('Content-Type: application/json');

// Output the response in JSON format
echo json_encode ("$cid/$hash");

