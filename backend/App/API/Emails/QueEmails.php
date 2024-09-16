<?php
/* this file is called from the front end to queue a series of emails 
   and expects the following parameters:
        letterId: from hl_email
        groupCode: pass to ChampionEmailAddressService
*/

use App\Controllers\Emails\EmailQueController;
use App\Models\People\ChampionModel;
use App\Services\Database\DatabaseService;
use App\Utilities\RequestValidator;

// Step 1: Fetch post data (assuming you're getting this from a frontend request)
// In a real-world scenario, you'd likely use a request handler or framework to get the POST data.
$postData = $_POST;  // This assumes your data is sent via POST

// Step 2: Validate request and authorization
// Use RequestValidator to validate admin authorization for this action.
RequestValidator::validateAdmin($postData, 'QueEmails');

// Step 3: Instantiate the necessary services
// Initialize the DatabaseService which handles interactions with the database.
$databaseService = new DatabaseService();

// Step 4: Get champions matching the group code
// Initialize the ChampionModel with the DatabaseService for database interactions.
$championModel = new ChampionModel($databaseService);
$champions = $championModel->getChampionEmails($postData['groupCode']); // Fetch champions based on group code

// Step 5: Queue the emails
// Instantiate the EmailQueController which might handle queuing operations for emails.
$emailQueController = new EmailQueController($databaseService);  // Pass the database service if needed in the controller
$result = $emailQueController->queEmails($champions, $postData['letterId']);  // Pass champions and letterId to queue emails

// Step 6: Prepare and output the response
$response = [
    'success' => true,  // Assume success if queuing operation completes
    'message' => $result,  // Return any message or result from queuing
]; 

header('Content-Type: application/json');
echo json_encode($response);
