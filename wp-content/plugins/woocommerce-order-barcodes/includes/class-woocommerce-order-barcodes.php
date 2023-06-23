<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use \Milon\Barcode\DNS1D;
use \Milon\Barcode\DNS2D;

use WooCommerce\OrderBarcodes\Order_Util;

class WooCommerce_Order_Barcodes {
	use Order_Util;

	/**
	 * The single instance of WooCommerce_Order_Barcodes.
	 * @var 	object
	 * @access  private
	 * @since 	1.0.0
	 */
	private static $_instance = null;

	/**
	 * Settings class object
	 * @var     object
	 * @access  public
	 * @since   1.0.0
	 */
	public $settings = null;

	/**
	 * The version number.
	 * @var     string
	 * @access  public
	 * @since   1.0.0
	 */
	public $_version;

	/**
	 * The token.
	 * @var     string
	 * @access  public
	 * @since   1.0.0
	 */
	public $_token;

	/**
	 * The main plugin file.
	 * @var     string
	 * @access  public
	 * @since   1.0.0
	 */
	public $file;

	/**
	 * The main plugin directory.
	 * @var     string
	 * @access  public
	 * @since   1.0.0
	 */
	public $dir;

	/**
	 * The plugin assets directory.
	 * @var     string
	 * @access  public
	 * @since   1.0.0
	 */
	public $assets_dir;

	/**
	 * The plugin assets URL.
	 * @var     string
	 * @access  public
	 * @since   1.0.0
	 */
	public $assets_url;

	/**
	 * Suffix for Javascripts.
	 * @var     string
	 * @access  public
	 * @since   1.0.0
	 */
	public $script_suffix;

	/**
	 * Type of barcode to be used.
	 * @var     string
	 * @access  public
	 * @since   1.0.0
	 */
	public $barcode_type = 'code128';

	/**
	 * Color of barcode.
	 * @var     string
	 * @access  public
	 * @since   1.0.0
	 */
	public $barcode_colours = array( 'foreground' => '#000000' );

	/**
	 * Constructor function.
	 * @access  public
	 * @since   1.0.0
	 * @param   string $file    Plugin file
	 * @param   string $version Plugin version
	 * @return  void
	 */
	public function __construct ( $file = WC_ORDER_BARCODES_FILE, $version = WC_ORDER_BARCODES_VERSION ) {

		// Set plugin data
		$this->_version = $version;
		$this->_token = 'woocommerce_order_barcodes';

		// Set global variables.
		$this->file = $file;
		$this->dir = dirname( $this->file );
		$this->assets_dir = trailingslashit( $this->dir ) . 'assets';
		$this->assets_url = esc_url( trailingslashit( plugins_url( '/assets/', $this->file ) ) );
		$this->script_suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

		// Apply plugin settings.
		$this->barcode_enable = get_option( 'wc_order_barcodes_enable', 'yes' );
		$this->barcode_type = get_option( 'wc_order_barcodes_type', 'code128' );
		$this->barcode_colours = get_option( 'wc_order_barcodes_colours', array( 'foreground' => '#000000' ) );

		// Declare HPOS Compatibility.
		add_action( 'before_woocommerce_init', array( $this, 'declare_hpos_compatibility' ) );

		// Register JS.
		add_action( 'admin_enqueue_scripts', array( $this, 'register_admin_assets' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'register_frontend_assets' ) );

		// Add barcode to order complete email
		add_action( 'woocommerce_email_after_order_table', array( $this, 'get_email_barcode' ), 1, 1 );

		// Display barcode on order details page.
		add_action( 'woocommerce_order_details_after_order_table', array( $this, 'get_display_barcode' ), 1, 1 );

		// Display barcode on order edit screen.
		add_action( 'add_meta_boxes', array( $this, 'add_order_metabox' ), 30, 2 );

		// Generate and save barcode as order meta.
		add_action( 'woocommerce_new_order', array( $this, 'generate_barcode' ), 1, 1 );
		add_action( 'woocommerce_resume_order', array( $this, 'generate_barcode' ), 1, 1 );

		// Save barcode from order edit screen.
		add_action( 'wp_ajax_save_barcode', array( $this, 'save_barcode' ) );
		add_action( 'wp_ajax_nopriv_save_barcode', array( $this, 'save_barcode' ) );

		// Add shortcode for barcode scanner
		add_shortcode( 'scan_barcode', array( $this, 'barcode_scan_form' ) );

		// Process barcode input/scan.
		add_action( 'wp_ajax_scan_barcode', array( $this, 'scan_barcode' ) );
		add_action( 'wp_ajax_nopriv_scan_barcode', array( $this, 'scan_barcode' ) );

		// Add check in status drop down to order edit screen.
		add_action( 'woocommerce_admin_order_data_after_order_details', array( $this, 'checkin_status_edit_field' ), 10, 1 );
		add_action( 'woocommerce_process_shop_order_meta', array( $this, 'checkin_status_edit_save' ), 40, 2 );

		// Remove the barcode from API responses. Disabled by default. Can use __return_true or __return_false to toggle.
		if ( true === apply_filters( 'wc_order_barcodes_remove_image_from_api', false ) ) {
			add_filter( 'woocommerce_rest_prepare_shop_order_object', array( $this, 'remove_barcode_from_api_response' ), null, 3 );
		}

		// Add barcode url in API responses. Enabled by default. Can use __return_true or __return_false to toggle.
		if ( true === apply_filters( 'wc_order_barcodes_add_url_in_api', true ) ) {
			add_filter( 'woocommerce_rest_prepare_shop_order_object', array( $this, 'add_barcode_url_in_api_response' ), 10, 3 );
		}

		add_action( 'init', array( $this, 'get_barcode_image' ), 10, 0 );

		// If OrderUtil does not exists, then use old filter.
		if ( $this->custom_orders_table_usage_is_enabled() ) {
			add_filter( 'woocommerce_order_query_args', array( $this, 'modify_get_orders_query_cot' ), 10, 1 );
		} else {
			add_filter( 'woocommerce_order_data_store_cpt_get_orders_query', array( $this, 'modify_get_orders_query' ), 10, 2 );
		}

		add_filter( 'woocommerce_debug_tools', array( $this, 'add_new_tools_action' ), 10, 1 );
	}

