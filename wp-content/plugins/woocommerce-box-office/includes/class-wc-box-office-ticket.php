<?php

/**
 * Class that represents a ticket.
 */
class WC_Box_Office_Ticket {

	/**
	 * Ticket's ID.
	 *
	 * @var int
	 */
	public $id;

	/**
	 * Post object of this ticket.
	 *
	 * @var WP_Post
	 */
	public $post;

	/**
	 * Ticket title.
	 *
	 * @var string
	 */
	public $title;

	/**
	 * Ticket status.
	 *
	 * @var string
	 */
	public $status;

	/**
	 * Ticket fields defined from product.
	 *
	 * @var array
	 */
	public $fields;

	/**
	 * Product ID.
	 *
	 * @var int
	 */
	public $product_id;

	/**
	 * Product in which this ticket is purchased from.
	 *
	 * @var WC_Product
	 */
	public $product;

	/**
	 * Order ID.
	 *
	 * @var int
	 */
	public $order_id;

	/**
	 * Order that creates this ticket.
	 *
	 * @var WC_Order
	 */
	public $order;

	/**
	 * Flag to indicate properties of instance has been populated or not.
	 *
	 * @var bool
	 */
	public $populated = false;

	/**
	 * Temporary order item data.
	 *
	 * @var array
	 */
	private $_order_item_data;

	/**
	 * Constructor.
	 *
	 * Instantiating this class may not populate the properties or create a post.
	 * If order data is passed and an ticket need to be created, invoke `create`
	 * method.
	 *
	 * @param mixed $ticket If array is passed, then it's assumed as order's data.
	 *                      Otherwise WP_Post or Post ID is expected.
	 */
	public function __construct( $ticket = false ) {
		if ( is_array( $ticket ) ) {
			$this->_order_item_data = $ticket;
		} else if ( is_int( $ticket ) || is_a( $ticket, 'WP_Post' ) ) {
			$this->populate( $ticket );
		}
	}

	/**
	 * Populate properties based on WP_Post.
	 *
	 * @param int|WP_Post $post  Post ID or object.
	 * @param bool        $force Force populate
	 *
	 * @return void
	 */
	public function populate( $ticket, $force = false ) {
		if ( ! $ticket ) {
			return;
		}

		$post = get_post( $ticket );
		if ( 'event_ticket' !== get_post_type( $post ) ) {
			return;
		}

		if ( $this->populated && ! $force ) {
			return;
		}

		$this->post       = $post;
		$this->id         = $post->ID;
		$this->title      = $post->post_title;
		$this->status     = $post->post_status;
		$this->order_id   = get_post_meta( $this->id, '_order', true );
		$this->order      = wc_get_order( $this->order_id );
		$this->product_id = get_post_meta( $this->id, '_product', true );
		$this->product    = wc_get_product( $this->product_id );

		$ticket_fields = wc_box_office_get_product_ticket_fields( $this->product_id );
		foreach ( $ticket_fields as $field_key => $field ) {
			$this->fields[ $field_key ] = array_merge(
				$field,
				array(
					'value' => get_post_meta( $this->id, $field_key, true )
				)
			);
		}

		$this->populated = true;
	}

