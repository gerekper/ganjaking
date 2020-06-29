<?php
defined('WYSIJA') or die('Restricted access');
class WYSIJA_control_back_config extends WYSIJA_control{

    function __construct(){
        if(!WYSIJA::current_user_can('wysija_config'))  die("Action is forbidden.");
        parent::__construct();
    }

    function _displayErrors(){
       if(version_compare(phpversion(), '5.4')>= 0){
            error_reporting(E_ALL ^ E_STRICT);

        }else{
            error_reporting(E_ALL);
        }
       @ini_set("display_errors", 1);
    }

    function _hideErrors(){
       error_reporting(0);
       @ini_set('display_errors', '0');
    }

    function send_test_mail(){
        $this->requireSecurity();
        $this->_displayErrors();
        /*switch the send method*/
        $configVal=$this->_convertPostedInarray();

        /*send a test mail*/
        $hEmail=WYSIJA::get('email','helper');
        $res['result']=$hEmail->send_test_mail($configVal);

        if($res['result']){
            $modelConf=WYSIJA::get('config','model');
            $modelConf->save(array('sending_emails_ok'=>$res['result']));
        }
        $this->_hideErrors();
        return $res;
    }

    function send_test_mail_ms(){
        $this->requireSecurity();
        $this->_displayErrors();
        /*switch the send method*/
        $configVal=$this->_convertPostedInarray();

        /*send a test mail*/
        $hEmail=WYSIJA::get('email','helper');
        $res['result']=$hEmail->send_test_mail($configVal,true);
        if($res['result']){
            $modelConf=WYSIJA::get('config','model');
            $modelConf->save(array('ms_sending_emails_ok'=>$res['result']));
        }
        //$this->_hideErrors();
        return $res;
    }

    function bounce_connect(){

        $configVal=$this->_convertPostedInarray();
        /*try to connect to thebounce server*/
        $bounceClass=WYSIJA::get('bounce','helper');
        $bounceClass->report = true;
        $res['result']=false;
        if($bounceClass->init($configVal)){
            if($bounceClass->connect()){
                $nbMessages = $bounceClass->getNBMessages();
                $this->notice(sprintf(__('Successfully connected to %1$s',WYSIJA),$bounceClass->config->getValue('bounce_login')));
                $this->notice(sprintf(__('There are %1$s messages in your mailbox',WYSIJA),$nbMessages));
                $bounceClass->close();
                if((int)$nbMessages >0) $res['result']=true;
                else $this->notice(sprintf(__('There are no bounced messages to process right now!',WYSIJA),$nbMessages));
                if(!empty($nbMessages)){
                        //$app->enqueueMessage('<a class="modal" style="text-decoration:blink" rel="{handler: \'iframe\', size: {x: 640, y: 480}}" href="'.acymailing_completeLink("bounces&task=process",true ).'">'.__("CLICK HERE to handle the messages",WYSIJA).'</a>');
                }
            }else{
                $errors = $bounceClass->getErrors();
                if(!empty($errors)){
                    $this->error($errors,true);
                    $errorString = implode(' ',$errors);
                    $port = $bounceClass->config->getValue('bounce_port','');
                    if(preg_match('#certificate#i',$errorString) && !$bounceClass->config->getValue('bounce_selfsigned',false)){
                            $this->notice(__('You may need to turn ON the option <i>Self-signed certificates</i>', WYSIJA));
                    }elseif(!empty($port) AND !in_array($port,array('993','143','110'))){
                            $this->notice(__('Are you sure you selected the right port? You can leave it empty if you do not know what to specify',WYSIJA));
                    }
                }
            }
        }


        return $res;
    }

    /**
     * processing the bounce manually through the config
     * @return type
     */
    function bounce_process(){
        $this->requireSecurity();
        // bounce handling
        $helper_bounce = WYSIJA::get('bounce','helper');

        // in a multisite case we process first the bounce recording into the bounce table
        if(is_multisite()){
            $helper_bounce->record_bounce_ms();

            // then we take actions from what has been returned by the bounce
            return $helper_bounce->process_bounce_ms();
        }else{
           return $helper_bounce->process_bounce();
        }
    }

    function linkignore(){
        $this->requireSecurity();
        $this->_displayErrors();

        $modelConf=WYSIJA::get('config','model');

        $ignore_msgs=$modelConf->getValue('ignore_msgs');
        if(!$ignore_msgs) $ignore_msgs=array();

        $ignore_msgs[$_REQUEST['ignorewhat']]=1;
        $modelConf->save(array('ignore_msgs'=>$ignore_msgs));

        $res['result']=true;
        $this->_hideErrors();
        return $res;
    }

    // Ajax called function to enable analytics sharing from welcome page.
    function share_analytics() {
        $this->requireSecurity();
        $this->_displayErrors();

        $model_config = WYSIJA::get('config','model');
        $model_config->save(array('analytics' => 1));

        $res['result'] = true;
        $this->_hideErrors();
        return $res;
    }

