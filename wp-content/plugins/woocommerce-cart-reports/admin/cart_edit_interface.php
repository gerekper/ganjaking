<?php
/*
*
* cart_edit_interfaces.php - Interface elements / modifications for the "Cart Edit" page.
*
*/

/*
*
* Add Metaboxes for "Cart Edit" page ( status / last updated date )
*
*/


class AV8_Edit_Interface {

	public $receipt;

	public function __construct() {
		global $post;

		$this->receipt = new AV8_Cart_Receipt();

		if ( isset( $post ) ) {
			$this->receipt->load_receipt( $post->ID );
		}

		//Add in title, since we removed the "Title Meta Box"
		add_action( 'admin_enqueue_scripts', [ &$this, 'tooltip_scripts' ] );

		//these meta boxes default to the right
		add_action( 'add_meta_boxes', [ $this, 'cart_status_meta_boxes' ] );
		add_action( 'add_meta_boxes', [ $this, 'cart_action_meta_boxes' ] );

		//these meta boxes default to the left
		add_action( 'add_meta_boxes', [ $this, 'cart_status_customer_meta_boxes' ] );
		add_action( 'add_meta_boxes', [ $this, 'add_cart_items_boxes' ] );
		add_action( 'add_meta_boxes', [ $this, 'cart_useragent_meta_boxes' ] );

		add_action( 'admin_menu', [ $this, 'remove_title_box' ] );
		add_action( 'admin_head', [ $this, 'remove_woocustom_box' ] );
		add_action( 'admin_menu', [ $this, 'remove_publish_box' ] );
		add_action( 'admin_menu', [ $this, 'remove_author_box' ] );
		add_action( 'admin_menu', [ $this, 'remove_slugdiv_box' ] );
	}

	/**
	 *
	 *
	 */

	/**
	 * @param $title
	 *
	 * @return string
	 */
	public function custom_edit_title( $title ) {
		return 'View Cart ' . $title;
	}

