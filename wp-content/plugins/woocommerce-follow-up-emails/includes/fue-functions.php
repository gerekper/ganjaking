<?php

/**
 * Returns the path to the log file
 */
function fue_get_log_path() {
	$uploads = wp_upload_dir();
	$path    = trailingslashit( $uploads['basedir'] ) . 'fue.log';

	if ( ! file_exists( $path ) ) {
		touch( $path );
	}

	return $path;
}

/**
 * Get the login URL which depends on the installed plugins. If WC is installed,
 * the my-account URL is return. Otherwise, WP's default login URL is returned.
 *
 * @param string $redirect The URL to redirect to after logging in
 * @return string
 */
function fue_get_login_url( $redirect = '' ) {
	if ( Follow_Up_Emails::is_woocommerce_installed() ) {
		return add_query_arg(
			array('redirect' => $redirect),
			get_permalink( wc_get_page_id('myaccount') )
		);
	} else {
		return wp_login_url( $redirect );
	}
}

/**
 * Extract the proper basename from the path
 * @param string $filename
 * @return string
 */
function fue_template_basename( $filename ) {
	$locations = array(
		FUE_TEMPLATES_DIR .'/emails/',
		get_stylesheet_directory() .'/follow-up-emails/emails/'
	);

	return str_replace( $locations, '', $filename );
}

/**
 * Locate the directory an email template is installed in and return the full path
 * @param string $filename basename of the email template
 * @param $type string 'path' or 'url'
 * @return string|bool
 */
function fue_locate_email_template( $filename, $type = 'path' ) {
	$fue_pattern    = FUE_TEMPLATES_DIR .'/emails/'. fue_template_basename( $filename ) ;
	$theme_pattern  = get_stylesheet_directory() .'/follow-up-emails/emails/'. fue_template_basename( $filename );

	if ( file_exists( $theme_pattern ) ) {
		if ( $type == 'path' ) {
			return $theme_pattern;
		} else {
			return get_stylesheet_directory_uri() .'/follow-up-emails/emails/'. fue_template_basename( $filename );
		}
	} elseif ( file_exists( $fue_pattern ) ) {
		if ( $type == 'path' ) {
			return $fue_pattern;
		} else {
			return FUE_TEMPLATES_URL .'/emails/'. fue_template_basename( $filename );
		}

	}

	return false;
}

/**
 * Get all installed templates including ones inside the active theme
 * @return array
 */
function fue_get_installed_templates() {
	$fue_pattern        = FUE_TEMPLATES_DIR .'/emails/*.html';
	$fue_dir_pattern    = FUE_TEMPLATES_DIR .'/emails/*/*.html';
	$theme_pattern      = get_stylesheet_directory() .'/follow-up-emails/emails/*.html';
	$theme_dir_pattern  = get_stylesheet_directory() .'/follow-up-emails/emails/*/*.html';

	$fue_templates          = glob( $fue_pattern );
	$fue_subdir_templates   = glob( $fue_dir_pattern );
	$theme_templates        = glob( $theme_pattern );
	$theme_subdir_templates = glob( $theme_dir_pattern );

	if ( false === $fue_templates ) {
		$fue_templates = array();
	}

	if ( false === $fue_subdir_templates ) {
		$fue_subdir_templates = array();
	}

	if ( false === $theme_templates ) {
		$theme_templates = array();
	}

	if ( false === $theme_subdir_templates ) {
		$theme_subdir_templates = array();
	}

	$templates = array_filter(
		array_merge( $fue_templates, $theme_templates, $fue_subdir_templates, $theme_subdir_templates )
	);

	sort( $templates );

	return apply_filters( 'fue_installed_templates', $templates );
}

/**
 * Get the different conditions available for $email.
 *
 * Add-ons can hook to the fue_trigger_conditions filter to register custom conditions
 *
 * @param FUE_Email $email
 * @return array
 */
function fue_get_trigger_conditions( $email = null ) {
	return apply_filters( 'fue_trigger_conditions', array(), $email );
}

/**
 * Get the proper string representation of a duration depending on the value
 *
 * @deprecated
 * @param string    $duration
 * @param int       $value
 * @return string
 */
function fue_get_duration_string( $duration, $value = 0 ) {
	_deprecated_function( 'fue_get_duration_string', '4.0', 'Follow_Up_Emails::get_duration()' );
	return Follow_Up_Emails::get_duration( $duration, $value );
}

/**
 * Create a new FUE_Email. @see fue_save_email()
 *
 * @param array $args Optional array of arguments
 * @return int|WP_Error The new email ID or WP_Error on error
 */
function fue_create_email( $args ) {
	return fue_save_email( $args );
}

/**
 * Update an existing FUE_Email
 *
 * The ID of the email to update must be passed to the $args parameter.
 * The rest of the keys are similar to @see fue_create_email(). Only pass
 * the data that needs updating.
 *
 * @param array $args
 * @return int|WP_Error Returns the email ID on success, WP_Error on error
 */
function fue_update_email( $args ) {

	if ( isset( $args['id'] ) ) {
		$args['ID'] = $args['id'];
	}

	if ( !isset( $args['ID'] ) || empty( $args['ID'] ) ) {
		return new WP_Error( 'update_email', __('Cannot update email without the ID', 'follow_up_email') );
	}

	return fue_save_email( $args );
}

/**
 * Create a new, or update an existing FUE_Email. If ID is passed, it will update
 * the email with the matching ID.
 *
 * $args
 *  - name: Name of the email
 *  - type (e.g. storewide, subscription, etc)
 *  - subject: Email Subject
 *  - message: Email Content
 *  - status (default: fue-inactive)
 *  - priority
 *
 * Other keys can be passed to $args and they will be saved as postmeta
 *
 * @param array $args Optional array of arguments
 * @return int|WP_Error The new email ID or WP_Error on error
 */
