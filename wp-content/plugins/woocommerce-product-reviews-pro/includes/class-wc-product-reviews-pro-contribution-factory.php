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
 * Contribution Factory class.
 *
 * The pattern in this class is similar to other main objects handlers found in other SkyVerge plugins.
 * The main scope is assisting with returning the right contribution type's object given a contribution or comment.
 *
 * @since 1.0.0
 */
class WC_Product_Reviews_Pro_Contribution_Factory {


	/**
	 * Loads contribution types.
	 *
	 * @since 1.10.0
	 */
	public function __construct() {

		$plugin_path = wc_product_reviews_pro()->get_plugin_path();

		// contribution types handler
		require_once( $plugin_path . '/includes/class-wc-product-reviews-pro-contribution-type.php' );
		// contribution flag object
		require_once( $plugin_path . '/includes/class-wc-product-reviews-pro-contribution-flag.php' );
		// contribution types objects
		require_once( $plugin_path . '/includes/contributions/abstract-wc-contribution.php' );
		require_once( $plugin_path . '/includes/contributions/class-wc-contribution-review.php' );
		require_once( $plugin_path . '/includes/contributions/class-wc-contribution-question.php' );
		require_once( $plugin_path . '/includes/contributions/class-wc-contribution-video.php' );
		require_once( $plugin_path . '/includes/contributions/class-wc-contribution-photo.php' );
		require_once( $plugin_path . '/includes/contributions/class-wc-contribution-comment.php' );

		// toggle WooCommerce review rating enabled status if reviews are disallowed
		add_filter( 'pre_option_woocommerce_enable_review_rating', array( $this, 'is_review_rating_enabled' ) );

		// forces contribution comments enabled for admins if related setting is enabled
		add_filter( 'wc_product_reviews_pro_enabled_contribution_types', array( $this, 'enable_admin_replies' ) );
	}


	/**
	 * Filters the woocommerce_enable_review_rating option.
	 *
	 * Checks if reviews have been enabled. If disabled, returns 'no'.
	 * This is intended as a filter callback to alter a WooCommerce core option output.
	 *
	 * @internal
	 *
	 * @since 1.0.0
	 *
	 * @param string $enabled either 'yes' or 'no' (default)
	 * @return string 'no' if reviews are disabled, pass-thru otherwise
	 */
	public function is_review_rating_enabled( $enabled ) {

		return ! in_array( 'review', wc_product_reviews_pro_get_enabled_contribution_types(), true ) ? 'no' : $enabled;
	}


	/**
	 * Returns all contribution types.
	 *
	 * @since 1.10.0
	 *
	 * @return string[] array of contribution type names, ie ['review', 'question', 'video', 'photo', 'contribution_comment']
	 */
	public function get_contribution_types() {

		/**
		 * Filters the contribution types.
		 *
		 * @since 1.0.0
		 *
		 * @param string[] $contribution_types the contribution types
		 */
		return (array) apply_filters( 'wc_product_reviews_pro_contribution_types', array(
			'review',
			'question',
			'video',
			'photo',
			'contribution_comment',
		) );
	}


	/**
	 * Returns enabled contribution types.
	 *
	 * @since 1.10.0
	 *
	 * @return string[] array of contribution type names, ie ['review', 'question', 'video', 'photo', 'contribution_comment']
	 */
	public function get_enabled_contribution_types() {

		if ( 'all' === get_option( 'wc_product_reviews_pro_enabled_contribution_types' ) ) {
			$types = $this->get_contribution_types();
		} else {
			$types = (array) get_option( 'wc_product_reviews_pro_specific_enabled_contribution_types', array() );
		}

		/**
		 * Filters enabled contribution types.
		 *
		 * @since 1.10.0
		 *
		 * @param string[] array of contribution types
		 */
		return (array) apply_filters( 'wc_product_reviews_pro_enabled_contribution_types', $types );
	}


	/**
	 * Force enables comment contributions for admins if the related setting is enabled.
	 *
	 * @internal
	 *
	 * @since 1.10.0
	 *
	 * @param string[] $types array of contribution types
	 * @return string[]
	 */
	public function enable_admin_replies( $types ) {

		if ( is_array( $types ) && ! in_array( 'contribution_comment', $types, true ) && 'yes' === get_option( 'wc_product_reviews_pro_admins_can_always_reply', 'no' ) && current_user_can( 'manage_woocommerce' ) ) {

			$types[] = 'contribution_comment';
		}

		return $types;
	}


	/**
	 * Returns a contribution object.
	 *
	 * Wraps a comment with its contribution type object.
	 *
	 * @since 1.10.0
	 *
	 * @param null|\WC_Contribution|\WP_Comment|int $the_contribution a contribution or comment object or ID (by default will get the current comment)
	 * @param array $args optional arguments
	 * @return null|\WC_Contribution the corresponding contribution object or null if undetermined
	 */
	public function get_contribution( $the_contribution = null, $args = array() ) {
		global $comment;

		// false is checked for legacy here
		if ( null === $the_contribution || false === $the_contribution ) {
			$the_contribution = $comment;
		} elseif ( is_numeric( $the_contribution ) ) {
			$the_contribution = get_comment( $the_contribution );
		}

		$contribution = null;

		if ( $the_contribution instanceof \WC_Contribution ) {

			$contribution = $the_contribution;

		} elseif ( $the_contribution instanceof \WP_Comment || ( is_object( $the_contribution ) && isset( $the_contribution->comment_ID, $the_contribution->comment_type ) ) ) {

			$comment_id   = absint( $the_contribution->comment_ID );
			$comment_type = $the_contribution->comment_type;
			// create a WC coding standards compliant class name e.g. WC_Contribution_Type_Class instead of WC_Contribution_type-class
			$classname    = 'WC_Contribution_' . implode( '_', array_map( 'ucfirst', explode( '-', $comment_type ) ) );

			/**
			 * Filters a class name so that the class can be overridden if extended.
			 *
			 * @since 1.0.0
			 *
			 * @param string $classname the class name
			 * @param string $comment_type the comment type
			 * @param int $comment_id the comment ID
			 */
			$classname = apply_filters( 'woocommerce_contribution_class', $classname, $comment_type, $comment_id );

			if ( ! class_exists( $classname ) ) {
				$classname = 'WC_Contribution_Review';
			}

			$contribution = new $classname( $the_contribution, $args );
		}

		return $contribution;
	}


}