	/**
	 * Declare High-Performance Order Storage (HPOS) compatibility
	 *
	 * @see https://github.com/woocommerce/woocommerce/wiki/High-Performance-Order-Storage-Upgrade-Recipe-Book#declaring-extension-incompatibility
	 *
	 * @return void
	 */
	public function declare_hpos_compatibility() {
		if ( class_exists( '\Automattic\WooCommerce\Utilities\FeaturesUtil' ) ) {
			\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', 'woocommerce-order-barcodes/woocommerce-order-barcodes.php' );
		}
	}

	/**
	 * Add barcode fields to checkout form.
	 *
	 * @since   1.0.0
	 * @since   1.3.19 Only add hidden input for barcode string.
	 * @param   object $checkout Checkout object.
	 * @return  void
	 */
	public function add_checkout_fields( $checkout ) {

		if ( 'yes' !== $this->barcode_enable ) {
			return;
		}

		echo '<input type="hidden" name="order_barcode_text" value="' . esc_attr( $this->get_barcode_string() ) . '" />';
	}

	/**
	 * Add barcode to order meta.
	 *
	 * @since   1.0.0
	 * @param   integer $order_id Order ID.
	 * @return  void
	 */
	public function update_order_meta( $order_id = 0 ) {
		// Only run if barcodes are enabled.
		if ( 'yes' !== $this->barcode_enable ) {
			return;
		}

		// Add barcode text to order
		if ( isset( $_POST['order_barcode_text'] ) && ! empty( $_POST['order_barcode_text'] ) ) {
			$this->save_order_meta( $order_id, '_barcode_text', $_POST['order_barcode_text'] );
		}
	}

	/**
	 * Generate unique barcode.
	 *
	 * @since  1.0.0
	 * @param  int $order_id The ID of the order post.
	 * @return void
	 */
	public function generate_barcode( $order_id ) {

		if ( empty( $order_id ) ) {
			return;
		}

		if ( 'yes' !== $this->barcode_enable ) {
			return;
		}

		// Get unqiue barcode string.
		$barcode_string = $this->get_barcode_string();

		$this->save_order_meta( $order_id, '_barcode_text', $barcode_string );
	}

	/**
	 * Save barcode via ajax.
	 *
	 * @access  public
	 * @since   1.0.0
	 * @return  void
	 */
	public function save_barcode() {
		$nonce = ! empty( $_REQUEST['security'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['security'] ) ) : '';

		if ( ! wp_verify_nonce( $nonce, 'wc_order_barcodes_save_barcode_nonce' ) || ! current_user_can( 'manage_woocommerce' ) || ! isset( $_POST['order_id'] ) ) {
			die( esc_html__( 'Permission denied: Security check failed', 'woocommerce-order-barcodes' ) );
		}

		$this->update_order_meta( intval( $_POST['order_id'] ) );

		exit;
	}

	/**
	 * Get text string for barcode
	 * @access  public
	 * @since   1.0.0
	 * @return  void
	 */
	public function get_barcode_string () {

		// Use PHP's uniqid() for the barcode
		$barcode_string = uniqid();

		// Check if this barcode already exists and add increment if so
		$existing_order_id = $this->get_barcode_order( $barcode_string );
		$orig_string = $barcode_string;
		$i = 1;
		while( $existing_order_id != 0 ) {
			$barcode_string = $orig_string . $i;
			$existing_order_id = $this->get_barcode_order( $barcode_string );
			++$i;
		}

		// Return unique barcode
		return apply_filters( $this->_token . '_barcode_string', $barcode_string );

	} // End get_barcode_string ()

