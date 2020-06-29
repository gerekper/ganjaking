<?php
defined('WYSIJA') or die('Restricted access');
/**
 * @class Wysija Engine Helper (PHP5 version)
 */
class WYSIJA_help_form_engine extends WYSIJA_object {
    // debug mode
    private $_debug = false;

    // rendering context (editor, web)
    private $_context = 'editor';
    // rendering mode (live, preview)
    private $_mode = 'live';

    // data holders
    private $_data = null;
    private $_styles = null;
    private $_lists = null;
    private $_fields = null;

    // static form fields
    private $_static_fields = array('email', 'submit');

    // unique form fields
    private $_unique_fields = array('firstname', 'lastname', 'list');

    // constructor
    function __construct(){
        parent::__construct();
    }


    // i18n methods
    public function get_translations() {
        return array(
            'savingnl' => __('Saving form...', WYSIJA),
            'save' => __('Save', WYSIJA),
            'edit_settings' => __('Edit display', WYSIJA),
            'list_cannot_be_empty' => __('You have to select at least 1 list', WYSIJA),
            'not_enough_options' => __('Your subscriber needs at least 2 options to select from', WYSIJA),
            'missing_checkbox_label' => __('You need to specify a value for your checkbox', WYSIJA),
            'add_field' => __('Add new field', WYSIJA),
            'edit_field' => __('Edit custom field', WYSIJA),
            'delete_field_confirmation' => __('Are you sure you want to delete this custom field?', WYSIJA),
            'date_select_year' => __('Year', WYSIJA),
            'date_select_month' => __('Month', WYSIJA),
            'date_select_day' => __('Day', WYSIJA)
        );
    }

    public function get_months($options = array()) {

        $defaults = array(
            'selected' => null
        );
        // is default today
        if(isset($options['params']['is_default_today']) && (bool)$options['params']['is_default_today'] === true) {
            $defaults['selected'] = (int)strftime('%m');
        }

        // merge options with defaults
        $options = array_merge($defaults, $options);

        $month_names = array(
            __('January', WYSIJA),
            __('February', WYSIJA),
            __('March', WYSIJA),
            __('April', WYSIJA),
            __('May', WYSIJA),
            __('June', WYSIJA),
            __('July', WYSIJA),
            __('August', WYSIJA),
            __('September', WYSIJA),
            __('October', WYSIJA),
            __('November', WYSIJA),
            __('December', WYSIJA)
        );

        $months = array();
        for($i = 0; $i < 12; $i++) {
            $months[] = array(
                'month' => ($i + 1),
                'month_name' => $month_names[$i],
                'is_selected' => (($i + 1) === $options['selected'])
            );
        }

        return $months;
    }

    public function get_years($options = array()) {
        $defaults = array(
            'selected' => null,
            'from' => (int)strftime('%Y') - 100,
            'to' => (int)strftime('%Y')
        );
        // is default today
        if(isset($options['params']['is_default_today']) && (bool)$options['params']['is_default_today'] === true) {
            $defaults['selected'] = (int)strftime('%Y');
        }

        // merge options with defaults
        $options = array_merge($defaults, $options);

        $years = array();

        // return years as an array
        for($i = (int)$options['from']; $i < (int)($options['to'] + 1); $i++) {
            $years[] = array(
                'year' => $i,
                'is_selected' => ($i === $options['selected'])
            );
        }

        return array_reverse($years);
    }

    public function get_days($options = array()) {
        $defaults = array(
            'selected' => null
        );
        // is default today
        if(isset($options['params']['is_default_today']) && (bool)$options['params']['is_default_today'] === true) {
            $defaults['selected'] = (int)strftime('%d');
        }

        // merge options with defaults
        $options = array_merge($defaults, $options);

        $days = array();

        // return days as an array
        for($i = 1; $i < 32; $i++) {
            $days[] = array(
                'day' => $i,
                'is_selected' => ($i === $options['selected'])
            );
        }

        return $days;
    }

