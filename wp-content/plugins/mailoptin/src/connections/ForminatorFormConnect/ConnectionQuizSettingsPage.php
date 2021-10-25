<?php

namespace MailOptin\ForminatorFormConnect;

use Forminator_Addon_Quiz_Settings_Abstract;
use Forminator_Addon_Abstract;
use Forminator_Addon_Exception;
use MailOptin\Connections\Init;
use MailOptin\Core\Connections\ConnectionFactory;

class ConnectionQuizSettingsPage extends Forminator_Addon_Quiz_Settings_Abstract
{

    /**
     * MailOptin Addon
     * @var FFMailOptin
     */
    protected $addon;

    /**
     * QuizConnectionSettingsPage constructor.
     *
     * MailOptin Addon
     *
     * @param Forminator_Addon_Abstract $addon
     * @param                           $quiz_id
     *
     * @throws Forminator_Addon_Exception
     */
    public function __construct(Forminator_Addon_Abstract $addon, $quiz_id)
    {
        parent::__construct($addon, $quiz_id);

        $this->_update_quiz_settings_error_message = __(
            'The update to your settings for this quiz failed, check the quiz input and try again.',
            'mailoptin'
        );
    }

    /**
     * For settings Wizard steps
     *
     * MailOptin Addon
     * @return array
     */
    public function quiz_settings_wizards()
    {
        return array(
            // 0
            array(
                'callback'     => array($this, 'choose_email_provider'),
                'is_completed' => array($this, 'step_choose_email_provider_is_completed'),
            ),
            //1
            array(
                'callback'     => array($this, 'choose_email_lists'),
                'is_completed' => array($this, 'step_choose_email_lists_is_completed'),
            ),
            // 1
            array(
                'callback'     => array($this, 'support_tags_map_fields'),
                'is_completed' => array($this, 'step_support_tags_map_fields_is_completed'),
            ),
        );
    }


    public function choose_email_provider($submitted_data)
    {
        $this->addon_quiz_settings = $this->get_quiz_settings_values();
        $connected_email_providers = '';

        if (isset($submitted_data['connected_email_providers'])) {
            $connected_email_providers = $submitted_data['connected_email_providers'];
        } elseif (isset($this->addon_quiz_settings['connected_email_providers'])) {
            $connected_email_providers = $this->addon_quiz_settings['connected_email_providers'];
        }

        forminator_addon_maybe_log(__METHOD__, 'current_data', $connected_email_providers);

        $is_submit = ! empty($submitted_data);

        $error_message        = '';
        $input_error_messages = [];

        $html_select_mail_list = '';
        $html_field_mail_list  = '';

        try {

            $email_service_providers = FFMailOptin::get_instance()->email_service_providers();

            $html_select_mail_list .= '<label for="moffSelectIntegration">' . esc_html__('Select Integration', 'mailoptin') . '</label>';
            $html_select_mail_list .= '<select name="connected_email_providers" class="sui-select sui-form-control" id="moffSelectIntegration">';
            foreach ($email_service_providers as $key => $value) {
                $html_select_mail_list .= '<option value="' . $key . '"' . selected($key, $connected_email_providers, false) . '>' . $value . '</option>';
            }
            $html_select_mail_list .= "</select>";

            //handles submission here:
            if ($is_submit) {
                forminator_addon_maybe_log(__METHOD__, '$submitted_data', $submitted_data);

                $this->addon_quiz_settings['connected_email_providers'] = $submitted_data['connected_email_providers'];

                $this->save_quiz_settings_values($this->addon_quiz_settings);
            }

            $html_field_mail_list = '<div class="sui-form-field">' . $html_select_mail_list . '</div>';


        } catch (\Exception $e) {
            $error_message = '<div class="sui-notice sui-notice-error"><p>' . $e->getMessage() . '</p></div>';
        }

        $buttons = [];
        if ($this->addon->is_quiz_connected($this->quiz_id)) {
            $buttons['disconnect']['markup'] = FFMailOptin::get_button_markup(
                esc_html__('Deactivate', 'mailoptin'),
                'sui-button-ghost sui-tooltip sui-tooltip-top-center forminator-addon-form-disconnect',
                esc_html__('Deactivate MailOptin from this Quiz.', 'mailoptin')
            );
        }

        $buttons['next']['markup'] = '<div class="sui-actions-right">' .
                                     FFMailOptin::get_button_markup(esc_html__('Next', 'mailoptin'), 'forminator-addon-next') .
                                     '</div>';

        return [
            'html'       => '<div class="sui-box-content integration-header"><h3 class="sui-box-title" id="dialogTitle2">' . __('Choose Integration', 'mailoptin') . '</h3>
                               <span class="sui-description" style="margin-top: 20px;">' . __('Select the integration to set up.', 'mailoptin') . '</span>
                               ' . $error_message . '</div>
							<form enctype="multipart/form-data">
							' . $html_field_mail_list . ConnectionFormSettingsPage::upsell_block() . '
							</form>
                            </div>',
            'redirect'   => false,
            'buttons'    => $buttons,
            'has_errors' => ( ! empty($error_message) || ! empty($input_error_messages)),
            'size'       => 'small',
        ];

    }

