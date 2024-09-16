<?php
// Ensure necessary namespaces are imported
use App\Controllers\Emails\EmailController;
use App\Services\Database\DatabaseService;

/**
 * Fetch and return a formatted email by its ID in JSON format.
 *
 * This script allows access to the email data from the database, formats it for display,
 * and returns it as a JSON-encoded response. The `EmailController` is responsible for
 * handling the request and formatting the email data for view. The ID is passed from
 * the router, not through GET or POST requests.
 *
 * @param int $id The ID of the email to fetch and format (passed from the router).
 * @return void Outputs the JSON-encoded response.
 */

// Step 1: Validate the email ID passed from the router
// Ensure that the $id is a valid non-negative integer.
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
// Initialize the DatabaseService, which will handle database operations.
$databaseService = new DatabaseService();

// Step 3: Instantiate the EmailController
// The EmailController uses the EmailModel, which interacts with the database via the DatabaseService.
$emailController = new EmailController(new \App\Models\Emails\EmailModel($databaseService));

// Step 4: Fetch and format the email data by ID
// Call the formatForView method to retrieve and format the email for view.
$data = $emailController->formatForView($id);

// Step 5: Prepare the response array
$response = [
    'success' => true,  // Indicates successful formatting and fetching of the email data
    'data' => $data,    // The formatted email data
];

// Step 6: Set the header for JSON response
header('Content-Type: application/json');

// Step 7: JSON encoding and output
echo json_encode($response);