	/**
	 * Get barcode for display in an email.
	 * @since   1.0.0
	 * @param   object $order Order object
	 * @return  void
	 */
	public function get_email_barcode( $order ) {
		if ( ! $order ) {
			return;
		}

		if ( ! apply_filters( 'woocommerce_order_barcodes_display_barcode', true ) ) {
			return;
		}

		// Generate correctly formatted HTML for email
		ob_start(); ?>
		<table cellspacing="0" cellpadding="0" border="0" style="width:100%;border:0;text-align:center;margin-top:20px;margin-bottom:20px;">
			<tbody>
				<tr>
					<td style="text-align:center;vertical-align:middle;word-wrap:normal;">
						<?php echo $this->maybe_display_barcode( $order ); ?>
					</td>
				</tr>
			</tbody>
		</table>
		<?php
		// Get after text
		$email = ob_get_clean();

		echo $email;
	}

	/**
	 * Get barcode for frontend display
	 * @since   1.0.0
	 * @param   object $order Order object
	 * @return  void
	 */
	public function get_display_barcode( $order ) {
		if ( ! $order ) {
			return;
		}

		if ( ! apply_filters( 'woocommerce_order_barcodes_display_barcode', true ) ) {
			return;
		}

		$barcode  = '<div class="woocommerce-order-barcodes-container" style="text-align:center;">';
		$barcode .= $this->maybe_display_barcode( $order );
		$barcode .= '</div>';

		echo $barcode;
	}

	/**
	 * Add barcode meta box to order edit screen
	 *
	 * @access  public
	 * @since   1.0.0
	 * @param   String           $post_type Current post type.
	 * @param   WP_Post|WC_Order $post_or_order_object Either Post object or Order object.
	 * @return  void
	 */
	public function add_order_metabox( $post_type, $post_or_order_object ) {
		if ( ! ( 'shop_order' === $post_type || 'woocommerce_page_wc-orders' === $post_type ) || ! $this->is_order_or_post( $post_or_order_object ) ) {
			return;
		}

		$screen       = $this->get_order_admin_screen();
		$order        = $this->init_theorder_object( $post_or_order_object );
		$barcode_text = $this->get_order_or_post_meta( $order->get_id(), '_barcode_text' );

		if ( 'yes' === $this->barcode_enable || $barcode_text ) {
			add_meta_box( 'woocommerce-order-barcode', __( 'Order Barcode', 'woocommerce-order-barcodes' ), array( $this, 'get_metabox_barcode' ), $screen, 'side', 'default' );
		}
	}

	/**
	 * Get barcode for display in the order metabox
	 *
	 * @since  1.0.0
	 * @param  object $post_or_order_object Order post object.
	 * @return void
	 */
	public function get_metabox_barcode( $post_or_order_object ) {
		if ( ! $this->is_order_or_post( $post_or_order_object ) ) {
			return;
		}

		$order        = $this->init_theorder_object( $post_or_order_object );
		$barcode_text = $this->get_order_or_post_meta( $order->get_id(), '_barcode_text' );

		wp_enqueue_style( $this->_token . '-admin' );

		if ( ! $barcode_text ) {
			$this->generate_barcode( $order->get_id() );
		}

		echo '<div class="woocommerce-order-barcodes-container" style="text-align:center;">';
		echo $this->maybe_display_barcode( $order );
		echo '</div>';
	}

	/**
	 * Maybe displaying barcode.
	 *
	 * @param WC_Order $order Order object.
	 *
	 * @return String.
	 */
	public function maybe_display_barcode( $order ) {
		return ( ! wc_is_order_status( $this->get_order_status( $order ) ) ) ? $this->barcode_not_ready_error_text() : $this->display_barcode( $order->get_id(), true );
	}

