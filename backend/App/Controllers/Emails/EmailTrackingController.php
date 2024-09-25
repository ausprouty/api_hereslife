<?php

Namespace App\Controllers\Emails;

use App\Services\Database\DatabaseService;

class EmailTrackingController {
    protected $databaseService;

    public function __construct(DatabaseService $databaseService) {
        $this->databaseService = $databaseService;
    }

    public function recordEmailOpened($emailId, $championId) {
        // Update the email tracking table to mark the email as opened
        $this->databaseService->update('hl_email_tracking', [
            'date_opened' => NOW()
        ], [
            'email_id' => $emailId,
            'champion_id' => $championId
        ]);

        // Serve the image
        $this->serveImage();
    }

    protected function serveImage() {
        $imagePath = TRACKING_IMAGE;

        if (file_exists($imagePath)) {
            $imageType = pathinfo($imagePath, PATHINFO_EXTENSION);

            // Send the correct headers
            header("Content-Type: image/" . $imageType);
            header("Content-Length: " . filesize($imagePath));

            // Read the file and output its contents
            readfile($imagePath);
            exit;
        } else {
            // Handle the case where the image doesn't exist
            header("HTTP/1.0 404 Not Found");
            echo 'Image not found';
            exit;
        }
    }
}
