<?php
// Ensure necessary namespaces are imported
use App\Controllers\Emails\EmailController;
use App\Services\Database\DatabaseService;
use App\Models\Emails\EmailModel;

/**
 * Fetch and return an email based on the series and sequence number in JSON format.
 *
 * This script allows access to the database to fetch an email by providing
 * the series and sequence number. The `EmailController` is responsible for handling
 * the request and fetching the relevant email. The output is returned as a 
 * JSON-encoded response.
 *
 * @param string $series The name of the email series to retrieve (from router).
 * @param int $sequence The sequence number of the email in the series (from router).
 * 
 * @return void Outputs the JSON-encoded response with the fetched email data.
 */

// Assuming $series and $sequence are passed from the router
// Validate inputs (Router should pass valid types, but still good to validate)
$sequence = (int) $sequence;  // Explicitly cast sequence to integer
if (empty($series) || !is_string($series) || is_null($sequence) || !is_int($sequence)) {
    // Invalid inputs, return an error response
    $response = [
        'success' => false,
        'message' => "Invalid series ($series) or sequence number ($sequence) provided."
    ];
    
    header('Content-Type: application/json');
    echo json_encode($response);
    return;
}

// Step 1: Instantiate the necessary services
// Initialize the DatabaseService to handle database interactions
$databaseService = new DatabaseService();

// Step 2: Instantiate the EmailModel
// EmailModel will interact with the database via the DatabaseService
$emailModel = new EmailModel($databaseService);

// Step 3: Instantiate the EmailController
// Inject the EmailModel into the EmailController
$emailController = new EmailController($emailModel);

// Step 4: Fetch the email data by series and sequence
// Use the EmailController to fetch the email based on series and sequence
$data = $emailController->getEmailBySeriesAndSequence($series, $sequence);

// Step 5: Prepare the response array
$response = [
    'success' => true,  // Indicates successful fetching of the email
    'data' => $data,    // The fetched email data
];

// Step 6: JSON encoding and output
header('Content-Type: application/json');
echo json_encode($response);
