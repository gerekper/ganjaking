<?php
/*
 * Plugin Name: WooCommerce Warranty Requests
 * Plugin URI: https://woocommerce.com/products/warranty-requests/
 * Description: Set warranties for your products (free and paid), and allow customers to purchase warranties when buying a product, and to initiate a return request right from their account. Manage RMA numbers, return status, email communications, and track return shipping easily with this extension.
 * Version: 1.9.23
 * Author: WooCommerce
 * Author URI: https://woocommerce.com
 * Text domain: wc_warranty
 * Tested up to: 5.5
 * WC tested up to: 4.5
 * WC requires at least: 3.0
 *
 * Copyright: © 2021 WooCommerce
 * License: GNU General Public License v3.0
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 *
 * Woo: 228315:9b4c41102e6b61ea5f558e16f9b63e25
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'WOOCOMMERCE_WARRANTY_VERSION', '1.9.23' ); // WRCS: DEFINED_VERSION.

// Plugin init hook.
add_action( 'plugins_loaded', 'wc_warranty_init' );

/**
 * Initialize plugin.
 */
function wc_warranty_init() {

	load_plugin_textdomain( 'wc_warranty', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );

	if ( ! class_exists( 'WooCommerce' ) ) {
		add_action( 'admin_notices', 'wc_warranty_woocommerce_deactivated' );
		return;
	}

	$GLOBALS['wc_warranty'] = new WooCommerce_Warranty();
}

/**
 * WooCommerce Deactivated Notice.
 */
function wc_warranty_woocommerce_deactivated() {
	/* translators: %s: WooCommerce link */
	echo '<div class="error"><p>' . sprintf( esc_html__( 'WooCommerce Warranty Requests requires %s to be installed and active.', 'wc_warranty' ), '<a href="https://woocommerce.com/" target="_blank">WooCommerce</a>' ) . '</p></div>';
}

class WooCommerce_Warranty {

	public static $plugin_file;
	public static $base_path;
	public static $includes_path;
	public static $admin;
	public static $default_statuses  = array();
	public static $shipping_tracking = false;
	public static $providers         = array(); // Shipping providers.
	public static $tips              = array();
	public static $db_version        = '20160506';

	/**
	 * Setup the WC_Warranty extension
	 */
	public function __construct() {

		self::$plugin_file      = __FILE__;
		self::$base_path        = plugin_dir_path( __FILE__ );
		self::$includes_path    = trailingslashit( self::$base_path ) . 'includes';
		self::$default_statuses = $this->get_default_statuses();
		self::$providers        = self::get_providers();

		// Form builder tips.
		self::$tips = apply_filters(
			'wc_warranty_form_builder_tips',
			array(
				'name'     => __( 'The name of the field that gets displayed on the Warranty Requests Table (Admin Panel)', 'wc_warranty' ),
				'label'    => __( 'The label of the field displayed to the user when requesting for an RMA (Frontend)', 'wc_warranty' ),
				'default'  => __( 'The initial value of the field', 'wc_warranty' ),
				'required' => __( 'Check this to make this field required', 'wc_warranty' ),
				'multiple' => __( 'Check this to allow users to select one or more options', 'wc_warranty' ),
				'options'  => __( 'One option per line', 'wc_warranty' ),
			)
		);

		$this->include_files();

		if ( ! is_admin() ) {
			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		}

		add_action( 'init', array( $this, 'init' ) );
		add_filter( 'woocommerce_translations_updates_for_woocommerce_warranty', '__return_true' );
	}

	/**
	 * Initialization logic
	 */
	public function init() {
		require_once self::$includes_path . '/class-warranty-privacy.php';
	}

	public function enqueue_scripts() {
		wp_enqueue_style( 'wc_warranty', plugins_url( 'assets/css/front.css', self::$plugin_file ), array(), WOOCOMMERCE_WARRANTY_VERSION );
	}

	/**
	 * Returns the default warranty statuses
	 *
	 * @return array
	 */
	public function get_default_statuses() {
		return apply_filters(
			'wc_warranty_default_statuses',
			array(
				__( 'New', 'wc_warranty' ),
				__( 'Reviewing', 'wc_warranty' ),
				__( 'Processing', 'wc_warranty' ),
				__( 'Completed', 'wc_warranty' ),
				__( 'Rejected', 'wc_warranty' ),
			)
		);
	}