	/**
	 * Display barcode as an image
	 *
	 * @since 1.0.0
	 *
	 * @param integer $order_id Order ID.
	 * @param boolean $image    Display as an image (default false).
	 * @return string The generated barcode.
	 */
	public function display_barcode( $order_id = 0, $image = false ) {
		if ( ! $order_id ) {
			return '';
		}

		// Get barcode text.
		$barcode_text = $this->get_order_or_post_meta( $order_id, '_barcode_text' );

		if ( ! $barcode_text ) {
			return esc_html__( 'Barcode does not exist!', 'woocommerce-order-barcodes' );
		}

		$foreground_color = $this->barcode_colours['foreground'];

		// Return an image (for emails and frontend order view).
		if ( $image ) {
			$barcode_url = $this->barcode_url( $order_id );

			ob_start();
			require dirname( __FILE__ ) . '/../templates/barcode-image.php';
			return ob_get_clean();
		}

		$upload_dir = wp_upload_dir();
		$dns1d = new DNS1D();
		$dns2d = new DNS2D();
		$dns1d->setStorPath( $upload_dir['path'] . '/cache/' );
		$dns2d->setStorPath( $upload_dir['path'] . '/cache/' );
		$barcode = '<div class="woocommerce-order-barcodes-container" style="text-align:center;justify-content: center;display:grid;margin-top:5px;">';

		// Generate barcode image based on string and selected type.
		switch ( $this->barcode_type ) {
			case 'datamatrix':
				$barcode .= $dns2d->getBarcodeSVG( $barcode_text, 'DATAMATRIX', 10, 10, $foreground_color );
				break;
			case 'qr':
				$barcode .= $dns2d->getBarcodeHTML( $barcode_text, 'QRCODE', 5, 5, $foreground_color );
				break;
			case 'code39':
				$barcode .= $dns1d->getBarcodeHTML( $barcode_text, 'C39', 1, 48, $foreground_color );
				break;
			case 'code93':
				$barcode .= $dns1d->getBarcodeHTML( $barcode_text, 'C93', 1, 48, $foreground_color );
				break;
			case 'code128':
			default:
				$barcode .= $dns1d->getBarcodeHTML( $barcode_text, 'C128', 1, 48, $foreground_color );
				break;
		}

		$barcode .= '<br /><span class="woocommerce-order-barcodes-number" style="color:' . esc_attr( $this->barcode_colours['foreground'] ) . ';font-family:monospace;position:relative;top:-12px;">' . esc_html( $barcode_text ) . '</span>';

		$barcode .= '</div>';

		return $barcode;
	}

	/**
	 * Get the URL for a given order's barcode
	 *
	 * @since 1.0.0
	 *
	 * @param  integer $order_id Order ID.
	 * @return string  URL for barcode.
	 */
	public function barcode_url( $order_id = 0 ) {

		if ( ! $order_id ) {
			return;
		}

		// Get barcode text.
		$barcode_text = $this->get_order_or_post_meta( $order_id, '_barcode_text' );
		return trailingslashit( get_site_url() ) . '?wc_barcode=' . $barcode_text;
	}

	/**
	 * Form for scanning barcodes
	 *
	 * @param array $params Shortcode parameters.
	 * @return string Form markup
	 */
	public function barcode_scan_form( $params = array() ) {

		// Check if user has barcode scanning permissions
		$can_scan = apply_filters( $this->_token . '_scan_permission', current_user_can( 'manage_woocommerce' ), 0 );
		if( ! $can_scan ) return;

		// Get shortcode parameters
		extract( shortcode_atts( array(
			'action' => '',
		), $params ) );

		// Add .woocommerce class as CSS namespace
		$html = '<div class="woocommerce">';

			// Create form
			$html .= '<div id="barcode-scan-form">
						<form name="barcode-scan" action="" method="post">
							<select name="scan-action" id="scan-action" class="scan_action" required>
								<option value="" ' . selected( $action, '', false ) . '>' . __( 'Select action', 'woocommerce-order-barcodes' ) . '</option>
								<option value="lookup" ' . selected( $action, 'lookup', false ) . '>' . __( 'Look up', 'woocommerce-order-barcodes' ) . '</option>
								<option value="complete" ' . selected( $action, 'complete', false ) . '>' . __( 'Complete order', 'woocommerce-order-barcodes' ) . '</option>
								<option value="checkin" ' . selected( $action, 'checkin', false ) . '>' . __( 'Check in', 'woocommerce-order-barcodes' ) . '</option>
								<option value="checkout" ' . selected( $action, 'checkout', false ) . '>' . __( 'Check out', 'woocommerce-order-barcodes' ) . '</option>
							</select>

							<input type="text" name="scan-code" id="scan-code" value="" placeholder="' . __( 'Scan or enter barcode', 'woocommerce-order-barcodes' ) . '" required />

							<input type="submit" value="' . __( 'Go', 'woocommerce-order-barcodes' ) . '" />
						</form>
					  </div>';

			// Add loading text
			$html .= '<div id="barcode-scan-loader">' . __( 'Processing barcode...', 'woocommerce-order-barcodes' ) . '</div>';

			// Add empty div for scan results to be loaded via ajax
			$html .= '<div id="barcode-scan-result"></div>';

		$html .= '</div>';

		// Load necessary JS & CSS
		$this->load_barcode_assets();

		return $html;

	} // End barcode_scan_form ()

