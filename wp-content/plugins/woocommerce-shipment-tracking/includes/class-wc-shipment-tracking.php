<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * WooCommerce Shipment Tracking Actions
 *
 * @since 1.4.0
 */
class WC_Shipment_Tracking_Actions {

	/**
	 * Instance of this class.
	 *
	 * @var object Class Instance
	 */
	private static $instance;

	/**
	 * Get the class instance
	 *
	 * @return WC_Shipment_Tracking_Actions
	 */
	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	/**
	 * Get shiping providers.
	 *
	 * @return array
	 */
	public function get_providers() {
		return apply_filters( 'wc_shipment_tracking_get_providers', array(
			'Australia' => array(
				'Australia Post'   => 'http://auspost.com.au/track/track.html?id=%1$s',
				'Fastway Couriers' => 'http://www.fastway.com.au/courier-services/track-your-parcel?l=%1$s',
			),
			'Austria' => array(
				'post.at' => 'https://www.post.at/sv/sendungsdetails?snr=%1$s',
				'dhl.at'  => 'http://www.dhl.at/content/at/de/express/sendungsverfolgung.html?brand=DHL&AWB=%1$s',
				'DPD.at'  => 'https://tracking.dpd.de/parcelstatus?locale=de_AT&query=%1$s',
			),
			'Brazil' => array(
				'Correios' => 'http://websro.correios.com.br/sro_bin/txect01$.QueryList?P_LINGUA=001&P_TIPO=001&P_COD_UNI=%1$s',
			),
			'Belgium' => array(
				'bpost' => 'https://track.bpost.be/btr/web/#/search?itemCode=%1$s',
			),
			'Canada' => array(
				'Canada Post' => 'http://www.canadapost.ca/cpotools/apps/track/personal/findByTrackNumber?trackingNumber=%1$s',
			),
			'Czech Republic' => array(
				'PPL.cz'      => 'http://www.ppl.cz/main2.aspx?cls=Package&idSearch=%1$s',
				'Česká pošta' => 'https://www.postaonline.cz/trackandtrace/-/zasilka/cislo?parcelNumbers=%1$s',
				'DHL.cz'      => 'http://www.dhl.cz/cs/express/sledovani_zasilek.html?AWB=%1$s',
				'DPD.cz'      => 'https://tracking.dpd.de/parcelstatus?locale=cs_CZ&query=%1$s',
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
				'DPD.ie'  => 'http://www2.dpd.ie/Services/QuickTrack/tabid/222/ConsignmentID/%1$s/Default.aspx',
				'An Post' => 'https://track.anpost.ie/TrackingResults.aspx?rtt=1&items=%1$s',
			),
			'Italy' => array(
				'BRT (Bartolini)' => 'http://as777.brt.it/vas/sped_det_show.hsm?referer=sped_numspe_par.htm&Nspediz=%1$s',
				'DHL Express'     => 'http://www.dhl.it/it/express/ricerca.html?AWB=%1$s&brand=DHL',
			),
			'India' => array(
				'DTDC' => 'http://www.dtdc.in/tracking/tracking_results.asp?Ttype=awb_no&strCnno=%1$s&TrkType2=awb_no',
			),
			'Netherlands' => array(
				'PostNL' => 'https://postnl.nl/tracktrace/?B=%1$s&P=%2$s&D=%3$s&T=C',
				'DPD.NL' => 'http://track.dpdnl.nl/?parcelnumber=%1$s',
				'UPS Netherlands'        => 'http://wwwapps.ups.com/WebTracking/processInputRequest?sort_by=status&tracknums_displayed=1&TypeOfInquiryNumber=T&loc=nl_NL&InquiryNumber1=%1$s',
			),
			'New Zealand' => array(
				'Courier Post' => 'http://trackandtrace.courierpost.co.nz/Search/%1$s',
				'NZ Post'      => 'http://www.nzpost.co.nz/tools/tracking?trackid=%1$s',
				'Fastways'     => 'http://www.fastway.co.nz/courier-services/track-your-parcel?l=%1$s',
				'PBT Couriers' => 'http://www.pbt.com/nick/results.cfm?ticketNo=%1$s',
			),
			'Poland' => array(
				'InPost' => 'https://inpost.pl/sledzenie-przesylek?number=%1$s',
				'DPD.PL' => 'https://tracktrace.dpd.com.pl/parcelDetails?p1=%1$s',
				'Poczta Polska' => 'https://emonitoring.poczta-polska.pl/?numer=%1$s',
			),
			'Romania' => array(
				'Fan Courier'      => 'https://www.fancourier.ro/awb-tracking/?xawb=%1$s',
				'DPD Romania'     => 'https://tracking.dpd.de/parcelstatus?query=%1$s&locale=ro_RO',
				'Urgent Cargus' => 'https://app.urgentcargus.ro/Private/Tracking.aspx?CodBara=%1$s',
			),
			'South African' => array(
				'SAPO' => 'http://sms.postoffice.co.za/TrackingParcels/Parcel.aspx?id=%1$s',
				'Fastway' => 'http://www.fastway.co.za/our-services/track-your-parcel?l=%1$s',
			),
			'Sweden' => array(
				'PostNord Sverige AB' => 'http://www.postnord.se/sv/verktyg/sok/Sidor/spara-brev-paket-och-pall.aspx?search=%1$s',
				'DHL.se'              => 'http://www.dhl.se/content/se/sv/express/godssoekning.shtml?brand=DHL&AWB=%1$s',
				'Bring.se'            => 'http://tracking.bring.se/tracking.html?q=%1$s',
				'UPS.se'              => 'http://wwwapps.ups.com/WebTracking/track?track=yes&loc=sv_SE&trackNums=%1$s',
				'DB Schenker'         => 'http://privpakportal.schenker.nu/TrackAndTrace/packagesearch.aspx?packageId=%1$s',
			),
			'United Kingdom' => array(
				'DHL'                       => 'http://www.dhl.com/content/g0/en/express/tracking.shtml?brand=DHL&AWB=%1$s',
				'DPD.co.uk'                 => 'http://www.dpd.co.uk/tracking/trackingSearch.do?search.searchType=0&search.parcelNumber=%1$s',
				'InterLink'                 => 'http://www.interlinkexpress.com/apps/tracking/?reference=%1$s&postcode=%2$s#results',
				'ParcelForce'               => 'http://www.parcelforce.com/portal/pw/track?trackNumber=%1$s',
				'Royal Mail'                => 'https://www.royalmail.com/track-your-item/?trackNumber=%1$s',
				'TNT Express (consignment)' => 'http://www.tnt.com/webtracker/tracking.do?requestType=GEN&searchType=CON&respLang=en&respCountry=GENERIC&sourceID=1&sourceCountry=ww&cons=%1$s&navigation=1&g
enericSiteIdent=',
				'TNT Express (reference)'   => 'http://www.tnt.com/webtracker/tracking.do?requestType=GEN&searchType=REF&respLang=en&respCountry=GENERIC&sourceID=1&sourceCountry=ww&cons=%1$s&navigation=1&genericSiteIdent=',
				'DHL Parcel UK'             => 'https://track.dhlparcel.co.uk/?con=%1$s',
			),
			'United States' => array(
				'Fedex'         => 'http://www.fedex.com/Tracking?action=track&tracknumbers=%1$s',
				'FedEx Sameday' => 'https://www.fedexsameday.com/fdx_dotracking_ua.aspx?tracknum=%1$s',
				'OnTrac'        => 'http://www.ontrac.com/trackingdetail.asp?tracking=%1$s',
				'UPS'           => 'http://wwwapps.ups.com/WebTracking/track?track=yes&trackNums=%1$s',
				'USPS'          => 'https://tools.usps.com/go/TrackConfirmAction_input?qtc_tLabels1=%1$s',
				'DHL US'        => 'https://www.logistics.dhl/us-en/home/tracking/tracking-ecommerce.html?tracking-id=%1$s',
			),
		) );
	}

