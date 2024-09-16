<?php
// Import necessary namespaces
use App\Controllers\Emails\EmailController;
use App\Models\Emails\EmailModel;
use App\Services\Database\DatabaseService;

// Step 1: Initialize Database Service
// This service will handle interactions with the database
$databaseService = new DatabaseService();

// Step 2: Initialize Email Model
// The EmailModel class is responsible for the business logic related to emails.
// It receives the DatabaseService instance through dependency injection to handle database operations.
$emailModel = new EmailModel($databaseService);

// Step 3: Initialize Email Controller
// The EmailController class handles HTTP requests and works with the EmailModel to process data.
$emailController = new EmailController($emailModel);

// Step 4: Get the Number of Blog Titles
// Assume $number is passed via a route (e.g., from a GET or POST request).
// This number determines how many recent blog titles to fetch.
$titles = $emailController->getRecentBlogTitles($number); // The number of titles to fetch is determined by the route.

// Step 5: Prepare JSON Response
// Create a response array containing the success status and the data (blog titles).
$response = [
    'success' => true,
    'data' => $titles,
];

// Step 6: Return the Response
// Encode the response array to JSON format and output it.
// This will return the response as a JSON object, which can be consumed by the frontend or an API client.
echo json_encode($response);

// Stop further execution of the script (if necessary).
return;
