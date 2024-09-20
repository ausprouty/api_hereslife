<?php

namespace App\Services\Emails;

class EmailSubscriptionService
{
    protected $secret_key;

    public function __construct()
    {
        $this->secret_key = 'your_secret_key';
    }

    public function unsubscribeUser($user_id, $token)
    {
        // Lookup user by userID
        $user = $this->findUserById($user_id);

        if ($user) {
            $expected_token = hash_hmac('sha256', $user_id . $user['email'], $this->secret_key);

            if ($token === $expected_token) {
                $this->performUnsubscribe($user_id);
                return ['success' => true, 'message' => 'Successfully unsubscribed.'];
            } else {
                return ['success' => false, 'message' => 'Invalid token.'];
            }
        } else {
            return ['success' => false, 'message' => 'User not found.'];
        }
    }

    public function handleChangeEmail($user_id, $token)
    {
        $user = $this->findUserById($user_id);

        if ($user) {
            $expected_token = hash_hmac('sha256', $user_id . $user['email'], $this->secret_key);

            if ($token === $expected_token) {
                return ['success' => true];
            } else {
                return ['success' => false, 'message' => 'Invalid token.'];
            }
        } else {
            return ['success' => false, 'message' => 'User not found.'];
        }
    }

    public function updateEmail($user_id, $new_email)
    {
        if (filter_var($new_email, FILTER_VALIDATE_EMAIL)) {
            $this->performEmailUpdate($user_id, $new_email);
            return ['success' => true, 'message' => 'Email successfully updated.'];
        } else {
            return ['success' => false, 'message' => 'Invalid email address.'];
        }
    }

    private function findUserById($user_id)
    {
        // Database lookup logic
    }

    private function performUnsubscribe($user_id)
    {
        // Unsubscribe logic
    }

    private function performEmailUpdate($user_id, $new_email)
    {
        // Update email logic
    }
}
