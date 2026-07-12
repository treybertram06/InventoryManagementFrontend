<?php
namespace Core;

class Validator {
    public static function validate_username($username): bool {
        // 3-32 chars, letters/numbers/underscore/dash, must start with a letter
        return is_string($username) && preg_match('/^[a-zA-Z][a-zA-Z0-9_-]{2,31}$/', $username) === 1;
    }

    public static function validate_email($email): bool {
        return is_string($email) && filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }

    public static function validate_password($password): bool {
        // at least 8 chars, one uppercase, one lowercase, one digit
        if (!is_string($password) || strlen($password) < 8) {
            return false;
        }
        return preg_match('/[A-Z]/', $password) === 1
            && preg_match('/[a-z]/', $password) === 1
            && preg_match('/[0-9]/', $password) === 1;
    }

}