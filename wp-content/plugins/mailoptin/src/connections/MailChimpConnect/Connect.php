<?php

namespace MailOptin\MailChimpConnect;

use MailOptin\Core\Admin\Customizer\OptinForm\CustomizerSettings;
use MailOptin\Core\Connections\ConnectionInterface;
use MailOptin\Core\OptinForms\AbstractOptinForm;
use MailOptin\Core\Repositories\EmailCampaignRepository;
use MailOptin\Core\Repositories\OptinCampaignsRepository;

if (strpos(__FILE__, 'mailoptin' . DIRECTORY_SEPARATOR . 'src') !== false) {
    // production url path to assets folder.
    define('MAILOPTIN_MAILCHIMP_CONNECT_ASSETS_URL', MAILOPTIN_URL . 'src/connections/MailChimpConnect/assets/');
} else {
    // dev url path to assets folder.
    define('MAILOPTIN_MAILCHIMP_CONNECT_ASSETS_URL', MAILOPTIN_URL . '../' . dirname(substr(__FILE__, strpos(__FILE__, 'mailoptin'))) . '/assets/');
}

class Connect extends AbstractMailChimpConnect implements ConnectionInterface
{
    /**
     * @var string key of connection service. its important all connection name ends with "Connect"
     */
    public static $connectionName = 'MailChimpConnect';

    public function __construct()
    {
        ConnectSettingsPage::get_instance();

        add_filter('mailoptin_registered_connections', array($this, 'register_connection'));

        add_filter('mo_optin_form_integrations_default', array($this, 'integration_customizer_settings'), 10, 3);
        add_action('mo_optin_integrations_controls_after', array($this, 'integration_customizer_controls'), 10, 4);

        add_action('mo_optin_form_global_css', [$this, 'css_style_segment_area'], 10, 3);

        add_filter('mo_optin_form_fonts_list', [$this, 'include_segment_field_font'], 10, 2);

        add_action('mailoptin_email_template_before_forge', [$this, 'mailchimp_reward_badge']);

        add_action('wp_ajax_mailoptin_customizer_fetch_mailchimp_groups', [$this, 'customizer_fetch_mailchimp_groups']);
        add_action('wp_ajax_mailoptin_customizer_fetch_mailchimp_segment', [$this, 'customizer_fetch_mailchimp_segment']);

        add_action('mo_optin_theme_shortcodes_add', [$this, 'add_mailchimp_interest_shortcode']);

        add_filter('mo_optin_js_config', [$this, 'add_values_js_config'], 10, 2);

        add_action('mo_optin_integration_control_enqueue', function () {
            wp_enqueue_script(
                'mailchimp-group-control',
                MAILOPTIN_MAILCHIMP_CONNECT_ASSETS_URL . 'mailchimp.js',
                array('jquery', 'customize-controls'),
                MAILOPTIN_VERSION_NUMBER
            );
        });

        add_action('mailoptin_email_campaign_enqueue_customizer_js', function () {
            wp_enqueue_script(
                'mailchimp-group-control',
                MAILOPTIN_MAILCHIMP_CONNECT_ASSETS_URL . 'emailcustomizer.js',
                array('jquery', 'customize-controls'),
                MAILOPTIN_VERSION_NUMBER
            );
        });


        add_filter('mo_optin_integrations_advance_controls', array($this, 'customizer_advance_controls'));
        add_filter('mo_optin_form_integrations_default', [$this, 'customizer_advance_controls_defaults']);

        add_filter('mo_connections_with_advance_settings_support', function ($val) {
            $val[] = self::$connectionName;

            return $val;
        });

        parent::__construct();
    }

    public static function features_support()
    {
        return [
            self::OPTIN_CAMPAIGN_SUPPORT,
            self::EMAIL_CAMPAIGN_SUPPORT,
            self::OPTIN_CUSTOM_FIELD_SUPPORT
        ];
    }

    public function customizer_advance_controls_defaults($defaults)
    {
        $defaults['MailChimpConnect_first_name_field_key'] = 'FNAME';
        $defaults['MailChimpConnect_last_name_field_key']  = 'LNAME';

        return $defaults;
    }

