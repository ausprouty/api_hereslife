public function generateUserHash($user_id)
    {
        $email = $this->findEmailById($user_id);
        return hash_hmac('sha256', $user_id . $email, USER_SECRET_KEY);
    }
    public function checkUserHash($user_id, $hash)
    {
        $email = $this->findEmailById($user_id);
        $correct_hash =  hash_hmac('sha256', $user_id . $email, USER_SECRET_KEY);
        if ($hash === $correct_hash) {
            return true;
        } else {
            return false;
        }
    }