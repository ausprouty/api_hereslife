<?php

namespace App\Controllers\Emails;

use App\Services\Emails\EmailSeriesService;

class EmailSeriesController
{
    /**
     * @var EmailSeriesService
     */
    private $emailSeriesService;

    /**
     * EmailSeriesController constructor.
     * @param EmailSeriesService $emailSeriesService
     */
    public function __construct(EmailSeriesService $emailSeriesService)
    {
        $this->emailSeriesService = $emailSeriesService;
    }

    /**
     * Fetch emails by series and return them in sequence order.
     * 
     * @param string $series The email series passed by Middleware.
     * @return array The response array with success or failure message
     */
    public function getEmailsBySeries($series)
    {
        return $this->emailSeriesService->getEmailsBySeries($series);
    }
}
