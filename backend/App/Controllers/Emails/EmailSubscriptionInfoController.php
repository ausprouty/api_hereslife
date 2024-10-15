<?php
namespace App\Controllers\Emails;

use App\Repositories\EmailSeriesMemberRepository;
use App\Repositories\ChampionRepository;


class EmailSubscriptionInfoController {
    private $championRepository;
    private $emailSeriesMemberRepository;

    public function __construct(ChampionRepository $championRepository, EmailSeriesMemberRepository $emailSeriesMemberRepository) {
        $this->championRepository = $championRepository;
        $this->emailSeriesMemberRepository = $emailSeriesMemberRepository;
    }

    public function getUserMailingListInfo($championId) {
        // Fetch the complete Champion record
        $champion = $this->championRepository->findByCid($championId);

        if (!$champion) {
            // Handle the case where the Champion is not found
            return null;
        }

        // Extract only the required fields
        $trimmedChampion = [
            'cid' => $championId,
            'first_name' => $champion->getFirstName(),
            'state' => $champion->getState(),
            'country' => $champion->getCountry(),
            'email' => $champion->getEmail(),
            'gender' => $champion->getGender(),
            'mailing_lists' => $this->getEmailLists($championId)
        ];

        // Return the trimmed record
        return $trimmedChampion;
    }

    private function getEmailLists($cid){
        return $this->emailSeriesMemberRepository->getListsForMember($cid);
    }
}
    

    

    