	/**
	 * Process scanning/input of barcode
	 *
	 * @return void
	 */
	public function scan_barcode() {
		// Security check.
		$do_nonce_check = apply_filters( $this->_token . '_do_nonce_check', true );
		if ( $do_nonce_check && ! wp_verify_nonce( $_POST[ $this->_token . '_scan_nonce' ], 'scan-barcode' ) ) {
			$this->display_notice( __( 'Permission denied: Security check failed', 'woocommerce-order-barcodes' ), 'error' );
			exit;
		}

		// Retrieve order ID from barcode.
		$order_id = $this->get_barcode_order( $_POST['barcode_input'] );
		if ( ! $order_id ) {
			$this->display_notice( __( 'Invalid barcode', 'woocommerce-order-barcodes' ), 'error' );
			exit;
		}

		// Check if user has barcode scanning permissions.
		$can_scan = apply_filters( $this->_token . '_scan_permission', current_user_can( 'manage_woocommerce' ), $order_id );
		if ( ! $can_scan ) {
			$this->display_notice( __( 'Permission denied: You do not have sufficient permissions to scan barcodes', 'woocommerce-order-barcodes' ), 'error' );
			exit;
		}

		// Get order object
		$order = wc_get_order( $order_id );

		if ( ! is_a( $order, 'WC_Order' ) || is_wp_error( $order ) ) {
			$this->display_notice( __( 'Invalid order ID', 'woocommerce-order-barcodes' ), 'error' );
			exit;
		}

		$response_type = 'success';

		// Get selected action and process accordingly
		$action = esc_attr( $_POST['scan_action'] );
		switch( $action ) {
			case 'complete':
				if ( apply_filters( $this->_token . '_complete_order', true, $order_id ) ) {
					if ( 'completed' === $order->get_status() ) {
						$response      = __( 'Order already completed', 'woocommerce-order-barcodes' );
						$response_type = 'notice';
					} else {
						$order->update_status( 'completed' );
						$response = __( 'Order marked as complete', 'woocommerce-order-barcodes' );
						$order = new WC_Order( $order_id );
					}
				} else {
					$response = __( 'Not able to complete order', 'woocommerce-order-barcodes' );
					$response_type = 'error';
				}
			break;

			case 'checkin':
				if ( 'yes' === $this->get_order_or_post_meta( $order_id, '_checked_in' ) ) {
					$response      = __( 'Customer already checked in', 'woocommerce-order-barcodes' );
					$response_type = 'notice';
				} else {
					$this->save_order_meta( $order_id, '_checked_in', 'yes' );
					$response = __( 'Customer has checked in', 'woocommerce-order-barcodes' );
				}
			break;

			case 'checkout':
				if ( 'no' === $this->get_order_or_post_meta( $order_id, '_checked_in' ) ) {
					$response      = __( 'Customer already checked out', 'woocommerce-order-barcodes' );
					$response_type = 'notice';
				} else {
					$this->save_order_meta( $order_id, '_checked_in', 'no' );
					$response = __( 'Customer has checked out', 'woocommerce-order-barcodes' );
				}
			break;

			case 'lookup':
				$response = sprintf( __( 'Found matched order: #%s', 'woocommerce-order-barcodes' ), $order_id );
			break;

			default:
				$response      = __( 'Please select an action to perform', 'woocommerce-order-barcodes' );
				$response_type = 'error';
			break;
		}

		// Display response notice.
		if ( $response ) {
			$this->display_notice( $response, $response_type );
		}

		// No need to display order info if response_type is 'error'.
		if ( 'error' === $response_type ) {
			exit;
		}

		// Display check-in status if set.
		$checked_in = $this->get_order_or_post_meta( $order_id, '_checked_in' );
		if ( $checked_in ) {
			$checkin_status = ( 'yes' === $checked_in ) ? __( 'Checked in', 'woocommerce-order-barcodes' ) : __( 'Checked out', 'woocommerce-order-barcodes' );
			echo '<h3 class="checked_in ' . esc_attr( $checked_in ) . '">' . $checkin_status . '</h3>';
		}

		// Display order details template
		wc_get_template( 'myaccount/view-order.php', array(
			'status'    => get_term_by( 'slug', $order->get_status(), 'shop_order_status' ),
			'order'     => $order,
			'order_id'  => $order_id
		) );

		// Exit function to prevent '0' displaying at the end of ajax request
		exit;

	} // End scan_barcode ()

	/**
	 * Display custom WooCommerce notice
	 * @return void
	 */
	public function display_notice( $message = '', $type = 'success' ) {

		if ( ! $message ) {
			return;
		}

		// Display notice template
		echo '<div class="woocommerce-' . esc_attr( $type ) . '" role="alert">' . wc_kses_notice( $message ) . '</div>';

	}

	/**
	 * Retrieve order ID from barcode.
	 *
	 * @param string $barcode Scanned barcode.
	 *
	 * @return integer Order ID
	 */
	public function get_barcode_order_before_wc_310( $barcode = '' ) {

		if ( ! $barcode ) {
			return 0;
		}

		// Set up query.
		$args = array(
			'post_type'      => 'shop_order',
			'posts_per_page' => 1,
			'meta_key'       => '_barcode_text',
			'meta_value'     => $barcode,
			'post_status'    => array_keys( wc_get_order_statuses() ),
		);

		// Get orders
		$orders = get_posts( $args );

		// Get order ID
		$order_id = 0;
		if ( 0 < count( $orders ) ) {
			foreach ( $orders as $order ) {
				$order_id = $order->ID;
				break;
			}
		}

		return $order_id;

	} // End get_barcode_order ()

