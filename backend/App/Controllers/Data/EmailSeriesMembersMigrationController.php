<?php

namespace App\Controllers\Data;

use App\Services\Database\LegacyDatabaseService;
use App\Services\Database\NewDatabaseService;
use App\Services\TimeService;

class EmailSeriesMembersMigrationController {
    protected $legacyDbService;
    protected $newDbService;
    protected $emailSeriesCounts = []; // Array to track counts for each list_name

    public function __construct(LegacyDatabaseService $legacyDbService, NewDatabaseService $newDbService) {
        $this->legacyDbService = $legacyDbService;
        $this->newDbService = $newDbService;
    }

    public function migrateEmailSeriesMembers($batchSize, $offset) {
        // Mapping tid to list_name and total emails in each series
        $listIdMapping = [
            3 => ['name' => 'DBSstart', 'total_emails' => 1],
            6 => ['name' => 'Tracts', 'total_emails' => 10],
            19 => ['name' => 'Followup', 'total_emails' => 4],
            24 => ['name' => 'DBSstart', 'total_emails' => 6],
            25 => ['name' => 'DBSmultiply', 'total_emails' => 6],
            41 => ['name' => 'MyFriends', 'total_emails' => 4],
            46 => ['name' => 'DBSlifeprinciples', 'total_emails' => 2],
            47 => ['name' => 'DBSleadership', 'total_emails' => 3],
        ];

        // Query the legacy database for a batch of records
        $query = "SELECT * FROM hl_email_series_members LIMIT " . (int)$batchSize . " OFFSET " . (int)$offset;
        $legacyData = $this->legacyDbService->executeQuery($query)->fetchAll(\PDO::FETCH_ASSOC);

        foreach ($legacyData as &$member) {
            // Skip records where tid is not mapped
            if (!isset($listIdMapping[$member['tid']])) {
                continue; // Skip this record if no mapping exists for tid
            }

            // Map tid to list_name and get total emails for the series
            $listData = $listIdMapping[$member['tid']];

            // Set last_tip_sent and last_tip_sent_date
            $last_tip_sent = $member['sequence'];
            $last_tip_sent_date = date('Y-m-d', $member['sent']); // The last email sent date

            // Calculate subscribed_date by working backwards from the last sent date
            // Assuming emails were sent every 8 days, so we calculate how many days passed from the first email
            $subscribed_date = date('Y-m-d', strtotime($last_tip_sent_date . " - " . (($last_tip_sent - 1) * 8) . " days"));

            // If they have received all tips, mark them as finished
            $finished_all_tips = $last_tip_sent >= $listData['total_emails'] ? 1 : 0;

            try {
                // Insert the converted data into the new database
                $this->newDbService->executeUpdate(
                    "INSERT INTO hl_email_series_members (list_name, champion_id, subscribed_date, last_tip_sent, last_tip_sent_date, finished_all_tips, unsubscribed_date) 
                    VALUES (?, ?, ?, ?, ?, ?, ?)",
                    [
                        $listData['name'],
                        $member['cid'],  // Champion ID from legacy data
                        $subscribed_date,
                        $last_tip_sent,
                        $last_tip_sent_date,
                        $finished_all_tips,
                        NULL  // No unsubscribed date in legacy data
                    ]
                );
            } catch (\Exception $e) {
                // Log the error
                echo "Error inserting record: " . $e->getMessage() . "\n";
            }

            // Count the number of members for each list_name (series)
            if (!isset($this->emailSeriesCounts[$listData['name']])) {
                $this->emailSeriesCounts[$listData['name']] = 0;
            }
            $this->emailSeriesCounts[$listData['name']]++;
        }

        // Return the fetched records and the series counts to check batch size in the loop
        return ['success' => true, 'records' => $legacyData, 'offset' => $offset];
    }

    // Return the counts of how many members are in each series (list_name)
    public function getEmailSeriesCounts() {
        return $this->emailSeriesCounts;
    }
}
