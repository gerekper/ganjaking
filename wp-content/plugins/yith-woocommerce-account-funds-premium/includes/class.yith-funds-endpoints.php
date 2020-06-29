<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'YITH_Funds_EndPoints' ) ) {

	class YITH_Funds_EndPoints {

		public $fund_query_vars = array();

		public function __construct() {

			add_action( 'init', array( $this, 'add_woocommerce_query_vars' ), 10 );
			add_action( 'init', array( $this, 'rewrite_rules' ), 20 );
			add_filter( 'woocommerce_account_menu_items', array( $this, 'funds_account_menu_items' ), 20 );

			$slug_make_deposit = yith_account_funds_get_endpoint_slug( 'make-a-deposit' );
			$slug_view_history = yith_account_funds_get_endpoint_slug( 'view-history' );


			add_action( 'woocommerce_account_' . $slug_make_deposit . '_endpoint', array(
				$this,
				'show_make_deposit_checkout_endpoint'
			) );
			add_action( 'woocommerce_account_' . $slug_view_history . '_endpoint', array(
				$this,
				'show_history_endpoint'
			) );

			add_filter( 'woocommerce_endpoint_make-a-deposit_title', array(
				$this,
				'show_make_deposit_endpoint_title'
			) );
			add_filter( 'woocommerce_endpoint_view-history_title', array(
				$this,
				'show_history_endpoint_title'
			) );

			add_filter( 'wcml_register_endpoints_query_vars', array( $this, 'register_endpoint' ), 10, 3 );
			add_filter( 'wcml_endpoint_permalink_filter', array( $this, 'endpoint_permalink_filter' ), 10, 2 );

			if ( ! is_admin() ) {
				add_filter( 'query_vars', array( $this, 'add_account_funds_query_vars' ), 0, 1 );
			}


			//Add custom item in YITH MY ACCOUNT MENU
			add_action( 'yith_myaccount_menu', array( $this, 'add_myaccount_menu' ) );
			add_filter( 'yit_get_myaccount_menu_icon_list', array( $this, 'funds_account_menu_icon_list' ) );
			add_filter( 'yit_get_myaccount_menu_icon_list_fa', array( $this, 'funds_account_menu_icon_list_fa' ) );
			add_filter( 'yit_panel_wc_before_update', array( $this, 'rewrite_endpoints' ), 10 );
			add_filter( 'yit_panel_wc_before_reset', array( $this, 'rewrite_endpoints' ), 10 );
			add_filter( 'body_class', array( $this, 'add_my_account_body_class' ), 20, 1 );

			$this->init_fund_query_vars();

			add_filter( 'woocommerce_get_query_vars', array( $this, 'add_woocommerce_query_vars' ), 20, 1 );
		}


		/**
		 * @param array $vars
		 */
		public function add_account_funds_query_vars( $vars ) {

			foreach ( $this->get_fund_query_vars() as $key => $value ) {

				$vars[] = $key;
			}

			return $vars;
		}

		/**
		 * init account fund endpoints
		 * @author Salvatore Strano
		 * @since 1.0.19
		 * return array
		 */
		public function init_fund_query_vars() {

			$slug_make_a_deposit = yith_account_funds_get_endpoint_slug( 'make-a-deposit' );
			$slug_view_history   = yith_account_funds_get_endpoint_slug( 'view-history' );
			$slug_redeem_funds   = yith_account_funds_get_endpoint_slug( 'redeem-funds' );


			$this->fund_query_vars = array(
				'make-a-deposit' => $slug_make_a_deposit,
				'view-history'   => $slug_view_history,
				'redeem-funds'   => $slug_redeem_funds
			);
		}

		/**
		 * get query vars
		 * @return array
		 * @since 1.0.19
		 * @author Salvatore Strano
		 */
		public function get_fund_query_vars() {

			return apply_filters( 'yith_account_funds_get_query_vars', $this->fund_query_vars );
		}


		/**
		 * add endpoint plugin in woocommerce
		 *
		 * @param array $query_vars
		 *
		 * @return array
		 * @author Salvatore Strano
		 * @since 1.0.19
		 *
		 */
		public function add_woocommerce_query_vars( $query_vars ) {

			if ( is_array( $query_vars ) ) {

				$query_vars = array_merge( $query_vars, $this->get_fund_query_vars() );
			}

			return $query_vars;
		}

		/**
		 * add menu items in my-account menu (WC 2.6)
		 *
		 * @param array $menu_items
		 *
		 * @return array
		 * @author Salvatore Strano
		 * @since 1.0.19
		 *
		 */
		public function funds_account_menu_items( $menu_items ) {

			$slug_make_deposit = yith_account_funds_get_endpoint_slug( 'make-a-deposit' );
			$slug_view_history = yith_account_funds_get_endpoint_slug( 'view-history' );

			$make_deposit_name = yith_account_funds_get_endpoint_title( 'make-a-deposit' );
			$view_history_name = yith_account_funds_get_endpoint_title( 'view-history' );

			if ( isset( $menu_items['customer-logout'] ) ) {
				$logout = $menu_items['customer-logout'];
				unset( $menu_items['customer-logout'] );
			}
			$menu_items[ $slug_make_deposit ] = $make_deposit_name;
			$menu_items[ $slug_view_history ] = $view_history_name;

			$menu_items = apply_filters('yith_account_funds_menu_items', $menu_items );

			if ( isset( $logout ) ) {
				$menu_items['customer-logout'] = $logout;
			}

			return $menu_items;
		}

		/**
		 * show the deposit form
		 *
		 * @param $value
		 *
		 * @author Salvatore Strano
		 *
		 */
		public function show_make_deposit_checkout_endpoint( $value ) {

			if ( ! is_user_logged_in()  ) {
				wp_redirect( esc_url( wc_get_page_permalink( 'myaccount' ) ) );
				exit;
			}

			if(  apply_filters('yith_funds_user_is_available', true ) ) {
				$min = get_option( 'yith_funds_min_value' );
				$max = get_option( 'yith_funds_max_value' );
				$max = $max !== '' ? 'max="' . esc_attr( $max ) . '"' : '';

				$args = array(
					'payment'      => array(
						'checkout'           => WC()->checkout(),
						'available_gateways' => WC()->payment_gateways()->get_available_payment_gateways(),
						'order_button_text'  => apply_filters( 'woocommerce_order_button_text', __( 'Place order', 'yith-woocommerce-account-funds' ) ),
					),
					'min'          => $min,
					'max'          => $max,
					'amount'       => isset( $_REQUEST['amount'] ) ? $_REQUEST['amount'] : '',
					'currency'     => get_woocommerce_currency(),
					'show_wc_menu' => true

				);

				wc_get_template( 'make-a-deposit.php', $args, '', YITH_FUNDS_TEMPLATE_PATH );
			}
		}

		/**
		 * show the fund history
		 *
		 * @param $value
		 *
		 * @author Salvatore Strano
		 *
		 */
		public function show_history_endpoint( $value ) {

			if ( ! is_user_logged_in() ) {

				wp_redirect( esc_url( wc_get_page_permalink( 'myaccount' ) ) );
				exit;
			}

			wc_get_template( 'deposit-history.php', array(), '', YITH_FUNDS_TEMPLATE_PATH );


		}

		/**
		 * show make a deposit endpoint title
		 * @return string
		 * @author Salvatore Strano
		 */
		public function show_make_deposit_endpoint_title() {

			return yith_account_funds_get_endpoint_title( 'make-a-deposit' );
		}

		/**
		 * show history funds endpoint title
		 * @return string
		 * @author Salvatore Strano
		 */
		public function show_history_endpoint_title() {

			return yith_account_funds_get_endpoint_title( 'view-history' );
		}


		/**
		 *register endpoint in wpml
		 *
		 * @param array $query_vars ,
		 * @param array $wc_vars
		 * @param WCML_Endpoints $obj
		 *
		 * @author Salvatore Strano
		 *
		 * @since 1.0.19
		 */

		public function register_endpoint( $query_vars, $wc_vars, $obj ) {
			foreach ( $this->get_fund_query_vars() as $key => $name ) {

				$query_vars[ $key ] = $obj->get_endpoint_translation( $key, isset( $wc_vars[ $key ] ) ? $wc_vars[ $key ] : $key );
			}

			return $query_vars;
		}

		/**
		 * @param $endpoint
		 * @param $key
		 *
		 * @return mixed
		 */
		public function endpoint_permalink_filter( $endpoint, $key ) {

			$endpoints = $this->get_fund_query_vars();
			$keys      = array_keys( $endpoints );
			if ( in_array( $key, $keys ) ) {

				$endpoint = $endpoints[ $key ];
			}

			return $endpoint;
		}

		/**
		 * @param string $myaccount_url
		 *
		 * @since 1.0.0
		 * @author Salvatore Strano
		 */
		public function add_myaccount_menu( $myaccount_url ) {

			global $wp;
			if ( is_user_logged_in() ) {
				$slug_make_deposit = yith_account_funds_get_endpoint_slug('make-a-deposit');
				$slug_view_history = yith_account_funds_get_endpoint_slug( 'view-history');

				?>
                <li>
                    <span class="fa fa-credit-card"></span>
                    <a style="display: inline-block;padding-left: 8px;"
                       href="<?php echo wc_get_endpoint_url( $slug_make_deposit, '', $myaccount_url ) ?>"
                       title="<?php _e( 'Make a deposit', 'yith-woocommerce-account-funds' ); ?>"<?php echo isset( $wp->query_vars['make-a-deposit'] ) ? ' class="active"' : ''; ?>><?php _e( 'Make a deposit', 'yith-woocommerce-account-funds' ) ?></a>
                </li>
                <li>
                    <a href="<?php echo wc_get_endpoint_url( $slug_view_history, '', $myaccount_url ) ?>"
                       title="<?php _e( 'My Funds History', 'yith-woocommerce-account-funds' ); ?>" <?php echo isset( $wp->query_vars['view-history'] ) ? ' class="active"' : ''; ?> >
                        <span data-icon="&#xe443;"
                              data-font="retinaicon-font"></span><?php _e( 'My Funds History', 'yith-woocommerce-account-funds' ); ?>
                    </a>
                </li>
				<?php
			}
		}


		public function funds_account_menu_icon_list( $icon_list ) {
			$slug_make_deposit = yith_account_funds_get_endpoint_slug('make-a-deposit');
			$slug_view_history = yith_account_funds_get_endpoint_slug( 'view-history');


			$icon_list[ $slug_make_deposit ] = '&#xe04d;';
			$icon_list[ $slug_view_history ] = '&#xe055;';

			return $icon_list;

		}

		/**
		 * add fontawesome icon
		 *
		 * @param $icon_list
		 *
		 * @return mixed
		 * @author YITHEMES
		 * @since 1.0.4
		 */
		public function funds_account_menu_icon_list_fa( $icon_list ) {
			$slug_make_deposit = yith_account_funds_get_endpoint_slug('make-a-deposit');
			$slug_view_history = yith_account_funds_get_endpoint_slug( 'view-history');


			$icon_list[ $slug_make_deposit ] = 'fa-money';
			$icon_list[ $slug_view_history ] = 'fa-folder-open';

			return $icon_list;

		}

		/**
		 * @param $old_value
		 * @param $new_value
		 * @param $option
		 */
		public function rewrite_endpoints() {

			if ( isset( $_GET['tab'] ) && 'endpoints-settings' == $_GET['tab'] ) {

				flush_rewrite_rules();
			}
		}

		public function rewrite_rules() {

			$rewrite = get_option( 'ywf_rewrite_rule', true );

			if ( $rewrite ) {

				flush_rewrite_rules();
				update_option( 'ywf_rewrite_rule', false );

			}
		}

		/**
		 * @param array $body_class
		 */
		public function add_my_account_body_class( $body_class ) {

			$my_account_page_id = wc_get_page_id( 'myaccount' );
			$current_page       = get_the_ID();

			if ( $my_account_page_id == $current_page ) {
				$body_class[] = 'woocommerce-account';
			}

			return $body_class;
		}


	}
}


function YITH_Fund_EndPoints() {
	return new YITH_Funds_EndPoints();
}

