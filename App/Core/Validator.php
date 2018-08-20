<?php namespace App\Core;


/**
 * Class Validator
 * @package App\Core
 */
class Validator
{
    /**
     * Data for validation
     *
     * @var array
     */
    private $data = [];

    /**
     * Validation rules for data validation
     *
     * @var array
     */
    private $rules = [];

    /**
     * List of errors
     *
     * @var array
     */
    private $errors = [];

    /**
     * Validator constructor.
     * @param array $data
     * @param array $rules
     */
    public function __construct(array $data, array $rules)
    {
        //Clean data from html tags and set it to class parameter
        $this->data = $this->clear($data);
        //set rules to class parameter
        $this->rules = $rules;
    }

    /**
     * Validate data
     *
     * @return Validator
     * @throws \Exception
     */
    public function validate(): Validator
    {
        foreach ($this->rules as $key => $rules) {
            $rules = explode('|', $rules);
            foreach ($rules as $rule) {
                //Get value from data set and if its empty and not required skip other checks
                $value = $this->getValue($key);
                if (!strlen($value) && $rule != 'required') {
                    continue;
                }

                //Resolve rule additional parameters
                if (preg_match('/\[(.*?)\]/', $rule, $match)) {
                    $rule = explode('[', $rule);
                    $rule = $rule[0];
                    $param = $match[1];
                    //check is rule method exists
                    if (!method_exists($this, $rule)) {
                        throw new \Exception("Method for rule $rule does not exist");
                    }
                    //set method call parameters
                    $call = array($value, $param);
                } else {
                    //check is rule method exists
                    if (!method_exists($this, $rule)) {
                        throw new \Exception("Method for rule $rule does not exist");
                    }
                    //set method call parameters
                    $call = array($value);
                }

                //call rule method with parameters
                $result = call_user_func_array(array($this, $rule), $call);

                //if rule return false add error message into error list
                if (!$result) {
                    //get error message template from message list
                    $error = $this->errorMessages()[$rule];

                    //Build error message with data
                    $replace = array(
                        ':attribute' => $key,
                        ':param' => $param ?? null
                    );
                    $errorMessage = str_replace(array_keys($replace), array_values($replace), $error);
                    $this->errors[$key][] = $errorMessage;
                }
            }
        }

        return $this;
    }

    /**
     * Check does validation has any errors
     *
     * @return bool
     */
    public function hasErrors(): bool
    {
        return count($this->errors) > 0;
    }

    /**
     * Get all errors
     *
     * @return array
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * Get all data (cleaned)
     *
     * @return array
     */
    public function getData(): array
    {
        return $this->data;
    }

    /**
     * Get value by key from data
     *
     * @param string $key
     * @return null|string
     */
    private function getValue(string $key): ?string
    {
        if (array_key_exists($key, $this->data)) {
            return (string)$this->data[$key];
        }
        return null;
    }

    /**
     * Clear all request data from html injections
     *
     * @param $data
     * @return array
     */
    private function clear($data): array
    {
        foreach ($data as $key => $value) {
            $value = strip_tags($value);
            $value = trim($value);
            $value = stripslashes($value);
            $value = htmlspecialchars($value);
            $data[$key] = $value;
        }
        return $data;
    }

    /**
     * List of error messages
     *
     * @return array
     */
    private function errorMessages(): array
    {
        return [
            'required' => 'The :attribute field is required.',
            'min_words' => 'The :attribute field must contain min :param words.',
            'alpha' => 'The :attribute field must contain only letters.',
            'date' => 'The :attribute field must contain date with format :param.',
            'before_now_date' => 'The :attribute field must containt date with format :param, and not future date.',
            'email' => 'The :attribute field must be a valid email address.'
        ];
    }

    //###########   RULE METHODS   ############################

    /**
     * Check value is set and not empty
     *
     * @param $value
     * @return bool
     */
    private function required($value): bool
    {
        return (strlen($value) !== 0);
    }

    /**
     * Check value has minimum words.
     *
     * @param $value
     * @param $param
     * @return bool
     */
    private function min_words($value, $param): bool
    {
        $value = explode(' ', $value);
        return (count($value) >= $param);
    }

    /**
     * Check value has only letters and spaces
     *
     * @param $value
     * @return bool
     */
    private function alpha($value): bool
    {
        $value = str_replace(' ', '', $value);
        return ctype_alpha($value);
    }

    /**
     * Check value is date with specified format
     *
     * @param $value
     * @param $param
     * @return bool
     */
    private function date($value, $param): bool
    {
        $date = \DateTime::createFromFormat('!' . $param, $value);
        return $date && $date->format($param) == $value;
    }

    /**
     * Check date is not future date and specified format
     *
     * @param $value
     * @param $param
     * @return bool
     */
    private function before_now_date($value, $param): bool
    {
        $date = \DateTime::createFromFormat('!' . $param, $value);
        if (!$date || $date->format($param) !== $value) {
            return false;
        }
        $now_date = new \DateTime("now");
        return $date < $now_date;
    }

    /**
     * Check value is email format
     *
     * @param $value
     * @return bool
     */
    private function email($value): bool
    {
        return filter_var($value, FILTER_VALIDATE_EMAIL) !== false;
    }
}
