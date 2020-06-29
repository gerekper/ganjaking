<?php
defined('WYSIJA') or die('Restricted access');
class WYSIJA_help_uninstall extends WYSIJA_object{

    var $options_delete = array(
        'wysija_import_fields',
        'wysija',
        'installation_step',
        'wysija_polls_views',
        'wysicheck',
        'dkim_autosetup',
        'wysija_queries',
        'wysija_queries_errors',
        'wysija_msg',
        'wysija_log',
        'wysija_last_scheduled_check',
        'wysija_post_type_updated',
        'wysija_post_type_created',
        'wysija_schedules',
        'wysija_last_php_cron_call',
        'wysija_check_pn',
        'wysija_bounce_being_recorded',
        'wysijey',
        'debug_on',
        'debug_new',
        'mpoet_frequency_set'
    );

    function __construct(){
      //require_once(ABSPATH . 'wp-admin'.DS.'includes'.DS.'upgrade.php');
      parent::__construct();
    }

    function reinstall(){

        if (current_user_can('delete_plugins') && $this->removeProcess()) {
            $this->notice(__('MailPoet has been reinstalled successfully using the same version. Settings and data have been deleted.',WYSIJA));
        } else {
            $this->notice(__('MailPoet cannot be reinstalled because your folder <em>wp-content/uploads/wysija</em> needs to be writable. Change your permissions and reinstall.',WYSIJA));
        }

    }

    function uninstall(){

        if(current_user_can('delete_plugins') && $this->removeProcess()) {
            $this->wp_notice(__("MailPoet has been uninstalled. Your site is now cleared of MailPoet.",WYSIJA));
        }

    }

    function removeProcess(){

            // Remove the wysija folder in uploads.
            $helper_file = WYSIJA::get('file','helper');
            $upload_dir = $helper_file->getUploadDir();
            $is_writable = is_writable($upload_dir);
            if ($is_writable) {
                $helper_file->rrmdir($upload_dir);
            } elseif ($upload_dir!=false) {
                return false;
            }

            $file_name = WYSIJA_DIR.'sql'.DS.'uninstall.sql';
            $handle = fopen($file_name, 'r');
            $query = fread($handle, filesize($file_name));
            fclose($handle);

            $queries = str_replace('DROP TABLE `','DROP TABLE `[wysija]',$query);

            $queries = explode('-- QUERY ---',$queries);
            $modelWysija = new WYSIJA_model();
            global $wpdb;
            foreach($queries as $query)
                $modelWysija->query($query);
            //wysija_last_php_cron_call

            foreach($this->options_delete as $option_key){
                delete_option($option_key);
            }

            WYSIJA::update_option('wysija_reinstall',1);

            global $wp_roles;
            foreach($wp_roles->roles as $rolek=>$roled){
                if($rolek=='administrator') continue;
                $role=get_role($rolek);
                //remove wysija's cap
                $arr=array('wysija_newsletters','wysija_subscribers','wysija_config');

                foreach($arr as $arrkey)    $role->remove_cap( $arrkey );
            }

            return true;

    }


}
