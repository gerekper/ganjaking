<?php


namespace MailOptin\FormidableFormConnect;

use FrmFormAction;
use MailOptin\Connections\Init;
use MailOptin\Core\AjaxHandler;
use FrmField;
use MailOptin\Core\Repositories\ConnectionsRepository;
use MailOptin\Core\OptinForms\ConversionDataBuilder;
use MailOptin\Core\Connections\ConnectionFactory;
use function MailOptin\Core\moVar;

class FormidableForm extends FrmFormAction
{
    private static $entry;
    private static $action;

    public function __construct()
    {
        $action_ops = array(
            'classes'  => 'frm_cloud_icon frm_icon_font',
            'active'   => true,
            'limit'    => 99,
            'priority' => 29,
            'event'    => array('create', 'update'),
            'color'    => 'var(--dark-blue)',
        );

        $this->FrmFormAction('mailoptin', __('MailOptin', 'mailoptin'), $action_ops);

        add_action('admin_footer', [$this, 'js_script']);
    }

    public function js_script()
    {
        ?>
        <script>
            (function ($) {
                $(document).on('change', '#mofmSelectIntegration, #mofmSelectList', function () {
                    var btnSaveList = document.getElementsByClassName('frm_submit_settings_btn');
                    if (btnSaveList.length > 0) btnSaveList[0].click();
                });
            })(jQuery);
        </script>
        <?php
    }

    public function form($form_action, $args = array())
    {
        $post_content = $form_action->post_content;

        $saved_integration = moVar($post_content, 'mofm_integration');
        $saved_list        = moVar($post_content, 'mofm_list');
        $saved_tags        = moVar($post_content, 'mofm_tags');

        $lists = [];
        if ( ! empty($saved_integration) && $saved_integration != 'leadbank') {
            $lists = ConnectionFactory::make($saved_integration)->get_email_list();
        }

        $tags = [];
        if ( ! empty($saved_integration) && in_array($saved_integration, Init::select2_tag_connections())) {
            $instance = ConnectionFactory::make($saved_integration);
            if (method_exists($instance, 'get_tags')) {
                $tags = $instance->get_tags();
            }
        }

        $custom_fields = [
            'moEmail' => esc_html__('Email Address', 'mailoptin'),
            'moName'  => esc_html__('Full Name', 'mailoptin'),
        ];

        if (in_array($saved_integration, Init::no_name_mapping_connections())) {
            unset($custom_fields['moName']);
        }

        $form_fields = FrmField::getAll('fi.form_id=' . (int)$args['form']->id . " and fi.type not in ('break', 'divider', 'end_divider', 'html', 'captcha', 'form')", 'field_order');

        if ( ! empty($saved_integration) && $saved_integration != 'leadbank') {

            if (defined('MAILOPTIN_DETACH_LIBSODIUM')) {
                $instance = ConnectionFactory::make($saved_integration);

                if (in_array($instance::OPTIN_CUSTOM_FIELD_SUPPORT, $instance::features_support())) {
                    $cfields = $instance->get_optin_fields($saved_list);
                    if (is_array($cfields) && ! empty($cfields)) {
                        $custom_fields += $cfields;
                    }
                }
            }
        }

        include dirname(__FILE__) . '/panel-settings-view.php';
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

    public static function send_to_mailoptin($action, $entry, $form)
    {
        $settings     = $action->post_content;
        self::$action = $action;
        self::$entry  = $entry;

        self::process_form($entry, $form, $settings);
    }

    public static function process_form($entry, $form, $settings)
    {
        $postdata = self::get_field_values_for_mailoptin($entry, $settings, []);

        $field_mapping = moVar($settings, 'mofm_custom_fields');

        $optin_data = new ConversionDataBuilder();
        // since it's non mailoptin form, set it to zero.
        $optin_data->optin_campaign_id   = 0;
        $optin_data->payload             = $postdata;
        $optin_data->name                = $postdata['moName'];
        $optin_data->email               = $postdata['moEmail'];
        $optin_data->optin_campaign_type = esc_html__('Formidable Forms', 'mailoptin');

        $optin_data->connection_service    = moVar($settings, 'mofm_integration');
        $optin_data->connection_email_list = moVar($settings, 'mofm_list');

        $optin_data->user_agent                = esc_html($_SERVER['HTTP_USER_AGENT']);
        $optin_data->is_timestamp_check_active = false;

        if (isset($_REQUEST['referrer'])) {
            $optin_data->conversion_page = esc_url_raw($_REQUEST['referrer']);
        }

        $optin_data->form_tags = moVar($settings, 'mofm_tags');
        foreach ($field_mapping as $key => $fm_form_tag) {
            if (in_array($key, ['moEmail', 'moName'])) continue;
            $field_value = moVar($postdata, $key);

            if ( ! empty($field_value)) {
                $optin_data->form_custom_field_mappings[$key] = $key;
            }
        }

        AjaxHandler::do_optin_conversion($optin_data);
    }


    private static function get_field_values_for_mailoptin($entry, $settings, $vars)
    {
        foreach ($settings['mofm_custom_fields'] as $field_tag => $field_id) {

            if (empty($field_id)) continue;

            $vars[$field_tag] = self::get_entry_or_post_value($entry, $field_id);
        }

        return $vars;
    }

    public static function get_entry_or_post_value($entry, $field_id)
    {
        $value = '';
        if ( ! empty($entry) && isset($entry->metas[$field_id])) {
            $value = $entry->metas[$field_id];
        } elseif (isset($_POST['item_meta'][$field_id])) {
            $value = $_POST['item_meta'][$field_id];
        }

        return $value;
    }

    /**
     * Singleton poop.
     *
     * @return FormidableForm|null
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