	/**
	 * Get shipping providers
	 *
	 * @return array
	 */
	public static function get_providers() {
		return apply_filters(
			'wc_shipment_tracking_get_providers',
			array(
				'Australia' => array(
					'Australia Post'   => 'http://auspost.com.au/track/track.html?id=%1$s',
					'Fastway Couriers' => 'http://www.fastway.com.au/courier-services/track-your-parcel?l=%1$s',
				),
				'Austria' => array(
					'post.at' => 'http://www.post.at/sendungsverfolgung.php?pnum1=%1$s',
					'dhl.at'  => 'http://www.dhl.at/content/at/de/express/sendungsverfolgung.html?brand=DHL&AWB=%1$s',
					'DPD.at'  => 'https://tracking.dpd.de/parcelstatus?locale=de_AT&query=%1$s',
				),
				'Belgium' => array(
					'bpost' => 'http://track.bpost.be/etr/light/showSearchPage.do?oss_language=EN',
				),
				'Brazil' => array(
					'Correios' => 'http://websro.correios.com.br/sro_bin/txect01$.QueryList?P_LINGUA=001&P_TIPO=001&P_COD_UNI=%1$s',
				),
				'Canada' => array(
					'Canada Post' => 'http://www.canadapost.ca/cpotools/apps/track/personal/findByTrackNumber?trackingNumber=%1$s',
				),
				'Czech Republic' => array(
					'PPL.cz'      => 'http://www.ppl.cz/main2.aspx?cls=Package&idSearch=%1$s',
					'Česká pošta' => 'http://www.ceskaposta.cz/cz/nastroje/sledovani-zasilky.php?barcode=%1$s&locale=CZ&send.x=52&send.y=8&go=ok',
					'DHL.cz'      => 'http://www.dhl.cz/content/cz/cs/express/sledovani_zasilek.shtml?brand=DHL&AWB=%1$s',
					'DPD.cz'      => 'https://tracking.dpd.de/cgi-bin/delistrack?pknr=%1$s&typ=32&lang=cz',
				),
				'Finland' => array(
					'Itella' => 'http://www.posti.fi/itemtracking/posti/search_by_shipment_id?lang=en&ShipmentId=%1$s',
				),
				'France' => array(
					'Colissimo' => 'http://www.colissimo.fr/portail_colissimo/suivre.do?language=fr_FR&colispart=%1$s',
				),
				'Germany' => array(
					'DHL Intraship (DE)' => 'http://nolp.dhl.de/nextt-online-public/set_identcodes.do?lang=de&idc=%1$s&rfn=&extendedSearch=true',
					'Hermes'             => 'https://tracking.hermesworld.com/?TrackID=%1$s',
					'Deutsche Post DHL'  => 'http://nolp.dhl.de/nextt-online-public/set_identcodes.do?lang=de&idc=%1$s',
					'UPS Germany'        => 'http://wwwapps.ups.com/WebTracking/processInputRequest?sort_by=status&tracknums_displayed=1&TypeOfInquiryNumber=T&loc=de_DE&InquiryNumber1=%1$s',
					'DPD.de'             => 'https://tracking.dpd.de/parcelstatus?query=%1$s&locale=en_DE',
				),
				'Ireland' => array(
					'DPD'     => 'http://www2.dpd.ie/Services/QuickTrack/tabid/222/ConsignmentID/%1$s/Default.aspx',
					'An Post' => 'https://track.anpost.ie/TrackingResults.aspx?rtt=1&items=%1$s',
				),
				'Italy' => array(
					'BRT (Bartolini)' => 'http://as777.brt.it/vas/sped_det_show.hsm?referer=sped_numspe_par.htm&Nspediz=%1$s',
					'DHL Express'     => 'http://www.dhl.it/it/express/ricerca.html?AWB=%1$s&brand=DHL',
				),
				'India' => array(
					'DTDC' => 'http://www.dtdc.in/dtdcTrack/Tracking/consignInfo.asp?strCnno=%1$s',
				),
				'Netherlands' => array(
					'PostNL' => 'https://mijnpakket.postnl.nl/Claim?Barcode=%1$s&Postalcode=%2$s&Foreign=False&ShowAnonymousLayover=False&CustomerServiceClaim=False',
					'DPD.NL' => 'http://track.dpdnl.nl/?parcelnumber=%1$s',
				),
				'New Zealand' => array(
					'Courier Post' => 'http://trackandtrace.courierpost.co.nz/Search/%1$s',
					'NZ Post'      => 'http://www.nzpost.co.nz/tools/tracking?trackid=%1$s',
					'Fastways'     => 'http://www.fastway.co.nz/courier-services/track-your-parcel?l=%1$s',
					'PBT Couriers' => 'http://www.pbt.com/nick/results.cfm?ticketNo=%1$s',
				),
				'Poland' => array(
					'InPost'        => 'https://inpost.pl/sledzenie-przesylek?number=%1$s',
					'DPD.PL'        => 'https://tracktrace.dpd.com.pl/parcelDetails?p1=%1$s',
					'Poczta Polska' => 'https://emonitoring.poczta-polska.pl/?numer=%1$s',
				),
				'Romania' => array(
					'Fan Courier'   => 'https://www.fancourier.ro/awb-tracking/?xawb=%1$s',
					'DPD Romania'   => 'https://tracking.dpd.de/parcelstatus?query=%1$s&locale=ro_RO',
					'Urgent Cargus' => 'https://app.urgentcargus.ro/Private/Tracking.aspx?CodBara=%1$s',
				),
				'South African' => array(
					'SAPO' => 'http://sms.postoffice.co.za/TrackingParcels/Parcel.aspx?id=%1$s',
				),
				'Sweden' => array(
					'Posten AB'   => 'http://www.posten.se/sv/Kundservice/Sidor/Sok-brev-paket.aspx?search=%1$s',
					'DHL.se'      => 'http://www.dhl.se/content/se/sv/express/godssoekning.shtml?brand=DHL&AWB=%1$s',
					'Bring.se'    => 'http://tracking.bring.se/tracking.html?q=%1$s',
					'UPS.se'      => 'http://wwwapps.ups.com/WebTracking/track?track=yes&loc=sv_SE&trackNums=%1$s',
					'DB Schenker' => 'http://privpakportal.schenker.nu/TrackAndTrace/packagesearch.aspx?packageId=%1$s',
				),
				'United Kingdom' => array(
					'InterLink'                 => 'http://www.interlinkexpress.com/apps/tracking/?reference=%1$s&postcode=%2$s#results',
					'DHL'                       => 'http://www.dhl.com/content/g0/en/express/tracking.shtml?brand=DHL&AWB=%1$s',
					'DPD'                       => 'http://www.dpd.co.uk/tracking/trackingSearch.do?search.searchType=0&search.parcelNumber=%1$s',
					'ParcelForce'               => 'http://www.parcelforce.com/portal/pw/track?trackNumber=%1$s',
					'Royal Mail'                => 'https://www.royalmail.com/track-your-item/?trackNumber=%1$s',
					'TNT Express (consignment)' => 'http://www.tnt.com/webtracker/tracking.do?requestType=GEN&searchType=CON&respLang=en&
	respCountry=GENERIC&sourceID=1&sourceCountry=ww&cons=%1$s&navigation=1&g
	enericSiteIdent=',
					'TNT Express (reference)'   => 'http://www.tnt.com/webtracker/tracking.do?requestType=GEN&searchType=REF&respLang=en&r
	espCountry=GENERIC&sourceID=1&sourceCountry=ww&cons=%1$s&navigation=1&gen
	ericSiteIdent=',
					'UK Mail'                   => 'https://old.ukmail.com/ConsignmentStatus/ConsignmentSearchResults.aspx?SearchType=Reference&SearchString=%1$s',
				),
				'United States' => array(
					'Fedex'         => 'http://www.fedex.com/Tracking?action=track&tracknumbers=%1$s',
					'FedEx Sameday' => 'https://www.fedexsameday.com/fdx_dotracking_ua.aspx?tracknum=%1$s',
					'OnTrac'        => 'http://www.ontrac.com/trackingdetail.asp?tracking=%1$s',
					'UPS'           => 'http://wwwapps.ups.com/WebTracking/track?track=yes&trackNums=%1$s',
					'USPS'          => 'https://tools.usps.com/go/TrackConfirmAction_input?qtc_tLabels1=%1$s',
					'DHL US'        => 'https://beta.dhl.com/us-en/home/tracking/tracking-ecommerce.html?tracking-id=%1$s',
				),
			)
		);
	}

