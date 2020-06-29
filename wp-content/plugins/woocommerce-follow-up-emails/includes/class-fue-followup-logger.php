<?php

/**
 * FUE_Followup_Logger Class.
 *
 * Class that logs all changes made to a Follow-Up Email.
 */

class FUE_Followup_Logger {

	public static function log( $followup_id, $message ) {
		global $wpdb;

		$user = wp_get_current_user();
		if ( ! $user ) {
			return;
		}

		$data = apply_filters( 'fue_followup_log_data', array(
			'followup_id' => $followup_id,
			'user_id'     => $user->display_name,
			'content'     => $message,
			'date_added'  => current_time( 'mysql' ),
		) );
		$wpdb->insert( $wpdb->prefix . 'followup_followup_history', $data );
	}

	public static function log_changes( $followup_id, $prev_followup ) {
		$new_followup   = new FUE_Email( $followup_id );
		$props      = get_object_vars( $new_followup );
		$log        = array();

		$excludes = apply_filters( 'fue_logger_excluded_props', array(
			'usage_count',
			'post',
			'edit_lock',
			'prev_name',
			'prev_subject',
			'prev_message',
			'prev_status',
			'prev_type',
		) );

		foreach ( $props as $prop => $value ) {
			if ( in_array( $prop, $excludes ) ) {
				continue;
			}

			if ( in_array( $prop, array( 'name', 'subject', 'message', 'status', 'type' ) ) ) {
				$prev_prop = 'prev_' . $prop;

				if ( isset( $prev_followup->$prev_prop ) ) {
					$prev_followup->$prop = $prev_followup->$prev_prop;
				}
			} elseif ( 'conditions' === $prop && ! empty( $value ) ) {
				$conditions = $value;
				$value      = '';
				foreach ( $conditions as $condition ) {
					foreach ( $condition as $key => $condition_value ) {
						if ( is_array( $condition_value ) ) {
							$condition_value = implode( ',', $condition_value );
						}
						$value .= $key . ': ' . $condition_value . '; ';
					}
				}

				if ( ! empty( $prev_followup->$prop ) ) {
					$prev_conditions = $prev_followup->$prop;
					$prev_followup->$prop = '';
					foreach ( $prev_conditions as $condition ) {
						foreach ( $condition as $key => $condition_value ) {
							if ( is_array( $condition_value ) ) {
								$condition_value = implode( ',', $condition_value );
							}
							$prev_followup->$prop .= $key . ': ' . $condition_value . '; ';
						}
					}
				}
			} elseif ( 'meta' === $prop ) {
				$meta         = $value;
				$value        = '';
				$old_meta     = $prev_followup->meta;
				$changed_meta = array();

				foreach ( $meta as $meta_key => $meta_value ) {
					if ( ! isset( $old_meta[ $meta_key ] ) ) {
						$changed_meta[ $meta_key ] = array( '', $meta_value );
					} elseif ( $old_meta[ $meta_key ] != $meta_value ) {
						$changed_meta[ $meta_key ] = array( $old_meta[ $meta_key ], $meta_value );
					}
				}

				foreach ( $changed_meta as $meta_key => $changes ) {
					$prop = 'meta[' . $meta_key . ']';
					$log[ $prop ] = array( $changes[0], $changes[1] );
				}

				continue;
			}

			if ( ! isset( $prev_followup->$prop ) ) {
				$log[ $prop ] = array( '', $new_followup->$prop );
			} elseif ( $prev_followup->$prop != $value ) {
				$log[ $prop ] = array( $prev_followup->$prop, $value );
			}
		}

		$log = apply_filters( 'fue_logger_log_props', $log, $followup_id, $prev_followup );
		if ( ! empty( $log ) ) {
			$message = __( 'Email attributes updated: %s', 'follow_up_emails' );
			$list = '';

			foreach ( $log as $property => $changes ) {
				if ( empty( $changes[0] ) ) {
					$changes[0] = '-';
				}

				if ( empty( $changes[1] ) ) {
					$changes[1] = '-';
				}

				if ( is_array( $changes[0] ) || is_object( $changes[0] ) ) {
					$changes[0] = serialize( $changes[0] );
				}

				if ( is_array( $changes[1] ) || is_object( $changes[1] ) ) {
					$changes[1] = serialize( $changes[1] );
				}

				$list .= '<div>
							<strong>' . $property . '</strong> from
							<p>
								<em>' . $changes[0] . '</em>
							</p> to
							<p>
								<em>' . $changes[1] . '</em>
							</p>
						</div>';
			}

			$message = sprintf( $message, $list );
			self::log( $followup_id, $message );
		}
	}

	/**
	 * Get logs from the followup_followup_history table.
	 *
	 * @param array $args
	 * @return array
	 */
	public static function get_logs( $args = array() ) {
		$wpdb = Follow_Up_Emails::instance()->wpdb;

		$default = array(
			'followup_id' => 0,
			'page'        => 0,
			'limit'       => 0,
		);
		$args   = wp_parse_args( $args, $default );
		$params = array();
		$sql    = "SELECT * FROM {$wpdb->prefix}followup_followup_history WHERE 1=1";

		if ( $args['followup_id'] ) {
			$sql .= " AND followup_id = %d";
			$params[] = $args['followup_id'];
		}

		$sql .= " ORDER BY date_added DESC";

		if ( $args['limit'] ) {
			$start  = 0;
			$limit  = absint( $args['limit'] );

			if ( $args['page'] ) {
				$start = ( $limit * $args['page'] ) - $limit;
			}

			$sql .= " LIMIT $start, $limit";
		}

		if ( ! empty( $params ) ) {
			$sql = $wpdb->prepare( $sql, $params );
		}

		return $wpdb->get_results( $sql );
	}

	/**
	 * Exclude follow-up emails history from queries and RSS
	 *
	 * @param array $clauses
	 * @return array
	 */
	public static function exclude_fue_comments( $clauses ) {
		global $wpdb, $typenow;

		$tab = ( empty( $_GET['tab'] ) ) ? '' : sanitize_text_field( wp_unslash( $_GET['tab'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended

		if ( is_admin() && 'history' === $tab ) {
			return $clauses; // Don't hide when viewing email history in admin.
		}

		if ( ! $clauses['join'] ) {
			$clauses['join'] = '';
		}

		if ( ! strstr( $clauses['join'], "JOIN $wpdb->posts" ) ) {
			$clauses['join'] .= " LEFT JOIN $wpdb->posts ON comment_post_ID = $wpdb->posts.ID ";
		}

		if ( $clauses['where'] ) {
			$clauses['where'] .= ' AND ';
		}

		$clauses['where'] .= " $wpdb->posts.post_type <> 'follow_up_email' ";

		return $clauses;
	}

	/**
	 * Exclude fue comments from queries and RSS.
	 *
	 * @param string $join
	 * @return string
	 */
	public static function exclude_fue_comments_from_feed_join( $join ) {
		global $wpdb;

		if ( ! strstr( $join, $wpdb->posts ) ) {
			$join = " LEFT JOIN $wpdb->posts ON $wpdb->comments.comment_post_ID = $wpdb->posts.ID ";
		}

		return $join;
	}

	/**
	 * Exclude fue comments from queries and RSS.
	 *
	 * @param string $where
	 * @return string
	 */
	public static function exclude_fue_comments_from_feed_where( $where ) {
		global $wpdb;

		if ( $where ) {
			$where .= ' AND ';
		}

		$where .= " $wpdb->posts.post_type <> 'follow_up_email' ";

		return $where;
	}
}
