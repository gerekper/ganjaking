<?php

namespace ACP\Sorting\FormatValue;

use ACP\Sorting\FormatValue;

class Roles implements FormatValue
{

    private function get_role_label(string $capability): ?string
    {
        static $labels = [];

        if ( ! isset($labels[$capability])) {
            $labels[$capability] = $this->get_translated_role_label($capability);
        }

        return $labels[$capability];
    }

    private function get_translated_role_label(string $capability): ?string
    {
        global $wp_roles;

        $role = $wp_roles->roles[$capability]['name'] ?? null;

        if ( ! $role) {
            return null;
        }

        return translate_user_role($role) ?: null;
    }

    public function format_value($value)
    {
        $caps = maybe_unserialize($value);

        if ( ! $caps || ! is_array($caps)) {
            return false;
        }

        $capabilities = array_keys(array_filter($caps));

        foreach ($capabilities as $capability) {
            $role = $this->get_role_label((string)$capability);

            if ($role) {
                return $role;
            }
        }

        return false;
    }

}
