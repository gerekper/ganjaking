<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Main Woocommerce_Gateway_Purchase_Order Class
 *
 * @class Woocommerce_Gateway_Purchase_Order
 * @version	1.0.0
 * @since 1.0.0
 * @package	Woocommerce_Gateway_Purchase_Order
 * @author Matty
 */
final class Woocommerce_Gateway_Purchase_Order extends WC_Payment_Gateway {
	/**
	 * The token.
	 *
	 * @var     string
	 * @since   1.0.0
	 */
	public $token;

	/**
	 * The version number.
	 *
	 * @var     string
	 * @since   1.0.0
	 */
	public $version;

	/**
	 * The plugin URL.
	 *
	 * @var     string
	 * @since   1.0.0
	 */
	public $plugin_url;

	/**
	 * The plugin path.
	 *
	 * @var     string
	 * @since   1.0.0
	 */
	public $plugin_path;

	/**
	 * The plugin instructions.
	 *
	 * @var     string
	 * @since   1.0.0
	 */
	public $instructions;

	/**
	 * Constructor function.
	 *
	 * @since   1.0.0
	 * @return  void
	 */
	public function __construct () {
		$this->token 			= 'woocommerce-gateway-purchase-order';
		$this->plugin_url 		= plugin_dir_url( __FILE__ );
		$this->plugin_path 		= plugin_dir_path( __FILE__ );
		$this->version 			= '1.1.5';

		register_activation_hook( __FILE__, array( $this, 'install' ) );

		add_action( 'init', array( $this, 'load_plugin_textdomain' ) );

		$this->id                 = 'woocommerce_gateway_purchase_order';
		$this->method_title       = __( 'Purchase Order', 'woocommerce-gateway-purchase-order' );
		$this->method_description = __( 'Purchase Order gateway adds a field to the checkout screen where your customer enters their purchase order number, provided by you directly to the customer in a manual agreement.' );
		$this->has_fields         = true;

		$this->init_form_fields();
		$this->init_settings();

		$this->title = $this->settings['title'];
		$this->description = $this->settings['description'];
		$this->instructions = $this->settings['instructions'];


		add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options' ) );
		add_action( 'woocommerce_thankyou_' . $this->id, array( $this, 'thank_you' ) );
	}

	/**
	 * Register the gateway's fields.
	 * @access public
	 * @since  1.0.0
	 * @return void
	 */
	public function init_form_fields () {
	   $this->form_fields = array(
				'enabled' => array(
					'title' => __( 'Enable/Disable', 'woocommerce-gateway-purchase-order' ),
					'type' => 'checkbox',
					'label' => __( 'Enable Purchase Orders.', 'woocommerce-gateway-purchase-order' ),
					'default' => 'no' ),
				'title' => array(
					'title' => __( 'Title:', 'woocommerce-gateway-purchase-order' ),
					'type'=> 'text',
					'description' => __( 'This controls the title which the user sees during checkout.', 'woocommerce-gateway-purchase-order' ),
					'default' => __( 'Purchase Order', 'woocommerce-gateway-purchase-order' ) ),
				'description' => array(
					'title' => __( 'Description:', 'woocommerce-gateway-purchase-order' ),
					'type' => 'textarea',
					'description' => __( 'This controls the description which the user sees during checkout.', 'woocommerce-gateway-purchase-order' ),
					'default' => __( 'Please add your P.O. Number to the purchase order field.', 'woocommerce-gateway-purchase-order' ) ),
				 'instructions' => array(
					'title' => __('Thank You note:', 'woocommerce-gateway-purchase-order'),
					'type' => 'textarea',
					'instructions' => __( 'Instructions that will be added to the thank you page.', 'woocommerce-gateway-purchase-order' ),
					'default' => '' )

			);
	}

	/**
	 * Register the gateway's admin screen.
	 * @access public
	 * @since  1.0.0
	 * @return void
	 */
	public function admin_options() {
		echo '<h3>' . esc_html__( 'Purchase Order Payment Gateway', 'woocommerce-gateway-purchase-order' ) . '</h3>';
		echo '<table class="form-table">';
		// Generate the HTML For the settings form.
		$this->generate_settings_html();
		echo '</table>';
	}

