<?php
	/**
	 * Author Name: Liton Arefin
	 * Author URL: https://jeweltheme.com
	 * Date: 9/8/19
	 */

	namespace MasterAddons\Inc\Templates\Types;

	if ( ! defined('ABSPATH') ) exit; // No access of directly access

	if ( ! class_exists( 'Master_Addons_Structure_Base' ) ) {


		abstract class Master_Addons_Structure_Base {

			abstract public function get_id();

			abstract public function get_single_label();

			abstract public function get_plural_label();

			abstract public function get_sources();

			abstract public function get_document_type();

			public function is_location() {
				return false;
			}


			public function location_name() {
				return '';
			}

			public function library_settings() {

				return array(
					'show_title'    => true,
					'show_keywords' => true,
				);

			}

		}

	}
