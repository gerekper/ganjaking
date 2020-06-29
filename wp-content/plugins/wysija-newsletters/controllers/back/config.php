<?php
defined('WYSIJA') or die('Restricted access');
class WYSIJA_control_back_config extends WYSIJA_control_back{
    var $view='config';
    var $model='config';

    function __construct(){
        parent::__construct();
    }

    function main() {
        parent::__construct();
        wp_enqueue_style('thickbox');

        if(!isset($_REQUEST['action'])) $this->action='main';
        else $this->action=$_REQUEST['action'];
        $this->jsTrans['testemail'] = __('Sending a test email', WYSIJA);
        $this->jsTrans['bounceconnect'] = __('Bounce handling connection test', WYSIJA);
        $this->jsTrans['processbounceT'] = __('Bounce handling processing', WYSIJA);
        $this->jsTrans['doubleoptinon'] = __('Subscribers will now need to activate their subscription by email in order to receive your newsletters. This is recommended.', WYSIJA);
        $this->jsTrans['doubleoptinoff'] = __('Unconfirmed subscribers will receive your newsletters from now on without the need to activate their subscriptions.', WYSIJA);
        $this->jsTrans['processbounce'] = __('Process bounce handling now!', WYSIJA);
        $this->jsTrans['errorbounceforward'] = __('When setting up the bounce system, you need to have a different address for the bounce email and the forward to address', WYSIJA);

        // form list
        $this->jsTrans['suredelete'] = __('Are you sure you want to delete this form?', WYSIJA);

        switch($this->action) {
            case 'log':
            case 'save':
            case 'clearlog':
                wp_enqueue_script('wysija-config-settings', WYSIJA_URL.'js/admin-config-settings.js', array('wysija-admin-js-global'), WYSIJA::get_version());
                wp_localize_script('wysija-config-settings', 'mpEmailCheck', WJ_Utils::get_tip_data());
                wp_enqueue_script('jquery-cookie', WYSIJA_URL.'js/jquery/jquery.cookie.js', array('jquery'), WYSIJA::get_version());
            case 'form_add':
            case 'form_edit':
            case 'form_duplicate':
            case 'form_delete':
            case 'form_widget_settings':
            case 'form_add_field':
                return $this->{$this->action}();
                break;
            case 'reinstall':
                $this->reinstall();
                return;
                break;
            case 'dkimcheck':
                $this->dkimcheck();
                if(defined('WYSIJA_REDIRECT'))  $this->redirectProcess();
                return;
                break;
            case 'doreinstall':
                $this->doreinstall();
                if(defined('WYSIJA_REDIRECT')){
                     global $wysi_location;
                     $wysi_location='admin.php?page=wysija_campaigns';
                    $this->redirectProcess();
                }
                return;
                break;
            default:
                wp_enqueue_script( 'mailpoet.tooltip', WYSIJA_URL . 'js/vendor/bootstrap.tooltip.js', array( 'jquery' ), WYSIJA::get_version(), true );
                wp_enqueue_style( 'mailpoet.tooltip', WYSIJA_URL . 'css/vendor/bootstrap.tooltip.css', array(), WYSIJA::get_version(), 'screen' );
                wp_enqueue_script('wysija-config-settings', WYSIJA_URL.'js/admin-config-settings.js', array('wysija-admin-js-global'), WYSIJA::get_version(), true);
                wp_localize_script('wysija-config-settings', 'mpEmailCheck', WJ_Utils::get_tip_data());
                wp_enqueue_script('jquery-cookie', WYSIJA_URL.'js/jquery/jquery.cookie.js', array('jquery'), WYSIJA::get_version());
        }

        if(WYSIJA_DBG > 1) {
            $this->viewObj->arrayMenus = array('log' => 'View log');
        }

        $this->data = array();
        $hook_settings_super_advanced_params = array();
        $this->data['hooks']['hook_settings_super_advanced'] = apply_filters('hook_settings_super_advanced',WYSIJA_module::execute_hook('hook_settings_super_advanced', $hook_settings_super_advanced_params), $hook_settings_super_advanced_params);
        $this->action='main';

        if(isset($_REQUEST['validate'])){
            $this->notice(str_replace(array('[link]','[/link]'),
            array('<a title="'.__('Get Premium now',WYSIJA).'" class="premium-activate" href="javascript:;">','</a>'),
            __('You\'re almost there. Click this [link]link[/link] to activate the licence you have just purchased.',WYSIJA)));

        }

    }

    function dkimcheck(){
        if(isset($_POST['xtz'])){

            $dataconf=json_decode(base64_decode($_POST['xtz']));
            if(isset($dataconf->dkim_pubk->key) && isset($dataconf->dkim_privk)){
                $modelConf=WYSIJA::get('config','model');
                $dataconfsave=array('dkim_pubk'=>$dataconf->dkim_pubk->key, 'dkim_privk'=>$dataconf->dkim_privk,'dkim_1024'=>1);

                $modelConf->save($dataconfsave);
                WYSIJA::update_option('dkim_autosetup',false);
            }
        }

        $this->redirect('admin.php?page=wysija_config');
        return true;
    }

