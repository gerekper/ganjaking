<?php

class FUE_Newsletter {

	const ACCESS_PRIVATE = 0;
	const ACCESS_PUBLIC  = 1;

	/**
	 * The number of subscribers found when searching with FUE_Newsletter::get_subscribers()
	 * @var int
	 */
	public $found_subscribers = 0;

	public function __construct() {}

	/**
	 * Get the site hash
	 * @return string
	 */
	public static function get_site_id() {
		$site_id = get_option( 'fue_newsletter_site_id', false );

		if ( !$site_id ) {
			$site_id = md5( uniqid() );
			update_option( 'fue_newsletter_site_id', $site_id );
		}

		return $site_id;
	}

	/**
	 * Get subscribers in the given list.
	 * @param array $args
	 * @return array
	 */
	public function get_subscribers( $args = array() ) {
		$wpdb = Follow_Up_Emails::instance()->wpdb;

		$defaults = array(
			'search'    => '',
			'list'      => false,
			'length'    => -1,
			'page'      => 1,
			'orderby'   => 'date_added',
			'order'     => 'DESC'
		);
		$args = wp_parse_args( $args, $defaults );

		$params     = array();
		$limit_str  = "";

		if ( $args['length'] > 0 ) {
			$start = ( $args['page'] * $args['length'] ) - $args['length'];
			$limit_str = "LIMIT $start, {$args['length']}";
		}

		if ( $args['list'] !== false ) {
			$list_id = $args['list'];

			// make sure we are working with the list ID instead of the name
			if ( !is_int( $args['list'] ) ) {
				$list_id = $wpdb->get_var($wpdb->prepare(
					"SELECT id
					FROM {$wpdb->prefix}followup_subscriber_lists
					WHERE list_name = %s",
					$args['list']
				));
			}

			if ( empty( $args['list'] ) ) {
				$sql = "SELECT SQL_CALC_FOUND_ROWS DISTINCT id
						FROM {$wpdb->prefix}followup_subscribers s
						WHERE NOT EXISTS(
							SELECT *
							FROM {$wpdb->prefix}followup_subscribers_to_lists s2l
							WHERE s.id = s2l.subscriber_id
						)";
			} else {
				$sql = "SELECT SQL_CALC_FOUND_ROWS DISTINCT id
						FROM {$wpdb->prefix}followup_subscribers s, {$wpdb->prefix}followup_subscribers_to_lists s2l
						WHERE s.id = s2l.subscriber_id
						AND s2l.list_id = %d";
				$params[] = $list_id;
			}
		} else {
			$sql = "SELECT SQL_CALC_FOUND_ROWS DISTINCT id
					FROM {$wpdb->prefix}followup_subscribers
					WHERE 1=1";
		}

		if ( !empty( $args['search'] ) ) {
			$sql .= " AND email LIKE %s";
			$params[] = '%'. $args['search'] .'%';
		}

		$sql .= " ORDER BY {$args['orderby']} {$args['order']} {$limit_str}";

		if ( !empty( $params ) ) {
			$sql = $wpdb->prepare( $sql, $params );
		}

		$subscribers = $wpdb->get_col( $sql );

		$this->found_subscribers = $wpdb->get_var("SELECT FOUND_ROWS()");

		if ( $subscribers ) {
			foreach ( $subscribers as $idx => $subscriber_id ) {
				$subscribers[ $idx ] = $this->get_subscriber( $subscriber_id );
			}
		}

		return $subscribers;
	}

	/**
	 * Get a subscriber using the ID or email
	 * @param int|string $term
	 * @return array
	 */
	public function get_subscriber( $term ) {
		$wpdb = Follow_Up_Emails::instance()->wpdb;

		if ( is_numeric( $term ) ) {
			$row = $wpdb->get_row( $wpdb->prepare( "SELECT id, email, first_name, last_name, date_added FROM {$wpdb->prefix}followup_subscribers WHERE id = %d", $term ), ARRAY_A );
		} else {
			$row = $wpdb->get_row( $wpdb->prepare( "SELECT id, email, first_name, last_name, date_added FROM {$wpdb->prefix}followup_subscribers WHERE email = %s", $term ), ARRAY_A );
		}

		if ( $row ) {
			$row['lists'] = $wpdb->get_results($wpdb->prepare(
				"SELECT l.id, l.list_name AS name
				FROM {$wpdb->prefix}followup_subscriber_lists l, {$wpdb->prefix}followup_subscribers_to_lists s2l
				WHERE l.id = s2l.list_id
				AND s2l.subscriber_id = %d",
				$row['id']
			), ARRAY_A);
		}

		return $row;
	}

