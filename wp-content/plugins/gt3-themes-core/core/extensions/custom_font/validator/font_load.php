<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }
if (!class_exists('Redux_Validation_font_load')) {

	class Redux_Validation_font_load {
		/* @var $parent ReduxFramework */

		public $parent;
		public $value;
		public $current;
		/* @var $inst ReduxFramework_extension_custom_font */
		private $inst;
		public static $valid_helper;
		public $count;

		/**
		 * Field Constructor.
		 * Required - must call the parent constructor, then assign field and value to vars, and obviously call the render field function
		 *
		 */
		function __construct($parent, $field, &$value, $current) {
			if(!isset($value) || $value == '' || empty($value)) return;
			
			if(!isset($value['attachment_id']) || $value['attachment_id'] == '') return;

			$this->parent = $parent;
			$this->field = $field;
			if(!is_array($this->field)){
				$this->field = array();
			}
			$this->field['msg'] = ( isset($this->field['msg']) ) ? $this->field['msg'] : esc_html__('You must provide a numerical value for this option.', 'wpdaddy_core');
			$this->current = $current;
			$this->inst = Custom_Font_Validator::getMainExtensition();
			$options = isset($this->parent->options[$this->field["id"]]) ? $this->parent->options[$this->field["id"]] : '';

			if (!empty($options) && isset($options[0]["attachment_id"]) && !empty($options[0]["attachment_id"])) {
				if ($value["attachment_id"] == "") {
					$this->field["msg"] = esc_html__("No attached file", 'wpdaddy_core');
					$this->error = $this->field;
					unset($value);
				}
			}
			$this->value = $value;
			$this->validate();
		}

//function

		/**
		 * Field Render Function.
		 * Takes the vars and outputs the HTML for the field in the settings
		 *
		 */
		function validate() {

			if ($this->value["attachment_id"] == "13857" || $this->value["attachment_id"] == "13866") {
				
			} else {
//				die();
			}
			
			$field_id = $this->field["id"];
			$options = isset($this->parent->options[$field_id]) ? $this->parent->options[$field_id] : '';


			if (isset($this->value["attachment_id"]) && !empty($this->value["attachment_id"])) {

				$this->count = Custom_Font_Validator::CountCheck($this->value["attachment_id"]);
				//chack if the folder exists
				if (Custom_Font_Validator::checkIfFontExist($this->value["attachment_id"])) {
					return false;
				}
				$format = Custom_Font_Validator::checkFormat($this->value["attachment_id"]);
				if (!$format) {
					$this->field["msg"] = esc_html__("Your must download .zip format file", 'wpdaddy_core');
					$this->error = $this->field;
					unset($this->value);
					return false;
				}
				$attach = get_attached_file($this->value["attachment_id"]);
				$this->attach = $attach;
				//Run//
				$this->upload();
			}
		}

		/**
		 * 
		 * @return boolean
		 */
		private function upload() {
			$info = pathinfo($this->attach);
			$file_name = basename($this->attach, '.' . $info['extension']);
			$temp_folder = $this->inst->upload_dir . "temp/" . $file_name;
			if (!file_exists($temp_folder)) {
				mkdir($temp_folder, 0777, true);
			}

			if (!file_exists($temp_folder)) {
				Custom_Font_Validator::remove_folder($temp_folder);
			} else {
				WP_Filesystem();
				unzip_file($this->attach, $temp_folder);
				$font_file_name = Custom_Font_Validator::get_zip_fontname($temp_folder);
				$has_font = Custom_Font_Validator::instance()->hasFont($font_file_name);
				if ($has_font) {
					$this->field["msg"] = esc_html__("Font exist", 'wpdaddy_core');
					$this->error = $this->field;
					unset($this->value);
					Custom_Font_Validator::remove_folder($temp_folder);
					return false;
				}
				if (trim($font_file_name) == '') {
					$this->field["msg"] = esc_html__("No found font file", 'wpdaddy_core');
					$this->error = $this->field;
					unset($this->value);
					Custom_Font_Validator::remove_folder($temp_folder);
				} else {
					if (file_exists($this->inst->upload_dir . $this->value["attachment_id"] . '/' . $font_file_name)) {
						if ($this->count > 0) {
							$this->field["msg"] = esc_html__('There is already a font with the same name', 'wpdaddy_core');
							$this->error = $this->field;
							unset($this->value);
							Custom_Font_Validator::remove_folder($temp_folder);
						}
					} else {
						if (!Custom_Font_Validator::copy_zip_fontfiles($temp_folder, $this->inst->upload_dir . $this->value["attachment_id"] . '/' . $font_file_name)) {
							$this->field["msg"] = esc_html__('Error during file upload', 'wpdaddy_core');
							$this->error = $this->field;
							unset($this->value);
						}
					}
				}
			}
		}

//function
	}

}