<?php

namespace App\Repositories;

use App\Models\People\EmailSeriesMemberModel;

use Exception;

class EmailSeriesMemberRepository extends BaseRepository
{
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