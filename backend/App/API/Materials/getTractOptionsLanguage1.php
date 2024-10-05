<?php

use App\Services\Database\DatabaseService;
use App\Controllers\Materials\TractOptionsLanguage1Controller;

$databaseService = new DatabaseService($database = 'standard'); // Connect to the 'standard' database for tract data<?php

$tractController = new TractOptionsLanguage1Controller($databaseService);

switch ($tract_type) {
    case 'bilingual-book':
        $data = $tractController->getDistinctLang1BilingualBooks();
        break;
    case 'bilingual-page':
        $data = $tractController->getDistinctLang1BilingualPages();
        break;
    case 'monolingual-book':
        $data = $tractController->getDistinctLang1MonolingualBooks();
        break;
    case 'monolingual-page':
        $data = $tractController->getDistinctLang1MonolingualPages();
        break;
    default:
        $data = $tractController->getDistinctLang1();
        break;
}

writeLog('getTraxtOptionsLanguage1-28' , $data);
// Output the response as JSON
header('Content-Type: application/json');
echo json_encode($data);