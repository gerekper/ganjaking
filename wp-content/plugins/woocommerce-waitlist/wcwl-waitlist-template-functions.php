<?php
/**
 * Helper functions for accessing waitlist elements
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
/**
 * Add given email to the waiting list for the given product ID
 *
 * @param string $email      user's email.
 * @param int    $product_id simple/variation product ID.
 * @param string $lang       user's language (if applicable).
 *
 * @return string|WP_Error
 */
function wcwl_add_user_to_waitlist( $email, $product_id, $lang = '' ) {
	if ( ! is_email( $email ) ) {
		$error = 'Failed to add user to waitlist: Email is not valid';
		wcwl_add_log( $error, $product_id, $email );

		return new WP_Error( 'woocommerce-waitlist', wcwl_get_generic_error_message( $error ) );
	}
	$product = wc_get_product( $product_id );
	if ( ! $product ) {
		$error = 'Failed to add user to waitlist: Product not found';
		wcwl_add_log( $error, $product_id, $email );

		return new WP_Error( 'woocommerce-waitlist', wcwl_get_generic_error_message( $error ) );
	}
	$waitlist = new Pie_WCWL_Waitlist( $product );
	return $waitlist->register_user( $email, $lang );
}

/**
 * Remove given email from waiting list from given product ID
 *
 * @param string $email      user's email.
 * @param int    $product_id simple/variation product ID.
 *
 * @return string|WP_Error
 */
function wcwl_remove_user_from_waitlist( $email, $product_id ) {
	global $sitepress;
	if ( isset( $sitepress ) ) {
		$product_id = wcwl_get_translated_main_product_id( $product_id );
	}
	$product = wc_get_product( $product_id );
	if ( ! $product ) {
		$error = 'Failed to remove user from waitlist: Product not found';
		wcwl_add_log( $error, $product_id, $email );

		return new WP_Error( 'woocommerce-waitlist', wcwl_get_generic_error_message( $error ) );
	}
	$waitlist = new Pie_WCWL_Waitlist( $product );

	return $waitlist->unregister_user( $email );
}

/**
 * Returns the HTML markup for the waitlist elements for the given product ID
 *
 * @param int    $product_id simple/variation/grouped product ID.
 * @param string $context    join/leave/update - determines which button to show.
 * @param string $notice     notice to display as the intro text (useful after button is pressed).
 *
 * @return string|WP_Error
 */
function wcwl_get_waitlist_fields( $product_id, $context = '', $notice = '', $lang = '' ) {
	$html = '';
	global $sitepress;
	if ( isset( $sitepress ) ) {
		$lang       = $lang ? $lang : wpml_get_language_information( null, $product_id )['language_code'];
		$product_id = wcwl_get_translated_main_product_id( $product_id );
	}
	$product = wc_get_product( $product_id );
	if ( ! $product ) {
		$error = 'Failed to load waitlist template: Product not found';
		wcwl_add_log( $error, $product_id );
	} else {
		if ( wcwl_waitlist_should_show( $product ) ) {
			$data         = wcwl_get_data_for_template( $product, $context, $notice );
			$data['lang'] = $lang;
			ob_start();
			wc_get_template( 'waitlist-single.php', $data, '', WooCommerce_Waitlist_Plugin::$path . 'templates/' );
			$html = ob_get_clean();
		}
	}

	return $html;
}

/**
 * Retrieve template for displaying waitlist elements on archive pages (e.g. shop, product-category pages)
 *
 * @param int    $product_id product ID.
 * @param string $context    join/leave etc.
 * @param string $notice     notice to display.
 *
 * @return string|WP_Error
 */
function wcwl_get_waitlist_for_archive( $product_id, $context = '', $notice = '' ) {
	$html = '';
	$lang = '';
	global $sitepress;
	if ( isset( $sitepress ) ) {
		$lang       = wpml_get_language_information( null, $product_id )['language_code'];
		$product_id = wcwl_get_translated_main_product_id( $product_id );
	}
	$product = wc_get_product( $product_id );
	if ( ! $product ) {
		$error = 'Failed to load waitlist template: Product not found';
		wcwl_add_log( $error, $product_id );
	} else {
		if ( wcwl_waitlist_should_show( $product ) ) {
			$data         = wcwl_get_data_for_template( $product, $context, $notice );
			$data['lang'] = $lang;
			ob_start();
			wc_get_template( 'waitlist-archive.php', $data, '', WooCommerce_Waitlist_Plugin::$path . 'templates/' );
			$html = ob_get_clean();
		}
	}

	return $html;
}

