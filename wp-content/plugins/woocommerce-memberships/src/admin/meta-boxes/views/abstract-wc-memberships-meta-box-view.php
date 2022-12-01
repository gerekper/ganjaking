<?php
/**
 * WooCommerce Memberships
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
 * Do not edit or add to this file if you wish to upgrade WooCommerce Memberships to newer
 * versions in the future. If you wish to customize WooCommerce Memberships for your
 * needs please refer to https://docs.woocommerce.com/document/woocommerce-memberships/ for more information.
 *
 * @author    SkyVerge
 * @copyright Copyright (c) 2014-2022, SkyVerge, Inc. (info@skyverge.com)
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

use SkyVerge\WooCommerce\PluginFramework\v5_10_13 as Framework;

defined( 'ABSPATH' ) or exit;

/**
 * Interface for Meta Box Views
 * to let meta boxes pass callable methods and properties to HTML views
 *
 * @since 1.7.0
 */
abstract class WC_Memberships_Meta_Box_View {


	/** @var \WC_Memberships_Meta_Box instance */
	protected $meta_box;

	/** @var \WP_Post instance */
	protected $post;

	/** @var \WC_Memberships_Membership_Plan instance */
	protected $membership_plan;

	/** @var \WC_Memberships_User_Membership instance */
	protected $user_membership;

	/** @var \WC_Product instance */
	protected $product;

	/** @var \WC_Order instance */
	protected $order;

	/** @var \WP_User instance */
	protected $user;

	/** @var \WC_Memberships_Membership_Plan_Rule instance */
	protected $rule;


	/**
	 * Init properties passed from current meta box
	 *
	 * @since 1.7.0
	 * @param null|\WC_Memberships_Meta_Box $meta_box Meta Box instance
	 * @param null|\WC_Memberships_Membership_Plan_Rule $rule Rule instance, optional
	 */
	public function __construct( $meta_box = null, $rule = null ) {

		$this->meta_box            = $meta_box;                         // the current meta box instance

		if ( $this->meta_box instanceof \WC_Memberships_Meta_Box ) {

			$this->post            = $meta_box->get_post();             // used in all contexts
			$this->product         = $meta_box->get_product();          // used in product edit screen context
			$this->membership_plan = $meta_box->get_membership_plan();  // used in membership plan edit screen context
			$this->user_membership = $meta_box->get_user_membership();  // used in user membership edit screen context
			$this->order           = $meta_box->get_order();            // used in user membership edit screen context
			$this->user            = $meta_box->get_user();             // used in user membership edit screen context
		}

		$this->rule                = $rule;                             // used in rules view loops within tabular views
	}


	/**
	 * HTML Output
	 *
	 * @since 1.7.0
	 * @param array $args Optional, used in some individual rule views
	 */
	abstract public function output( $args = array() );


	/**
	 * Get rule index
	 *
	 * Used in rule views
	 *
	 * @since 1.7.0
	 * @param array $args
	 * @return string|int
	 */
	protected function get_rule_index( $args ) {
		return isset( $args['index'] ) ? $args['index'] : '';
	}


}