    public function step_choose_email_provider_is_completed()
    {
        $this->addon_quiz_settings = $this->get_quiz_settings_values();

        if ( ! isset($this->addon_quiz_settings['connected_email_providers'])) {
            $this->addon_quiz_settings['connected_email_providers'] = [];

            return false;
        }

        if (empty($this->addon_quiz_settings['connected_email_providers'])) {
            $this->addon_quiz_settings['connected_email_providers'] = [];

            return false;
        }

        return true;
    }

    public function choose_email_lists($submitted_data)
    {
        $this->addon_quiz_settings = $this->get_quiz_settings_values();
        $connected_email_providers = $this->addon_quiz_settings['connected_email_providers'];
        $connected_lists           = '';
        $connected_tags            = [];
        $is_double_optin           = false;


        if (isset($submitted_data[$connected_email_providers]['lists'])) {
            $connected_lists = $submitted_data[$connected_email_providers]['lists'];
        } elseif ( ! empty($this->addon_quiz_settings[$connected_email_providers]['lists'])) {
            $connected_lists = $this->addon_quiz_settings[$connected_email_providers]['lists'];
        }

        if(isset($submitted_data[$connected_email_providers]['double_optin'])) {
            $is_double_optin = $submitted_data[$connected_email_providers]['double_optin'];
        } elseif(! empty($this->addon_quiz_settings[$connected_email_providers]['double_optin'])) {
            $is_double_optin = $this->addon_quiz_settings[$connected_email_providers]['double_optin'];
        }

        if (isset($submitted_data[$connected_email_providers]['tags'])) {
            $connected_tags = $submitted_data[$connected_email_providers]['tags'];
        } elseif ( ! empty($this->addon_quiz_settings[$connected_email_providers]['tags'])) {
            $connected_tags = $this->addon_quiz_settings[$connected_email_providers]['tags'];
        }

        $is_submit            = ! empty($submitted_data);
        $error_message        = '';
        $html_select_list     = '';
        $html_field_list      = '';
        $input_error_messages = array();

        try {
            if ( ! empty($connected_email_providers)) {

                $lists = [];
                if ( ! empty($connected_email_providers) && $connected_email_providers != 'leadbank') {
                    $lists = ConnectionFactory::make($connected_email_providers)->get_email_list();
                }

                $tags = [];
                if ( ! empty($connected_email_providers) && in_array($connected_email_providers, Init::select2_tag_connections())) {
                    $instance = ConnectionFactory::make($connected_email_providers);
                    if (method_exists($instance, 'get_tags')) {
                        $tags = $instance->get_tags();
                    }
                }

                if ( ! empty($lists)) {
                    $html_select_list .= '<div style="margin-bottom: 30px;">';
                    $html_select_list .= '<label for="' . $connected_email_providers . '">' . esc_html__('Select List', 'mailoptin') . '</label>';
                    $html_select_list .= '<select name="' . $connected_email_providers . '[lists]" class="sui-select sui-form-control" id="' . $connected_email_providers . '[lists]">';
                    $html_select_list .= '<option value="">' . esc_html__('Select...', 'mailoptin') . '</option>';
                    foreach ($lists as $key => $value) {
                        $html_select_list .= '<option value="' . $key . '"' . selected($key, $connected_lists, false) . '>' . $value . '</option>';
                    }
                    $html_select_list .= "</select></div>";
                }

                if(defined('MAILOPTIN_DETACH_LIBSODIUM') && in_array($connected_email_providers, Init::double_optin_support_connections(true))) {
                    $default_double_optin = false;
                    $double_optin_connections = Init::double_optin_support_connections();
                    foreach($double_optin_connections as $key => $value) {
                        if($connected_email_providers === $key) {
                            $default_double_optin = $value;
                        }
                    }

                    $double_optin_status = esc_html__('Enable Double Optin', 'mailoptin');
                    if($default_double_optin) {
                        $double_optin_status = esc_html__('Disable Double Optin', 'mailoptin');
                    }

                    $html_select_list .= '<div style="margin-top: 30px;">';
                    $html_select_list .= '<label for="moffDoubleOptin">'. $double_optin_status . '</label>';
                    $html_select_list .= '<input id="moffDoubleOptin" type="checkbox" value="true" name="'. $connected_email_providers . '[double_optin]" class="sui-checkbox"  '. checked($is_double_optin, "true", false). ' style="margin-left: 30px" />';
                    $html_select_list .= "</div>";
                }

                if (defined('MAILOPTIN_DETACH_LIBSODIUM') && in_array($connected_email_providers, Init::select2_tag_connections())) {
                    if (is_array($tags) && ! empty($tags)) {
                        $html_select_list .= '<div style="margin-top: 30px;">';
                        $html_select_list .= '<label for="moffSelectTags">' . esc_html__('Select Tags', 'mailoptin') . '</label>';
                        $html_select_list .= '<select name="' . $connected_email_providers . '[tags][]" class="sui-select sui-multiselect sui-form-control" id="moffSelectTags" multiple>';
                        foreach ($tags as $key => $value) {
                            $selected         = @in_array($key, $connected_tags) ? 'selected' : '';
                            $html_select_list .= '<option value="' . $key . '" ' . $selected . '>' . $value . '</option>';
                        }
                        $html_select_list .= '</select>';
                        $html_select_list .= "</div>";
                    }
                }

                if (defined('MAILOPTIN_DETACH_LIBSODIUM') && in_array($connected_email_providers, Init::text_tag_connections())) {
                    $tags = ! empty($connected_tags) && ! is_array($connected_tags) ? $connected_tags : '';

                    $html_select_list .= '<div style="margin-top: 30px;">';
                    $html_select_list .= '<label for="moffSelectTags">' . esc_html__('Tags (Enter a comma-separated list of tags to assign to subscribers.)', 'mailoptin') . '</label>';
                    $html_select_list .= '<input type="text" class="sui-form-control" placeholder="tag1, tag2" id="moffSelectTags" name="' . $connected_email_providers . '[tags]" value="' . $tags . '">';
                    $html_select_list .= "</div>";
                }

                //handles submission here:
                if ($is_submit) {
                    forminator_addon_maybe_log(__METHOD__, '$submitted_data', $submitted_data);
                    if (isset($submitted_data[$connected_email_providers]['lists'])) {
                        $this->addon_quiz_settings[$connected_email_providers]['lists'] = $submitted_data[$connected_email_providers]['lists'];
                    }

                    if (isset($submitted_data[$connected_email_providers]['double_optin'])) {
                        $this->addon_quiz_settings[$connected_email_providers]['double_optin'] = $submitted_data[$connected_email_providers]['double_optin'];
                    }

                    if (isset($submitted_data[$connected_email_providers]['tags'])) {
                        $this->addon_quiz_settings[$connected_email_providers]['tags'] = $submitted_data[$connected_email_providers]['tags'];
                    }

                    $this->save_quiz_settings_values($this->addon_quiz_settings);
                }

                $html_field_list = '<div class="sui-form-field">' . $html_select_list . '</div>';

            }
        } catch (\Exception $e) {
            $error_message = '<div class="sui-notice sui-notice-error"><p>' . $e->getMessage() . '</p></div>';
        }


        $buttons = [];
        if ($this->addon->is_quiz_connected($this->quiz_id)) {
            $buttons['disconnect']['markup'] = FFMailOptin::get_button_markup(
                esc_html__('Deactivate', 'mailoptin'),
                'sui-button-ghost sui-tooltip sui-tooltip-top-center forminator-addon-form-disconnect',
                esc_html__('Deactivate MailOptin from this Quiz.', 'mailoptin')
            );
        }

        $buttons['next']['markup'] = '<div class="sui-actions-right">' .
                                     FFMailOptin::get_button_markup(esc_html__('Next', 'mailoptin'), 'sui-button-primary forminator-addon-finish') .
                                     '</div>';

        return [
            'html'       => '<div class="sui-box-content integration-header"><h3 class="sui-box-title" id="dialogTitle2">' . __('Setup Integration', 'mailoptin') . '</h3>
                               <span class="sui-description" style="margin-top: 20px;">' . __('Choose the list you want to send form data to.', 'mailoptin') . '</span>
                               ' . $error_message . '</div>
							<form enctype="multipart/form-data">
							' . $html_field_list . ConnectionFormSettingsPage::upsell_block() . '
							</form>
                            </div>',
            'redirect'   => false,
            'buttons'    => $buttons,
            'has_errors' => ( ! empty($error_message) || ! empty($input_error_messages)),
            'size'       => 'small',
            'has_back'   => true,
        ];
    }

