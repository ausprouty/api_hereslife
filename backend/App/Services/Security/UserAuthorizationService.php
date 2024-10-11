<?php

namespace App\Services\Security;

use App\Repositories\ChampionRepository;
use App\Services\Database\DatabaseService;

/**
 * Class UserAuthorizationService
 *
 * This service handles user authorization by generating and validating hashes based on the user's email
 * and their unique ID, using a secret key. It interacts with the ChampionRepository to fetch user information.
 */
class UserAuthorizationService
{
    /**
     * @var ChampionRepository Instance of the ChampionRepository for data access.
     */
    protected $championRepository;

    /**
     * UserAuthorizationService constructor.
     *
     * @param ChampionRepository $championRepository The repository used for accessing user data.
     */
    public function __construct(ChampionRepository $championRepository)
    {
        $this->championRepository = $championRepository;
    }

    /**
     * Generate a hash for the user based on their ID and email.
     *
     * @param int $user_id The user's unique ID.
     * @return string The generated hash.
     */
    public function generateUserHash($user_id)
    {
        // Fetch the user's email using the repository
        $email = $this->championRepository->findEmailById($user_id);
        // Generate and return a hash using the user ID and email
        return hash_hmac('sha256', $user_id . $email, USER_SECRET_KEY);
    }

    /**
     * Validate a provided hash for a user.
     *
     * @param int $user_id The user's unique ID.
     * @param string $hash The hash to validate.
     * @return bool True if the hash is correct, false otherwise.
     */
    public function checkUserHash($user_id, $hash)
    {
        // Fetch the user's email using the repository
        $email = $this->championRepository->findEmailById($user_id);
        $id = (int) $user_id;
        // Generate the correct hash
        $correct_hash = hash_hmac('sha256', $id . $email, USER_SECRET_KEY);

        // Compare the provided hash with the correct hash
        return $hash === $correct_hash;
    }
    // used with post from wordpress
    function generateWordpressNonce($action, $expire = 3600) {
        // Add time-based salt (current time divided by expiration time)
        $time = ceil(time() / $expire);
        // Generate the hash using the action, time, and a secret salt
        $nonce = base64_encode(hash('sha256', WORDPRESS_HL_NONCE_SALT . $action . $time, true));
    
        return $nonce;
    }
    // used with post from wordpress
    function verifyWordpressNonce($nonce, $action, $expire = 3600) {
        // Calculate the expected nonce for the current and previous time windows
        $time = ceil(time() / $expire);
        $expectedNonce = base64_encode(hash('sha256', WORDPRESS_HL_NONCE_SALT . $action . $time, true));
    
        // Check if the provided nonce matches the expected nonce
        if ($nonce === $expectedNonce) {
            return true; // Nonce is valid
        }
    
        // Optionally, check for the previous time window to allow some flexibility in timing
        $previousTime = $time - 1;
        $previousNonce = base64_encode(hash('sha256', WORDPRESS_HL_NONCE_SALT . $action . $previousTime, true));
    
        if ($nonce === $previousNonce) {
            return true; // Nonce is still valid within the expiration time window
        }
    
        return false; // Nonce is invalid
    }
    

}