	/**
	 * Include core files
	 */
	public function include_files() {
		require_once self::$includes_path . '/class.warranty_compat.php';
		require_once self::$includes_path . '/functions.php';
		require_once self::$includes_path . '/class-warranty-install.php';
		require_once self::$includes_path . '/class-warranty-shortcodes.php';
		require_once self::$includes_path . '/class-warranty-query.php';
		require_once self::$includes_path . '/class-warranty-order.php';
		require_once self::$includes_path . '/class-warranty-item.php';

		if ( is_admin() ) {
			require_once self::$includes_path . '/class-warranty-coupons.php';
			require_once self::$includes_path . '/class-warranty-settings.php';
			self::$admin = include self::$includes_path . '/class-warranty-admin.php';
		} else {
			include_once self::$includes_path . '/class-warranty-frontend.php';
			include_once self::$includes_path . '/class-warranty-cart.php';
		}

		if ( defined( 'DOING_AJAX' ) ) {
			require_once self::$includes_path . '/class-warranty-ajax.php';
		}

	}

	/**
	 * Helper method to properly format a warranty string (e.g. 5 months)
	 *
	 * @param int $duration
	 * @param string $unit
	 * @return string Formatted warranty string
	 */
	public function get_warranty_string( $duration, $unit = 'days' ) {
		$units_i18n = array(
			'day'    => __( 'Day', 'wc_warranty' ),
			'days'   => __( 'Days', 'wc_warranty' ),
			'week'   => __( 'Week', 'wc_warranty' ),
			'weeks'  => __( 'Weeks', 'wc_warranty' ),
			'month'  => __( 'Month', 'wc_warranty' ),
			'months' => __( 'Months', 'wc_warranty' ),
			'year'   => __( 'Year', 'wc_warranty' ),
			'years'  => __( 'Years', 'wc_warranty' ),
		);

		if ( isset( $units_i18n[ $unit ] ) ) {
			$unit = $units_i18n[ $unit ];
		}

		return $duration . ' ' . $unit;
	}

