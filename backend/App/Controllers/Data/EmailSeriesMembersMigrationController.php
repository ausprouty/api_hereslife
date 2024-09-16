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
        // Mapping list_id to list_name and total emails in each series
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
        $query = "SELECT * FROM hl_email_list_members LIMIT " . (int)$batchSize . " OFFSET " . (int)$offset;
        $legacyData = $this->legacyDbService->executeQuery($query)->fetchAll(\PDO::FETCH_ASSOC);

        foreach ($legacyData as &$member) {
            writeLogAppend('EmailSeriesMembersMigrationController-64', $member['list_id'] . ' -- ' . $listIdMapping[$member['list_id']]);
            // Skip records where list_id is not mapped
            if (!isset($listIdMapping[$member['list_id']])) {
                continue; // Skip this record if no mapping exists for list_id
            }

            // Convert timestamps and map fields
            $this->convertTimestamps($member);

            // Map list_id to list_name and get total emails for the series
            $listData = $listIdMapping[$member['list_id']];

            // Calculate how many emails have been sent based on days since subscribed
            $last_tip_sent = $this->calculateLastTipSent($member['subscribed_date'], $listData['total_emails']);

            // If they have received all tips, mark them as finished
            $finished_all_tips = $last_tip_sent >= $listData['total_emails'] ? 1 : 0;

            // Insert the converted data into the new database
            $this->newDbService->executeUpdate(
                "INSERT INTO hl_email_list_members (list_name, champion_id, subscribed_date, last_tip_sent, last_tip_sent_date, finished_all_tips, unsubscribed) 
                VALUES (?, ?, ?, ?, ?, ?, ?)",
                [
                    $listData['name'],
                    $member['champion_id'],
                    $member['subscribed_date'],
                    $last_tip_sent,
                    date('Y-m-d', strtotime($member['subscribed_date'] . " + " . ($last_tip_sent * 8) . " days")),
                    $finished_all_tips,
                    $member['unsubscribed']
                ]
            );

            // Count the number of members for each list_name (series)
            if (!isset($this->emailSeriesCounts[$listData['name']])) {
                $this->emailSeriesCounts[$listData['name']] = 0;
            }
            $this->emailSeriesCounts[$listData['name']]++;
        }

        // Return the fetched records and the series counts to check batch size in the loop
        return ['success' => true, 'records' => $legacyData, 'offset' => $offset];
    }

    protected function convertTimestamps(&$member) {
        // Convert timestamps to dates
        $member['subscribed_date'] = $member['subscribed'] ? TimeService::timestampToDate($member['subscribed']) : NULL;
        $member['unsubscribed'] = $member['unsubscribed'] ? TimeService::timestampToDate($member['unsubscribed']) : NULL;
    }

    protected function calculateLastTipSent($subscribedDate, $totalEmails) {
        // Get the current date
        $currentDate = date('Y-m-d');

        // Calculate the number of days since the subscribed date
        $daysSinceSubscribed = (strtotime($currentDate) - strtotime($subscribedDate)) / (60 * 60 * 24);

        // Calculate how many emails have been sent (every 8 days)
        $emailsSent = floor($daysSinceSubscribed / 8);

        // Cap the number of emails sent at the total number in the series
        return min($emailsSent, $totalEmails);
    }

    // Return the counts of how many members are in each series (list_name)
    public function getEmailSeriesCounts() {
        return $this->emailSeriesCounts;
    }
}