	/**
	 * Add a subscriber, new or existing, to a specific list
	 *
	 * @param string $email
	 * @param string|array $lists
	 *
	 * @return int|WP_Error
	 */
	public function add_subscriber( $email, $lists = '' ) {
		_deprecated_function( 'FUE_Newsletter::add_subscriber', '4.6.0', 'FUE_Newsletter::add_subscriber_to_list' );

		return self::add_subscriber_to_list( $lists, array(
			'email' => $email,
		) );
	}

	/**
	 * Add a subscriber, new or existing, to a specific list
	 *
	 * @param string|array $lists Comma separated string of list names/ids, or array of list names/ids
	 * @param array        $args  Arguments
	 *
	 * @return int|WP_Error
	 */
	public function add_subscriber_to_list( $lists, $args ) {
		$email = sanitize_email( ! empty( $args['email'] ) ? $args['email'] : '' );

		if ( ! is_email( $email ) ) {
			return new WP_Error( 'fue_add_subscriber', __( 'Please enter a valid email address', 'follow_up_emails' ) );
		}

		$wpdb = Follow_Up_Emails::instance()->wpdb;

		$subscriber = $this->get_subscriber( $email );

		if ( ! $subscriber ) {
			$insert = apply_filters( 'fue_insert_subscriber', array(
				'email'         => $email,
				'first_name'    => ! empty( $args['first_name'] ) ? $args['first_name'] : '',
				'last_name'     => ! empty( $args['last_name'] ) ? $args['last_name'] : '',
				'date_added'    => current_time( 'mysql' )
			) );

			$wpdb->insert( $wpdb->prefix .'followup_subscribers', $insert );

			$subscriber = $this->get_subscriber( $wpdb->insert_id );
		}

		if ( ! empty( $lists ) ) {
			if ( ! is_array( $lists ) ) {
				if ( strpos( $lists, ',' ) !== false ) {
					$lists = array_filter( explode( ',', $lists ) );
				} else {
					$lists = array( $lists );
				}
			}

			$lists = apply_filters( 'fue_insert_subscriber_lists', $lists );

			foreach ( $lists as $list ) {
				$this->add_to_list( $subscriber['id'], $list );
			}
		}

		do_action( 'fue_newsletter_added_subscriber', $subscriber['id'], $lists );

		return $subscriber['id'];
	}

	/**
	 * Delete a subscriber from the system
	 *
	 * @param mixed $term ID or email address of the subscriber
	 */
	public function remove_subscriber( $term ) {
		$wpdb = Follow_Up_Emails::instance()->wpdb;
		$id   = $term;

		if ( is_email( $term ) ) {
			$subscriber = $this->get_subscriber( $term );
			$id = $subscriber['id'];
		}

		do_action( 'fue_before_delete_subscriber', $id );

		$this->remove_from_list( $id );

		$wpdb->query($wpdb->prepare(
			"DELETE FROM {$wpdb->prefix}followup_subscribers
			WHERE id = %d",
			$id
		));

		do_action( 'fue_deleted_subscriber', $id );

	}

	public function remove_from_list( $subscriber, $list = array() ) {
		$wpdb = Follow_Up_Emails::instance()->wpdb;

		if ( is_email( $subscriber ) ) {
			$subscriber = $this->get_subscriber( $subscriber );
			$subscriber_id = $subscriber['id'];
		} else {
			$subscriber_id = $subscriber;
		}

		if ( empty( $list ) ) {
			// remove from all the lists
			$wpdb->query($wpdb->prepare(
				"DELETE FROM {$wpdb->prefix}followup_subscribers_to_lists
				WHERE subscriber_id = %d",
				$subscriber_id
			));
		} else {
			$lists = implode( ',', array_map( 'esc_sql', $list ) );
			$wpdb->query($wpdb->prepare(
				"DELETE FROM {$wpdb->prefix}followup_subscribers_to_lists
				WHERE subscriber_id = %d
				AND list_id IN ($lists)",
				$subscriber_id
			));
		}
	}

