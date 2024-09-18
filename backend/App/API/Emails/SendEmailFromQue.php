<?php
use App\Controllers\Emails\EmailQueueController;
use App\Services\Database\DatabaseService;
use App\Services\Emails\Smtp2GoMailerService;

// Instantiate the necessary services
$databaseService = new DatabaseService();
$mailer = new Smtp2GoMailerService('bob@hereslife.com', 'Bob Prouty', STMP_API_KEY);

// Instantiate the controller and process the queue
$queueController = new EmailQueueController($databaseService, $mailer);
$queueController->processQueue();