function fue_save_email( $args ) {

	$post_args  = array( 'post_type' => 'follow_up_email' );
	$updating   = false;
	$old_email  = null;

	// support both id and ID
	if ( isset( $args['id'] ) ) {
		$args['ID'] = $args['id'];
		unset( $args['id'] );
	}

	if ( !empty( $args['ID'] ) ) {
		// update email
		$post_args['ID']    = $args['ID'];
		$updating           = true;
		$email_id           = $args['ID'];
		$old_email          = new FUE_Email( $email_id );
	} else {
		$args['ID'] = 0;
	}

	// only save if the email type is valid
	if ( !empty( $args['type'] ) ) {
		$email_type = Follow_Up_Emails::get_email_type( $args['type'] );

		if ( $email_type === false ) {
			return new WP_Error(
				'fue_save_email',
				sprintf( __('Invalid email type passed (%s)', 'follow_up_emails'), $args['type'] )
			);
		}
	}

	$args = apply_filters('fue_email_pre_save', $args, $args['ID']);

	if ( isset( $args['name'] ) ) {
		$post_args['post_title'] = trim( $args['name'] );
	}

	if ( isset( $args['subject'] ) ) {
		$post_args['post_excerpt'] = trim( $args['subject'] );
	}

	if ( isset( $args['message'] ) ) {
		$post_args['post_content'] = $args['message'];
	}

	if ( isset( $args['status'] ) ) {
		$post_args['post_status'] = $args['status'];
	}

	if ( isset( $args['priority'] ) ) {
		$post_args['menu_order'] = $args['priority'];
	}

	if ( $updating ) {
		wp_update_post( $post_args );
		$email_id = $args['ID'];
	} else {
		$email_id = wp_insert_post( $post_args );
	}

	if ( is_wp_error( $email_id ) ) {
		return $email_id;
	}

	// set email type
	if ( isset( $args['type'] ) ) {
		$type = $args['type'];

		wp_set_object_terms( $email_id, $type, 'follow_up_email_type', false );

		// data cleanup
		switch ( $args['type'] ) {
			case 'signup':
				$args['product_id']     = 0;
				$args['category_id']    = 0;
				$args['always_send']    = 1;
				break;

			case 'manual':
				// For manual email types we don't need interval_type (trigger)
				// and duration as it must be triggered manually.
				$args['interval_type']      = 'manual';
				$args['interval_duration']  = 0;
				break;

			case 'reminder':
				$args['always_send']    = 1;
				break;

		}

		// make sure the trigger matches the correct email type
		$fue_email      = new FUE_Email( $email_id );
		$trigger        = $fue_email->trigger;
		$fue_email_type = $fue_email->get_email_type();
		$email_triggers = $fue_email_type->triggers;

		if ( is_array( $email_triggers ) && !array_key_exists( $trigger, $email_triggers ) ) {
			$trigger = key( $email_triggers );
			update_post_meta( $email_id, '_interval_type', $trigger );
		}

	}

	// update campaigns
	if ( isset( $args['campaign'] ) ) {
		if ( empty( $args['campaign'] ) ) {
			// trying to remove campaigns
			wp_set_object_terms( $email_id, null, 'follow_up_email_campaign' );
		} else {
			if ( is_array( $args['campaign' ] ) ) {
				$campaigns = $args['campaign'];
			} else {
				$campaigns = array_filter( array_map( 'trim', explode( ',', $args['campaign'] ) ) );
			}

			wp_set_object_terms( $email_id, $campaigns, 'follow_up_email_campaign' );
		}

	}

	// Always Send always defaults to 0
	if ( isset($args['always_send'] ) && empty($args['always_send']) ) {
		$args['always_send'] = 0;
	}

	// empty product and category IDs must always be 0
	if ( isset( $args['product_id'] ) && empty( $args['product_id'] ) ) {
		$args['product_id'] = 0;
	}

	if ( isset( $args['category_id'] ) && empty( $args['category_id'] ) ) {
		$args['category_id'] = 0;
	}

	// store the new name, subject and content as old data for comparison purposes
	if ( !empty( $_POST['post_title'] ) ) {
		$args['prev_name'] = fue_clean( wp_unslash( $_POST['post_title'] ) );
	}

	if ( !empty( $_POST['post_excerpt'] ) ) {
		$args['prev_name'] = fue_clean( wp_unslash( $_POST['post_excerpt'] ) );
	}

	if ( !empty( $_POST['content'] ) ) {
		$args['prev_message'] = wp_kses_post( $_POST['content'] );
	}

	if ( !empty( $args['status'] ) ) {
		$args['prev_status'] = $args['status'];
	}

	if ( !empty( $args['type'] ) ) {
		$args['prev_type'] = $args['type'];
	}


	// unset the already processed keys and store the remaining keys as postmeta
	unset(
		$args['name'],
		$args['subject'],
		$args['message'],
		$args['status'],
		$args['type'],
		$args['ID'],
		$args['priority']
	);

	if ( isset($args['tracking_on']) && $args['tracking_on'] == 0 ) {
		$args['tracking_code'] = '';
	}

	if ( isset($args['interval_duration']) && $args['interval_duration'] == 'date' ) {
		$args['interval_type'] = 'date';
	}

	foreach ( $args as $meta_key => $meta_value ) {
		// merge the meta field
		if ( $meta_key == 'meta' ) {
			$meta_value = maybe_unserialize( $meta_value );

			$old_meta = get_post_meta( $email_id, '_meta', true );

			if (! is_array($old_meta) ) {
				$old_meta = maybe_unserialize( $old_meta );
			}

			if ( is_array( $old_meta ) && is_array( $meta_value ) ) {
				$meta_value = array_merge( $old_meta, $meta_value );
			}
		}

		update_post_meta( $email_id, '_'. $meta_key, $meta_value );
	}

	if ( $updating ) {
		do_action('fue_email_updated', $email_id, $args);
		FUE_Followup_Logger::log_changes( $email_id, $old_email );
	} else {
		do_action('fue_email_created', $email_id, $args);
		FUE_Followup_Logger::log( $email_id, __('Created email', 'follow_up_emails') );
	}

	return $email_id;

}

/**
 * Clone an existing FUE_Email
 *
 * @param $email_id
 * @param $new_name
 *
 * @return int|WP_Error
 */
function fue_clone_email($email_id, $new_name) {
	global $wpdb;

	$original_email = new FUE_Email( $email_id );
	$email_row      = $wpdb->get_row( $wpdb->prepare(
		"SELECT * FROM {$wpdb->posts} WHERE ID = %d",
		$email_id
	), ARRAY_A );

	if ( $email_row ) {
		unset(
			$email_row['ID'],
			$email_row['post_date'],
			$email_row['post_date_gmt'],
			$email_row['post_modified'],
			$email_row['post_modified_gmt'],
			$email_row['guid'],
			$email_row['post_name']
		);

		$email_row['post_title'] = $new_name;

		$wpdb->insert( $wpdb->posts, $email_row );
		$new_id = $wpdb->insert_id;

		// email type
		$type = $original_email->type;
		wp_set_object_terms( $new_id, $type, 'follow_up_email_type' );

		// campaign
		$campaigns = wp_get_object_terms( $email_id, 'follow_up_email_campaign' );

		if ( !is_wp_error( $campaigns ) ) {
			$campaign_slugs = array();
			foreach ( $campaigns as $campaign ) {
				$campaign_slugs[] = $campaign->slug;
			}

			if ( !empty( $campaign_slugs ) ) {
				wp_set_object_terms( $new_id, $campaign_slugs, 'follow_up_email_campaign' );
			}
		}

		// copy the meta
		$meta = get_post_meta( $email_id );

		foreach ( $meta as $key => $value ) {
			$value = maybe_unserialize( $value[0] );
			update_post_meta( $new_id, $key, $value );
		}

		// set the usage count to 0
		update_post_meta( $new_id, '_usage_count', 0 );

		do_action( 'fue_email_cloned', $new_id, $email_id );
		/* translators: %1$d Current email ID, %2$d New email ID. */
		fue_debug_log( sprintf( __( 'Cloned email from ID %1$d to ID %2$d', 'follow_up_emails' ), $email_id, $new_id ) );
		return $new_id;
	} else {
		/* translators: %d : Email ID. */
		$error = sprintf( __( 'Clone email (%d) could not be found', 'follow_up_emails' ), $email_id );
		fue_debug_log( $error );
		return new WP_Error( $error );
	}

}

