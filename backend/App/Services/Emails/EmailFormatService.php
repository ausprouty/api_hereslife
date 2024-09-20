<?php
namespace App\Services\Emails;

use App\Models\Emails\EmailModel;
use App\Services\Database\DatabaseService;

class EmailFormatService
{
    protected $databaseService;
    protected $emailModel;

    private $body;
    private $champion_email; // This is the email address of the champion and will REPLACE the email_to field
    private $champion_id;   
    private $delay_until;
    private $email_from;
    private $email_id; // This is the ID of the email to be sent and will REPLACE the body, subject, and template fields 
    private $email_to;
    private $first_email_date;
    private $first_name;
    private $gender;
    private $headers;
    private $id; // This is the ID of the email que record  
    private $last_email_date;
    private $params;
    private $plain_text_body;
    private $plain_text_only;
    private $postscript;
    private $subject;
    private $template;
    private $unsubscribe;


    /**
     * Constructor to inject the database and email model.
     *
     * @param DatabaseService $database
     * @param EmailModel $emailModel
     */
    public function __construct(DatabaseService $databaseService, EmailModel $emailModel)
    {
        $this->databaseService = $databaseService;
        $this->emailModel = $emailModel;
    }

    // Set values for the object, applying default values if needed
    public function setValues(array $data)
    {
        writeLog('EmailFormatService::setValues-47', $data);
        $defaults = [
            'body' => null,
            'champion_email' => '',
            'champion_id' => null,
            'delay_until' => null,
            'email_from' => DEFAULT_EMAIL_SENDER,
            'email_id' => null,
            'email_to' => '',
            'first_email_date' => null,
            'first_name' => '',
            'gender' => null,
            'headers' => '',
            'id' => null,
            'last_email_date' => null,
            'params' => '',
            'postscript' => '',
            'plain_text_body' => null,
            'plain_text_only' => 0,
            'subject' => '',
            'template' => EMAIL_DEFAULT_TEMPLATE,
            'unsubscribe' => '',
        ];
        // Merge provided data with defaults
        $data = array_merge($defaults, $data);

        // Set object properties using the merged array
        foreach ($data as $field => $value) {
            if (property_exists($this, $field)) {
                $this->$field = $value;
            }
        }
        $this->setFromStandardEmail();
        $this->setRecipient();
        $this->setUnsubscribeLink();
        $this->formatBody();
        $output = array(
            'body' => $this->body,
            'email_from' => $this->email_from,
            'email_to' => $this->email_to,
            'subject' => $this->subject,
            'headers' => $this->headers,
            'plain_text_body' => $this->plain_text_body,
            'plain_text_only' => $this->plain_text_only,
        );
        writeLog('EmailFormatService::setValues-89', $output);
        return $output;
    }
    // Set the email body from a standard email template if an email ID is provided
    private function setFromStandardEmail()
    {
        if ($this->email_id) {
            $formEmail = $this->emailModel->findById($this->email_id);
            $this->body = $formEmail['body'];
            $this->subject = $formEmail['subject'];
            $this->template = $formEmail['template'];
        }
    }
    // Set the recipient email address based on the champion ID or provided email
    private function setRecipient(){
        if ($this->champion_email) {
            $this->email_to = $this->champion_email;
        }
    }
    private function formatBody(){
        // Load the default template.
        $template = $this->getTemplate();

        // Replace placeholders in the template with actual content.
        $template = str_replace('{{subject}}', $this->subject, $template);
        $template = str_replace('{{body}}', $this->body, $template);
        $template = str_replace('{{header}}', EMAIL_HEADERIMAGE, $template);
        $template = str_replace('{{signature}}', EMAIL_SIGNATURE, $template);
        $template = str_replace('{{author-bio}}', EMAIL_AUTHORBIO, $template);
        $template = str_replace('{{postscript}}', $this->postscript, $template);
        $template = str_replace('{{hash}}', $this->generateUnsubscribeHash(), $template);
        $template = str_replace('{{cid}}', $this->champion_id, $template);

        // Assign the formatted email body back to the data array.
        $this->body = $template;
    }
    private function getTemplate(){
        if (!$this->template) {
            $this->template = EMAIL_DEFAULT_TEMPLATE;
        }
        if (file_exists(EMAIL_TEMPLATE_DIRECTORY . '/' . $this->template . '.template')) {
            return file_get_contents(EMAIL_TEMPLATE_DIRECTORY . '/' . $this->template . '.template');
        }
        else {
            return file_get_contents(EMAIL_TEMPLATE_DIRECTORY . '/' . EMAIL_DEFAULT_TEMPLATE . '.template');
        }
        
    }
    private function setUnsubscribeLink(){
        $this->unsubscribe = $this->unsubscribe . '?email=' . $this->email_to;
    }

    private function generateUnsubscribeHash(){
        return hash('sha256', $this->champion_id . $this->email_to . UNSUBSCRIBE_HASH);
    }


    /**
     * Format an email for viewing in a specific template.
     *
     * This method selects the appropriate template and content for the email,
     * then merges any fields that require personalization. Specialized fields
     * are identified by [FieldName] and replaced accordingly.
     *
     * @param int $id The ID of the email to format.
     * @return array The email data with the formatted template.
     */
    public function formatForView(int $id): array
    {
        // Retrieve email data from the database using the email model.
        $data = $this->emailModel->findById($id);

        // Load the default template.
        $template = file_get_contents(EMAIL_DEFAULT_TEMPLATE);

        // Replace placeholders in the template with actual content.
        $template = str_replace('{{subject}}', $data['subject'], $template);
        $template = str_replace('{{body}}', $this->replaceSpecialFields($data['body']), $template);
        $template = str_replace('{{header}}', EMAIL_HEADERIMAGE, $template);
        $template = str_replace('{{signature}}', EMAIL_SIGNATURE, $template);
        $template = str_replace('{{author-bio}}', EMAIL_AUTHORBIO, $template);
        $template = str_replace('{{postscript}}', $this->replaceSpecialFields($data['postscript']), $template);
        $template = str_replace('{{unsubscribe}}', $this->unsubscribe, $template);
        // Assign the formatted email body back to the data array.
        $data['body'] = $template;

        return $data;
    }

    /**
     * Replace any specialized fields within the email content.
     *
     * Fields are marked by [FieldName] and will be dynamically replaced with appropriate values.
     *
     * @param string $content The email content with [FieldName] placeholders.
     * @return string The content with placeholders replaced.
     */
    protected function replaceSpecialFields(string $content): string
    {
        // Define specialized fields and their replacement values.
        $specialFields = [
            '[FirstName]' => $content['first'],  // Example field value, this could be dynamic.
            '[LastName]'  => 'Doe',
            // Add more field replacements as needed.
        ];

        // Replace all [FieldName] placeholders with their respective values.
        foreach ($specialFields as $field => $value) {
            $content = str_replace($field, $value, $content);
        }

        return $content;
    }
}
