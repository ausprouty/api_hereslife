<?php

namespace App\Services\Security;
use \HTMLPurifier;

class SanitizeInputService
{
    public function __construct()
    {
    }

    public function sanitize(array $data) : array
    {
        $sanitized = array();
        $sanitizedMailLists = array(); // Initialize the array here
        writeLogDebug("PostInputModel-17", $data);

        foreach ($data as $name => $value) {
            if (preg_match('/^mail_lists\[[^\]]+\]$/', $name)) {
                // Handle mail_lists
                $sanitizedMailLists[] = is_null($value) ? null : $this->sanitizeString($value);
            } else {
                switch ($name) {
                    case 'email':
                        // Handle email fields (allow null)
                        $sanitized[$name] = is_null($value) ? null : filter_var($value, FILTER_SANITIZE_EMAIL);
                        break;
                    case 'body':
                        // Do not sanitize the body (HTML content) but use filterHtml for XSS protection
                        $sanitized[$name] = is_null($value) ? null : $this->filterHtml($value);
                        break;
                    default:
                        // Generic fields, check if value is an array or a string
                        if (is_array($value)) {
                            // If value is an array, recursively sanitize the array
                            $sanitized[$name] = $this->sanitizeArray($value);
                        } else {
                            // Handle string values
                            $sanitized[$name] = is_null($value) ? null : $this->sanitizeString($value);
                        }
                        break;
                }
            }
        }

        // Handle mail lists after the loop
        if (!empty($sanitizedMailLists)) {
            $sanitized['selected_mail_lists'] = implode(',', $sanitizedMailLists);
        } else {
            $sanitized['selected_mail_lists'] = null;
        }

        return $sanitized;
    }

    // Method to sanitize HTML input (body content)
    public function filterHtml($text)
    {
        require_once APP_FILEDIR . '\Libraries\HtmlPurifierStandalone\HTMLPurifier.standalone.php';
        // Create an instance of the HTMLPurifier
        $purifier = new HTMLPurifier();
        // Sanitize the HTML input
        return $purifier->purify($text);
    }

    // Method to sanitize a string input
    public function sanitizeString($input)
    {
        return trim(htmlspecialchars($input, ENT_QUOTES, 'UTF-8'));
    }

    // Method to recursively sanitize an array
    public function sanitizeArray(array $array): array
    {
        $sanitizedArray = [];

        foreach ($array as $key => $value) {
            // Recursively sanitize if the value is an array
            if (is_array($value)) {
                $sanitizedArray[$key] = $this->sanitizeArray($value);
            } else {
                // Sanitize the string values
                $sanitizedArray[$key] = $this->sanitizeString($value);
            }
        }

        return $sanitizedArray;
    }
}