    public function step_choose_email_lists_is_completed()
    {
        $this->addon_quiz_settings = $this->get_quiz_settings_values();
        $connected_email_providers = $this->addon_quiz_settings['connected_email_providers'];

        if ( ! empty($connected_email_providers) && $connected_email_providers != 'leadbank') {
            if ( ! isset($this->addon_quiz_settings[$connected_email_providers]['lists'])) {
                $this->addon_quiz_settings[$connected_email_providers]['lists'] = [];

                return false;
            }

            if (empty($this->addon_quiz_settings[$connected_email_providers]['lists'])) {
                $this->addon_quiz_settings[$connected_email_providers]['lists'] = [];

                return false;
            }
        }

        return true;
    }

    public function support_tags_map_fields($submitted_data)
    {
        $this->addon_quiz_settings = $this->get_quiz_settings_values();
        $is_close                  = false;

        $connected_lists = '';
        $connected_tags  = [];

        $is_submit             = ! empty($submitted_data);
        $error_message         = '';
        $html_input_map_fields = '';
        $input_error_messages  = array();

        $connected_email_providers = $this->addon_quiz_settings['connected_email_providers'];
        if (isset($this->addon_quiz_settings[$connected_email_providers]['lists'])) {
            $connected_lists = $this->addon_quiz_settings[$connected_email_providers]['lists'];
        }

        if (isset($this->addon_quiz_settings[$connected_email_providers]['tags'])) {
            $connected_tags = $this->addon_quiz_settings[$connected_email_providers]['tags'];
        }

        try {

            $instance = ConnectionFactory::make($connected_email_providers);

            $custom_fields = [
                'moEmail'     => esc_html__('Email Address', 'mailoptin'),
                'moName'      => esc_html__('Full Name', 'mailoptin'),
                'moFirstName' => esc_html__('First Name', 'mailoptin'),
                'moLastName'  => esc_html__('Last Name', 'mailoptin'),
            ];

            if (in_array($connected_email_providers, Init::no_name_mapping_connections())) {
                unset($custom_fields['moName']);
                unset($custom_fields['moFirstName']);
                unset($custom_fields['moLastName']);
            }

            if ( ! empty($connected_email_providers) && $connected_email_providers != 'leadbank') {
                if (defined('MAILOPTIN_DETACH_LIBSODIUM')) {
                    if (in_array($instance::OPTIN_CUSTOM_FIELD_SUPPORT, $instance::features_support())) {
                        $cfields = $instance->get_optin_fields($connected_lists);
                        if (is_array($cfields) && ! empty($cfields)) {
                            $custom_fields += $cfields;
                        }
                    }
                }
            }

            $current_data = ['fields_map' => []];
            foreach ($custom_fields as $key => $value) {
                $current_data['fields_map'][$key] = '';
            }


            //find type of email
            $email_fields = array();
            foreach ($this->form_fields as $form_field) {
                if ('email' === $form_field['type']) {
                    $email_fields[] = $form_field;
                    unset($custom_fields['moEmail']);
                }
            }

            //Email
            $current_data['fields_map']['moEmail'] = '';
            if (isset($submitted_data['fields_map']['moEmail'])) {
                $current_data['fields_map']['moEmail'] = $submitted_data['fields_map']['moEmail'];
            } elseif (isset($this->addon_quiz_settings['fields_map']['moEmail'])) {
                $current_data['fields_map']['moEmail'] = $this->addon_quiz_settings['fields_map']['moEmail'];
            }

            foreach ($current_data['fields_map'] as $key => $current_field) {
                if (isset($submitted_data['fields_map'][$key])) {
                    $current_data['fields_map'][$key] = $submitted_data['fields_map'][$key];
                } elseif (isset($this->addon_quiz_settings['fields_map'][$key])) {
                    $current_data['fields_map'][$key] = $this->addon_quiz_settings['fields_map'][$key];
                }
            }

            /** Build table map fields input */
            ob_start();
            $this->get_input_map_fields($email_fields, $custom_fields, $current_data);
            $html_input_map_fields = ob_get_clean();


            //if submission
            if ($is_submit) {
                $this->addon_quiz_settings['fields_map'] = $submitted_data['fields_map'];
                $this->save_quiz_settings_values($this->addon_quiz_settings);
                $is_close = true;
            }
        } catch (\Exception $e) {
            $error_message = '<div class="sui-notice sui-notice-error"><p>' . $e->getMessage() . '</p></div>';
        }

        //cleanup map fields input markup placeholder
        if ( ! empty($html_input_map_fields)) {
            $replaced_html_input_map_fields = $html_input_map_fields;
            $replaced_html_input_map_fields = preg_replace('/\{\{\$error_css_class_(.+)\}\}/', '', $replaced_html_input_map_fields);
            $replaced_html_input_map_fields = preg_replace('/\{\{\$error_message_(.+)\}\}/', '', $replaced_html_input_map_fields);
            if ( ! is_null($replaced_html_input_map_fields)) {
                $html_input_map_fields = $replaced_html_input_map_fields;
            }
        }

        $buttons = [];
        if ($this->addon->is_quiz_connected($this->quiz_id)) {
            $buttons['disconnect']['markup'] = FFMailOptin::get_button_markup(
                esc_html__('Deactivate', 'mailoptin'),
                'sui-button-ghost sui-tooltip sui-tooltip-top-center forminator-addon-form-disconnect',
                esc_html__('Deactivate MailOptin from this Quiz.', 'mailoptin')
            );
        }


        $buttons['next']['markup'] = '<div class="sui-actions-right">' .
                                     FFMailOptin::get_button_markup(esc_html__('Save', 'mailoptin'), 'sui-button-primary forminator-addon-finish') .
                                     '</div>';

        $notification = [];

        if ($is_submit && empty($error_message) && empty($input_error_messages)) {
            $notification = array(
                'type' => 'success',
                'text' => '<strong>' . $this->addon->get_title() . '</strong> ' . __('is activated successfully.'),
            );
        }


        return [
            'html'         => '<div class="sui-box-content integration-header"><h3 class="sui-box-title" id="dialogTitle2">' . __('Assign Fields', 'mailoptin') . '</h3>
							<span class="sui-description" style="margin-top: 20px;">' . __('Lastly, match up your form fields with your campaign fields to make sure we\'re sending data to the right place.', 'mailoptin') . '</span>
							' . $error_message . '</div>
							<form enctype="multipart/form-data">
								' . $html_input_map_fields . ConnectionFormSettingsPage::upsell_block() . '
							</form>',
            'redirect'     => false,
            'is_close'     => $is_close,
            'buttons'      => $buttons,
            'has_errors'   => ! empty($error_message) || ! empty($input_error_messages),
            'notification' => $notification,
            'size'         => 'normal',
            'has_back'     => true,
        ];
    }

    public function step_support_tags_map_fields_is_completed()
    {
        $this->addon_quiz_settings = $this->get_quiz_settings_values();

        if ( ! $this->step_choose_mail_list_is_completed()) {
            return false;
        }

        if (empty($this->addon_quiz_settings['fields_map'])) {
            return false;
        }

        if ( ! is_array($this->addon_quiz_settings['fields_map'])) {
            return false;
        }

        if (count($this->addon_quiz_settings['fields_map']) < 1) {
            return false;
        }

        return true;
    }

    //check if email provider and lists still valid
    public function step_choose_mail_list_is_completed()
    {
        $this->addon_quiz_settings = $this->get_quiz_settings_values();
        if ( ! isset($this->addon_quiz_settings['connected_email_providers'])) {
            return false;
        }

        if ( ! empty($this->addon_quiz_settings['connected_email_providers']) && $this->addon_quiz_settings['connected_email_providers'] != 'leadbank') {
            $connected_email_providers = $this->addon_quiz_settings['connected_email_providers'];
            if ( ! isset($this->addon_quiz_settings[$connected_email_providers]['lists'])) {
                return false;
            }

            if (empty($this->addon_quiz_settings[$connected_email_providers]['lists'])) {
                return false;
            }
        }

        return true;

    }

    public function get_input_map_fields($email_fields, $custom_fields, $current_data)
    {
        $quiz_questions = $this->get_quiz_fields();
        $quiz_fields    = array(
            'quiz-name' => __('Quiz Name', 'mailoptin'),
        );
        foreach ($quiz_questions as $quiz_question) {
            $quiz_fields[$quiz_question['slug']] = $quiz_question['title'];
        }
        if ('knowledge' === $this->quiz->quiz_type) {
            $quiz_fields['correct-answers'] = __('Correct Answers', 'mailoptin');
            $quiz_fields['total-answers']   = __('Total Answers', 'mailoptin');
        } elseif ('nowrong' === $this->quiz->quiz_type) {
            $quiz_fields['result-answers'] = __('Result Answer', 'mailoptin');
        }
        include dirname(__FILE__) . '/panel-quiz-settings-view.php';
    }
}