    // getters/setters
    public function get_data($type = null) {
        if($type !== null) {
            if(array_key_exists($type, $this->_data)) {
                return $this->_data[$type];
            } else {
                // return default value
                $defaults = $this->get_default_data();
                return $defaults[$type];
            }
        }
        return $this->_data;
    }

    public function set_data($value = null, $decode = false) {
        if(!$value) {
            $this->_data = $this->get_default_data();
        } else {
            $this->_data = $value;
            if($decode) {
                $this->_data = $this->get_decoded('data');
            }
        }
    }

    public function set_lists($lists = array()) {
        $this->_lists = $lists;
    }

    public function get_formatted_lists() {

        $lists = $this->get_lists();
        $formatted_lists = array();

        foreach($lists as $list) {
            $formatted_lists[$list['list_id']] = $list['name'];
        }
        return $formatted_lists;
    }

    public function get_lists() {
        if($this->_lists === null) {
            // get available lists which users can subscribe to
            $model_list = WYSIJA::get('list','model');

            // get lists users can subscribe to (aka "enabled list")
            $lists = $model_list->get(array('name', 'list_id', 'is_public'), array('is_enabled' => 1));

            $this->set_lists($lists);
        }
        return $this->_lists;
    }

    private function get_context() {
        return $this->_context;
    }

    private function set_context($value = null) {
        if($value !== null) $this->_context = $value;
    }

    public function set_mode($value = null) {
        if($value !== null) $this->_mode = $value;
    }

    private function get_mode() {
        return $this->_mode;
    }

    public function get_encoded($type = 'data') {
        return base64_encode(serialize($this->{'get_'.$type}()));
    }

    public function get_decoded($type = 'data') {
        return unserialize(base64_decode($this->{'get_'.$type}()));
    }

    private function get_default_data() {

        $lists = $this->get_lists();

        // select default list
        $default_list = array();
        if(!empty($lists)) {
            $default_list[] = $lists[0]['list_id'];
        }

        return array(
            'version' => '0.4',
            'settings' => array(
                'on_success' => 'message',
                'success_message' => __('Check your inbox or spam folder now to confirm your subscription.', WYSIJA),
                'lists' => $default_list,
                'lists_selected_by' => 'admin'
            ),
            'body' => array(
                array(
                    'name' => __('Email', WYSIJA),
                    'type' => 'input',
                    'field' => 'email',
                    'params' => array(
                        'label' => __('Email', WYSIJA),
                        'required' => true
                    )
                ),
                array(
                    'name' => __('Submit', WYSIJA),
                    'type' => 'submit',
                    'field' => 'submit',
                    'params' => array(
                        'label' => __('Subscribe!', WYSIJA)
                    )
                )
            )
        );
    }

    public function get_setting($key = null) {
        if($key === null) return null;

        if($this->is_data_valid() === true) {
            $settings = $this->get_data('settings');
            if(array_key_exists($key, $settings)) {
                // otherwise, simply return the value
                return $settings[$key];
            } else {
                return null;
            }
        }
    }

    // common methods
    private function is_debug() {
        return ($this->_debug === true);
    }

    private function is_data_valid() {
        return ($this->get_data() !== null);
    }

