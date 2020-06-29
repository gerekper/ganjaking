<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class WC_Box_Office_Ticket_Barcode {

	/**
	 * Checks if barcode functionality is available or not.
	 *
	 * @since 1.1.1
	 *
	 * @return bool Returns true if barcode functionality is available
	 */
	public function is_available() {
		if ( ! function_exists( 'WC_Order_Barcodes' ) ) {
			return;
		}

		return 'yes' === WC_Order_Barcodes()->barcode_enable;
	}

	/**
	 * Display ticket barcode of a given ticket.
	 *
	 * The barcode for this ticket is displayed in ticket meta box (in admin
	 * screen) and printed page (for customer). If current ticket doesn't have
	 * barcode (for example the order barcodes extension just installed recently),
	 * it will generate new barcode text and image.
	 *
	 * @param integer $ticket Ticket ID
	 * @param array   $args {
	 *     Optional. Arguments to display ticket barcode.
	 *
	 *     @type bool $auto_generate Automatically generate barcode when barcode
	 *                               meta is not present in ticket post. Default
	 *                               to `true`. In case enqueuing JS is not possible
	 *                               (e.g. in printed layout) then set this to
	 *                               `false`
	 * }
	 *
	 * @return void
	 */
	public function display_ticket_barcode( $ticket_id = 0, $args = array() ) {
		if ( ! $this->is_available() ) {
			return;
		}

		if ( ! $ticket_id ) {
			return;
		}

		if ( is_object( $ticket_id ) ) {
			$ticket_id = $ticket_id->ID;
		}

		$args = wp_parse_args(
			$args,
			array(
				'auto_generate' => true,
			)
		);

		ob_start();

		$barcode_text = get_post_meta( $ticket_id, '_barcode_text', true );

		if ( $barcode_text ) {

			$barcode_url = WC_Order_Barcodes()->barcode_url( $ticket_id );

			$before = '<p><a href="' . esc_url( $barcode_url ) . '" target="_blank">';

			$after = '</a></p>';

			WC_Order_Barcodes()->display_barcode( $ticket_id, $before, $after );

		} elseif ( $args['auto_generate'] ) {
			$this->generate_ticket_barcode( $ticket_id );
		}

		$barcode = ob_get_clean();

		echo $barcode;
	}

	/**
	 * Generate unique barcode for ticket.
	 *
	 * Please note that barcode image is generated on the front-end by order
	 * barcodes extension via JS. Using this on the background won't work.
	 *
	 * @since 1.0.0
	 *
	 * @param int $ticket_id Ticket ID
	 *
	 * @return  void
	 */
	public function generate_ticket_barcode( $ticket_id = 0 ) {
		if ( ! $this->is_available() ) {
			return;
		}

		if ( ! $ticket_id ) {
			return;
		}

		// Load JS & CSS needed for barcode generation.
		WC_Order_Barcodes()->load_barcode_assets();

		$barcode_text      = $this->generate_barcode_text_for_ticket();
		$barcode_container = '#barcode_container';
		$type              = WC_Order_Barcodes()->barcode_type;

		// Generate barcode image via JA based on string and selected type.
		$js = $this->get_js( array(
			'container' => $barcode_container,
			'text'      => $barcode_text,
			'type'      => $type,
			'post_ajax' => true,
			'ticket_id' => $ticket_id,
		) );

		ob_start();

		// Render the barcode container.
		echo  sprintf(
			'
			<div id="barcode_container"></div>
			<span style="color:%1$s;font-family:monospace;text-align:center;width:100%;display:block;">
			%2$s
			</span>
			',
			WC_Order_Barcodes()->barcode_colours['foreground'],
			$barcode_text
		);

		// Enqueue the JS for barcode generation.
		wc_enqueue_js( $js );

		$barcode = ob_get_clean();

		echo $barcode;
	}

	/**
	 * Get JS string to be enqueued for rendering barcode or qrcode.
	 *
	 * @since 1.1.1
	 *
	 * @param array $args {
	 *     Arguments to build the JS string.
	 *
	 *     @type string $container Element container that will render the barcode
	 *     @type string $text      Barcode text
	 *     @type string $type      Barcode type
	 *     @type bool   $post_ajax Whether to post via AJAX if barcode is not present
	 *     @type int    $ticket_id Ticket ID
	 * }
	 */
	public function get_js( $args ) {
		$args = wp_parse_args(
			$args,
			array(
				'container' => '#barcode_container',
				'text'      => '',
				'type'      => WC_Order_Barcodes()->barcode_type,
				'post_ajax' => false,
				'ticket_id' => 0,
			)
		);

		$js        = '';       // JS string to enqueue va wc_enqueue_js
		$el_target = 'object'; // Target element which contains the barcode or qrcode
		$el_attr   = 'data';   // Target element attribute which contains the data

		if ( in_array( $args['type'], array( 'qr', 'qrcode' ) ) ) {
			$js = $this->get_qrcode_js( $args );

			$el_target = 'img';
			$el_attr   = 'src';
		} else {
			$js = $this->get_barcode_js( $args );
		}

		// JS to save barcode via ajax.
		if ( $args['post_ajax'] && $args['ticket_id'] ) {
			$js .= sprintf(
				'var ajax_args = %1$s; ajax_args.ticket_barcode_image = $( "%2$s" ).attr( "%3$s" );',
				wp_json_encode( array(
					'action'              => 'save_ticket_barcode',
					'ticket_id'           => $args['ticket_id'],
					'ticket_barcode_text' => $args['text'],
				) ),
				$args['container'] . ' ' . $el_target,
				$el_attr
			);
			$js .= sprintf( '$.post( "%s", ajax_args );', admin_url( 'admin-ajax.php' ) );
		}

		return $js;
	}

	/**
	 * Get JS string to be enqueued for rendering barcode via jQuery.barcode
	 * call.
	 *
	 * @since 1.1.1
	 *
	 * @param array $args {
	 *     Arguments to build JS string that renders barcode.
	 *
	 *     @type string $container Element container that will render the barcode
	 *     @type string $text      Barcode text
	 *     @type string $type      Barcode type
	 * }
	 */
	public function get_barcode_js( $args ) {
		$args = wp_parse_args(
			$args,
			array(
				'container' => '#barcode_container',
				'text'      => '',
				'type'      => WC_Order_Barcodes()->barcode_type,
			)
		);

		$data     = $this->_get_barcode_js_data_args( $args['text'], $args['type'] );
		$settings = $this->_get_barcode_js_settings_args();

		return sprintf(
			'$( "%1$s" ).barcode( %2$s, "%3$s", %4$s );',
			$args['container'],
			$data,
			$args['type'],
			$settings
		);
	}

	/**
	 * Get data arg (first positional parameter) for jQuery.barcode.
	 *
	 * @since 1.1.1
	 *
	 * @param string $barcode_text Barcode text
	 * @param string $type         Barcode type
	 *
	 * @return string
	 */
	protected function _get_barcode_js_data_args( $barcode_text, $type ) {
		$args = array(
			'code' => $barcode_text,
		);

		switch ( $type ) {
			case 'code93':
				$args['crc'] = true;
				break;
			case 'datamatrix':
				$args['rect'] = false;
				break;
		}

		return wp_json_encode( $args );
	}

	/**
	 * Get settings arg (third positional parameter) for jQuery.barcode.
	 *
	 * @since 1.1.1
	 *
	 * @param array $args {
	 *     Arguments to build settings args
	 *
	 *     @type string $color     Stroke color
	 *     @type string $bgColor   Background color
	 *     @type int    $barWidth  Bar width
	 *     @type int    $barHeight Bar height
	 *     @type int    $fontSize  Font size
	 *     @type string $output    Output type
	 * }
	 *
	 * @return string Settings arg for jQuery barcode
	 */
	protected function _get_barcode_js_settings_args( $args = array() ) {
		$defaults = array(
			'color'     => WC_Order_Barcodes()->barcode_colours['foreground'],
			'bgColor'   => WC_Order_Barcodes()->barcode_colours['background'],
			'barWidth'  => 2,
			'barHeight' => 70,
			'fontSize'  => 14,
			'output'    => 'bmp',
		);

		$args = wp_parse_args( $args, $defaults );

		return wp_json_encode( $args );
	}

	/**
	 * Get JS string to be enqueued for rendering qrcode via jQuery.qrcode call.
	 *
	 * @since 1.1.1
	 *
	 * @param array $args {
	 *     Arguments to build JS string that renders qrcode.
	 *
	 *     @type string $container Element container that will render the barcode
	 *     @type string $text      Barcode text
	 * }
	 */
	public function get_qrcode_js( $args ) {
		$args = wp_parse_args(
			$args,
			array(
				'container' => '#barcode_container',
				'text'      => '',
			)
		);

		$qrcode_args = $this->_get_qrcode_js_args( $args['text'] );

		return sprintf(
			'$( "%1$s" ).qrcode( %2$s );',
			$args['container'],
			$qrcode_args
		);
	}

	/**
	 * Get args for for jQuery qrcode.
	 *
	 * @since 1.1.1
	 *
	 * @param string $barcode_text Barcode text
	 * @param array  $args         Args for jQuery qrcode
	 *
	 * @return string Args in JSON string
	 */
	protected function _get_qrcode_js_args( $barcode_text, $args = array() ) {
		$defaults = array(
			'text'       => $barcode_text,
			'label'      => $barcode_text,
			'fill'       => WC_Order_Barcodes()->barcode_colours['foreground'],
			'background' => WC_Order_Barcodes()->barcode_colours['background'],
			'render'     => 'image',
			'width'      => 100,
			'height'     => 100,
		);

		$args = wp_parse_args( $args, $defaults );

		return wp_json_encode( $args );
	}

	/**
	 * Generate a unique barcode text for a ticket.
	 *
	 * @since 1.1.1
	 *
	 * @return string Generated barcode text
	 */
	public function generate_barcode_text_for_ticket() {
		// Use PHP's uniqid() for the barcode
		$barcode_string = uniqid();

		// Check if this barcode already exists and add increment if so
		$existing_ticket_id = $this->get_ticket_id_from_barcode_text( $barcode_string );
		$orig_string        = $barcode_string;

		$i = 1;
		while ( $existing_ticket_id != 0 ) {
			$barcode_string     = $orig_string . $i;
			$existing_ticket_id = $this->get_ticket_id_from_barcode_text( $barcode_string );
			++$i;
		}

		// Return unique barcode.
		return apply_filters( 'woocommerce_box_office_barcode_string', $barcode_string );
	}

	/**
	 * @deprecated
	 */
	public function get_ticket_barcode_string() {
		_deprecated_function( __METHOD__, '1.1.1', 'WC_Box_Office_Ticket_Barcode::generate_barcode_text_for_ticket' );
		return $this->generate_barcode_text_for_ticket();
	}

	/**
	 * Retrieve ticket ID from a given barcode text.
	 *
	 * @since 1.1.1
	 *
	 * @param  string  $barcode Barcode text
	 * @return integer          Ticket ID
	 */
	public function get_ticket_id_from_barcode_text( $barcode = '' ) {
		if ( ! $barcode ) {
			return 0;
		}

		global $wpdb;

		return absint( $wpdb->get_var( $wpdb->prepare( "SELECT post_id FROM {$wpdb->postmeta} WHERE meta_key = %s AND meta_value = %s", '_barcode_text', $barcode ) ) );
	}

	/**
	 * @deprecated
	 */
	public function get_barcode_ticket( $barcode = '' ) {
		_deprecated_function( __METHOD__, '1.1.1', 'WC_Box_Office_Ticket_Barcode::get_ticket_id_from_barcode_text' );
		return $this->get_ticket_id_from_barcode_text( $barcode );
	}
}