	/**
	 *
	 */
	public function tooltip_scripts() {
		global $pagenow;
		if ( is_admin() ) {
			if ( $pagenow == 'post.php' && get_post_type( get_post( $_GET['post'] ) ) == 'carts' ) {

				global $woocommerce;
				//wp_register_script( 'woocommerce_admin', $woocommerce->plugin_url() . '/assets/js/admin/woocommerce_admin.min.js', array('jquery', 'jquery-ui-widget', 'jquery-ui-core'), '1.0' );

				wp_enqueue_script( 'woocommerce_admin' );
				wp_enqueue_script( 'jquery' );
				wp_enqueue_script( 'jquery-ui' );
				wp_enqueue_script( 'ajax-chosen' );
				wp_enqueue_style( 'woocommerce_admin_styles', $woocommerce->plugin_url() . '/assets/css/admin.css' );
				wp_enqueue_style(
					'woocommerce_cart_report_admin_edit_css',
					plugins_url() . '/woocommerce-cart-reports/assets/css/cart_reports_admin_edit.css'
				);
				wp_register_script(
					'jquery-tiptip',
					plugins_url() . '/woocommerce-cart-reports/assets/js/jquery.tipTip.minified.js'
				);
				wp_enqueue_script( 'jquery-tiptip' );
				wp_enqueue_script( 'moment-js', 'https://unpkg.com/moment@2.14.1/min/moment.min.js' );
				wp_enqueue_script(
					'moment-duration',
					'https://unpkg.com/moment-duration-format@2.2.2/lib/moment-duration-format.js',
					[ 'moment-js' ]
				);

				$inline_js = "jQuery('.help_tip').tipTip({
					'attribute' : 'data-tip',
					'fadeIn' : 50,
					'fadeOut' : 50,
					'delay' : 200
				});";

				if ( function_exists( 'wc_enqueue_js' ) ) { //Check for compatibility
					wc_enqueue_js( $inline_js );
				} else {
					$woocommerce->add_inline_js( $inline_js );
				}
			}
		}
	}

	/**
	 * Set up the the cart status metabox and point to our handy callback - cart_status_metabox
	 */
	public function cart_status_meta_boxes() {
		add_meta_box(
			'cart_status_meta_boxes',
			__( 'Cart Status', 'woocommerce_cart_reports' ),
			[
				$this,
				'cart_status_metabox',
			],
			'carts',
			'side',
			'default'
		);
	}

	/**
	 * Set up Customer actions metabox
	 */
	public function cart_action_meta_boxes() {
		add_meta_box(
			'cart_action_meta_boxes',
			__( 'Customer Actions', 'woocommerce_cart_reports' ),
			[
				$this,
				'cart_action_customer_metabox',
			],
			'carts',
			'side',
			'default'
		);
	}

	/**
	 * Cart Customer metabox, show customer name where available.
	 */
	public function cart_status_customer_meta_boxes() {
		add_meta_box(
			'cart_status_customer_meta_boxes',
			__( 'Cart Customer', 'woocommerce_cart_reports' ),
			[
				$this,
				'cart_status_customer_metabox',
			],
			'carts',
			'normal',
			'default'
		);
	}

	/**
	 * Add metabox to show items in the cart, complete with a bunch of info about the items
	 * Layout was taken from the order details page( thanks woo!)
	 */
	public function add_cart_items_boxes() {

		add_meta_box(
			'woocommerce-cart-items',
			__( 'Cart Items', 'woocommerce_cart_reports' ),
			[
				$this,
				'woocommerce_cart_items_meta_box',
			],
			'carts',
			'normal',
			'default'
		);
	}

	/**
	 *
	 * Set up the "Cart Data" holding the front-facing fields for "last online, "last_updated", "ip address" and "cart
	 * age/time to conversion"
	 */
	public function cart_useragent_meta_boxes() {
		add_meta_box(
			'cart_useragent_meta_boxes',
			__( 'Cart Data', 'woocommerce_cart_reports' ),
			[
				$this,
				'cart_useragent_meta_box',
			],
			'carts',
			'normal',
			'default'
		);
	}

	/**
	 * "Customer Name" box implementation
	 *
	 */
	public function cart_status_customer_metabox() {

		$full_name = $this->receipt->full_name();
		$author_id = $this->receipt->post_author;
		if ( $author_id > 0 ) {

			if ( WP_DEBUG == true ) {
				assert( $full_name != '' && $full_name != ' ' );
			}

			$user_edit_url = admin_url( 'user-edit.php?user_id=' . $author_id );
			printf( '<a href="%s">%s</a>', $user_edit_url, $full_name );
		} elseif ( $full_name != false ) {
			if ( WP_DEBUG == true ) {
				assert( $full_name != '' && $full_name != ' ' );
			}
			printf( '<p>%s</p>', $full_name );
			//Print out actions
		} elseif ( $this->receipt->status() == 'Converted' ) {
			$order_id = $this->receipt->get_order_id();
			if ( WP_DEBUG == true ) {
				assert( $order_id > 0 );
			}

			$order = new WC_Order( $order_id );

			if ( version_compare( WC_VERSION, '3.0', '<' ) ) {
				echo __(
					'<p>' . ucwords( $order->billing_first_name . ' ' . $order->billing_last_name ) . ' (' . __(
						'Guest',
						'woocommerce_cart_reports'
					) . ')</p>'
				);
			} else {
				echo '<p>' . $order->get_formatted_billing_full_name() . ' (' . __( 'Guest', 'woocommerce_cart_reports' ) . ')</p>';
			}
		} else {
			echo __( '<span style="color: gray">Name Not Available</span>', 'woocommerce_cart_reports' ) . av8_tooltip(
					__(
						'No customer name information available for carts created by non-logged-in Guests.',
						'woocommerce_cart_reports'
					),
					false
				);
		}
	}

	/**
	 * "Cart Status" Box implementation
	 *
	 */
	public function cart_status_metabox() {
		global $woocommerce_cart_reports_options;
		global $post;

		$tooltip = '';
		$this->receipt->load_receipt( $post->ID );
		$timeout = $woocommerce_cart_reports_options['timeout'];
		//Show Cart State

		$show_custom_state = $this->receipt->status();
		$timeout_sec       = $timeout;
		$timeout_min       = $timeout_sec / 60;
		if ( WP_DEBUG == true ) {
			assert(
				$show_custom_state == 'Abandoned' || $show_custom_state == 'Converted' || $show_custom_state == 'Open'
			);
		}

		echo __(
			'<div><p><strong>' . __( 'Created:', 'woocommerce_cart_reports' ) . '</strong><br/>' . date(
				'F j, Y \a\t g:i a',
				$this->receipt->created()
			) . '</p></div>'
		);

		switch ( $show_custom_state ) {

			case 'Abandoned':
				$tooltip = av8_tooltip(
					sprintf(
						/* translators: %s: Cart timeout setting in minutes. */
						__(
							"A cart becomes <i>Abandoned</i> when the cart's owner has not accessed the site in an amount of time exceeding your timeout set in the <i>WooCommerce Cart Reports</i> settings page. Your current timeout is set to %s Minutes. Don't worry! The cart will become open again when the customer returns.",
							'woocommerce_cart_reports'
						),
						$timeout_min
					),
					false
				);
				break;

			case 'Open':
				$tooltip = av8_tooltip(
					sprintf(
						/* translators: %s: Cart timeout setting in minutes. */
						__(
							"A cart is considered <i>Open</i> when the customer has accessed the site with items in the cart, within the timeout set in the <i>WooCommerce Cart Reports</i> settings page. Your current timeout is set to %s Minutes.",
							'woocommerce_cart_reports'
						),
						$timeout_min
					),
					false
				);
				break;

			case 'Converted':
				$tooltip = av8_tooltip(
					__(
						'A cart becomes <i>Converted</i> when the customer purchases the cart contents. Congrats :) ',
						'woocommerce_cart_reports'
					),
					false
				);
				break;
		}

		echo __(
			'<div id="edit_status"><mark class="color-wrapper ' . strtolower( $show_custom_state ) . '_edit">' . __(
				$show_custom_state,
				'woocommerce_cart_reports'
			) . $tooltip . '</mark></div>'
		);
	}

	/**
	 * "Cart Data" box implementation
	 *
	 */
	public function cart_useragent_meta_box() {
		global $post;
		$this->receipt->load_receipt( $post->ID );
		$ip = $this->receipt->ip_address;
		include_once WC_CART_REPORTS_ABSPATH . 'admin/views/html-user-agent-meta-box.php';
	}

	/**
	 * Cart Actions Implementation
	 *
	 */
	public function cart_action_customer_metabox() {
		global $post;
		$this->receipt->load_receipt( $post->ID );

		//Show customer / cart owner
		$this->receipt->print_cart_actions();
	}

	/*
	*
	* Remove Title meta Box from "Cart Edit" page
	*
	*/

	public function remove_title_box() {
		remove_post_type_support( 'carts', 'title' );
	}

	/*
	*
	* Remove Publish meta Box from "Cart Edit" page
	*
	*/

	public function remove_publish_box() {
		remove_meta_box( 'submitdiv', 'carts', 'side' );
	}

	/*
	*
	* Remove Author meta Box from "Cart Edit" page
	*
	*/

	public function remove_author_box() {
		remove_meta_box( 'authordiv', 'carts', 'side' );
	}

	/**
	 * Remove the WooThemes' custom configuration box for posts and pages - not needed!
	 *
	 */
	public function remove_woocustom_box() {
		remove_meta_box( 'woothemes-settings', 'carts', 'normal' );
	}

	/**
	 * Remove box that shows post slug - we don't need it!
	 *
	 */
	public function remove_slugdiv_box() {
		remove_meta_box( 'slugdiv', 'carts', 'normal' );
	}

	/*
	*
	* Add Cart Products Box
	*
	*/
	public function woocommerce_cart_items_meta_box( $post ) {

		$order_items  = (array) maybe_unserialize( get_post_meta( $post->ID, 'av8_cartitems', true ) );
		$subtotal     = get_post_meta( $post->ID, 'av8_cart_subtotal', true );
		$number_items = count( $order_items );

		include_once WC_CART_REPORTS_ABSPATH . 'admin/views/html-cart-items-meta-box.php';
	}

} //END CLASS


