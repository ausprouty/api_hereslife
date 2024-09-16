<?php

namespace App\Controllers\Data;

class EmailNodeMigrationController
{
    private $legacyDbService;
    private $newDbService;

    public function __construct($legacyDbService, $newDbService)
    {
        $this->legacyDbService = $legacyDbService;
        $this->newDbService = $newDbService;
    }

    public function migrateEmailNodes($batchSize, $offset)
    {
        // Step 1: Get email IDs in the batch
        $emailIds = $this->getEmailIds($batchSize, $offset);

        // Step 2: Fetch subjects and content for these emails
        $subjects = $this->getSubjects($emailIds);
        $contents = $this->getContents($emailIds);

        // Step 3: Loop through email IDs and update hl_email_nodes
        foreach ($emailIds as $emailId) {
            $emailSubject = $subjects[$emailId] ?? null;
            $emailContent = $contents[$emailId] ?? null;
            $this->updateEmailNode($emailId, $emailSubject, $emailContent);
        }

        return ['records' => $emailIds];
    }

    private function getEmailIds($batchSize, $offset)
    {
        // Query to get email IDs in batches
        $query = "SELECT email_id FROM hl_email_nodes LIMIT " . (int)$batchSize . " OFFSET " . (int)$offset;
        return $this->newDbService->fetchColumn($query);
    }

    private function getSubjects(array $emailIds)
    {
        // Query to get email subjects from field_revision_field_subject table
        $placeholders = implode(',', array_fill(0, count($emailIds), '?'));
        $query = "SELECT entity_id AS email_id, field_subject_value AS email_subject FROM field_revision_field_subject WHERE entity_id IN ($placeholders)";
        writeLog('getSubjects-47', $query);
        writeLog('getSubjects-48', $emailIds);
        $results = $this->legacyDbService->fetchAll($query, $emailIds);

        // Transform the results into an associative array
        $subjects = [];
        foreach ($results as $row) {
            $subjects[$row['email_id']] = $row['email_subject'];
        }

        return $subjects;
    }

    private function getContents(array $emailIds)
    {
        // Query to get email content from field_data_body table
        $placeholders = implode(',', array_fill(0, count($emailIds), '?'));
        $query = "SELECT entity_id AS email_id, body_value AS email_content FROM field_data_body WHERE entity_id IN ($placeholders)";
        writeLog('getSubjects-65', $query);
        writeLog('getSubjects-66', $emailIds);
        $results = $this->legacyDbService->fetchAll($query, $emailIds);

        // Transform the results into an associative array
        $contents = [];
        foreach ($results as $row) {
            $contents[$row['email_id']] = $row['email_content'];
        }

        return $contents;
    }

    private function updateEmailNode($emailId, $emailSubject, $emailContent)
    {
        // Update query to update the hl_email_nodes table
        $query = "UPDATE hl_email_nodes SET email_subject = :email_subject, email_content = :email_content WHERE email_id = :email_id";
        $params = [
            'email_subject' => $emailSubject,
            'email_content' => $emailContent,
            'email_id' => $emailId
        ];
        $this->newDbService->executeQuery($query, $params);
    }
}
