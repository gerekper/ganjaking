<?php
/**
 * This file belongs to the YIT Plugin Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

if ( ! function_exists( 'ywfp_get_option' ) ) {

	/**
	 * Get plugin option
	 *
	 * @param   $option  string
	 * @param   $default mixed
	 *
	 * @return  mixed
	 * @since   1.0.0
	 *
	 * @author  Alberto Ruggiero
	 */
	function ywfp_get_option( $option, $default = false ) {
		return YITH_FAQ_Settings::get_instance()->get_option( 'faq', $option, $default );
	}

}