	/**
	 * Register the gateway's payment fields.
	 *
	 * @access public
	 * @since  1.0.0
	 * @return void
	 */
	public function payment_fields() {
		if ( $this->description ) {
			echo wp_kses_post( wpautop( wptexturize( $this->description ) ) );
		}

		// In case of an AJAX refresh of the page, check the form post data to see if we can repopulate an previously entered PO.
		$po_number = '';
		// phpcs:disable WordPress.Security.NonceVerification.Recommended
		if ( isset( $_REQUEST['post_data'] ) ) {
			parse_str( sanitize_text_field( wp_unslash( $_REQUEST['post_data'] ) ), $post_data );
			if ( isset( $post_data['po_number_field'] ) ) {
				$po_number = $post_data['po_number_field'];
			}
		}
		?>
		<fieldset>
			<p class="form-row form-row-first">
				<label for="poorder"><?php esc_html_e( 'Purchase Order', 'woocommerce-gateway-purchase-order' ); ?> <span class="required">*</span></label>
				<input type="text" class="input-text" value="<?php echo esc_attr( $po_number ); ?>" id="po_number_field" name="po_number_field" />
			</p>
		</fieldset>
		<?php
		// phpcs:enable
	}

	/**
	 * Process the payment.
	 *
	 * @param  int $order_id Order ID.
	 * @access public
	 * @since  1.0.0
	 * @return array An array containing the result text and a redirect URL.
	 */
	public function process_payment( $order_id ) {
		$order = new WC_Order( $order_id );

		$poorder = $this->get_post( 'po_number_field' );

		if ( isset( $poorder ) ) {
			$order->update_meta_data( '_po_number', esc_attr( $poorder ) );
			$order->set_transaction_id( esc_attr( $poorder ) );
			$order->save();
		}

		$order->update_status( 'on-hold', __( 'Waiting to be processed', 'woocommerce-gateway-purchase-order' ) );

		// Reduce stock levels.
		wc_reduce_stock_levels( $order->get_id() );

		// Remove cart.
		WC()->cart->empty_cart();

		// Return thankyou redirect.
		return array(
			'result'   => 'success',
			'redirect' => $this->get_return_url( $order ),
		);
	}

	/**
	 * Display thank you instructions.
	 * @access public
	 * @since  1.0.0
	 * @return void
	 */
	public function thank_you() {
		echo '' !== $this->instructions ? wp_kses_post( wpautop( $this->instructions ) ) : '';
	}

	/**
	 * Retrieve a posted value, if it exists.
	 *
	 * @access public
	 * @since  1.0.0
	 * @param str $name Name.
	 * @return string/null
	 */
	private function get_post( $name ) {
		// phpcs:disable WordPress.Security.NonceVerification.Missing
		if ( isset( $_POST[ $name ] ) ) {
			return wp_kses_post( wp_unslash( $_POST[ $name ] ) );
		} else {
			return null;
		}
		// phpcs:enable
	}

	/**
	 * Validate the fields.
	 *
	 * @return boolean
	 */
	public function validate_fields() {
		$poorder = $this->get_post( 'po_number_field' );
		if ( ! $poorder ) {
			if ( function_exists( 'wc_add_notice' ) ) {
				// Replace deprecated $woocommerce_add_error() function.
				wc_add_notice( esc_html__( 'Please enter your PO Number.', 'woocommerce-gateway-purchase-order' ), 'error' );
			} else {
				WC()->add_error( esc_html__( 'Please enter your PO Number.', 'woocommerce-gateway-purchase-order' ) );
			}
			return false;
		} else {
			return true;
		}
	}

	/**
	 * Load the localisation file.
	 * @access  public
	 * @since   1.0.0
	 * @return  void
	 */
	public function load_plugin_textdomain() {
		load_plugin_textdomain( 'woocommerce-gateway-purchase-order', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
	}

	/**
	 * Cloning is forbidden.
	 *
	 * @since 1.0.0
	 */
	public function __clone () {
		_doing_it_wrong( __FUNCTION__, esc_html__( 'Cheatin&#8217; huh?' ), '1.0.0' );
	}

	/**
	 * Unserializing instances of this class is forbidden.
	 *
	 * @since 1.0.0
	 */
	public function __wakeup () {
		_doing_it_wrong( __FUNCTION__, esc_html__( 'Cheatin&#8217; huh?' ), '1.0.0' );
	}

	/**
	 * Installation. Runs on activation.
	 * @access  public
	 * @since   1.0.0
	 * @return  void
	 */
	public function install () {
		$this->_log_version_number();
	}

	/**
	 * Log the plugin version number.
	 * @access  private
	 * @since   1.0.0
	 * @return  void
	 */
	private function _log_version_number () {
		// Log the version number.
		update_option( $this->token . '-version', $this->version );
	}
}
