<?php

use App\Services\Database\DatabaseService;
use App\Controllers\Materials\TractController;

$databaseService = new DatabaseService($database = 'standard'); // Connect to the 'standard' database for tract data<?php

$tractController = new TractController($databaseService);
$data = $tractController->getDistinctBilingualContacts($lang1, $lang2, $audience, $format);
// Output the response as JSON
header('Content-Type: application/json');
echo json_encode($data);