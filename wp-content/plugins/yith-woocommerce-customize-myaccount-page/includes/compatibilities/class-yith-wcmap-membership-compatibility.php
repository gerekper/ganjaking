<?php
/**
 * YITH WooCommerce Membership Compatibility Class
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH WooCommerce Customize My Account Page
 * @version 1.0.0
 */

defined( 'YITH_WCMAP' ) || exit;

if ( ! class_exists( 'YITH_WCMAP_Membership_Compatibility' ) ) {
	/**
	 * Class YITH_WCMAP_Membership_Compatibility
	 *
	 * @since 3.0.0
	 */
	class YITH_WCMAP_Membership_Compatibility extends YITH_WCMAP_Compatibility {

		/**
		 * Constructor
		 *
		 * @since 3.0.0
		 */
		public function __construct() {

			$title     = __( 'Membership Plans', 'yith-woocommerce-membership' );
			$shortcode = '[membership_history title="' . $title . '"]';

			$this->endpoint_key = 'yith-membership';
			$this->endpoint     = array(
				'slug'    => 'membership-plans',
				'label'   => __( 'Membership Plans', 'yith-woocommerce-customize-myaccount-page' ),
				'icon'    => 'list',
				/**
				 * APPLY_FILTERS: yith_wcmbs_membership_history_shortcode_in_my_account
				 *
				 * Filters the endpoint content for the integration with YITH WooCommerce Membership.
				 *
				 * @param string $shortcode Shortcode to use in the endpoint content.
				 * @param string $title     Shortcode title.
				 *
				 * @return string
				 */
				'content' => apply_filters( 'yith_wcmbs_membership_history_shortcode_in_my_account', $shortcode, $title ),
			);

			// Register endpoint.
			$this->register_endpoint();

			// Add item option.
			add_filter( 'yith_wcmap_items_options_fields', array( $this, 'add_item_option' ) );
			// Check if item is visible by membership plans.
			add_filter( 'yith_wcmap_is_menu_item_visible', array( $this, 'check_item_visibility' ), 10, 3 );

			// Handle compatibility.
			add_action( 'template_redirect', array( $this, 'hooks' ), 5 );
		}

		/**
		 * Compatibility hooks and filters
		 *
		 * @since 3.0.0
		 */
		public function hooks() {
			if ( class_exists( 'YITH_WCMBS_Frontend_Premium' ) ) {
				// Remove content in my account.
				remove_action( 'woocommerce_after_my_account', array( YITH_WCMBS_Frontend(), 'print_membership_history' ), 10 );
				remove_action( 'woocommerce_account_dashboard', array( YITH_WCMBS_Frontend(), 'print_membership_history' ), 10 );
			}
		}

		/**
		 * Add item option. Let's filter item base on membership plan
		 *
		 * @since 3.0.0
		 * @param array $options The endpoint options array.
		 * @return array
		 */
		public function add_item_option( $options ) {
			if ( class_exists( 'YITH_WCMBS_Manager' ) ) {
				$membership_plans = YITH_WCMBS_Manager()->get_plans();
			}

			if ( empty( $membership_plans ) ) {
				return $options;
			}
			// Create an array of plans to be used as option.
			$plans = array();
			foreach ( $membership_plans as $plan ) {
				if ( - 1 === version_compare( YITH_WCMBS_VERSION, '1.4.0' ) ) {
					$plans[ $plan->ID ] = $plan->post_title;
				} else {
					$plans[ $plan->get_id() ] = $plan->get_name();
				}
			}

			foreach ( $options as $type => &$fields ) {
				$new_fields = array();
				foreach ( $fields as $key => $field ) {
					if ( 'visibility' === $key ) {
						$field['options']['plans'] = __( 'Only users of a specific membership plan', 'yith-woocommerce-customize-myaccount-page' );
					} elseif ( 'usr_roles' === $key ) {
						// Append membership option.
						$new_fields['membership_plans'] = array(
							'type'     => 'select',
							'label'    => __( 'Membership plans', 'yith-woocommerce-customize-myaccount-page' ),
							'desc'     => __( 'Restrict endpoint visibility to users who are purchased following membership plan(s)', 'yith-woocommerce-customize-myaccount-page' ),
							'options'  => $plans,
							'multiple' => true,
							'deps'     => array(
								'ids'    => 'visibility',
								'values' => 'plans',
							),
						);
					}

					$new_fields[ $key ] = $field;
				}
				$fields = $new_fields;
			}

			return $options;
		}

		/**
		 * Check if an item is visible by membership plans
		 *
		 * @since 3.0.0
		 * @param boolean $visible True if item is visible, false otherwise.
		 * @param string  $item The item.
		 * @param array   $options The item options.
		 * @return boolean
		 */
		public function check_item_visibility( $visible, $item, $options ) {
			if ( isset( $options['visibility'] ) && 'plans' === $options['visibility'] && isset( $options['membership_plans'] ) && $this->hide_by_membership_plan( $options['membership_plans'] ) ) {
				return false;
			}

			return $visible;
		}

		/**
		 * Check if current customer own an item plan
		 *
		 * @since 3.0.0
		 * @param array $membership_plans An array of membership plans.
		 * @return bool
		 */
		protected function hide_by_membership_plan( $membership_plans ) {
			// Return if $roles is empty.
			if ( ! class_exists( 'YITH_WCMBS_Members' ) ) {
				return false;
			}

			$user_id    = get_current_user_id();
			$member     = YITH_WCMBS_Members()->get_member( $user_id );
			$user_plans = $member->get_membership_plans( array( 'return' => 'complete' ) );

			foreach ( $user_plans as $plan ) {
				if ( in_array( $plan->plan_id, $membership_plans ) ) { // phpcs:ignore
					return false;
				}
			}

			return true;
		}
	}
}
