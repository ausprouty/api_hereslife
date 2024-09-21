<?php
namespace App\Controllers\Emails;

use App\Services\Emails\EmailSubscriptionService;

class EmailSubscriptionController
{
    protected $emailSubscriptionService;

    public function __construct(EmailSubscriptionService $emailSubscriptionService)
    {
        $this->emailSubscriptionService = $emailSubscriptionService;
    }

    // Unsubscribe action
    public function unsubscribe($cid)
    {

        $result = $this->emailSubscriptionService->unsubscribeUserFromAll($cid);

        if ($result['success']) {
            echo "You have been successfully unsubscribed.";
        } else {
            echo $result['message'];
        }
       
    }

    