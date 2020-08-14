<?php

if ( ! defined( 'ABSPATH' ) || ! defined( 'YITH_YWPAR_VERSION' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Implements features of YITH WooCommerce Points and Rewards Frontend
 *
 * @class   YITH_WC_Points_Rewards_Frontend
 * @since   1.0.0
 * @author  YITH
 * @package YITH WooCommerce Points and Rewards
 */
if ( ! class_exists( 'YITH_WC_Points_Rewards_Frontend' ) ) {

	/**
	 * Class YITH_WC_Points_Rewards_Frontend
	 */
	class YITH_WC_Points_Rewards_Frontend {

		/**
		 * Single instance of the class
		 *
		 * @var \YITH_WC_Points_Rewards_Frontend
		 */
		protected static $instance;

		public $endpoint;

		/**
		 * Returns single instance of the class
		 *
		 * @return \YITH_WC_Points_Rewards_Frontend
		 * @since 1.0.0
		 */
		public static function get_instance() {
			if ( is_null( self::$instance ) ) {
				self::$instance = new self();
			}

			return self::$instance;
		}

		/**
		 * Constructor
		 *
		 * Initialize plugin and registers actions and filters to be used.
		 *
		 * @since  1.0.0
		 * @author Emanuela Castorina
		 */
		public function __construct() {

			// add shortcodes on my account.
			add_shortcode( 'ywpar_my_account_points', array( $this, 'shortcode_my_account_points' ) );

			if ( YITH_WC_Points_Rewards()->get_option( 'enable_points_on_birthday_exp' ) == 'yes' ) {
				if ( ! ( class_exists( 'YITH_WC_Coupon_Email_System' ) && get_option( 'ywces_enable_birthday' ) == 'yes' ) ) {
					$available_places = get_option( 'ywpar_birthday_date_field_where', array( 'my-account', 'register_form', 'checkout' ) );

					if ( in_array( 'my-account', $available_places ) ) {
						add_action( 'woocommerce_edit_account_form', array( $this, 'add_birthday_field' ) );
					}
					if ( in_array( 'register_form', $available_places ) ) {
						add_action( 'woocommerce_register_form', array( $this, 'add_birthday_field' ) );
					}

					if ( in_array( 'checkout', $available_places ) ) {
						add_filter( 'woocommerce_checkout_fields', array( $this, 'add_birthday_field_checkout' ) );
					}

					add_action( 'woocommerce_save_account_details', array( YITH_WC_Points_Rewards(), 'save_birthdate' ) );
					add_action( 'woocommerce_created_customer', array( YITH_WC_Points_Rewards(), 'save_birthdate' ), 10, 1 );
					add_action( 'woocommerce_checkout_update_user_meta', array( YITH_WC_Points_Rewards(), 'save_birthdate' ), 10 );
				}
			}

			// Disable plugin when it is disabled by option.
			if ( ! YITH_WC_Points_Rewards()->is_enabled() ) {
				return;
			}

			// Hide messages of points for guests if option is enabled.
			// APPLY_FILTER : ywpar_hide_messages: filtering the hiding messages to guests.
			if ( apply_filters( 'ywpar_hide_messages', false ) || ( YITH_WC_Points_Rewards()->get_option( 'hide_point_system_to_guest' ) == 'yes' && ! is_user_logged_in() ) ) {
				return;
			}

			// From here the plugin is active.

			// Add shortcode to show messages in single product page.
			add_shortcode( 'yith_points_product_message', array( $this, 'show_single_product_message' ) );
			// Add shortcode to show the Checkout Thresholds extra points message.
			add_shortcode( 'yith_checkout_thresholds_message', array( $this, 'show_checkout_thresholds_message' ) );

			add_action( 'init', array( $this, 'init' ), 5 );

			// if the user is not enabled to and the option to a the option to hide points to guest is enabled (no filter at this time) don't show the messages.
			if ( ( is_user_logged_in() && ! YITH_WC_Points_Rewards()->is_user_enabled() ) || ( ! is_user_logged_in() && YITH_WC_Points_Rewards()->get_option( 'hide_point_system_to_guest' ) == 'yes' ) ) {
				return;
			}

			// Add messages on cart or checkout if them are enabled.
			add_action( 'template_redirect', array( $this, 'show_messages' ), 30 );

			// check if the messages on cart are enabled for ajax calls.
			if ( YITH_WC_Points_Rewards()->get_option( 'enabled_cart_message' ) == 'yes' ) {
				add_action( 'wc_ajax_ywpar_update_cart_messages', array( $this, 'print_cart_message' ) );
			}

			if ( 'yes' == YITH_WC_Points_Rewards()->get_option( 'show_point_summary_on_order_details' ) ) {
				add_action( 'woocommerce_view_order', 'ywpar_add_order_points_summary', 5 );
			}

		}


		/**
		 * Show messages on cart or checkout page if the options are enabled
		 */
		public function show_messages() {

			// APPLY_FILTER : ywpar_enable_points_upon_sales: Enable the store to earn or rewards points overriding the option.
			if ( apply_filters( 'ywpar_enable_points_upon_sales', YITH_WC_Points_Rewards()->get_option( 'enable_points_upon_sales', 'yes' ) == 'yes' ) ) {

				// check and show messages on single product page.
				if ( YITH_WC_Points_Rewards()->get_option( 'enabled_single_product_message' ) == 'yes' ) {
					$this->show_single_product_message_position();
				}

				// check and show messages on show page.
				if ( YITH_WC_Points_Rewards()->get_option( 'enabled_loop_message' ) == 'yes' ) {
					$this->show_single_loop_position();
				}

				// check if the user is enabled to earn.
				if ( YITH_WC_Points_Rewards()->is_user_enabled() ) {

					$coupons = WC()->cart->get_applied_coupons();
					// the messages will not show if the coupon is just applied to cart so the user is redeeming point and the option to earn while redeeming is activated
					if ( YITH_WC_Points_Rewards_Redemption()->check_coupon_is_ywpar( $coupons ) && get_option( 'ywpar_disable_earning_while_reedeming', 'no' ) == 'yes' ) {
						return;
					}

					// check if cart messages are enabled.
					if ( YITH_WC_Points_Rewards()->get_option( 'enabled_cart_message' ) == 'yes' ) {
						add_action( 'woocommerce_before_cart', array( $this, 'print_messages_in_cart' ) );
						// add_action( 'wc_ajax_ywpar_update_cart_messages', array( $this, 'print_cart_message' ) );
					}

					// check if the messages are enabled at checkout.
					if ( 'yes' === YITH_WC_Points_Rewards()->get_option( 'enabled_checkout_message' ) ) {
						add_action( 'woocommerce_before_checkout_form', array( $this, 'print_messages_in_cart' ) );
						add_action( 'before_woocommerce_pay', array( $this, 'print_messages_in_cart' ) );
					}

					// show if enabled the message for checkout thresholds extra points.
					if ( 'yes' === YITH_WC_Points_Rewards()->get_option( 'enable_checkout_threshold_exp' ) && 'yes' === YITH_WC_Points_Rewards()->get_option( 'checkout_threshold_show_message' ) ) {
						add_action( 'woocommerce_before_cart', array( $this, 'print_checkout_threshold' ) );
						add_action( 'woocommerce_before_checkout_form', array( $this, 'print_checkout_threshold' ) );
					}
				}
			}
		}


		/**
		 *  Add hooks when init action is triggered
		 */
		public function init() {

			// add points to earn to each variation
			add_filter( 'woocommerce_available_variation', array( $this, 'add_params_to_available_variation' ), 10, 3 );

			// enqueue scripts
			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_styles_scripts' ) );

			/** REDEEM  */
			// check if user can redeem points and add the rewards messages if are enabled

			if ( YITH_WC_Points_Rewards()->get_option( 'enable_rewards_points' ) == 'yes' && YITH_WC_Points_Rewards()->get_option( 'enabled_rewards_cart_message' ) == 'yes' && YITH_WC_Points_Rewards()->is_user_enabled( 'redeem' ) ) {
				add_action( 'woocommerce_before_cart', array( $this, 'print_rewards_message_in_cart' ) );
				add_action( 'woocommerce_before_checkout_form', array( $this, 'print_rewards_message_in_cart' ) );
				add_action( 'wc_ajax_ywpar_update_cart_rewards_messages', array( $this, 'print_rewards_message' ) );
			}

			// exit if user is not enabled to earn
			if ( ! YITH_WC_Points_Rewards()->is_user_enabled() ) {
				return;
			}

			// Add the endpoints to WooCommerce My Account
			if ( YITH_WC_Points_Rewards()->get_option( 'show_point_list_my_account_page' ) == 'yes' ) {
				$endpoint                                  = YITH_WC_Points_Rewards()->get_option( 'my_account_page_endpoint' );
				$this->endpoint                            = ! empty( $endpoint ) ? $endpoint : 'my-points';
				WC()->query->query_vars[ $this->endpoint ] = $this->endpoint;
				add_filter( 'option_rewrite_rules', array( $this, 'rewrite_rules' ), 1 );
				add_action( 'woocommerce_account_' . $this->endpoint . '_endpoint', array( $this, 'add_endpoint' ) );
				add_filter( 'woocommerce_endpoint_' . $this->endpoint . '_title', array( $this, 'load_endpoint_title' ) );

				add_filter( 'woocommerce_account_menu_items', array( $this, 'ywpar_add_points_menu_items' ), 20 );

				global $post;
				if ( $post && wc_get_page_id( 'myaccount' ) === $post->ID ) {
					function_exists( 'get_home_path' ) && flush_rewrite_rules();
				}

				if ( defined( 'YITH_PROTEO_VERSION' ) ) {
					add_filter( 'yith_proteo_myaccount_custom_icon', array( $this, 'customize_my_account_proteo_icon' ), 10, 2 );
				}
			}

		}

		/**
		 * Change the icon inside my account on Proteo Theme
		 *
		 * @param $icon
		 * @param $endpoint
		 *
		 * @return string
		 */
		public function customize_my_account_proteo_icon( $icon, $endpoint ) {
			if ( $endpoint == $this->endpoint ) {
				return '<span class="yith-proteo-myaccount-icons lnr lnr-diamond"></span>';
			}

			return $icon;
		}


		/**
		 * Check if the permalink should be flushed.
		 *
		 * @param $rules
		 *
		 * @return bool
		 */
		public function rewrite_rules( $rules ) {
			return isset( $rules["(.?.+?)/{$this->endpoint}(/(.*))?/?$"] ) ? $rules : false;
		}


		/**
		 * Show the points My account page
		 */
		public function add_endpoint() {
			$shortcode = '[ywpar_my_account_points]';
			echo is_callable( 'apply_shortcodes' ) ? apply_shortcodes( $shortcode ) : do_shortcode( $shortcode ); //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}

		public function load_endpoint_title( $title ) {
			return YITH_WC_Points_Rewards()->get_option( 'my_account_page_label', esc_html__( 'My Points', 'yith-woocommerce-points-and-rewards' ) );
		}

		/**
		 * Add the menu item on WooCommerce My account Menu
		 * before the Logout item menu.
		 *
		 * @param $wc_menu array
		 *
		 * @return mixed
		 */
		public function ywpar_add_points_menu_items( $wc_menu ) {

			if ( isset( $wc_menu['customer-logout'] ) ) {
				$logout = $wc_menu['customer-logout'];
				unset( $wc_menu['customer-logout'] );
			}

			$wc_menu[ $this->endpoint ] = YITH_WC_Points_Rewards()->get_option( 'my_account_page_label', __( 'My Points', 'yith-woocommerce-points-and-rewards' ) );

			if ( isset( $logout ) ) {
				$wc_menu['customer-logout'] = $logout;
			}

			return $wc_menu;
		}

		/**
		 * Enqueue Scripts and Styles
		 *
		 * @return void
		 * @since  1.0.0
		 * @author Emanuela Castorina
		 */
		public function enqueue_styles_scripts() {

			wp_enqueue_script(
				'ywpar_frontend',
				YITH_YWPAR_ASSETS_URL . '/js/frontend' . YITH_YWPAR_SUFFIX . '.js',
				array(
					'jquery',
					'wc-add-to-cart-variation',
				),
				YITH_YWPAR_VERSION,
				true
			);
			wp_enqueue_style( 'ywpar_frontend', YITH_YWPAR_ASSETS_URL . '/css/frontend.css', array(), YITH_YWPAR_VERSION );

			$script_params = array(
				'ajax_url'    => admin_url( 'admin-ajax' ) . '.php',
				'wc_ajax_url' => WC_AJAX::get_endpoint( '%%endpoint%%' ),
			);

			wp_localize_script( 'ywpar_frontend', 'yith_wpar_general', $script_params );
		}

		/**
		 * Add message in single product page
		 *
		 * @param $atts
		 *
		 * @return string
		 * @since   1.0.0
		 * @author  Emanuela Castorina
		 *
		 */
		public function show_single_product_message( $atts ) {

			$product_id = 0;
			$atts       = shortcode_atts(
				array(
					'product_id' => 0,
					'message'    => YITH_WC_Points_Rewards()->get_option( 'single_product_message' ),
				),
				$atts
			);

			extract( $atts );
			$product_id = intval( $product_id );
			if ( ! $product_id ) {
				global $product;
			} else {
				$product = wc_get_product( $product_id );
			}

			if ( ! $product || $product->is_type( 'external' ) ) {
				return '';
			}

			$product_points = YITH_WC_Points_Rewards_Earning()->calculate_product_points( $product );

			if ( is_numeric( $product_points ) && $product_points <= 0 ) {
				return '';
			}

			$message = $this->replace_placeholder_on_product_message( $product, $message, $product_points );

			$class = 'hide';
			if ( $product->is_type( 'variable' ) ) {
				$message = '<div class="yith-par-message ' . esc_attr( $class ) . '">' . $message . '</div><div class="yith-par-message-variation ' . esc_attr( $class ) . '">' . $message . '</div>';
			} else {
				$message = '<div class="yith-par-message">' . $message . '</div>';
			}

			// APPLY_FILTER : ywpar_point_message_single_page: filtering the point message on single product page
			return apply_filters( 'ywpar_point_message_single_page', $message, $product, $class );
		}

		/**
		 * Print single product message.
		 *
		 * @author Francesco Licandro
		 */
		public function print_single_product_message() {
			$shortcode = '[yith_points_product_message]';
			echo is_callable( 'apply_shortcodes' ) ? apply_shortcodes( $shortcode ) : do_shortcode( $shortcode ); //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}

		/**
		 * Shortcode to show the Checkout Thresholds Extra Points Message
		 *
		 * @param array $atts shortcode params.
		 *
		 * @return  string
		 * @since   1.7.9
		 * @author  Armando Liccardo
		 */
		public function show_checkout_thresholds_message( $atts ) {
			$atts = shortcode_atts(
				array(
					'title' => esc_html__( 'Checkout total thresholds', 'yith-woocommerce-points-and-rewards' ),
				),
				$atts
			);

			$message = "";

			$checkout_thresholds = YITH_WC_Points_Rewards()->get_option( 'checkout_threshold_exp' );
			$thresholds          = $checkout_thresholds['list'];
			array_multisort( array_column( $thresholds, 'number' ), SORT_DESC, $thresholds );
			if ( isset( $thresholds ) && ! empty( $thresholds ) ) {
				ob_start();
				echo '<div id="yith-par-message-checkout_threshold" class="woocommerce-cart-notice woocommerce-info">';
				if ( trim( $atts['title'] ) !== '' ) {
					echo '<p><strong>' . $atts['title'] . '</strong></p>';
				}
				do_action( 'ywpar_checkout_thresholds_message_before' );
				echo '<table>';
				echo '<thead><tr><th>' . esc_html__( 'Order Amount', 'yith-woocommerce-points-and-rewards' ) . '</th><th>' . esc_html__( 'Points', 'yith-woocommerce-points-and-rewards' ) . '</th></tr></thead>';
				echo '<tbody>';
				foreach ( $thresholds as $checkout_threshold ) {
					echo '<tr><td>' . wc_price( $checkout_threshold['number'] ) . '</td><td>' . $checkout_threshold['points'] . '</td></tr>';
				}
				echo '</tbody></table>';
				do_action( 'ywpar_checkout_thresholds_message_after' );
				echo '</div>';
				$message = ob_get_clean();
			}

			return $message;
		}

		/**
		 * Print the Checkout Thresholds Extra Points Message
		 *
		 * @return  void
		 * @since   1.7.9
		 * @author  Armando Liccardo
		 */
		public function print_checkout_threshold() {
			$shortcode = '[yith_checkout_thresholds_message]';
			echo is_callable( 'apply_shortcodes' ) ? apply_shortcodes( $shortcode ) : do_shortcode( $shortcode ); //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

		}

		/**
		 * Set the position where display the message in single product
		 *
		 * @return  void
		 * @since   1.0.0
		 * @author  Emanuela Castorina
		 */
		public function show_single_product_message_position() {

			$position                = YITH_WC_Points_Rewards()->get_option( 'single_product_message_position' );
			$priority                = 10;
			$action                  = '';
			$priority_single_excerpt = has_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_excerpt' );
			$priority_after_meta     = has_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_meta' );
			switch ( $position ) {
				case 'before_add_to_cart':
					$action = 'woocommerce_before_add_to_cart_form';
					break;
				case 'after_add_to_cart':
					$action = 'woocommerce_after_add_to_cart_form';
					break;
				case 'before_excerpt':
					$action   = 'woocommerce_single_product_summary';
					$priority = $priority_single_excerpt ? $priority_single_excerpt - 1 : 18;
					break;
				case 'after_excerpt':
					$action   = 'woocommerce_single_product_summary';
					$priority = $priority_single_excerpt ? $priority_single_excerpt + 1 : 22;
					break;
				case 'after_meta':
					$action   = 'woocommerce_single_product_summary';
					$priority = $priority_after_meta ? $priority_after_meta + 1 : 42;
					break;
				default:
					break;
			}

			// APPLY_FILTER : ywpar_show_single_product_message_position_action: filtering action where show the message in single product page
			$action = apply_filters( 'ywpar_show_single_product_message_position_action', $action, $position, $priority );
			// APPLY_FILTER : ywpar_show_single_product_message_position_priority: filtering the priority where show the message in single product page
			$priority = apply_filters( 'ywpar_show_single_product_message_position_priority', $priority, $position, $action );

			add_action( $action, array( $this, 'print_single_product_message' ), $priority );
		}

		/**
		 * Set the position where display the message in loop
		 *
		 * @return  void
		 * @since   1.0.0
		 * @author  Emanuela Castorina
		 */
		public function show_single_loop_position() {
			// APPLY_FILTER : ywpar_loop_position: filtering the position where show the message in the loop
			$position = apply_filters( 'ywpar_loop_position', 'woocommerce_after_shop_loop_item_title' );
			// APPLY_FILTER : ywpar_loop_position: filtering the priority where show the message in the loop
			$priority = apply_filters( 'ywpar_loop_position_priority', 11 );
			add_action( $position, array( $this, 'print_messages_in_loop' ), $priority );
		}

		/**
		 * Print a message in loop
		 *
		 * @return  void
		 * @since   1.0.0
		 * @author  Emanuela Castorina
		 */
		public function print_messages_in_loop() {
			global $product;
			if ( $product->is_type( 'external' ) ) {
				return;
			}
			$message        = YITH_WC_Points_Rewards()->get_option( 'loop_message' );
			$product_points = YITH_WC_Points_Rewards_Earning()->calculate_product_points( $product );
			if ( $product_points > 0 || ( strpos( $product_points, '-' ) && $product_points != '0-0' ) ) {
				$message = $this->replace_placeholder_on_product_message( $product, $message, $product_points, true );
				// APPLY_FILTER : ywpar_single_product_message_in_loop: filtering the message in loop
				echo apply_filters( 'ywpar_single_product_message_in_loop', '<div  class="yith-par-message">' . wp_kses_post( $message ) . '</div>', $product, $product_points ); //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			}

		}

		/**
		 * Return the message with the placeholder replaced.
		 *
		 * @param      $product        WC_Product
		 * @param      $message        string
		 * @param      $product_points int
		 *
		 * @param bool $loop
		 *
		 * @return mixed
		 */
		private function replace_placeholder_on_product_message( $product, $message, $product_points, $loop = false ) {
			$singular         = YITH_WC_Points_Rewards()->get_option( 'points_label_singular' );
			$plural           = YITH_WC_Points_Rewards()->get_option( 'points_label_plural' );
			$class_name       = $loop ? 'product_point_loop' : 'product_point';
			$product_discount = ( 'fixed' == YITH_WC_Points_Rewards_Redemption()->get_conversion_method() ) ? YITH_WC_Points_Rewards_Redemption()->calculate_price_worth( $product, $product_points ) : '';

			$product_points_formatted = apply_filters( 'ywpar_product_points_formatted', $product_points );
			// replace {points} placeholder
			$message = str_replace( '{points}', '<span class="' . esc_attr( $class_name ) . '">' . $product_points_formatted . '</span>', $message );

			// replace {price_discount_fixed_conversion} placeholder
			$message = empty( $product_discount ) ? str_replace( '{price_discount_fixed_conversion}', '', $message ) : str_replace( '{price_discount_fixed_conversion}', '<span class="product-point-conversion">' . $product_discount . '</span>', $message );

			// replace {points_label} placeholder
			$points_label = apply_filters( 'ywpar_override_points_label', $product_points > 1 ? $plural : $singular, $product_points, $plural, $singular );
			$message      = str_replace( '{points_label}', $points_label, $message );

			return $message;
		}

		/**
		 * Print a message in cart/checkout page or in my account pay order page.
		 *
		 * @return  void
		 * @since   1.0.0
		 * @author  Emanuela Castorina
		 */
		public function print_messages_in_cart() {

			$points_earned = false;

			if ( isset( $_GET['key'] ) ) {
				$order_id = wc_get_order_id_by_order_key( $_GET['key'] );
				if ( $order_id ) {
					$points_earned = get_post_meta( $order_id, 'ywpar_points_from_cart', true );
				}
			}

			$message      = $this->get_cart_message( $points_earned );
			$message_type = 'earn';
			if ( ! empty( $message ) ) {
				// APPLY_FILTER : yith_par_messages_class: filtering the classes of messages in cart/checkout
				$yith_par_message_classes = apply_filters(
					'yith_par_messages_class',
					array(
						'woocommerce-cart-notice',
						'woocommerce-cart-notice-minimum-amount',
						'woocommerce-info',
					),
					$message_type
				);
				$classes                  = count( $yith_par_message_classes ) > 0 ? implode( ' ', $yith_par_message_classes ) : '';
				printf( '<div id="yith-par-message-cart" class="%s">%s</div>', esc_attr( $classes ), $message ); //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			}
		}

		/**
		 * Return the message to show on cart or checkout for point to earn.
		 *
		 * @param int $total_points
		 *
		 * @return string
		 * @since   1.1.3
		 * @author  Andrea Frascaspata
		 *
		 */
		private function get_cart_message( $total_points = 0 ) {

			$page = is_checkout() ? 'checkout' : 'cart';

			$message  = YITH_WC_Points_Rewards()->get_option( $page . '_message' );
			$singular = YITH_WC_Points_Rewards()->get_option( 'points_label_singular' );
			$plural   = YITH_WC_Points_Rewards()->get_option( 'points_label_plural' );

			if ( $total_points == 0 ) {
				$total_points = YITH_WC_Points_Rewards_Earning()->calculate_points_on_cart();
				if ( $total_points == 0 ) {
					return '';
				}
			}

			$conversion_method = YITH_WC_Points_Rewards_Redemption()->get_conversion_method();

			$discount = '';
			if ( $conversion_method == 'fixed' ) {
				$conversion  = YITH_WC_Points_Rewards_Redemption()->get_conversion_rate_rewards();
				$point_value = $conversion['money'] / $conversion['points'];
				$discount    = $total_points * $point_value;
			}

			$message = str_replace( '{points}', $total_points, $message );
			$message = str_replace( '{points_label}', ( $total_points > 1 ) ? $plural : $singular, $message );
			$message = str_replace( '{price_discount_fixed_conversion}', isset( $discount ) ? wc_price( $discount ) : '', $message );
			$message = str_replace( '{price_discount_fixed_conversion}', ( isset( $discount ) && $discount != '' ) ? wc_price( $discount ) : '', $message );

			// APPLY_FILTER : ywpar_cart_message_filter: filtering the cart messages in cart/checkout
			return apply_filters( 'ywpar_cart_message_filter', $message, $total_points, $discount );
		}

		/**
		 * Print the cart message on ajax call the the cart is updated.
		 *
		 * @since   1.1.3
		 * @author  Andrea Frascaspata
		 */
		public function print_cart_message() {
			echo $this->get_cart_message(); //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			die;
		}

		/**
		 * Print rewards message in cart/checkout page
		 *
		 * @return  void
		 * @since   1.0.0
		 * @author  Emanuela Castorina
		 */
		public function print_rewards_message_in_cart() {

			$coupons      = WC()->cart->get_applied_coupons();
			$message_type = 'reward';

			// the message will not showed if the coupon is just applied to cart
			if ( YITH_WC_Points_Rewards_Redemption()->check_coupon_is_ywpar( $coupons ) ) {
				return '';
			}

			$message = $this->get_rewards_message();
			if ( $message ) {
				// APPLY_FILTER : yith_par_messages_class: filtering the classes of messages in cart/checkout
				$yith_par_message_classes = apply_filters(
					'yith_par_messages_class',
					array(
						'woocommerce-cart-notice',
						'woocommerce-cart-notice-minimum-amount',
						'woocommerce-info',
					),
					$message_type
				);
				$classes                  = count( $yith_par_message_classes ) > 0 ? implode( ' ', $yith_par_message_classes ) : '';
				printf( '<div id="yith-par-message-reward-cart" class="%s">%s</div>', esc_attr( $classes ), $message ); //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			}

		}

		/**
		 * @return mixed|string|void
		 * @since   1.1.3
		 * @author  Andrea Frascaspata
		 */
		private function get_rewards_message() {
			// DO_ACTION : ywpar_before_rewards_message : action triggered before the rewards message
			do_action( 'ywpar_before_rewards_message' );
			$message = '';

			if ( is_user_logged_in() ) {

				$message                 = YITH_WC_Points_Rewards()->get_option( 'rewards_cart_message' );
				$plural                  = YITH_WC_Points_Rewards()->get_option( 'points_label_plural' );
				$max_discount            = YITH_WC_Points_Rewards_Redemption()->calculate_rewards_discount();
				$minimum_amount          = YITH_WC_Points_Rewards()->get_option( 'minimum_amount_to_redeem', '' );
				$max_percentual_discount = YITH_WC_Points_Rewards_Redemption()->get_max_percentual_discount();

				// APPLY_FILTER : ywpar_hide_value_for_max_discount: hide the message if $max_discount is < 0
				$max_discount_2 = apply_filters( 'ywpar_hide_value_for_max_discount', $max_discount );

				if ( apply_filters( 'ywpar_exclude_taxes_from_calculation', false ) ) {
					$subtotal = (float) WC()->cart->get_subtotal();
				} else {
					$subtotal = ( (float) WC()->cart->get_subtotal() + (float) WC()->cart->get_subtotal_tax() );
				}

				if ( ! $max_discount || ( ! empty( $minimum_amount ) && $subtotal < $minimum_amount ) ) {
					return '';
				}

				if ( $max_discount > 0 ) {

					$max_points = YITH_WC_Points_Rewards_Redemption()->get_max_points();

					if ( $max_points == 0 ) {
						return '';
					}

					if ( YITH_WC_Points_Rewards()->get_option( 'conversion_rate_method' ) == 'fixed' ) {

						$minimum_discount_amount = YITH_WC_Points_Rewards()->get_option( 'minimum_amount_discount_to_redeem' );
						if ( ! empty( $minimum_discount_amount ) && $max_discount < $minimum_discount_amount ) {
							return '';
						}

						$conversion               = YITH_WC_Points_Rewards_Redemption()->get_conversion_rate_rewards( get_woocommerce_currency() );
						$minimum_points_to_redeem = ( $conversion['points'] / $conversion['money'] ) * floatval( $minimum_discount_amount );

						// APPLY_FILTER : ywpar_min_value_to_reedem_error_msg: change the error message that appears on trying to use less points than the ones needed as minimum
						$min_value_to_reedem_error_msg = apply_filters( 'ywpar_min_value_to_reedem_error_msg', __( 'The minimum value to reedem is ', 'yith-woocommerce-points-and-rewards' ) . $minimum_points_to_redeem, $minimum_points_to_redeem );

						$before_ywpar_points_max = apply_filters( 'ywpar_before_ywpar-points-max', '' );
						$after_ywpar_points_max  = apply_filters( 'ywpar_after_ywpar-points-max', '' );
						$max_points_formatted    = apply_filters( 'ywpar_max_points_formatted', $max_points );
						$message                 = str_replace( '{points_label}', $plural, $message );
						$message                 = str_replace( '{max_discount}', wc_price( $max_discount ), $message );
						$message                 = str_replace( '{points}', $max_points_formatted, $message );
						$message                 .= ' <a class="ywpar-button-message">' . YITH_WC_Points_Rewards()->get_option( 'label_apply_discounts' ) . '</a>';
						$message                 .= '<div class="clear"></div><div class="ywpar_apply_discounts_container"><form class="ywpar_apply_discounts" method="post">' . wp_nonce_field( 'ywpar_apply_discounts', 'ywpar_input_points_nonce' ) . '
                                    <input type="hidden" name="ywpar_points_max" value="' . $max_points . '">
                                    <input type="hidden" name="ywpar_max_discount" value="' . $max_discount_2 . '">
                                    <input type="hidden" name="ywpar_rate_method" value="fixed">
                                    ' . $before_ywpar_points_max . '
                                    <p class="form-row form-row-first">
                                        <input type="text" min="' . $minimum_points_to_redeem . '" name="ywpar_input_points" class="input-text"  id="ywpar-points-max" value="' . $max_points . '">
                                        <input type="hidden" name="ywpar_input_points_check" id="ywpar_input_points_check" value="0">
                                    </p>
                                    ' . $after_ywpar_points_max . '
                                    <p class="form-row form-row-last">
                                        <input type="submit" class="button" name="ywpar_apply_discounts" id="ywpar_apply_discounts" value="' . YITH_WC_Points_Rewards()->get_option( 'label_apply_discounts' ) . '">
                                    </p>
                                    <div class="clear"></div>
                                    <div style="display: none" class="ywpar_min_reedem_value_error">' . $min_value_to_reedem_error_msg . '</div>
                                </form></div>';

					} elseif ( YITH_WC_Points_Rewards()->get_option( 'conversion_rate_method' ) == 'percentage' ) {
						$max_points_formatted = apply_filters( 'ywpar_max_points_formatted', $max_points );

						$message = str_replace( '{points_label}', $plural, $message );
						$message = str_replace( '{max_discount}', wc_price( $max_discount ), $message );
						$message = str_replace( '{max_percentual_discount}', $max_percentual_discount . '%', $message );
						$message = str_replace( '{points}', $max_points_formatted, $message );
						$message .= ' <a class="ywpar-button-message ywpar-button-percentage-discount">' . YITH_WC_Points_Rewards()->get_option( 'label_apply_discounts' ) . '</a>';
						$message .= '<div class="ywpar_apply_discounts_container"><form class="ywpar_apply_discounts" method="post">' . wp_nonce_field( 'ywpar_apply_discounts', 'ywpar_input_points_nonce' ) . '
                                     <input type="hidden" name="ywpar_points_max" value="' . $max_points . '">
                                     <input type="hidden" name="ywpar_max_discount" value="' . $max_discount_2 . '">
                                     <input type="hidden" name="ywpar_rate_method" value="percentage">';

						$message .= '</form></div>';
					}
				} else {
					$message = '';
				}

				// DO_ACTION : ywpar_after_rewards_message : action triggered after the rewards message
				do_action( 'ywpar_after_rewards_message' );
			}

			return $message;
		}

		/**
		 * @since   1.1.3
		 * @author  Andrea Frascaspata
		 */
		public function print_rewards_message() {
			$coupons = WC()->cart->get_applied_coupons();

			// the message will not showed if the coupon is just applied to cart
			if ( YITH_WC_Points_Rewards_Redemption()->check_coupon_is_ywpar( $coupons ) ) {
				return '';
			}
			echo $this->get_rewards_message(); //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

		}

		/**
		 * Shortcode my account points
		 *
		 * @return string
		 * @since  1.1.3
		 * @author Francesco Licandro
		 */
		public function shortcode_my_account_points() {
			if ( ! YITH_WC_Points_Rewards()->is_enabled() || ! YITH_WC_Points_Rewards()->is_user_enabled() ) {
				return '';
			}

			ob_start();
			wc_get_template( '/myaccount/my-points-view.php', null, '', YITH_YWPAR_TEMPLATE_PATH );

			return ob_get_clean();
		}

		/**
		 * Add points section to my-account page
		 *
		 * @return  void
		 * @since   1.0.0
		 * @author  Emanuela Castorina
		 */
		public function my_account_points() {
			$shortcode = '[ywpar_my_account_points]';
			echo is_callable( 'apply_shortcodes' ) ? apply_shortcodes( $shortcode ) : do_shortcode( $shortcode ); //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}

		/**
		 * Add custom params to variations
		 *
		 * @access public
		 *
		 * @param $args      array
		 * @param $product   object
		 * @param $variation object
		 *
		 * @return array
		 * @since  1.1.1
		 */
		public function add_params_to_available_variation( $args, $product, $variation ) {

			if ( $variation ) {
				$args['variation_points']                          = YITH_WC_Points_Rewards_Earning()->calculate_product_points( $variation );
				$args['variation_price_discount_fixed_conversion'] = YITH_WC_Points_Rewards_Redemption()->calculate_price_worth( $variation->get_id(), $args['variation_points'] );
			}

			return $args;
		}

		/**
		 * Add customer birth date field to edit account page
		 *
		 * @return  void
		 * @throws Exception
		 * @since   1.0.0
		 * @author  Alberto Ruggiero
		 */
		public function add_birthday_field() {

			$user         = get_user_by( 'id', get_current_user_id() );
			$date_format  = YITH_WC_Points_Rewards()->get_option( 'birthday_date_format' );
			$date_formats = ywpar_get_date_formats();

			$birth_date       = '';
			$date_placeholder = ywpar_date_placeholders();
			$date_patterns    = ywpar_get_date_patterns();

			if ( ! empty( $user ) ) {
				$registered_date = YITH_WC_Points_Rewards()->get_user_birthdate( $user->ID );

				if ( $registered_date ) {
					$date       = DateTime::createFromFormat( 'Y-m-d', esc_attr( $registered_date ) );
					$birth_date = $date->format( $date_formats[ $date_format ] );
				}
			}

			$enabled = ( $birth_date == '' ) ? '' : 'disabled';

			if ( is_wc_endpoint_url( 'edit-account' ) ) {
				if ( ! empty( $birth_date ) ) {
					?>

					<p class="form-row form-row-wide">
						<label for="yith_birthday">
							<?php echo wp_kses_post( apply_filters( 'yith_birthday_label', __( 'Date of birth', 'yith-woocommerce-points-and-rewards' ) ) ); ?><?php echo wp_kses_post( ( apply_filters( 'ywpar_required_birthday', '' ) == 'required' ) ? ' <abbr class="required" title="required">*</abbr>' : '' ); ?>
						</label>
						<span id="yith_birthday" style="display: block;border: solid 1px #ccc;padding:10px;"><?php echo esc_html( $birth_date ); ?></span>
						<span class="yith_birthday_account_message" style="font-style: italic"><?php esc_html_e( 'If you need to change the Birthdate, please contact the website administrator.', 'yith-woocommerce-points-and-rewards' ); ?></span>
					</p>

					<?php
					return;
				}
			}
			?>

			<p class="form-row form-row-wide">
				<label for="yith_birthday">
					<?php echo wp_kses_post( apply_filters( 'yith_birthday_label', esc_html( __( 'Date of birth', 'yith-woocommerce-points-and-rewards' ) ) ) ); ?><?php echo wp_kses_post( ( apply_filters( 'ywpar_required_birthday', '' ) == 'required' ) ? ' <abbr class="required" title="required">*</abbr>' : '' ); ?>
				</label>
				<input
					type="text"
					class="input-text"
					name="yith_birthday"
					maxlength="10"
					placeholder="<?php echo esc_attr( $date_placeholder[ $date_format ] ); ?>"
					pattern="<?php echo esc_attr( $date_patterns[ $date_format ] ); ?>"
					value="<?php echo esc_attr( $birth_date ); ?>"
					<?php echo esc_attr( apply_filters( 'ywpar_required_birthday', '' ) ); ?>
					<?php echo esc_attr( $enabled ); ?>
				/>

			</p>

			<?php

		}


		/**
		 * Add customer birth date field to checkout process
		 *
		 * @param   $fields
		 *
		 * @return  array
		 * @since   1.0.0
		 *
		 * @author  Alberto Ruggiero
		 */
		public function add_birthday_field_checkout( $fields ) {

			$date_format      = YITH_WC_Points_Rewards()->get_option( 'birthday_date_format' );
			$date_placeholder = ywpar_date_placeholders();
			$date_patterns    = ywpar_get_date_patterns();

			if ( is_user_logged_in() ) {
				$user            = get_user_by( 'id', get_current_user_id() );
				$registered_date = get_user_meta( $user->ID, 'yith_birthday', true );
				$section         = $registered_date ? '' : 'billing';
			} else {
				$section = 'account';
			}

			if ( ! empty( $section ) ) {
				$fields[ $section ]['yith_birthday'] = array(
					'label'             => apply_filters( 'yith_birthday_label', __( 'Date of birth', 'yith-woocommerce-points-and-rewards' ) ),
					'custom_attributes' => array(
						'pattern'   => $date_patterns[ $date_format ],
						'maxlength' => 10,
					),
					'placeholder'       => $date_placeholder[ $date_format ],
					'input_class'       => array( 'ywpar-birthday' ),
					'class'             => array( 'form-row-wide' ),
					'priority'          => 999,
				);

				if ( apply_filters( 'ywpar_required_birthday', '' ) == 'required' ) {
					$fields[ $section ]['yith_birthday']['label']                         .= ' <abbr class="required" title="required">*</abbr>';
					$fields[ $section ]['yith_birthday']['custom_attributes']['required'] = 'required';
				}
			}

			return $fields;
		}
	}

}

/**
 * Unique access to instance of YITH_WC_Points_Rewards_Frontend class
 *
 * @return \YITH_WC_Points_Rewards_Frontend
 */
function YITH_WC_Points_Rewards_Frontend() {
	return YITH_WC_Points_Rewards_Frontend::get_instance();
}