    /**
     * @param $controls
     *
     * @return array
     */
    public function customizer_advance_controls($controls)
    {
        // always prefix with the name of the connect/connection service.
        $controls[] = [
            'field'       => 'text',
            'name'        => 'MailChimpConnect_first_name_field_key',
            'label'       => __('First Name Merge Tag', 'mailoptin'),
            'description' => sprintf(
                __('If subscribers first names are missing, change this to the correct merge tag. %sLearn more%s', 'mailoptin'),
                '<a href="https://mailoptin.io/?p=21482" target="_blank">', '</a>'
            )
        ];
        $controls[] = [
            'field'       => 'text',
            'name'        => 'MailChimpConnect_last_name_field_key',
            'label'       => __('Last Name Merge Tag', 'mailoptin'),
            'description' => sprintf(
                __('If subscribers last names are missing, change this to the correct merge tag. %sLearn more%s', 'mailoptin'),
                '<a href="https://mailoptin.io/?p=21482" target="_blank">', '</a>'
            )
        ];

        return $controls;
    }

    /**
     * Register MailChimp Connection.
     *
     * @param array $connections
     *
     * @return array
     */
    public function register_connection($connections)
    {
        $connections[self::$connectionName] = __('Mailchimp', 'mailoptin');

        return $connections;
    }

    /**
     * Include mailchimp reward badge so their automatic footer won't be added for forever-free accounts.
     *
     * @param $email_campaign_id
     */
    public function mailchimp_reward_badge($email_campaign_id)
    {
        if (apply_filters('mo_disable_mailchimp_reward_badge', false)) return;

        $connect_service = EmailCampaignRepository::get_customizer_value($email_campaign_id, 'connection_service');

        if ($connect_service == 'MailChimpConnect') {
            add_filter('mailoptin_email_template_footer_description', [$this, 'add_reward_merge_tag']);
        } else {
            remove_filter('mailoptin_email_template_footer_description', [$this, 'add_reward_merge_tag']);
        }
    }

    /**
     * Replace placeholder tags with actual Mailchimp tags.
     *
     * {@inheritdoc}
     */
    public function replace_placeholder_tags($content, $type = 'html')
    {
        $search = [
            '{{webversion}}',
            '{{unsubscribe}}'
        ];

        $replace = [
            '*|ARCHIVE|*',
            '*|UNSUB|*'
        ];

        $content = str_replace($search, $replace, $content);

        return $this->replace_footer_placeholder_tags($content);
    }

    /**
     * {@inherit_doc}
     *
     * Return array of email list
     *
     * @return mixed
     */
    public function get_email_list()
    {
        try {
            $response = $this->mc_list_instance()->getLists(['count' => 100]);

            // an array with list id as key and name as value.
            $lists_array = array();
            if (isset($response->lists) && is_array($response->lists)) {
                foreach ($response->lists as $list) {
                    $lists_array[$list->id] = $list->name;
                }
            }

            return $lists_array;


        } catch (\Exception $e) {
            self::save_optin_error_log($e->getMessage(), 'mailchimp');
        }
    }

    /**
     * {@inherit_doc}
     *
     * Return array of email list
     *
     * @return mixed
     */
    public function get_optin_fields($list_id = '')
    {
        try {

            $response = $this->mc_list_instance()->getMergeFields(
                $list_id,
                ['count' => 9999, 'fields' => 'merge_fields.tag,merge_fields.name,merge_fields.type,merge_fields.options']
            );

            $firstname_key = $this->get_first_name_merge_tag();
            $lastname_key  = $this->get_last_name_merge_tag();

            $merge_fields_array = array();

            if (isset($response->merge_fields) && is_array($response->merge_fields)) {
                foreach ($response->merge_fields as $merge_field) {
                    if (in_array($merge_field->tag, [$firstname_key, $lastname_key])) continue;

                    $merge_fields_array[$merge_field->tag] = $merge_field->name;
                }
            }

            return $merge_fields_array;


        } catch (\Exception $e) {
            self::save_optin_error_log($e->getMessage(), 'mailchimp');

            return [];
        }
    }

    /**
     * @param int $email_campaign_id
     * @param int $campaign_log_id
     * @param string $subject
     * @param string $content_html
     * @param string $content_text
     *
     * @return array
     * @throws \Exception
     *
     */
    public function send_newsletter($email_campaign_id, $campaign_log_id, $subject, $content_html, $content_text)
    {
        return (new SendCampaign($email_campaign_id, $campaign_log_id, $subject, $content_html, $content_text))->send();
    }