	/**
	 * Localisation.
	 */
	public function load_plugin_textdomain() {
		$plugin_file = wc_shipment_tracking()->plugin_file;
		load_plugin_textdomain( 'woocommerce-shipment-tracking', false, dirname( plugin_basename( $plugin_file ) ) . '/languages/' );
	}

	/**
	 * Load admin styles.
	 */
	public function admin_styles() {
		$plugin_url  = wc_shipment_tracking()->plugin_url;
		wp_enqueue_style( 'shipment_tracking_styles', $plugin_url . '/assets/css/admin.css' );
	}

	/**
	 * Define shipment tracking column in admin orders list.
	 *
	 * @since 1.6.1
	 *
	 * @param array $columns Existing columns
	 *
	 * @return array Altered columns
	 */
	public function shop_order_columns( $columns ) {
		$columns['shipment_tracking'] = __( 'Shipment Tracking', 'woocommerce-shipment-tracking' );
		return $columns;
	}

	/**
	 * Render shipment tracking in custom column.
	 *
	 * @since 1.6.1
	 *
	 * @param string $column Current column
	 */
	public function render_shop_order_columns( $column ) {
		global $post;

		if ( 'shipment_tracking' === $column ) {
			echo $this->get_shipment_tracking_column( $post->ID );
		}
	}