/**
 * Retrieve template for displaying waitlist elements on event pages
 *
 * @param int    $event_id event ID.
 * @param string $context  join/leave etc.
 * @param string $notice   notice to display.
 *
 * @return string|WP_Error
 */
function wcwl_get_waitlist_for_event( $event_id, $context = 'update', $notice = '' ) {
	$html = '';
	if ( ! wcwl_is_event( $event_id ) ) {
		$error = 'Failed to load waitlist template: Event not found';
		wcwl_add_log( $error, $event_id );
	} elseif ( ! tribe_events_has_tickets( $event_id ) ) {
		$error = 'Failed to load waitlist template: No tickets found';
		wcwl_add_log( $error, $event_id );
	} else {
		$data = wcwl_get_data_for_event_template( $event_id, $context, $notice );
		ob_start();
		wc_get_template( 'waitlist-event.php', $data, '', WooCommerce_Waitlist_Plugin::$path . 'templates/' );
		$html = ob_get_clean();
	}

	return $html;
}

/**
 * Get the HTML to display a checkbox for the given product
 *
 * Used in conjunction with "wcwl_get_waitlist_fields( $product_id, 'update' )" to handle grouped products
 * Can be used for any page that displays a list of products (user checks desired products and can sign up to multiple waitlists)
 *
 * @param WC_Product $product product object.
 * @param string     $lang    user's language (if applicable).
 *
 * @return string
 */
function wcwl_get_waitlist_checkbox( WC_Product $product, $lang ) {
	if ( ! $product ) {
		return '';
	}
	$user     = get_user_by( 'id', get_current_user_id() );
	$waitlist = new Pie_WCWL_Waitlist( $product );
	$checked  = '';
	if ( $user && $waitlist->user_is_registered( $user->user_email ) ) {
		$checked = 'checked';
	}
	ob_start();
	wc_get_template(
		'waitlist-grouped-checkbox.php',
		array(
			'product_id'  => $product->get_id(),
			'lang'        => $lang,
			'user'        => $user,
			'button_text' => apply_filters( 'wcwl_waitlist_checkbox_text', __( 'Join Waitlist', 'woocommerce-waitlist' ) ),
			'checked'     => $checked,
		),
		'',
		WooCommerce_Waitlist_Plugin::$path . 'templates/'
	);

	return ob_get_clean();
}

/**
 * Return waitlist data required for template
 *
 * @param WC_Product $product product object.
 * @param string     $context join/leave etc.
 * @param string     $notice  notice to display.
 *
 * @return array
 */
function wcwl_get_data_for_template( $product, $context, $notice ) {
	$waitlist            = new Pie_WCWL_Waitlist( $product );
	$user                = get_user_by( 'id', get_current_user_id() );
	$user_is_on_waitlist = $user ? $waitlist->user_is_registered( $user->user_email ) : false;
	$on_waitlist         = $product->is_type( 'grouped' ) ? false : $user_is_on_waitlist;
	if ( ! $context ) {
		$context = $on_waitlist ? 'leave' : 'join';
	}
	$data                = wcwl_get_default_template_values( $user, $product->get_id(), $context, $notice );
	$data['on_waitlist'] = $on_waitlist;
	$data['intro']       = wcwl_get_intro_text( $product->get_type(), $on_waitlist );
	$data['product']     = $product;

	return $data;
}

/**
 * Return waitlist data required for template when displaying elements on an event page
 *
 * @param int    $event_id event ID.
 * @param string $context  join/leave etc.
 * @param string $notice   notice to display.
 *
 * @return array
 */
function wcwl_get_data_for_event_template( $event_id, $context = 'update', $notice = '' ) {
	$user                = get_user_by( 'id', get_current_user_id() );
	$data                = wcwl_get_default_template_values( $user, $event_id, $context, $notice );
	$data['on_waitlist'] = false;
	$data['intro']       = wcwl_get_intro_text( 'event', false );
	$lang                = '';
	global $sitepress;
	if ( isset( $sitepress ) ) {
		$lang = wpml_get_language_information( null, $event_id )['language_code'];
	}
	$data['lang'] = $lang;

	return $data;
}

