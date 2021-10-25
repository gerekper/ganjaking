<?php
	namespace MasterAddons\Inc\Templates\Documents;
	use Elementor\Core\Base\Document as Document;

	/**
	 * Author Name: Liton Arefin
	 * Author URL: https://jeweltheme.com
	 * Date: 9/8/19
	 */



	if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

	class Master_Addons_Document_Base extends Document {

		public function get_name() {
			return '';
		}

		public static function get_title() {
			return '';
		}

		public function has_conditions() {
			return true;
		}

		public function get_preview_as_query_args() {
			return array();
		}

	}
