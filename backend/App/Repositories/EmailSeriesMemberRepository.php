<?php

namespace App\Repositories;

use App\Models\People\EmailSeriesMemberModel;
use Exception;

class EmailSeriesMemberRepository extends BaseRepository
{
    // Implementing abstract method to define the table name
    protected function getTableName()
    {
        return 'hl_email_series_members';
    }

    // Implementing abstract method to define valid columns for this repository
    protected function getValidColumns()
    {
        return [
            'id',
            'list_name',
            'champion_id',
            'subscribed_date',
            'last_tip_sent',
            'last_tip_sent_date',
            'finished_all_tips',
            'unsubscribed_date',
        ];
    }

    public function getListsForMember($cid)
    {
        $query = "SELECT * FROM hl_email_series_members WHERE champion_id = :cid";
        $params = [':cid' => $cid];

        try {
            $results = $this->databaseService->executeQuery($query, $params);
            $data = $results->fetchAll(\PDO::FETCH_ASSOC);

            if ($data) {
                return $data;
            }
            return null;
        } catch (Exception $e) {
            writeLogError('EmailSeriesMemberRepository-getListsForMember', $e->getMessage());
            return null;
        }
    }
}