/**
 * Quickly get an FUE_Email's type without instantiating FUE_Email
 *
 * @param int $email_id
 * @return string String representation of the email type
 */
function fue_get_email_type( $email_id ) {

	$terms  = @get_the_terms( $email_id, 'follow_up_email_type' );
	$type   = ! empty( $terms ) && isset( current( $terms )->name )
		? sanitize_title( current( $terms )->name )
		: 'ad-hoc';

	return $type;
}

/**
 * Get FUE_Emails based on type and status
 *
 * @param string        $type The email type (e.g. storewide, product, etc). Use 'any' to return all email types
 * @param string|array  $status
 * @param array         $filters Additional filters @see http://codex.wordpress.org/Class_Reference/WP_Query#Parameters
 *
 * @return FUE_Email[]
 */
function fue_get_emails( $type = 'any', $status = '', $filters = array() ) {
	$args = array(
		'nopaging'  => true,
		'orderby'   => 'ID',
		'order'     => 'DESC',
		'post_type' => 'follow_up_email'
	);

	if ( !empty( $status ) ) {
		$args['post_status'] = $status;
	} else {
		$args['post_status'] = array(
			FUE_Email::STATUS_ACTIVE,
			FUE_Email::STATUS_INACTIVE,
			FUE_Email::STATUS_ARCHIVED
		);
	}

	if ( $type != 'any' ) {
		if ( is_array( $type ) ) {
			$args['tax_query'][] = array(
				'taxonomy'  => 'follow_up_email_type',
				'terms'     => $type,
				'field'     => 'slug',
				'operator'  => 'IN'
			);
		} else {
			$args['tax_query'][] = array(
				'taxonomy'  => 'follow_up_email_type',
				'terms'     => $type,
				'field'     => 'slug'
			);
		}
	}

	if ( isset( $args['tax_query'] ) && isset( $filters['tax_query'] ) )
	{
		$args['tax_query'] = array_merge( $args['tax_query'], $filters['tax_query'] );
		unset( $filters['tax_query'] );
	}

	// apply the custom filters
	if ( !empty( $filters ) ) {
		$args = array_merge( $args, $filters );
	}

	$args = apply_filters( 'fue_get_emails_args', $args, $type );

	$rows   = get_posts( $args );
	$emails = array();

	if ( !empty( $args['fields'] ) && $args['fields'] == 'ids' ) {
		return $rows;
	}

	foreach ( $rows as $row ) {
		$emails[] = new FUE_Email( $row->ID );
	}

	return $emails;

}

function fue_get_emails_with_like_title( $type = 'any', $status = '', $filters = array() ) {
    $filters['suppress_filters'] = false;
    add_filter( 'posts_where', 'fue_title_filter', 10, 2 );
    $emails = fue_get_emails( $type, $status, $filters );
    remove_filter( 'posts_where', 'fue_title_filter', 10, 2 );
    return $emails;
}

function fue_title_filter( $where, $wp_query )
{
    global $wpdb;
    if ( $search_term = $wp_query->get( 'fue_post_title' ) ) {
        $where .= ' AND ' . $wpdb->posts . '.post_title LIKE \'%' . esc_sql( $wpdb->esc_like( $search_term ) ) . '%\'';
    }
    return $where;
}


/**
 * Add an email address to the list of exclusions
 *
 * @param string $email_address
 * @param int $email_id The email ID that triggered this unsubscription
 * @param int $order_id Limit the unsubscription to this order ID only
 *
 * @return int|WP_Error The inserted ID
 */
function fue_exclude_email_address( $email_address, $email_id = 0, $order_id = 0 ) {
	$wpdb = Follow_Up_Emails::instance()->wpdb;

	$email_name = '-';
	if ( $email_id > 0 ) {
		$email_name = get_the_title( $email_id );
	}

	$count = $wpdb->get_var( $wpdb->prepare(
		"SELECT COUNT(*)
		FROM {$wpdb->prefix}followup_email_excludes
		WHERE email = %s",
		$email_address
	) );

	if ( $count > 0 ) {
		$error = __( 'This email has already been removed from the list of exclusions', 'follow_up_emails' );
		fue_debug_log( $error, $email_address );
		return new WP_Error( 'fue_email_excluded', $error );
	}

	$data = array(
		'email_id'   => $email_id,
		'order_id'   => $order_id,
		'email_name' => $email_name,
		'email'      => $email_address,
		'date_added' => current_time( 'mysql' ),
	);

	if ( $wpdb->insert( $wpdb->prefix . 'followup_email_excludes', $data ) ) {
		fue_debug_log( __( 'Added email to list of excluded addresses', 'follow_up_emails' ), $data );
	} else {
		fue_debug_log( __( 'Unable to add email to list of excluded addresses', 'follow_up_emails' ), $data );
	}

	return $wpdb->insert_id;
}

/**
 * Check the provided $email_address if it is in the list of exclusions
 *
 * @param string    $email_address
 * @param int       $email_id
 * @param int       $order_id
 * @return bool
 */
function fue_is_email_excluded( $email_address, $email_id = 0, $order_id = 0 ) {
	$wpdb       = Follow_Up_Emails::instance()->wpdb;
	$excluded   = false;
	$params     = array( $email_address );

	$sql = "SELECT COUNT(*)
			FROM {$wpdb->prefix}followup_email_excludes
			WHERE email = %s";

	if ( $email_id > 0 ) {
		$sql .= " AND email_id = %d";
		$params[] = $email_id;
	}

	if ( $order_id > 0 ) {
		$sql .= " AND order_id = %d";
		$params[] = $order_id;
	} else {
		$sql .= " AND order_id = 0";
	}

	if ( !empty( $params ) ) {
		$sql = $wpdb->prepare( $sql, $params );
	}

	$rows = $wpdb->get_var( $sql );

	if ( $rows > 0 ) {
		$excluded = true;
	}

	return apply_filters( 'fue_is_email_excluded', $excluded, $email_address, $email_id );
}

