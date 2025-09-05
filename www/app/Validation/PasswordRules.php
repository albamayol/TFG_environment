<?php
namespace App\Validation;

class PasswordRules
{
    /**
     * not_contains_user_fields[email,name,...]
     * Falla si la contraseña contiene (ignorando mayúsculas/espacios) alguno de esos campos.
     */
    public function not_contains_user_fields(string $str, string $fields, array $data, ?string &$error = null): bool
    {
        $fieldNames = array_filter(array_map('trim', explode(',', $fields)));
        $haystack = mb_strtolower(preg_replace('/\s+/', '', $str)); // sin espacios

        foreach ($fieldNames as $name) {
            $val = $data[$name] ?? '';
            if ($val === '') continue;

            // email: también prueba con la parte local (antes de @)
            $candidates = [$val];
            if ($name === 'email') {
                $local = strstr($val, '@', true);
                if ($local) $candidates[] = $local;
            }

            foreach ($candidates as $cand) {
                $needle = mb_strtolower(preg_replace('/\s+/', '', (string)$cand));
                if (mb_strlen($needle) >= 3 && mb_strpos($haystack, $needle) !== false) {
                    $error = 'Password can not contain user information (email, name, phone, etc.).';
                    return false;
                }
            }
        }
        return true;
    }
}
