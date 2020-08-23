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
 * versions in the future. If you wish to customize WooCommerce Product Reviews Pro for your
 * needs please refer to http://docs.woocommerce.com/document/woocommerce-product-reviews-pro/ for more information.
 *
 * @author    SkyVerge
 * @copyright Copyright (c) 2015-2020, SkyVerge, Inc.
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

defined( 'ABSPATH' ) or exit;

use SkyVerge\WooCommerce\PluginFramework\v5_5_0 as Framework;

/**
 * WooCommerce Tab Manager integration.
 *
 * @since 1.10.0
 */
class WC_Product_Reviews_Pro_Integration_Tab_Manager {


	/**
	 * Hooks into plugin.
	 *
	 * @since 1.10.0
	 */
	public function __construct() {

		// correct the review count in the Reviews tab title
		add_filter( 'wc_tab_manager_reviews_tab_title_review_count', array( $this, 'set_reviews_tab_title_review_count' ), 10, 2 );
	}


	/**
	 * Filters the Tab Manager Reviews tab title review count.
	 *
	 * @since 1.10.0
	 *
	 * @param int $review_count the review count
	 * @param \WC_Product $product product object
	 * @return int the filtered review count
	 */
	public function set_reviews_tab_title_review_count( $review_count, $product ) {

		// get enabled contribution types
		$enabled_contribution_types = wc_product_reviews_pro_get_enabled_contribution_types();

		// do not take contribution_comments into account
		if ( ( $key = array_search( 'contribution_comment', $enabled_contribution_types, false ) ) !== false ) {
			unset( $enabled_contribution_types[ $key ] );
		}

		return wc_product_reviews_pro_get_contributions_count( $product, $enabled_contribution_types );
	}


}