    function save(){
        $_REQUEST   = stripslashes_deep($_REQUEST);
        $_POST   = stripslashes_deep($_POST);
        $this->requireSecurity();

        $hook_settings_before_save = array(
            'REQUEST' =>& $_REQUEST
        );
        apply_filters('hook_settings_before_save',WYSIJA_module::execute_hook('hook_settings_before_save', $hook_settings_before_save),$hook_settings_before_save);

        $this->modelObj->save($_REQUEST['wysija']['config'],true);

        $hook_settings_super_advanced_params = array();
        $this->data['hooks']['hook_settings_super_advanced'] = apply_filters('hook_settings_super_advanced',WYSIJA_module::execute_hook('hook_settings_super_advanced', $hook_settings_super_advanced_params),$hook_settings_super_advanced_params);
        // redirect so that javascript values get updated
        wp_redirect('admin.php?page=wysija_config'.$_REQUEST['redirecttab']);
    }

    function reinstall(){
        $this->viewObj->title=__('Reinstall MailPoet?',WYSIJA);
        return true;
    }

    function doreinstall(){
        $this->requireSecurity();
        if(isset($_REQUEST['postedfrom']) && $_REQUEST['postedfrom'] === 'reinstall') {
            $uninstaller=WYSIJA::get('uninstall','helper');
            $uninstaller->reinstall();
        }
        $this->redirect('admin.php?page=wysija_config');
        return true;
    }

    function render(){
        $this->checkTotalSubscribers();
        $this->viewObj->render($this->action,$this->data);
    }

    function log(){
        $this->viewObj->arrayMenus=array('clearlog'=>'Clear log');
        $this->viewObj->title='MailPoet\'s log';
        return true;
    }

    function clearlog(){
        $this->requireSecurity();
        update_option('wysija_log', array());
        $this->redirect('admin.php?page=wysija_config&action=log');
        return true;
    }

    // WYSIJA Form Editor
    function form_add() {
        $this->requireSecurity();
        $helper_form_engine = WYSIJA::get('form_engine', 'helper');
        // set default form data
        $helper_form_engine->set_data();

        // create form in database with default data
        $form = array('name' => __('New Form', WYSIJA));

        // insert into form table
        $model_forms = WYSIJA::get('forms', 'model');
        $form_id = $model_forms->insert($form);

        if($form_id !== null && (int)$form_id > 0) {
            // merge form_id into form data for later use
            $data = array_merge(array('form_id' => $form_id), $helper_form_engine->get_data());
            // update form data in form engine
            $helper_form_engine->set_data($data);
            // update form data in database
            $model_forms->update(array('data' => $helper_form_engine->get_encoded('data')), array('form_id' => (int)$form_id));

            // redirect to form editor, passing along the newly created form id
            WYSIJA::redirect('admin.php?page=wysija_config&action=form_edit&id='.$form_id);
        } else {
            WYSIJA::redirect('admin.php?page=wysija_config#tab-forms');
        }
        return true;
    }

    function form_duplicate() {
        $this->requireSecurity();
        if(isset($_GET['id']) && (int)$_GET['id'] > 0) {
            $form_id = (int)$_GET['id'];

            $model_forms = WYSIJA::get('forms', 'model');

            // get form data
            $form = $model_forms->getOne(array('name', 'data', 'styles'), array('form_id' => $form_id));

            if(empty($form)) {
                $this->error(__('This form does not exist', WYSIJA), true);
            } else {
                // reset model forms
                $model_forms->reset();

                // add "copy" to the name
                $form['name'] = $form['name'].' - '.__('Copy', WYSIJA);

                // insert form (duplicated)
                $model_forms->insert($form);

                // display notice
                $this->notice(sprintf(__('The form named "%1$s" has been created.', WYSIJA), $form['name']));
            }
        }

        WYSIJA::redirect('admin.php?page=wysija_config#tab-forms');
    }

    function form_delete() {

        $this->requireSecurity();

        if(isset($_GET['id']) && (int)$_GET['id'] > 0) {
            $form_id = (int)$_GET['id'];

            $model_forms = WYSIJA::get('forms', 'model');

            // get form data
            $form = $model_forms->getOne(array('name'), array('form_id' => $form_id));

            if(empty($form)) {
                $this->error(__('This form has already been deleted.', WYSIJA), true);
            } else {
                // delete the form in the database
                $model_forms->reset();
                $model_forms->delete(array('form_id' => $form_id));

                // display notice
                $this->notice(sprintf(__('The form named "%1$s" has been deleted.', WYSIJA), $form['name']));
            }
        }

        WYSIJA::redirect('admin.php?page=wysija_config#tab-forms');
    }

