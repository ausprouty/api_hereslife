<?php
namespace App\Services\Emails;

use App\Models\Emails\EmailSeriesMemberModel;

class EmailTipsService
{
    private $emailSeriesMemberModel;

    // Inject the EmailSeriesMemberModel via constructor
    public function __construct(EmailSeriesMemberModel $emailSeriesMemberModel)
    {
        $this->emailSeriesMemberModel = $emailSeriesMemberModel;
    }

    // Process new requests and send tips
    public function processNewRequestsForTips()
    {
        // Fetch new requests for tips from the model
        $newRequests = $this->emailSeriesMemberModel->findNewRequestsForTips();

        // Loop through each new request and send the tips
        foreach ($newRequests as $request) {
            $this->queTipForMember($request);
            // Update the record to mark the tip as sent
            $data = [
                'last_tip_sent' => 1, // Current time
                'last_tip_sent_time' => Now(),
            ];
            $this->emailSeriesMemberModel->update($request['id'], $data);
        }
    }

    
}