	/**
	 * Retrieve order ID from barcode.
	 *
	 * @param string $barcode Scanned barcode.
	 *
	 * @return integer Order ID
	 */
	public function get_barcode_order( $barcode = '' ) {

		if ( ! $barcode ) {
			return 0;
		}

		if ( version_compare( WC()->version, '3.1.0', '<' ) ) {
			return $this->get_barcode_order_before_wc_310( $barcode );
		}

		$args = array(
			'get_barcode_text' => $barcode,
			'limit'            => 1,
		);

		// Get orders.
		$orders = wc_get_orders( $args );

		// Get order ID.
		$order_id = 0;
		if ( 0 < count( $orders ) ) {
			foreach ( $orders as $order ) {
				$order_id = $order->get_id();
				break;
			}
		}

		return $order_id;

	} // End get_barcode_order ()

	/**
	 * Display check in status field on order edit screen
	 * @access  public
	 * @since   1.0.0
	 * @param   object $order Order object
	 * @return  void
	 */
	public function checkin_status_edit_field( $order ) {
		$order_id   = $order->get_id();
		$checked_in = $this->get_order_or_post_meta( $order_id, '_checked_in' );

		if ( $checked_in ) {
			?>
			<p class="form-field form-field-wide"><label for="checkin_status"><?php _e( 'Check in status:', 'woocommerce-order-barcodes' ) ?></label>
			<select id="checkin_status" name="checkin_status">
				<option value="<?php esc_attr_e( 'yes' ); ?>" <?php selected( 'yes', $checked_in, true ); ?>><?php _e( 'Checked in', 'woocommerce-order-barcodes' ); ?></option>
				<option value="<?php esc_attr_e( 'no' ); ?>" <?php selected( 'no', $checked_in, true ); ?>><?php _e( 'Checked out', 'woocommerce-order-barcodes' ); ?></option>
			</select></p>
			<?php
		}
	} // End checkin_status_edit_field ()

	/**
	 * Save check in status on order edit screen
	 * @access  public
	 * @since   1.0.0
	 * @param   integer $post_id Order post ID
	 * @param   object  $post    Order post object
	 * @return  void
	 */
	public function checkin_status_edit_save ( $post_id, $post ) {
		if( isset( $_POST['checkin_status'] ) && $_POST['checkin_status'] ) {
			$this->save_order_meta( $post_id, '_checked_in', $_POST['checkin_status'] );
		}
	} // End checkin_status_edit_save ()

	/**
	 * Remove the _barcode_image metadata from REST API responses.
	 * @access  public
	 * @since   1.3.1
	 * @param   object  $response 	WP_REST_Response
	 * @param   object  $object     Order Object
	 * @param   object  $request    The request made to WC-API
	 * @return  object  $response
	 */
	public function remove_barcode_from_api_response ( $response, $object, $request ) {
		if ( is_a( $response, 'WP_REST_Response' ) && isset( $response->data['meta_data'] ) ) {
			if ( 0 < count( $response->data['meta_data'] ) ) {
				foreach ( $response->data['meta_data'] as $k => $v ) {
					if ( '_barcode_image' == $v->key ) {
						unset( $response->data['meta_data'][$k] );
					}
				}
			}
		}
		return $response;
	}

	/**
	 * Add the barcode URL in REST API responses.
	 * @access  public
	 * @since   1.3.24
	 * @param   object  $response 	WP_REST_Response
	 * @param   object  $object     Order Object
	 * @param   object  $request    The request made to WC-API
	 * @return  object  $response
	 */
	public function add_barcode_url_in_api_response ( $response, $object, $request ) {
		if ( is_a( $response, 'WP_REST_Response' ) && is_a( $object, 'WC_Order' ) ) {
			$barcode_url = $this->barcode_url( $object->get_id() );

			if ( ! empty( $barcode_url ) ) {
				$response->data['barcode_url'] = $barcode_url;
			}
		}
		return $response;
	}

	/**
	 * Register all required JS & CSS for admin.
	 * @since   1.0.0
	 * @since   1.3.19 Isolate to admin assets.
	 * @return  void
	 */
	public function register_admin_assets() {
		wp_register_style( $this->_token . '-admin', esc_url( $this->assets_url ) . 'css/admin.css', array(), $this->_version );
	}

	/**
	 * Load onscan js library.
	 *
	 * @since 1.3.19
	 * @return void
	 */
	public function load_onscan_js() {
		wp_enqueue_script( $this->_token . '-frontend-onscan', esc_url( plugins_url( '/assets/js/', $this->file ) ) . 'onscan' . $this->script_suffix . '.js', array( 'jquery' ), $this->_version, true );
	}

