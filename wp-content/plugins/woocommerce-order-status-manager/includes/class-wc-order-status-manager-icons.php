<?php
/**
 * WooCommerce Order Status Manager
 *
 * This source file is subject to the GNU General Public License v3.0
 * that is bundled with this package in the file license.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.html
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@skyverge.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade WooCommerce Order Status Manager to newer
 * versions in the future. If you wish to customize WooCommerce Order Status Manager for your
 * needs please refer to http://docs.woocommerce.com/document/woocommerce-order-status-manager/ for more information.
 *
 * @author      SkyVerge
 * @copyright   Copyright (c) 2015-2020, SkyVerge, Inc. (info@skyverge.com)
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

defined( 'ABSPATH' ) or exit;

use SkyVerge\WooCommerce\PluginFramework\v5_5_0 as Framework;

/**
 * Order Status Manager Icons class
 *
 * This class handles the setup and extraction of font icons
 *
 * @since 1.0.0
 */
class WC_Order_Status_Manager_Icons {


	/**
	 * Constructor
	 *
	 * @since 1.0.0
	 */
	public function __construct() {

		// add a new image size for icons
		add_image_size( 'wc_order_status_icon', 32, 32, true );
	}


	/**
	 * Extract icon font classes & glyphs
	 *
	 * @since 1.0.0
	 * @param string $path Absolute path to CSS file
	 * @param string $class_prefix The class prefix that every icon class uses.
	 *                              If icon classes are named like `dashicons-admin`,
	 *                              then the prefix would be `dashicons`.
	 * @return array Associative array with classes and their glyphs
	 */
	public function extract_icon_glyphs( $path, $class_prefix ) {

		$css = file_get_contents( $path );

		$class_pattern = '/(?<=\.)(?<class>' . $class_prefix . '[-\w]+)(?::before\s*{\s*content:\s*")(?<glyph>\\\\\w+)(?=")/';

		$matches = array();
		$results = array();

		preg_match_all( $class_pattern, $css, $matches );

		// Restructure matches into an associative array like $class => $glyph
		if ( ! empty( $matches ) && isset( $matches['class'] ) && ! empty( $matches['class'] ) ) {

			foreach ( $matches['class'] as $key => $class ) {

				$results[ $class ] = $matches['glyph'][ $key ];
			}
		}

		return $results;
	}


	/**
	 * Update icon options
	 *
	 * Extracts icons and their glyphs from font icon stylesheets and
	 * saves the resulting array as an option to wp_options table
	 *
	 * @since 1.0.0
	 */
	public function update_icon_options() {

		$options = array();

		// Dashicons, included in WP
		$options['Dashicons'] = array();
		$glyphs = $this->extract_icon_glyphs( ABSPATH . WPINC . '/css/dashicons.css', 'dashicons' );

		foreach ( $glyphs as $class => $glyph ) {

			// prepend each icon class with the generic `dashicons` class
			$options['Dashicons'][ 'dashicons ' . $class ] = $glyph;
		}

		$plugin_path = wc_order_status_manager()->get_plugin_path();

		// WooCommerce, included in WC, but classes need to be loaded from our plugin
		$options['WooCommerce'] = $this->extract_icon_glyphs( $plugin_path . '/assets/css/woocommerce-font-classes.css', 'wcicon' );

		// FontAwesome
		$options['FontAwesome'] = array();
		$glyphs = $this->extract_icon_glyphs( $plugin_path . '/assets/css/font-awesome.css', 'fa' );

		foreach ( $glyphs as $class => $glyph ) {

			// prepend each icon class with the generic `fa` class
			$options['FontAwesome'][ 'fa ' . $class ] = $glyph;
		}

		// update the option, specifying autoload=no
		delete_option( 'wc_order_status_manager_icon_options' );
		add_option( 'wc_order_status_manager_icon_options', $options, '', 'no' );
	}


	/**
	 * Return a list of available icon options
	 *
	 * Icons are grouped by icon fonts in an associative array
	 *
	 * @since 1.0.0
	 * @return array Array of icon options, grouped by font name
	 */
	public function get_icon_options() {

		/**
		 * Filter the list of available icon options
		 *
		 * Icons are grouped by icon fonts in an associative array
		 *
		 * @since 1.0.0
		 * @param array $icon_options Array of icon options, grouped by font name
		 */
		return apply_filters( 'wc_order_status_manager_icon_options', get_option( 'wc_order_status_manager_icon_options' ) );
	}


	/**
	 * Get icon font and glyph based on class
	 *
	 * @since 1.0.0
	 * @param string $icon_class
	 * @return null|array
	 */
	public function get_icon_details( $icon_class ) {

		$result = array();

		foreach ( $this->get_icon_options() as $font => $icons ) {

			foreach( $icons as $class => $glyph ) {

				if ( $class == $icon_class ) {

					$result['font']  = $font;
					$result['glyph'] = $glyph;
					break 2;
				}
			}
		}

		return ! empty( $result ) ? $result : null;
	}


	/**
	 * Determine best text color based on input background color
	 *
	 * Returns either white or black
	 *
	 * @since 1.0.0
	 * @param string $hexcolor HEX color code (for example: #000000)
	 * @return string
	 */
	public function get_contrast_text_color( $hexcolor ) {

		$hexcolor = str_replace( '#', '', $hexcolor );

		$r = hexdec( substr( $hexcolor, 0, 2 ) );
		$g = hexdec( substr( $hexcolor, 2, 2 ) );
		$b = hexdec( substr( $hexcolor, 4, 2 ) );

		$yiq = ( ( $r * 299) + ($g * 587) + ( $b * 114 ) ) / 1000;

		return ( $yiq >= 160 ) ? 'black' : 'white';
	}


}