    function validate(){
        $this->requireSecurity();
        $helper_licence = WYSIJA::get('licence','helper');
        $result = $helper_licence->check();

        if(!isset($result['result'])){
            $result['result']=false;
        }

        return $result;
    }

    function _convertPostedInarray(){
        $_POST   = stripslashes_deep($_POST);
        $data_temp = $_POST['data'];
        $_POST['data']=array();
        foreach($data_temp as $val) $_POST['data'][$val['name']]=$val['value'];
        $data_temp = null;
        foreach($_POST['data'] as $k =>$v){
            $new_key = str_replace(array('wysija[config][',']'),'',$k);
            $config_val[$new_key] = $v;
        }
        return $config_val;
    }

    // WYSIJA Form Editor
    function wysija_form_generate_template() {
        $data = $this->_wysija_form_get_data();

        $helper_form_engine = WYSIJA::get('form_engine', 'helper');
        return base64_encode($helper_form_engine->render_editor_template($data));
    }

    function wysija_form_manage_field() {
        $this->requireSecurity();
        $response = array('result' => true, 'error' => null);

        // get data
        $data = $this->_wysija_form_get_data();
        $form_id = (int)$_REQUEST['form_id'];

        // check for required fields
        if(!isset($data['type']) || isset($data['type']) && strlen(trim($data['type'])) === 0) {
            $response['error'] = __('You need to select a type for this field', WYSIJA);
            $response['result'] = false;
        }
        if(!isset($data['name']) || isset($data['name']) && strlen(trim($data['name'])) === 0) {
            $response['error'] = __('You need to specify a name for this field', WYSIJA);
            $response['result'] = false;
        }

        // only proceed if there is no error
        if($response['error'] === null) {
            $is_required = (isset($data['params']['required']) ? WJ_Utils::to_bool($data['params']['required']) : false);

            if(isset($data['field_id']) && (int)$data['field_id'] > 0) {
                // it's an update
                $custom_field = WJ_Field::get($data['field_id']);

                if($custom_field !== NULL) {
                    $data['params'] = array_merge($custom_field->settings, $data['params']);
                    // update fields
                    $custom_field->name = $data['name'];
                    $custom_field->type = $data['type'];
                    $custom_field->required = $is_required;
                    $custom_field->settings = $data['params'];
                    $custom_field->save();
                } else {
                    // throw error if field does not exist
                    $response['error'] = __('This field does not exist', WYSIJA);
                    $response['result'] = false;
                }
            } else {
                // create new custom field
                $custom_field = new WJ_Field();
                $custom_field->set(array(
                    'name' => $data['name'],
                    'type' => $data['type'],
                    'required' => $is_required,
                    'settings' => $data['params']
                ));
                $custom_field->save();
            }

            if($response['error'] === null) {
                $helper_form_engine = WYSIJA::get('form_engine', 'helper');

                // need to update each block instance of this custom field
                $block = $helper_form_engine->refresh_custom_field($form_id, array(
                    'name' => $data['name'],
                    'field' => $custom_field->user_column_name(),
                    'type' => $data['type'],
                    'required' => $is_required,
                    'settings' => $data['params']
                ));

                // render editor toolbar & templates
                $response['data'] = array(
                    'toolbar' => base64_encode($helper_form_engine->render_editor_toolbar()),
                    'templates' => base64_encode($helper_form_engine->render_editor_templates())
                );

                if($block !== null) {
                    // refresh block using this custom field in the current form
                    $block_template = $helper_form_engine->render_editor_template($block);

                    if($block_template !== null) {
                        $response['data']['block'] = base64_encode($block_template);
                    }
                }
            }
        }

        return $response;
    }

    // get wysija form data from post (auto decoding)
    private function _wysija_form_get_data() {
        if(isset($_POST['wysijaData'])) {
            // decode the data string
            $decoded_data = base64_decode($_POST['wysijaData']);

            // avoid using stripslashes as it's not reliable depending on the magic quotes settings
            $json_data = str_replace('\"', '"', $decoded_data);
            return json_decode($json_data, true);
        }
        return array();
    }

    // remove a custom field
    function form_field_delete() {
        $this->requireSecurity();
        $data = $this->_wysija_form_get_data();

        // check for field_id parameter
        if(isset($data['field_id']) && (int)$data['field_id'] > 0) {
            // get custom field by id
            $custom_field = WJ_Field::get($data['field_id']);

            // if the custom field exists
            if($custom_field !== null) {
                // we need to remove the field in any form
                // get all forms
                $model_forms = WYSIJA::get('forms', 'model');
                $forms = $model_forms->getRows();

                // get custom field name
                $field_name = $custom_field->user_column_name();
                if(is_array($forms) && count($forms) > 0) {
                    // loop through each form
                    foreach ($forms as $i => $form) {
                        $requires_update = false;

                        // decode form data
                        $data = unserialize(base64_decode($form['data']));

                        // loop through each block
                        foreach($data['body'] as $j => $block) {
                            // in case we find a text block
                            if($block['field'] === $field_name) {
                                unset($data['body'][$j]);
                                // flag form to be updated
                                $requires_update = true;
                            }
                        }

                        // if the form requires update, let's do it
                        if($requires_update === true) {
                            $model_forms->reset();
                            $model_forms->update(array('data' => base64_encode(serialize($data))), array('form_id' => (int)$form['form_id']));
                        }
                    }
                }

                // delete custom field
                $custom_field->delete();
            }
        }
    }

