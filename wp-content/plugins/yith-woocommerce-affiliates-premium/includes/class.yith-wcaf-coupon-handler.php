<?php

/**
 * Affiliates' coupons handling class
 *
 * @author  Your Inspiration Themes
 * @package YITH WooCommerce Affiliates
 * @version 1.0.0
 */

/*
 * This file belongs to the YIT Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */

if ( ! defined( 'YITH_WCAF' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'YITH_WCAF_Coupon_Handler' ) ) {
	/**
	 * WooCommerce Coupon Handler
	 *
	 * @since 1.0.0
	 */
	class YITH_WCAF_Coupon_Handler {
		/**
		 * Single instance of the class for each token
		 *
		 * @var \YITH_WCAF_Coupon_Handler
		 * @since 1.0.0
		 */
		protected static $instance = null;

		/**
		 * Enable coupon handling
		 *
		 * @var bool
		 */
		protected $_coupon_enable = false;

		/**
		 * Show Coupon affiliate section
		 *
		 * @var bool
		 */
		protected $_coupon_show_section = false;

		/**
		 * Show affiliate section just to affiliates with coupons
		 *
		 * @var bool
		 */
		protected $_coupon_limit_section = false;

		/**
		 * Notify affiliates when a new coupon is added
		 *
		 * @var bool
		 */
		protected $_payment_pending_notify_admin = false;

		/**
		 * Constructor method
		 *
		 * @return \YITH_WCAF_Coupon_Handler
		 */
		public function __construct() {
			// init class
			$this->_retrieve_options();

			// backend
			add_filter( 'yith_wcaf_general_settings', array( $this, 'filter_general_settings' ) );

			if ( $this->_coupon_enable ) {
				add_filter( 'woocommerce_coupon_data_tabs', array( $this, 'add_coupon_tab' ) );
				add_action( 'woocommerce_coupon_data_panels', array( $this, 'print_coupon_tab' ), 10, 2 );
				add_action( 'woocommerce_coupon_options_save', array( $this, 'save_coupon_tab' ), 10, 2 );
			}

			// dashboard handling
			if ( $this->_coupon_show_section ) {
				add_filter( 'yith_wcaf_available_endpoints', array( $this, 'add_coupons_endpoint' ) );
				add_filter( 'yith_wcaf_standby', array( $this, 'hide_coupons_section' ) );
				add_filter( 'yith_wcaf_custom_dashboard_sections', array( $this, 'add_coupons_section' ), 10, 3 );
			}

			add_action( 'update_option_yith_wcaf_coupon_enable', array( $this, 'fix_coupon_endpoint' ), 10, 2 );
		}

		/* === BACKEND METHODS === */

		/**
		 * Filter general settings, to add coupon settings
		 *
		 * @param $settings mixed Original settings array
		 *
		 * @return mixed Filtered settings array
		 * @since 1.0.0
		 */
		public function filter_general_settings( $settings ) {
			$coupon_settings = array(
				'coupon-options' => array(
					'title' => __( 'Coupon', 'yith-woocommerce-affiliates' ),
					'type'  => 'title',
					'desc'  => '',
					'id'    => 'yith_wcaf_coupon_options'
				),

				'coupon-enable' => array(
					'title'   => __( 'Eanble coupon handling', 'yith-woocommerce-affiliates' ),
					'type'    => 'checkbox',
					'desc'    => __( 'Enable coupons handling for affiliates', 'yith-woocommerce-affiliates' ),
					'id'      => 'yith_wcaf_coupon_enable',
					'default' => 'no'
				),

				'coupon-show-section' => array(
					'title'   => __( 'Show coupon section', 'yith-woocommerce-affiliates' ),
					'type'    => 'checkbox',
					'desc'    => __( 'Enable coupons section into Affiliate Dashboard', 'yith-woocommerce-affiliates' ),
					'id'      => 'yith_wcaf_coupon_show_section',
					'default' => 'yes'
				),

				'coupon-limit-section' => array(
					'title'   => __( 'Limit coupon section', 'yith-woocommerce-affiliates' ),
					'type'    => 'checkbox',
					'desc'    => __( 'Enable coupons section for affiliates with coupon only', 'yith-woocommerce-affiliates' ),
					'id'      => 'yith_wcaf_coupon_limit_section',
					'default' => 'yes'
				),

				'coupon-new-notify-affiliate' => array(
					'title'   => __( 'Notify affiliate on new coupon', 'yith-woocommerce-affiliates' ),
					'type'    => 'checkbox',
					'desc'    => sprintf( '%s <a href="%s">%s</a>', __( 'Notify affiliate when a new coupon is bound to his account; customize email on', 'yith-woocommerce-affiliates' ), esc_url( add_query_arg( array(
						'page'    => 'wc-settings',
						'tab'     => 'email',
						'section' => 'yith_wcaf_customer_new_coupon_email'
					), admin_url( 'admin.php' ) ) ), __( 'WooCommerce Settings Page', 'yith-woocommerce-affiliates' ) ),
					'id'      => 'yith_wcaf_coupon_notify_affiliate',
					'default' => 'no'
				),

				'coupon-options-end' => array(
					'type' => 'sectionend',
					'id'   => 'yith_wcaf_coupon_options'
				),
			);

			$settings['settings'] = yith_wcaf_append_items( $settings['settings'], 'commission-options-end', $coupon_settings );

			return $settings;
		}

		/**
		 * Add custom tab to coupon edit page
		 *
		 * @param $tabs array Array of currently defined tabs
		 *
		 * @return array Array of filtered tabs
		 */
		public function add_coupon_tab( $tabs ) {

			$tabs['affiliates'] = array(
				'label'  => __( 'Affiliates', 'yith-woocommerce-affiliates' ),
				'target' => 'affiliates_coupon_data',
				'class'  => '',
			);

			return $tabs;

		}

		/**
		 * Print custom tab into coupon edit page
		 *
		 * @param $coupon_id int Coupon ID
		 * @param $coupon    \WC_Coupon Coupon object
		 *
		 * @return void
		 */
		public function print_coupon_tab( $coupon_id, $coupon ) {
			?>
			<div id="affiliates_coupon_data" class="panel woocommerce_options_panel">
				<p class="form-field">
					<label><?php _e( 'Referrer', 'yith-woocommerce-affiliates' ); ?></label>
					<select class="yith-users-select wc-product-search" style="width: 50%;" name="coupon_referrer" data-placeholder="<?php esc_attr_e( 'Search for an affiliate&hellip;', 'woocommerce' ); ?>" data-action="json_search_affiliates" data-allow_clear="true">
						<?php
						$referrer  = $coupon->get_meta( 'coupon_referrer', true );
						$affiliate = YITH_WCAF_Affiliate_Handler()->get_affiliate_by_id( $referrer );

						if ( $affiliate ) {
							$user = get_user_by( 'id', $affiliate['user_id'] );

							$username = '';
							if ( $user->first_name || $user->last_name ) {
								$username .= esc_html( ucfirst( $user->first_name ) . ' ' . ucfirst( $user->last_name ) );
							} else {
								$username .= esc_html( ucfirst( $user->display_name ) );
							}

							$affiliate_formatted_name = $username . ' (#' . $user->ID . ' &ndash; ' . sanitize_email( $user->user_email ) . ')';

							echo '<option value="' . esc_attr( $referrer ) . '"' . selected( true, true, false ) . '>' . wp_kses_post( $affiliate_formatted_name ) . '</option>';
						}
						?>
					</select>
					<?php echo wc_help_tip( __( 'User that will be referred when someone purchase with this coupon', 'yith-toolkit' ) ); ?>
				</p>
			</div>
			<?php
		}

		/**
		 * Save fields from custom coupon tab
		 *
		 * @param $coupon_id int Coupon ID
		 * @param $coupon    \WC_Coupon Coupon object
		 *
		 * @return void
		 */
		public function save_coupon_tab( $coupon_id, $coupon ) {
			$prev_value = $coupon->get_meta( 'coupon_referrer', true );
			$new_value  = isset( $_POST['coupon_referrer'] ) ? intval( $_POST['coupon_referrer'] ) : false;

			$coupon->update_meta_data( 'coupon_referrer', $new_value );
			$coupon->save_meta_data();

			if ( $new_value && $prev_value != $new_value ) {
				do_action( 'yith_wcaf_affiliate_coupon_saved', $coupon, $new_value, $prev_value );
			}
		}

		/* === INIT METHODS === */

		/**
		 * Retrieve options for payment from db
		 *
		 * @return void
		 * @since 1.0.0
		 */
		public function _retrieve_options() {
			$this->_coupon_enable                = 'yes' == get_option( 'yith_wcaf_coupon_enable', $this->_coupon_enable );
			$this->_coupon_show_section          = 'yes' == get_option( 'yith_wcaf_coupon_show_section', $this->_coupon_show_section );
			$this->_coupon_limit_section         = 'yes' == get_option( 'yith_wcaf_coupon_limit_section', $this->_coupon_limit_section );
			$this->_payment_pending_notify_admin = 'yes' == get_option( 'yith_wcaf_payment_pending_notify_admin', $this->_payment_pending_notify_admin );
		}

		/* === AFFILIATE DASHBOARD METHODS === */

		/**
		 * Add coupons tab to Affiliate Dashboard
		 *
		 * @param $endpoints array Array of defined endpoints
		 *
		 * @return array Array of filtered endpoints
		 */
		public function add_coupons_endpoint( $endpoints ) {
			$endpoints = yith_wcaf_append_items( $endpoints, 'commissions', array(
				'coupons' => __( 'Coupons', 'yith-woocommerce-affiliates' )
			) );

			return $endpoints;
		}

		/**
		 * Hide coupons section when it is not required for current user
		 *
		 * @return void
		 */
		public function hide_coupons_section() {
			if ( ! is_user_logged_in() ) {
				return;
			}

			$user_id   = get_current_user_id();
			$affiliate = YITH_WCAF_Affiliate_Handler()->get_affiliate_by_user_id( $user_id );

			if ( ! $affiliate || ( $this->_coupon_limit_section && apply_filters( 'yith_wcaf_show_coupon_section', ! $this->has_affiliate_coupons( $affiliate['ID'] ) ) ) ) {
				add_filter( 'yith_wcaf_get_dashboard_endpoints', array( $this, 'remove_coupon_endpoint' ) );
			}
		}

		/**
		 * Removes coupons endpoint from available endpoints
		 *
		 * @param $endpoints array Currently available endpoints
		 *
		 * @return array Filtered endpoints
		 */
		public function remove_coupon_endpoint( $endpoints ) {
			if ( isset( $endpoints['coupons'] ) ) {
				unset( $endpoints['coupons'] );
			}

			return $endpoints;
		}

		/**
		 * Mark rewrite rules for flush when adding coupon endpoint
		 *
		 * @param $old_value string Old yith_wcaf_coupon_enable option value
		 * @param $value     string New yith_wcaf_coupon_enable option value
		 *
		 * @return void
		 * @since 1.3.0
		 */
		public function fix_coupon_endpoint( $old_value, $value ) {

			if ( 'yes' == $value ) {
				update_option( '_yith_wcaf_flush_rewrite_rules', true );
			}
		}

		/**
		 * Returns output of withdraw endpoint when correct queryvar is found
		 *
		 * @param $content    string Ednpoint content
		 * @param $query_vars array Current query vars
		 * @param $atts       mixed Array of shortcodes attributes
		 *
		 * @return string Section content, or empty string
		 * @since 1.0.0
		 */
		public function add_coupons_section( $content, $query_vars, $atts ) {
			if ( ! isset( $query_vars['coupons'] ) ) {
				return $content;
			}

			if ( ! is_user_logged_in() ) {
				return $content;
			}

			$user_id   = get_current_user_id();
			$affiliate = YITH_WCAF_Affiliate_Handler()->get_affiliate_by_user_id( $user_id );

			if ( ! $affiliate ) {
				return $content;
			}

			if ( $this->_coupon_limit_section && apply_filters( 'yith_wcaf_show_coupon_section', ! $this->has_affiliate_coupons( $affiliate['ID'] ) ) ) {
				return $content;
			}

			$content = YITH_WCAF_Shortcode_Premium::affiliate_dashboard_coupons( $atts );

			return $content;
		}

		/* === HELPER METHODS === */

		/**
		 * Returns true if affiliate has at least one coupon
		 *
		 * @pram $affiliate_id int Affiliate ID
		 * @return bool Whether affiliates has at least one coupon
		 */
		public function has_affiliate_coupons( $affiliate_id ) {
			return (bool) $this->get_affiliate_coupons( $affiliate_id );
		}

		/**
		 * Returns number of coupons bind to affiliate
		 *
		 * @pram $affiliate_id int Affiliate ID
		 * @return int Number of coupons bind to affiliate
		 */
		public function count_affiliate_coupons( $affiliate_id ) {
			return count( $this->get_affiliate_coupons( $affiliate_id ) );
		}

		/**
		 * Returns list of coupons ID related to a specific affiliate
		 *
		 * @pram $affiliate_id int Affiliate ID
		 * @return array Array of coupon ids
		 */
		public function get_affiliate_coupons( $affiliate_id, $args = array() ) {
			global $wpdb;

			$defaults = array(
				'limit'  => 0,
				'offset' => 0
			);

			$args = wp_parse_args( $args, $defaults );

			$query      = "SELECT ID 
                       FROM {$wpdb->posts} AS p LEFT JOIN {$wpdb->postmeta} AS pm ON p.ID = pm.post_id 
                       WHERE p.post_type = %s 
                         AND pm.meta_key = %s 
                         AND pm.meta_value = %s";
			$query_args = array(
				'shop_coupon',
				'coupon_referrer',
				$affiliate_id
			);

			if ( ! empty( $args['limit'] ) ) {
				$query .= sprintf( ' LIMIT %d, %d', ! empty( $args['offset'] ) ? $args['offset'] : 0, $args['limit'] );
			}

			$res = $wpdb->get_col( $wpdb->prepare( $query, $query_args ) );

			return $res;
		}

		/**
		 * Returns single instance of the class
		 *
		 * @return \YITH_WCAF_Coupon_Handler
		 * @since 1.0.0
		 */
		public static function get_instance() {
			if ( is_null( self::$instance ) ) {
				self::$instance = new self;
			}

			return self::$instance;
		}
	}
}

/**
 * Unique access to instance of YITH_WCAF_Coupon_Handler class
 *
 * @return \YITH_WCAF_Coupon_Handler
 * @since 1.0.0
 */
function YITH_WCAF_Coupon_Handler() {
	return YITH_WCAF_Coupon_Handler::get_instance();
}