    /**
     * @param string $email
     * @param string $name
     * @param string $list_id ID of email list to add subscriber to
     * @param mixed|null $extras
     *
     * @return mixed
     */
    public function subscribe($email, $name, $list_id, $extras = null)
    {
        return (new Subscription($email, $name, $list_id, $extras, $this))->subscribe();
    }

    /**
     * Forever free account requires the reward badge to be added in email.
     *
     * @param string $footer_description
     *
     * @return string
     */
    public function add_reward_merge_tag($footer_description)
    {
        // do not add MailChimp reward merge tag to customizer preview.
        if ( ! is_customize_preview()) {
            $footer_description .= '<br/>' . '*|IF:REWARDS|* *|HTML:REWARDS|* *|END:IF|*';
        }

        return $footer_description;
    }

    private function default_display_style($optin_campaign_type)
    {
        return $optin_campaign_type == 'bar' ? 'inline' : 'block';
    }

    private function default_display_align($optin_campaign_type)
    {
        return $optin_campaign_type == 'bar' ? 'center' : 'left';
    }

    private function default_segmentation_values($optin_type = null)
    {
        return [
            'segment_type'           => apply_filters('mailoptin_customizer_optin_campaign_MailChimpConnect_group_segment_type', 'automatic'),
            'selection_type'         => apply_filters('mailoptin_customizer_optin_campaign_MailChimpConnect_selection_type', 'checkbox'),
            'field_label'            => apply_filters('mailoptin_customizer_optin_campaign_MailChimpConnect_user_input_field_label', __('Select Your Group', 'mailoptin')),
            'segment_area_font'      => apply_filters('mailoptin_customizer_optin_campaign_MailChimpConnect_user_input_segment_area_font', 'Open+Sans'),
            'display_alignment'      => apply_filters('mailoptin_customizer_optin_campaign_MailChimpConnect_segment_display_alignment', $this->default_display_align($optin_type)),
            'field_color'            => apply_filters('mailoptin_customizer_optin_campaign_MailChimpConnect_user_input_field_color', '#60656f'),
            'display_style'          => apply_filters('mailoptin_customizer_optin_campaign_MailChimpConnect_segment_display_style', $this->default_display_style($optin_type)),
            'interests'              => apply_filters('mailoptin_customizer_optin_campaign_MailChimpConnect_interests', []),
            'segment_required'       => apply_filters('mailoptin_customizer_optin_campaign_MailChimpConnect_segment_required', true),
            'segment_required_error' => apply_filters('mailoptin_customizer_optin_campaign_MailChimpConnect_segment_required_error', __('You did not select any group', 'mailoptin'))
        ];
    }

    /**
     * @param array $settings
     * @param CustomizerSettings $customizerSettings
     *
     * @return mixed
     */
    public function integration_customizer_settings($settings, $all_default_settings, $optin_type)
    {
        $default_values = $this->default_segmentation_values($optin_type);

        $settings['MailChimpConnect_disable_double_optin'] = apply_filters('mailoptin_customizer_optin_campaign_MailChimpConnect_disable_double_optin', false);

        $settings['MailChimpConnect_group_segment_type'] = $default_values['segment_type'];

        $settings['MailChimpConnect_selection_type'] = $default_values['selection_type'];

        $settings['MailChimpConnect_user_input_field_label'] = $default_values['field_label'];

        $settings['MailChimpConnect_interests'] = $default_values['interests'];

        $settings['MailChimpConnect_user_input_field_color'] = $default_values['field_color'];

        $settings['MailChimpConnect_user_input_segment_area_font'] = $default_values['segment_area_font'];

        $settings['MailChimpConnect_segment_display_style'] = $default_values['display_style'];

        $settings['MailChimpConnect_segment_display_alignment'] = $default_values['display_alignment'];

        $settings['MailChimpConnect_segment_required'] = $default_values['segment_required'];

        $settings['MailChimpConnect_segment_required_error'] = $default_values['segment_required_error'];

        $settings['MailChimpConnect_lead_tags'] = apply_filters('mailoptin_customizer_optin_campaign_MailChimpConnect_lead_tags', '');

        return $settings;
    }