    function form_name_save() {
        $this->requireSecurity();
        // get name from post and stripslashes it
        $form_name = trim(stripslashes($_POST['name']));
        // get form_id from post
        $form_id = (int)$_POST['form_id'];

        if(strlen($form_name) > 0 && $form_id > 0) {
            // update the form name within the database
            $model_forms = WYSIJA::get('forms', 'model');
            $model_forms->update(array('name' => $form_name), array('form_id' => $form_id));
        }
        return array('name' => $form_name);
    }

    function form_save() {
        $this->requireSecurity();
        // get form id
        $form_id = null;
        if(isset($_POST['form_id']) && (int)$_POST['form_id'] > 0) {
            $form_id = (int)$_POST['form_id'];
        }

        // decode json data and convert to array
        $raw_data = null;
        if(isset($_POST['wysijaData'])) {
            // decode the data string
            $decoded_data = base64_decode($_POST['wysijaData']);

            // avoid using stripslashes as it's not reliable depending on the magic quotes settings
            $json_data = str_replace('\"', '"', $decoded_data);
            // decode JSON data
            $raw_data = json_decode($json_data, true);
        }

        if($form_id === null or $raw_data === null) {
            $this->error('Error saving', false);
            return array('result' => false);
        } else {

            // flag to see if the user can select his own lists
            $has_list_selection = false;
            $raw_data['settings']['lists_selected_by'] = 'admin';

            // special case for block params, as we base64_encode the values and serialize arrays, so let's decode it before saving it
            foreach($raw_data['body'] as $block_id => $block) {
                if(isset($block['params']) && !empty($block['params'])) {
                    $params = array();

                    foreach($block['params'] as $key => $value) {
                        $value = base64_decode($value);
                        if(is_serialized($value) === true) {
                            $value = (array) unserialize($value);
                        }
                        $params[$key] = $value;
                    }

                    if(!empty($params)) {
                        $raw_data['body'][$block_id]['params'] = $params;
                    }
                }
                // special case when the list selection widget is present
                if($block['type'] === 'list') {
                    $has_list_selection = true;

                    $lists = array();
                    foreach($params['values'] as $list) {
                        $lists[] = (int)$list['list_id'];
                    }

                    // override lists in form settings
                    $raw_data['settings']['lists'] = $lists;
                    $raw_data['settings']['lists_selected_by'] = 'user';
                }
            }

            // make sure the lists parameter is an array, otherwise it's not gonna work for a single list
            if($has_list_selection === false) {
                if(!is_array($raw_data['settings']['lists'])) {
                    $raw_data['settings']['lists'] = array((int)$raw_data['settings']['lists']);
                }
            }

            // set form id into data so we can track who subscribed through it
            $raw_data['form_id'] = $form_id;

            // set data in form engine so we can generate the render the web version
            $helper_form_engine = WYSIJA::get('form_engine', 'helper');
            $helper_form_engine->set_data($raw_data);

            // check if the form has already been inserted in a widget and therefore display different success message
            $widgets = get_option('widget_wysija');
            $is_form_added_as_widget = false;
            if($widgets !== false) {
                foreach($widgets as $widget) {
                    if(is_array($widget) && isset($widget['form']) && (int)$widget['form'] === $form_id) {
                        $is_form_added_as_widget = true;
                    }

                }
            }
            if($is_form_added_as_widget === true) {
                $save_message = __('Saved! The changes are already active in your widget.', WYSIJA);
            } else {
                $save_message = str_replace(array(
                       '[link_widget]',
                       '[/link_widget]'
                    ), array(
                        '<a href="'.admin_url('widgets.php').'" target="_blank">',
                        '</a>'
                    ),
                    __('Saved! Add this form to [link_widget]a widget[/link_widget].', WYSIJA)
                );
            }

            // update form data in DB
            $model_forms = WYSIJA::get('forms', 'model');
            $model_forms->reset();
            $result = $model_forms->update(array(
                // get encoded data to store it in the database
                'data' => $helper_form_engine->get_encoded('data')
            ), array('form_id' => $form_id));

            // return response depending on db save result
            if(!$result) {
                // throw error
                $this->error(__('Your form could not be saved.', WYSIJA));
            } else {
                // save successful
                $this->notice(__('Your form has been saved.', WYSIJA));
            }
        }

        return array('result' => $result, 'save_message' => base64_encode($save_message), 'exports' => base64_encode($helper_form_engine->render_editor_export($form_id)));
    }

    function wysija_dismiss_update_notice() {
        WYSIJA::update_option('wysija_dismiss_update_notice', true);
    }

    function wysija_dismiss_license_notice() {
        WYSIJA::update_option('wysija_dismiss_license_notice', true);
    }
}