	public function subscriber_exists( $email ) {
		$wpdb = Follow_Up_Emails::instance()->wpdb;

		$count = $wpdb->get_var( $wpdb->prepare(
			"SELECT COUNT(*)
			FROM {$wpdb->prefix}followup_subscribers
			WHERE email = %s",
			sanitize_email( $email )
		) );

		$exists = ($count > 0);

		return apply_filters( 'fue_subscriber_exists', $exists, $email );
	}

	/**
	 * Get all the lists available
	 * @return array
	 */
	public function get_lists() {
		$wpdb = Follow_Up_Emails::instance()->wpdb;

		$lists = $wpdb->get_results("SELECT DISTINCT * FROM {$wpdb->prefix}followup_subscriber_lists", ARRAY_A);

		return apply_filters( 'fue_newsletter_lists', $lists );
	}

	/**
	 * Get all public lists
	 * @return array
	 */
	public function get_public_lists() {
		$wpdb  = Follow_Up_Emails::instance()->wpdb;
		$lists = $wpdb->get_results("SELECT DISTINCT * FROM {$wpdb->prefix}followup_subscriber_lists WHERE access = ". self::ACCESS_PUBLIC, ARRAY_A);
		return apply_filters( 'fue_newsletter_public_lists', $lists );
	}

	/**
	 * Add a new list
	 *
	 * @param string $list
	 * @return int The ID of the new list
	 */
	public function add_list( $list ) {
		$wpdb = Follow_Up_Emails::instance()->wpdb;

		$list_id = $wpdb->get_var($wpdb->prepare(
			"SELECT id
			FROM {$wpdb->prefix}followup_subscriber_lists
			WHERE list_name = %s",
			$list
		));

		if ( !$list_id ) {
			$wpdb->insert( $wpdb->prefix . 'followup_subscriber_lists', array('list_name' => $list) );
			$list_id = $wpdb->insert_id;

			do_action( 'fue_newsletter_list_created', $list_id, $list );
		}

		return $list_id;
	}

	/**
	 * Update an existing list
	 *
	 * @param int       $id ID of the list to update
	 * @param string    $name The new name of the list. Leave empty if not changing the name
	 * @param int       $access Access to the list - 0: private; 1: public. Leave empty to skip
	 * @return void
	 */
	public function edit_list( $id, $name = null, $access = null ) {
		$wpdb = Follow_Up_Emails::instance()->wpdb;
		if ( is_null( $name ) && is_null( $access ) ) {
			return;
		}
		$updates = array();
		if ( !is_null( $name ) ) {
			$updates['list_name'] = $name;
		}
		if ( !is_null( $access ) ) {
			$updates['access'] = $access;
		}
		$wpdb->update( $wpdb->prefix .'followup_subscriber_lists', $updates, array('id' => $id) );
	}

	/**
	 * Update an existing subscriber.
	 *
	 * @param int       $id   ID of the list to update.
	 * @param array     $data Data to update the DB to.
	 * @return void
	 */
	public function edit_subscriber( $id, $data = array() ) {
		$wpdb = Follow_Up_Emails::instance()->wpdb;
		$updates = array();

		if ( ! empty( $data['email'] ) ) {
			if ( ! is_email( $data['email'] ) ) {
				return;
			}

			$data['email'] = sanitize_email( $data['email'] );

			$existing_subscriber = $this->get_subscriber( $data['email'] );

			if ( $existing_subscriber && $existing_subscriber['id'] !== $subscriber['id'] ) {
				return;
			}
		}

		foreach ( array( 'email', 'first_name', 'last_name' ) as $field ) {
			if ( ! empty( $data[ $field ] ) ) {
				$update_args[ $field ] = $data[ $field ];
			}
		}

		$wpdb->update( $wpdb->prefix . 'followup_subscribers', $update_args, array( 'id' => $id ) );

		if ( isset( $data['lists'] ) ) {
			$data['lists'] = array_filter( array_map( 'trim', explode( ',', $data['lists'] ) ) );

			// remove from all lists
			$lists = array();
			foreach ( $subscriber['lists'] as $list ) {
				$lists[] = $list['id'];
			}

			$this->remove_from_list( $id, $lists );

			if ( ! empty( $data['lists'] ) ) {
				foreach ( $data['lists'] as $list ) {
					$this->add_to_list( $id, $list );
				}
			}
		}

		do_action( 'fue_edited_subscriber', $id, $data );
	}