    /**
     * @param $controls
     * @param $optin_campaign_id
     * @param $index
     * @param $saved_values
     *
     * @return array
     */
    public function integration_customizer_controls($controls, $optin_campaign_id, $index, $saved_values)
    {
        if (defined('MAILOPTIN_DETACH_LIBSODIUM') === true) {

            $controls[] = [
                'field'       => 'text',
                'name'        => 'MailChimpConnect_lead_tags',
                'label'       => __('Tags', 'mailoptin'),
                'placeholder' => 'tag1, tag2',
                'description' => __('Comma-separated list of tags to assign to a new subscriber in MailChimp', 'mailoptin'),
            ];

            // always prefix with the name of the connect/connection service.
            $controls[] = [
                'field'       => 'toggle',
                'name'        => 'MailChimpConnect_disable_double_optin',
                'label'       => __('Disable Double Optin', 'mailoptin'),
                'description' => __("Double optin requires users to confirm their email address before they are added or subscribed (recommended).", 'mailoptin')
            ];

            $controls[] = [
                'field'   => 'custom_content',
                'content' => '<div class="MailChimpConnect_group_header mc-group-block" style="background:#0085ba;color:#fff;font-weight:bold;padding:10px;font-size:14px;width: 100%;">' . __("Group Segmentation", 'mailoptin') . '</div>'
            ];

            $controls[] = [
                'field'       => 'select',
                'name'        => 'MailChimpConnect_group_segment_type',
                'choices'     => [
                    'automatic'  => esc_html__('Automatic', 'mailoptin'),
                    'user_input' => esc_html__('User Input', 'mailoptin'),
                ],
                'class'       => 'mc-group-block',
                'label'       => __('Segmentation Method', 'mailoptin'),
                'description' => esc_html__('Select how Segmentation is performed.', 'mailoptin')
            ];

            $controls[] = [
                'field'       => 'select',
                'name'        => 'MailChimpConnect_selection_type',
                'choices'     => [
                    'checkbox' => __('Checkboxes', 'mailoptin'),
                    'radio'    => __('Radio Buttons', 'mailoptin')
                ],
                'class'       => 'mc-group-block',
                'label'       => __('Choice Type', 'mailoptin'),
                'description' => esc_html__('Choose how users will select groups to subscribe to.', 'mailoptin')
            ];

            $interest_groups = [];
            if (isset($index)) {
                $list_id         = isset($saved_values[$index]['connection_email_list']) ? $saved_values[$index]['connection_email_list'] : '';
                $interest_groups = $this->get_group_interests($list_id);
            }

            if (empty($interest_groups)) {
                $controls[] = [
                    'field'   => 'custom_content',
                    'content' => '<div class="MailChimpConnect_interests mc-group-block" style="background:#000000;color:#fff;margin:5px 0;padding:10px;font-size:14px;">' . __('No MailChimp group found. Try selecting another email list.', 'mailoptin') . '</div>'
                ];
            } else {

                $controls[] = [
                    'field'   => 'mc_group_select',
                    'name'    => 'MailChimpConnect_interests',
                    'choices' => $interest_groups,
                    'class'   => 'mc-group-block'
                ];
            }

            $controls[] = [
                'field' => 'text',
                'name'  => 'MailChimpConnect_user_input_field_label',
                'class' => 'mc-group-block',
                'label' => __('Field Label', 'mailoptin')
            ];

            $controls[] = [
                'field' => 'color',
                'name'  => 'MailChimpConnect_user_input_field_color',
                'class' => 'mc-group-block',
                'label' => __('Field Color', 'mailoptin')
            ];

            $controls[] = [
                'field' => 'font',
                'name'  => 'MailChimpConnect_user_input_segment_area_font',
                'class' => 'mc-group-block',
                'label' => __('Field Font', 'mailoptin')
            ];

            $controls[] = [
                'field'   => 'select',
                'name'    => 'MailChimpConnect_segment_display_style',
                'choices' => [
                    'inline' => __('Inline', 'mailoptin'),
                    'block'  => __('Block', 'mailoptin')
                ],
                'class'   => 'mc-group-block',
                'label'   => __('Display Style', 'mailoptin')
            ];

            $controls[] = [
                'field'   => 'select',
                'name'    => 'MailChimpConnect_segment_display_alignment',
                'choices' => [
                    'left'   => __('Left', 'mailoptin'),
                    'center' => __('Center', 'mailoptin'),
                    'right'  => __('Right', 'mailoptin')
                ],
                'class'   => 'mc-group-block',
                'label'   => __('Display Alignment', 'mailoptin')
            ];

            $controls[] = [
                'field'       => 'toggle',
                'name'        => 'MailChimpConnect_segment_required',
                'class'       => 'mc-group-block',
                'label'       => __('Group Selection Required?', 'mailoptin'),
                'description' => __("Toggle ON if you want visitors to select at least one group before they are subscribed.", 'mailoptin')
            ];

            $controls[] = [
                'field'       => 'text',
                'name'        => 'MailChimpConnect_segment_required_error',
                'class'       => 'mc-group-block',
                'label'       => __('Group Selection Error', 'mailoptin'),
                'description' => __("Error message displayed when a visitor failed to select at least a group.", 'mailoptin')
            ];

            return $controls;
        }
    }

