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
 * Query and rewrite rules handler
 *
 * @since 1.6.0
 */
class WC_Product_Reviews_Pro_Query {


	/**
	 * Constructor
	 *
	 * @since 1.6.0
	 */
	public function __construct() {

		// create a new endpoint
		add_action( 'init',       array( $this, 'add_endpoint' ) );
		add_filter( 'query_vars', array( $this, 'add_query_vars' ), 0 );
	}


	/**
	 * Register new Contributions endpoint to use inside My Account page
	 *
	 * @link https://developer.wordpress.org/reference/functions/add_rewrite_endpoint/
	 *
	 * @since 1.6.0
	 */
	public function add_endpoint() {

		add_rewrite_endpoint( 'contributions', EP_ROOT | EP_PAGES );
	}


	/**
	 * Add new query var for Contributions endpoint
	 *
	 * @since 1.6.0
	 * @param array $vars Query vars
	 * @return array
	 */
	public function add_query_vars( $vars ) {

		$vars[] = 'contributions';

		return $vars;
	}


}