    public function get_custom_fields() {
        if($this->_fields === null) {
            // get available custom fields
            $custom_fields = WJ_Field::get_all(array('order_by' => 'name ASC'));
            $user_fields = array();

            if(!empty($custom_fields)) {
                foreach($custom_fields as $custom_field) {
                    $user_fields[] = array(
                        'field_id' => $custom_field->id,
                        'name' => $custom_field->name,
                        'column_name' => $custom_field->user_column_name(),
                        'column_type' => $custom_field->type,
                        'params' => $custom_field->settings
                    );
                }
            }

            // we need to figure out the default list for the "List selection" widget
            $lists = $this->get_lists();

            // select default list
            $default_list = array();
            if(!empty($lists)) {
                $default_list[] = array(
                    'list_id' => $lists[0]['list_id'],
                    'is_checked' => 0
                );
            }

            // extra widgets that can be added more than once
            $extra_fields = array(
                array(
                    'name' => __('Divider', WYSIJA),
                    'column_name' => 'divider',
                    'column_type' => 'divider'
                ),
                array(
                    'name' => __('First name', WYSIJA),
                    'column_name' => 'firstname',
                    'column_type' => 'input'
                ),
                array(
                    'name' => __('Last name',WYSIJA),
                    'column_name' => 'lastname',
                    'column_type' => 'input'
                ),
                array(
                    'name' => __('List selection', WYSIJA),
                    'column_name' => 'list',
                    'column_type' => 'list',
                    'params' => array(
                        'label' => __('Select list(s):', WYSIJA),
                        'values' => $default_list
                    )
                ),
                array(
                    'name' => __('Random text or HTML', WYSIJA),
                    'column_name' => 'html',
                    'column_type' => 'html',
                    'params' => array(
                        'text' => __('Subscribe to our newsletter and join our [total_subscribers] subscribers.', WYSIJA)
                    )
                )
            );

            // set data to be passed to the view
            $this->_fields = array_merge($user_fields, $extra_fields);
        }

        return $this->_fields;
    }

    // editor rendering methods
    public function render_editor_toolbar() {

        // get custom fields
        $fields = $this->get_custom_fields();

        $output = '';
        $output .= '<li style="text-align:center;"><a id="wysija-add-field" class="button" href="javascript:;" href2="admin.php?page=wysija_config&action=form_widget_settings&field_id=0">'.__('Add New Field',WYSIJA).'</a></li>';

        foreach($fields as $field) {
            // get field type or defaults to "input"
            $type = (isset($field['column_type'])) ? $field['column_type'] : 'input';

            // set unique if the field type matches the unique_fields OR is a custom field
            $is_unique = (bool)(in_array($field['column_name'], $this->_unique_fields) or isset($field['field_id']));

            // check whether it's an actual custom field
            $is_custom_field = $this->is_custom_field($field);

            // actions
            $actions = '';
            if($is_custom_field) {
                $actions = '<a class="wysija_form_item_settings settings" title="'.__('Edit field', WYSIJA).'" href="javascript:;" href2="admin.php?page=wysija_config&action=form_widget_settings&field_id='.((int)$field['field_id']).'" data-field-id="'.((int)$field['field_id']).'"><span class="dashicons dashicons-admin-generic"></span></a>';
                $actions .= '<a class="wysija_form_item_delete delete" title="'.__('Delete field', WYSIJA).'" href="javascript:;" data-field-id="'.((int)$field['field_id']).'"><span class="dashicons dashicons-dismiss"></span></a>';
            }

            // generate html for toolbar item
            $output .= '<li><a class="wysija_form_item" id="'.$field['column_name'].'" wysija_field="'.$field['column_name'].'" wysija_name="'.$field['name'].'" wysija_unique="'.$is_unique.'" wysija_type="'.$type.'">'.$field['name'].'</a>'.$actions.'</li>';
        }

        return $output;
    }

    // renders all widgets' templates
    function render_editor_templates() {
        $this->set_context('editor');

        // get custom fields
        $fields = $this->get_custom_fields();

        // get parser helper
        $helper_render_engine = WYSIJA::get('render_engine', 'helper');
        $helper_render_engine->setTemplatePath(WYSIJA_EDITOR_TOOLS);

        // define html output string
        $output = '';

        foreach($fields as $field) {
             // get field type or defaults to "input"
            $type = (isset($field['column_type'])) ? $field['column_type'] : 'input';

            // get label from params, defaults to field name
            $label = (isset($field['params']['label'])) ? $field['params']['label'] : $field['name'];

            // build field data in order to pass it to the widget template
            $block = array(
                'field' => $field['column_name'],
                'type' => $type,
                'name' => $field['name'],
                'unique' => (in_array($field['column_name'], $this->_unique_fields)),
                'static' => (in_array($field['column_name'], $this->_static_fields)),
                'params' => array(
                    'label' => $label
                ),
                'i18n' => $this->get_translations()
            );

            // get field extra params if specified
            if(isset($field['params'])) {
                // merge the params
                $block['params'] = array_merge($field['params'], $block['params']);
            }

            // get extra data depending on field type
            $block = $this->_get_extra_data($block);

            // render widget templates
            $output .= $helper_render_engine->render($block, 'templates/form/editor/widgets/template.html');
        }
        return $output;
    }