	/**
	 * Get content for shipment tracking column.
	 *
	 * @since 1.6.1
	 *
	 * @param int $order_id Order ID
	 *
	 * @return string Column content to render
	 */
	public function get_shipment_tracking_column( $order_id ) {
		ob_start();

		$tracking_items = $this->get_tracking_items( $order_id );

		if ( count( $tracking_items ) > 0 ) {
			echo '<ul>';

			foreach ( $tracking_items as $tracking_item ) {
				$formatted = $this->get_formatted_tracking_item( $order_id, $tracking_item );
				printf(
					'<li><a href="%s" target="_blank">%s</a></li>',
					esc_url( $formatted['formatted_tracking_link'] ),
					esc_html( $tracking_item['tracking_number'] )
				);
			}
			echo '</ul>';
		} else {
			echo '–';
		}

		return apply_filters( 'woocommerce_shipment_tracking_get_shipment_tracking_column', ob_get_clean(), $order_id, $tracking_items );
	}

	/**
	 * Add the meta box for shipment info on the order page
	 */
	public function add_meta_box() {
		add_meta_box( 'woocommerce-shipment-tracking', __( 'Shipment Tracking', 'woocommerce-shipment-tracking' ), array( $this, 'meta_box' ), 'shop_order', 'side', 'high' );
	}

	/**
	 * Returns a HTML node for a tracking item for the admin meta box
	 */
	public function display_html_tracking_item_for_meta_box( $order_id, $item ) {
			$formatted = $this->get_formatted_tracking_item( $order_id, $item );
			?>
			<div class="tracking-item" id="tracking-item-<?php echo esc_attr( $item['tracking_id'] ); ?>">
				<p class="tracking-content">
					<strong><?php echo esc_html( $formatted['formatted_tracking_provider'] ); ?></strong>
					<?php if ( strlen( $formatted['formatted_tracking_link'] ) > 0 ) : ?>
						- <?php echo sprintf( '<a href="%s" target="_blank" title="' . esc_attr( __( 'Click here to track your shipment', 'woocommerce-shipment-tracking' ) ) . '">' . __( 'Track', 'woocommerce-shipment-tracking' ) . '</a>', esc_url( $formatted['formatted_tracking_link'] ) ); ?>
					<?php endif; ?>
					<br/>
					<em><?php echo esc_html( $item['tracking_number'] ); ?></em>
				</p>
				<p class="meta">
					<?php /* translators: 1: shipping date */ ?>
					<?php echo esc_html( sprintf( __( 'Shipped on %s', 'woocommerce-shipment-tracking' ), date_i18n( 'Y-m-d', $item['date_shipped'] ) ) ); ?>
					<a href="#" class="delete-tracking" rel="<?php echo esc_attr( $item['tracking_id'] ); ?>"><?php _e( 'Delete', 'woocommerce-shipment-tracking' ); ?></a>
				</p>
			</div>
			<?php
	}

