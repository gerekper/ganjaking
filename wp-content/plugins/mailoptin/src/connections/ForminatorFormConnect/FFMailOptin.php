<?php

namespace MailOptin\ForminatorFormConnect;

use Forminator_Addon_Abstract;
use MailOptin\Core\Repositories\ConnectionsRepository;

class FFMailOptin extends Forminator_Addon_Abstract
{

    private static $_instance = null;

    protected $_slug = 'mailoptin';

    protected $_version = MAILOPTIN_VERSION_NUMBER;

    protected $_min_forminator_version = '1.1';

    protected $_short_title = 'MailOptin';

    protected $_title = 'MailOptin';

    protected $_url = 'https://mailoptin.io/pricing/';

    protected $_full_path = __FILE__;

    /**
     * Class name of form settings
     *
     * MailOptin Addon
     *
     * @var string
     */
    protected $_form_settings = 'MailOptin\ForminatorFormConnect\ConnectionFormSettingsPage';

    /**
     * Class name of form hooks
     *
     * MailOptin Addon
     * @var string
     */
    protected $_form_hooks = 'MailOptin\ForminatorFormConnect\FormHook';

    /**
     * Class name of quiz settings
     *
     * MailOptin Addon
     *
     * @var string
     */
    protected $_quiz_settings = 'MailOptin\ForminatorFormConnect\ConnectionQuizSettingsPage';

    /**
     * Class name of quiz hooks
     *
     * MailOptin Addon
     * @var string
     */
    protected $_quiz_hooks = 'MailOptin\ForminatorFormConnect\QuizHook';

    /**
     * Hold account information that currently connected
     * Will be saved to @see FFMailOptin::save_settings_values()
     *
     * @var array
     */
    private $_connected_account = array();

    protected $_position = 8;

    /**
     * FFMailOptin constructor.
     * - Set dynamic translatable text(s) that will be displayed to end-user
     * - Set dynamic icons and images
     *
     * MailOptin Addon
     */
    public function __construct()
    {
        // late init to allow translation
        $this->_description                = __('Get awesome by your form', 'mailoptin');
        $this->_activation_error_message   = __('Sorry but we failed to activate MailOptin integration, don\'t hesitate to contact us', 'mailoptin');
        $this->_deactivation_error_message = __('Sorry but we failed to deactivate MailOptin integration, please try again', 'mailoptin');

        $this->_update_settings_error_message = __(
            'Sorry, we failed to update settings, please check your form and try again',
            'mailoptin'
        );

        //insert mailoptin icons here
        $this->_icon     = MAILOPTIN_ASSETS_URL . 'images/forminator-addon-icon.png';
        $this->_icon_x2  = MAILOPTIN_ASSETS_URL . 'images/forminator-addon-icon@2x.png';
        $this->_image    = MAILOPTIN_ASSETS_URL . 'images/forminator-addon-icon.png';
        $this->_image_x2 = MAILOPTIN_ASSETS_URL . 'images/forminator-addon-icon@2x.png';
    }

    /**
     * Hook before save settings values
     *
     * for future reference
     *
     * MailOptin Addon
     *
     * @param array $values
     *
     * @return array
     */
    public function before_save_settings_values($values)
    {
        forminator_addon_maybe_log(__METHOD__, $values);

        if ( ! empty($this->_connected_account)) {
            $values['connected_account'] = $this->_connected_account;
        }

        return $values;
    }

    /**
     * Flag for check whether mailoptin addon is connected globally
     *
     * MailOptin Addon
     * @return bool
     */
    public function is_connected()
    {
        try {
            if (empty($this->email_service_providers())) {
                throw new \Exception(__('No Email Provider is Connected', 'mailoptin'));
            }

            // if user completed settings
            $is_connected = $this->mailoptin_settings_complete();

        } catch (\Exception $e) {
            $is_connected = false;
        }

        return apply_filters('forminator_addon_mailoptin_is_connected', $is_connected);
    }

    /**
     * Settings wizard
     *
     * mailOptin Addon
     * @return array
     */
    public function settings_wizards()
    {
        return array(
            array(
                'callback'     => [$this, 'connect_mailoptin'],
                'is_completed' => [$this, 'mailoptin_settings_complete'],
            ),
        );
    }

    /**
     * Wizard of connect_mailoptin
     *
     * MailOptin Addon
     *
     * @param     $submitted_data
     * @param int $form_id
     *
     * @return array
     */
    public function connect_mailoptin($submitted_data, $form_id = 0)
    {
        $link = '';
        $html = '';
        if ( ! $this->is_connected()) {
            $link = '<a href="' . MAILOPTIN_CONNECTIONS_SETTINGS_PAGE . '" class="button button-secondary">' . __('Connect Now', 'mailoptin') . '</a>';

            $html = __('No Integration Connected to MailOptin', 'mailoptin');
        } else {
            $html = __('Connected', 'mailoptin');
        }

        return [
            'html' => '<div class="integration-header">
					<h3 class="sui-box-title" id="dialogTitle2">' . sprintf(__('%1$s with Forminator', 'mailoptin'), 'MailOptin') . '</h3> 
				</div>
				<div class="sui-form-field" style="text-align: center">
				    <h3>' . $html . '</h3>
				    <div>' . $link . '</div>
                </div>
				',
        ];
    }

