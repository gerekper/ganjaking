<?php

/**
 * FUE_Email Class
 *
 * Class that represents a Follow-Up Email
 */

/**
 * Class FUE_Email
 *
 * @property $product_id
 * @property $category_id
 */
class FUE_Email {

	/**
	 * FUE_Email status constants.
	 */
	const STATUS_ARCHIVED = 'fue-archived';
	const STATUS_INACTIVE = 'fue-inactive';
	const STATUS_ACTIVE   = 'fue-active';

	/**
	 * @var int The Email ID.
	 */
	public $id;

	/**
	 * @var object The post object.
	 */
	public $post;

	/**
	 * @var string The email's type (storewide, subscription, etc).
	 */
	public $type = null;

	/**
	 * @var string The email's status.
	 */
	public $status = null;

	/**
	 * @var string The email's name.
	 */
	public $name;

	/**
	 * @var string The email's subject (mapped from post_excerpt).
	 */
	public $subject;

	/**
	 * @var string The email's content (mapped from post_content).
	 */
	public $message;

	/**
	 * @var int The priority.
	 */
	public $priority;

	/**
	 * @var string The template file to use.
	 */
	public $template;

	/**
	 * @var int The interval, used with $duration, before the action fires.
	 */
	public $interval;

	/**
	 * @var string The duration of the interval.
	 */
	public $duration;

	/**
	 * @var string Alias of interval_type.
	 */
	public $trigger;


	/**
	 * Get the Follow-Up Email if the id is provided. Otherwise, return a blank Email.
	 *
	 * @param int $email_id Optional ID of the follow-up email to load.
	 */
	public function __construct( $email_id = 0 ) {
		if ( ! empty( $email_id ) )
			$this->init( $email_id );
	}

	/**
	 * Load and initialize the FUE_Email object.
	 *
	 * @param int $email_id
	 */
	protected function init( $email_id ) {
		$this->id   = absint( $email_id );
		$this->post = get_post( $this->id );

		$this->populate();
	}

	/**
	 * Load the meta for this email and store them as class variables.
	 */
	private function populate() {
		$this->get_type();

		$custom = get_post_custom( $this->id );
		foreach ( $custom as $key => $value ) {
			$key = ltrim( $key, '_' );
			$this->$key = maybe_unserialize( $value[0] );
		}

		if ( $this->post ) {
			$this->status   = $this->post->post_status;
			$this->name     = $this->post->post_title;
			$this->subject  = $this->post->post_excerpt;
			$this->message  = $this->post->post_content;
			$this->priority = $this->post->menu_order;
		}

		$this->interval = get_post_meta( $this->id, '_interval_num', true );
		$this->duration = get_post_meta( $this->id, '_interval_duration', true );
		$this->trigger  = get_post_meta( $this->id, '_interval_type', true );

		do_action( 'fue_email_loaded', $this );
	}

	/**
	 * __isset function.
	 *
	 * @param mixed $key
	 * @return bool
	 */
	public function __isset( $key ) {
		return metadata_exists( 'post', $this->id, '_' . $key );
	}

	/**
	 * __get function.
	 *
	 * @param string $key
	 * @return mixed
	 */
	public function __get( $key ) {
		if ( 'type' === $key && is_null( $this->type ) ) {
			$this->get_type();
		}

		if ( property_exists( $this, $key ) ) {
			return $this->$key;
		}

		if ( 'usage_count' === $key ) {
			$value = get_post_meta( $this->id, '_usage_count', true );

			if ( ! $value ) {
				$value = 0;
			}
		} else {
			$value = get_post_meta( $this->id, '_' . $key, true );
		}

		return $value;
	}

	/**
	 * Get the preview URL
	 * @return string
	 */
	public function get_preview_url() {
		$url = add_query_arg( array(
			'fue-preview'   => 1,
			'email'         => $this->id,
			'key'           => md5( $this->post->post_title ),
		), get_bloginfo( 'url' ) );

		return apply_filters( 'fue_email_preview_url', $url );
	}

