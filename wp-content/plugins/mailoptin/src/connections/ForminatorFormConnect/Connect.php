<?php

namespace MailOptin\ForminatorFormConnect;

use Forminator_Addon_Loader;

class Connect
{
    public function __construct()
    {
        add_action('forminator_addons_loaded', [$this, 'is_forminator_form_installed']);
    }

    public function is_forminator_form_installed()
    {
        $instance = Forminator_Addon_Loader::get_instance();

        $instance->register('MailOptin\ForminatorFormConnect\FFMailOptin');

        if ( ! $instance->addon_is_active('mailoptin')) {
            $instance->activate_addon('mailoptin');
        }
    }

    /**
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