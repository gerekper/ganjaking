<?php

namespace MailOptin\ContactForm7Connect;

class Connect
{
    public function __construct()
    {
        add_action('wpcf7_init', function () {
            new CF7();
            \WPCF7_Integration::get_instance()->add_service('mailoptin', IntegrationsListing::get_instance());
        });
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