    /**
     * Return with true / false, you may update you setting update message too
     *
     * @param $email_service_providers
     *
     * @return bool
     * @see   _update_settings_error_message
     *
     * @since 1.0 Mailchimp Addon
     *
     */
    protected function validate_mailoptin_email_service_providers($email_service_providers)
    {
        if (empty($email_service_providers)) {
            $this->_update_settings_error_message = __('Please connect an Email Service Provider', 'mailoptin');

            return false;
        }

        try {
            $this->_connected_account = $this->email_providers_and_lists();

        } catch (\Exception $e) {
            $this->_update_settings_error_message = $e->getMessage();

            return false;
        }

        return true;
    }


    public function email_service_providers()
    {
        $connections = ConnectionsRepository::get_connections();

        if (defined('MAILOPTIN_DETACH_LIBSODIUM')) {
            $connections['leadbank'] = __('MailOptin Leads', 'mailoptin');
        }

        return $connections;
    }

    public function email_providers_and_lists()
    {
        $data = [];

        foreach ($this->email_service_providers() as $key => $value) {

            if ($key == 'leadbank') continue;

            $data[$value] = ConnectionsRepository::connection_email_list($key);
        }

        return $data;
    }

    /**
     * Check if user already completed settings
     *
     * MailOptin Addon
     * @return bool
     */
    private function mailoptin_settings_complete()
    {
        $setting_values = $this->get_settings_values();

        $setting_values['email_service_providers'] = $this->email_service_providers();

        // check api_key and connected_account exists and not empty
        return isset($setting_values['email_service_providers']) && ! empty($setting_values['email_service_providers']);
    }

    /**
     * Flag for check if and addon connected to a form
     * by default it will check if last step of form settings already completed by user
     *
     * MailOptin Addon
     *
     * @param $form_id
     *
     * @return bool
     */
    public function is_form_connected($form_id)
    {

        try {
            // initialize with null
            $form_settings_instance = null;
            if ( ! $this->is_connected()) {
                throw new \Exception(__('MailOptin addon not connected.', 'mailoptin'));
            }

            $form_settings_instance = $this->get_addon_form_settings($form_id);
            if ( ! $form_settings_instance instanceof ConnectionFormSettingsPage) {
                throw new \Exception(__('Form settings instance is not valid Forminator MailOptin Form Addon.', 'mailoptin'));
            }

            $wizards = $form_settings_instance->form_settings_wizards();

            $last_step             = end($wizards);
            $last_step_is_complete = call_user_func($last_step['is_completed']);
            if ( ! $last_step_is_complete) {
                throw new \Exception(__('Form settings is not yet completed.', 'mailoptin'));
            }

            $is_form_connected = true;
        } catch (\Exception $e) {
            $is_form_connected = false;

            forminator_addon_maybe_log(__METHOD__, $e->getMessage());
        }

        $is_form_connected = apply_filters('forminator_addon_mailoptin_is_form_connected', $is_form_connected, $form_id, $form_settings_instance);

        return $is_form_connected;
    }

    /**
     * Flag for check if and addon connected to a form
     * by default it will check if last step of form settings already completed by user
     *
     * MailOptin Addon
     *
     * @param $quiz_id
     *
     * @return bool
     */
    public function is_quiz_connected($quiz_id)
    {
        try {
            // initialize with null
            $quiz_settings_instance = null;
            if ( ! $this->is_connected()) {
                throw new \Exception(__('MailOptin addon not connected.', 'mailoptin'));
            }

            $quiz_settings_instance = $this->get_addon_quiz_settings($quiz_id);
            if ( ! $quiz_settings_instance instanceof ConnectionQuizSettingsPage) {
                throw new \Exception(__('Quiz settings instance is not valid Forminator MailOptin Quiz Addon.', 'mailoptin'));
            }

            $wizards = $quiz_settings_instance->quiz_settings_wizards();

            $last_step             = end($wizards);
            $last_step_is_complete = call_user_func($last_step['is_completed']);
            if ( ! $last_step_is_complete) {
                throw new \Exception(__('Quiz settings is not yet completed.', 'mailoptin'));
            }

            $is_quiz_connected = true;


        } catch (\Exception $e) {
            $is_quiz_connected = false;

            forminator_addon_maybe_log(__METHOD__, $e->getMessage());
        }

        /**
         * Filter connected status of mailchimp with the form
         *
         * @param bool $is_quiz_connected
         * @param int $quiz_id Current Quiz ID
         * @param ConnectionQuizSettingsPage |null $quiz_settings_instance Instance of quiz settings, or null when unavailable
         *
         * @since 1.1
         *
         */
        $is_quiz_connected = apply_filters('forminator_addon_mailoptin_is_form_connected', $is_quiz_connected, $quiz_id, $quiz_settings_instance);

        return $is_quiz_connected;
    }


    /**
     * Get addon instance
     *
     * MailOptin Addon
     * @return self|null
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