	/**
	 * Create new ticket from an order item data.
	 *
	 * @param string $status Ticket's status
	 *
	 * @return mixed
	 */
	public function create( $status = 'publish' ) {
		global $wpdb;

		if ( empty( $this->_order_item_data ) ) {
			return false;
		}

		$data = wp_parse_args(
			$this->_order_item_data,
			array(
				'product_id'    => '',
				'variation_id'  => '',
				'variations'    => array(),
				'fields'        => array(),
				'order_item_id' => '',
				'customer_id'   => is_user_logged_in() ? get_current_user_id() : 0,
			)
		);

		if ( ! $data['product_id'] ) {
			return false;
		}

		$product = wc_get_product( $data['product_id'] );

		$title = $this->_maybe_create_title_variation( $product->get_title(), $data );

		// Get order ID from order item.
		if ( $data['order_item_id'] ) {
			$order_id = $wpdb->get_var( $wpdb->prepare( "SELECT order_id FROM {$wpdb->prefix}woocommerce_order_items WHERE order_item_id = %d", $data['order_item_id'] ) );
		} else {
			$order_id = 0;
		}

		$ticket_data = array(
			'post_type'    => 'event_ticket',
			'post_title'   => $title,
			'post_status'  => $status,
			'ping_status'  => 'closed',
			'post_excerpt' => '',
			'post_parent'  => $order_id
		);

		$ticket_id = wp_insert_post( $ticket_data );
		if ( ! $ticket_id ) {
			return $ticket_id;
		}

		// Add ticket meta data.
		$ticket_fields = wc_box_office_get_product_ticket_fields( $product->get_id() );
		$content_array = array();
		foreach ( $ticket_fields as $key => $field ) {
			if ( isset( $data['fields'][ $key ] ) ) {
				$content_array[ $key ] = $data['fields'][ $key ];
				$this->save_ticket_field( $ticket_id, $key, $data['fields'][ $key ], $field['type'] );
			}
		}

		if ( ! empty( $content_array ) ) {
			wp_update_post( array(
				'ID'           => $ticket_id,
				'post_content' => maybe_serialize( $content_array ),
			) );
		}

		update_post_meta( $ticket_id, '_token', $this->_generate_token( $ticket_id ) );

		update_post_meta( $ticket_id, '_product_id', $product->get_id() );

		update_post_meta( $ticket_id, '_customer_id', $data['customer_id'] );

		// @TODO(gedex) remove this and update all references to this meta to use _product_id instead.
		update_post_meta( $ticket_id, '_product', $product->get_id() );

		// @TODO(gedex) remove this and update all references to this meta to use post_parent instead.
		update_post_meta( $ticket_id, '_order', $order_id );

		update_post_meta( $ticket_id, '_ticket_order_item_id', $data['order_item_id'] );

		// @TODO(gedex) remove this and update all references to this meta to use _customer_id instead.
		update_post_meta( $ticket_id, '_user', $data['customer_id'] );

		do_action( 'woocommerce_box_office_event_ticket_created', $ticket_id );

		$this->id = $ticket_id;

		// Force populate after creation.
		$this->populate( $this->id, true );
	}

	/**
	 * Update the ticket.
	 *
	 * @throws Exception
	 *
	 * @param array $data Ticket fields data for the update
	 *
	 * @return void
	 */
	public function update( $data ) {
		if ( ! $this->populated ) {
			throw new Exception( __( 'Unknown ticket to update', 'woocommerce-box-office' ) );
		}

		$content_array = array();
		foreach ( $this->fields as $key => $field ) {
			if ( isset( $data[ $key ] ) ) {
				// Build serialized fields as post_content so it's searchable.
				$content_array[ $key ] = $data[ $key ];

				$this->save_ticket_field( $this->id, $key, $data[ $key ], $field['type'] );
			}
		}

		if ( ! empty( $content_array ) ) {
			$post_data = array(
				'ID'           => $this->id,
				'post_content' => maybe_serialize( $content_array ),
			);
			wp_update_post( $post_data );
		}

		// Force populate with the new update.
		$this->populate( $this->id, true );
	}

	/**
	 * Get printed content with label variables replaced with real values.
	 *
	 * @return string Printed ticket content
	 */
	public function get_printed_content() {
		// Load print content.
		$ticket_content = get_post_meta( $this->product_id, '_ticket_content', true );

		// Replace content with ticket field's values.
		foreach ( $this->fields as $field_key => $field ) {
			$val = $field['value'];
			if ( is_array( $val ) ) {
				$val = implode( ', ', $val );
			}
			$ticket_content = str_replace( '{' . $field['label'] . '}', $val, $ticket_content );
		}

		$post = get_post( $this->product_id );

		// Replace {post_title} and {post_content}.
		$post_vars = array(
			'{post_title}'   => $post->post_title,
			'{post_content}' => $post->post_content,
		);
		foreach ( $post_vars as $var => $value ) {
			$ticket_content = str_replace( $var, $value, $ticket_content );
		}

		/**
		 * Display ticket content with paragraph formatting and shortcodes
		 * Follows same steps as the_content filters
		 */
		$ticket_content = wpautop( $ticket_content );
		$ticket_content = shortcode_unautop( $ticket_content );
		$ticket_content = do_shortcode( $ticket_content );

		return apply_filters( 'woocommerce_box_office_get_printed_ticket_content', $ticket_content );
	}

