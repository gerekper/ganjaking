<?php
/**
 * Global Helper functions for Box Office.
 *
 * @package woocommerce-box-office
 */

/**
 * Returns the main instance of WC_Box_Office to prevent the need to use globals.
 *
 * @since  1.0.0
 * @return object WC_Box_Office.
 */
function WCBO() {
	$instance = WC_Box_Office::instance( dirname( __DIR__ ) . '/woocommerce-box-office.php', WOOCOMMERCE_BOX_OFFICE_VERSION );
	return $instance;
}

/**
 * Get ticket.
 *
 * @param mixed $ticket Ticket ID or WP_Post object
 *
 * @return mixed
 */
function wc_box_office_get_ticket( $ticket ) {
	return new WC_Box_Office_Ticket( $ticket );
}

/**
 * Is a given product with ticketing enabled.
 *
 * @version 1.1.9
 *
 * @param mixed $product Product object or ID.
 *
 * @return bool Returns true if product is ticket product.
 */
function wc_box_office_is_product_ticket( $product ) {
	$product = wc_get_product( $product );
	if ( ! $product ) {
		return false;
	}

	$product_id = $product->is_type( 'variation' ) ? $product->get_parent_id() : $product->get_ID();

	return 'yes' === get_post_meta( $product_id, '_ticket', true );
}

/**
 * Check if current page is the my ticket page.
 *
 * @since 1.1.0
 *
 * @return bool Returns true if current page is my ticket page.
 */
function wcbo_is_my_ticket_page() {
	return is_page( get_option( 'box_office_my_ticket_page_id' ) ) && ! empty( $_GET['token'] );
}

/**
 * Get ticket by token.
 *
 * @param string $token Ticket's token
 *
 * @return null|WC_Box_Office_Ticket Ticket object
 */
function wc_box_office_get_ticket_by_token( $token ) {
	$ticket = null;

	if ( ! $token ) {
		return $ticket;
	}

	$args = array(
		'post_type'      => 'event_ticket',
		'post_status'    => array( 'publish', 'pending' ),
		'posts_per_page' => 1,
		'meta_query'     => array(
			array(
				'key'   => '_token',
				'value' => $token,
			),
		),
	);

	$tickets = get_posts( $args );
	if ( ! empty( $tickets[0] ) ) {
		$ticket = new WC_Box_Office_Ticket( $tickets[0] );
	}

	return $ticket;
}

/**
 * Get ticket by email and product ID.
 *
 * @param string $email      Ticket's email
 * @param mixed  $product_id Product's enabled-ticket ID
 *
 * @return null|WC_Box_Office_Ticket Ticket object
 */
function wc_box_office_get_ticket_by_email( $email, $product_id ) {
	$ticket = null;

	if ( ! is_email( $email ) || ! $product_id ) {
		return $ticket;
	}

	$email_field = '';

	// Get ticket fields from the product.
	$fields = wc_box_office_get_product_ticket_fields( $product_id );
	if ( empty( $fields ) ) {
		return $ticket;
	}

	foreach ( $fields as $key => $field ) {
		if ( 'email' === $field['type'] && 'yes' === $field['email_contact'] ) {
			$email_field = $key;
			break;
		}
	}

	if ( empty( $email_field ) ) {
		return $ticket;
	}

	$args = array(
		'post_type'      => 'event_ticket',
		'post_status'    => array( 'publish' ),
		'posts_per_page' => 1,
		'meta_query'     => array(
			array(
				'key'   => '_product_id',
				'value' => $product_id,
			),
			array(
				'key'   => $email_field,
				'value' => $email,
			),
		),
	);

	$tickets = get_posts( $args );
	if ( ! empty( $tickets[0] ) ) {
		$ticket = new WC_Box_Office_Ticket( $tickets[0] );
	}

	return $ticket;
}

/**
 * Get description of a ticket.
 *
 * @param  integer $ticket_id Ticket ID
 * @param  string  $formater  Display formatter. Defaults to flat.
 * @return string             Ticket description
 */