    private function set_lists_names($block = array()) {
        // get lists using each list id as key
        $lists = $this->get_formatted_lists();

        if($this->get_context() === 'editor') {
            $block['lists'] = $lists;
        } else {
            // if the block has no list, then simply return the block
            if(!isset($block['params']['values']) or empty($block['params']['values'])) return $block;

            $values = array();

            foreach($block['params']['values'] as $list) {
                // check if the list id exists in the lists
                if(isset($lists[$list['list_id']])) {
                    $is_checked = (isset($list['is_checked']) ? (int)$list['is_checked'] : 0);
                    $values[] = array('name' => $lists[$list['list_id']], 'list_id' => $list['list_id'], 'is_checked' => $is_checked);
                }
            }

            $block['params']['values'] = $values;
        }

        return $block;
    }

    // renders a single widget's template
    public function render_editor_template($block = array()) {
        $this->set_context('editor');

        if(empty($block)) return null;

        // get parser helper
        $helper_render_engine = WYSIJA::get('render_engine', 'helper');
        $helper_render_engine->setTemplatePath(WYSIJA_EDITOR_TOOLS);

        // get extra data depending on field type
        $block = $this->_get_extra_data($block);

        $block = array_merge($block, array(
            'unique' => (in_array($block['field'], $this->_unique_fields)),
            'static' => (in_array($block['field'], $this->_static_fields)),
            'i18n' => $this->get_translations()
        ));

        // render widget templates
        return $helper_render_engine->render($block, 'templates/form/editor/widgets/template.html');
    }

    public function refresh_custom_field($form_id = null, $field = array()) {
        if($form_id === null or empty($field)) return null;

        // check if refreshing the field is necessary
        if($this->is_custom_field($field)) {
            // get all forms
            $model_forms = WYSIJA::get('forms', 'model');
            $forms = $model_forms->getRows();

            $updated_block = null;

            // check if there's at least one form otherwise it's kinda pointless
            if(is_array($forms) && count($forms) > 0) {
                // loop over each form
                foreach ($forms as $i => $form) {
                    $requires_update = false;

                    // decode form data
                    $data = unserialize(base64_decode($form['data']));

                    // loop through each block
                    foreach ($data['body'] as $j => $block) {
                        // in case we find an instance of the custom field that needs to be updated
                        if($block['field'] === $field['field']) {
                            $updated_params = $field['settings'];
                            $display_fields = array('label', 'label_within', 'lines');

                            // apply block display options
                            foreach($display_fields as $display_field) {
                                if(array_key_exists($display_field, $block['params'])) {
                                    $updated_params[$display_field] = $block['params'][$display_field];
                                }
                            }

                            // apply new parameters
                            $data['body'][$j]['params'] = $updated_params;
                            // set flag in order to save changes
                            $requires_update = true;

                            // if it's in the current form, we need to return an updated version of this block
                            if((int)$form['form_id'] === $form_id) {
                                $updated_block = $data['body'][$j];
                            }
                        }
                    }

                    // if the form requires update, let's do it
                    if($requires_update === true) {
                        $model_forms->reset();
                        $model_forms->update(array('data' => base64_encode(serialize($data))), array('form_id' => (int)$form['form_id']));
                    }
                }
            }

            // return false if there's no need to update any block in the current form
            return $updated_block;
        }
    }

