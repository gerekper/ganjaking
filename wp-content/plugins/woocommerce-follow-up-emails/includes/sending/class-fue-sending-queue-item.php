<?php

/**
 * Class FUE_Sending_Queue_Item
 */
class FUE_Sending_Queue_Item {

	const STATUS_DELETED    = -1; // unused
	const STATUS_SUSPENDED  = 0;
	const STATUS_ACTIVE     = 1;
	const STATUS_BOUNCED    = 2;

	/**
	 * @var int
	 */
	public $id = 0;

	/**
	 * @var int
	 */
	public $email_id = 0;

	/**
	 * @var int
	 */
	public $user_id = 0;

	/**
	 * @var string
	 */
	public $user_email = '';

	/**
	 * @var int
	 */
	public $order_id = 0;

	/**
	 * @var int
	 */
	public $product_id = 0;

	/**
	 * @var int
	 */
	public $send_on = '';

	/**
	 * @var int
	 */
	public $is_cart = 0;

	/**
	 * @var int
	 */
	public $is_sent = 0;

	/**
	 * @var string
	 */
	public $date_sent = '';

	/**
	 * @var string
	 */
	public $email_trigger = '';

	/**
	 * @var array
	 */
	public $meta = array();

	/**
	 * @var int
	 */
	public $status = 1;

	/**
	 * Class constructor. Load a queue item row based on the given $id
	 * @param int $id
	 */
	public function __construct( $id = null ) {
		if ( !is_null( $id ) ) {
			$this->populate( $id );
		}
	}

	public function populate( $id ) {
		$wpdb = Follow_Up_Emails::instance()->wpdb;

		$row = $wpdb->get_row( $wpdb->prepare(
			"SELECT *
			FROM {$wpdb->prefix}followup_email_orders
			WHERE id = %d",
			$id
		), ARRAY_A );

		if ( $row ) {
			foreach ( $row as $key => $value ) {

				if ( $key == 'meta' ) {
					$value = maybe_unserialize( $value );
					$value = maybe_unserialize( $value );
				}

				$this->$key = $value;
			}
		}
	}

	/**
	 * Check if the queue item exists in the database
	 * @return bool
	 */
	public function exists() {
		if ( !$this->id ) {
			return false;
		}

		$items = Follow_Up_Emails::instance()->scheduler->get_items( array(
			'id'    => $this->id
		) );

		if ( count( $items ) == 0 ) {
			return false;
		}

		return true;
	}

	/**
	 * Append a note to the queue item
	 * @param string $message
	 */
	public function add_note( $message ) {

		// do not add if queue item doesn't exist (deleted)
		if ( !$this->exists() ) {
			return;
		}

		$this->populate( $this->id );

		$this->meta = maybe_unserialize( $this->meta );

		$note = array(
			'date'      => current_time('mysql'),
			'message'   => $message
		);

		if ( ! is_array( $this->meta ) ) {
			$this->meta = array();
		}

		if ( !isset( $this->meta['notes'] ) || ! is_array( $this->meta ) ) {
			$this->meta['notes'] = array();
		}

		$this->meta['notes'][] = $note;

		// write the change to the DB
		$this->save();
	}

	/**
	 * Write the current values to the database
	 * @return int The ID of the queue item
	 */
	public function save() {
		$wpdb = Follow_Up_Emails::instance()->wpdb;

		$fields = get_object_vars( $this );
		$data   = array();
		$id     = 0;

		if ( $this->id ) {
			$id = $this->id;
		}

		foreach ( $fields as $field => $value ) {
			if ( $field == 'meta' ) {
				$data[ $field ] = maybe_serialize( $this->$field );
			} else {
				$data[ $field ] = $this->$field;
			}
		}

		if ( $id ) {
			// updating
			unset($data['id']);

			$wpdb->update(
				$wpdb->prefix .'followup_email_orders',
				$data,
				array('id' => $id)
			);
		} else {
			$wpdb->insert(
				$wpdb->prefix .'followup_email_orders',
				$data
			);

			$this->id = $wpdb->insert_id;
			$id = $this->id;
		}

		return $id;

	}

	public function get_user() {
		$first_name = '';
		$last_name  = '';

		if ( empty( $this->user_id ) ) {
			// attempt to get the user id from the order
			if ( !empty( $this->order_id ) ) {
				$this->user_id = get_post_meta( $this->order_id, '_customer_user', true );
			}

			// if the user_id is still empty, use the order's billing email
			if ( empty( $this->user_id ) && !empty( $this->order_id ) ) {
				$first_name = get_post_meta( $this->order_id, '_billing_first_name', true );
				$last_name  = get_post_meta( $this->order_id, '_billing_last_name', true );
				$email      = get_post_meta( $this->order_id, '_billing_email', true );
			} else {
				$email = $this->user_email;
			}

		} else {
			// look for the billing name
			$first_name = get_user_meta( $this->user_id, 'billing_first_name', true );
			$last_name  = get_user_meta( $this->user_id, 'billing_last_name', true );
			$email      = $this->user_email;
		}

		if ( $first_name && $last_name ) {
			$name = $first_name .' '. $last_name;

			if ( empty( $email ) && $this->user_id ) {
				$email = get_user_meta( $this->user_id, 'billing_email', true );
			}
		} else {
			// fallback to using the display name
			$user   = new WP_User( $this->user_id );
			$name   = $user->display_name;

			if ( empty( $email ) ) {
				$email  = $user->user_email;
			}

		}

		if ( $this->user_id ) {
			return sprintf(
				__('<a href="%s">#%d - %s &lt;%s&gt;', 'follow_up_emails'),
				get_edit_user_link( $this->user_id ),
				$this->user_id,
				$name,
				$email
			);
		} else {
			return sprintf(
				__('%s &lt;%s&gt;', 'follow_up_emails'),
				$name,
				$email
			);
		}
	}

	public function get_web_version_url() {
		$item_key = md5( $this->user_email .'.'. $this->email_id .'.'. $this->send_on );
		$url = add_query_arg( array('fue-web-version' => 1, 'email-id' => $this->id, 'key' => $item_key), home_url() );

		return apply_filters( 'fue_email_web_version_url', $url );
	}

}
