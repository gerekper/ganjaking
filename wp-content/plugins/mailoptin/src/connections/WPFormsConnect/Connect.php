<?php

namespace MailOptin\WPFormsConnect;


class Connect
{
    public function __construct()
    {
        add_action('wpforms_loaded', function () {
            MailOptin::get_instance();
        });
    }

    /**
     * Singleton poop.
     *
     * @return Connect|null
     */
    public static function get_instance()
    {
        static $instance = null;

        if (is_null($instance)) {
            $instance = new self();
        }

        return $instance;
    }
}