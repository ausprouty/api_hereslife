<?php
use App\Controllers\Emails\EmailQueueController;
use App\Services\Database\DatabaseService;
use App\Services\Emails\Smtp2GoMailerService;

// Instantiate the necessary services
$databaseService = new DatabaseService();
$mailer = new Smtp2GoMailerService(DEFAULT_EMAIL_ADDRESS, DEFAULT_EMAIL_SENDER);

// Instantiate the controller and process the queue
$queueController = new EmailQueueController($databaseService, $mailer);
$queueController->processQueue();