/**
 * Set a user to not receive follow-up emails in general or only order-specific emails
 * @param int $user_id
 * @param int $order_id
 */
function fue_add_user_opt_out( $user_id, $order_id = null ) {
	if ( is_null( $order_id ) ) {
		update_user_meta( $user_id, 'fue_opted_out', true );
		/* translators: %d User ID. */
		fue_debug_log( sprintf( __( 'Added opt out for user %d', 'follow_up_emails' ), $user_id ) );
	} else {
		$opt_out_orders = get_user_meta( $user_id, 'fue_opted_out_orders', true );

		if ( ! $opt_out_orders ) {
			$opt_out_orders = array();
		}

		$opt_out_orders[ $order_id ] = current_time( 'mysql', true );

		update_user_meta( $user_id, 'fue_opted_out_orders', $opt_out_orders );
		/* translators: %1$d Order ID, %2$d User ID. */
		fue_debug_log( sprintf( __( 'Added opt out for order %1$d user %2$d', 'follow_up_emails' ), $order_id, $user_id ), $opt_out_orders );
	}
}

/**
 * Set the user to receive follow-up emails again in general or only order-specific emails
 * @param int $user_id
 * @param int $order_id
 */
function fue_remove_user_opt_out( $user_id, $order_id = null ) {
	if ( is_null( $order_id ) ) {
		update_user_meta( $user_id, 'fue_opted_out', false );
		/* translators: %d User ID. */
		fue_debug_log( sprintf( __( 'Removed opt out for user %d', 'follow_up_emails' ), $user_id ) );
	} else {
		$opt_out_orders = get_user_meta( $user_id, 'fue_opted_out_orders', true );

		if ( ! $opt_out_orders ) {
			$opt_out_orders = array();
		}

		unset( $opt_out_orders[ $order_id ] );

		update_user_meta( $user_id, 'fue_opted_out_orders', $opt_out_orders );
		/* translators: %1$d Order ID, %2$d User ID. */
		fue_debug_log( sprintf( __( 'Removed opt out for order %1$d user %2$d', 'follow_up_emails' ), $order_id, $user_id ), $opt_out_orders );
	}
}

/**
 * Check if a registered user has chosen to not receive follow-up emails
 *
 * @param int $user_id
 * @param int $order_id
 * @return bool
 */
function fue_user_opted_out( $user_id, $order_id = null ) {
	$opt_out = get_user_meta( $user_id, 'fue_opted_out', true );

	$opt_out = ( $opt_out != true ) ? false : true;

	if ( !$opt_out && !is_null( $order_id ) ) {
		$opt_out_orders = get_user_meta( $user_id, 'fue_opted_out_orders', true );

		if ( !$opt_out_orders ) {
			$opt_out_orders = array();
		}

		if ( array_key_exists( $order_id, $opt_out_orders ) !== false ) {
			$opt_out = true;
		}
	}

	return apply_filters( 'fue_user_opt_out', $opt_out, $user_id );
}

/**
 * Create a new FUE Coupon
 *
 * @param array $args
 * @return int|WP_Error The coupon ID on success or WP_Error on failure
 */
function fue_insert_coupon( $args = array() ) {
	global $wpdb;

	$defaults = array(
		'coupon_name'          => '',
		'coupon_prefix'        => '',
		'coupon_type'          => '',
		'amount'               => 0.0,
		'individual'           => 0,
		'before_tax'           => 0,
		'exclude_sale_items'   => 0,
		'free_shipping'        => 0,
		'minimum_amount'       => '',
		'maximum_amount'       => '',
		'usage_limit'          => '',
		'usage_limit_per_user' => '',
		'expiry_value'         => '',
		'expiry_type'          => '',
	);

	$args = wp_parse_args( $args, $defaults );

	if ( isset( $args['id'] ) ) {
		// Updating.
		$coupon_id = $args['id'];
		unset( $args['id'] );

		if ( $wpdb->update( $wpdb->prefix . 'followup_coupons', $args, array( 'id' => $coupon_id ) ) ) {
			fue_debug_log( __( 'Updated coupon', 'follow_up_emails' ), $args );
		} else {
			fue_debug_log( __( 'Failed to update coupon', 'follow_up_emails' ), $args );
		}
	} else {
		// New coupon.
		if ( $wpdb->insert( $wpdb->prefix . 'followup_coupons', $args ) ) {
			fue_debug_log( __( 'Added a new coupon', 'follow_up_emails' ), $args );
			$coupon_id = $wpdb->insert_id;
		} else {
			fue_debug_log( __( 'Failed to add new coupon', 'follow_up_emails' ), $args );
			$coupon_id = 0;
		}
	}

	return $coupon_id;
}

function fue_create_coupon( $args ) {
	return fue_insert_coupon( $args );
}

/**
 * Update an existing FUE Coupon
 *
 * @param array $args
 * @return int|WP_Error
 */
function fue_update_coupon( $args ) {
	if ( empty( $args['id'] ) ) {
		$error = __( 'Can not update coupon without the ID', 'follow_up_email' );
		fue_debug_log( $error, $args );
		return new WP_Error( 'update_email', $error );
	}

	return fue_insert_coupon( $args );
}

/**
 * Delete the specified coupon from the DB
 * @param int $coupon_id
 */
function fue_delete_coupon( $coupon_id ) {
	$wpdb = Follow_Up_Emails::instance()->wpdb;
	$wpdb->query( $wpdb->prepare( "DELETE FROM {$wpdb->prefix}followup_coupons WHERE id = %d", $coupon_id ) );
	/* translators: %d Coupon ID. */
	fue_debug_log( sprintf( __( 'Deleted coupon with ID %d', 'follow_up_emails' ), $coupon_id ) );
}

/**
 * Timezone - helper to retrieve the timezone string for a site until
 * a WP core method exists (see http://core.trac.wordpress.org/ticket/24730)
 *
 * Adapted from http://www.php.net/manual/en/function.timezone-name-from-abbr.php#89155
 *
 * @since 4.1
 * @return string a valid PHP timezone string for the site
 */
