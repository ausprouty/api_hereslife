<?php

use App\Services\Database\DatabaseService;
use App\Controllers\Materials\TractOptionsPageSizeController;

$databaseService = new DatabaseService($database = 'standard'); // Connect to the 'standard' database for tract data<?php

$tractController = new TractOptionsPageSizeController($databaseService);

switch ($tract_type) {
    case 'bilingual-books':
        $data = $tractController->getDistinctPageSizeBilingualBooks($lang1, $lang2, $audience);
        break;
    case 'bilingual-pages':
        $data = $tractController->getDistinctPageSizeBilingualPages($lang1, $lang2, $audience);
        break;
    case 'monolingual-books':
        $data = $tractController->getDistinctPageSizeMonolingualBooks($lang1, $audience);
        break;
    case 'monolingual-pages':
        $data = $tractController->getDistinctPageSizeMonolingualPages($lang1, $audience);
        break;
    default:
        $data = []; // Return an empty array if the tract type is not recognized]   
        break;
}

writeLog('getTractOptionsPageSize-28' , $data);
// Output the response as JSON
header('Content-Type: application/json');
echo json_encode($data);