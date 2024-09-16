<?php

namespace App\Services\Database;

class NewDatabaseService extends DatabaseService {
    public function __construct() {
        parent::__construct('standard'); // Refers to the 'new' config in DATABASES
    }
}