	/**
	 * Get the email type
	 *
	 * @return string
	 */
	public function get_type() {
		if ( is_null( $this->type ) ) {
			$terms      = get_the_terms( $this->id, 'follow_up_email_type' );
			$this->type = ! empty( $terms ) && isset( current( $terms )->name ) ? sanitize_title( current( $terms )->name ) : '';
		}

		// Convert generic to storewide, and merge product and normal emails to storewide.
		if ( 'generic' === $this->type || 'normal' === $this->type || 'product' === $this->type ) {
			// generic is obsolete. Use 'storewide' instead.
			wp_set_post_terms( $this->id, 'storewide', 'follow_up_email_type' );
			$this->type = 'storewide';
		}

		return $this->type;
	}

	/**
	 * Check if the current email type supports the provided feature
	 * @param string $feature
	 * @return bool
	 */
	public function supports( $feature ) {
		$supports = false;
		$email_type = $this->get_email_type();

		if ( isset( $email_type->supports ) && in_array( $feature, $email_type->supports ) ) {
			$supports = true;
		}

		return apply_filters( 'fue_email_supports', $supports, $feature );
	}

	/**
	 * Return a formatted string of this email's sending trigger
	 *
	 * @return string
	 */
	public function get_trigger_string() {

		if ( 'manual' === $this->type ) {
			$trigger = __( 'Single Email', 'follow_up_emails' );
		} else {
			if ( 'date' === $this->duration ) {
				$trigger = sprintf( __( 'Send on %s' ), fue_format_send_datetime( $this ) );
			} elseif ( 'signup' === $this->trigger ) {
				$trigger = sprintf(
					__( '%1$d %2$s after user signs up', 'follow_up_emails' ),
					$this->interval,
					Follow_Up_Emails::get_duration( $this->duration, $this->interval )
				);
			} else {
				$type = $this->get_email_type();

				if ( $type && $this->trigger ) {
					$trigger_name   = $type->get_trigger_name( $this->trigger );
					$trigger        = sprintf(
						__( '%1$d %2$s %3$s' ),
						$this->interval,
						Follow_Up_Emails::get_duration( $this->duration, $this->interval ),
						$trigger_name
					);
				} else {
					$trigger = sprintf( __( 'Error getting the trigger. %s is not a valid type', 'follow_up_emails' ), $this->get_type() );
				}
			}
		}

		// Support for older versions.
		$trigger = apply_filters( 'fue_interval_str', $trigger, $this );

		return apply_filters( 'fue_trigger_str', $trigger, $this );
	}

	/**
	 * Get the timestamp when the email will be sent that is relative to the current date/time.
	 * @param string $start_date The date to base the calculation on. Leave empty to use the current date.
	 *
	 * @return int
	 */
	public function get_send_timestamp( $start_date = null ) {
		$send_on = 0;
		if ( 'date' === $this->interval_type ) {
			$this->send_date_hour   = absint( $this->send_date_hour );
			$this->send_date_minute = absint( $this->send_date_minute );

			$send_on = strtotime( $this->send_date . ' ' . $this->send_date_hour . ':' . $this->send_date_minute );

			if ( false === $send_on ) {
				// Fallback to only using the date.
				$send_on = strtotime( $this->send_date );
			}
		} else {
			$add = FUE_Sending_Scheduler::get_time_to_add( $this->interval_num, $this->interval_duration );

			if ( ! is_null( $start_date ) ) {
				$time_from = strtotime( $start_date );
			} else {
				$time_from = current_time( 'timestamp' );
			}

			$send_on = $time_from + $add;
		}

		return apply_filters( 'fue_email_send_timestamp', $send_on, $this );
	}

	/**
	 * Return this emails FUE_Email_Type object.
	 *
	 * @return FUE_Email_Type
	 */
	public function get_email_type() {
		return Follow_Up_Emails::get_email_type( $this->get_type() );
	}

	/**
	 * Check and compare the email's type.
	 *
	 * @param mixed $type String or Array of Types to check against.
	 * @return bool
	 */
	public function is_type( $type ) {
		return ( $this->get_type() == $type || ( is_array( $type ) && in_array( $this->type, $type ) ) ) ? true : false;
	}

	/**
	 * Check if the FUE_Email exists.
	 *
	 * @return bool
	 */
	public function exists() {
		return empty( $this->post ) ? false : true;
	}

