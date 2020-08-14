<?php
/*
 * This file belongs to the YIT Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */
if ( ! defined ( 'ABSPATH' ) ) {
	exit( 'Direct access forbidden.' );
}

if( ! class_exists( 'YITH_Frontend_Manager_Section_Logout' ) ) {

	class YITH_Frontend_Manager_Section_Logout extends YITH_WCFM_Section {

		/**
		 * Constructor method
		 *
		 * @return \YITH_Frontend_Manager_Section
		 * @since 1.0.0
		 */
		public function __construct() {
			$this->id                    = 'user-logout';
			$this->_default_section_name = _x( 'Logout', '[Frontend]: Dashboard menu item', 'yith-frontend-manager-for-woocommerce' );
			add_filter( 'yith_wcfm_get_sections_before_print_navigation', array( $this, 'set_logout_last_menu_item' ), 99 );

            add_action( 'template_redirect', array( $this, 'logout_redirect' ) );

			parent::__construct();
		}

		/**
		 * Print shortcode function
		 *
		 * @author Andrea Grillo    <andrea.grillo@yithemes.com>
		 * @return void
		 * @since  1.0.0
		 */
		public function print_shortcode( $atts = array(), $content = '', $tag ) {
			return false;
		}

		/* === SECTION METHODS === */

		/**
		 * Make logout redirect
		 *
		 * @author Andrea Grillo    <andrea.grillo@yithemes.com>
		 * @return void
		 * @since  1.0.0
		 */
		public function logout_redirect(){
		    if( $this->is_current() ){
		    	$redirect = str_replace( '&amp;', '&', wp_logout_url( esc_attr( yith_wcfm_get_main_page_url() ) , 301 ) );
		    	$redirect = apply_filters( 'yith_wcfm_logout_redirect_url', $redirect, $this );
		        wp_redirect( $redirect );
            }
        }

		/**
		 * Set dahboard to first menu item
		 *
		 * @author Andrea Grillo 	<andrea.grillo@yithemes.com>
		 * @return array Sections array for navigation menu
		 * @since 1.0.0
		 */
		final public function set_logout_last_menu_item( $sections ) {
			$key  = $this->id;
			if( isset( $sections[ $key ] ) ){
				$temp = array( $key => $sections[ $key ] );
				unset( $sections[ $key ] );
				$sections = $sections + $temp;
			}

			return $sections;
		}
	}
}

