<?php namespace App\Core;


use App\Config;

/**
 * Class View
 * @package App\Core
 */
class View
{
    /**
     * View template name
     * @var string
     */
    private $template = '';

    /**
     * Validation errors list
     *
     * @var array
     */
    private $errors = [];

    /**
     * View constructor.
     * @param string $template
     */
    public function __construct(string $template)
    {
        $this->template = $template;
    }

    /**
     * Convert special characters to HTML entities
     *
     * @param string $string
     * @return string
     */
    public function _($string): ?string
    {
        return htmlspecialchars((string)$string, ENT_QUOTES, 'UTF-8');
    }

    /**
     * Render view template.
     *
     * @param array $data
     * @return string
     */
    public function render(array $data): string
    {
        //Set variables from data set
        extract($data);
        $this->errors = $errors ?? null;

        //begin content writing
        ob_start();
        include($this->resolveTemplate());
        //get written content from buffer
        $content = ob_get_contents();
        ob_end_clean();
        return $content;
    }

    /**
     * Resolve template path
     *
     * @return string
     */
    private function resolveTemplate(): string
    {
        return Config::VIEW_DIR . $this->template . ".php";
    }

    /**
     * Check does field has validation errors
     *
     * @param string $fieldName
     * @return bool
     */
    public function hasError(string $fieldName): bool
    {
        if (empty($this->errors)) {
            return false;
        }
        return $this->errors[$fieldName] ?? false;
    }

    /**
     * Get formatted error string if field has validation error
     *
     * @param string $fieldName
     */
    public function showErrors(string $fieldName)
    {
        if(isset($this->errors[$fieldName])) {
            foreach ($this->errors[$fieldName] as $error) {
                echo "<span class='err-message'>$error</span><br>";
            }
        }
        echo '';
    }
}