    public function get_groups($list_id, $interest_category_id)
    {
        try {
            $response = $this->mc_list_instance()->getInterests(
                $list_id,
                $interest_category_id,
                ['count' => 100, 'fields' => 'interests.id,interests.name']
            );

            if (isset($response->interests) && is_array($response->interests)) return $response->interests;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    public function get_group_interests($list_id)
    {
        if (is_null($list_id) || empty($list_id) || ! $list_id) return [];

        try {
            $output = get_transient("mo_mailchimp_get_group_interests_$list_id");

            if ($output === false) {

                $output = [];

                $response = $this->mc_list_instance()->getInterestCategories($list_id, ['count' => 100, 'fields' => 'categories.id,categories.title']);

                if (isset($response->categories) && is_array($response->categories)) {
                    $index = 0;
                    foreach ($response->categories as $category) {
                        $output[$index]['id']        = $category->id;
                        $output[$index]['title']     = $category->title;
                        $output[$index]['interests'] = $this->get_groups($list_id, $category->id);
                        $index++;
                    }
                }

                $output = json_encode($output);

                set_transient("mo_mailchimp_get_group_interests_$list_id", $output, MINUTE_IN_SECONDS);
            }

            return json_decode($output, true);

        } catch (\Exception $e) {
            self::save_optin_error_log($e->getMessage(), 'mailchimp');

            return [];
        }
    }

    public function get_list_segments($list_id)
    {
        if (is_null($list_id) || empty($list_id) || ! $list_id) return [];

        try {
            $output = get_transient("mo_mailchimp_get_list_segment_$list_id");


            if ($output === false) {

                $output = ['' => __('Select...', 'mailoptin')];

                $response = $this->mc_list_instance()->getSegments($list_id, ['count' => 100, 'fields' => 'segments.id,segments.name']);

                if (isset($response->segments) && is_array($response->segments)) {
                    foreach ($response->segments as $segment) {
                        $output[$segment->id] = $segment->name;
                    }
                }

                $output = json_encode($output);

                set_transient("mo_mailchimp_get_list_segment_$list_id", $output, MINUTE_IN_SECONDS);
            }

            return json_decode($output, true);

        } catch (\Exception $e) {
            self::save_optin_error_log($e->getMessage(), 'mailchimp');

            return [];
        }
    }

    public function add_mailchimp_interest_shortcode($optin_campaign_id)
    {
        add_shortcode('mo-mailchimp-interests', function () use ($optin_campaign_id) {

            if ( ! defined('MAILOPTIN_DETACH_LIBSODIUM')) return '';

            // bail if this is a CTA button only
            if (OptinCampaignsRepository::get_customizer_value($optin_campaign_id, 'display_only_button')) return '';

            $integrations_data = json_decode(
                OptinCampaignsRepository::get_customizer_value($optin_campaign_id, 'integrations', ''),
                true
            );

            if ( ! is_array($integrations_data)) return '';

            $optin_type  = OptinCampaignsRepository::get_optin_campaign_type($optin_campaign_id);
            $default_val = $this->default_segmentation_values($optin_type);

            $interest_found_flag = false;

            $content = '';

            foreach ($integrations_data as $integration_data) {
                if ($interest_found_flag === true) break;

                $segment_type = $this->get_integration_data('MailChimpConnect_group_segment_type', $integration_data, $default_val['segment_type']);

                if ($segment_type == 'automatic') continue;

                $connection_service = $this->get_integration_data('connection_service', $integration_data);

                if ($connection_service !== 'MailChimpConnect') continue;

                $connection_email_list = $this->get_integration_data('connection_email_list', $integration_data);
                $mc_get_email_list     = is_array(self::get_email_list()) ? self::get_email_list() : [];

                if (empty($connection_email_list) || ! in_array($connection_email_list, array_keys($mc_get_email_list))) {
                    continue;
                }

                $mc_list_groups = self::get_group_interests($connection_email_list);
                $choices        = $this->get_integration_data('MailChimpConnect_interests', $integration_data, []);

                $label             = $this->get_integration_data('MailChimpConnect_user_input_field_label', $integration_data, $default_val['field_label']);
                $selection_type    = $this->get_integration_data('MailChimpConnect_selection_type', $integration_data, $default_val['selection_type']);
                $display_style     = $this->get_integration_data('MailChimpConnect_segment_display_style', $integration_data, $default_val['display_style']);
                $display_alignment = $this->get_integration_data('MailChimpConnect_segment_display_alignment', $integration_data, $default_val['display_alignment']);
                $field_color       = $this->get_integration_data('MailChimpConnect_user_input_field_color', $integration_data, $default_val['field_color']);
                $field_font        = AbstractOptinForm::_remove_web_safe_font($this->get_integration_data('MailChimpConnect_user_input_segment_area_font', $integration_data, $default_val['segment_area_font']));


                if (is_array($choices) && ! empty($choices)) {

                    // get all interests ID in the list.
                    $mc_list_groups_keys = array_reduce($mc_list_groups, function ($carry, $item) {
                        if (is_array($item['interests']) && ! empty($item['interests'])) {
                            $carry[] = array_reduce($item['interests'], function ($carry, $item2) {
                                $carry[] = $item2['id'];

                                return $carry;
                            });
                        }

                        return $carry;
                    }, []);

                    // flatten the multi-dimensional array.
                    $mc_list_groups_keys = \MailOptin\Core\array_flatten($mc_list_groups_keys);

                    // find an intersection otherwise the interests selected in customizer
                    // doesn't belong to the saved list interests.
                    $result = array_intersect(array_keys($choices), $mc_list_groups_keys);

                    if (empty($result)) continue;

                    $interest_found_flag = true;
                    $style               = "text-align:$display_alignment;color:$field_color;";
                    if ($field_font != 'inherit') {
                        $style .= "font-family: \"$field_font\"";
                    }

                    $content = "<div class='mo-mailchimp-interest-container' style='$style'>";
                    $content .= '<div class="mo-mailchimp-interest-label">' . $label . '</div>';

                    foreach ($choices as $key => $value) {
                        $content .= "<div class='mo-mailchimp-interest-choice-container' style='display:$display_style;'>";
                        $content .= '<label>';
                        $content .= "<input type='$selection_type' class='mo-mailchimp-interest-choice' name='mo-mailchimp-interests[]' value='$key'/>";
                        $content .= "<span class='mo-mailchimp-choice-label'>$value</span>";
                        $content .= '</label>';
                        $content .= '</div>';
                    }

                    $content .= '</div>';
                }
            }

            return $content;
        });
    }

    public function include_segment_field_font($fonts, $optin_campaign_id)
    {
        $integrations_data = json_decode(
            OptinCampaignsRepository::get_customizer_value($optin_campaign_id, 'integrations', ''),
            true
        );

        if (is_array($integrations_data) && ! empty($integrations_data)) {

            foreach ($integrations_data as $integration_data) {
                $font         = $this->get_integration_data('MailChimpConnect_user_input_segment_area_font', $integration_data, 'Open+Sans');
                $segment_type = $this->get_integration_data('MailChimpConnect_group_segment_type', $integration_data, 'automatic');
                if ($segment_type == 'user_input' && $font != 'inherit') {
                    $segment_field_font = AbstractOptinForm::_remove_web_safe_font($font);
                    if ( ! empty($segment_field_font)) {
                        $fonts[] = "'$segment_field_font'";
                    }
                }
            }
        }

        return $fonts;
    }

    public function css_style_segment_area($global_css, $optin_campaign_uuid, $optin_css_id)
    {
        $global_css .= "div#$optin_campaign_uuid .mo-mailchimp-interest-container {
                            margin: 0 10px 2px;
        }
        
        div#$optin_campaign_uuid .mo-mailchimp-interest-label {
                           font-size: 16px;
                           margin: 5px 0 2px;
        }
        
        div#$optin_campaign_uuid input.mo-mailchimp-interest-choice {
                           line-height: normal;
                            border: 0;
                            margin: 0 5px;
        }
        
