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

    public function getChampionInfo($championId) {
        // Fetch the complete Champion record
        $champion = $this->championRepository->findById($championId);

        if (!$champion) {
            // Handle the case where the Champion is not found
            return null;
        }

        // Extract only the required fields
        $trimmedChampion = [
            'cid' => $champion->cid,
            'first_name' => $champion->first_name,
            'state' => $champion->state,
            'country' => $champion->country,
            'email' => $champion->email,
            'gender' => $champion->gender,
            'mailing_lists' => $this->getEmailLists($championId)
        ];

        // Return the trimmed record
        return $trimmedChampion;
    }

    private function getEmailLists($cid){
        return $emailSeriesMemberRepository->getListsForMember($cid)
    }
}
    

    

    