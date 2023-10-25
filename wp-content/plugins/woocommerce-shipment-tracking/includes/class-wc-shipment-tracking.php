<?php

include_once 'trait-order-util.php';

use WooCommerce\ShipmentTracking\Order_Util;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * WooCommerce Shipment Tracking Actions
 *
 * @since 1.4.0
 */
class WC_Shipment_Tracking_Actions {

	use Order_Util;

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
	 * Get shipping providers.
	 *
	 * @return array
	 */
	public function get_providers() {
		return apply_filters(
			'wc_shipment_tracking_get_providers',
			array(
				'Australia'      => array(
					'Australia Post'   => 'https://auspost.com.au/mypost/track/#/details/%1$s',
					'Fastway Couriers' => 'https://www.fastway.com.au/tools/track/?l=%1$s',
				),
				'Austria'        => array(
					'post.at' => 'https://www.post.at/sv/sendungsdetails?snr=%1$s',
					'dhl.at'  => 'https://www.dhl.at/content/at/de/express/sendungsverfolgung.html?brand=DHL&AWB=%1$s',
					'DPD.at'  => 'https://tracking.dpd.de/parcelstatus?locale=de_AT&query=%1$s',
				),
				'Brazil'         => array(
					'Correios' => 'http://websro.correios.com.br/sro_bin/txect01$.QueryList?P_LINGUA=001&P_TIPO=001&P_COD_UNI=%1$s',
				),
				'Belgium'        => array(
					'bpost' => 'https://track.bpost.be/btr/web/#/search?itemCode=%1$s',
				),
				'Canada'         => array(
					'Canada Post' => 'https://www.canadapost-postescanada.ca/track-reperage/en#/resultList?searchFor=%1$s',
					'Purolator'   => 'https://www.purolator.com/purolator/ship-track/tracking-summary.page?pin=%1$s',
				),
				'Czech Republic' => array(
					'PPL.cz'      => 'https://www.ppl.cz/main2.aspx?cls=Package&idSearch=%1$s',
					'Česká pošta' => 'https://www.postaonline.cz/trackandtrace/-/zasilka/cislo?parcelNumbers=%1$s',
					'DHL.cz'      => 'https://www.dhl.cz/cs/express/sledovani_zasilek.html?AWB=%1$s',
					'DPD.cz'      => 'https://tracking.dpd.de/parcelstatus?locale=cs_CZ&query=%1$s',
				),
				'Finland'        => array(
					'Itella' => 'https://www.posti.fi/itemtracking/posti/search_by_shipment_id?lang=en&ShipmentId=%1$s',
				),
				'France'         => array(
					'Colissimo' => 'https://www.laposte.fr/outils/suivre-vos-envois?code=%1$s',
				),
				'Germany'        => array(
					'DHL Intraship (DE)' => 'https://www.dhl.de/de/privatkunden/pakete-empfangen/verfolgen.html?lang=de&idc=%1$s&rfn=&extendedSearch=true',
					'Hermes'             => 'https://www.myhermes.de/empfangen/sendungsverfolgung/sendungsinformation/#%1$s',
					'Deutsche Post DHL'  => 'https://www.dhl.de/de/privatkunden/pakete-empfangen/verfolgen.html?lang=de&idc=%1$s',
					'UPS Germany'        => 'https://wwwapps.ups.com/WebTracking?sort_by=status&tracknums_displayed=1&TypeOfInquiryNumber=T&loc=de_DE&InquiryNumber1=%1$s',
					'DPD.de'             => 'https://tracking.dpd.de/parcelstatus?query=%1$s&locale=en_DE',
				),
				'Ireland'        => array(
					'DPD.ie'  => 'https://dpd.ie/tracking?deviceType=5&consignmentNumber=%1$s',
					'An Post' => 'https://track.anpost.ie/TrackingResults.aspx?rtt=1&items=%1$s',
				),
				'Italy'          => array(
					'BRT (Bartolini)' => 'https://as777.brt.it/vas/sped_det_show.hsm?referer=sped_numspe_par.htm&Nspediz=%1$s',
					'DHL Express'     => 'https://www.dhl.it/it/express/ricerca.html?AWB=%1$s&brand=DHL',
				),
				'India'          => array(
					'DTDC' => 'https://www.dtdc.in/tracking/tracking_results.asp?Ttype=awb_no&strCnno=%1$s&TrkType2=awb_no',
				),
				'Netherlands'    => array(
					'PostNL'          => 'https://postnl.nl/tracktrace/?B=%1$s&P=%2$s&D=%3$s&T=C',
					'DPD.NL'          => 'https://tracking.dpd.de/status/en_US/parcel/%1$s',
					'UPS Netherlands' => 'https://wwwapps.ups.com/WebTracking?sort_by=status&tracknums_displayed=1&TypeOfInquiryNumber=T&loc=nl_NL&InquiryNumber1=%1$s',
				),
				'New Zealand'    => array(
					'Courier Post' => 'https://trackandtrace.courierpost.co.nz/Search/%1$s',
					'NZ Post'      => 'https://www.nzpost.co.nz/tools/tracking?trackid=%1$s',
					'Aramex'       => 'https://www.aramex.co.nz/tools/track?l=%1$s',
					'PBT Couriers' => 'http://www.pbt.com/nick/results.cfm?ticketNo=%1$s',
				),
				'Poland'         => array(
					'InPost'        => 'https://inpost.pl/sledzenie-przesylek?number=%1$s',
					'DPD.PL'        => 'https://tracktrace.dpd.com.pl/parcelDetails?p1=%1$s',
					'Poczta Polska' => 'https://emonitoring.poczta-polska.pl/?numer=%1$s',
				),
				'Romania'        => array(
					'Fan Courier'   => 'https://www.fancourier.ro/awb-tracking/?xawb=%1$s',
					'DPD Romania'   => 'https://tracking.dpd.de/parcelstatus?query=%1$s&locale=ro_RO',
					'Urgent Cargus' => 'https://app.urgentcargus.ro/Private/Tracking.aspx?CodBara=%1$s',
				),
				'South African'  => array(
					'SAPO'    => 'http://sms.postoffice.co.za/TrackingParcels/Parcel.aspx?id=%1$s',
					'Fastway' => 'https://fastway.co.za/our-services/track-your-parcel?l=%1$s',
				),
				'Sweden'         => array(
					'PostNord Sverige AB' => 'https://portal.postnord.com/tracking/details/%1$s',
					'DHL.se'              => 'https://www.dhl.com/se-sv/home/tracking.html?submit=1&tracking-id=%1$s',
					'Bring.se'            => 'https://tracking.bring.se/tracking/%1$s',
					'UPS.se'              => 'https://www.ups.com/track?loc=sv_SE&tracknum=%1$s&requester=WT/',
					'DB Schenker'         => 'http://privpakportal.schenker.nu/TrackAndTrace/packagesearch.aspx?packageId=%1$s',
				),
				'United Kingdom' => array(
					'DHL'                       => 'https://www.dhl.com/content/g0/en/express/tracking.shtml?brand=DHL&AWB=%1$s',
					'DPD.co.uk'                 => 'https://www.dpd.co.uk/apps/tracking/?reference=%1$s#results',
					'DPD Local'                 => 'https://apis.track.dpdlocal.co.uk/v1/track?postcode=%2$s&parcel=%1$s',
					'EVRi'                      => 'https://www.evri.com/track/parcel/%1$s',
					'EVRi (international)'      => 'https://international.evri.com/tracking/%1$s',
					'ParcelForce'               => 'https://www.parcelforce.com/track-trace?trackNumber=%1$s',
					'Royal Mail'                => 'https://www3.royalmail.com/track-your-item#/tracking-results/%1$s',
					'TNT Express (consignment)' => 'https://www.tnt.com/express/en_gb/site/shipping-tools/tracking.html?searchType=con&cons=%1$s',
					'TNT Express (reference)'   => 'https://www.tnt.com/express/en_gb/site/shipping-tools/tracking.html?searchType=ref&cons=%1$s',
					'DHL Parcel UK'             => 'https://track.dhlparcel.co.uk/?con=%1$s',
				),
				'United States'  => array(
					'DHL US'        => 'https://www.logistics.dhl/us-en/home/tracking/tracking-ecommerce.html?tracking-id=%1$s',
					'DHL eCommerce' => 'https://webtrack.dhlecs.com/orders?trackingNumber=%1$s',
					'Fedex'         => 'https://www.fedex.com/apps/fedextrack/?action=track&action=track&tracknumbers=%1$s',
					'FedEx Sameday' => 'https://www.fedexsameday.com/fdx_dotracking_ua.aspx?tracknum=%1$s',
					'GlobalPost'    => 'https://www.goglobalpost.com/track-detail/?t=%1$s',
					'OnTrac'        => 'https://www.ontrac.com/tracking/?number=%1$s',
					'UPS'           => 'https://www.ups.com/track?loc=en_US&tracknum=%1$s',
					'USPS'          => 'https://tools.usps.com/go/TrackConfirmAction_input?qtc_tLabels1=%1$s',
				),
			)
		);
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
	 * @param array $columns Existing columns
	 *
	 * @return array Altered columns
	 * @since 1.8.0
	 *
	 */
	public function add_wc_orders_list_columns( $columns ) {
		$columns['shipment_tracking'] = __( 'Shipment Tracking', 'woocommerce-shipment-tracking' );

		return $columns;
	}

	/**
	 * Render shipment tracking in custom column.
	 *
	 * @param string $column_name The name of the column to display.
	 * @param int $post_id The current post ID.
	 *
	 * @since 1.6.1
	 *
	 */
	public function render_shop_order_columns( $column_name, $post_id ) {
		if ( 'shipment_tracking' === $column_name ) {
			echo wp_kses_post( $this->get_shipment_tracking_column( $post_id ));
		}
	}

	/**
	 * Render shipment tracking in custom column on WC Orders page (when using Custom Order Tables).
	 *
	 * @param string $column_name Identifier for the custom column.
	 * @param \WC_Order $order Current WooCommerce order object.
	 *
	 * @return void
	 * @since 1.8.0
	 *
	 */
	public function render_wc_orders_list_columns( $column_name, $order ) {
		if ( 'shipment_tracking' === $column_name ) {
			echo wp_kses_post( $this->get_shipment_tracking_column( $order->get_id() ) );
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
		add_meta_box( 'woocommerce-shipment-tracking', __( 'Shipment Tracking', 'woocommerce-shipment-tracking' ), array( $this, 'meta_box' ), $this->get_order_admin_screen(), 'side', 'high' );
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
						- <?php echo sprintf( '<a href="%s" target="_blank" title="' . esc_attr( __( 'Click here to track your shipment', 'woocommerce-shipment-tracking' ) ) . '">' . esc_html__( 'Track', 'woocommerce-shipment-tracking' ) . '</a>', esc_url( $formatted['formatted_tracking_link'] ) ); ?>
					<?php endif; ?>
					<br/>
					<em><?php echo esc_html( $item['tracking_number'] ); ?></em>
				</p>
				<p class="meta">
					<?php /* translators: 1: shipping date */ ?>
					<?php echo sprintf( esc_html__( 'Shipped on %s', 'woocommerce-shipment-tracking' ), esc_html( date_i18n( wc_date_format(), $item['date_shipped'] ) ) ); ?>
					<a href="#" class="delete-tracking" rel="<?php echo esc_attr( $item['tracking_id'] ); ?>"><?php esc_html_e( 'Delete', 'woocommerce-shipment-tracking' ); ?></a>
				</p>
			</div>
			<?php
	}

	/**
	 * Show the meta box for shipment info on the order page
	 */
	public function meta_box( $post_or_order_object   ) {
		$order = $this->init_theorder_object( $post_or_order_object );

		$tracking_items = $this->get_tracking_items( $order->get_id() );

		echo '<div id="tracking-items">';

		if ( count( $tracking_items ) > 0 ) {
			foreach ( $tracking_items as $tracking_item ) {
				$this->display_html_tracking_item_for_meta_box( $order->get_id(), $tracking_item );
			}
		}

		echo '</div>';

		echo '<button class="button button-show-form" type="button">' . esc_html__( 'Add Tracking Number', 'woocommerce-shipment-tracking' ) . '</button>';

		echo '<div id="shipment-tracking-form">';
		// Providers
		echo '<p class="form-field tracking_provider_field"><label for="tracking_provider">' . esc_html__( 'Provider:', 'woocommerce-shipment-tracking' ) . '</label><br/><select id="tracking_provider" name="tracking_provider" class="chosen_select" style="width:100%;">';

		echo '<option value="">' . esc_html__( 'Custom Provider', 'woocommerce-shipment-tracking' ) . '</option>';

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

		echo '<button class="button button-primary button-save-form">' . esc_html__( 'Save Tracking', 'woocommerce-shipment-tracking' ) . '</button>';

		// Live preview
		echo '<p class="preview_tracking_link">' . esc_html__( 'Preview:', 'woocommerce-shipment-tracking' ) . ' <a href="" target="_blank">' . esc_html__( 'Click here to track your shipment', 'woocommerce-shipment-tracking' ) . '</a></p>';

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
		// phpcs:disable WordPress.Security.NonceVerification.Missing -- Nonce is checked before woocommerce_process_shop_order_meta is invoked.
		if ( !empty( $_POST['tracking_number'] ) ) {
			$args = array(
				'tracking_provider'        => wc_clean( $_POST['tracking_provider'] ),
				'custom_tracking_provider' => wc_clean( $_POST['custom_tracking_provider'] ),
				'custom_tracking_link'     => wc_clean( $_POST['custom_tracking_link'] ),
				'tracking_number'          => wc_clean( $_POST['tracking_number'] ),
				'date_shipped'             => wc_clean( $_POST['date_shipped'] ),
			);

			$this->add_tracking_item( $post_id, $args );
		}
		// phpcs:enable WordPress.Security.NonceVerification.Missing
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

		if ( !empty( $_POST['tracking_number'] ) ) {

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
	 * List of excluded email classes.
	 * Shipment tracking will not be added to emails that are instances of these email classes.
	 *
	 * @return Array.
	 */
	public function get_excluded_email_classes() {
		return apply_filters(
			'wc_shipment_tracking_excluded_email_classes',
			array(
				/**
				 * Don't include tracking information in an order refund email.
				 *
				 * When the email instance is `WC_Email_Customer_Refunded_Order`, it may
				 * be a full or partial refund.
				 *
				 * @see https://github.com/woocommerce/woocommerce-shipment-tracking/issues/61
				 */
				'WC_Email_Customer_Refunded_Order'
			)
		);
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
		$excluded_email_classes = $this->get_excluded_email_classes();

		foreach ( $excluded_email_classes as $email_class ) {
			if ( is_a( $email, $email_class ) ) {
				return;
			}
		}

		if ( true === $plain_text ) {
			wc_get_template( 'email/plain/tracking-info.php', array( 'tracking_items' => $this->get_tracking_items( $order->get_id(), true ) ), 'woocommerce-shipment-tracking/', $this->get_plugin_path() . '/templates/' );
		} else {
			wc_get_template( 'email/tracking-info.php', array( 'tracking_items' => $this->get_tracking_items( $order->get_id(), true ) ), 'woocommerce-shipment-tracking/', $this->get_plugin_path() . '/templates/' );
		}
	}

	/**
	 * Prevents shipment tracking data being copied to subscription renewals
	 *
	 * @param array $data
	 * @return array
	 */
	public function prevent_copying_shipment_tracking_data( $data ) {
		unset( $data['_wc_shipment_tracking_items'] );

		return $data;
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
		$order = wc_get_order( $order_id );

		if ( ! $order instanceof WC_Order ) {
			return array();
		}

		$postcode = ! empty( $order->get_shipping_postcode() ) ? $order->get_shipping_postcode() : $order->get_billing_postcode();

		$country_code = $order->get_shipping_country();

		$formatted = array();

		$formatted['formatted_tracking_provider'] = '';
		$formatted['formatted_tracking_link']     = '';

		if ( $tracking_item['custom_tracking_provider'] ) {
			$formatted['formatted_tracking_provider'] = $tracking_item['custom_tracking_provider'];
			$formatted['formatted_tracking_link']     = $tracking_item['custom_tracking_link'];
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
				$values  = apply_filters( 'wc_shipment_tracking_provider_url_values',
					array(
						$tracking_item['tracking_number'],
						urlencode( wc_normalize_postcode( $postcode ) ),
						$country_code,
						$order_id
					),
					$tracking_item
				);
   				array_unshift( $values, $link_format );
				$formatted['formatted_tracking_link'] = call_user_func_array( "sprintf", $values );
			}
		}

		return apply_filters( 'wc_shipment_tracking_formatted_item', $formatted, $order_id, $tracking_item, $this );
	}

	/**
	 * Deletes a tracking item from order meta array
	 *
	 * @param int    $order_id    Order ID
	 * @param string $tracking_id Tracking ID
	 *
	 * @return bool True if tracking item is deleted successfully
	 */
	public function delete_tracking_item( $order_id, $tracking_id ) {
		$tracking_items = $this->get_tracking_items( $order_id );

		$is_deleted              = false;
		$tracking_item_to_delete = array();

		if ( count( $tracking_items ) > 0 ) {
			foreach ( $tracking_items as $key => $item ) {
				if ( $item['tracking_id'] == $tracking_id ) {
					$tracking_item_to_delete = $item;
					unset( $tracking_items[ $key ] );
					$is_deleted = true;
					break;
				}
			}

			/**
			 * Filter the tracking items before deleting it from order meta.
			 *
			 * @param array $tracking_items          List of tracking item.
			 * @param array $tracking_item_to_delete New tracking item.
			 * @param int   $order_id                Order ID.
			 */
			$tracking_items = apply_filters( 'wc_shipment_tracking_before_delete_tracking_items', $tracking_items, $tracking_item_to_delete, $order_id );

			$this->save_tracking_items( $order_id, $tracking_items );
		}

		return $is_deleted;
	}

	/*
	 * Adds a tracking item to the order meta array
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

		/**
		 * Filter the tracking items before adding it into order meta.
		 *
		 * @param array $tracking_items List of tracking item.
		 * @param array $tracking_item  New tracking item.
		 * @param int   $order_id       Order ID.
		 */
	    $tracking_items = apply_filters( 'wc_shipment_tracking_before_add_tracking_items', $tracking_items, $tracking_item, $order_id );

		$this->save_tracking_items( $order_id, $tracking_items );

		return $tracking_item;
	}

	/**
	 * Saves the tracking items array to order meta.
	 *
	 * @param int   $order_id       Order ID
	 * @param array $tracking_items List of tracking item
	 */
	public function save_tracking_items( $order_id, $tracking_items ) {
		$order = wc_get_order( $order_id );

		if ( ! $order instanceof WC_Order ) {
			return;
		}

		// Always re-index the array
		$tracking_items = array_values( $tracking_items );

		$order->update_meta_data( '_wc_shipment_tracking_items', $tracking_items );
		$order->save();
	}

	/**
	 * Gets a single tracking item from the order meta array for an order.
	 *
	 * @param int    $order_id    Order ID
	 * @param string $tracking_id Tracking ID
	 * @param bool   $formatted   Whether to resolve the final tracking
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
	 * Gets all tracking items from the post meta array for an order
	 *
	 * @param int  $order_id  Order ID
	 * @param bool $formatted Whether to resolve the final tracking link
	 *                        and provider in the returned tracking item.
	 *                        Default to false.
	 *
	 * @return array List of tracking items
	 */
	public function get_tracking_items( $order_id, $formatted = false ) {
		$order = wc_get_order( $order_id );

		if ( ! $order instanceof WC_Order ) {
			return array();
		}

		$tracking_items = $order->get_meta( '_wc_shipment_tracking_items' );

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
		return wc_shipment_tracking()->get_plugin_path();
	}
}
