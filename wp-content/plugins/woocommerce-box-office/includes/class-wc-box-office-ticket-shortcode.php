<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class WC_Box_Office_Ticket_Shortcode {

	/**
	 * Notice when trying to retrieve link for viewing private content.
	 *
	 * @var string
	 */
	private $_notice_link_retrieval = '';

	/**
	 * Notice type when trying to retrieve link for viewing private content.
	 *
	 * @var string
	 */
	private $_notice_type_link_retrieval = '';

	/**
	 * Constructor.
	 */
	public function __construct() {
		// Add shortcodes.
		add_shortcode( 'my_ticket', array( $this, 'my_ticket' ) );
		add_shortcode( 'user_tickets', array( $this, 'user_ticket_list' ) );
		add_shortcode( 'order_tickets', array( $this, 'order_ticket_list' ) );
		add_shortcode( 'tickets', array( $this, 'display_tickets' ) );
		add_shortcode( 'scan_ticket', array( $this, 'ticket_scan_form' ) );
		add_shortcode( 'ticket_private_content', array( $this, 'private_content' ) );

		add_action( 'template_redirect', array( $this, 'maybe_send_private_content_link' ) );
	}

	/**
	 * Shortcode for my ticket.
	 *
	 * @since 1.1.0
	 *
	 * @param  array  $params Shortcode parameters
	 * @return string         HTML of edit ticket
	 */
	public function my_ticket( $params = array() ) {
		$params = shortcode_atts( array(
			'token' => ! empty( $_GET['token'] ) ? $_GET['token'] : ''
		), $params );

		$ticket = wc_box_office_get_ticket_by_token( $params['token'] );
		if ( empty( $ticket->fields ) ) {
			return __( 'No field defined for this product.', 'woocommerce-box-office' );
		}

		// Prepare form.
		$ticket_form = new WC_Box_Office_Ticket_Form(
			$ticket->product,
			wp_list_pluck( $ticket->fields, 'value' )
		);

		$description = sprintf(
			__( 'Your information for %1$s%2$s.', 'woocommerce-box-office' ),
			get_the_title( $ticket->product_id ),
			'pending' === $ticket->status ? ' &mdash; ' . __( 'Pending', 'woocommerce-box-office' ) : ''
		);

		$template_vars = apply_filters(
			'woocommerce_box_office_my_ticket_template_vars',
			array(
				'ticket_description' => $description,
				'ticket_form_params' => array(
					'ticket_id'            => $ticket->id,
					'ticket_form'          => $ticket_form,
					'print_ticket_enabled' => is_ticket_ready_for_printing( $ticket ),
					'print_ticket_url'     => wcbo_get_my_ticket_url( $ticket->id, true ),
					'editable'             => is_ticket_editable( $ticket->product ),
				),
			)
		);

		ob_start();
		wc_print_notices();
		wc_get_template( 'my-ticket.php', $template_vars, 'woocommerce-box-office', WCBO()->dir . 'templates/' );
		return ob_get_clean();

	}

	/**
	 * Shortcode for user ticket list.
	 *
	 * @param  array  $params Shortcode parameters
	 * @return string         HTML of user ticket list
	 */
	public function user_ticket_list( $params = array() ) {
		// Get shortcode parameters.
		extract( shortcode_atts( array(
			'user_id'       => get_current_user_id(),
			'amount'        => 'all',
			'fields_format' => 'flat',
			'title'         => __( 'My Tickets', 'woocommerce-box-office' ),
		), $params ) );

		$tickets = wc_box_office_get_tickets_by_user( $user_id, $amount );
		if ( 0 == count( $tickets ) ) {
			return;
		}

		ob_start();
		wc_get_template( 'ticket/user-tickets.php', array( 'title' => $title, 'tickets' => $tickets, 'fields_format' => $fields_format ), 'woocommerce-box-office', WCBO()->dir . 'templates/' );

		return ob_get_clean();
	}

	/**
	 * Shortcode for order ticket list.
	 *
	 * @param  array  $params Shortcode parameters
	 * @return string         HTML of order ticket list
	 */
	public function order_ticket_list( $params = array() ) {
		// Get shortcode parameters.
		extract( shortcode_atts( array(
			'order_id'      => 0,
			'amount'        => 'all',
			'fields_format' => 'flat',
		), $params ) );

		if ( ! $order_id ) {
			return;
		}

		$tickets = WCBO()->components->order->get_tickets_by_order( $order_id, $amount );
		if ( 0 == count( $tickets ) ) {
			return;
		}

		ob_start();
		wc_get_template( 'order/order-tickets.php', array( 'tickets' => $tickets, 'fields_format' => $fields_format ), 'woocommerce-box-office', WCBO()->dir . 'templates/' );

		return ob_get_clean();
	}

	/**
	 * Shortcode to display purchased tickets (attendees).
	 *
	 * @param  array  $params Shortcode paramaters
	 * @return string         HTML of ticket list
	 */
	public function display_tickets( $params = array() ) {
		// Get shortcode parameters.
		extract( shortcode_atts( array(
			'products' 		=> 0,
			'amount' 		=> 'all',
			'order' 		=> 'date',
			'avatar_size' 	=> 96,
			'columns'		=> 3,
		), $params ) );

		if ( 'all' === $amount ) {
			$amount = -1;
		}

		$args = array(
			'post_type' 		=> 'event_ticket',
			'post_status' 		=> 'publish',
			'posts_per_page' 	=> $amount,
		);

		if ( $products ) {
			$products_array     = explode( ',', $products );
			$args['meta_query'] = array(
				array(
					'key'     => '_product',
					'value'   => $products_array,
					'compare' => 'IN',
				),
			);
		}

		$args = apply_filters( 'woocommerce_box_office_display_tickets_query', $args );

		$tickets = get_posts( $args );

		$i = 0;

		$html = '<ul class="ticket-list columns-' . $columns . '">' . "\n";

		foreach ( $tickets as $ticket ) {
			$position = '';

			if ( 0 == $i % $columns ) {
				$position = 'first';
			} elseif ( 0 == ( $i + 1 ) % $columns ) {
				$position = 'last';
			}

			// Get ticket product ID
			$product_id = get_post_meta( $ticket->ID, '_product', true );

			// Get available fields from ticket product
			$ticket_fields = get_post_meta( $product_id, '_ticket_fields', true );

			$first_name 	= '';
			$last_name 		= '';
			$avatar 		= '';
			$url 			= '';
			$twitter 		= '';

			foreach ( $ticket_fields as $field_key => $field ) {

				$ticket_meta = get_post_meta( $ticket->ID, $field_key, true );

				switch ( $field['type'] ) {

					case 'first_name': $first_name = $ticket_meta; break;

					case 'last_name': $last_name = $ticket_meta; break;

					case 'email':
						if ( 'yes' === $field['email_gravatar'] ) {
							$avatar = get_avatar( $ticket_meta, $avatar_size );
						}
					break;

					case 'url':
						$url_link = esc_url( $ticket_meta );
						$url = str_replace( 'http://', '', $url_link );
						$url = str_replace( 'https://', '', $url );
						$url = str_replace( 'www.', '', $url );
						$url = trim( $url, '/' );
						$url = trim( $url, '.' );
					break;

					case 'twitter':
						$twitter = str_replace( 'http://', '', $ticket_meta );
						$twitter = str_replace( 'https://', '', $twitter );
						$twitter = str_replace( 'www.', '', $twitter );
						$twitter = str_replace( 'twitter.com', '', $twitter );
						$twitter = trim( $twitter, '/' );
						$twitter = trim( $twitter, '.' );
						$twitter = str_replace( '@', '', $twitter );
					break;
				}
			}

			$full_name = '';
			if ( $first_name ) {
				$full_name = $first_name;
			}

			if ( $last_name ) {
				if ( $full_name ) {
					$full_name .= ' ';
				}
				$full_name .= $last_name;
			}

			$html .= '<li class="' . $position . '">' . "\n";

			if ( $avatar ) {
				$html .= '<div>' . $avatar . '</div>' . "\n";
			}

			$html .= '<div>' . "\n";

			if ( $full_name ) {
				$html .= $full_name . '<br/>' . "\n";
			}

			if ( $url ) {
				$html .= '<a href="' . $url_link . '">' . $url . '</a><br/>' . "\n";
			}

			if ( $twitter ) {
				$html .= '<a href="https://twitter.com/' . $twitter . '">@' . $twitter . '</a><br/>' . "\n";
			}

			$html .= '</div>' . "\n";

			$html .= '</li>' . "\n";

			$i++;
		}

		$html .= '</ul>' . "\n";

		return $html;
	}

	/**
	 * Form for scanning barcodes.
	 *
	 * @param  array  $params Shortcode parameters
	 * @return string         Form markup
	 */
	public function ticket_scan_form( $params = array() ) {
		if ( ! function_exists( 'WC_Order_Barcodes' ) ) {
			WCBO()->components->logger->log_debug( 'WooCommerce Order Barcodes is not active when trying to render [scan_ticket] barcode' );
			return;
		}

		// Check if user has ticket scanning permissions
		$can_scan = apply_filters( 'woocommerce_box_office_scan_permission', current_user_can( 'manage_woocommerce' ), 0 );
		if ( ! $can_scan ) {
			return;
		}

		// Get shortcode parameters
		extract( shortcode_atts( array(
			'action' => '',
		), $params ) );

		WCBO()->components->assets->enqueue_scripts( true );
		WC_Order_Barcodes()->load_onscan_js();

		ob_start();
		wc_get_template( 'ticket/scan.php', array( 'action' => $action ), 'woocommerce-box-office', WCBO()->dir . 'templates/' );

		return ob_get_clean();
	}

	/**
	 * Lock post content with [ticket_private_content][/ticket_private_content].
	 *
	 * If token is not passed in $_GET then user will be prompted with notice
	 * and email form to retrieve link (with token in query string) to unlock
	 * content.
	 *
	 * @param array  $atts    Shortcode attributes
	 * @param string $content Content in [ticket_private_content][/ticket_private_content]
	 *
	 * @return string Private content
	 */
	public function private_content( $atts, $content ) {
		$atts = shortcode_atts(
			array(
				'product_id' => '',
			),
			$atts
		);

		// product_id is required in the shortcode.
		if ( empty( $atts['product_id'] ) ) {
			return  '';
		}

		$content_vars = array(
			'show_content'    => false,
			'content'         => $content,
			'show_email_form' => true,
			'email'           => ! empty( $_POST['ticket_email'] ) ? $_POST['ticket_email'] : '',
			'notice'          => '',
			'notice_type'     => '',
			'product_id'      => $atts['product_id'],
		);

		try {
			if ( ! empty( $this->_notice_link_retrieval ) && ! empty( $this->_notice_type_link_retrieval ) ) {
				throw new Exception( $this->_notice_link_retrieval, $this->_notice_type_link_retrieval );
			}

			if ( empty( $_GET['token'] ) ) {
				throw new Exception( __( 'The content on this page is private.', 'woocommerce-box-office' ), 400 );
			}

			$ticket = wc_box_office_get_ticket_by_token( $_GET['token'] );
			if ( ! $ticket ) {
				throw new Exception( __( 'Invalid ticket token.', 'woocommerce-box-office' ), 400 );
			}

			if ( $content_vars['product_id'] !== $ticket->product_id ) {
				throw new Exception( __( 'Sorry, but your ticket does not allow you to view this content.', 'woocommerce-box-office' ), 400 );
			}

			// Last call for other plugins to alter access for private content.
			$show_content = apply_filters( 'woocommerce_box_office_show_private_content', true, $ticket, $atts, $content );
			if ( ! $show_content ) {
				throw new Exception( __( 'Sorry, but your ticket does not allow you to view this content.', 'woocommerce-box-office' ), 400 );
			}

			$content_vars['show_content']    = $show_content;
			$content_vars['show_email_form'] = false;
		} catch ( Exception $e ) {
			$content_vars['notice']      = $e->getMessage();
			$content_vars['notice_type'] = 200 === $e->getCode() ? 'success' : 'error';
		}

		return $this->_get_private_content( $content_vars );
	}

	/**
	 * Maybe send link to ticket's email to unlock private content. Link is URL
	 * of the page with ticket's token appended in query string.
	 *
	 * @return void
	 */
	public function maybe_send_private_content_link() {
		if ( empty( $_POST['ticket_send_link_for_private_content'] ) && empty( $_POST['private_content_id'] ) ) {
			return;
		}

		$private_content_id = absint( $_POST['private_content_id'] );
		if ( ! $private_content_id ) {
			return;
		}

		$email = ! empty( $_POST['ticket_email'] ) ? $_POST['ticket_email'] : '';
		if ( ! is_email( $email ) ) {
			$this->_notice_type_link_retrieval = 400;
			$this->_notice_link_retrieval = __( 'The e-mail address you have entered does not seem to be valid.', 'woocommerce-box-office' );
			return;
		}

		$product_id = ! empty( $_POST['ticket_product_id'] ) ? $_POST['ticket_product_id'] : '';

		// Get ticket by email.
		$ticket = wc_box_office_get_ticket_by_email( $email, $product_id );
		if ( ! $ticket ) {
			$this->_notice_type_link_retrieval = 400;
			$this->_notice_link_retrieval = __( 'No attendee matches with your email.', 'woocommerce-box-office' );
			return;
		}

		// Ticket matches with the email, send the email.
		WCBO()->components->cron->schedule_send_email_for_private_content_link( time(), $email, $ticket->id, $private_content_id );

		$this->_notice_type_link_retrieval = 200;
		$this->_notice_link_retrieval = sprintf( __( 'URL to view this content is already sent to %s', 'woocommerce-box-office' ), $email );
	}

	/**
	 * Get private content based on given content vars for the template.
	 *
	 * @param array $content_vars Variables for the template
	 *
	 * @return string Private content
	 */
	private function _get_private_content( $content_vars ) {
		return wc_get_template_html( 'ticket/private.php', $content_vars, 'woocommerce-box-office', WCBO()->dir . 'templates/' );
	}
}
