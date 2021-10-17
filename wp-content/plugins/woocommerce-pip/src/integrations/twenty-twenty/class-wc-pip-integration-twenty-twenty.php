<?php
/**
 * WooCommerce Print Invoices/Packing Lists
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
 * Do not edit or add to this file if you wish to upgrade WooCommerce Print
 * Invoices/Packing Lists to newer versions in the future. If you wish to
 * customize WooCommerce Print Invoices/Packing Lists for your needs please refer
 * to http://docs.woocommerce.com/document/woocommerce-print-invoice-packing-list/
 *
 * @author    SkyVerge
 * @copyright Copyright (c) 2011-2021, SkyVerge, Inc. (info@skyverge.com)
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

defined( 'ABSPATH' ) or exit;

/**
 * Integration class for the Twenty Twenty theme.
 *
 * Although it acts like an integration class, it is more to a compatibility class to add a workaround to the customizer
 * page, which breaks due to javascript issues caused by Twenty Twenty theme.
 *
 * @see https://core.trac.wordpress.org/ticket/48890
 *
 * TODO: Remove this integration class once the ticket above is updated with a fix release {AC 2021-01-22}
 *
 * @since 3.11.1
 */
class WC_PIP_Integration_Twenty_Twenty {


	/**
	 * Add actions.
	 *
	 * @since 3.11.1
	 */
	public function __construct() {

		add_action( 'init', [ $this, 'remove_remove_sections_callback' ] );

		add_action( 'customize_controls_head', [ $this, 'hide_non_pip_sections' ] );
	}


	/**
	 * Removes the remove_sections callback for the customize_register filter.
	 *
	 * @internal
	 *
	 * @since 3.11.1
	 */
	public function remove_remove_sections_callback() {

		remove_filter( 'customize_register', [ wc_pip()->get_customizer_instance(), 'remove_sections' ], 150 );
	}


	/**
	 * Hides non-PIP sections if on PIP customizer.
	 *
	 * @internal
	 *
	 * @since 3.11.1
	 */
	public function hide_non_pip_sections() {
		global $wp_customize;

		$sections   = $wp_customize instanceof \WP_Customize_Manager ? $wp_customize->sections() : null;
		$customizer = wc_pip()->get_customizer_instance();

		// conditions to hide all non-PIP sections
		if ( $customizer && ! empty( $sections ) && isset( $_GET[ $customizer->get_customizer_trigger() ] ) ) {

			$css = '';

			// hide all non-PIP sections
			foreach ( array_keys( $sections ) as $section_id  ) {

				if ( 0 !== strpos( $section_id, 'wc_pip_' ) ) {
					$css .= '#accordion-section-' . $section_id . ' { display: none !important; }';
				}
			}

			echo '<style>' . $css . '</style>';
		}
	}


}