        div#$optin_campaign_uuid span.mo-mailchimp-choice-label {
                           vertical-align: middle;
                           font-size: 14px;
        }
        
        div#$optin_campaign_uuid .mo-mailchimp-interest-choice-container {
                           margin: 5px 0;
         }
        ";

        return $global_css;
    }


    /**
     * Keep in sync with WP_Customize_Integration_Repeater_Control::mc_group_select
     *
     * @param $name
     * @param $choices
     * @param string $class
     */
    public static function mc_group_select($name, $choices, $class = '')
    {
        if ( ! empty($class)) {
            $class = " $class";
        }

        echo "<div class=\"$name mo-integration-block{$class}\">";

        if (empty($choices)) {
            echo '<div style="background:#000000;color:#fff;padding:10px;font-size:14px;">' . __('No Mailchimp group found. Try selecting another email list.', 'mailoptin') . '</div>';

            return;
        }

        foreach ($choices as $choice) : ?>
            <div>
                <span class="customize-control-title"><?= $choice['title']; ?></span>
                <?php foreach ($choice['interests'] as $interests) : ?>
                    <div>
                        <label>
                            <input type="checkbox" class="mo_mc_interest" name="<?= $name; ?>[]" value="<?= $interests['id']; ?>">
                            <span class="mo_mc_interest_label"><?= $interests['name']; ?></span>
                        </label>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endforeach;
        echo '</div>';
    }

    /**
     * Fetch Mailchimp groups/interest of a list.
     */
    public function customizer_fetch_mailchimp_groups()
    {
        check_ajax_referer('customizer-fetch-email-list', 'security');

        \MailOptin\Core\current_user_has_privilege() || exit;

        $list_id = sanitize_text_field($_REQUEST['list_id']);

        $interests = $this->get_group_interests($list_id);

        self::mc_group_select(
            'MailChimpConnect_interests',
            $interests,
            'mc-group-block'
        );

        $structure = ob_get_clean();

        $response = ['structure' => $structure, 'interests' => $interests];

        wp_send_json_success($response);

        wp_die();
    }

    /**
     * Fetch Mailchimp groups/interest of a list for Email Customizer.
     */
    public function customizer_fetch_mailchimp_segment()
    {
        check_ajax_referer('customizer-fetch-email-list', 'security');

        \MailOptin\Core\current_user_has_privilege() || exit;

        $list_id = sanitize_text_field($_REQUEST['list_id']);

        $segments = $this->get_list_segments($list_id);

        if (count($segments) > 1) {
            foreach ($segments as $key => $value) {
                echo '<option value="' . esc_attr($key) . '">' . $value . '</option>';
            }
        }

        $structure = ob_get_clean();

        wp_send_json_success($structure);

        wp_die();
    }

    /**
     * @param array $js_config
     * @param AbstractOptinForm $abstractOptinFormClass
     *
     * @return mixed
     */
    public function add_values_js_config($js_config, $abstractOptinFormClass)
    {
        $integrations_data = $abstractOptinFormClass->get_customizer_value('integrations');

        if (is_string($integrations_data)) {
            $integrations_data = json_decode($integrations_data, true);

            if (is_array($integrations_data)) {

                $default_values = $this->default_segmentation_values($abstractOptinFormClass->optin_campaign_type);

                $interest_found_flag = false;

                foreach ($integrations_data as $integration_data) {
                    if ($interest_found_flag === true) break;

                    $segment_type       = $this->get_integration_data('MailChimpConnect_group_segment_type', $integration_data, $default_values['segment_type']);
                    $connection_service = $this->get_integration_data('connection_service', $integration_data);

                    if ($connection_service !== 'MailChimpConnect') continue;
                    if ($segment_type == 'automatic') continue;


                    $choices = $this->get_integration_data('MailChimpConnect_interests', $integration_data, []);

                    if (is_array($choices) && ! empty($choices)) $interest_found_flag = true;

                    $error_msg        = $this->get_integration_data('MailChimpConnect_segment_required_error', $integration_data, $default_values['segment_required_error']);
                    $segment_required = $this->get_integration_data('MailChimpConnect_segment_required', $integration_data, $default_values['segment_required']);

                    $js_config['mailchimp_segment_required']       = $segment_required;
                    $js_config['mailchimp_segment_required_error'] = $error_msg;
                }
            }
        }

        return $js_config;
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