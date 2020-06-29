<?php
/*
 * This file belongs to the YIT Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit( 'Direct access forbidden.' );
}

if ( ! class_exists( 'YITH_Frontend_Manager_Section_Premium' ) ) {

	class YITH_Frontend_Manager_Section_Premium extends YITH_Frontend_Manager_Section{

		/**
		 * Constructor method
		 *
		 * @return \YITH_Frontend_Manager_Section
		 * @since 1.0.0
		 */
		public function __construct() {
			/* Premium Options */
			add_filter( 'yith_wcfm_admin_tabs', array( $this, 'add_premium_panel_options' ) );
			add_filter( 'yith_wcfm_sections_options', array( $this, 'sections_options' ) );

			parent::__construct();
		}

		/**
		 * Create Section class alias to dynamic class extends
		 *
		 * @since 1.0
		 * @author Andrea Grillo <andrea.grillo@yithemes.com>
		 * @retun void
		 * @access public static
		 */
		public static function section_class_alias() {
			if( ! class_exists( 'YITH_WCFM_Section' ) ){
				class_alias( __CLASS__, 'YITH_WCFM_Section' );
			}
		}

		/**
		 * Add premium options for sections tab
		 *
		 * @author   Andrea Grillo <andrea.grillo@yithemes.com>
		 * @since    1.0.0
		 *
		 * @param $options
		 *
		 * @return array section tab option
		 */
		public function sections_options( $options ){

			$section_id = yith_wcfm_get_section_enabled_id_from_object( 'section_id', $this );
			$section_settings = array(
				"{$section_id}_options_start" => array( 'type' => 'sectionstart' ),

				"{$section_id}_options_title"    => array( 'type' => 'title' ),

				"{$section_id}_section_name" => array(
					'type'    => apply_filters( 'yith_wcfm_section_option_type', 'checkbox', $this ),
					'id'      => yith_wcfm_get_section_enabled_id_from_object( 'option_id', $this ),
					'title'   => apply_filters( 'yith_wcfm_section_option_title', $this->_default_section_name, $this ),
					'default' => 'yes',
				),

				"{$section_id}_options_end" => array( 'type' => 'sectionend' ),
			);

			return array_merge( $options, $section_settings );
		}

		/**
		 * Add premium tab options
		 *
		 * @author   Andrea Grillo <andrea.grillo@yithemes.com>
		 * @since    1.0.0
		 *
		 * @param $admin_tabs
		 *
		 * @return array admn tabs option
		 */
		public function add_premium_panel_options( $admin_tabs ) {

			$sections_admin_tab = apply_filters( 'yith_wcfm_admin_premium_tabs', array(
					'sections'  => __( 'Sections', 'yith-frontend-manager-for-woocommerce' ),
				)
			);
			$endpoints_admin_tab = array();

			if( isset( $admin_tabs['endpoints'] ) ){
				$endpoints_admin_tab['endpoints'] =  $admin_tabs['endpoints'];
				unset( $admin_tabs['endpoints'] );
			}

			return array_merge( $admin_tabs, $sections_admin_tab, $endpoints_admin_tab );
		}
	}
}

add_action( 'yith_wcfm_after_load_common_classes', 'YITH_Frontend_Manager_Section_Premium::section_class_alias' );