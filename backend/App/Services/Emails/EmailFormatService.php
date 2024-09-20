<?php
namespace App\Services\Emails;

use App\Models\Emails\EmailModel;
use App\Services\Database\DatabaseService;

class EmailFormatService
{
    protected $databaseService;
    protected $emailModel;

    private $body;
    private $champion_id;
    private $delay_until;
    private $email;
    private $email_from;
    private $email_id;
    private $email_to;
    private $first_email_date;
    private $first_name;
    private $gender;
    private $headers;
    private $id;
    private $last_email_date;
    private $params;
    private $plain_text_body;
    private $plain_text_only;
    private $subject;
    private $template;


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
        $defaults = [
            'body' => null,
            'champion_id' => null,
            'delay_until' => null,
            'champion_email' => '',
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
            'plain_text_body' => null,
            'plain_text_only' => 0,
            'subject' => '',
            'template' => EMAIL_DEFAULT_TEMPLATE,
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
        writeLog('EmailFormatService::setValues', $this);
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
        if ($this->champion_id) {
            $this->email = $this->champion_email;
        }
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