	/**
	 * Show the meta box for shipment info on the order page
	 */
	public function meta_box() {
		global $post;

		$tracking_items = $this->get_tracking_items( $post->ID );

		echo '<div id="tracking-items">';

		if ( count( $tracking_items ) > 0 ) {
			foreach ( $tracking_items as $tracking_item ) {
				$this->display_html_tracking_item_for_meta_box( $post->ID, $tracking_item );
			}
		}

		echo '</div>';

		echo '<button class="button button-show-form" type="button">' . __( 'Add Tracking Number', 'woocommerce-shipment-tracking' ) . '</button>';

		echo '<div id="shipment-tracking-form">';
		// Providers
		echo '<p class="form-field tracking_provider_field"><label for="tracking_provider">' . __( 'Provider:', 'woocommerce-shipment-tracking' ) . '</label><br/><select id="tracking_provider" name="tracking_provider" class="chosen_select" style="width:100%;">';

		echo '<option value="">' . __( 'Custom Provider', 'woocommerce-shipment-tracking' ) . '</option>';

		$selected_provider = '';

		if ( ! $selected_provider ) {
			$selected_provider = wc_clean( apply_filters( 'woocommerce_shipment_tracking_default_provider', '' ) );
		}

		foreach ( $this->get_providers() as $provider_group => $providers ) {
			echo '<optgroup label="' . esc_attr( $provider_group ) . '">';
			foreach ( $providers as $provider => $url ) {
				echo '<option value="' . esc_attr( wc_clean( $provider ) ) . '" ' . selected( wc_clean( $provider ), $selected_provider, true ) . '>' . esc_html( $provider ) . '</option>';
			}
			echo '</optgroup>';
		}

		echo '</select> ';

		woocommerce_wp_hidden_input( array(
			'id'    => 'wc_shipment_tracking_get_nonce',
			'value' => wp_create_nonce( 'get-tracking-item' ),
		) );

		woocommerce_wp_hidden_input( array(
			'id'    => 'wc_shipment_tracking_delete_nonce',
			'value' => wp_create_nonce( 'delete-tracking-item' ),
		) );

		woocommerce_wp_hidden_input( array(
			'id'    => 'wc_shipment_tracking_create_nonce',
			'value' => wp_create_nonce( 'create-tracking-item' ),
		) );

		woocommerce_wp_text_input( array(
			'id'          => 'custom_tracking_provider',
			'label'       => __( 'Provider Name:', 'woocommerce-shipment-tracking' ),
			'placeholder' => '',
			'description' => '',
			'value'       => '',
		) );

		woocommerce_wp_text_input( array(
			'id'          => 'tracking_number',
			'label'       => __( 'Tracking number:', 'woocommerce-shipment-tracking' ),
			'placeholder' => '',
			'description' => '',
			'value'       => '',
		) );

		woocommerce_wp_text_input( array(
			'id'          => 'custom_tracking_link',
			'label'       => __( 'Tracking link:', 'woocommerce-shipment-tracking' ),
			'placeholder' => 'http://',
			'description' => '',
			'value'       => '',
		) );

		woocommerce_wp_text_input( array(
			'id'          => 'date_shipped',
			'label'       => __( 'Date shipped:', 'woocommerce-shipment-tracking' ),
			'placeholder' => date_i18n( __( 'Y-m-d', 'woocommerce-shipment-tracking' ), time() ),
			'description' => '',
			'class'       => 'date-picker-field',
			'value'       => date_i18n( __( 'Y-m-d', 'woocommerce-shipment-tracking' ), current_time( 'timestamp' ) ),
		) );

		echo '<button class="button button-primary button-save-form">' . __( 'Save Tracking', 'woocommerce-shipment-tracking' ) . '</button>';

		// Live preview
		echo '<p class="preview_tracking_link">' . __( 'Preview:', 'woocommerce-shipment-tracking' ) . ' <a href="" target="_blank">' . __( 'Click here to track your shipment', 'woocommerce-shipment-tracking' ) . '</a></p>';

		echo '</div>';

		$provider_array = array();

		foreach ( $this->get_providers() as $providers ) {
			foreach ( $providers as $provider => $format ) {
				$provider_array[ wc_clean( $provider ) ] = urlencode( $format );
			}
		}

		$js = "
			jQuery( 'p.custom_tracking_link_field, p.custom_tracking_provider_field ').hide();

			jQuery( 'input#custom_tracking_link, input#tracking_number, #tracking_provider' ).change( function() {

				var tracking  = jQuery( 'input#tracking_number' ).val();
				var provider  = jQuery( '#tracking_provider' ).val();
				var providers = JSON.parse( decodeURIComponent( '" . rawurlencode( wp_json_encode( $provider_array ) ) . "' ) );

				var postcode = jQuery( '#_shipping_postcode' ).val();

				if ( ! postcode.length ) {
					postcode = jQuery( '#_billing_postcode' ).val();
				}

				postcode = encodeURIComponent( postcode );

				let country = jQuery( '#_shipping_country' ).val();
				country = encodeURIComponent(country);

				var link = '';

				if ( providers[ provider ] ) {
					link = providers[provider];
					link = link.replace( '%251%24s', tracking );
					link = link.replace( '%252%24s', postcode );
					link = link.replace( '%253%24s', country );
					link = decodeURIComponent( link );

					jQuery( 'p.custom_tracking_link_field, p.custom_tracking_provider_field' ).hide();
				} else {
					jQuery( 'p.custom_tracking_link_field, p.custom_tracking_provider_field' ).show();

					link = jQuery( 'input#custom_tracking_link' ).val();
				}

				if ( link ) {
					jQuery( 'p.preview_tracking_link a' ).attr( 'href', link );
					jQuery( 'p.preview_tracking_link' ).show();
				} else {
					jQuery( 'p.preview_tracking_link' ).hide();
				}

			} ).change();";

		if ( function_exists( 'wc_enqueue_js' ) ) {
			wc_enqueue_js( $js );
		} else {
			WC()->add_inline_js( $js );
		}

		$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

		wp_enqueue_script( 'wc-shipment-tracking-js', wc_shipment_tracking()->plugin_url . '/assets/js/admin' . $suffix . '.js' );

	}

