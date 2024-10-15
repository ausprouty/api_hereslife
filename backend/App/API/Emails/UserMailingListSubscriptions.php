<?php

// Import necessary classes
use App\Controllers\Emails\EmailSubscriptionInfoController;
use App\Repositories\ChampionRepository;
use App\Repositories\EmailSeriesMemberRepository;
use App\Services\Database\DatabaseService;
use App\Services\Security\UserAuthorizationService;

/**
 * Initiator script verifying that user exists 
 * so we can determine which fields to show on forms.
 *  
 * */

// Instantiate necessary services
$databaseService = new DatabaseService();  // Database connection handler
$championRepository = new ChampionRepository($databaseService);  // Repository for user data
$userAuthorizationService = new UserAuthorizationService($championRepository);  // Service for authorization
$emailSeriesMemberRepository = new EmailSeriesMemberRepository($databaseService);   
$emailSubscriptionInfoController = new EmailSubscriptionInfoController( $championRepository, $emailSeriesMemberRepository);


if (!$userAuthorizationService->verifyWordpressNonce($postData['wpnonce'], $postData['action'])) {
    // If the nonce is invalid, return an error
    writeLog('UserMailingListSubscriptions-30', 'Invalid nonce.');
    $response = [
        'status' => 'error',
        'message' => 'Invalid nonce.'
    ];
    echo json_encode($response);
    exit;
}
if (!isset($postData['cid']) && !isset($postData['email'] )) {
    writeLog('UserMailingListSubscriptions-38', 'Email or Cid is required.');
    $response = [
        'status' => 'error',
        'message' => 'Email or Cid is required.'
    ];
    echo json_encode($response);
    exit;
}
if (!isset($postData['cid'])){ 
    $cid = $championRepository->getCidByEmail($postData['email']);
    if ($cid == NULL){
        $response = [
            'status' => 'success',
            'champion' => 'NULL'
        ];
        writeLog('UserMailingListSubscriptions-61', json_encode($response));
        echo json_encode($response);
        exit;
    }
    $postData['cid'] = $cid;
}
writeLog('UserMailingListSubscriptions-60', $postData['cid']);

$userMailingListInfo = $emailSubscriptionInfoController->getUserMailingListInfo($postData['cid']);
// Set content type to JSON
writeLog('UserMailingListSubscriptions-64', $userMailingListInfo);

$response = [
    'status' => 'success',
    'data' => $userMailingListInfo
];
// Output the response in JSON format
echo json_encode($response);

