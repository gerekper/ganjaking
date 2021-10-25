<?php
	/**
	 * Author Name: Liton Arefin
	 * Author URL: https://jeweltheme.com
	 * Date: 9/8/19
	 */

	namespace MasterAddons\Inc\Templates\Types;

	if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly


	if ( ! class_exists( 'Master_Addons_Structure_Section' ) ) {

		class Master_Addons_Structure_Section extends Master_Addons_Structure_Base {

			public function get_id() {
				return 'master_section';
			}

			public function get_single_label() {
				return __( 'Section', MELA_TD );
			}

			public function get_plural_label() {
				return __( 'Sections', MELA_TD );
			}

			public function get_sources() {
				return array( 'master-api' );
			}

			public function get_document_type() {
				return array(
					'class' => 'Master_Addons_Section_Document',
					'file'  => MELA_PLUGIN_PATH . '/inc/templates/documents/section.php',
				);
			}

			public function library_settings() {

				return array(
					'show_title'    => true,
					'show_keywords' => true,
				);

			}

		}

	}
