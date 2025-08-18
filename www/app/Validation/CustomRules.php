<?php

namespace App\Validation;

class CustomRules
{
    /**
     * Check if the special code starts with "A" and is exactly 5 characters long.
     *
     * @param string $str The input value.
     * @return bool
     */
    public function check_special_code(string $str): bool
    {
        return (str_starts_with($str, 'A') && strlen($str) === 5);
    }

    /**
     * Check if the input is a valid URL.
     *
     * @param string $str The input value.
     * @return bool
     */
    public function check_url(string $str): bool
    {
        return filter_var($str, FILTER_VALIDATE_URL) !== false;
    }


    /**
     * Check if the input is a valid phone number.
     *
     * @param string $str The input value.
     * @return bool
     */
    public function check_phone_number(string $str): bool
    {
        return preg_match('/^\+?[0-9\s\-\(\)]{7,20}$/', $str) === 1;
    }

}