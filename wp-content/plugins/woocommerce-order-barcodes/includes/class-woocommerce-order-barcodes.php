<?php

if ( ! defined( 'ABSPATH' ) ) exit;

class WooCommerce_Order_Barcodes {

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
	public $barcode_colours = array( 'foreground' => '#000000', 'background' => '#FFFFFF' );

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

		// Set global variables
		$this->file = $file;
		$this->dir = dirname( $this->file );
		$this->assets_dir = trailingslashit( $this->dir ) . 'assets';
		$this->assets_url = esc_url( trailingslashit( plugins_url( '/assets/', $this->file ) ) );
		$this->script_suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

		// Apply plugin settings
		$this->barcode_enable = get_option( 'wc_order_barcodes_enable', 'yes' );
		$this->barcode_type = get_option( 'wc_order_barcodes_type', 'code128' );
		$this->barcode_colours = get_option( 'wc_order_barcodes_colours', array( 'foreground' => '#000000', 'background' => '#FFFFFF' ) );

		// Register JS
		add_action( 'admin_enqueue_scripts', array( $this, 'register_assets' ), 1 );
		add_action( 'wp_enqueue_scripts', array( $this, 'register_assets' ), 1 );

		// Add barcode to order complete email
		add_action( 'woocommerce_email_after_order_table', array( $this, 'get_email_barcode' ), 1, 1 );
		add_action( 'init', array( $this, 'get_barcode_image' ), 10, 0 );

		// Display barcode on order details page
		add_action( 'woocommerce_order_details_after_order_table', array( $this, 'get_display_barcode' ), 1, 1 );

		// Display barcode on order edit screen
		add_action( 'add_meta_boxes', array( $this, 'add_order_metabox' ), 30 );

		// Generate and save barcode as order meta
		add_action( 'woocommerce_after_checkout_form', array( $this, 'generate_barcode_checkout' ) );
		add_action( 'woocommerce_after_order_notes', array( $this, 'add_checkout_fields' ), 1, 1 );
		add_action( 'woocommerce_new_order', array( $this, 'update_order_meta' ), 1, 1 );
		add_action( 'woocommerce_resume_order', array( $this, 'update_order_meta' ), 1, 1 );

		// Save barcode from order edit screen
		add_action( 'wp_ajax_save_barcode', array( $this, 'save_barcode' ) );
		add_action( 'wp_ajax_nopriv_save_barcode', array( $this, 'save_barcode' ) );

		// Add shortcode for barcode scanner
		add_shortcode( 'scan_barcode', array( $this, 'barcode_scan_form' ) );

		// Process barcode input/scan
		add_action( 'wp_ajax_scan_barcode', array( $this, 'scan_barcode' ) );
		add_action( 'wp_ajax_nopriv_scan_barcode', array( $this, 'scan_barcode' ) );

		// Add check in status drop down to order edit screen
		add_action( 'woocommerce_admin_order_data_after_order_details', array( $this, 'checkin_status_edit_field' ), 10, 1 );
		add_action( 'woocommerce_process_shop_order_meta', array( $this, 'checkin_status_edit_save' ), 40, 2 );

		// Remove the barcode from API responses. Disabled by default. Can use __return_true or __return_false to toggle.
		if ( true === apply_filters( 'wc_order_barcodes_remove_image_from_api', false ) ) {
			add_filter( 'woocommerce_rest_prepare_shop_order_object', array( $this, 'remove_barcode_from_api_response' ), null, 3 );
		}