    private function _get_extra_data($block = array()) {
        // special case for lists
        if($block['field'] === 'list') {
            $block = $this->set_lists_names($block);
        }

        // special case for "date" types
        if($block['type'] === 'date') {
            $block['days'] = $this->get_days($block);
            $block['months'] = $this->get_months($block);
            $block['years'] = $this->get_years($block);

            $display_date_fields = explode('_', $block['params']['date_type']);

            // date order
            $date_orders = array(
                'year_month_day' => array('mm/dd/yyyy', 'dd/mm/yyyy', 'yyyy/mm/dd'),
                'year_month' => array('mm/yyyy', 'yyyy/mm'),
                'year' => array('yyyy'),
                'month' => array('mm')
            );

            if(isset($block['params']['date_order']) && in_array($block['params']['date_order'], $date_orders[$block['params']['date_type']])) {
                $fields = explode('/', $block['params']['date_order']);
            } else {
                $fields = explode('/', $date_orders[$block['params']['date_type']][0]);
            }
            $block['date_fields'] = $fields;
        }

        return $block;
    }

    // renders the editor
    function render_editor() {
        $this->set_context('editor');

        if($this->is_data_valid() === false) {
            throw new Exception('data is not valid');
        } else {
            $helper_render_engine = WYSIJA::get('render_engine', 'helper');
            $helper_render_engine->setTemplatePath(WYSIJA_EDITOR_TOOLS);

            $data = array(
                'body' => $this->render_editor_body(),
                'is_debug' => $this->is_debug(),
                'i18n' => $this->get_translations()
            );

            return $helper_render_engine->render($data, 'templates/form/editor/template.html');
        }
    }

    // renders editor's body
    function render_editor_body() {
        $helper_render_engine = WYSIJA::get('render_engine', 'helper');
        $helper_render_engine->setTemplatePath(WYSIJA_EDITOR_TOOLS);

        $blocks = $this->get_data('body');
        if(empty($blocks)) return '';

        $body = '';
        foreach($blocks as $block) {
            // get extra data depending on field type
            $block = $this->_get_extra_data($block);

            // generate block template
            $data = array_merge($block, array(
                'unique' => (in_array($block['field'], $this->_unique_fields)),
                'static' => (in_array($block['field'], $this->_static_fields)),
                'i18n' => $this->get_translations())
            );

            $body .= $helper_render_engine->render($data, 'templates/form/editor/widgets/template.html');
        }

        return $body;
    }

    // web rendering methods
    public function render_web($data = array()) {
        $this->set_context('web');

        if($this->is_data_valid() === false) {
            throw new Exception('data is not valid');
        } else {
            $helper_render_engine = WYSIJA::get('render_engine', 'helper');
            $helper_render_engine->setTemplatePath(WYSIJA_EDITOR_TOOLS);

            $data = array(
                'preview' => ($this->get_mode() === 'preview'),
                'settings' => $this->get_data('settings'),
                'body' => $this->render_web_body()
            );

            // in live mode, we need to specify the form id
            if($this->get_mode() === 'live') {
                $data['form_id'] = (int)$this->get_data('form_id');
            }

            $helper_render_engine = WYSIJA::get('render_engine', 'helper');
            $helper_render_engine->setTemplatePath(WYSIJA_EDITOR_TOOLS);

            // make sure we get the messages
            $output = '';

            $posted_form = (isset($_POST['form_id']) && (int)$_POST['form_id'] > 0) ? (int)$_POST['form_id'] : 0;

/*            if($data['form_id'] === $posted_form) {
                $view = WYSIJA::get('widget_nl','view','front');
                if(count($view->getMsgs()) > 0) {
                    $output .= $view->messages();
                }
            }*/

            try {
                $output .= $helper_render_engine->render($data, 'templates/form/web/template.html');
                return $output;
            } catch(Exception $e) {
                return '';
            }
        }
    }

