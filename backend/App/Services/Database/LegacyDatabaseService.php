<?php

namespace App\Services\Database;
use App\Services\Database\DatabaseService;

class LegacyDatabaseService extends DatabaseService {
    public function __construct() {
        parent::__construct('legacy'); // Refers to the 'legacy' config in DATABASES
    }
}