	/**
	 * Order Tracking Save
	 *
	 * Function for saving tracking items
	 */
	public function save_meta_box( $post_id, $post ) {
		if ( isset( $_POST['tracking_number'] ) && strlen( $_POST['tracking_number'] ) > 0 ) {
			$args = array(
				'tracking_provider'        => wc_clean( $_POST['tracking_provider'] ),
				'custom_tracking_provider' => wc_clean( $_POST['custom_tracking_provider'] ),
				'custom_tracking_link'     => wc_clean( $_POST['custom_tracking_link'] ),
				'tracking_number'          => wc_clean( $_POST['tracking_number'] ),
				'date_shipped'             => wc_clean( $_POST['date_shipped'] ),
			);

			$this->add_tracking_item( $post_id, $args );
		}
	}

	/**
	 * Order Tracking Get All Order Items AJAX
	 *
	 * Function for getting all tracking items associated with the order
	 */
	public function get_meta_box_items_ajax() {
		check_ajax_referer( 'get-tracking-item', 'security', true );

		$order_id = wc_clean( $_POST['order_id'] );
		$tracking_items = $this->get_tracking_items( $order_id );

		foreach ( $tracking_items as $tracking_item ) {
			$this->display_html_tracking_item_for_meta_box( $order_id, $tracking_item );
		}

		die();
	}

	/**
	 * Order Tracking Save AJAX
	 *
	 * Function for saving tracking items via AJAX
	 */
	public function save_meta_box_ajax() {
		check_ajax_referer( 'create-tracking-item', 'security', true );

		if ( isset( $_POST['tracking_number'] ) && strlen( $_POST['tracking_number'] ) > 0 ) {

			$order_id = wc_clean( $_POST['order_id'] );
			$args = array(
				'tracking_provider'        => wc_clean( $_POST['tracking_provider'] ),
				'custom_tracking_provider' => wc_clean( $_POST['custom_tracking_provider'] ),
				'custom_tracking_link'     => wc_clean( $_POST['custom_tracking_link'] ),
				'tracking_number'          => wc_clean( $_POST['tracking_number'] ),
				'date_shipped'             => wc_clean( $_POST['date_shipped'] ),
			);

			$tracking_item = $this->add_tracking_item( $order_id, $args );

			$this->display_html_tracking_item_for_meta_box( $order_id, $tracking_item );
		}

		die();
	}

	/**
	 * Order Tracking Delete
	 *
	 * Function to delete a tracking item
	 */
	public function meta_box_delete_tracking() {
		check_ajax_referer( 'delete-tracking-item', 'security', true );

		$order_id    = wc_clean( $_POST['order_id'] );
		$tracking_id = wc_clean( $_POST['tracking_id'] );

		$this->delete_tracking_item( $order_id, $tracking_id );
	}

	/**
	 * Display Shipment info in the frontend (order view/tracking page).
	 */
	public function display_tracking_info( $order_id ) {
		wc_get_template( 'myaccount/view-order.php', array( 'tracking_items' => $this->get_tracking_items( $order_id, true ) ), 'woocommerce-shipment-tracking/', $this->get_plugin_path() . '/templates/' );
	}