    protected function get_validation_class($block) {
        $rules = array();

        // if it's the email field, it's mandatory and needs to be valid
        if($block['field'] === 'email') {
            $rules[] = 'required';
            $rules[] = 'custom[email]';
        }

        // if it's the list field, at least one option needs to be selected
        if($block['field'] === 'list') {
            $rules[] = 'required';
        }

        // check if the field is required
        if(isset($block['params']['required']) && (bool)$block['params']['required'] === true) {
            $rules[] = 'required';
        }

        // check for validation rules
        if(isset($block['params']['validate'])) {
            if(is_array($block['params']['validate'])) {
                // handle multiple validation rules
                foreach($block['params']['validate'] as $rule) {
                    $rules[] = 'custom['.$rule.']';
                }
            } else if(strlen(trim($block['params']['validate'])) > 0) {
                // handle single validation rule
                $rules[] = 'custom['.$block['params']['validate'].']';
            }
        }

        // generate string if there is at least one rule to validate against
        if(empty($rules)) {
            return '';
        } else {
            // make sure rules are not duplicated
            $rules = array_unique($rules);
            return 'validate['.join(',', $rules).']';
        }
    }

    protected function render_web_body() {
        $helper_render_engine = WYSIJA::get('render_engine', 'helper');
        $helper_render_engine->setTemplatePath(WYSIJA_EDITOR_TOOLS);

        $model_config = WYSIJA::get('config','model');
        $helperUser = WYSIJA::get('user','helper');

        $blocks = $this->get_data('body');

        if(empty($blocks)) return '';

        $body = '';
        foreach($blocks as $key => $block) {
            // get extra data depending on field type
            $block = $this->_get_extra_data($block);

            // special case for email widget
            if($block['field'] === 'email') {
                $user_email = WYSIJA::wp_get_userdata('user_email');

                if($user_email && is_string($user_email) && is_user_logged_in() && !current_user_can('switch_themes') && !is_admin()) {
                    $block['value'] = $user_email;
                }
            }

            // special case for a submit button
            if($block['type'] === 'submit' && $helperUser->isCaptchaEnabled()) {
                $block['params']['recaptcha_key'] = htmlspecialchars($model_config->getValue('recaptcha_key'));
            }

            // set field name 'prefix' depending whether it's a custom field or not
            if($this->is_custom_field($block)) {
                $field_prefix = 'wysija[field]';
            } else {
                $field_prefix = 'wysija[user]';
            }

            // generate block template
            $data = array_merge($block, array(
                'field_prefix' => $field_prefix,
                'preview' => ($this->get_mode() === 'preview'),
                'i18n' => $this->get_translations(),
                'validation' => $this->get_validation_class($block)
            ));
            $body .= $helper_render_engine->render($data, 'templates/form/web/widgets/template.html');
        }

        return $body;
    }

    public function is_custom_field($field = array()) {
         return (bool)(isset($field['field_id']) || (isset($field['field']) && strpos($field['field'], 'cf_') === 0));
    }

    public function get_exports($form_id) {
        return array(
            'iframe' => base64_encode($this->export($form_id, 'iframe')),
            'php' => base64_encode($this->export($form_id, 'php')),
            'html' => base64_encode($this->export($form_id, 'html'))
        );
    }

    public function render_editor_export($form_id) {
        $helper_render_engine = WYSIJA::get('render_engine', 'helper');
        $helper_render_engine->setTemplatePath(WYSIJA_EDITOR_TOOLS);

        $data = array(
            'types' => array(
                'iframe' => $this->export($form_id, 'iframe'),
                'php' => $this->export($form_id, 'php'),
                'html' => $this->export($form_id, 'html'),
                'shortcode' => $this->export($form_id, 'shortcode')
            )
        );

        return $helper_render_engine->render($data, 'templates/form/web/export.html');
    }