	/**
	 * Update the FUE_Email's status
	 *
	 * @param string $new_status
	 */
	public function update_status( $new_status ) {
		$args = array(
			'ID'          => $this->id,
			'post_status' => $new_status,
		);

		$old_status = $this->status;

		wp_update_post( $args );

		$this->status = $new_status;

		update_post_meta( $this->id, '_prev_status', $new_status );
		FUE_Followup_Logger::log( $this->id, sprintf( __( '<p>Email attributes updated:</p><p><strong>status</strong> from <em>%1$s</em> to <em>%2$s</em></p>', 'follow_up_emails' ), $old_status, $new_status ) );
	}

	/**
	 * Split the $message into sections and apply the sections to the template.
	 *
	 * @param string $message If passed, this will be used as the source of the sections.
	 * @return string The modified message with all the sections replaced
	 */
	public function apply_template( $message = null ) {
		if ( is_null( $message ) ) {
			$message = $this->message;
		}

		if ( empty( $this->template ) ) {
			// No template set. simply return the message.
			return $message;
		}

		$tpl        = new FUE_Email_Template( $this->template );
		$sections   = $tpl->get_sections();
		$contents   = $tpl->get_contents();

		if ( empty( $contents ) ) {
			// Nothing to process.
			return $message;
		}

		foreach ( $sections as $section ) {
			$section_body = fue_str_search( '{section:' . $section . '}', '{/section}', $message );

			if ( ! empty( $section_body ) ) {
				$value = $section_body[0];

				// No paragraphs for title
				if ( ! in_array( $section, array( 'title', 'subtitle', 'title_two', 'subtitle_two' ) ) ) {
					$value = wpautop( $value );
				}

				$contents = str_replace( '{section:' . $section . '}', $value, $contents );
			}
		}

		// Remove unused sections from $contents.
		foreach ( $sections as $section ) {
			$contents = str_replace( '{section:' . $section . '}', '', $contents );
		}

		return apply_filters( 'fue_email_apply_template', $contents, $this );
	}

