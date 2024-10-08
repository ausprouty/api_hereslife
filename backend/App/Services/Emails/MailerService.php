<?php

namespace App\Services\Emails;

use SMTP2GO\ApiClient;
use SMTP2GO\Service\Mail\Send as MailSend;
use SMTP2GO\Types\Mail\Address;
use SMTP2GO\Collections\Mail\AddressCollection;

class MailerService
{
    private $senderEmail;
    private $senderName;


    public function __construct($senderEmail, $senderName)
    {
        $this->senderEmail = $senderEmail;
        $this->senderName = $senderName;

    }

    public function sendEmail($recipientEmail, $recipientName, $subject, $body, $bcc = null)
    {
        // Implement your logic for sending an email through Smtp2Go
        $postData = [

            'recipient' => ['email' => $recipientEmail, 'name' => $recipientName],
            'subject' => $subject,
            'body' => $body,
            'bcc' => $bcc,
        ];
        // This part is abstracted, assuming you have a function to handle API requests
        return $this->sendToApi($postData);
    }

    private function sendToApi($postData)
    {
        $sendService = new MailSend(
            new Address($this->senderEmail, $this->senderName),
            new AddressCollection([
                new Address($postData['recipient']['email'], $postData['recipient']['name']),
            ]),
            $postData['subject'],
            $postData['body'],
        );
        if ($postData['bcc']){
            $sendService->addAddress('bcc', new Address($postData['bcc']));
        }
        $apiClient = new ApiClient(STMP_API_KEY);
        $success = $apiClient->consume($sendService);
        $responseBody = $apiClient->getResponseBody();
        writeLogDebug("MailerService-51", $responseBody);
        return $responseBody;
    }
}
