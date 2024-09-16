<?php

use App\Controllers\Emails\EmailSeriesMemberController;
use App\Models\Emails\EmailSeriesMemberModel;
use App\Models\Emails\EmailModel;
use App\Services\Emails\EmailTipsService;
use App\Services\Database\DatabaseService;
use App\Services\Emails\EmailService;
use App\Models\Emails\EmailQueModel;

/**
 * Initialize necessary services and models for handling email tips processing.
 */

// Instantiate the DatabaseService
$databaseService = new DatabaseService();

// Instantiate the EmailSeriesMemberModel with a DatabaseService dependency
$emailSeriesMemberModel = new EmailSeriesMemberModel($databaseService); 

// Instantiate the EmailModel with a DatabaseService dependency
$emailModel = new EmailModel($databaseService); 

// Instantiate the EmailQueModel with a DatabaseService dependency
$emailQueModel = new EmailQueModel($databaseService);  

// Instantiate the EmailTipsService using the EmailSeriesMemberModel
$emailTipsService = new EmailTipsService($emailSeriesMemberModel);

// Instantiate the EmailSeriesMemberController with required models
$emailSeriesMemberController = new EmailSeriesMemberController(
    $emailSeriesMemberModel, 
    $emailModel, 
    $emailQueModel
);

// Process new email tips and log an error if it fails
$result = $emailSeriesMemberController->processNextEmailTips();
if ($result) {
    echo $result . " email tips processed successfully.";
} else {
    error_log ("Error processing email tips.");
    echo "Error processing email tips.";
}   


