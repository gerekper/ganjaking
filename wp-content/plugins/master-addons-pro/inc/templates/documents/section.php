<?php
	/**
	 * Author Name: Liton Arefin
	 * Author URL: https://jeweltheme.com
	 * Date: 9/8/19
	 */

	namespace MasterAddons\Inc\Templates\Documents;

	if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

	class Master_Addons_Section_Document extends Master_Addons_Document_Base {

		public function get_name() {
			return 'master_page';
		}

		public static function get_title() {
			return __( 'Section', MELA_TD );
		}

		public function has_conditions() {
			return false;
		}

	}