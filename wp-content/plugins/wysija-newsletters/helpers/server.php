<?php
defined('WYSIJA') or die('Restricted access');
class WYSIJA_help_server extends WYSIJA_object {

    function __construct(){
        parent::__construct();
    }

    /**
     * test if the server on which you are running wysija is unhealthy and may cause troubles
     * @return mixed
     */
    function unhealthy($return_result=false){

        $server_missing_capabilities=array();

        $missing_functions_result=$this->missing_php_functions();
        // can we mkdir
        if($missing_functions_result) $server_missing_capabilities['functions']=$missing_functions_result;

        // can we mkdir
        if(!$this->can_make_dir()) $server_missing_capabilities['mkdir']=true;

        // can we unzip a file to a temp folder
        if(!$this->can_unzip()) $server_missing_capabilities['unzip']=true;

        // can we move files to a new folder
        if(!$this->can_move()) $server_missing_capabilities['move']=true;

        // can we create, alter tables to the database
        if(!$this->can_sql_create_tables()) $server_missing_capabilities['sql_create']=true;

        // can we create, alter tables to the database
        if(!$this->can_sql_alter_tables()) $server_missing_capabilities['sql_alter']=true;

        if(!empty($server_missing_capabilities))  return $server_missing_capabilities;
        else return false;
    }

    /**
     * check if the server miss some vital functions to work properly
     * @return mixed
     */
    function missing_php_functions(){
        // can we use those functions on the server?
        $functions_per_environment=array(
            'required'=> array(
                'functions' => array('base64_decode', 'base64_encode'),
                ),
            'remote calls' => array(
                'functions'=>array('curl_init', 'http_get', 'file_get_contents'),
            ),
            'DKIM signature'=>array(
                'functions'=>array('openssl_sign')
                )
            );

        $missing_functions=array();
        foreach($functions_per_environment as $environment => &$data){
            // if the function we're testing has an alternative we try it
            foreach($data['functions'] as $function_name){
                if($this->is_function_available($function_name)){
                    $data['functions'][$function_name]=true;
                }else{
                    $missing_functions[$environment][$function_name]=true;
                }
            }
        }

        if(!empty($missing_functions))    return $missing_functions;
        return false;
    }


        /**
     * check if a php function is usable on this host
     * some hosts disable base64_encode or base64_decode for instance ...
     * @param string $function_name
     * @return boolean
     */
    function is_function_available($function_name) {
        // we just want string here
        if(!is_string($function_name) || $function_name=='') return false;

        // get the list of disabled function recorded in the php.ini
        $disabled = explode(', ', ini_get('disable_functions'));

        // does our function exists and is not in the disabled functions list of php.ini
        if(function_exists($function_name) && !in_array(strtolower($function_name), $disabled)) return true;

        return false;
    }

    /**
     * check if the php user can make a dir on the server
     * @return boolean
     */
    function can_make_dir(){
        // Test temp directory creation.
        $hFile = WYSIJA::get('file','helper');
        $upload_dir = wp_upload_dir();
        $temp_dir = $hFile->makeDir();
        if (!$temp_dir) {
            $this->error(sprintf(__('The folder "%1$s" is not writable, please change the access rights to this folder so that MailPoet can setup itself properly.',WYSIJA),$upload_dir['basedir']).'<a target="_blank" href="http://codex.wordpress.org/Changing_File_Permissions">'.__('Read documentation',WYSIJA).'</a>');
            return false;
        } else {
            // Create index.html to protect the temp directory.
            $index_file = 'index.html';
            fclose(fopen($temp_dir.$index_file, 'w'));
            return true;
        }
    }

    /**
     * check if the server allows us to unzip files through php
     * @return boolean
     */
    function can_unzip(){
        return true;
    }

    /**
     * check if the server can move a directory with nested content and directories to another directory
     * @return boolean
     */
    function can_move(){
        return true;
    }

    /**
     * check if the SQL user is allowed to create tables onto the database
     * @global type $wpdb
     * @return boolean
     */
    function can_sql_create_tables(){

        // test that we can create tables on the mysql server
        $model_user = WYSIJA::get('user','model');

        $this->_create_temp_sql_table_if_not_exists();

        $query="SHOW TABLES like '".$model_user->getPrefix()."user_list_temp';";

        global $wpdb;
        $res = $wpdb->get_var($query);

        if(!$res){
            $this->error(sprintf(
                    __('The MySQL user you have setup on your WordPress site (wp-config.php) doesn\'t have enough privileges to CREATE MySQL tables. Please change this user yourself or contact the administrator of your site in order to complete MailPoet\'s installation. mysql errors:(%1$s)',WYSIJA),  $wpdb->last_error));
            return false;
        }
        return true;

    }

    /**
     * check if the SQL user is allowed to alter tables onto the database
     * @return boolean
     */
    function can_sql_alter_tables(){
        // if we call that function out of the main process we make sure the table exists
        $this->_create_temp_sql_table_if_not_exists();

        // test that we can alter tables on the mysql server
        $model_user=WYSIJA::get('user','model');

        $query='ALTER TABLE `'.$model_user->getPrefix().'user_list_temp` ADD `namekey` VARCHAR( 255 ) NULL;';

        global $wpdb;
        $wpdb->query($query);
        if(!$wpdb->result){
            $error_message=__('The MySQL user you have setup on your WordPress site (wp-config.php) doesn\'t have enough privileges to CREATE MySQL tables. Please change this user yourself or contact the administrator of your site in order to complete MailPoet\'s installation. mysql errors:(%1$s)',WYSIJA);
            $this->error(sprintf(str_replace('CREATE', 'ALTER', $error_message), $wpdb->last_error ));
            $this->_drop_temp_sql_table();
            return false;
        }
        $this->_drop_temp_sql_table();
        return true;
    }

    /**
     * create the temporary table we need for SQL access rights tests
     * @global type $wpdb
     * @return boolean
     */
    function _create_temp_sql_table_if_not_exists(){
        $model_user=WYSIJA::get('user','model');
        $query='CREATE TABLE IF NOT EXISTS `'.$model_user->getPrefix().'user_list_temp` (
  `list_id` INT unsigned NOT NULL,
  `user_id` INT unsigned NOT NULL,
  `sub_date` INT unsigned DEFAULT 0,
  `unsub_date` INT unsigned DEFAULT 0,
  PRIMARY KEY (`list_id`,`user_id`)
)';

        global $wpdb;

        $wpdb->query($query);
        return true;
    }


    /**
     * drop the temporary table we've created
     * @return boolean
     */
    function _drop_temp_sql_table(){
        $model_user=WYSIJA::get('user','model');
        global $wpdb;
        $query='DROP TABLE `'.$model_user->getPrefix().'user_list_temp`;';

        $wpdb->query($query);
        if(!$wpdb->result){
            return false;
        }
        return true;
    }

}