	/**
	 * Get ticket fields by its type.
	 *
	 * @param string
	 */
	public function get_ticket_fields_by_type( $type ) {
		if ( ! $this->populated ) {
			return null;
		}

		$filtered_fields = array();
		foreach ( $this->fields as $key => $field ) {
			if ( ! empty( $field['type'] ) && $type === $field['type'] ) {
				$filtered_fields[ $key ] = $field;
			}
		}

		return $filtered_fields;
	}

	/**
	 * Maybe create title with its formatted variations.
	 *
	 * @param string $title Base title
	 * @param array  $data  Data
	 *
	 * @return string Title that might be formatted with variations
	 */
	private function _maybe_create_title_variation( $title, $data ) {
		// If this is a variation then add the variation attributes to the ticket title.
		if ( ! empty( $data['variations'] ) ) {
			$title = sprintf(
				__( '%1$s (%2$s)', 'woocommerce-box-office' ),
				$title,
				wc_get_formatted_variation( $data['variations'], true )
			);
		}

		return apply_filters( 'woocommerce_box_office_create_ticket_title', $title );
	}

	/**
	 * Save ticket field.
	 *
	 * @param integer $ticket_id Ticket ID
	 * @param string  $key       Field key
	 * @param string  $value     Submitted value
	 * @param string  $type      Field type
	 *
	 * @return void
	 */
	public function save_ticket_field( $ticket_id, $key, $value, $type = 'text' ) {
		if ( ! $ticket_id || ! $key ) {
			return;
		}

		// Validate field according to type.
		if ( $type ) {
			switch ( $type ) {
				case 'email':
					$value = sanitize_email( $value );
					break;

				case 'twitter':
					$value = str_replace( 'http://', '', $value );
					$value = str_replace( 'https://', '', $value );
					$value = str_replace( 'www.', '', $value );
					$value = str_replace( 'twitter.com', '', $value );
					$value = trim( $value, '/' );
					$value = trim( $value, '.' );
					$value = str_replace( '@', '', $value );
					break;

				case 'url':
					$value = esc_url( $value );
					break;

				case 'checkbox':
					$value = array_map( 'sanitize_text_field', $value );
					break;

				case 'text':
				default:
					$value = sanitize_text_field( $value );
			}
		}

		update_post_meta( $ticket_id, $key, $value );
	}

	/**
	 * Set ticket to an order and order item by ID.
	 *
	 * @param int $order_id      Order ID
	 * @param int $order_item_id Order item ID
	 *
	 * @return void
	 */
	public function set_order_item_id( $order_id, $order_item_id ) {
		$this->order_id = $order_id;
		wp_update_post( array( 'ID' => $this->id, 'post_parent' => $this->order_id ) );
		update_post_meta( $this->id, '_ticket_order_item_id', $order_item_id );
	}

	/**
	 * Generate token from a given ticket ID. Copied from wp_hash_password.
	 *
	 * @param int $ticket_id Ticket ID
	 */
	private function _generate_token( $ticket_id ) {
		global $wp_hasher;

		if ( empty($wp_hasher) ) {
			require_once( ABSPATH . WPINC . '/class-phpass.php');
			// By default, use the portable hash from phpass
			$wp_hasher = new PasswordHash(8, true);
		}

		return md5( $wp_hasher->HashPassword( 'woocommerce_box_office_ticket_' . $ticket_id . '_token' ) );
	}
}