function fue_timezone_string() {

	// if site timezone string exists, return it
	if ( $timezone = get_option( 'timezone_string' ) ) {
		return $timezone;
	}

	// get UTC offset, if it isn't set then return UTC
	if ( 0 === ( $utc_offset = get_option( 'gmt_offset', 0 ) ) ) {
		return 'UTC';
	}

	// adjust UTC offset from hours to seconds
	$utc_offset *= 3600;

	// attempt to guess the timezone string from the UTC offset
	$timezone = timezone_name_from_abbr( '', $utc_offset );

	// last try, guess timezone string manually
	if ( false === $timezone ) {
		$is_dst = date( 'I' );

		foreach ( timezone_abbreviations_list() as $abbr ) {
			foreach ( $abbr as $city ) {
				if ( $city['dst'] == $is_dst && $city['offset'] == $utc_offset ) {
					return $city['timezone_id'];
				}
			}
		}
	}

	// fallback to UTC
	return 'UTC';
}

/**
 * Return a properly-formatted date/datetime string based on WP's date and time format settings
 *
 * @param int|string    $date_or_timestamp
 * @param bool          $show_time
 * @return string Formatted date/datetime
 */
function fue_format_date( $date_or_timestamp, $show_time = false ) {
	$timestamp      = $date_or_timestamp;
	$date_format    = wc_date_format();
	$time_format    = wc_time_format();

	if ( !is_numeric( $date_or_timestamp ) ) {
		$timestamp = strtotime( $date_or_timestamp );
	}

	if ( !$show_time ) {
		return date_i18n( $date_format, $timestamp );
	} else {
		return date_i18n( $date_format .' '. $time_format, $timestamp );
	}
}

/**
 * Format the date/time by zero-padding values that are < 10
 * @param object $email Email Object
 *
 * @return string
 */
function fue_format_send_datetime( $email ) {
	$meta = maybe_unserialize($email->meta);

	if ( !empty( $email->send_date_hour ) ) {
		return $email->send_date .' '.
			   fue_zero_pad_time( $email->send_date_hour ) .':'.
			   fue_zero_pad_time( $email->send_date_minute ) .' '.
			   $meta['send_date_ampm'];
	}

	return $email->send_date;
}

/**
 * Zero-pad a number if it is less than 10.
 *
 * @param int $number
 * @return string
 */
function fue_zero_pad_time( $number ) {
	if ( intval( $number ) < 10 ) {
		$number = '0' . $number;
	}

	return $number;
}

/**
 * Build a HTML output for a given recursive metadata array.
 *
 * @param array $meta  Metadata
 * @param int   $depth Depth of recursion (must be 0 for callers)
 *
 * @return string HTML output
 */
function fue_build_html_meta( $meta, $depth = 0 ) {
	// Limit recursion to depth of level 10.
	if ( $depth > 10 ) {
		return '';
	}

	$list = '<ul>';

	foreach ( $meta as $key => $value ) {
		while ( is_array( $value ) ) {
			$value = fue_build_html_meta( $value, $depth + 1 );
		}

		$list .= '<li>'. $key .': '. $value .'</li>';
	}

	$list .= '</ul>';

	return $list;
}

/**
 * Return the custom field value that matches a preg test.
 *
 * This is a callback function for preg_replace_callback - @see FUE_Mailer::send_email_order()
 *
 * @param array $matches
 *
 * @return string
 */
function fue_add_custom_fields( $matches ) {

	if ( empty($matches) ) {
		return '';
	}

	$post_id    = $matches[1];
	$field_key  = $matches[2];

	$meta = get_post_meta( $post_id, $field_key, true );

	if ( ! $meta ) {
		return '';
	}

	if ( '_downloadable_files' == $field_key ) {
		// link download URLs and enclose in <li> tags if there's more than one
		if ( count( $meta ) == 1 ) {
			$file = array_pop( $meta );
			$meta = '<a href="'. $file['file'] .'">'. $file['name'] .'</a>';
		} else {
			$list = '<ul>';

			foreach ( $meta as $file ) {
				$list .= '<li><a href="'. $file['file'] .'">'. $file['name'] .'</a></li>';
			}

			$list .= '</ul>';

			$meta = $list;
		}

		return $meta;
	}

	$meta = fue_build_html_meta( $meta );

	return $meta;
}

/**
 * Return an excerpt of a post that matches a preg test.
 *
 * This is a callback function for preg_replace_callback - @see FUE_Mailer::send_email_order()
 *
 * @param array $matches
 *
 * @return string
 */
function fue_add_post( $matches ) {
	if ( empty($matches) ) {
		return '';
	}

	if (! isset($matches[1]) || empty($matches[1]) ) {
		return '';
	}

	$post_id = $matches[1];

	$post = get_post( $post_id );

	if ( isset($post->post_excerpt) ) {
		return $post->post_excerpt;
	} else {
		return '';
	}
}

/**
 * Retrieve Page IDs
 * @param string $page
 *
 * @return int|mixed|void
 */
function fue_get_page_id( $page ) {
	$page = get_option('fue_' . $page . '_page_id');

	return ( $page ) ? $page : -1;
}

/**
 * Get the FUE Customer based on user ID or email address used in the $order
 *
 * @param int|WC_Order $order
 * @return object
 * @since 4.1
 */
function fue_get_customer_from_order( $order ) {

	if ( is_numeric( $order ) ) {
		$order = WC_FUE_Compatibility::wc_get_order( $order );
	}

	return fue_get_customer( WC_FUE_Compatibility::get_order_user_id( $order ), WC_FUE_Compatibility::get_order_prop( $order, 'billing_email' ) );

}

/**
 * Get an FUE Customer matching either the user ID or email address
 *
 * @param int       $user_id
 * @param string    $user_email
 *
 * @return object|null
 */
function fue_get_customer( $user_id = 0, $user_email = '' ) {
	global $wpdb;

	if ( empty( $user_id ) && empty( $user_email ) ) {
		return null;
	}

	$vars   = array();
	$sql    = "SELECT *
			  FROM {$wpdb->prefix}followup_customers
			  WHERE 1=1";

	if ( $user_id > 0 ) {
		$sql .= " AND user_id = %d";
		$vars[] = $user_id;
	} elseif ( !empty( $user_email ) ) {
		$sql .= " AND email_address = %s";
		$vars[] = $user_email;
	}

	if ( !empty($vars) ) {
		$sql = $wpdb->prepare( $sql, $vars );
	}

	return $wpdb->get_row( $sql );
}

/**
 * Get an FUE Customer matching the customer ID
 *
 * @param int       $customer_id
 *
 * @return object|null
 */
function fue_get_customer_by_id( $customer_id ) {
	global $wpdb;

	return $wpdb->get_row($wpdb->prepare(
		"SELECT *
		FROM {$wpdb->prefix}followup_customers
		WHERE id = %d",
		$customer_id
	));
}

/**
 * Get customer notes
 *
 * @param int $customer_id
 * @return array
 */
