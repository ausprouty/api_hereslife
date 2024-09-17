<?php
namespace App\Controllers\Data;

class BlogMigrationController
{
    private $legacyDbService;
    private $newDbService;

    public function __construct($legacyDbService, $newDbService)
    {
        $this->legacyDbService = $legacyDbService;
        $this->newDbService = $newDbService;
    }

    public function migrateNewsletters($batchSize, $offset)
    {
        // Step 1: Get newsletter node IDs in the batch
        $nodeIds = $this->getNewsletterNodeIds($batchSize, $offset);

        // Step 2: Fetch subjects, content, and other necessary fields for these newsletters
        $subjects = $this->getSubjects($nodeIds);
        $contents = $this->getContents($nodeIds);
        $datesCreated = $this->getCreationDates($nodeIds);
        $datesEdited = $this->getEditedDates($nodeIds);

        // Step 3: Loop through node IDs and insert into hl_email_series
        foreach ($nodeIds as $nodeId) {
            $subject = $subjects[$nodeId] ?? null;
            $content = $contents[$nodeId] ?? null;
            $dateCreated = $datesCreated[$nodeId] ?? null;
            $dateEdited = $datesEdited[$nodeId] ?? null;

            $this->insertNewsletter($nodeId, $subject, $content, $dateCreated, $dateEdited);
        }

        return ['records' => $nodeIds];
    }

    private function getNewsletterNodeIds($batchSize, $offset)
    {
        // Query to get node IDs for newsletters in batches
        $query = "SELECT nid FROM node WHERE type = 'newsletter_merge' LIMIT " . (int)$batchSize . " OFFSET " . (int)$offset;
        return $this->legacyDbService->fetchColumn($query);
    }

    private function getSubjects(array $nodeIds)
    {
        // Query to get the subjects from the field_revision_field_subject table
        $placeholders = implode(',', array_fill(0, count($nodeIds), '?'));
        $query = "SELECT entity_id AS nid, field_subject_value AS subject FROM field_revision_field_subject WHERE entity_id IN ($placeholders)";
        $results = $this->legacyDbService->fetchAll($query, $nodeIds);

        // Transform the results into an associative array
        $subjects = [];
        foreach ($results as $row) {
            $subjects[$row['nid']] = $row['subject'];
        }

        return $subjects;
    }

    private function getContents(array $nodeIds)
    {
        // Query to get the body content from the field_data_body table
        $placeholders = implode(',', array_fill(0, count($nodeIds), '?'));
        $query = "SELECT entity_id AS nid, body_value AS content FROM field_data_body WHERE entity_id IN ($placeholders)";
        $results = $this->legacyDbService->fetchAll($query, $nodeIds);

        // Transform the results into an associative array
        $contents = [];
        foreach ($results as $row) {
            $contents[$row['nid']] = $row['content'];
        }

        return $contents;
    }

    private function getCreationDates(array $nodeIds)
    {
        // Query to get the creation dates from the node table
        $placeholders = implode(',', array_fill(0, count($nodeIds), '?'));
        $query = "SELECT nid, FROM_UNIXTIME(created) AS date_created FROM node WHERE nid IN ($placeholders)";
        $results = $this->legacyDbService->fetchAll($query, $nodeIds);

        // Transform the results into an associative array
        $datesCreated = [];
        foreach ($results as $row) {
            $datesCreated[$row['nid']] = $row['date_created'];
        }

        return $datesCreated;
    }

    private function getEditedDates(array $nodeIds)
    {
        // Query to get the last edited dates from the node table
        $placeholders = implode(',', array_fill(0, count($nodeIds), '?'));
        $query = "SELECT nid, FROM_UNIXTIME(changed) AS date_edited FROM node WHERE nid IN ($placeholders)";
        $results = $this->legacyDbService->fetchAll($query, $nodeIds);

        // Transform the results into an associative array
        $datesEdited = [];
        foreach ($results as $row) {
            $datesEdited[$row['nid']] = $row['date_edited'];
        }

        return $datesEdited;
    }

    private function insertNewsletter($nodeId, $subject, $content, $dateCreated, $dateEdited)
    {
        // Insert the newsletter data into the hl_email_series table
        $query = "INSERT INTO hl_email_series 
            (id, subject, body, plain_text_only, headers, template, series, sequence, params, date_created, date_edited, date_sent) 
            VALUES (:id, :subject, :body, 0, NULL, NULL, 'Blog', :sequence, NULL, :date_created, :date_edited, NULL)";

        $params = [
            'id' => $nodeId,
            'subject' => $subject,
            'body' => $content,
            'sequence' => $nodeId, // Using nid as the sequence number
            'date_created' => $dateCreated,
            'date_edited' => $dateEdited
        ];

        $this->newDbService->executeQuery($query, $params);
    }
}