	public static function clear_all_product_warranties() {
		global $wpdb;

		$product_ids = $wpdb->get_col(
			"SELECT post_id
			FROM {$wpdb->postmeta}
			WHERE meta_key = '_warranty'"
		);

		foreach ( $product_ids as $product_id ) {
			delete_post_meta( $product_id, '_warranty' );
			delete_post_meta( $product_id, '_warranty_type' );
			delete_post_meta( $product_id, '_warranty_unit' );
			delete_post_meta( $product_id, '_warranty_duration' );
			delete_post_meta( $product_id, '_warranty_label' );
		}
	}

	public static function render_warranty_form( $extra_key = '' ) {
		$defaults = array(
			'fields' => array(),
			'inputs' => ''
		);
		$form     = get_option( 'warranty_form', $defaults );
		$inputs   = array();

		if (! empty( $form['inputs'] ) ) {
			$inputs = json_decode( $form['inputs'] );
		}

		foreach ( $inputs as $input ) {
			$key  = $input->key;
			$type = $input->type;

			self::render_warranty_form_field( $type, $key, $form['fields'][ $key ], $extra_key );
		}
	}

	public static function render_warranty_form_field( $type, $key, $field, $extra_key = '' ) {
		echo '<div class="wfb-field-div wfb-field-div-' . $type . '" id="wfb-field-' . $key . $extra_key . '-div">';

		$required      = '';
		$required_note = '';
		if ( ! empty( $field['required'] ) && 'yes' === $field['required'] ) {
			$required      = 'data-required="true" required';
			$required_note = ' <span class="required">*</span>';
		}

		$name  = 'wfb-field[' . $key . ']';
		$value = isset( $field['default'] ) ? $field['default'] : '';

		if ( $extra_key ) {
			$name = 'wfb-field[' . $key . '][' . $extra_key . ']';

			if ( ! empty( $_REQUEST['wfb-field'][ $key ][ $extra_key ] ) ) {
				$value = $_REQUEST['wfb-field'][ $key ][ $extra_key ];
			}
		}

		switch ( $type ) {
			case 'paragraph':
				echo '<p class="wfb-field-para" id="wfb-field-' . $key . $extra_key . '">' . $field['text'] . '</p>';
				break;

			case 'text':
				echo '<label
					for="wfb-field-' . $key . $extra_key . '"
					id="wfb-field-label-' . $key . $extra_key . '"
					>
					' . $field['label'] . $required_note . '
					</label>';
				echo '<input
					type="text"
					name="' . $name . '"
					id="wfb-field-' . $key . $extra_key . '"
					value="' . esc_attr( $value ) . '"
					' . $required . '
					class="wfb-field"
					/>';
				break;

			case 'textarea':
				echo '<label
					for="wfb-field-' . $key . '"
					id="wfb-field-label-' . $key . $extra_key . '"
					>
					' . $field['label'] . $required_note . '
					</label>';
				echo '<textarea
					name="' . $name . '"
					id="wfb-field-' . $key . $extra_key . '"
					rows="' . $field['rows'] . '"
					cols="' . $field['cols'] . '"
					' . $required . '
					class="wfb-field">' . $value . '</textarea>';
				break;

			case 'select':
				$select_name = ( isset( $field['multiple'] ) && 'yes' === $field['multiple'] )
					? $name . '[]'
					: $name;
				$multiple    = ( isset( $field['multiple'] ) && 'yes' === $field['multiple'] ) ? 'multiple' : '';
				$options     = preg_split( "/(\r\n|\n|\r)/", $field['options'] );
				echo '<label
					for="wfb-field-' . $key . '"
					id="wfb-field-label-' . $key . '">
					' . $field['label'] . $required_note . '
					</label>';
				echo '<select
					name="' . $select_name . '"
					id="wfb-field-' . $key . '"
					' . $multiple . '
					' . $required . '
					class="wfb-field"
					>';

				foreach ( $options as $option ) {
					echo '<option value="' . $option . '">' . $option . '</option>';
				}

				echo '</select>';
				break;

			case 'file':
				echo '<label
					for="wfb-field-' . $key . $extra_key . '"
					id="wfb-field-label-' . $key . $extra_key . '"
					>
					' . $field['label'] . $required_note . '
					</label>';
				echo '<input
					type="file"
					name="' . $name . '"
					id="wfb-field-' . $key . $extra_key . '"
					' . $required . '
					class="wfb-field"
					/>';
				break;
		}

		echo '</div>';
	}

