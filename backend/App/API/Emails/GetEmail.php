<?php
// Ensure necessary namespaces are imported
use App\Controllers\Emails\EmailController;
use App\Services\Database\DatabaseService;

/**
 * Fetch and return an email by its ID in JSON format.
 *
 * This script is designed to fetch an email by its ID from the database
 * and return it as a JSON response. The `EmailController` is responsible
 * for handling the request and fetching the data. The ID is passed from
 * the router, not from GET or POST requests.
 *
 * @param int $id The ID of the email to fetch.
 * @return void Outputs the JSON-encoded response.
 */

// Step 1: Validate the email ID passed from the router
// Check if the $id is a valid integer (including 0 as valid)
if (!is_int($id) || $id < 0) {
    // Invalid ID, return an error response
    $response = [
        'success' => false,
        'message' => 'Invalid email ID provided.',
    ];

    header('Content-Type: application/json');
    echo json_encode($response);
    return;
}

// Step 2: Instantiate necessary services
// Initialize the DatabaseService, which handles interactions with the database.
$databaseService = new DatabaseService();

// Step 3: Instantiate the EmailController
// The EmailController is responsible for handling email-related requests.
// It requires the EmailModel (which requires the DatabaseService) to function.
$emailController = new EmailController(new \App\Models\Emails\EmailModel($databaseService));

// Step 4: Fetch the email data by ID
// The getEmailById method is called to fetch the email data associated with the given ID.
$data = $emailController->getEmailById($id);

// Check if the data was successfully fetched
if (!$data) {
    // If no email was found for the provided ID, return an error response
    $response = [
        'success' => false,
        'message' => 'Email not found.',
    ];
} else {
    // Step 5: Prepare the response array
    // This will contain a success flag and the actual email data.
    $response = [
        'success' => true,  // Indicates successful fetching of the data
        'data' => $data,    // The fetched email data
    ];
}

// Step 6: JSON encoding and output
// Set the content type to JSON and return the response.
header('Content-Type: application/json');
echo json_encode($response);