	/**
	 * Register all required JS & CSS for frontend.
	 * @since   1.0.0
	 * @since   1.3.19 Isolate to frontend assets.
	 * @return  void
	 */
	public function register_frontend_assets() {
		wp_register_script( $this->_token . '-frontend', esc_url( $this->assets_url ) . 'js/frontend' . $this->script_suffix . '.js', array( 'jquery', $this->_token . '-frontend-onscan' ), $this->_version, true );

		wp_register_style( $this->_token . '-frontend', esc_url( $this->assets_url ) . 'css/frontend.css', array(), $this->_version );

		// Pass data to frontend JS.
		wp_localize_script(
			$this->_token . '-frontend', 'wc_order_barcodes',
			array(
				'ajaxurl' => admin_url( 'admin-ajax.php' ),
				'scan_nonce' => wp_create_nonce( 'scan-barcode' ),
			)
		);
	}

	/**
	 * Load JS & CSS required for barcode generation
	 * @since   1.0.0
	 * @since   1.3.19 Remove deprecated qr code script.
	 * @return  void
	 */
	public function load_barcode_assets() {
		$this->load_onscan_js();
		wp_enqueue_script( $this->_token . '-frontend' );

		if ( ! is_admin() ) {
			wp_enqueue_style( $this->_token . '-frontend' );
		}
	}

	/**
	 * Get barcode image.
	 *
	 * @since   1.0.0
	 * @return  void
	 */
	public function get_barcode_image() {
		if ( empty( $_GET['wc_barcode'] ) ) {
			return;
		}

		$barcode = wc_clean( wp_unslash( $_GET['wc_barcode'] ) );

		// New url format uses generated uniq_id which is not easy to guess.
		$order_id = $this->get_barcode_order( $barcode );

		if ( ! $order_id ) {
			// Either wrong order or this may be a Box Office Ticket.
			if ( class_exists( 'WC_Box_Office' ) ) {
				$order_id = WCBO()->components->ticket_barcode->get_ticket_id_from_barcode_text( $barcode );
			}
		}

		// Check if barcode is an order id
		$order = wc_get_order( $barcode );

		if ( ! $order_id && is_a( $order, 'WC_Order' ) ) {
			$order_id = $barcode;

			if ( ! ( current_user_can( 'manage_woocommerce' ) || current_user_can( 'view_order', $order_id ) ) ) {
				return;
			}
		}

		if ( ! $order_id ) {
			return;
		}

		$foreground  = $this->hex_to_rgb( $this->barcode_colours['foreground'] );
		$barcode_img = '';
		$upload_dir  = wp_upload_dir();
		$dns1d       = new DNS1D();
		$dns2d       = new DNS2D();
		$dns1d->setStorPath( $upload_dir['path'] . '/cache/' );
		$dns2d->setStorPath( $upload_dir['path'] . '/cache/' );

		// Generate barcode image based on string and selected type.
		switch ( $this->barcode_type ) {
			case 'datamatrix':
				$barcode_img = $dns2d->getBarcodePNG( $barcode, 'DATAMATRIX', 6, 6, $foreground );
				break;
			case 'qr':
				$barcode_img = $dns2d->getBarcodePNG( $barcode, 'QRCODE', 6, 6, $foreground );
				break;
			case 'code39':
				$barcode_img = $dns1d->getBarcodePNG( $barcode, 'C39', 1, 48, $foreground );
				break;
			case 'code93':
				$barcode_img = $dns1d->getBarcodePNG( $barcode, 'C93', 1, 48, $foreground );
				break;
			case 'code128':
			default:
				$barcode_img = $dns1d->getBarcodePNG( $barcode, 'C128', 1, 48, $foreground );
				break;
		}

		$barcode_img = base64_decode( $barcode_img );

		// Set headers for image output.
		if ( ini_get( 'zlib.output_compression' ) ) {
			ini_set( 'zlib.output_compression', 'Off' );
		}

		header( 'Pragma: public' );
		header( 'Expires: 0' );
		header( 'Cache-Control: must-revalidate, post-check=0, pre-check=0' );
		header( 'Cache-Control: private', false );
		header( 'Content-Transfer-Encoding: binary' );
		header( 'Content-Type: image/png' );

		exit( $barcode_img );
	}

	/**
	 * Convert hexidecimal colour to RGB.
	 *
	 * @since 1.3.21
	 *
	 * @param string $hex Hexidecimal colour code.
	 * @return array RGB colours.
	 */
	private function hex_to_rgb( $hex ) {
		$hex = ltrim( $hex, '#' );
		switch ( strlen( $hex ) ) {
			case 3:
				list( $r, $g, $b ) = sscanf( $hex, '%1s%1s%1s' );
				return array( hexdec( "$r$r" ), hexdec( "$g$g" ), hexdec( "$b$b" ) );
			case 6:
				return array_map( 'hexdec', sscanf( $hex, '%2s%2s%2s' ) );
		}

		return array( 0, 0, 0 ); // Default black.
	}

	/**
	 * Add new tools action button.
	 *
	 * @param array $tools List of tools action.
	 *
	 * @result array
	 */
	public function add_new_tools_action( $tools ) {
		$tools['generate_barcode_orders'] = array(
			'name'     => esc_html__( 'Generate Barcodes', 'woocommerce-order-barcodes' ),
			'button'   => esc_html__( 'Generate', 'woocommerce-order-barcodes' ),
			'desc'     => esc_html__( 'Generate the barcode for existing orders.', 'woocommerce-order-barcodes' ),
			'callback' => array( $this, 'generate_barcode_for_existing_orders' ),
		);

		return $tools;
	}

