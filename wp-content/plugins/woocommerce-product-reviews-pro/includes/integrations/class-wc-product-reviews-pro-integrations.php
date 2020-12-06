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
 * Class handling integrations with other plugins:
 *
 * - Jetpack
 * - WooCommerce Points And Rewards
 * - WooCommerce Product Vendors
 * - WooCommerce Tab Manager
 *
 * @since 1.10.0
 */
class WC_Product_Reviews_Pro_Integrations {


	/* @var null|\WC_Product_Reviews_Pro_Integration_Points_And_Rewards instance */
	private $points_and_rewards;

	/** @var bool whether WooCommerce Points And Rewards is active */
	private $is_points_and_rewards_active;

	/* @var null|\WC_Product_Reviews_Pro_Integration_Product_Vendors instance */
	private $product_vendors;

	/** @var bool whether WooCommerce Product Vendors is active */
	private $is_product_vendors_active;

	/** @var null|\WC_Product_Reviews_Pro_Integration_Tab_Manager instance */
	private $tab_manager;

	/** @var bool whether WooCommerce Tab Manager is active */
	private $is_tab_manager_active;

	/** @var null|\WC_Product_Reviews_Pro_Integration_Jetpack instance */
	private $jetpack;

	/** @var bool whether Jetpack is active */
	private $is_jetpack_active;


	/**
	 * Loads integrations.
	 *
	 * @since 1.10.0
	 */
	public function __construct() {

		// Jetpack
		if ( $this->is_jetpack_active() ) {
			$this->jetpack = wc_product_reviews_pro()->load_class( '/includes/integrations/jetpack/class-wc-product-reviews-pro-integration-jetpack.php', 'WC_Product_Reviews_Pro_Integration_Jetpack' );
		}

		// Points and Rewards
		if ( $this->is_points_and_rewards_active() ) {
			$this->points_and_rewards = wc_product_reviews_pro()->load_class( '/includes/integrations/woocommerce-points-and-rewards/class-wc-product-reviews-pro-integration-points-and-rewards.php', 'WC_Product_Reviews_Pro_Integration_Points_And_Rewards' );
		}

		// Product Vendors
		if ( $this->is_product_vendors_active() ) {
			$this->product_vendors = wc_product_reviews_pro()->load_class( '/includes/integrations/woocommerce-product-vendors/class-wc-product-reviews-pro-integration-product-vendors.php', 'WC_Product_Reviews_Pro_Integration_Product_Vendors' );
		}

		// Tab Manager
		if ( $this->is_tab_manager_active() ) {
			$this->tab_manager = wc_product_reviews_pro()->load_class( '/includes/integrations/woocommerce-tab-manager/class-wc-product-reviews-pro-integration-tab-manager.php', 'WC_Product_Reviews_Pro_Integration_Tab_Manager' );
		}
	}


	/**
	 * Initializes Jilt Promotions handlers.
	 *
	 * We need to instantiate Jilt Promotion handlers after plugins_loaded to ensure all necessary classes are loaded first.
	 *
	 * Consider creating a Jilt Promotions integrations handler if we start adding more prompt handlers here.
	 *
	 * TODO: remove this method by version 2.0.0 or by 2021-11-16 {DM 2020-11-16}
	 *
	 * @internal
	 *
	 * @since 1.5.15-dev.1
	 * @deprecated 1.17.0
	 */
	public function load_jilt_promotions_handlers() {

		wc_deprecated_function( __METHOD__, '1.17.0' );
	}


	/**
	 * Returns the Jetpack integration instance.
	 *
	 * @since 1.12.0
	 *
	 * @return null|\WC_Product_Reviews_Pro_Integration_Jetpack
	 */
	public function get_jetpack_instance() {

		return $this->jetpack;
	}


	/**
	 * Returns the Woocommerce Points And Rewards integration instance.
	 *
	 * @since 1.10.0
	 *
	 * @return null|\WC_Product_Reviews_Pro_Integration_Points_And_Rewards
	 */
	public function get_points_and_rewards_instance() {

		return $this->points_and_rewards;
	}


	/**
	 * Returns the WooCommerce Product Vendors instance.
	 *
	 * @since 1.10.0
	 *
	 * @return null|\WC_Product_Reviews_Pro_Integration_Product_Vendors
	 */
	public function get_product_vendors_instance() {

		return $this->product_vendors;
	}


	/**
	 * Returns the WooCommerce Tab Manager instance.
	 *
	 * @since 1.10.0
	 *
	 * @return null|\WC_Product_Reviews_Pro_Integration_Tab_Manager
	 */
	public function get_tab_manager_instance() {

		return $this->tab_manager;
	}


	/**
	 * Checks if Jetpack is active.
	 *
	 * @since 1.12.0
	 *
	 * @return bool
	 */
	public function is_jetpack_active() {

		if ( null === $this->is_jetpack_active ) {
			$this->is_jetpack_active = wc_product_reviews_pro()->is_plugin_active( 'jetpack.php' );
		}

		return $this->is_jetpack_active;
	}


	/**
	 * Checks if WooCommerce Points And Rewards is active.
	 *
	 * @since 1.10.0
	 *
	 * @return bool
	 */
	public function is_points_and_rewards_active() {

		if ( null === $this->is_points_and_rewards_active ) {
			$this->is_points_and_rewards_active = wc_product_reviews_pro()->is_plugin_active( 'woocommerce-points-and-rewards.php' );
		}

		return $this->is_points_and_rewards_active;
	}


	/**
	 * Checks if Woocommerce Product Vendors is active.
	 *
	 * @since 1.10.0
	 *
	 * @return bool
	 */
	public function is_product_vendors_active() {

		if ( null === $this->is_product_vendors_active ) {
			$this->is_product_vendors_active = wc_product_reviews_pro()->is_plugin_active( 'woocommerce-product-vendors.php' );
		}

		return $this->is_product_vendors_active;
	}


	/**
	 * Checks if WooCommerce Tab Manager is active.
	 *
	 * @since 1.10.0
	 *
	 * @return bool
	 */
	public function is_tab_manager_active() {

		if ( null === $this->is_tab_manager_active ) {
			$this->is_tab_manager_active = wc_product_reviews_pro()->is_plugin_active( 'woocommerce-tab-manager.php' );;
		}

		return $this->is_tab_manager_active;
	}


}