function wc_box_office_get_ticket_description( $ticket_id = 0, $formatter = 'flat' ) {
	if ( ! $ticket_id ) {
		return '';
	}

	// Get ticket product ID.
	$product_id = get_post_meta( $ticket_id, '_product', true );

	// Get available fields from ticket product.
	$ticket_fields = get_post_meta( $product_id, '_ticket_fields', true );

	switch ( $formatter ) {
		case 'flat':
			$formatter = 'wc_box_office_ticket_description_flat_formatter';
			break;
		case 'list':
			$formatter = 'wc_box_office_ticket_description_list_formatter';
			break;
		case 'table':
			$formatter = 'wc_box_office_ticket_description_table_formatter';
			break;
		default:
			if ( ! is_callable( $formatter ) ) {
				$formatter = 'wc_box_office_ticket_description_flat_formatter';
			}
	}

	return call_user_func_array( $formatter, array( $ticket_id, $ticket_fields ) );
}

/**
 * Flat formatter for ticket description. Returns pipe separated fields.
 *
 * @param integer $ticket_id     Ticket ID
 * @param array   $ticket_fields Ticket fields
 *
 * @return string Ticket description
 */
function wc_box_office_ticket_description_flat_formatter( $ticket_id, $ticket_fields ) {
	$ticket_description = '';

	if ( empty( $ticket_fields ) ) {
		return $ticket_description;
	}

	foreach ( $ticket_fields as $field_key => $field ) {
		$ticket_meta = get_post_meta( $ticket_id, $field_key, true );

		// Checkboxes.
		if ( is_array( $ticket_meta ) ) {
			$value = implode( ', ', $ticket_meta );
		} else {
			$value = $ticket_meta;
		}

		if ( empty( $value ) ) {
			$value = '-';
		}

		if ( $ticket_description ) {
			$ticket_description .= ' | ';
		}
		$ticket_description .= esc_html( $field['label'] . ': ' . $value );
	}

	return $ticket_description;
}

/**
 * List formatter for ticket description. Returns unordered list of fields.
 *
 * @param integer $ticket_id     Ticket ID
 * @param array   $ticket_fields Ticket fields
 *
 * @return string Ticket description
 */
function wc_box_office_ticket_description_list_formatter( $ticket_id, $ticket_fields ) {
	$ticket_description = '';
	foreach ( $ticket_fields as $field_key => $field ) {
		$ticket_meta = get_post_meta( $ticket_id, $field_key, true );

		// Checkboxes.
		if ( is_array( $ticket_meta ) ) {
			$value = implode( ', ', $ticket_meta );
		} else {
			$value = $ticket_meta;
		}

		if ( empty( $value ) ) {
			$value = '-';
		}

		$ticket_description .= sprintf( '<li><strong>%s</strong>: <span class="text">%s</span></li>', esc_html( $field['label'] ), esc_html( $value ) );
	}

	if ( ! empty( $ticket_description ) ) {
		$ticket_description = '<ul>' . $ticket_description . '</ul>';
	}

	return $ticket_description;
}

/**
 * Table formatter for ticket description.
 *
 * @since 1.1.1
 *
 * @param integer $ticket_id     Ticket ID
 * @param array   $ticket_fields Ticket fields
 *
 * @return string Ticket description
 */
function wc_box_office_ticket_description_table_formatter( $ticket_id, $ticket_fields ) {
	$ticket_description = '';
	foreach ( $ticket_fields as $field_key => $field ) {
		$ticket_meta = get_post_meta( $ticket_id, $field_key, true );

		// Checkboxes.
		if ( is_array( $ticket_meta ) ) {
			$value = implode( ', ', $ticket_meta );
		} else {
			$value = $ticket_meta;
		}

		if ( empty( $value ) ) {
			$value = '-';
		}

		$ticket_description .= sprintf( '<tr><td><strong>%1$s</strong></td><td>%2$s</td></tr>', esc_html( $field['label'] ), esc_html( $value ) );
	}

	if ( ! empty( $ticket_description ) ) {
		$ticket_description = sprintf( '<table class="ticket-table"><tbody>%s</tbody></table>', $ticket_description );
	}

	return $ticket_description;
}

