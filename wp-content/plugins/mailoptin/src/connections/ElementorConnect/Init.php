<?php

namespace MailOptin\ElementorConnect;

use Elementor\Controls_Manager;
use ElementorPro\Modules\Forms\Module;
use MailOptin\Core\Connections\ConnectionFactory;
use MailOptin\SendinblueConnect\Connect as SendinblueConnect;

if (strpos(__FILE__, 'mailoptin' . DIRECTORY_SEPARATOR . 'src') !== false) {
    // production url path to assets folder.
    define('MAILOPTIN_ELEMENTOR_CONNECT_ASSETS_URL', MAILOPTIN_URL . 'src/connections/ElementorConnect/assets/');
} else {
    // dev url path to assets folder.
    define('MAILOPTIN_ELEMENTOR_CONNECT_ASSETS_URL', MAILOPTIN_URL . '../' . dirname(substr(__FILE__, strpos(__FILE__, 'mailoptin'))) . '/assets/');
}

class Init
{
    public function __construct()
    {
        add_action('elementor_pro/init', [$this, 'load_integration']);
        add_action('elementor/controls/controls_registered', [$this, 'register_custom_control']);
        add_action('elementor/editor/after_enqueue_scripts', [$this, 'enqueue_script']);
        add_action('wp_ajax_mo_elementor_fetch_custom_fields', [$this, 'fetch_custom_fields']);
        add_action('wp_ajax_mo_elementor_fetch_tags', [$this, 'fetch_tags']);
    }

    public function load_integration()
    {
        Module::instance()->add_form_action('mailoptin', new Elementor());
    }

    public function register_custom_control(Controls_Manager $control_manager)
    {
        $control_manager->register_control('moselect', new CustomSelect());
    }

    public function enqueue_script()
    {
        if ( ! Init::is_mailoptin_detach_libsodium()) return;

        wp_enqueue_script('mailoptin-elementor', MAILOPTIN_ELEMENTOR_CONNECT_ASSETS_URL . 'elementor.js', ['jquery', 'underscore'], MAILOPTIN_VERSION_NUMBER, true);

        wp_localize_script('mailoptin-elementor', 'moElementor', [
            'fields'                  => [],
            'ajax_url'                => admin_url('admin-ajax.php'),
            'nonce'                   => wp_create_nonce('mailoptin-elementor'),
            'select2_tag_connections' => \MailOptin\Connections\Init::select2_tag_connections(),
            'text_tag_connections'    => \MailOptin\Connections\Init::text_tag_connections()
        ]);
    }

    public function fetch_custom_fields()
    {
        check_ajax_referer('mailoptin-elementor', 'nonce');

        \MailOptin\Core\current_user_has_privilege() || exit;

        if (empty($_POST['connection'])) wp_send_json_error([]);

        $instance = ConnectionFactory::make(sanitize_text_field($_POST['connection']));

        if ( ! in_array($instance::OPTIN_CUSTOM_FIELD_SUPPORT, $instance::features_support())) wp_send_json_error([]);

        $custom_fields = $instance->get_optin_fields(sanitize_text_field($_POST['connection_email_list']));

        if (empty($custom_fields)) wp_send_json_error([]);

        $fields = [];

        foreach ($custom_fields as $field_id => $field_label) {
            $fields[] = [
                'remote_id'    => $field_id,
                'remote_label' => $field_label,
                'remote_type'  => 'text'
            ];
        }

        $response = [
            'fields' => $fields
        ];

        wp_send_json_success($response);
    }

    public function fetch_tags()
    {
        check_ajax_referer('mailoptin-elementor', 'nonce');

        \MailOptin\Core\current_user_has_privilege() || exit;

        if (empty($_POST['connection'])) wp_send_json_error([]);

        $instance = ConnectionFactory::make(sanitize_text_field($_POST['connection']));

        if ( ! method_exists($instance, 'get_tags')) wp_send_json_error();

        $tags = $instance->get_tags();

        if (empty($tags)) wp_send_json_error([]);

        wp_send_json_success($tags);
    }

    public static function is_mailoptin_detach_libsodium()
    {
        return defined('MAILOPTIN_DETACH_LIBSODIUM') || SendinblueConnect::is_connected();
    }

    /**
     * Singleton poop.
     *
     * @return Init|null
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