	public static function render_old_warranty_form() {
		$reasons      = get_option( 'warranty_reason', '' );
		$reason_array = preg_split( "/(\r\n|\n|\r)/", $reasons );

		if ( ! empty( $reasons ) && ! empty( $reason_array ) ):
		?>
			<p><?php _e( 'Select reason to request for warranty', 'wc_warranty' ); ?><br/>
				<select name="warranty_reason">
					<?php
					foreach ( $reason_array as $reason ) {
						if ( empty( $reason ) ) continue;
						echo '<option value="' . trim( $reason ) . '">' . trim( $reason ) . '</option>';
					}
					?>
				</select>
			</p>
		<?php
		else:
			echo '<input type="hidden" name="warranty_reason" value="" />';
		endif;

		$question = get_option( 'warranty_question', '' );
		$required = get_option( 'warranty_require_question', 'no' );

		if ( $question ) :
			?>
			<p><?php echo $question; ?> <?php if ( 'yes' === $required ) echo '<b>(*)</b>'; ?><br/>
				<textarea style="width:250px;" rows="4" name="warranty_answer" id="warranty_answer"></textarea>
			</p>
		<?php endif; ?>

		<?php
		$upload = get_option( 'warranty_upload', 'no' );

		if ( 'yes' === $upload ):
			$title    = get_option( 'warranty_upload_title', 'Upload Attachment' );
			$required = get_option( 'warranty_require_upload', 'no' );
			?>
			<p>
				<?php echo $title; ?> <?php if ( 'yes' === $required ) echo '<b>(*)</b>'; ?><br/>
				<input type="file" name="warranty_upload" />
			</p>
		<?php endif;
	}