	/**
	 * Display shipment info in customer emails.
	 *
	 * @version 1.6.8
	 *
	 * @param WC_Order $order         Order object.
	 * @param bool     $sent_to_admin Whether the email is being sent to admin or not.
	 * @param bool     $plain_text    Whether email is in plain text or not.
	 * @param WC_Email $email         Email object.
	 */
	public function email_display( $order, $sent_to_admin, $plain_text = null, $email = null ) {
		/**
		 * Don't include tracking information in refunded email.
		 *
		 * When email instance is `WC_Email_Customer_Refunded_Order`, it may
		 * full or partial refund.
		 *
		 * @see https://github.com/woocommerce/woocommerce-shipment-tracking/issues/61
		 */
		if ( is_a( $email, 'WC_Email_Customer_Refunded_Order' ) ) {
			return;
		}

		$order_id = is_callable( array( $order, 'get_id' ) ) ? $order->get_id() : $order->id;
		if ( true === $plain_text ) {
			wc_get_template( 'email/plain/tracking-info.php', array( 'tracking_items' => $this->get_tracking_items( $order_id, true ) ), 'woocommerce-shipment-tracking/', $this->get_plugin_path() . '/templates/' );
		} else {
			wc_get_template( 'email/tracking-info.php', array( 'tracking_items' => $this->get_tracking_items( $order_id, true ) ), 'woocommerce-shipment-tracking/', $this->get_plugin_path() . '/templates/' );
		}
	}

	/**
	 * Prevents data being copied to subscription renewals
	 */
	public function woocommerce_subscriptions_renewal_order_meta_query( $order_meta_query, $original_order_id, $renewal_order_id ) {
		$order_meta_query .= " AND `meta_key` NOT IN ( '_wc_shipment_tracking_items' )";
		return $order_meta_query;
	}

	/*
	 * Works out the final tracking provider and tracking link and appends then to the returned tracking item
	 *
	*/
	public function get_formatted_tracking_item( $order_id, $tracking_item ) {
		$formatted = array();

		if ( version_compare( WC_VERSION, '3.0', '<' ) ) {
			$postcode = get_post_meta( $order_id, '_shipping_postcode', true );
			$country_code = get_post_meta( $order_id, '_shipping_country', true);
		} else {
			$order    = new WC_Order( $order_id );
			$postcode = $order->get_shipping_postcode();
			$country_code = $order->get_shipping_country();
		}

		$formatted['formatted_tracking_provider'] = '';
		$formatted['formatted_tracking_link']     = '';

		if ( empty( $postcode ) ) {
			$postcode = get_post_meta( $order_id, '_shipping_postcode', true );
		}

		if ( $tracking_item['custom_tracking_provider'] ) {
			$formatted['formatted_tracking_provider'] = $tracking_item['custom_tracking_provider'];
			$formatted['formatted_tracking_link'] = $tracking_item['custom_tracking_link'];
		} else {

			$link_format = '';

			foreach ( $this->get_providers() as $providers ) {
				foreach ( $providers as $provider => $format ) {
					if ( wc_clean( $provider ) === $tracking_item['tracking_provider'] || sanitize_title( $provider ) === $tracking_item['tracking_provider'] ) {
						$link_format = $format;
						$formatted['formatted_tracking_provider'] = $provider;
						break;
					}
				}

				if ( $link_format ) {
					break;
				}
			}

			if ( $link_format ) {
				$formatted['formatted_tracking_link'] = sprintf( $link_format, $tracking_item['tracking_number'], urlencode( wc_normalize_postcode( $postcode ) ), $country_code );
			}
		}

		return $formatted;
	}

	/**
	 * Deletes a tracking item from post_meta array
	 *
	 * @param int    $order_id    Order ID
	 * @param string $tracking_id Tracking ID
	 *
	 * @return bool True if tracking item is deleted successfully
	 */
	public function delete_tracking_item( $order_id, $tracking_id ) {
		$tracking_items = $this->get_tracking_items( $order_id );

		$is_deleted = false;

		if ( count( $tracking_items ) > 0 ) {
			foreach ( $tracking_items as $key => $item ) {
				if ( $item['tracking_id'] == $tracking_id ) {
					unset( $tracking_items[ $key ] );
					$is_deleted = true;
					break;
				}
			}
			$this->save_tracking_items( $order_id, $tracking_items );
		}

		return $is_deleted;
	}