/**
 * Get ticket URL of a given ticket ID.
 *
 * @since 1.1.0
 *
 * @param int  $ticket_id Ticket ID
 * @param bool $print     Whether to print the ticket
 *
 * @return string Ticket URL
 */
function wcbo_get_my_ticket_url( $ticket_id, $print = false ) {
	$page_id = absint( get_option( 'box_office_my_ticket_page_id' ) );

	// Backward compatibility with previous 1.1.0.
	if ( ! $page_id ) {
		return wc_box_office_get_ticket_url( $ticket_id, $print ? 'print' : 'edit' );
	}

	$token = get_post_meta( $ticket_id, '_token', true );
	$url   = add_query_arg( 'token', $token, get_permalink( $page_id ) );
	if ( $print ) {
		$url = add_query_arg( 'print', 'true', $url );
	}

	return apply_filters( 'woocommerce_box_office_my_ticket_url', $url );
}

/**
 * Get tickets purchased by user.
 *
 * @param  integer $user_id User ID
 * @param  string  $amount  Number of tickets to fetch
 * @param  integer $page    Page to fetch
 * @return array            Array of ticket posts
 */
function wc_box_office_get_tickets_by_user( $user_id = 0, $amount = 'all', $page = 1 ) {
	if ( ! $user_id ) {
		$user_id = get_current_user_id();
	}

	if ( ! $user_id ) {
		return;
	}

	if ( 'all' === $amount ) {
		$amount = -1;
	}

	$plugin_token = WCBO()->_token;

	$args = apply_filters( $plugin_token . '_user_tickets_query', array(
		'post_type'      => 'event_ticket',
		'post_status'    => 'publish',
		'posts_per_page' => $amount,
		'page'           => $page,
		'meta_query'     => array(
			array(
				'key'   => '_user',
				'value' => $user_id,
			),
		),
	), $user_id );

	return get_posts( $args );
}


/**
 * Get all ticket products in database.
 *
 * @return array Product posts
 */
function wc_box_office_get_all_ticket_products() {
	$args = array(
		'post_type'      => 'product',
		'post_status'    => 'publish',
		'posts_per_page' => -1,
		'meta_query'     => array(
			array(
				'key'     => '_ticket',
				'value'   => 'yes',
				'compare' => '=',
			),
		),
	);

	return get_posts( $args );
}

/**
 * Send email to ticket holder.
 *
 * @param integer      $ticket_id  Ticket ID
 * @param string|array $address    Email address(es). If empty, uses email in ticket
 *                                 ticket field which sets as email contact
 * @param string       $subject    Email subject
 * @param string       $message    Email body
 *
 * @return bool
 */
function wc_box_office_send_ticket_email( $ticket_id = 0, $address = '', $subject = '', $message = '' ) {
	if ( ! $ticket_id ) {
		return;
	}

	// Get ticket product.
	$product_id = get_post_meta( $ticket_id, '_product', true );
	if ( ! $product_id ) {
		return;
	}

	// Check if email must be sent.
	$send_email = apply_filters( 'woocommerce_box_office_send_ticket_email', 'yes' === get_post_meta( $product_id, '_email_tickets', true ), $ticket_id );
	if ( ! $send_email ) {
		return;
	}

	$to = array();
	if ( ! empty( $address ) ) {
		if ( is_array( $address ) ) {
			$to = $address;
		} else {
			$to = array( $address );
		}
	} else {
		$to = wc_box_office_get_ticket_email_contacts( $ticket_id );
	}

	if ( empty( $to ) ) {
		return;
	}

	$message = wpautop( $message );
	$message = do_shortcode( $message );
	$message = wc_box_office_get_parsed_ticket_content( $ticket_id, $message );
	$subject = wc_box_office_get_parsed_ticket_content( $ticket_id, $subject );

	return wc_box_office_send_mail( $to, $subject, $message );
}

