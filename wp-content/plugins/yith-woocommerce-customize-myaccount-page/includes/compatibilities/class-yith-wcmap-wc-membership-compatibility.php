<?php
/**
 * WC Membership Compatibility Class
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH WooCommerce Customize My Account Page
 * @version 1.0.0
 */

defined( 'YITH_WCMAP' ) || exit; // Exit if accessed directly.

if ( ! class_exists( 'YITH_WCMAP_WC_Membership_Compatibility' ) ) {
	/**
	 * Class YITH_WCMAP_WC_Membership_Compatibility
	 *
	 * @since 2.3.0
	 */
	class YITH_WCMAP_WC_Membership_Compatibility extends YITH_WCMAP_Compatibility {

		/**
		 * Main WC Members Area instance
		 *
		 * @var WC_Memberships_Members_Area
		 */
		public $members_area = null;

		/**
		 * Current membership id
		 *
		 * @var false|WC_Memberships_User_Membership current user membership object
		 */
		public $current_membership = null;

		/**
		 * Membership endpoint
		 *
		 * @var string
		 */
		public $endpoint_slug = '';

		/**
		 * Constructor
		 *
		 * @since 2.3.0
		 */
		public function __construct() {
			$this->register();

			add_action( 'template_redirect', array( $this, 'init' ), 1 );
		}

		/**
		 * Init class variables
		 *
		 * @since  2.3.0
		 */
		public function register() {
			// Get members area instance.
			$this->set_members_area_instance();

			$this->endpoint_key  = 'members-area';
			$this->endpoint_slug = get_option( 'woocommerce_myaccount_members_area_endpoint', 'members-area' );
			$this->endpoint      = array(
				'slug'  => $this->endpoint_slug,
				'label' => __( 'My Membership', 'yith-woocommerce-customize-myaccount-page' ),
				'icon'  => 'list',
			);

			// Register endpoint.
			$this->register_endpoint();
		}

		/**
		 * Init class frontend hooks
		 *
		 * @since 2.3.0
		 * @return void
		 */
		public function init() {
			if ( empty( $this->members_area ) ) {
				return;
			}

			// Set current membership.
			$this->current_membership = ! empty( $this->members_area ) ? $this->members_area->get_members_area_user_membership() : null;

			// Remove content in my account.
			remove_action( 'woocommerce_before_my_account', array( $this->members_area, 'my_account_memberships' ), 10 );
			add_shortcode( 'ywcmap_woocommerce_membership', array( $this, 'wc_membership' ) );

			// Filter current endpoint.
			add_filter( 'yith_wcmap_get_current_endpoint', array( $this, 'get_current_endpoint' ) );

			if ( yith_wcmap_get_current_endpoint() === $this->endpoint_slug && $this->current_membership ) {
				// Remove standard endpoints.
				remove_all_actions( 'yith_wcmap_print_single_endpoint' );
				remove_all_actions( 'yith_wcmap_print_endpoints_group' );

				add_action( 'yith_wcmap_after_endpoints_items', array( $this, 'custom_wc_members_nav' ) );
			}
		}

		/**
		 * Get Members Area Instance
		 *
		 * @since  2.3.0
		 */
		protected function set_members_area_instance() {
			$class = function_exists( 'wc_memberships' ) ? wc_memberships() : null;
			if ( empty( $class ) ) {
				return;
			}
			$frontend_class = $class->get_frontend_instance();
			// Try to set member area var.
			if ( ! empty( $frontend_class ) ) {
				if ( 1 === version_compare( '1.19.0', WC_Memberships::VERSION ) ) {
					$this->members_area = $frontend_class->get_members_area_instance();
				} else {
					$this->members_area = $frontend_class->get_my_account_instance()->get_members_area_instance();
				}
			}

		}

		/**
		 * Endpoint shortcode wc_membership
		 *
		 * @since  2.3.0
		 * @param array $args The shortcode args.
		 * @return string
		 */
		public function wc_membership( $args ) {
			if ( ! class_exists( 'WC_Memberships' ) ) {
				return '';
			}

			ob_start();
			$this->members_area->output_members_area();
			return ob_get_clean();
		}

		/**
		 * Change endpoint menu with the custom WC Members Navigation
		 *
		 * @since  2.3.0
		 */
		public function custom_wc_members_nav() {

			$membership_plan       = $this->current_membership->get_plan();
			$members_area_sections = $this->members_area->get_members_area_navigation_items( $membership_plan );

			foreach ( $members_area_sections as $endpoint => $members_area_section ) {
				// Build args array.
				$options = array(
					'label' => $members_area_section['label'],
				);
				/**
				 * APPLY_FILTERS: yith_wcmap_print_single_endpoint_args
				 *
				 * Filters the array of arguments needed to print the endpoint.
				 *
				 * @param array  $args     Array of arguments.
				 * @param string $endpoint Endpoint key.
				 * @param array  $options  Endpoint options.
				 *
				 * @return array
				 */
				$args = apply_filters(
					'yith_wcmap_print_single_endpoint_args',
					array(
						'url'      => $members_area_section['url'],
						'endpoint' => $endpoint,
						'options'  => $options,
						'classes'  => $members_area_section['class'],
					),
					$endpoint,
					$options
				);

				wc_get_template( 'ywcmap-myaccount-menu-item.php', $args, '', YITH_WCMAP_DIR . 'templates/' );
			}
		}

		/**
		 * Filter current endpoint
		 *
		 * @since  1.0.0
		 * @param string $current Current endpoint.
		 * @return string
		 */
		public function get_current_endpoint( $current ) {
			if ( 'members_area' === $current ) {
				return $this->endpoint_slug;
			}
			return $current;
		}

	}
}