	/**
	 * Delete a list
	 * @param int $id
	 * @return void
	 */
	public function remove_list( $id ) {
		$wpdb = Follow_Up_Emails::instance()->wpdb;
		$wpdb->query( $wpdb->prepare("DELETE FROM {$wpdb->prefix}followup_subscribers_to_lists WHERE list_id = %d", $id) );
		$wpdb->query( $wpdb->prepare("DELETE FROM {$wpdb->prefix}followup_subscriber_lists WHERE id = %d", $id) );
	}

	/**
	 * Add a subscriber to a list
	 *
	 * @param int   $subscriber Subscriber ID
	 * @param mixed $list       List name or ID
	 */
	public function add_to_list( $subscriber_id, $list ) {
		$wpdb = Follow_Up_Emails::instance()->wpdb;

		if ( empty( $list ) ) {
			return;
		}

		if ( !is_numeric( $list ) ) {
			$list_id = $this->add_list( $list );
		} else {
			$list_id = $list;
		}

		if ( !$this->in_list( $list_id, $subscriber_id ) ) {
			$wpdb->insert(
				$wpdb->prefix .'followup_subscribers_to_lists',
				array(
					'subscriber_id' => $subscriber_id,
					'list_id'       => $list_id
				)
			);

			do_action( 'fue_subscriber_added_to_list', $subscriber_id, $list_id );
		}

	}

	/**
	 * Check if the $subscriber is in the provided $list
	 *
	 * @param mixed $list
	 * @param mixed $subscriber
	 * @return bool
	 */
	public function in_list( $list, $subscriber ) {
		$wpdb = Follow_Up_Emails::instance()->wpdb;

		if ( !is_numeric( $list ) ) {
			$list_id = $wpdb->get_var($wpdb->prepare(
				"SELECT id
				FROM {$wpdb->prefix}followup_subscriber_lists
				WHERE list_name = %s",
				$list
			));
		} else {
			$list_id = $list;
		}

		if ( !is_numeric( $subscriber ) ) {
			$subscriber = $this->get_subscriber( $subscriber );
			$subscriber_id = $subscriber['id'];
		} else {
			$subscriber_id = $subscriber;
		}

		$check = $wpdb->get_var($wpdb->prepare(
			"SELECT COUNT(*)
			FROM {$wpdb->prefix}followup_subscribers_to_lists
			WHERE subscriber_id = %d
			AND list_id = %d",
			$subscriber_id,
			$list_id
		));

		if ( $check > 0 ) {
			return true;
		}

		return false;
	}

	/**
	 * Get emails from the excluded/opt-out list
	 *
	 * @param array $args
	 * @return array
	 */
	public function get_excludes( $args = array() ) {
		$wpdb = Follow_Up_Emails::instance()->wpdb;

		$defaults = array(
			'search'    => '',
			'email_id'  => -1,
			'order_id'  => -1,
			'page'      => 0,
			'per_page'  => 20
		);
		$args = wp_parse_args( $args, $defaults );

		$params = array();
		$sql    = "SELECT SQL_CALC_FOUND_ROWS * FROM {$wpdb->prefix}followup_email_excludes WHERE 1=1";

		if ( !empty( $args['search'] ) ) {
			$sql .= " AND email LIKE %s";
			$params[] = '%'. $args['search'] .'%';
		}

		if ( $args['email_id'] > -1 ) {
			$sql .= " AND email_id = %d";
			$params[] = absint( $args['email_id'] );
		}

		if ( $args['order_id'] > -1 ) {
			$sql .= " AND order_id = %d";
			$params[] = absint( $args['order_id'] );
		}

		$start = ( $args['page'] * $args['per_page'] ) - $args['per_page'];

		$sql .= " ORDER BY email ASC LIMIT {$start}, {$args['per_page']}";

		if ( !empty( $params ) ) {
			$sql = $wpdb->prepare( $sql, $params );
		}

		return $wpdb->get_results( $sql );
	}

	/**
	 * Remove an email from the excludes table
	 *
	 * @param string $email
	 */
	public function remove_excluded_email( $email ) {
		$wpdb = Follow_Up_Emails::instance()->wpdb;

		$wpdb->query( $wpdb->prepare("DELETE FROM {$wpdb->prefix}followup_email_excludes WHERE email = %s", $email ) );
	}

}
