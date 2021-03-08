<?php

namespace MailOptin\NinjaFormsConnect;


use MailOptin\Core\Connections\AbstractConnect;
use MailOptin\Core\Repositories\ConnectionsRepository;
use MailOptin\Core\Repositories\StateRepository;

class Connect
{
    public $state_version = MAILOPTIN_VERSION_NUMBER;

    public function __construct()
    {
        add_filter('ninja_forms_field_settings_groups', array($this, 'register_settings_groups'));

        add_filter('ninja_forms_register_actions', [$this, 'register_nf_actions']);
    }

    public static function features_support()
    {
        return [];
    }

    /*
     * Function to register our field settings.
     */
    public function register_settings_groups($groups)
    {
        $groups['motags'] = array(
            'id'       => 'motags',
            'label'    => __('Tags', 'mailoptin'),
            'priority' => 200
        );

        return $groups;
    }

    public function register_nf_actions($actions)
    {
        $connections = ConnectionsRepository::get_connections();

        if (is_array($connections) && ! empty($connections)) {

            foreach ($connections as $key => $label) {

                if (empty($key)) continue;

                $this->generate_classes($key, $label);

                $ninjaFormActionClass = "MailOptin\\NinjaFormsConnect\\Integrations\\$key";

                if (class_exists($ninjaFormActionClass)) {
                    $actions[$key] = new $ninjaFormActionClass();
                }
            }
        }

        return $actions;
    }

    private function generate_classes($key, $label)
    {
        //if ( ! defined('W3GUY_LOCAL')) return false;

        $db_saved_state = StateRepository::get_instance()->get('mo_ninjaforms_action_state');

        $filename = dirname(__FILE__) . "/integrations/$key.php";

        if ($db_saved_state < $this->state_version) {
            $this->setup_file_system()->delete($filename);
        }

        // using file_exists instead of $wp_filesystem own to remove the overhead of loading wp file system
        if ( ! file_exists($filename)) {

            if (false === ($wp_filesystem = $this->setup_file_system())) return false;

            if (false === ($class_file_content = $wp_filesystem->get_contents(dirname(__FILE__) . '/MoNinjaConnect.php'))) {
                AbstractConnect::save_optin_error_log($key . ' Failed to read connect class', 'ninjaforms');

                return false;
            }

            $class_file_content = str_replace(['MoNinjaConnect', 'MoNinja'], [$key, $label], $class_file_content);

            if ( ! $wp_filesystem->put_contents($filename, $class_file_content, FS_CHMOD_FILE)) {
                AbstractConnect::save_optin_error_log($key . ' Error saving file', 'ninjaforms');

                return false;
            }

            StateRepository::get_instance()->set('mo_ninjaforms_action_state', $this->state_version);
        }

        return true;
    }

    private function setup_file_system()
    {
        static $instance = false;

        if (false === $instance) {

            if ( ! function_exists('request_filesystem_credentials')) {
                require_once ABSPATH . 'wp-admin/includes/file.php';
            }

            if (false === ($creds = request_filesystem_credentials(site_url() . '/wp-admin/', '', false, false, []))) {
                AbstractConnect::save_optin_error_log('Filesystem requires credentials to proceed', 'ninjaforms');

                return $instance;
            }

            // now we have some credentials, try to get the wp_filesystem running
            if ( ! WP_Filesystem($creds)) {
                AbstractConnect::save_optin_error_log('Filesystem requires credentials incorrect. Bail', 'ninjaforms');

                return $instance;
            }

            global $wp_filesystem;

            $instance = $wp_filesystem;
        }

        return $instance;
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