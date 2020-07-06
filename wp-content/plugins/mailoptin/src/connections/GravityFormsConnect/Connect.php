<?php

namespace MailOptin\GravityFormsConnect;

class Connect
{
    public function __construct()
    {
        add_action('gform_loaded', function () {
            // Registers the class name with GFAddOn.
            \GFAddOn::register(__NAMESPACE__ . '\GFMailOptin');
        }, 5);
    }

    /**
     * @return self
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