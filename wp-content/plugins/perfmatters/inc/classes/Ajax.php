<?php
namespace Perfmatters;

class Ajax
{

	public function __construct() 
	{
		add_action('wp_ajax_save_settings', array('Perfmatters\Ajax', 'save_settings'));
		add_action('wp_ajax_restore_defaults', array('Perfmatters\Ajax', 'restore_defaults'));
		add_action('wp_ajax_export_settings', array('Perfmatters\Ajax', 'export_settings'));
		add_action('wp_ajax_import_settings', array('Perfmatters\Ajax', 'import_settings'));
	}

	//save settings ajax action
	public static function save_settings() {

		self::security_check();

		parse_str(stripslashes($_POST['form']), $form);
		
		if(!empty($form['perfmatters_options'])) {
			update_option('perfmatters_options', $form['perfmatters_options']);
		}
		
		if(!empty($form['perfmatters_tools'])) {
			update_option('perfmatters_tools', $form['perfmatters_tools']);
		}

		wp_send_json_success(array(
		    'message' => __('Settings saved.', 'perfmatters'), 
		));
	}

	//restore defaults ajax action
	public static function restore_defaults() {

		self::security_check();

		$defaults = perfmatters_default_options();
		
		if(!empty($defaults)) {
			update_option("perfmatters_options", $defaults);
		}

		wp_send_json_success(array(
	    	'message' => __('Successfully restored default options.', 'perfmatters'),
	    	'reload' => true
		));
	}

	//export settings ajax settings
	public static function export_settings() {

		self::security_check();

		$settings = array();

		$settings['perfmatters_options'] = get_option('perfmatters_options');
		$settings['perfmatters_tools'] = get_option('perfmatters_tools');

		wp_send_json_success(array(
		    'message' => __('Settings exported.', 'perfmatters'), 
		    'export' => json_encode($settings)
		));
	}

	//import settings ajax action
	public static function import_settings() {

		self::security_check();

		if(!empty($_FILES)) {
			$import_file = $_FILES['perfmatters_import_settings_file']['tmp_name'];
		}

		//cancel if there's no file
		if(empty($import_file)) {
			wp_send_json_error(array(
		    	'message' => __('No import file given.', 'perfmatters')
			));
		}

		//check if uploaded file is valid
		$file_parts = explode('.', $_FILES['perfmatters_import_settings_file']['name']);
		$extension = end($file_parts);
		if($extension != 'json') {
			wp_send_json_error(array(
		    	'message' => __('Please upload a valid .json file.', 'perfmatters')
			));
		}

		//unpack settings from file
		$settings = (array) json_decode(file_get_contents($import_file), true);

		if(isset($settings['perfmatters_options'])) {
			update_option('perfmatters_options', $settings['perfmatters_options']);
		}

		if(isset($settings['perfmatters_tools'])) {
			update_option('perfmatters_tools', $settings['perfmatters_tools']);
		}

		wp_send_json_success(array(
	    	'message' => __('Successfully imported Perfmatters settings.', 'perfmatters'),
	    	'reload' => true
		));

	}

	//ajax security check
	public static function security_check() {

		if(!current_user_can('manage_options')) {

			wp_send_json_error(array(
		    	'message' => __('Permission denied.', 'perfmatters')
			));
		}

		if(!check_ajax_referer('perfmatters-nonce', 'nonce', false)) {

		    wp_send_json_error(array(
		    	'message' => __('Nonce is invalid.', 'perfmatters')
			));
		}
	}
}