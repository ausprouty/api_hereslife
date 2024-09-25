<?php

use App\Services\Database\DatabaseService;
use App\Controllers\Emails\EmailTrackingController;

$databaseservice = new DatabaseService();
$emailTrackingController = new EmailTrackingController($databaseservice);
$emailTrackingController->recordEmailOpened( $championId, $emailId );

