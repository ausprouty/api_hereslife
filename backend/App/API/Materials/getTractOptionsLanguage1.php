<?php

use App\Services\Database\DatabaseService;
use App\Controllers\Materials\TractOptionsLanguage1Controller;

$databaseService = new DatabaseService($database = 'standard'); // Connect to the 'standard' database for tract data<?php

$tractController = new TractOptionsLanguage1Controller($databaseService);

switch ($tract_type) {
    case 'bilingual-books':
        $data = $tractController->getDistinctLang1BilingualBooks();
        break;
    case 'bilingual-pages':
        $data = $tractController->getDistinctLang1BilingualPages();
        break;
    case 'monolingual-tracts':
        $data = $tractController->getDistinctLang1MonolingualBooks();
        break;
    case 'monolingual-pages':
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