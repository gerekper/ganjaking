<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class WC_Box_Office_Product_Admin {

	/**
	 * Constructor.
	 */
	public function __construct() {
		// Product options.
		add_filter( 'product_type_options', array( $this, 'ticket_type_option' ) );
		add_action( 'woocommerce_product_data_tabs', array( $this, 'ticket_tab' ) );
		add_action( 'woocommerce_product_data_panels', array( $this, 'ticket_panel' ) );
		add_action( 'woocommerce_process_product_meta', array( $this, 'save_ticket_options' ), 1, 2 );
	}

	/**
	 * Add 'Ticket' option to products.
	 *
	 * @param  array  $options Default options
	 * @return array           Modified options
	 */
	public function ticket_type_option( $options = array() ) {
		$options['ticket'] = array(
			'id'            => '_ticket',
			'wrapper_class' => 'show_if_simple show_if_variable hide_if_deposit hide_if_subscription hide_if_variable-subscription hide_if_grouped hide_if_external',
			'label'         => __( 'Ticket', 'woocommerce-box-office' ),
			'description'   => __( 'Each ticket purchased will have attendee details added to it.', 'woocommerce-box-office' ),
			'default'       => 'no',
		);

		return $options;
	}

	/**
	 * Add 'Ticket Fields' tab.
	 *
	 * @param  array  $tabs Default tabs
	 * @return array        Modified tabs
	 */
	public function ticket_tab( $tabs = array() ) {
		$tabs['ticket'] = array(
			'label'  => __( 'Ticket Fields', 'woocommerce-box-office' ),
			'target' => 'ticket_field_data',
			'class'  => array( 'show_if_ticket' ),
		);

		$tabs['ticket-content'] = array(
			'label'  => __( 'Ticket Printing', 'woocommerce-box-office' ),
			'target' => 'ticket_content_data',
			'class'  => array( 'show_if_ticket' ),
		);

		$tabs['ticket-email'] = array(
			'label'  => __( 'Ticket Emails', 'woocommerce-box-office' ),
			'target' => 'ticket_email_data',
			'class'  => array( 'show_if_ticket' ),
		);

		return $tabs;
	}

	/**
	 * Add content to the 'Ticket Fields' write panel
	 * @return void
	 */
	public function ticket_panel() {
		global $post;

		require_once( WCBO()->dir . 'includes/views/admin/product-ticket-panel.php' );
	}

	/**
	 * Save ticket options.
	 *
	 * @param  integer $post_id Post ID
	 * @param  object  $post    Post object
	 * @return void
	 */
	public function save_ticket_options( $post_id, $post ) {

		$is_ticket = isset( $_POST['_ticket'] ) ? 'yes' : 'no';
		update_post_meta( $post_id, '_ticket', $is_ticket );

		$field_labels         = isset( $_POST['_ticket_field_labels'] ) ? array_map( 'wc_clean', $_POST['_ticket_field_labels'] ) : array();
		$field_types          = isset( $_POST['_ticket_field_types'] ) ? array_map( 'wc_clean', $_POST['_ticket_field_types'] ) : array();
		$field_options        = isset( $_POST['_ticket_field_options'] ) ? array_map( 'wc_clean', $_POST['_ticket_field_options'] ) : array();
		$field_autofill       = isset( $_POST['_ticket_field_autofill'] ) ? array_map( 'wc_clean', $_POST['_ticket_field_autofill'] ) : array();
		$field_email_contact  = isset( $_POST['_ticket_field_email_contact'] ) ? array_map( 'wc_clean', $_POST['_ticket_field_email_contact'] ) : array();
		$field_email_gravatar = isset( $_POST['_ticket_field_email_gravatar'] ) ? array_map( 'wc_clean', $_POST['_ticket_field_email_gravatar'] ) : array();
		$field_required       = isset( $_POST['_ticket_field_required'] ) ? array_map( 'wc_clean', $_POST['_ticket_field_required'] ) : array();

		$field_count = sizeof( $field_labels );

		$fields = array();
		for ( $i = 0; $i < $field_count; $i ++ ) {
			if ( ! empty( $field_labels[ $i ] ) ) {
				$key = md5( $field_labels[ $i ] . $field_types[ $i ] );
				$fields[ $key ] = array(
					'label'          => $field_labels[ $i ],
					'type'           => $field_types[ $i ],
					'options'        => $field_options[ $i ],
					'autofill'       => $field_autofill[ $i ],
					'email_contact'  => $field_email_contact[ $i ],
					'email_gravatar' => $field_email_gravatar[ $i ],
					'required'       => $field_required[ $i ],
				);
			}
		}

		update_post_meta( $post_id, '_ticket_fields', $fields );

		// Ticket printing options
		if ( isset( $_POST['_print_tickets'] ) ) {
			update_post_meta( $post_id, '_print_tickets', $_POST['_print_tickets'] );
		} else {
			delete_post_meta( $post_id, '_print_tickets' );
		}

		if ( isset( $_POST['_print_barcode'] ) ) {
			update_post_meta( $post_id, '_print_barcode', $_POST['_print_barcode'] );
		} else {
			delete_post_meta( $post_id, '_print_barcode' );
		}

		if ( isset( $_POST['ticket-content'] ) ) {
			update_post_meta( $post_id, '_ticket_content', $_POST['ticket-content'] );
		}

		// Ticket email options
		if ( isset( $_POST['_email_tickets'] ) ) {
			update_post_meta( $post_id, '_email_tickets', $_POST['_email_tickets'] );
		} else {
			delete_post_meta( $post_id, '_email_tickets' );
		}

		if ( isset( $_POST['_email_ticket_subject'] ) ) {
			update_post_meta( $post_id, '_email_ticket_subject', $_POST['_email_ticket_subject'] );
		}

		if ( isset( $_POST['ticket-email'] ) ) {
			update_post_meta( $post_id, '_ticket_email_html', $_POST['ticket-email'] );
		}

		if ( isset( $_POST['_ticket_email_plain'] ) ) {
			update_post_meta( $post_id, '_ticket_email_plain', $_POST['_ticket_email_plain'] );
		}
	}
}
