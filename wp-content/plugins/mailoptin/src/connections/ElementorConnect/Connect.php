<?php

namespace MailOptin\ElementorConnect;

class Connect
{
    public function __construct()
    {
        Init::get_instance();
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