/**
 * Get default shared values for waitlist template
 *
 * @param WP_User/false $user    user object.
 * @param int           $id      product ID.
 * @param string        $context join/leave etc.
 * @param string        $notice  notice to display.
 *
 * @return array
 */
function wcwl_get_default_template_values( $user, $id, $context, $notice ) {
	global $wp;
	$current_url = home_url( add_query_arg( array(), $wp->request ) );

	return array(
		'user'                           => $user,
		'email_class'                    => $user ? 'wcwl_hide' : '',
		'product_id'                     => $id,
		'context'                        => $context,
		'url'                            => apply_filters( 'wcwl_waitlist_button_url', '#', $id ),
		'notice'                         => $notice,
		'opt_in'                         => wcwl_is_optin_enabled( $user ),
		'opt_in_text'                    => wcwl_get_optin_text( $user ),
		'email_address_label_text'       => apply_filters( 'wcwl_email_field_label', __( 'Enter your email address to join the waitlist for this product', 'woocommerce-waitlist' ) ),
		'email_address_placeholder_text' => apply_filters( 'wcwl_email_field_placeholder', __( 'Email address', 'woocommerce-waitlist' ) ),
		'is_archive'                     => isset( $_POST['archive'] ) ? $_POST['archive'] : false,
		'dismiss_notification_text'      => apply_filters( 'wcwl_dismiss_notification_text', __( 'Dismiss notification', 'woocommerce-waitlist' ) ),
		'registration_required_text'     => apply_filters( 'wcwl_join_waitlist_user_requires_registration_message_text', sprintf( __( 'You must register to use the waitlist feature. Please %1$slogin or create an account%2$s', 'woocommerce-waitlist' ), '<a href="' . wc_get_page_permalink( 'myaccount' ) . '?wcwl_redirect=' . urlencode( $current_url ) . '">', '</a>' ) ),
	);
}

/**
 * Get the text to display on the waitlist button
 *
 * @param string $context join/leave/update depending on product type and user.
 *
 * @return mixed|void
 */
function wcwl_get_button_text( $context = 'join' ) {
	switch ( $context ) {
		case 'join':
			$text = __( 'Join Waitlist', 'woocommerce-waitlist' );
			break;
		case 'leave':
			$text = __( 'Leave Waitlist', 'woocommerce-waitlist' );
			break;
		case 'update':
			$text = __( 'Update Waitlist', 'woocommerce-waitlist' );
			break;
		case 'confirm':
			$text = __( 'Confirm', 'woocommerce-waitlist' );
			break;
		default:
			$text = ucwords( $context );
	}

	return apply_filters( 'wcwl_' . $context . '_waitlist_button_text', $text );
}

/**
 * Get the default intro text to display above the waitlist dependent on product type
 *
 * @param string $product_type        simple/variation/grouped (variation is the same as simple by default).
 * @param bool   $user_is_on_waitlist is user on waitlist.
 *
 * @return mixed|void
 */
function wcwl_get_intro_text( $product_type = 'simple', $user_is_on_waitlist = false ) {
	$context = 'join';
	$text    = __( 'Join the waitlist to be emailed when this product becomes available', 'woocommerce-waitlist' );
	if ( $user_is_on_waitlist ) {
		$context = 'leave';
		$text    = __( 'You are on the waitlist for this product', 'woocommerce-waitlist' );
	} elseif ( 'grouped' === $product_type || 'event' === $product_type ) {
		$context = $product_type;
		$text    = __( 'Check the box alongside any Out of Stock products and update the waitlist to be emailed when those products become available', 'woocommerce-waitlist' );
	}

	return apply_filters( 'wcwl_' . $context . '_waitlist_message_text', $text );
}

/**
 * Are all conditions met to show the waitlist for the given product?
 *
 * @param WC_Product $product product object.
 */
function wcwl_waitlist_should_show( $product ) {
	$waitlist_is_required = false;
	if ( ! wcwl_waitlist_is_enabled_for_product( $product->get_id() ) ) {
		$waitlist_is_required = false;
	} elseif ( $product->is_on_backorder() && WooCommerce_Waitlist_Plugin::enable_waitlist_for_backorder_products( $product->get_id() ) ) {
		$waitlist_is_required = true;
	} elseif ( ! $product->is_in_stock() ) {
		$waitlist_is_required = true;
	} elseif ( $product->is_type( 'bundle' ) || $product->is_type( 'grouped' ) ) {
		$waitlist_is_required = true;
	}

	return apply_filters( 'wcwl_waitlist_is_required', $waitlist_is_required, $product );
}