	/**
	 * Process and save data from fields generated by the form builder
	 *
	 * @param int $request_id
	 * @return bool|WP_Error
	 */
	public static function process_warranty_form( $request_id ) {
		$defaults = array(
			'fields' => array(),
			'inputs' => ''
		);
		$form     = get_option( 'warranty_form', $defaults );
		$inputs   = array();
		$errors   = array();
		$data     = array();

		if ( ! empty( $form['inputs'] ) ) {
			$inputs = json_decode( $form['inputs'] );
		}

		// Enable restriction of MIME types - start.
		add_filter( 'upload_mimes', array( 'WooCommerce_Warranty', 'restrict_allowed_mime_types' ) );

		foreach ( $inputs as $input ) {
			$key    = $input->key;
			$type   = $input->type;
			$field  = $form['fields'][$key];
			$posted = isset( $_POST['wfb-field'] ) ? $_POST['wfb-field'] : array();

			if ( 'paragraph' === $type ) {
				continue;
			}

			if ( 'file' === $type ) {
				$required = isset( $field['required'] ) && 'yes' === $field['required'];
				$files    = isset( $_FILES['wfb-field'] ) ? $_FILES['wfb-field'] : false;

				if ( $required && ! is_uploaded_file( $files['tmp_name'][ $key ] ) ) {
					$errors[] = sprintf( __( 'The field "%s" is required', 'wc_warranty' ), $field['label'] );
					continue;
				}

				if ( is_uploaded_file( $files['tmp_name'][ $key ] ) ) {
					$filename      = sanitize_file_name( $files['name'][ $key ] );
					$validate_file = wp_check_filetype_and_ext( $files['tmp_name'][ $key ], $filename );

					// Check the file type, before we do anything further.
					if ( '' == $validate_file['ext'] || '' == $validate_file['type'] ) {
						$errors[] = __( 'The file you selected is not permitted. Please select another.', 'wc_warranty' );
						continue;
					}

					$upload_dir = wp_upload_dir();
					$filename   = wp_unique_filename( $upload_dir['path'] . '/', $filename );
					$new_path   = $upload_dir['path'] . '/' . $filename;

					if ( move_uploaded_file( $files['tmp_name'][ $key ], $new_path ) ) {
						$filename     = wp_unique_filename( $upload_dir['subdir'] . '/', $filename );
						$data[ $key ] = $upload_dir['subdir'] . '/' .  $filename;
					}
				}

			} else {
				$value    = isset( $posted[ $key ] ) ? $posted[ $key ] : false;
				$required = isset( $field['required'] ) && 'yes' === $field['required'];

				if ( $required && ! $value ) {
					$errors[] = sprintf( __( 'The field "%s" is required', 'wc_warranty' ), $field['label'] );
				} elseif ( $value ) {
					$data[ $key ] = $value;
				}
			}
		}

		// Enable restriction of MIME types - start.
		remove_filter( 'upload_mimes', array( 'WooCommerce_Warranty', 'restrict_allowed_mime_types' ) );

		if ( ! empty( $errors ) ) {
			$wp_error = new WP_Error();
			foreach ( $errors as $idx => $error ) {
				$wp_error->add( 'wfb-error-' . $idx, $error );
			}

			return $wp_error;
		} else {
			// Store data.
			if ( ! empty( $data ) ) {
				foreach ( $data as $key => $value ) {
					update_post_meta( $request_id, '_field_' . $key, $value );
				}
			}
			return true;
		}
	}

	/**
	 * Make sure we restrict to a specific subset of MIME types, before doing the validation check.
	 *
	 * @param array $mimes list of supported mimes, by default.
	 * @return array restricted list of mimes.
	 */
	public static function restrict_allowed_mime_types( $mimes ) {
		$mimes = array (
			'jpg|jpeg|jpe'    => 'image/jpeg',
			'png'             => 'image/png',
			'gif'             => 'image/gif',
			'pdf'             => 'application/pdf',
			'doc'             => 'application/msword',
			'docx'            => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
			'docm'            => 'application/vnd.ms-word.document.macroEnabled.12',
			'pot|pps|ppt'     => 'application/vnd.ms-powerpoint',
			'pptx'            => 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
			'pptm'            => 'application/vnd.ms-powerpoint.presentation.macroEnabled.12',
			'odt'             => 'application/vnd.oasis.opendocument.text',
			'ppsx'            => 'application/vnd.openxmlformats-officedocument.presentationml.slideshow',
			'ppsm'            => 'application/vnd.ms-powerpoint.slideshow.macroEnabled.12',
			'xla|xls|xlt|xlw' => 'application/vnd.ms-excel',
			'xlsx'            => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
			'xlsm'            => 'application/vnd.ms-excel.sheet.macroEnabled.12',
			'xlsb'            => 'application/vnd.ms-excel.sheet.binary.macroEnabled.12',
			'key'             => 'application/vnd.apple.keynote',
		);
		return $mimes;
	}

}