function fue_get_customer_notes( $customer_id ) {
	$wpdb = Follow_Up_Emails::instance()->wpdb;

	$notes = $wpdb->get_results($wpdb->prepare(
		"SELECT *
		FROM {$wpdb->prefix}followup_customer_notes
		WHERE followup_customer_id = %d
		ORDER BY date_added DESC",
		$customer_id
	));

	return $notes;
}

/**
 * Add a new customer note
 *
 * @param int       $customer_id
 * @param string    $note
 * @param int       $author_id
 * @return int
 */
function fue_add_customer_note( $customer_id, $note, $author_id = null ) {
	$wpdb = Follow_Up_Emails::instance()->wpdb;

	if ( is_null( $author_id ) ) {
		$author_id = get_current_user_id();
	}

	$now  = current_time( 'mysql' );
	$data = array(
		'followup_customer_id' => $customer_id,
		'note'                 => $note,
		'author_id'            => $author_id,
		'date_added'           => $now,
	);

	if ( $wpdb->insert( $wpdb->prefix . 'followup_customer_notes', $data ) ) {
		fue_debug_log( __( 'Added customer note', 'follow_up_emails' ), $data );
	} else {
		fue_debug_log( __( 'Failed to add customer note', 'follow_up_emails' ), $data );
	}

	return $wpdb->insert_id;
}

/**
 * Attempt to get the correct name of a user.
 *
 * This function first looks at the billing_first_name and billing_last_name fields
 * then falls back to the display_name data.
 *
 * @param int $user_id
 * @return string
 */
function fue_get_user_full_name( $user_id ) {
	$user = new WP_User( $user_id );

	if ( !empty( $user->billing_first_name ) || !empty( $user->billing_last_name ) ) {
		$name = $user->billing_first_name .' '. $user->billing_last_name;
	} elseif ( !empty( $user->first_name ) || !empty( $user->last_name ) ) {
		$name = $user->first_name .' '. $user->last_name;
	} else {
		$name = $user->display_name;
	}

	return $name;
}

/**
 * Get a subscriber using the ID or email
 * @param int|string $term
 * @return array
 */
function fue_get_subscriber( $term ) {
	return Follow_Up_Emails::instance()->newsletter->get_subscriber( $term );
}

/**
 * Get emails from the followup_subscribers table
 * @param array $args
 * @return array
 */
function fue_get_subscribers( $args = array() ) {
	return Follow_Up_Emails::instance()->newsletter->get_subscribers( $args );
}

/**
 * Add an entry to the followup_subscribers table
 * @param string $email
 * @param string $list
 * @return int|WP_Error
 */
function fue_add_subscriber( $email, $list = '' ) {
	_deprecated_function( 'fue_add_subscriber', '4.6.0', 'fue_add_subscriber_to_list' );

	if ( ! empty( $lists ) ) {
		if ( ! is_array( $lists ) ) {
			if ( strpos( $lists, ',' ) !== false ) {
				$lists = array_filter( explode( ',', $lists ) );
			} else {
				$lists = array( $lists );
			}
		}
	}

	return fue_add_subscriber_to_list( $list, array(
		'email'      => $email,
		'first_name' => '',
		'last_name'  => '',
	) );
}

/**
 * Add an entry to the followup_subscribers table
 *
 * @param string|array $lists
 * @param array        $args  Arguments
 *
 * @return int|WP_Error
 */
function fue_add_subscriber_to_list( $lists, $args ) {
	return Follow_Up_Emails::instance()->newsletter->add_subscriber_to_list( $lists, $args );
}

/**
 * Remove an email from the followup_subscribers table
 *
 * @param string $email
 */
function fue_remove_subscriber( $email ) {
	Follow_Up_Emails::instance()->newsletter->remove_subscriber( $email );
}

/**
 * Get all subscription lists
 */
function fue_get_subscription_lists() {
	$lists = Follow_Up_Emails::instance()->newsletter->get_lists();

	return apply_filters( 'fue_subscription_lists', array_filter( $lists ) );
}

/**
 * Check if the given email address is already a subscriber
 * @param string $email
 * @return bool
 */
function fue_subscriber_email_exists( $email ) {
	return Follow_Up_Emails::instance()->newsletter->subscriber_exists( $email );
}

/**
 * Get the URL to the unsubscribe endpoint
 */
function fue_get_unsubscribe_url() {
	$unsubscribe = get_option( 'fue_unsubscribe_endpoint', 'unsubscribe' );
	return apply_filters( 'fue_email_unsubscribe_url', site_url( "/$unsubscribe/" ) );
}

/**
 * Get the URL to the email-subscriptions endpoint
 */
function fue_get_email_subscriptions_url() {
	$email_subscriptions = get_option( 'fue_email_subscriptions_endpoint', 'email-subscriptions' );
	return apply_filters( 'fue_email_subscriptions_unsubscribe_url', site_url( "/my-account/$email_subscriptions/" ) );
}

/**
 * Get the URL to the FUE REST API
 *
 * @since 4.1
 * @param string $path an endpoint to include in the URL
 * @return string the URL
 */
function fue_get_api_url( $path ) {

	$version = defined( 'FUE_API_REQUEST_VERSION' ) ? FUE_API_REQUEST_VERSION : FUE_API::VERSION;

	$url = get_home_url( null, "fue-api/v{$version}/", is_ssl() ? 'https' : 'http' );

	if ( ! empty( $path ) && is_string( $path ) ) {
		$url .= ltrim( $path, '/' );
	}

	return $url;
}

/**
 * Get other templates passing attributes and including the file.
 *
 * @param string $template_name
 * @param array $args (default: array())
 * @param string $template_path (default: '')
 * @param string $default_path (default: '')
 * @return void
 */
function fue_get_template( $template_name, $args = array(), $template_path = '', $default_path = '' ) {
	if ( $args && is_array( $args ) ) {
		extract( $args );
	}

	$located = fue_locate_template( $template_name, $template_path, $default_path );

	// Allow 3rd party plugin filter template file from their plugin
	$located = apply_filters( 'fue_get_template', $located, $template_name, $args, $template_path, $default_path );

	if ( ! file_exists( $located ) ) {
		_doing_it_wrong( __FUNCTION__, sprintf( '<code>%s</code> does not exist.', esc_html( $located ) ), '2.1' );
		return;
	}

	do_action( 'fue_before_template_part', $template_name, $template_path, $located, $args );

	include( $located );

	do_action( 'fue_after_template_part', $template_name, $template_path, $located, $args );
}

/**
 * Locate a template and return the path for inclusion.
 *
 * This is the load order:
 *
 *  yourtheme       /   $template_path  /   $template_name
 *  yourtheme       /   $template_name
 *  $default_path   /   $template_name
 *
 * @param string $template_name
 * @param string $template_path (default: '')
 * @param string $default_path (default: '')
 * @return string
 */
