<?php

namespace Hms\Utils;

use Exception;

class Validator
{
    public function validate($params, $validation_rules)
    {
        try {
            $response = [];

            foreach ($validation_rules as $field => $rules) {
                foreach (explode('|', $rules) as $rule) {
                    if ($rule == 'required' && (array_key_exists($field, $params) == false || empty(trim($params[$field])))) {
                        $this->checkArrayKey($response, $field);
                        $response[$field] .= "The " . $field ." is required.";
                    }

                    if (array_key_exists($field, $params) == true) {
                        if ($rule == 'alphanumeric' && preg_match('/^[a-z0-9 .\-]+$/i', $params[$field]) == false) {
                            $this->checkArrayKey($response, $field);
                            $response[$field] .= "The value of " . $field . " is not a valid alphanumeric value.";
                        }

                        //if($rule == 'date' && preg_match())

                        if ($rule == 'phone' && preg_match('/^[0-9 \-\(\)\+]+$/i', $params[$field]) == false) {
                            $this->checkArrayKey($response, $field);
                            $response[$field] .= "The value of " . $field . " is not a valid phone number.";
                        }

                        if ($rule == 'email' && filter_var($params[$field], FILTER_VALIDATE_EMAIL) == false) {
                            $this->checkArrayKey($response, $field);
                            $response[$field] .= "The value of " . $field . " is not a valid email value.";
                        }

                        if ($rule == 'greater_zero' && $params[$field] == 0) {
                            $this->checkArrayKey($response, $field);
                            $response[$field] .= "Please select a $field";
                        }

                        if ($rule == 'password') {
                            if (!array_key_exists("{$field}_repeat", $params) || $params[$field] != $params["{$field}_repeat"]) {
                                $this->checkArrayKey($response, $field);
                                $response[$field] .= "Passwort und Passwort wiederholen stimmen nicht überein.";
                            }
                        }
                    }
                }
            }

            return $response;
        } catch (Exception $ex) {
            return $ex->getMessage();
        }
    }

    private function checkArrayKey(array &$array, string $field)
    {
        if (!array_key_exists($field, $array)) {
            $array[$field] = "";
        } else {
            $array[$field] .= "<br>";
        }
    }
}
