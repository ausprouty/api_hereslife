<?php

use App\Services\Database\DatabaseService;
use App\Controllers\Materials\TractOptionsLanguage2Controller;

$databaseService = new DatabaseService($database = 'standard'); // Connect to the 'standard' database for tract data<?php

$tractController = new TractOptionsLanguage2Controller($databaseService);
writeLog('getTractOptionsLanguage2-9' , $tract_type . ' ' . $lang1);
switch ($tract_type) {
    case 'bilingual-books':
        $data = $tractController->getDistinctLang2BilingualBooks($lang1);
        break;
    case 'bilingual-pages':
        $data = $tractController->getDistinctLang2BilingualPages($lang1);
        break;
    default:
        $data = []; // Return an empty array if the tract type is not recognized]
        break;
}

writeLog('getTractOptionsLanguage2-28' , $data);
// Output the response as JSON
header('Content-Type: application/json');
echo json_encode($data);