    function form_edit() {
        // define whether the form can be edited
        $this->data['can_edit'] = true;

        // wysija form editor javascript files
        $this->js[]='wysija-form-editor';
        $this->js[]='wysija-admin-ajax-proto';
        // $this->js[]='wysija-admin-ajax';
        $this->js[]='wysija-base-script-64';
        $this->js[] = 'mailpoet-select2';

        // make sure the editor content is not cached
        //header('Cache-Control: no-cache, max-age=0, must-revalidate, no-store'); // HTTP/1.1
        //header('Expires: Fri, 9 Mar 1984 00:00:00 GMT');

        // get form id
        $form_id = (isset($_REQUEST['id']) && (int)$_REQUEST['id'] > 0) ? (int)$_REQUEST['id'] : null;
        $form = array('name' => __('New form', WYSIJA));

        // if no form id was specified, then it's a new form
        if($form_id !== null) {
            // try to get form data based on form id
            $model_forms = WYSIJA::get('forms', 'model');
            $form = $model_forms->getOne($form_id);

            // if the form does not exist
            if(empty($form)) {
                // redirect to forms list
                $this->error(__('This form does not exist.', WYSIJA), true);
                WYSIJA::redirect('admin.php?page=wysija_config#tab-forms');
            } else {
                // pass form id to the view
                $this->data['form_id'] = (int)$form['form_id'];
            }
        }
        // pass form to the view
        $this->data['form'] = $form;

        $helper_form_engine = WYSIJA::get('form_engine', 'helper');
        $lists = $helper_form_engine->get_lists();
        $this->data['lists'] = $lists;

        // disable editing capability when there is no list
        if(empty($lists)) {
            $this->data['can_edit'] = false;
        }

        // get custom fields
        $this->data['custom_fields'] = $helper_form_engine->get_custom_fields();

        // translations
        $this->jsTrans = array_merge($this->jsTrans, $helper_form_engine->get_translations());
    }

    /*
     * Handles the settings popup of wysija form widgets
     */
    function form_widget_settings() {
        $this->iframeTabs = array('form_widget_settings' => __('Widget Settings', WYSIJA));
        $this->js[] = 'wysija-admin-ajax';
        $this->js[] = 'wysija-base-script-64';
        $this->js[] = 'wysija-scriptaculous';

        $_GET['tab'] = 'form_widget_settings';

        // if there is a field id, let's get all that from this field
        if(isset($_REQUEST['field_id'])) {
            $field_id = ((int)$_REQUEST['field_id'] > 0) ? (int)$_REQUEST['field_id'] : 0;

            // if the id is positive then try to fetch field data
            $custom_field = WJ_Field::get($field_id);

            // if field has been found
            if($custom_field !== NULL) {
                $this->data['name'] = (isset($_REQUEST['name'])) ? $_REQUEST['name'] : $custom_field->name;
                $this->data['type'] = (isset($_REQUEST['type'])) ? $_REQUEST['type'] : $custom_field->type;
                $this->data['field'] = $custom_field->user_column_name();
                $this->data['params'] = $custom_field->settings;
            } else {
                $this->data['name'] = (isset($_REQUEST['name'])) ? $_REQUEST['name'] : null;
                $this->data['type'] = (isset($_REQUEST['type'])) ? $_REQUEST['type'] : null;
                $this->data['field'] = null;
                $this->data['params'] = null;
            }

            $this->data['field_id'] = $field_id;
        } else {
            // extract parameters from url
            $params = array();
            if(isset($_REQUEST['params']) && trim(strlen($_REQUEST['params'])) > 0) {
                $pairs = explode('|', $_REQUEST['params']);
                if(count($pairs) > 0) {
                    foreach($pairs as $pair) {
                        // extract both key and value
                        list($key, $value) = explode(':', $pair);

                        // decode value
                        $value = base64_decode($value);
                        // unserialize if necessary (using is_serialized from WordPress) and making sure we only unserialize arrays not objects
                        if(is_serialized($value) === true && preg_match('/^a:[0-9]+:{/', $value) && !preg_match('/(^|;|{|})O:\+?[0-9]+:"/', $value) ) {
                            $value = (array) unserialize($value);
                        }
                        $params[$key] = $value;
                    }
                }
            }

            // common widget data
            $this->data['name'] = (isset($_REQUEST['name'])) ? $_REQUEST['name'] : null;
            $this->data['type'] = (isset($_REQUEST['type'])) ? $_REQUEST['type'] : null;
            $this->data['field'] = (isset($_REQUEST['field'])) ? $_REQUEST['field'] : null;

            // widget params
            $this->data['params'] = $params;

            // extra data that needs to be fetched for some widget
            $extra = array();

            switch($this->data['type']) {
                // in case of the list widget, we need to pass an array of all available lists
                case 'list':
                    $model_list = WYSIJA::get('list', 'model');

                    // get lists users can subscribe to (aka "enabled list")
                    $extra['lists'] = $model_list->get(array('name', 'list_id', 'is_public'), array('is_enabled' => 1));
                    break;
            }

            $this->data['extra'] = $extra;
        }

        return $this->popupContent();
        exit;
    }
    // End: WYSIJA Form Editor
}