	/**
	 * Check for existing follow-up emails that are similar to $email.
	 *
	 * Similar emails are classified as having the same duration, interval type,
	 * interval period, always send setting and email type but differ in the interval value.
	 *
	 * @return bool Returns true if a similar email is found
	 */
	public function has_similar_email() {
		$base_args = array(
			'post_type'   => Follow_Up_Emails::$post_type,
			'post_status' => 'any',
			'meta_query'  => array(
				'relation' => 'AND',
				array(
					'key'   => '_interval_duration',
					'value' => $this->interval_duration,
				),
				array(
					'key'   => '_interval_type',
					'value' => $this->interval_type,
				)
			),
		);

		// Exclude from search the email that is being checked against.
		if ( $this->id ) {
			$base_args['post__not_in'] = array( $this->id );
		}

		// Storewide
		if ( 'storewide' === $this->type ) {
			$search_args = $base_args;
			$search_args['tax_query'][] = array(
				'taxonomy'  => 'follow_up_email_type',
				'field'     => 'slug',
				'terms'     => $this->type,
			);

			$search_args['meta_query'][] = array(
				'key'   => '_always_send',
				'value' => $this->always_send,
			);

			// Duplicate search.
			$results = get_posts( $search_args );

			if ( count( $results ) > 0 ) {
				return true;
			}
		}

		// Always send emails.
		// Disregard the email type but look for an exact match against the other properties.
		if ( $this->always_send ) {
			$search_args = $base_args;

			// Force the always_send setting to 1.
			$search_args['meta_query'][] = array(
				'key'   => '_always_send',
				'value' => 1,
			);

			// Add the product and category ids in the search parameters.
			$search_args['meta_query'][] = array(
				'key'   => '_product_id',
				'value' => $this->product_id,
			);

			$search_args['meta_query'][] = array(
				'key'   => '_category_id',
				'value' => $this->category_id,
			);

			// Run search.
			$results = get_posts( $search_args );

			if ( count( $results ) > 0 ) {
				return true;
			}
		}

		// Product type.
		if ( $this->product_id > 0 ) {
			$search_args = $base_args;

			$search_args['tax_query'][] = array(
				'taxonomy' => 'follow_up_email_type',
				'field'    => 'slug',
				'terms'    => $this->type,
			);

			// Add the product id in the search parameters.
			$search_args['meta_query'][] = array(
				'key'   => '_product_id',
				'value' => $this->product_id,
			);

			// Run search.
			$results = get_posts( $search_args );

			if ( count( $results ) > 0 ) {
				return true;
			}
		} elseif ( $this->category_id > 0 ) {
			$search_args = $base_args;

			$search_args['tax_query'][] = array(
				'taxonomy' => 'follow_up_email_type',
				'field'    => 'slug',
				'terms'    => $this->type,
			);

			// Add the product id in the search parameters.
			$search_args['meta_query'][] = array(
				'key'   => '_category_id',
				'value' => $this->category_id,
			);

			// Run search.
			$results = get_posts( $search_args );

			if ( count( $results ) > 0 ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Check for existing follow-up emails that have the exact properties as $email.
	 *
	 * An email is considered to be a duplicate when the duration, interval type,
	 * interval period, always send setting, and email type are exactly the same.
	 *
	 * @return bool Returns true if duplicate is found
	 */
	public function has_duplicate_email() {
		$base_args = array(
			'post_type'     => Follow_Up_Emails::$post_type,
			'post_status'   => 'any',
			'meta_query'    => array(
				'relation'      => 'AND',
				array(
					'key'       => '_interval_num',
					'value'     => $this->interval_num,
				),
				array(
					'key'       => '_interval_duration',
					'value'     => $this->interval_duration,
				),
				array(
					'key'       => '_interval_type',
					'value'     => $this->interval_type,
				)
			),
		);

		// Exclude from search the email that is being checked against.
		if ( $this->id ) {
			$base_args['post__not_in'] = array( $this->id );
		}

		// Storewide.
		if ( 'storewide' === $this->type ) {
			$search_args = $base_args;
			$search_args['tax_query'][] = array(
				'taxonomy' => 'follow_up_email_type',
				'field'    => 'slug',
				'terms'    => $this->type,
			);

			$search_args['meta_query'][] = array(
				'key'   => '_always_send',
				'value' => $this->always_send,
			);

			// Duplicate search.
			$results = get_posts( $search_args );
			if ( count( $results ) > 0 ) {
				return true;
			}
		}

		// Always send emails.
		// Disregard the email type but look for an exact match against the other properties.
		if ( $this->always_send ) {
			$search_args = $base_args;

			// Force the always_send setting to 1.
			$search_args['meta_query'][] = array(
				'key'   => '_always_send',
				'value' => 1,
			);

			// Add the product and category ids in the search parameters.
			$search_args['meta_query'][] = array(
				'key'   => '_product_id',
				'value' => $this->product_id,
			);

			$search_args['meta_query'][] = array(
				'key'   => '_category_id',
				'value' => $this->category_id,
			);

			// Run search.
			$results = get_posts( $search_args );
			if ( count( $results ) > 0 ) {
				return true;
			}
		}

		// Product type.
		if ( $this->product_id > 0 ) {
			$search_args = $base_args;

			$search_args['tax_query'][] = array(
				'taxonomy' => 'follow_up_email_type',
				'field'    => 'slug',
				'terms'    => $this->type,
			);

			// Add the product id in the search parameters.
			$search_args['meta_query'][] = array(
				'key'   => '_product_id',
				'value' => $this->product_id,
			);

			// Run search.
			$results = get_posts( $search_args );
			if ( count( $results ) > 0 ) {
				return true;
			}
		} elseif ( $this->category_id > 0 ) {
			$search_args = $base_args;

			$search_args['tax_query'][] = array(
				array(
					'taxonomy' => 'follow_up_email_type',
					'field'    => 'slug',
					'terms'    => $this->type,
				),
			);

			// Add the product id in the search parameters.
			$search_args['meta_query'][] = array(
				'key'   => '_category_id',
				'value' => $this->category_id,
			);

			// Run search.
			$results = get_posts( $search_args );
			if ( count( $results ) > 0 ) {
				return true;
			}
		}

		return false;
	}
}
