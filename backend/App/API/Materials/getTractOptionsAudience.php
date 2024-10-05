<?php

use App\Services\Database\DatabaseService;
use App\Controllers\Materials\TractOptionsAudienceController;

$databaseService = new DatabaseService($database = 'standard'); // Connect to the 'standard' database for tract data<?php

$tractController = new TractOptionsAudienceController($databaseService);

switch ($tract_type) {
    case 'bilingual-book':
        $data = $tractController->getDistinctAudienceBilingualBooks($lang1, $lang2);
        break;
    case 'bilingual-page':
        $data = $tractController->getDistinctAudienceBilingualPages($lang1, $lang2);
        break;
    case 'monolingual-book':
        $data = $tractController->getDistinctAudienceMonolingualBooks($lang1);
        break;
    case 'monolingual-page':
        $data = $tractController->getDistinctAudienceMonolingualPages($lang1);
        break;
    default:
        $data = []; // Return an empty array if the tract type is not recognized]
        break;
}

writeLog('getTractOptionsAudience-28' , $data);
// Output the response as JSON
header('Content-Type: application/json');
echo json_encode($data);