    public function export($form_id, $type) {
        switch($type) {
            case 'iframe':
                $url_params = array(
                    'wysija-page' => 1,
                    'controller' => 'subscribers',
                    'action' => 'wysija_outter',
                    'wysija_form' => $form_id
                );

                $url_params['external_site'] = 1;

                $model_config = WYSIJA::get('config','model');
                $source_url = WYSIJA::get_permalink($model_config->getValue('confirm_email_link'), $url_params, true);

                return '<iframe width="100%" scrolling="no" frameborder="0" src="'.$source_url.'" class="iframe-wysija" vspace="0" tabindex="0" style="position: static; top: 0pt; margin: 0px; border-style: none; height: 330px; left: 0pt; visibility: visible;" marginwidth="0" marginheight="0" hspace="0" allowtransparency="true" title="'.__('Subscription MailPoet',WYSIJA).'"></iframe>';
            break;
            case 'php':
                $output = array(
                    '$widgetNL = new WYSIJA_NL_Widget(true);',
                    'echo $widgetNL->widget(array(\'form\' => '.(int)$form_id.', \'form_type\' => \'php\'));'
                );
                return join("\n", $output);
            break;
            case 'html':
                //need some language for the validation
                $helper_toolbox = WYSIJA::get('toolbox','helper');
                $wp_language_code = $helper_toolbox->get_language_code();

                $helperUser=WYSIJA::get('user','helper');

                $wysija_version = WYSIJA::get_version();
                $scripts_to_include = '<!--START Scripts : this is the script part you can add to the header of your theme-->'."\n";
                $scripts_to_include .= '<script type="text/javascript" src="'.includes_url().'js/jquery/jquery.js'.'?ver='.$wysija_version.'"></script>'."\n";
                if(file_exists(WYSIJA_DIR.'js'.DS.'validate'.DS.'languages'.DS.'jquery.validationEngine-'.$wp_language_code.'.js')){
                    $scripts_to_include .= '<script type="text/javascript" src="'.WYSIJA_URL.'js/validate/languages/jquery.validationEngine-'.$wp_language_code.'.js'.'?ver='.$wysija_version.'"></script>'."\n";
                }else{
                    $scripts_to_include .= '<script type="text/javascript" src="'.WYSIJA_URL.'js/validate/languages/jquery.validationEngine-en.js'.'?ver='.$wysija_version.'"></script>'."\n";
                }
                $scripts_to_include .= '<script type="text/javascript" src="'.WYSIJA_URL.'js/validate/jquery.validationEngine.js'.'?ver='.$wysija_version.'"></script>'."\n";
                $scripts_to_include .= '<script type="text/javascript" src="'.WYSIJA_URL.'js/front-subscribers.js'.'?ver='.$wysija_version.'"></script>'."\n";
                $scripts_to_include .= '<script type="text/javascript">
                /* <![CDATA[ */
                var wysijaAJAX = {"action":"wysija_ajax","controller":"subscribers","ajaxurl":"'.admin_url('admin-ajax.php','absolute').'","loadingTrans":"'.__('Loading...',WYSIJA).'"};
                /* ]]> */
                </script>';
                $scripts_to_include .= '<script type="text/javascript" src="'.WYSIJA_URL.'js/front-subscribers.js?ver='.$wysija_version.'"></script>'."\n";

                if($helperUser->isCaptchaEnabled()) {
                  $scripts_to_include .= '<script type="text/javascript" src="https://www.google.com/recaptcha/api.js"></script>'."\n";
                }

                $scripts_to_include .= '<!--END Scripts-->'."\n"."\n";

                //enqueue the scripts
                $html_result = $scripts_to_include;

                // add the html for the form
                $widget_NL = new WYSIJA_NL_Widget(true);
                $html_result .= $widget_NL->widget(array('form' => (int)$form_id, 'form_type' => 'html'));

                return $html_result;
            break;
            case 'shortcode':
                return '[wysija_form id="'.(int)$form_id.'"]';
            break;
        }
    }
}