	/*
	 * Adds a tracking item to the post_meta array
	 *
	 * @param int   $order_id    Order ID
	 * @param array $tracking_items List of tracking item
	 *
	 * @return array Tracking item
	 */
	public function add_tracking_item( $order_id, $args ) {
		$tracking_item = array();

		$tracking_item['tracking_provider']        = wc_clean( $args['tracking_provider'] );
		$tracking_item['custom_tracking_provider'] = wc_clean( $args['custom_tracking_provider'] );
		$tracking_item['custom_tracking_link']     = wc_clean( $args['custom_tracking_link'] );
		$tracking_item['tracking_number']          = wc_clean( $args['tracking_number'] );
		$tracking_item['date_shipped']             = wc_clean( strtotime( $args['date_shipped'] ) );

		if ( 0 == (int) $tracking_item['date_shipped'] ) {
			 $tracking_item['date_shipped'] = time();
		}

		if ( $tracking_item['custom_tracking_provider'] ) {
			$tracking_item['tracking_id'] = md5( "{$tracking_item['custom_tracking_provider']}-{$tracking_item['tracking_number']}" . microtime() );
		} else {
			$tracking_item['tracking_id'] = md5( "{$tracking_item['tracking_provider']}-{$tracking_item['tracking_number']}" . microtime() );
		}

		$tracking_items   = $this->get_tracking_items( $order_id );
		$tracking_items[] = $tracking_item;

		$this->save_tracking_items( $order_id, $tracking_items );

		return $tracking_item;
	}

	/**
	 * Saves the tracking items array to post_meta.
	 *
	 * @param int   $order_id       Order ID
	 * @param array $tracking_items List of tracking item
	 */
	public function save_tracking_items( $order_id, $tracking_items ) {
		if ( version_compare( WC_VERSION, '3.0', '<' ) ) {
			update_post_meta( $order_id, '_wc_shipment_tracking_items', $tracking_items );
		} else {
			$order = new WC_Order( $order_id );
			$order->update_meta_data( '_wc_shipment_tracking_items', $tracking_items );
			$order->save_meta_data();
		}
	}

	/**
	 * Gets a single tracking item from the post_meta array for an order.
	 *
	 * @param int    $order_id    Order ID
	 * @param string $tracking_id Tracking ID
	 * @param bool   $formatted   Wether or not to reslove the final tracking
	 *                            link and provider in the returned tracking item.
	 *                            Default to false.
	 *
	 * @return null|array Null if not found, otherwise array of tracking item will be returned
	 */
	public function get_tracking_item( $order_id, $tracking_id, $formatted = false ) {
		$tracking_items = $this->get_tracking_items( $order_id, $formatted );

		if ( count( $tracking_items ) ) {
			foreach ( $tracking_items as $item ) {
				if ( $item['tracking_id'] === $tracking_id ) {
					return $item;
				}
			}
		}

		return null;
	}

	/*
	 * Gets all tracking itesm fron the post meta array for an order
	 *
	 * @param int  $order_id  Order ID
	 * @param bool $formatted Wether or not to reslove the final tracking link
	 *                        and provider in the returned tracking item.
	 *                        Default to false.
	 *
	 * @return array List of tracking items
	 */
	public function get_tracking_items( $order_id, $formatted = false ) {
		global $wpdb;

		if ( version_compare( WC_VERSION, '3.0', '<' ) ) {
			$tracking_items = get_post_meta( $order_id, '_wc_shipment_tracking_items', true );
		} else {
			$order          = new WC_Order( $order_id );
			$tracking_items = $order->get_meta( '_wc_shipment_tracking_items', true );
		}

		if ( is_array( $tracking_items ) ) {
			if ( $formatted ) {
				foreach ( $tracking_items as &$item ) {
					$formatted_item = $this->get_formatted_tracking_item( $order_id, $item );
					$item           = array_merge( $item, $formatted_item );
				}
			}
			return $tracking_items;
		} else {
			return array();
		}
	}

	/**
	* Gets the absolute plugin path without a trailing slash, e.g.
	* /path/to/wp-content/plugins/plugin-directory
	*
	* @return string plugin path
	*/
	public function get_plugin_path() {
		$this->plugin_path = untrailingslashit( plugin_dir_path( dirname( __FILE__ ) ) );
		return $this->plugin_path;
	}
}