/**
 * Is waitlist enabled for the given product ID?
 *
 * @param int $product_id product ID.
 *
 * @return bool
 */
function wcwl_waitlist_is_enabled_for_product( $product_id ) {
	$enabled = true;
	$options = get_post_meta( $product_id, 'wcwl_options', true );
	if ( isset( $options['enable_waitlist'] ) && 'false' === $options['enable_waitlist'] ) {
		$enabled = false;
	}

	return apply_filters( 'wcwl_show_waitlist', $enabled, $product_id );
}

/**
 * Is the opt-in functionality currently enabled?
 *
 * @param object $user user object.
 *
 * @return bool
 */
function wcwl_is_optin_enabled( $user ) {
	if ( ( ! $user && 'yes' == get_option( 'woocommerce_waitlist_new_user_opt-in' ) ) || ( $user && 'yes' == get_option( 'woocommerce_waitlist_registered_user_opt-in' ) ) ) {
		return true;
	}

	return false;
}

/**
 * Get the text to display for the opt-in checkbox
 *
 * @param object $user user object.
 *
 * @return mixed|void
 */
function wcwl_get_optin_text( $user ) {
	if ( ! $user ) {
		return apply_filters( 'wcwl_new_user_opt-in_text', __( 'By ticking this box you agree to an account being created using the given email address and to receive waitlist communications by email', 'woocommerce-waitlist' ) );
	} else {
		return apply_filters( 'wcwl_registered_user_opt-in_text', __( 'By ticking this box you agree to receive waitlist communications by email', 'woocommerce-waitlist' ) );
	}
}

/**
 * Return the main product for the given translated product ID
 * Required to support WPML as all meta data is saved to the original/main product
 *
 * @param int $product_id product ID.
 *
 * @return int
 */
function wcwl_get_translated_main_product_id( $product_id ) {
	global $woocommerce_wpml;
	$master_post_id = $product_id;
	if ( isset( $woocommerce_wpml->products ) && $woocommerce_wpml->products ) {
		$master_post_id = $woocommerce_wpml->products->get_original_product_id( $product_id );
	}

	return $master_post_id;
}

/**
 * Get the language required for the given email address
 *
 * @param  string/int $user      user's email address or ID
 * @param  int        $product_id legacy method for finding language per product.
 * @return string             language code
 */
function wcwl_get_user_language( $user, $product_id = 0 ) {
	if ( ! function_exists( 'wpml_get_default_language' ) ) {
		return '';
	}
	$lang_option = get_option( '_' . WCWL_SLUG . '_languages' );
	$user_object = get_user_by( 'id', $user );
	if ( $user_object && $product_id ) {
			$languages = get_user_meta( $user_object->ID, 'wcwl_languages', true );
			if ( isset( $languages[ $product_id ] ) ) {
					return $languages[ $product_id ];
			}
	}
	if ( isset( $lang_option[ $user ] ) ) {
		return $lang_option[ $user ];
	}
	return wpml_get_default_language();
}

/**
 * Check whether given post ID is of type "event"
 *
 * @param int $post_id post ID.
 *
 * @return bool
 */
function wcwl_is_event( $post_id ) {
	if ( function_exists( 'tribe_events_get_event' ) && tribe_events_get_event( $post_id ) ) {
		return true;
	}

	return false;
}

/**
 * Return a generic, filterable message for the given error
 *
 * @param string $error error message.
 *
 * @return mixed|void
 */
function wcwl_get_generic_error_message( $error ) {
	return apply_filters( 'wcwl_generic_error_message', __( 'I\'m afraid something went wrong with your request. Please try again or contact us for help', 'woocommerce-waitlist' ), $error );
}

/**
 * Add a message to the WC logs
 *
 * @param string $message    error message.
 * @param int    $product_id product ID.
 * @param string $email      user email.
 */
function wcwl_add_log( $message, $product_id = 0, $email = '' ) {
	$logger = wc_get_logger();
	$logger->debug( $message . ' (Post ID: ' . $product_id . '; User email: ' . $email . ')', array( 'source' => 'woocommerce-waitlist' ) );
}