function fue_locate_template( $template_name, $template_path = '', $default_path = '' ) {
	if ( ! $template_path ) {
		$template_path = 'follow-up-emails/';
	}

	if ( ! $default_path ) {
		$default_path = trailingslashit(FUE_TEMPLATES_DIR);
	}

	// Look within passed path within the theme - this is priority
	$template = locate_template(
		array(
			trailingslashit( $template_path ) . $template_name,
			$template_name
		)
	);

	// Get default template
	if ( ! $template ) {
		$template = $default_path . $template_name;
	}

	// Return what we found
	return apply_filters( 'fue_locate_template', $template, $template_name, $template_path );
}

/**
 * Get the link_meta property used by FUE_Sending_Mailer
 * @return array
 */
function fue_get_link_meta() {
	$fue = Follow_Up_Emails::instance();

	if ( property_exists( $fue, 'link_meta' ) ) {
		return $fue->link_meta;
	}

	return array();
}

/**
 * Store link_meta in the Follow_Up_Emails class
 *
 * @param array $meta
 */
function fue_set_link_meta( $meta ) {
	Follow_Up_Emails::instance()->link_meta = $meta;
}

/**
 * Search for and extract a portion of a string
 *
 * @param string    $start
 * @param string    $end
 * @param string    $string
 * @param bool      $borders
 *
 * @return mixed
 */
function fue_str_search($start, $end, $string, $borders = false) {
	$reg = "!".preg_quote ($start)."(.*?)".preg_quote ($end)."!is";
	preg_match_all ($reg, $string, $matches);
	if ($borders) {
		return $matches[0];
	} else {
		return $matches[1];
	}
}

/**
 * A backwards-compatible way of getting a product
 */
if (! function_exists('sfn_get_product') ) {
	function sfn_get_product( $product_id ) {
		//_deprecated_function( 'sfn_get_product', '3.7', 'WC_FUE_Compatibility::wc_get_product' );

		return WC_FUE_Compatibility::wc_get_product( $product_id );
	}
}

/**
 * Override the default Logger class of Action-Scheduler to stop logging FUE actions
 *
 * @param string $class class name
 * @return string
 */
function fue_add_logger_class( $class ) {
	if ( 'ActionScheduler_wpCommentLogger' === $class && get_option( 'fue_disable_action_scheduler_logging', true ) ) {
		require_once FUE_INC_DIR . '/class-fue-action-scheduler-logger.php';
		$class = "FUE_ActionScheduler_Logger";
	}

	return $class;
}

/**
 * Set the batch size that AS runs to 100 instead of the default 20
 *
 * @return int
 */
function fue_action_scheduler_batch_size() {
	return 100;
}

function fue_action_scheduler_concurrent_batches( $concurrent_batches ) {
	return 10;
}

/**
 * Generate an array of system data
 * @param string $type plain or array
 * @return mixed
 */
function fue_get_system_data( $type = 'plain' ) {
	$wpdb  = Follow_Up_Emails::instance()->wpdb;
	$theme = wp_get_theme();
	$parent = $theme->parent();
	$child = ($parent === false) ? false : true;
	$parent_theme = null;

	if ( $child ) {
		$parent_theme = array(
			'Parent Theme Name'         => $parent->get('Name'),
			'Parent Theme Version'      => $parent->get('Version'),
			'Parent Theme Author'       => $parent->get('Author') .' ('. $parent->get('AuthorURI') .')'
		);
	}

	$data = array(
		'Basic' => array(
			'WP Home'       => get_bloginfo('url'),
			'WP Version'    => get_bloginfo('version'),
			'WooCommerce'   => Follow_Up_Emails::is_woocommerce_installed() ? 'Yes' : 'No',
			'Sensei'        => Follow_Up_Emails::is_sensei_installed() ? 'Yes' : 'No'
		),
		'Theme' => array(
			'Theme Name'    => $theme->get('Name'),
			'Theme Version' => $theme->get('Version'),
			'Author'        => $theme->get('Author') .' '. $theme->get('AuthorURI'),
			'Child Theme'   => ($child) ? 'Yes' : 'No'
		),
		'Options' => array()
	);

	if ( $child ) {
		$data['Theme'] += $parent_theme;
	}

	$options = $wpdb->get_results("SELECT * FROM {$wpdb->options} WHERE option_name LIKE '%fue%'" );

	foreach ( $options as $option ) {
		if (
			strpos( $option->option_name, 'transient' ) !== false ||
			strpos( $option->option_name, 'lock' ) !== false
		) {
			continue;
		}

		$data['Data'][ $option->option_name ] = $option->option_value;
	}

	if ( $type == 'array' ) {
		return $data;
	}

	return fue_system_data_to_text( $data );

}

function fue_system_data_to_text( $data ) {
	$out = "<h2>System Info</h2>";

	foreach ( $data as $header => $section ) {
		$out .= "<h3>$header</h3>";

		$out .= '<table cellspacing="0" cellpadding="0"><tbody>';
		foreach ( $section as $title => $value ) {
			$out .= "<tr><th align=\"left\" width=\"300\">$title:</th><td>$value</td>";
		}
		$out .= '</table>';
	}

	return $out;
}

/**
 * let_to_num function.
 *
 * This function transforms the php.ini notation for numbers (like '2M') to an integer.
 *
 * @param $size
 * @return int
 */
function fue_let_to_num( $size ) {
	$let = substr( $size, -1 );
	$ret = substr( $size, 0, -1 );
	switch ( strtoupper( $let ) ) {
		case 'P':
			$ret *= pow( 1024, 5 );
			break;
		case 'T':
			$ret *= pow( 1024, 4 );
			break;
		case 'G':
			$ret *= pow( 1024, 3 );
			break;
		case 'M':
			$ret *= pow( 1024, 2 );
			break;
		case 'K':
			$ret *= 1024;
			break;
	}
	return $ret;
}

/**
 * Use RegEx to extract URLs from arbitrary content. Based on wp_extract_urls()
 * but it returns ALL URLS instead of only returning unique ones.
 *
 * @param string $content Content to extract URLs from.
 * @return array URLs found in passed string.
 */
function fue_extract_urls( $content ) {
	preg_match_all(
		"#([\"']?)("
		. "(?:([\w-]+:)?//?)"
		. "[^\s()<>]+"
		. "[.]"
		. "(?:"
		. "\([\w\d]+\)|"
		. "(?:"
		. "[^`!()\[\]{};:'\".,<>«»“”‘’\s]|"
		. "(?:[:]\d+)?/?"
		. ")+"
		. ")"
		. ")\\1#",
		$content,
		$post_links
	);

	$post_links = array_map( 'html_entity_decode', $post_links[2] );

	return array_values( $post_links );
}

