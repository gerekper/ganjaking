<?php

namespace MailOptin\FormidableFormConnect;

class Connect
{
    public function __construct()
    {
        //load formidable hooks
        add_action( 'frm_trigger_mailoptin_action', [$this, 'trigger_integration'], 10, 3 );
        add_action( 'frm_registered_form_actions', [$this, 'register_actions']);

        add_filter('frm_action_groups', function ($groups) {
            $groups['marketing']['actions'][] = 'mailoptin';

            return $groups;
        });
    }

    public static function trigger_integration($action, $entry, $form)
    {
        FormidableForm::send_to_mailoptin($action, $entry, $form);
    }

    public static function register_actions( $actions ) {
        $actions['mailoptin'] = 'MailOptin\FormidableFormConnect\FormidableForm';

        return $actions;
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