		// Handle localisation
		$this->load_plugin_textdomain();
		add_action( 'init', array( $this, 'load_localisation' ), 0 );
		add_action( 'init', array( $this, 'includes' ) );

	} // End __construct ()

	/**
	 * Add barcode fields to checkout form
	 * @access  public
	 * @since   1.0.0
	 * @param   object $checkout Checkout object
	 * @return  void
	 */
	public function add_checkout_fields ( $checkout ) {

		if( 'yes' != $this->barcode_enable ) {
			return;
		}

		// Prepare barcode image
	    woocommerce_form_field( 'order_barcode_image', array(
	        'type'	=> 'text',
	        'class' => array( 'order_barcode_image' ),
		), $checkout->get_value( 'order_barcode_image' ) );

	    // Prepare barcode text
		woocommerce_form_field( 'order_barcode_text', array(
	        'type'	=> 'text',
	        'class'	=> array( 'order_barcode_text' ),
		), $checkout->get_value( 'order_barcode_text' ) );

	} //End add_checkout_fields ()

	/**
	 * Add barcode to order meta
	 * @access  public
	 * @since   1.0.0
	 * @param   integer $order_id Order ID
	 * @return  void
	 */
	public function update_order_meta ( $order_id = 0 ) {

		// Only run if barcodes are enabled
		if( 'yes' != $this->barcode_enable ) {
			return;
		}

		// Add encoded barcode image to order
		if ( isset( $_POST['order_barcode_image'] ) && ! empty( $_POST['order_barcode_image'] ) ) {
	        update_post_meta( $order_id, '_barcode_image', $_POST['order_barcode_image'] );
	    }

	    // Add barcode text to order
	    if ( isset( $_POST['order_barcode_text'] ) && ! empty( $_POST['order_barcode_text'] ) ) {
	        update_post_meta( $order_id, '_barcode_text', $_POST['order_barcode_text'] );
	    }

	    // Add order note
	    $order = new WC_Order( $order_id );
	    $barcode_url = $this->barcode_url( $order_id );
	    $order_note = sprintf( __( 'Barcode generated successfully: %s', 'woocommerce-order-barcodes' ), '<a href="' . esc_url( $barcode_url ) . '" target="_blank">' . $barcode_url . '</a>' );
	    $order->add_order_note( $order_note );

	} // End update_order_meta ()

	/**
	 * Generate barcode on checkout page
	 * @access  public
	 * @since   1.0.0
	 * @return  void
	 */
	public function generate_barcode_checkout () {
		$this->generate_barcode( 'checkout' );
	}

	/**
	 * Generate unique barcode
	 * @access  public
	 * @since   1.0.0
	 * @return  void
	 */
	public function generate_barcode ( $context = 'checkout' ) {

		if( 'yes' != $this->barcode_enable ) {
			return;
		}

		// Load JS & CSS needed for barcode generation
		$this->load_barcode_assets();

		// Get unqiue barcode string
		$barcode_string = $this->get_barcode_string();

		// Generate barcode image based on string and selected type
		switch( $this->barcode_type ) {
			case 'qr':

				// Build up JS for QR code generation
				$js = "$( '#barcode_container' ).qrcode( { text: '" . $barcode_string . "', label: '" . $barcode_string . "', fill: '" . $this->barcode_colours['foreground'] . "', background: '" . $this->barcode_colours['background'] . "', render: 'image', width: 100, height: 100 } );";
				$barcode_obj = array( 'img', 'src' );

			break;
			default:

				// Set extra data for certain barcode types
				switch( $this->barcode_type ) {
					case 'code93': $data_tail = ', crc: true'; break;
					case 'datamatrix': $data_tail = ', rect: false';
					default: $data_tail = '';
				}

				// Build up JS for barcode generation
				$js = "$( '#barcode_container' ).barcode( { code: '" . $barcode_string . "'" . $data_tail . " }, '" . $this->barcode_type . "', { color: '" . $this->barcode_colours['foreground'] . "', bgColor: '" . $this->barcode_colours['background'] . "', barWidth: 2, barHeight: 70, fontSize: 14, output: 'bmp' } );";
				$barcode_obj = array( 'object', 'data' );

			break;
		}

		$js .= "var barcode = $('#barcode_container " . $barcode_obj[0] . "').attr( '" . $barcode_obj[1] . "' );";

		switch( $context ) {
			case 'checkout':
				$js .= "$('#order_barcode_image').val( barcode );
						$('#order_barcode_text').val( '" . $barcode_string . "' );";
				$tail_content = '';
			break;

			case 'dashboard':
				global $post;
				if( isset( $post->ID ) ) {
					$js .= "$.post( '" . admin_url( 'admin-ajax.php' ) . "', { action: 'save_barcode', order_id: '" . $post->ID . "', order_barcode_image: barcode, order_barcode_text: '" . $barcode_string . "' } );";
					$tail_content = '<span style="color:' . $this->barcode_colours['foreground'] . ';font-family:monospace;text-align:center;width:100%;display:block;">' . $barcode_string . '</span>';
				}
			break;
		}

		ob_start();

		// Set barcode container
		echo '<div id="barcode_container"></div>' . $tail_content;

		// Run JS for barcode generation
		wc_enqueue_js( $js );

		$barcode = ob_get_clean();

		// Display barcode
		echo $barcode;

	} // End generate_barcode ()

	/**
	 * Save barcode via ajax
	 * @access  public
	 * @since   1.0.0
	 * @return  void
	 */
	public function save_barcode() {

		if( ! current_user_can( 'manage_woocommerce' ) ) {
			exit;
		}

		if( ! isset( $_POST['order_id'] ) ) {
			exit;
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
	 * Get barcode for display in an email
	 * @access  public
	 * @since   1.0.0
	 * @param   object $order Order object
	 * @return  void
	 */
	public function get_email_barcode ( $order ) {

		if( ! $order ) return;

		if ( version_compare( WC_VERSION, '3.0.0', '>=' ) ) {
			$order_id = $order->get_id();
		} else {
			$order_id = $order->id;
		}

		// Generate correctly formatted HTML for email
		ob_start(); ?>
<table cellspacing="0" cellpadding="0" border="0" style="width:100%;border:0;text-align:center;margin-top:20px;margin-bottom:20px;">
	<tbody>
		<tr>
			<td style="text-align:center;vertical-align:middle;word-wrap:normal;">
				<p>
<?php
		// Get before text
		$before = ob_get_clean();

		ob_start(); ?>
				</p>
			</td>
		</tr>
	</tbody>
</table>
<?php
		// Get after text
		$after = ob_get_clean();

		// Display barcode
		$this->display_barcode( $order_id, $before, $after );

	} // End get_email_barcode ()

	/**
	 * Get barcode for frontend display
	 * @access  public
	 * @since   1.0.0
	 * @param   object $order Order object
	 * @return  void
	 */
	public function get_display_barcode ( $order ) {

		if( ! $order ) return;

		if ( version_compare( WC_VERSION, '3.0.0', '>=' ) ) {
			$order_id = $order->get_id();
		} else {
			$order_id = $order->id;
		}

		wp_enqueue_style( $this->_token . '-frontend' );

		$before = '<div id="view-order-barcode">';

		$after = '</div>';

		$this->display_barcode( $order_id, $before, $after );
	}

	/**
	 * Add barcode meta box to order edit screen
	 * @access  public
	 * @since   1.0.0
	 * @return  void
	 */
	public function add_order_metabox () {
		global $post;
		$barcode_text = get_post_meta( $post->ID, '_barcode_text', true );
		if( 'yes' == $this->barcode_enable || $barcode_text ) {
			add_meta_box( 'woocommerce-order-barcode', __( 'Order Barcode', 'woocommerce-order-barcodes' ), array( $this, 'get_metabox_barcode' ), 'shop_order', 'side', 'default' );
		}
	}

	/**
	 * Get barcode for display in the order metabox
	 * @access  public
	 * @since   1.0.0
	 * @param   object $order Order post object
	 * @return  void
	 */
	public function get_metabox_barcode ( $order ) {

		if( ! $order ) return;

		$barcode_text = get_post_meta( $order->ID, '_barcode_text', true );

		wp_enqueue_style( $this->_token . '-admin' );

		if( $barcode_text ) {

			$barcode_url = $this->barcode_url( $order->ID );

			$before = '<p><a href="' . $barcode_url . '" target="_blank">';

			$after = '</a></p>';

			$this->display_barcode( $order->ID, $before, $after );

		} else {
			$this->generate_barcode( 'dashboard' );
		}
	}

	/**
	 * Display barcode as an image
	 * @access  public
	 * @since   1.0.0
	 * @param   integer $order_id Order ID
	 * @param   string  $before   Markup/text to display before barcode
	 * @param   string  $after    Markup/text to display after barcode
	 * @param   boolean $echo     Whether to echo out the barcode or just return it
	 * @return  void
	 */
	public function display_barcode ( $order_id = 0, $before = '', $after = '', $echo = true ) {

		if( ! $order_id ) return;

		// Get barcode text
		$barcode_text = get_post_meta( $order_id, '_barcode_text', true );

		if( ! $barcode_text ) return;

		// Get URL for barcode image
		$barcode_url = $this->barcode_url( $order_id );

		// Display barcode with before & after text
		$barcode = $before . '<img src="' . esc_url( $barcode_url ) . '" title="' . __( 'Barcode', 'woocommerce-order-barcodes' ) . '" alt="' . __( 'Barcode', 'woocommerce-order-barcodes' ) . '" style="display:inline;border:0;max-width:100%" /><br/><span style="color:' . $this->barcode_colours['foreground'] . ';font-family:monospace;">' . $barcode_text . '</span>' . $after;

		if( ! $echo ) {
			return $barcode;
		}

		echo $barcode;

	} // End display_barcode ()

	/**
	 * Get the URL for a given order's barcode
	 * @access  public
	 * @since   1.0.0
	 * @param   integer $order_id Order ID
	 * @return  string            URL for barcode
	 */
	public function barcode_url ( $order_id = 0 ) {

		if( ! $order_id ) {
			return;
		}

		$barcode_text = get_post_meta( $order_id, '_barcode_text', true );

		return trailingslashit( get_site_url() ) . '?wc_barcode=' . $barcode_text;

	} // End barcode_url ()

	/**
	 * Get barcode image
	 * @access  public
	 * @since   1.0.0
	 * @return  void
	 */
	public function get_barcode_image() {
		if ( empty( $_GET['wc_barcode'] ) ) {
			return;
		}

		// Get order ID
		if ( is_numeric( $_GET['wc_barcode'] ) ) {
			// This is the deprecated url format which used order id. Capability check is added because it's easy to guess.
			$order_id = intval( $_GET['wc_barcode'] );
			if ( ! ( current_user_can( 'manage_woocommerce' ) || current_user_can( 'view_order', $order_id ) ) ) {
				return;
			}
		} else {
			// New url format uses generated uniq_id which is not easy to guess.
			$order_id = $this->get_barcode_order( $_GET['wc_barcode'] );
			if ( ! $order_id ) {
				// Either wrong order or this may be a Box Office Ticket.
				if ( class_exists( 'WC_Box_Office' ) ) {
					$order_id = WCBO()->components->ticket_barcode->get_ticket_id_from_barcode_text( $_GET['wc_barcode'] );
				}
			}

			if( ! $order_id ) {
				return;
			}
		}

		// Get order barcode
		$barcode_data = get_post_meta( $order_id, '_barcode_image', true );

		// Get image data from barcode string
		list( $settings, $string ) = explode( ',', $barcode_data );
		list( $img_type, $method ) = explode( ';', substr( $settings, 5 ) );

		// Get image extensoin
		$img_ext = str_replace( 'image/', '', $img_type );

		// Decode barcode image
		$barcode = base64_decode( $string );

		// Set headers for image output
		if ( ini_get( 'zlib.output_compression' ) ) {
			ini_set( 'zlib.output_compression', 'Off' );
		}

		header( 'Pragma: public' );
		header( 'Expires: 0' );
		header( 'Cache-Control: must-revalidate, post-check=0, pre-check=0' );
		header( 'Cache-Control: private', false );
		header( 'Content-Transfer-Encoding: binary' );
		header( 'Content-Type: ' . $img_type );

		// Output image
		exit( $barcode );
	}

	/**
	 * Form for scanning barcodes
	 * @param  array  $params Shortcode parameters
	 * @return string         Form markup
	 */
	public function barcode_scan_form ( $params = array() ) {

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
		$this->load_scanner_assets();

		return $html;

	} // End barcode_scan_form ()

	/**
	 * Process scanning/input of barcode
	 * @return void
	 */
	public function scan_barcode () {

		// Security check
		$do_nonce_check = apply_filters( $this->_token . '_do_nonce_check', true );
		if( $do_nonce_check && ! wp_verify_nonce( $_POST[ $this->_token . '_scan_nonce' ], 'scan-barcode' ) ) {
			$this->display_notice( __( 'Permission denied: Security check failed', 'woocommerce-order-barcodes' ), 'error' );
			exit;
		}

		// Retrieve order ID from barcode
		$order_id = $this->get_barcode_order( $_POST['barcode_input'] );
		if( ! $order_id ) {
			$this->display_notice( __( 'Invalid barcode', 'woocommerce-order-barcodes' ), 'error' );
			exit;
		}

		// Check if user has barcode scanning permissions
		$can_scan = apply_filters( $this->_token . '_scan_permission', current_user_can( 'manage_woocommerce' ), $order_id );
		if( ! $can_scan ) {
			$this->display_notice( __( 'Permission denied: You do not have sufficient permissions to scan barcodes', 'woocommerce-order-barcodes' ), 'error' );
			exit;
		}

		// Get order object
		$order = new WC_Order( $order_id );

		if( ! is_a( $order, 'WC_Order' ) || is_wp_error( $order ) ) {
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
				if ( 'yes' === get_post_meta( $order_id, '_checked_in', true ) ) {
					$response      = __( 'Customer already checked in', 'woocommerce-order-barcodes' );
					$response_type = 'notice';
				} else {
					update_post_meta( $order_id, '_checked_in', 'yes' );
					$response = __( 'Customer has checked in', 'woocommerce-order-barcodes' );
				}
			break;

			case 'checkout':
				if ( 'no' === get_post_meta( $order_id, '_checked_in', true ) ) {
					$response      = __( 'Customer already checked out', 'woocommerce-order-barcodes' );
					$response_type = 'notice';
				} else {
					update_post_meta( $order_id, '_checked_in', 'no' );
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

		// Display check-in status if set
		$checked_in = get_post_meta( $order_id, '_checked_in', true );
		if ( $checked_in ) {
			$checkin_status = ( 'yes' === $checked_in ) ? __( 'Checked in', 'woocommerce-order-barcodes' ) : __( 'Checked out', 'woocommerce-order-barcodes' );
			echo '<h3 class="checked_in ' . esc_attr( $checked_in ) . '">' . $checkin_status . '</h3>';
		}

		// Display order details template
		wc_get_template( 'myaccount/view-order.php', array(
	        'status'    => get_term_by( 'slug', $order->status, 'shop_order_status' ),
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
	public function display_notice ( $message = '', $type = 'success' ) {

		if( ! $message ) return;

		// Display notice template
		wc_get_template( "notices/{$type}.php", array(
			'messages' => array( $message ),
		) );

	} // End display_notice ()

	/**
	 * Retrieve order ID from barcode
	 * @param  string  $barcode Scanned barcode
	 * @return integer        	Order ID
	 */
	public function get_barcode_order ( $barcode = '' ) {

		if( ! $barcode ) return 0;

		// Set up query
		$args = array(
			'post_type' => 'shop_order',
			'posts_per_page' => 1,
			'meta_key' => '_barcode_text',
			'meta_value' => $barcode,
		);

	    if( version_compare( WC()->version, 2.2, ">=" ) ) {
	    	$args['post_status'] = array_keys( wc_get_order_statuses() );
	    }

		// Get orders
		$orders = get_posts( $args );

		// Get order ID
		$order_id = 0;
		if( 0 < count( $orders ) ) {
			foreach( $orders as $order ) {
				$order_id = $order->ID;
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
	public function checkin_status_edit_field ( $order ) {
		if ( version_compare( WC_VERSION, '3.0.0', '>=' ) ) {
			$order_id = $order->get_id();
		} else {
			$order_id = $order->id;
		}

		$checked_in = get_post_meta( $order_id, '_checked_in', true );

		if( $checked_in ) {
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
			update_post_meta( $post_id, '_checked_in', $_POST['checkin_status'] );
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
	 * Register all required JS & CSS
	 * @access  public
	 * @since   1.0.0
	 * @return  void
	 */
	public function register_assets () {

		// Barcodes (all types)
		wp_register_script( $this->_token . '-barcode', esc_url( $this->assets_url ) . 'js/jquery.barcode' . $this->script_suffix . '.js', array( 'jquery' ), $this->_version );

		// QR codes
		wp_register_script( $this->_token . '-qrcode-src', esc_url( $this->assets_url ) . 'js/qrcode' . $this->script_suffix . '.js', array(), $this->_version );
		wp_register_script( $this->_token . '-qrcode', esc_url( $this->assets_url ) . 'js/jquery.qrcode' . $this->script_suffix . '.js', array( 'jquery', $this->_token . '-qrcode-src' ), $this->_version );

		// Scanner detection
		wp_register_script( $this->_token . '-scanner-compat', esc_url( $this->assets_url ) . 'js/jquery.scannerdetection.compatibility' . $this->script_suffix . '.js', array( 'jquery' ), $this->_version );
		wp_register_script( $this->_token . '-scanner', esc_url( $this->assets_url ) . 'js/jquery.scannerdetection' . $this->script_suffix . '.js', array( 'jquery', $this->_token . '-scanner-compat' ), $this->_version );

		if( ! is_admin() ) {
			wp_register_script( $this->_token . '-frontend', esc_url( $this->assets_url ) . 'js/frontend' . $this->script_suffix . '.js', array( 'jquery', $this->_token . '-scanner' ), $this->_version );
			wp_register_style( $this->_token . '-frontend', esc_url( $this->assets_url ) . 'css/frontend.css', array(), $this->_version );

			// Pass data to frontend JS
			wp_localize_script( $this->_token . '-frontend', 'wc_order_barcodes', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ), 'scan_nonce' => wp_create_nonce( 'scan-barcode' ) ) );
		} else {
			wp_register_style( $this->_token . '-admin', esc_url( $this->assets_url ) . 'css/admin.css', array(), $this->_version );
		}

	} // End register_assets ()

	/**
	 * Load JS & CSS required for barcode generation
	 * @access  public
	 * @since   1.0.0
	 * @return  void
	 */
	public function load_barcode_assets () {
		wp_enqueue_script( $this->_token . '-barcode' );
		wp_enqueue_script( $this->_token . '-qrcode' );
		if( ! is_admin() ) {
			wp_enqueue_style( $this->_token . '-frontend' );
		}
	} // End load_barcode_assets ()

	/**
	 * Load JS & CSS required for scanner form
	 * @access  public
	 * @since   1.0.0
	 * @return  void
	 */
	public function load_scanner_assets () {
		wp_enqueue_script( $this->_token . '-frontend' );
		wp_enqueue_style( $this->_token . '-frontend' );
	} // End load_scanner_assets ()

	/**
	 * Load plugin localisation
	 * @access  public
	 * @since   1.0.0
	 * @return  void
	 */
	public function load_localisation () {
		load_plugin_textdomain( 'woocommerce-order-barcodes', false, dirname( plugin_basename( $this->file ) ) . '/lang/' );
	} // End load_localisation ()

	/**
	 * Load necessary files
	 */
	public function includes() {
		require_once( dirname( __FILE__ ) . '/class-woocommerce-order-barcodes-privacy.php' );
	}

	/**
	 * Load plugin textdomain
	 * @access  public
	 * @since   1.0.0
	 * @return  void
	 */
	public function load_plugin_textdomain () {
	    $domain = 'woocommerce-order-barcodes';

	    $locale = apply_filters( 'plugin_locale', get_locale(), $domain );

	    load_textdomain( $domain, WP_LANG_DIR . '/' . $domain . '/' . $domain . '-' . $locale . '.mo' );
	    load_plugin_textdomain( $domain, FALSE, dirname( plugin_basename( $this->file ) ) . '/lang/' );
	} // End load_plugin_textdomain ()

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
