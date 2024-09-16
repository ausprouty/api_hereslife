<?php

use App\Controllers\Emails\EmailSeriesController;
use App\Models\Emails\EmailSeriesModel;
use App\Services\Database\DatabaseService;
use App\Services\Emails\EmailSeriesService;

/**
 * Fetch and return emails based on the series in JSON format.
 */

// Validate input (assuming $series is provided by Middleware)
if (empty($series) || !is_string($series)) {
    $response = [
        'success' => false,
        'message' => 'Invalid series name provided.',
    ];
    
    header('Content-Type: application/json');
    echo json_encode($response);
    return;
}

// Step 1: Instantiate the necessary services
$databaseService = new DatabaseService();
$emailSeriesModel = new EmailSeriesModel($databaseService);
$emailSeriesService = new EmailSeriesService($emailSeriesModel);
$emailSeriesController = new EmailSeriesController($emailSeriesService);

// Step 2: Fetch the email data by series
$data = $emailSeriesController->getEmailsBySeries($series);

// Step 3: Prepare and output the response as JSON
/*
//  Return the response array
                return [
                    'success' => true,
                    'data'    => $data
                ];
            } else {
                // Step 5: Return the response for no data found
                return [
                    'success' => false,
                    'message' => 'No emails found for this series.'
                ];
    
*/
header('Content-Type: application/json');
echo json_encode($data);
