<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
if ( ! class_exists( 'Pie_WCWL_Frontend_Product' ) ) {
	/**
	 * Legacy code - no longer used
	 *
	 * Abstract class for all frontend classes to load from
	 *
	 * @package  WooCommerce Waitlist
	 */
	abstract class Pie_WCWL_Frontend_Product {

		/**
		 * Current product object
		 *
		 * @var WC_Product
		 */
		protected $product;
		/**
		 * Child products of current parent product
		 *
		 * @var array
		 */
		protected $children = array();
		/**
		 * Current user object
		 *
		 * @var object
		 */
		protected $user;
		/**
		 * Has the user just requested to update the waitlist
		 *
		 * @var bool
		 */
		protected $user_modified_waitlist = false;
		/**
		 * Is WPML installed and active?
		 *
		 * @var bool
		 */
		public $has_wpml = false;

		/**
		 * Pie_WCWL_Frontend_Product constructor.
		 *
		 * Notices are cleared on shutdown to ensure waitlist notices don't persist to cart
		 */
		public function __construct() {
			global $sitepress;
			$this->user                   = is_user_logged_in() ? wp_get_current_user() : false;
			$this->user_modified_waitlist = $this->user_has_altered_waitlist();
			$this->has_wpml               = isset( $sitepress );
			$this->setup_text_strings();
			$this->setup_product();
			if ( ! $this->product && ! $this->is_event_page() ) {
				return;
			}
			$this->setup_waitlist();
			add_filter( 'woocommerce_add_to_cart_url', array( $this, 'remove_waitlist_parameters_from_query_string' ) );
		}

		/**
		 * Determine product for the current page
		 *
		 * @param int $product_id
		 */
		protected function setup_product( $product_id = 0 ) {
			if ( ! $product_id ) {
				$product_id = $this->get_product_id();
			}
			if ( $this->has_wpml ) {
				$product_id = Pie_WCWL_Frontend_Init::get_main_product_id( $product_id );
			}
			$this->product = wc_get_product( $product_id );
		}

		/**
		 * Setup required variables for the frontend UI
		 *
		 * @access public
		 * @return void
		 * @since  1.8.0
		 */
		protected function setup_waitlist() {
			if ( $this->product ) {
				if ( $this->product->get_children() ) {
					$this->setup_child_waitlists();
				} else {
					$this->product->waitlist = $this->get_waitlist( $this->product );
				}
			}
		}

		/**
		 * Return current product ID
		 *
		 * @return int|mixed|NULL
		 *
		 * @since 1.8.0
		 */
		protected function get_product_id() {
			global $post;
			if ( Pie_WCWL_Frontend_Init::is_ajax_variation_request() ) {
				if ( $this->has_wpml ) {
					$product_id = Pie_WCWL_Frontend_Init::get_main_product_id( absint( $_REQUEST['product_id'] ) );
				} else {
					$product_id = absint( $_REQUEST['product_id'] );
				}
			} elseif ( ! is_product() && ( ! empty( $post->post_content ) && strstr( $post->post_content, '[product_page' ) ) ) {
				$product_id = $this->get_product_id_from_post_content( $post->post_content );
			} elseif ( ! is_product() && ( ! empty( $post->post_content ) && strstr( $post->post_content, '[products' ) ) ) {
				$product    = wc_get_products( array( 'type' => 'simple', 'limit' => 1 ) );
				$product_id = $product[0]->get_id();
			} else {
				$product_id = $post->ID;
			}

			return $product_id;
		}

		/**
		 * Look fpr the product ID in the provided post content
		 *
		 * @param $content
		 *
		 * @return mixed
		 */
		public function get_product_id_from_post_content( $content ) {
			$content_after_shortcode    = substr( $content, strpos( $content, '[product_page' ) + 1 );
			$content_before_closing_tag = strtok( $content_after_shortcode, ']' );
			$product_id                 = filter_var( $content_before_closing_tag, FILTER_SANITIZE_NUMBER_INT );

			return $product_id;
		}

		/**
		 * Setup child product waitlists for parent product
		 */
		protected function setup_child_waitlists() {
			$children = array();
			foreach ( $this->product->get_children() as $child_id ) {
				if ( $this->has_wpml ) {
					$child_id = $this->get_main_product_id( $child_id );
				}
				$child                 = wc_get_product( $child_id );
				$child->waitlist       = $this->get_waitlist( $child );
				$children[ $child_id ] = $child;
			}
			$this->children = $children;
		}

		/**
		 * Return the waitlist for the given product
		 *
		 * @param $product
		 *
		 * @return mixed
		 */
		protected function get_waitlist( $product ) {
			if ( isset( $this->children[ $product->get_id() ]->waitlist ) ) {
				return $this->children[ $product->get_id() ]->waitlist;
			}

			return new Pie_WCWL_Waitlist( $product );
		}

		/**
		 * Return the waitlist for the given child product
		 *
		 * @param $child
		 *
		 * @return mixed
		 */
		protected function get_child_waitlist( $child ) {
			$child_id = $child->get_id();

			return $this->children[ $child_id ]->waitlist;
		}

		/**
		 * Are we on an event page?
		 *
		 * @return bool
		 */
		protected function is_event_page() {
			if ( ! function_exists( 'tribe_is_event' ) ) {
				return false;
			}
			if ( 'yes' != get_option( 'woocommerce_waitlist_events' ) ) {
				return false;
			}
			return tribe_is_event();
		}

		/**
		 * Checks to see if request to adjust waitlist is valid for user
		 *
		 * @access private
		 * @return boolean true if valid, false if not
		 * @since  1.3
		 */
		protected function user_has_altered_waitlist() {
			if ( isset( $_REQUEST[ WCWL_SLUG ] ) && is_numeric( $_REQUEST[ WCWL_SLUG ] ) && ! isset( $_REQUEST['added-to-cart'] ) ) {
				return true;
			} else {
				return false;
			}
		}

		/**
		 * Check if current user is able to use the waitlist functionality and output error if not
		 *
		 * @return bool
		 */
		protected function new_user_can_join_waitlist() {
			if ( 'yes' == get_option( 'woocommerce_waitlist_registration_needed' ) ) {
				wc_add_notice( apply_filters( 'wcwl_join_waitlist_user_requires_registration_message_text', $this->users_must_register_and_login_message_text ), 'error' );

				return false;
			} elseif ( ! isset( $_REQUEST['wcwl_email'] ) || ! is_email( $_REQUEST['wcwl_email'] ) ) {
				wc_add_notice( apply_filters( 'wcwl_join_waitlist_invalid_email_message_text', $this->join_waitlist_invalid_email_message_text ), 'error' );

				return false;
			} elseif ( $this->product->is_type( 'grouped' ) && empty( $_REQUEST['wcwl_join'] ) ) {
				wc_add_notice( apply_filters( 'wcwl_toggle_waitlist_no_product_message_text', $this->toggle_waitlist_no_product_message_text ), 'error' );

				return false;
			} else {
				$this->setup_new_user( sanitize_email( $_REQUEST['wcwl_email'] ) );

				return true;
			}
		}

		/**
		 * Find existing user by email given or create a new user if required
		 *
		 * @param $email
		 */
		protected function setup_new_user( $email ) {
			if ( email_exists( $email ) ) {
				$this->user = get_user_by( 'email', $email );
			} else {
				$this->user = get_user_by( 'id', WooCommerce_Waitlist_Plugin::create_new_customer_from_email( $email ) );
			}
		}

		/**
		 * Handle functionality for logged out user joining/leaving a waitlist
		 *
		 * Reset user to false as creating new users can cause issues
		 *
		 * @param $waitlist object Waitlist object that needs updating
		 */
		protected function handle_waitlist_when_new_user( $waitlist ) {
			if ( $this->new_user_can_join_waitlist() ) {
				$waitlist->register_user( $this->user );
				wc_add_notice( apply_filters( 'wcwl_join_waitlist_success_message_text', $this->join_waitlist_success_message_text ) );
			}
			$this->user = false;
		}

		/**
		 * Process the waitlist request
		 *
		 * @param $waitlist object Waitlist object that needs updating
		 */
		protected function toggle_waitlist_action( $waitlist ) {
			if ( 'leave' == $_GET[ WCWL_SLUG . '_action' ] && $waitlist->user_is_registered( $this->user->ID ) && $waitlist->unregister_user( $this->user ) ) {
				wc_add_notice( apply_filters( 'wcwl_leave_waitlist_success_message_text', $this->leave_waitlist_success_message_text ) );
			}
			if ( ( isset ( $_GET[ WCWL_SLUG . '_action' ] ) && 'join' === $_GET[ WCWL_SLUG . '_action' ] ) && ! $waitlist->user_is_registered( $this->user->ID ) && $waitlist->register_user( $this->user ) ) {
				wc_add_notice( apply_filters( 'wcwl_join_waitlist_success_message_text', $this->join_waitlist_success_message_text ) );
			}
		}

		/**
		 * Get HTML for waitlist email
		 *
		 * @access protected
		 * @return  string
		 * @since  1.8.0
		 */
		protected function get_waitlist_email_field() {
			$html = '<div class="wcwl_email_elements">';
			$html .= apply_filters( 'wcwl_frontend_email_field_html', '<div class="wcwl_email_field">
						<label for="wcwl_email" class="wcwl_email_label wcwl_hide">' . apply_filters( 'wcwl_email_field_label', $this->email_field_placeholder_text ) . '</label>
						<input type="email" name="wcwl_email" class="wcwl_email" placeholder="' . $this->email_field_placeholder_text . '" />
				    </div>' );
			if ( 'yes' == get_option( 'woocommerce_waitlist_new_user_opt-in' ) ) {
				$notice = apply_filters( 'wcwl_new_user_opt-in_text', __( 'By ticking this box you agree to an account being created using the given email address and to receive waitlist communications by email', 'woocommerce-waitlist' ) );
				$html .= $this->get_waitlist_opt_in_html( $notice );
			}
			$html .= '</div>';

			return apply_filters( 'wcwl_frontend_logged_out_user_html', $html );
		}

		/**
		 * Return HTML for user opt in
		 *
		 * @param $notice
		 *
		 * @return mixed|void
		 * @since 1.8.0
		 */
		protected function get_waitlist_opt_in_html( $notice ) {
			$html = '<div class="wcwl_optin">';
			$html .= '<input type="checkbox" name="wcwl_optin" id="wcwl_optin">';
			$html .= '<label for="wcwl_optin">' . $notice . '</label>';
			$html .= '</div>';

			return apply_filters( 'wcwl_signup_notice_html', $html );
		}

		/**
		 * Removes waitlist parameters from query string
		 *
		 * @access public
		 *
		 * @param  string $query_string current query
		 *
		 * @return string               updated query
		 */
		public function remove_waitlist_parameters_from_query_string( $query_string ) {
			return esc_url( remove_query_arg( array(
				WCWL_SLUG,
				WCWL_SLUG . '_nonce',
				'wcwl_email',
				'wcwl_join',
				'wcwl_leave',
			), $query_string ) );
		}

		/**
		 * Get the current language from the given URL
		 *
		 * @param $url
		 *
		 * @return mixed
		 */
		public function get_language_from_url( $url ) {
			$parts = parse_url( $url );
			parse_str( $parts['query'], $query );

			return $query['lang'];
		}

		/**
		 * Get the current page URL
		 *
		 * @return string|void
		 */
		public function get_current_product_url() {
			global $wp;

			return home_url( add_query_arg( array(), $wp->request ) );
		}

		/**
		 * Generate the URL to redirect the user to after their waitlist request has been processed
		 *
		 * @param $action
		 * @param $product_id
		 *
		 * @return mixed|void
		 */
		public function create_button_url( $action, $product_id ) {
			global $wp;
			if ( ( ! isset( $wp->request ) || empty( $wp->request ) ) && '/?wc-ajax=get_variation' == $_SERVER['REQUEST_URI'] ) {
				$request_url = add_query_arg( array(), $_SERVER['HTTP_REFERER'] );
			} else {
				$request_url = $wp->request ? home_url( add_query_arg( array(), trailingslashit( $wp->request ) ) ) : get_permalink( $product_id );
			}
			$url = add_query_arg( array(
				WCWL_SLUG             => $product_id,
				WCWL_SLUG . '_action' => $action,
				WCWL_SLUG . '_nonce'  => wp_create_nonce( __FILE__ ),
			), $request_url );

			return apply_filters( 'wcwl_toggle_waitlist_url', $url, $product_id );
		}

		/**
		 * Check product options to ensure waitlist is enabled
		 *
		 * @param $product_id
		 *
		 * @return bool
		 */
		public function waitlist_is_enabled_for_product( $product_id ) {
			$options = get_post_meta( $product_id, 'wcwl_options', true );
			if ( isset( $options['enable_waitlist'] ) && 'false' == $options['enable_waitlist'] ) {
				return false;
			}

			return true;
		}

		/**
		 * Sets up the text strings used by the plugin in the front end
		 *
		 * @access private
		 * @return void
		 * @since  1.0
		 */
		protected function setup_text_strings() {
			$this->join_waitlist_button_text                    = __( 'Join waitlist', 'woocommerce-waitlist' );
			$this->leave_waitlist_button_text                   = __( 'Leave waitlist', 'woocommerce-waitlist' );
			$this->confirm_waitlist_button_text                 = __( 'Confirm', 'woocommerce-waitlist' );
			$this->update_waitlist_button_text                  = __( 'Update waitlist', 'woocommerce-waitlist' );
			$this->join_waitlist_message_text                   = __( "Join the waitlist to be emailed when this product becomes available", 'woocommerce-waitlist' );
			$this->leave_waitlist_message_text                  = __( 'You are on the waitlist for this product', 'woocommerce-waitlist' );
			$this->leave_waitlist_success_message_text          = __( 'You have been removed from the waitlist for this product', 'woocommerce-waitlist' );
			$this->leave_ticket_waitlist_success_message_text   = __( 'You have been removed from the waitlist for this ticket', 'woocommerce-waitlist' );
			$this->join_waitlist_success_message_text           = __( 'You have been added to the waitlist for this product', 'woocommerce-waitlist' );
			$this->join_ticket_waitlist_success_message_text    = __( 'You have been added to the waitlist for this ticket', 'woocommerce-waitlist' );
			$this->update_waitlist_success_message_text         = __( 'You have updated your waitlist for these products', 'woocommerce-waitlist' );
			$this->toggle_waitlist_no_product_message_text      = __( 'You must select at least one product for which to update the waitlist', 'woocommerce-waitlist' );
			$this->toggle_waitlist_ambiguous_error_message_text = __( 'Something seems to have gone awry. Are you trying to mess with the fabric of the universe?', 'woocommerce-waitlist' );
			$this->join_waitlist_invalid_email_message_text     = __( 'You must provide a valid email address to join the waitlist for this product', 'woocommerce-waitlist' );
			$this->users_must_register_and_login_message_text   = sprintf( __( 'You must register to use the waitlist feature. Please %1$slogin or create an account%2$s', 'woocommerce-waitlist' ), '<a href="' . wc_get_page_permalink( 'myaccount' ) . '">', '</a>' );
			$this->grouped_product_message_text                 = __( "Check the box alongside any Out of Stock products and update the waitlist to be emailed when those products become available", 'woocommerce-waitlist' );
			$this->no_user_grouped_product_message_text         = __( "Check the box alongside any Out of Stock products, enter your email address and join the waitlist to be notified when those products become available", 'woocommerce-waitlist' );
			$this->grouped_product_joined_message_text          = __( 'You have updated the selected waitlist/s', 'woocommerce-waitlist' );
			$this->email_field_placeholder_text                 = __( "Email address", 'woocommerce-waitlist' );
			$this->registered_opt_in_text                       = __( 'By ticking this box you agree to receive waitlist communications by email', 'woocommerce-waitlist' );
		}
	}
}
