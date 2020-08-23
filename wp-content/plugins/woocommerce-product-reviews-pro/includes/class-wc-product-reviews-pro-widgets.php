<?php
/**
 * WooCommerce Product Reviews Pro
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
 * Do not edit or add to this file if you wish to upgrade WooCommerce Product Reviews Pro to newer
 * versions in the future. If you wish to customize WooCommerce Social Login for your
 * needs please refer to http://docs.woocommerce.com/document/woocommerce-product-reviews-pro/ for more information.
 *
 * @author      SkyVerge
 * @copyright   Copyright (c) 2015-2020, SkyVerge, Inc.
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

defined( 'ABSPATH' ) or exit;

use SkyVerge\WooCommerce\PluginFramework\v5_5_0 as Framework;

/**
 * Widgets handler.
 *
 * @since 1.10.0.dev-3
 */
class WC_Product_Reviews_Pro_Widgets {


	/**
	 * Adds new widgets to WordPress.
	 *
	 * @since 1.10.0
	 */
	public function __construct() {

		// register widgets: make sure we're later than the WC widgets so we can extend that class
		add_action( 'widgets_init', array( $this, 'register_widgets' ), 15 );
	}


	/**
	 * Registers Product Reviews Pro widgets.
	 *
	 * @internal
	 *
	 * @since 1.10.0
	 */
	public function register_widgets() {

		$contribution_types = wc_product_reviews_pro_get_enabled_contribution_types();

		if ( ! empty( $contribution_types ) ) {

			// remove contribution comments, they won't get a widget
			if ( ( $key = array_search( 'contribution_comment', $contribution_types, true ) ) !== false ) {
				unset( $contribution_types[ $key ] );
			}

			// load widgets only for enabled contribution types
			foreach ( $contribution_types as $type ) {

				// only load widgets for the types we expect to find and have widgets for
				if ( ! in_array( $type, array( 'review', 'question', 'video', 'photo' ), true ) ) {
					continue;
				}

				require_once( wc_product_reviews_pro()->get_plugin_path() . "/includes/widgets/class-wc-product-reviews-pro-recent-{$type}s-widget.php" );

				$widget_class = 'WC_Product_Reviews_Pro_Recent_' . ucwords( $type ) . 's_Widget';
				register_widget( $widget_class );
			}
		}
	}


}
