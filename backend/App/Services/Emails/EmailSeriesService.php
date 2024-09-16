<?php

namespace App\Services\Emails;

use App\Models\Emails\EmailSeriesModel;

class EmailSeriesService
{
    private $emailSeriesModel;

    /**
     * Constructor to inject the EmailSeriesModel dependency.
     *
     * @param EmailSeriesModel $emailSeriesModel
     */
    public function __construct(EmailSeriesModel $emailSeriesModel)
    {
        $this->emailSeriesModel = $emailSeriesModel;
    }

    /**
     * Get the email series data by the given series name.
     * 
     * @param string $series The series name.
     * @return array The formatted email series data or an error message.
     */
    public function getEmailsBySeries($series)
    {
        // Step 1: Fetch emails using the model
        $emails = $this->emailSeriesModel->getEmailsBySeries($series);
        
        // Step 2: Check if emails were found
        if (!$emails || empty($emails)) {
            // No emails found, return failure message
            return [
                'success' => false,
                'message' => 'No emails found for this series.'
            ];
        }

        // Step 3: Return success response with email data
        return [
            'success' => true,
            'data'    => $emails
        ];
    }
}
