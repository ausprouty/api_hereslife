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
    public function unsubscribe()
    {
        if (isset($_GET['user_id']) && isset($_GET['token'])) {
            $user_id = $_GET['user_id'];
            $token = $_GET['token'];

            $result = $this->emailSubscriptionService->unsubscribeUser($user_id, $token);

            if ($result['success']) {
                echo "You have been successfully unsubscribed.";
            } else {
                echo $result['message'];
            }
        } else {
            echo "Invalid request.";
        }
    }

    // Change email action
    public function changeEmail()
    {
        if (isset($_GET['user_id']) && isset($_GET['token'])) {
            $user_id = $_GET['user_id'];
            $token = $_GET['token'];

            $result = $this->emailSubscriptionService->handleChangeEmail($user_id, $token);

            if ($result['success']) {
                echo "<form method='POST'>
                        New email: <input type='email' name='new_email' required>
                        <button type='submit'>Change Email</button>
                      </form>";

                if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                    $new_email = $_POST['new_email'];
                    $changeResult = $this->emailSubscriptionService->updateEmail($user_id, $new_email);
                    echo $changeResult['message'];
                }
            } else {
                echo $result['message'];
            }
        } else {
            echo "Invalid request.";
        }
    }
}
