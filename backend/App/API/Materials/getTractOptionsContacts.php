<?php

use App\Services\Database\DatabaseService;
use App\Controllers\Materials\TractOptionsContactsController;

$databaseService = new DatabaseService($database = 'standard'); // Connect to the 'standard' database for tract data<?php

$tractController = new TractOptionsContactsController($databaseService);

switch ($tract_type) {
    case 'bilingual-book':
        $data = $tractController->getDistinctContactsBilingualBooks($lang1, $lang2, $audience, $pagesize);
        break;
    case 'bilingual-page':
        $data = $tractController->getDistinctContactsBilingualPages($lang1, $lang2, $audience, $pagesize);
        break;
    case 'monolingual-book':
        $data = $tractController->getDistinctContactsMonolingualBooks($lang1, $audience, $pagesize);
        break;
    case 'monolingual-page':
        $data = $tractController->getDistinctContactsMonolingualPages($lang1,$audience, $pagesize);
        break;
    default:
        $data = []; // Return an empty array if the tract type is not recognized]
        break;
}

writeLog('getTractOptionsContacts-28' , $data);
// Output the response as JSON
header('Content-Type: application/json');
echo json_encode($data);