/**
 * Send mail.
 *
 * @param string|array $to      Email address(es)
 * @param string       $subject Email subject
 * @param string       $message Email body
 *
 * @return bool
 */
function wc_box_office_send_mail( $to, $subject, $message ) {
	// Set email headers.
	$headers = "Content-Type: text/html\r\n";

	// Filters for the email.
	add_filter( 'wp_mail_from', 'wc_box_office_get_email_from_address' );
	add_filter( 'wp_mail_from_name', 'wc_box_office_get_email_from_name' );
	add_filter( 'wp_mail_content_type', 'wc_box_office_get_email_content_type' );

	// Send email.
	$result = wp_mail( $to, $subject, $message, $headers );

	// Unhook filters.
	remove_filter( 'wp_mail_from', 'wc_box_office_get_email_from_address' );
	remove_filter( 'wp_mail_from_name', 'wc_box_office_get_email_from_name' );
	remove_filter( 'wp_mail_content_type', 'wc_box_office_get_email_content_type' );

	return $result;
}

/**
 * Get email contacts from a ticket.
 *
 * @param int $ticket_id Ticket ID
 *
 * @return array List of email contacts on a ticket
 */
function wc_box_office_get_ticket_email_contacts( $ticket_id ) {
	$emails     = array();
	$product_id = get_post_meta( $ticket_id, '_product', true );
	foreach ( wc_box_office_get_product_ticket_fields( $product_id ) as $field_key => $field ) {
		if ( 'email' === $field['type'] && 'yes' === $field['email_contact'] ) {
			$field_value = get_post_meta( $ticket_id, $field_key, true );
			if ( $field_value ) {
				$emails[] = $field_value;
			}
		}
	}

	return $emails;
}

/**
 * Get parsed ticket content. Variables (.e.g, '{First Name}, {Email}, etc') will
 * be replaced by value based on given ticket.
 *
 * @param int    $ticket_id Ticket ID.
 * @param string $content Content to parse.
 *
 * @return string Parsed content
 */
function wc_box_office_get_parsed_ticket_content( $ticket_id, $content ) {
	$barcode_obj = new WC_Box_Office_Ticket_Barcode();

	if ( $barcode_obj->is_available() ) {
		$barcode = WC_Order_Barcodes()->display_barcode( $ticket_id );
		$content = str_replace( '{barcode}', $barcode, $content );
	}

	// Parse link var '{ticket_link}'.
	$ticket_link = wcbo_get_my_ticket_url( $ticket_id );
	$content     = str_replace( '{ticket_link}', $ticket_link, $content );

	// Parse ticket id '{ticket_id}'.
	$content = str_replace( '{ticket_id}', $ticket_id, $content );

	// Parse link var '{ticket_token}'.
	$ticket_token = get_post_meta( $ticket_id, '_token', true );
	$content      = str_replace( '{token}', $ticket_token, $content );

	// Parse post vars: '{post_title}' and '{post_content}'. The post object
	// would be ticket product.
	$product_id = get_post_meta( $ticket_id, '_product', true );
	if ( $product_id ) {
		$ticket_product = wc_get_product( $product_id );
		$description    = is_callable( array( $ticket_product, 'get_description' ) )
			? call_user_func( array( $ticket_product, 'get_description' ) )
			: $ticket_product->post->post_content;

		$post_vars = array(
			'{post_title}'   => $ticket_product->get_title(),
			'{post_content}' => $description,
		);
		foreach ( $post_vars as $var => $value ) {
			$content = str_replace( $var, $value, $content );
		}
	}

	foreach ( wc_box_office_get_product_ticket_fields( $product_id ) as $field_key => $field ) {
		// Replace content placeholders with ticket fields.
		$field_value = get_post_meta( $ticket_id, $field_key, true );
		if ( is_array( $field_value ) ) {
			$field_value = implode( ', ', $field_value );
		}
		$content = str_replace( '{' . $field['label'] . '}', $field_value, $content );
	}

	return $content;
}

