<?php
use PHPUnit\Framework\TestCase;
use App\Controllers\Emails\EmailSeriesMemberController;
use App\Models\Emails\EmailSeriesMemberModel;
use App\Models\Emails\EmailModel;
use App\Models\Emails\EmailQueModel;

class EmailSeriesMemberControllerTest extends TestCase
{
    protected $emailSeriesMemberModel;
    protected $emailModel;
    protected $emailQueModel;
    protected $emailSeriesMemberController;

    protected function setUp(): void
    {
        // Create mocks for models
        $this->emailSeriesMemberModel = $this->createMock(EmailSeriesMemberModel::class);
        $this->emailModel = $this->createMock(EmailModel::class);
        $this->emailQueModel = $this->createMock(EmailQueModel::class);

        // Create the controller instance
        $this->emailSeriesMemberController = new EmailSeriesMemberController(
            $this->emailSeriesMemberModel,
            $this->emailModel,
            $this->emailQueModel
        );
    }

    public function testProcessNewEmailTips()
    {
        // Simulate three members with 'followup' list_name
        $newRequests = [
            [
                'id' => 1,
                'list_name' => 'followup',
                'champion_id' => 101,
                'last_tip_sent' => 1,
                'last_tip_sent_time' => time() - 3600, // 1 hour ago
            ],
            [
                'id' => 2,
                'list_name' => 'followup',
                'champion_id' => 102,
                'last_tip_sent' => 0,
                'last_tip_sent_time' => time() - 600, // 10 minutes ago
            ],
            [
                'id' => 3,
                'list_name' => 'followup',
                'champion_id' => 103,
                'last_tip_sent' => 0,
                'last_tip_sent_time' => time() - 2400, // 40 minutes ago
            ]
        ];

        // Expect findNewRequestsForTips() to return the mock data
        $this->emailSeriesMemberModel->method('findNewRequestsForTips')
            ->willReturn($newRequests);

        // Mock queTipForMember() to always return 'TRUE' for this test
        $this->emailSeriesMemberController
            ->expects($this->exactly(2)) // Two updates (records 2 and 3)
            ->method('queTipForMember')
            ->willReturn('TRUE');

        // Mock the update method to ensure it gets called with the right data
        $this->emailSeriesMemberModel
            ->expects($this->once()) // Only the member who subscribed 40 min ago should be updated
            ->method('update')
            ->with(
                3, // ID of the member who subscribed 40 minutes ago
                $this->callback(function ($data) {
                    return $data['last_tip_sent'] == 1 && abs($data['last_tip_sent_time'] - time()) < 5;
                })
            );

        // Run the processNewEmailTips method
        $this->emailSeriesMemberController->processNewEmailTips();
    }
}