/**
 * Gets and formats a list of cart item data + variations for display on the frontend.
 *
 * @param array $cart_item Cart item object.
 * @param bool  $flat Should the data be returned flat or in a list.
 * @return string
 */
function fue_get_cart_item_data( $cart_item, $flat = false ) {
	$item_data = array();

	// Variation values are shown only if they are not found in the title as of 3.0.
	// This is because variation titles display the attributes.
	if ( $cart_item['data']->is_type( 'variation' ) && is_array( $cart_item['variation'] ) ) {
		foreach ( $cart_item['variation'] as $name => $value ) {
			$taxonomy = wc_attribute_taxonomy_name( str_replace( 'attribute_pa_', '', urldecode( $name ) ) );

			if ( taxonomy_exists( $taxonomy ) ) {
				// If this is a term slug, get the term's nice name.
				$term = get_term_by( 'slug', $value, $taxonomy );
				if ( ! is_wp_error( $term ) && $term && $term->name ) {
					$value = $term->name;
				}
				$label = wc_attribute_label( $taxonomy );
			} else {
				// If this is a custom option slug, get the options name.
				$value = apply_filters( 'woocommerce_variation_option_name', $value );
				$label = wc_attribute_label( str_replace( 'attribute_', '', $name ), $cart_item['data'] );
			}

			// Check the nicename against the title.
			if ( '' === $value || wc_is_attribute_in_product_name( $value, $cart_item['data']->get_name() ) ) {
				continue;
			}

			$item_data[] = array(
				'key'   => $label,
				'value' => $value,
			);
		}
	}

	// Filter item data to allow 3rd parties to add more to the array.
	$item_data = apply_filters( 'woocommerce_get_item_data', $item_data, $cart_item );

	// Format item data ready to display.
	foreach ( $item_data as $key => $data ) {
		// Set hidden to true to not display meta on cart.
		if ( ! empty( $data['hidden'] ) ) {
			unset( $item_data[ $key ] );
			continue;
		}
		$item_data[ $key ]['key']     = ! empty( $data['key'] ) ? $data['key'] : $data['name'];
		$item_data[ $key ]['display'] = ! empty( $data['display'] ) ? $data['display'] : $data['value'];
	}

	// Output flat or in list format.
	if ( count( $item_data ) > 0 ) {
		ob_start();

		if ( $flat ) {
			foreach ( $item_data as $data ) {
				echo esc_html( $data['key'] ) . ': ' . wp_kses_post( $data['display'] ) . "\n";
			}
		} else {
			wc_get_template( 'cart/cart-item-data.php', array( 'item_data' => $item_data ) );
		}

		return ob_get_clean();
	}

	return '';
}

add_action(
	'woocommerce_loaded',
	function () {
		if ( ! function_exists( 'wc_esc_json' ) ) {
			/**
			 * Escape JSON for use on HTML or attribute text nodes.
			 * Added to WC 3.5.5, defined here if using older WC
			 *
			 * @param string $json JSON to escape.
			 * @param bool $html True if escaping for HTML text node, false for attributes. Determines how quotes are handled.
			 *
			 * @return string Escaped JSON.
			 */
			function wc_esc_json( $json, $html = false ) {
				return _wp_specialchars(
					$json,
					$html ? ENT_NOQUOTES : ENT_QUOTES, // Escape quotes in attribute nodes only.
					'UTF-8',                           // json_encode() outputs UTF-8 (really just ASCII), not the blog's charset.
					true                               // Double escape entities: `&amp;` -> `&amp;amp;`.
				);
			}
		}
	}
);

/**
 * Exact copy of wc_clean() implemented here because WooCommerce is not always available.
 *
 * @param string|array $var Data to sanitize.
 * @return string|array
 */
function fue_clean( $var ) {
	if ( is_array( $var ) ) {
		return array_map( 'fue_clean', $var );
	} else {
		return is_scalar( $var ) ? sanitize_text_field( $var ) : $var;
	}
}

/**
 * Exact copy of wc_deprecated_hook() implemented here because WooCommerce is not always available.
 *
 * @param string $hook        The hook that was used.
 * @param string $version     The version of WordPress that deprecated the hook.
 * @param string $replacement The hook that should have been used.
 * @param string $message     A message regarding the change.
 */
function fue_deprecated_hook( $hook, $version, $replacement = null, $message = null ) {
	global $wp;

	// @codingStandardsIgnoreStart
	if ( is_ajax() || defined( 'FUE_API_REQUEST' ) ) {
		do_action( 'deprecated_hook_run', $hook, $replacement, $version, $message );

		$message    = empty( $message ) ? '' : ' ' . $message;
		$log_string = "{$hook} is deprecated since version {$version}";
		$log_string .= $replacement ? "! Use {$replacement} instead." : ' with no alternative available.';

		error_log( $log_string . $message );
	} else {
		_deprecated_hook( $hook, $version, $replacement, $message );
	}
	// @codingStandardsIgnoreEnd
}

/**
 * Helper function for replacing URL variables, to handle cases where
 * sometimes they are prefixed with http/https.
 *
 * @param string $text        Text to replace in.
 * @param string $replacement The value that should have been used.
 *
 * @return string $text with substitutions done
 */
function fue_replacement_url_var( $replacement ) {
	return function( $text, $placeholder ) use ( $replacement ) {
		$values = array(
			'http://{'. $placeholder .'}',
			'https://{'. $placeholder .'}',
			'{'. $placeholder .'}',
		);

		return str_replace( $values, $replacement, $text );
	};
}

/**
 * Helper function for hashing an email address to pass as the hqid query parameter.
 *
 * @param string $email the email address to hash
 *
 * @return string the salted, hashed email address
 *
 * @since 4.9.16
 */
function fue_email_hash( $email ) {
	return wp_hash( $email, 'fue_email' );
}

/**
 * Log debug messages (using the WooCommerce logging class).
 *
 * @since 4.9.4
 *
 * @param string $message Text to log.
 * @param mixed  $data    Additional data to log as JSON.
 */
function fue_debug_log( $message, $data = null ) {
	if ( ! function_exists( 'wc_get_logger' ) ) {
		return;
	}

	$logging = get_option( 'fue_logging', null );

	if ( $logging ) {
		if ( ! is_null( $data ) ) {
			$message .= ' ' . wp_json_encode( $data, JSON_PRETTY_PRINT );
		}
		$logger = wc_get_logger();
		$logger->debug( $message, array( 'source' => 'fue' ) );
	}
}