/**
 * Get from name for email.
 *
 * @access public
 * @return string
 */
function wc_box_office_get_email_from_name() {
	$from_name = get_option( 'woocommerce_email_from_name' );
	return wp_specialchars_decode( $from_name );
}

/**
 * Get from email address.
 *
 * @access public
 * @return string
 */
function wc_box_office_get_email_from_address() {
	$from_address = get_option( 'woocommerce_email_from_address' );
	return $from_address;
}

/**
 * Get the content type for the email.
 *
 * @access public
 * @return string
 */
function wc_box_office_get_email_content_type() {
	return 'text/html';
}

/**
 * Get ticket fields of a product.
 *
 * @param int $product_id Product ID
 *
 * @return array
 */
function wc_box_office_get_product_ticket_fields( $product_id ) {
	$ticket_fields = get_post_meta( $product_id, '_ticket_fields', true );

	return ! empty( $ticket_fields ) ? $ticket_fields : array();
}

function wc_box_office_get_product_ticket_fields_options( $product_id, $selected = array() ) {
	$ticket_fields = wc_box_office_get_product_ticket_fields( $product_id );

	$options = '';
	foreach ( $ticket_fields as $key => $field ) {
		$options .= sprintf( '<option value="%s"%s>%s</option>', esc_attr( $key ), selected( in_array( $key, $selected ), true, false ), esc_html( $field['label'] ) );
	}

	return $options;
}

/**
 * Get all available ticket field types.
 *
 * @return array Array of field types
 */
function wc_box_office_ticket_field_types() {
	$plugin_token = WCBO()->_token;

	$types = array(
		'first_name' => __( 'First name', 'woocommerce-box-office' ),
		'last_name'  => __( 'Last name', 'woocommerce-box-office' ),
		'email'      => __( 'Email address', 'woocommerce-box-office' ),
		'url'        => __( 'URL', 'woocommerce-box-office' ),
		'twitter'    => __( 'Twitter', 'woocommerce-box-office' ),
		'text'       => __( 'Text', 'woocommerce-box-office' ),
		'select'     => __( 'Drop down menu', 'woocommerce-box-office' ),
		'radio'      => __( 'Radio buttons', 'woocommerce-box-office' ),
		'checkbox'   => __( 'Checkbox(es)', 'woocommerce-box-office' ),
	);

	return apply_filters( $plugin_token . '_ticket_field_types', $types );
}

/**
 * Get all available autofill options for tickets.
 *
 * @return array Array of options
 */
function wc_box_office_autofill_options() {
	$plugin_token = WCBO()->_token;

	$options = array(
		'billing_first_name' => __( 'First name', 'woocommerce-box-office' ),
		'billing_last_name'  => __( 'Last name', 'woocommerce-box-office' ),
		'billing_email'      => __( 'Email address', 'woocommerce-box-office' ),
		'billing_phone'      => __( 'Phone', 'woocommerce-box-office' ),
		'billing_company'    => __( 'Company', 'woocommerce-box-office' ),
		'billing_country'    => __( 'Country', 'woocommerce-box-office' ),
		'billing_city'       => __( 'Town / City', 'woocommerce-box-office' ),
		'billing_state'      => __( 'State', 'woocommerce-box-office' ),
		'billing_postcode'   => __( 'Postcode / Zip', 'woocommerce-box-office' ),
	);

	return apply_filters( $plugin_token . '_ticket_autofill_options', $options );
}

/**
 * Get input HTML for ticket field.
 *
 * @param  string  $field_key Field key
 * @param  array   $field     Field info
 * @param  integer $ticket_id Ticket ID
 * @param  boolean $disabled  Whether field should be disabled or not
 * @return string             HTML of input field
 */