	/**
	 * Save Order meta.
	 *
	 * @param int    $order_id The ID of the order post.
	 * @param String $meta_name The name of the meta.
	 * @param Mixed  $meta_value The value of the meta.
	 */
	public function save_order_meta( $order_id, $meta_name, $meta_value ) {
		$order = wc_get_order( $order_id );

		if ( ! is_a( $order, 'WC_Order' ) ) {
			return false;
		}

		$order->update_meta_data( $meta_name, $meta_value );
		$order->save();
	}

	/**
	 * Get Order or post meta value.
	 *
	 * @param int    $id The ID of the order or post.
	 * @param String $meta_name The name of the meta.
	 */
	public function get_order_or_post_meta( $id, $meta_name ) {
		$order = wc_get_order( $id );

		if ( $this->is_wc_order( $order ) ) {
			return $order->get_meta( $meta_name );
		}

		// Check the post too for WC Box Office compatibility.
		$post = get_post( $id );

		if ( $this->is_wp_post( $post ) ) {
			return get_post_meta( $id, $meta_name, true );
		}

		return false;
	}

	/**
	 * Generate barcode for existing orders before WC version 3.1.0.
	 */
	public function generate_barcode_for_existing_orders_before_wc_310() {
		// Set up query.
		$args = array(
			'post_type'      => 'shop_order',
			'posts_per_page' => -1,
			'meta_query'     => array(
				array(
					'key'     => '_barcode_text',
					'compare' => 'NOT EXISTS',
				),
			),
			'post_status'    => array_keys( wc_get_order_statuses() ),
		);

		// Get orders.
		$orders = get_posts( $args );

		// Get order ID.
		$order_id = 0;
		if ( ! empty( $orders ) ) {
			foreach ( $orders as $order ) {
				$this->generate_barcode( $order->ID );
			}
		}
	}

	/**
	 * Generate barcode for existing orders.
	 */
	public function generate_barcode_for_existing_orders() {
		if ( version_compare( WC_VERSION, '3.1.0', '<' ) ) {
			$this->generate_barcode_for_existing_orders_before_wc_310();
			return;
		}

		$result = wc_get_orders(
			array(
				'barcode_not_exists' => true,
				'limit'              => -1,
			)
		);

		if ( ! empty( $result ) ) {
			foreach ( $result as $order ) {
				$this->generate_barcode( $order->get_id() );
			}
		}
	}

	/**
	 * Modify the wc_get_orders query.
	 *
	 * @param array $query_vars Query variable.
	 *
	 * @return array
	 */
	public function modify_get_orders_query_cot( $query_vars ) {
		if ( ! empty( $query_vars['barcode_not_exists'] ) ) {
			$query_vars['meta_query'][] = array(
				'key'     => '_barcode_text',
				'compare' => 'NOT EXISTS',
			);
		}

		if ( ! empty( $query_vars['get_barcode_text'] ) ) {
			$query_vars['meta_query'][] = array(
				'key'   => '_barcode_text',
				'value' => esc_attr( $query_vars['get_barcode_text'] ),
			);
		}

		return $query_vars;
	}

	/**
	 * Modify the wc_get_orders query.
	 *
	 * @param array $query Query variable.
	 * @param array $query_vars Query variable.
	 *
	 * @return array
	 */
	public function modify_get_orders_query( $query, $query_vars ) {
		if ( ! empty( $query_vars['barcode_not_exists'] ) ) {
			$query['meta_query'][] = array(
				'key'     => '_barcode_text',
				'compare' => 'NOT EXISTS',
			);
		}

		if ( ! empty( $query_vars['get_barcode_text'] ) ) {
			$query['meta_query'][] = array(
				'key'   => '_barcode_text',
				'value' => esc_attr( $query_vars['get_barcode_text'] ),
			);
		}

		return $query;
	}

	/**
	 * Main class instance - ensures only one instance of the class is loaded or can be loaded
	 * @access public
	 * @since  1.0.0
	 * @static
	 * @see    WC_Order_Barcodes()
	 * @return Main WooCommerce_Order_Barcodes instance
	 */
	public static function instance ( $file = WC_ORDER_BARCODES_FILE, $version = WC_ORDER_BARCODES_VERSION ) {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self( $file, $version );
		}
		return self::$_instance;
	} // End instance ()

	/**
	 * Cloning is forbidden
	 * @access public
	 * @since  1.0.0
	 */
	public function __clone () {
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?' ), $this->_version );
	} // End __clone ()

	/**
	 * Unserializing instances of this class is forbidden
	 * @access public
	 * @since  1.0.0
	 */
	public function __wakeup () {
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?' ), $this->_version );
	} // End __wakeup ()
}
