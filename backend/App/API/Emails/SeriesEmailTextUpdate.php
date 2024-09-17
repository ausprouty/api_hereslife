<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
// Ensure necessary namespaces are imported
use App\Controllers\Data\PostInputController;
use App\Controllers\Emails\EmailController;
use App\Services\Database\DatabaseService;
use App\Utilities\RequestValidator;
use App\Models\Emails\EmailModel;

/**
 * Update an email in the series based on input data.
 *
 * This script processes a request to update an email in an email series. It validates
 * that the request is authorized by checking if the user is an admin. If validation passes,
 * it updates the email with the provided data using the `EmailController`. The script
 * returns a JSON response indicating the success of the update.
 * 
 * @param array $postData The input data for the email update, including fields such as
 *                        'series', 'sequence', 'subject', and 'body'.
 * 
 * @return void Outputs a JSON-encoded response indicating success.
 */

// Step 1: Fetch POST input data (assuming it's from a frontend form or API call)


// Step 2: Validate request and check admin authorization
// Use the RequestValidator to ensure that the request is from an authorized admin
RequestValidator::validateAdmin($postData, 'SeriesEmailTextUpdate');

// Step 3: Instantiate necessary services
// Initialize the DatabaseService to handle database interactions
$databaseService = new DatabaseService();

// Step 4: Instantiate the EmailController
// EmailController will use the EmailModel, which depends on DatabaseService
$emailController = new EmailController(new EmailModel($databaseService));
  
// Step 5: Update the email in the series using the EmailController
// Pass the $postData to update the email with provided series, sequence, subject, body, etc.
$data = $emailController->updateEmailFromInputData($postData);
  
// Step 6: Prepare the response array indicating success
$response = [
    'success' => true,                        // Status of the update operation
    'message' => 'Record updated successfully',  // Confirmation message
];

// Step 7: Set content type to JSON and output the response
header('Content-Type: application/json');
echo json_encode($response);