function wc_box_office_ticket_field_input( $field_key = '', $field = array(), $ticket_id = 0, $disabled = false ) {
	$html = '';

	if ( ! $field_key || ! is_array( $field ) || ! isset( $field['type'] ) ) {
		return $html;
	}

	$value = '';
	if ( $ticket_id ) {
		$value = get_post_meta( $ticket_id, $field_key, true );
	}

	if ( 'url' === $field['type'] ) {
		$value = esc_url( $value );
	}

	if ( $disabled ) {
		$disabled = 'disabled="disabled"';
	} else {
		$disabled = '';
	}

	switch ( $field['type'] ) {
		case 'text':
		case 'first_name':
		case 'last_name':
		case 'email':
		case 'url':
		case 'twitter':
			$html .= '<input type="text" class="input-text" value="' . $value . '" name="ticket_fields[' . $field_key . ']" id="field_' . $field_key . '"' . $disabled . ' />' . "\n";
		break;

		case 'select':
			$options = explode( ',', $field['options'] );

			$html .= '<select name="ticket_fields[' . $field_key . ']" id="field_' . $field_key . '"' . $disabled . '>' . "\n";
			foreach ( $options as $option ) {
				$option = trim( $option );
				$html .= '<option ' . selected( esc_attr( $option ), $value, false ) . 'value="' . esc_attr( $option ) . '">' . $option . '</option>' . "\n";
			}
			$html .= '</select>' . "\n";
		break;

		case 'radio':
			$options = explode( ',', $field['options'] );

			$i = 1;
			foreach ( $options as $option ) {
				$option = trim( $option );
				$html .= '<label for="field_' . $field_key . '_' . $i . '"><input type="radio" ' . checked( esc_attr( $option ), $value, false ) . ' name="ticket_fields[' . $field_key . ']" class="field_' . $field_key . '" id="field_' . $field_key . '_' . $i . '" value="' . esc_attr( $option ) . '"' . $disabled . ' /> ' . $option . '</label> ' . "\n";
				++$i;
			}
		break;

		case 'checkbox':
			$options = explode( ',', $field['options'] );

			$i = 1;
			foreach ( $options as $option ) {
				$option = trim( $option );

				$checked = '';
				if ( in_array( $option, $value ) ) {
					$checked = 'checked="checked"';
				}
				$html .= '<label for="field_' . $field_key . '_' . $i . '"><input type="checkbox" ' . $checked . ' name="ticket_fields[' . $field_key . '][]" class="field_' . $field_key . '" id="field_' . $field_key . '_' . $i . '" value="' . esc_attr( $option ) . '"' . $disabled . ' /> ' . $option . '</label> ' . "\n";
				++$i;
			}
		break;
	}

	return $html;
}

/**
 * Escape a string to be used in a CSV context
 *
 * Malicious input can inject formulas into CSV files, opening up the possibility
 * for phishing attacks and disclosure of sensitive information.
 *
 * Additionally, Excel exposes the ability to launch arbitrary commands through
 * the DDE protocol.
 *
 * @see http://www.contextis.com/resources/blog/comma-separated-vulnerabilities/
 * @see https://hackerone.com/reports/72785
 *
 * @since 1.1.0
 *
 * @param string $field CSV field to escape
 *
 * @return string
 */
function wcbo_esc_csv( $field ) {
	$active_content_triggers = array( '=', '+', '-', '@' );

	if ( in_array( mb_substr( $field, 0, 1 ), $active_content_triggers, true ) ) {
		$field = "'" . $field;
	}

	return $field;
}

/**
 * Check if a ticket can be printed.
 *
 * In order for the ticket to be printable, the printing option must be enabled,
 * and the ticket must be in publish state.
 *
 * @param mixed $ticket  Ticket object.
 *
 * @return bool          Is ticket printable.
 */
function is_ticket_ready_for_printing( $ticket ) {
	// Check if printing is enabled for the ticket product.
	$ticket_printing_enabled = get_post_meta( $ticket->product_id, '_print_tickets', true ) === 'yes' ? true : false;
	// Check if order is in a state that allows printing the ticket.
	$is_status_allowed_for_printing = 'publish' === $ticket->status;

	return $ticket_printing_enabled && $is_status_allowed_for_printing;
}
