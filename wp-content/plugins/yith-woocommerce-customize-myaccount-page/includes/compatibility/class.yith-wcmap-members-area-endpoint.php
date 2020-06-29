<?php
/**
 * WC Membership Compatibility Class
 *
 * @author  YITH
 * @package YITH WooCommerce Customize My Account Page
 * @version 1.0.0
 */


if ( ! defined( 'YITH_WCMAP' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'YITH_WCMAP_Members_Area_Endpoint' ) ) {
	/**
	 * Class YITH_WCMAP_Members_Area_Endpoint
	 *
	 * @since 2.3.0
	 */
	class YITH_WCMAP_Members_Area_Endpoint {

		/**
		 * Main WC Members Area instance
		 *
		 * @var \WC_Memberships_Members_Area
		 */
		public $members_area = null;

		/**
		 * Current membership id
		 *
		 * @var false|\WC_Memberships_User_Membership current user membership object
		 */
		public $current_membership = null;

		/**
		 * Membership endpoint
		 *
		 * @var string
		 */
		public $endpoint = '';

		/**
		 * Is new WC Membership plugin > 1.9
		 *
		 * @var boolean
		 */
		public $is_new_WCM = false;

		/**
		 * Constructor
		 *
		 * @since 2.3.0
		 */
		public function __construct() {

			$this->init();

			// remove content in my account
			remove_action( 'woocommerce_before_my_account', array( $this->members_area, 'my_account_memberships' ), 10 );
			add_shortcode( 'ywcmap_woocommerce_membership', array( $this, 'wc_membership' ) );

			// filter current endpoint
			add_filter( 'yith_wcmap_get_current_endpoint', array( $this, 'get_current_endpoint' ) );

			if ( $this->is_new_WCM && yith_wcmap_get_current_endpoint() == $this->endpoint && $this->current_membership ) {
				// remove standard endpoints
				remove_all_actions( 'yith_wcmap_print_single_endpoint' );
				remove_all_actions( 'yith_wcmap_print_endpoints_group' );

				add_action( 'yith_wcmap_after_endpoints_items', array( $this, 'custom_wc_members_nav' ) );
			}
		}

		/**
		 * Init class variables
		 *
		 * @since  2.3.0
		 * @author Francesco Licandro
		 */
		public function init() {
			// get members area instance
			$this->_set_members_area_instance();
			$this->endpoint           = 'members-area';
			$this->current_membership = $this->_get_members_area_user_membership();
			$this->is_new_WCM         = version_compare( WC_Memberships::VERSION, '1.9.0', '>=' );
		}

		/**
		 * Get Members Area Instance
		 *
		 * @since  2.3.0
		 * @author Francesco Licandro
		 */
		protected function _set_members_area_instance() {
			$class              = wc_memberships();
			$frontend_class     = method_exists( $class, 'get_frontend_instance' ) ? $class->get_frontend_instance() : $class->frontend;
			$this->members_area = method_exists( $frontend_class, 'get_members_area_instance' ) ? $frontend_class->get_members_area_instance() : $frontend_class;
		}

		/**
		 * Endpoint shortcode wc_membership
		 *
		 * @since  2.3.0
		 * @author Francesco Licandro
		 * @param array $args
		 * @return string
		 */
		public function wc_membership( $args ) {
			if ( ! class_exists( 'WC_Memberships' ) ) {
				return '';
			}

			ob_start();
			if ( ! $this->is_new_WCM ) {
				if ( $this->current_membership ) {
					$this->members_area->render_members_area_content();
				} else {
					$this->members_area->my_account_memberships();
				}
			} else {
				$this->members_area->output_members_area();
			}
			return ob_get_clean();
		}

		/**
		 * Change endpoint menu with the custom WC Members Navigation
		 *
		 * @since  2.3.0
		 * @author Francesco Licandro
		 */
		public function custom_wc_members_nav() {

			$membership_plan       = $this->current_membership->get_plan();
			$members_area_sections = $this->members_area->get_members_area_navigation_items( $membership_plan );

			foreach ( $members_area_sections as $endpoint => $members_area_section ) {

				// build args array
				$args = apply_filters( 'yith_wcmap_print_single_endpoint_args', array(
					'url'      => $members_area_section['url'],
					'endpoint' => $endpoint,
					'options'  => array(
						'label' => $members_area_section['label'],
					),
					'classes'  => $members_area_section['class'],
				) );

				wc_get_template( 'ywcmap-myaccount-menu-item.php', $args, '', YITH_WCMAP_DIR . 'templates/' );
			}
		}

		/**
		 * Get the user membership to display in members area.
		 *
		 * @since  2.3.0
		 * @author Francesco Licandro
		 * @return false|\WC_Memberships_Integration_Subscriptions_User_Membership|\WC_Memberships_User_Membership
		 */
		protected function _get_members_area_user_membership() {

			if ( $this->is_new_WCM ) {
				return $this->members_area->get_members_area_user_membership();
			}

			global $wp;

			// get query vars
			$query_vars = ! empty( $wp->query_vars[ $this->endpoint ] ) ? explode( '/', $wp->query_vars[ $this->endpoint ] ) : [];
			// get plan ID
			$plan_id = isset( $query_vars[0] ) && is_numeric( $query_vars[0] ) ? $query_vars[0] : 0;

			return wc_memberships_get_user_membership( get_current_user_id(), $plan_id );
		}

		/**
		 * Filter current endpoint
		 *
		 * @since  1.0.0
		 * @author Francesco Licandro
		 * @param string $current
		 * @return string
		 */
		public function get_current_endpoint( $current ) {
			if ( $current == 'members_area' ) {
				return $this->endpoint;
			}

			return $current;
		}

	}
}

new YITH_WCMAP_Members_Area_Endpoint();