<?php

namespace App\Models\Emails;

use App\Models\BaseModel;

class EmailSeriesModel extends BaseModel
{
    

    /**
     * Fetch emails by series name from the database.
     *
     * @param string $series The name of the email series to filter by.
     * @return array The fetched email records or an empty array if none found.
     */
    public function getEmailsBySeries($series)
    {
        // Step 1: Define the query to fetch the email series
        $query = "
            SELECT id, subject, series, sequence
            FROM hl_email_series
            WHERE series = :series
            ORDER BY sequence ASC
        ";

        // Step 2: Execute the query and fetch the results
        return $this->databaseService->fetchAll($query, ['series' => $series]);
    }
}
