<?php

/*
 * Request Handlers - These functions will be moved into class-wc-wishlists-request-handler
 */

add_action( 'wp_loaded', 'woocommerce_wishlist_handle_add_to_wishlist_action', 9 );

function woocommerce_wishlist_handle_add_to_wishlist_action() {

	if ( isset( $_REQUEST['wlaction'] ) && $_REQUEST['wlaction'] == 'clear-session-items' ) {
		return;
	}

	if ( ! isset( $_REQUEST['add-to-wishlist-itemid'] ) || empty( $_REQUEST['add-to-wishlist-itemid'] ) ) {
		return;
	} else {
		remove_action( 'init', 'woocommerce_add_to_cart_action' );
		remove_action( 'wp_loaded', 'WC_Form_Handler::add_to_cart_action', 20 );
	}

	header( "Cache-Control: no-store, no-cache, must-revalidate, max-age=0" );
	header( "Cache-Control: post-check=0, pre-check=0", false );
	header( "Pragma: no-cache" );
	header( "X-Robots-Tag: noindex, nofollow", true );

	WC_Wishlists_User::set_cookie();


	//We need to force WooCommerce to set the session cookie.
	//We do this here since we need to make sure that the product pages are no longer cached.
	if ( ! WC_Wishlist_Compatibility::WC()->session->has_session() ) {
		WC_Wishlist_Compatibility::WC()->session->set_customer_session_cookie( true );
	}

	if ( ! is_user_logged_in() && ( WC_Wishlists_Settings::get_setting( 'wc_wishlist_guest_enabled', 'enabled' ) == 'disabled' ) ) {
		return;
	}

	$wishlist_id = isset( $_REQUEST['wlid'] ) ? $_REQUEST['wlid'] : 0;
	if ( ! $wishlist_id && ( WC_Wishlists_Settings::get_setting( 'wc_wishlist_autocreate', 'yes' ) == 'yes' ) ) {
		$wishlist_id = WC_Wishlists_Wishlist::create_list( __( 'Wishlist', 'wc_wishlist' ) );

		//Wishlist created successfully.  Show messages
		if ( $wishlist_id ) {
			if ( is_user_logged_in() ) {
				$redirect_url = WC_Wishlists_Wishlist::get_the_url_edit( $wishlist_id );
				if ( WC_Wishlists_Settings::get_setting( 'woocommerce_wishlist_redirect_after_add', 'yes' ) == 'yes' && WC_Wishlist_Compatibility::wc_error_count() == 0 ) {
					$message = sprintf( __( 'This list has been automatically created for you.', 'wc_wishlist' ) );
					WC_Wishlist_Compatibility::wc_add_notice( apply_filters( 'woocommerce_wishlist_wishlist_created_message', $message ) );
				} else {
					$message = sprintf( __( 'A list has been created for you. <a href="%s">Manage list</a> ', 'wc_wishlist' ), $redirect_url );
					WC_Wishlist_Compatibility::wc_add_notice( apply_filters( 'woocommerce_wishlist_wishlist_created_message', $message, $wishlist_id ) );
				}
			} else {
				$myaccounturl = get_permalink( wc_get_page_id( 'myaccount' ) );
				$redirect_url = WC_Wishlists_Wishlist::get_the_url_edit( $wishlist_id );
				$auth_url     = apply_filters( 'woocommerce_wishlist_authentication_url', esc_url( add_query_arg( array( 'redirect' => urlencode( $redirect_url ) ), $myaccounturl ) ) );
				$register_url = apply_filters( 'woocommerce_wishlist_registration_url', esc_url( add_query_arg( array( 'redirect' => urlencode( $redirect_url ) ), $myaccounturl ) ) );
				$message      = sprintf( __( 'A temporary list has been created for you. <a href="%s">Login</a> or <a href="%s">register for an account</a> to save this list for future use.  You may access this temporary list for up to 30 days or until you clear your browser history.', 'wc_wishlist' ), $auth_url, $register_url );
				WC_Wishlist_Compatibility::wc_add_notice( apply_filters( 'woocommerce_wishlist_wishlist_created_message', $message, $wishlist_id ) );
			}
		}
	} else {
		//Auto Create is disabled.  Require user to create a list manually.
	}

	if ( ! $wishlist_id ) {
		WC_Wishlist_Compatibility::wc_add_notice( __( 'Unable to locate or create a list for you.  Please try again later', 'wc_wishlist' ), 'error' );
		wp_redirect( apply_filters( 'woocommerce_add_to_cart_product_id', get_permalink( $_REQUEST['product_id'] ) ) );
		exit;
	} else {

	}

	$added_to_wishlist = false;

	$item_id    = isset( $_REQUEST['add-to-cart'] ) ? $_REQUEST['add-to-cart'] : $_REQUEST['add-to-wishlist-itemid'];
	$product_id = apply_filters( 'woocommerce_add_to_cart_product_id', absint( $item_id ) );
	$product    = wc_get_product( $product_id );
	if ( empty( $product ) ) {
		exit;
	}
	$product_type = $product->get_type();
	switch ( $product_type ) {
		// Variable Products
		case 'variable' :
		case 'variable-subscription':
			$added_to_wishlist = woocommerce_wishlist_add_to_wishlist_handler_variable( $product_id, $wishlist_id );
			break;
		case 'group':
		case 'grouped' :
			if ( isset( $_REQUEST['quantity'] ) && is_array( $_REQUEST['quantity'] ) ) {
				$added_to_wishlist = woocommerce_wishlist_add_to_cart_handler_grouped( $product_id, $wishlist_id );
				if ( ! $added_to_wishlist ) {
					wp_redirect( get_permalink( $product_id ) );
					exit;
				}

			} elseif ( $_REQUEST['add-to-wishlist-itemid'] ) {

				/* Link on product archives */
				WC_Wishlist_Compatibility::wc_add_notice( __( 'Please choose a product&hellip;', 'woocommerce' ), 'error' );
				wp_redirect( get_permalink( $_REQUEST['add-to-wishlist-itemid'] ) );
				exit;
			}

			break;

		// Simple Products - add-to-cart contains product ID
		default :

			//Get product ID to add and quantity
			$quantity = ( isset( $_REQUEST['quantity'] ) ) ? (int) $_REQUEST['quantity'] : 1;

			//Add to cart validation
			$passed_validation = apply_filters( 'woocommerce_add_to_wishlist_validation', apply_filters( 'woocommerce_add_to_cart_validation', true, $product_id, $quantity ), $product_id, $quantity );

			if ( $passed_validation ) {
				//Add the product to the wishlist
				if ( WC_Wishlists_Wishlist_Item_Collection::add_item( $wishlist_id, $product_id, $quantity ) ) {

					$added_to_wishlist = true;
				}
			}

			break;
	}

	//If we added the product to the cart we can now do a redirect, otherwise just continue loading the page to show errors
	if ( $added_to_wishlist && $wishlist_id != 'session' ) {
		woocommerce_wishlist_add_to_wishlist_message( $wishlist_id );

		if ( ! isset( $_REQUEST['wl_from_single_product'] ) || empty( $_REQUEST['wl_from_single_product'] ) ) {
			$url = add_query_arg( array( 'add-to-wishlist-itemid' => false ) );
			wp_redirect( esc_url_raw( $url ) );
			die();
		}

		$url = apply_filters( 'add_to_wishlist_redirect_url', false, $wishlist_id );
		//If has custom URL redirect there
		if ( $url ) {
			wp_safe_redirect( $url );
			exit;
		} elseif ( WC_Wishlists_Settings::get_setting( 'woocommerce_wishlist_redirect_after_add_to_cart', 'yes' ) == 'yes' && WC_Wishlist_Compatibility::wc_error_count() == 0 ) {
			//Redirect to the wishlist
			wp_safe_redirect( WC_Wishlists_Wishlist::get_the_url_edit( $wishlist_id ) );
			die();
		} else {
			//Check for is product so we can add items from the shop page / and quick view.
			if ( is_numeric( $_REQUEST['add-to-wishlist-itemid'] ) ) {
				wp_redirect( get_permalink( $_REQUEST['add-to-wishlist-itemid'] ) );
				die();
			} elseif ( isset( $_GET['product_id'] ) ) {
				wp_redirect( get_permalink( $_GET['product_id'] ) );
				die();
			}
		}
	} elseif ( $wishlist_id == 'session' ) {

		if ( $added_to_wishlist ) {

			$message    = sprintf( __( '%s Items ready to move to a new list', 'wc_wishlist' ), 1 );
			$message    = apply_filters( 'woocommerce_wishlist_wishlist_ready_to_move_message', $message, $wishlist_id );
			$action_url = WC_Wishlists_Plugin::nonce_url( 'clear-session-items', add_query_arg( array( 'wlaction' => 'clear-session-items' ) ) );
			$action_url = apply_filters( 'woocommerce_wishlist_wishlist_ready_to_move_cancel_url', $action_url, $wishlist_id );
			$action     = '<a class="wishlist-message-dismiss" href="' . $action_url . '">' . __( 'Cancel', 'wc_wishlist' ) . '</a>';

			WC_Wishlist_Compatibility::wc_add_notice( $message . $action );
		}

		$wl_return_to = false;
		if ( isset( $_REQUEST['wl_from_single_product'] ) || ! empty( $_REQUEST['wl_from_single_product'] ) ) {
			if ( is_numeric( $_REQUEST['add-to-wishlist-itemid'] ) ) {
				$wl_return_to = $_REQUEST['add-to-wishlist-itemid'];
			} elseif ( isset( $_GET['product_id'] ) ) {
				$wl_return_to = $_GET['product_id'];
			}
		}

		wp_safe_redirect( add_query_arg( array( 'wl_return_to' => $wl_return_to ), WC_Wishlists_Pages::get_url_for( 'create-a-list' ) ) );
		die();
	}
}

function woocommerce_wishlist_add_to_wishlist_handler_variable( $product_id, $wishlist_id ) {
	$adding_to_cart     = wc_get_product( $product_id );
	$variation_id       = empty( $_REQUEST['variation_id'] ) ? '' : absint( $_REQUEST['variation_id'] );
	$quantity           = empty( $_REQUEST['quantity'] ) ? 1 : wc_stock_amount( $_REQUEST['quantity'] );
	$missing_attributes = array();
	$variations         = array();
	$attributes         = $adding_to_cart->get_attributes();

	// If no variation ID is set, attempt to get a variation ID from posted attributes.
	if ( empty( $variation_id ) ) {
		$data_store   = WC_Data_Store::load( 'product' );
		$variation_id = $data_store->find_matching_product_variation( $adding_to_cart, wp_unslash( $_POST ) );
	}

	// Validate the attributes.
	try {
		if ( empty( $variation_id ) ) {
			throw new Exception( __( 'Please choose product options&hellip;', 'woocommerce' ) );
		}

		$variation_data = wc_get_product_variation_attributes( $variation_id );

		foreach ( $attributes as $attribute ) {
			if ( ! $attribute['is_variation'] ) {
				continue;
			}

			$taxonomy = 'attribute_' . sanitize_title( $attribute['name'] );

			if ( isset( $_REQUEST[ $taxonomy ] ) ) {
				// Get value from post data
				if ( $attribute['is_taxonomy'] ) {
					// Don't use wc_clean as it destroys sanitized characters
					$value = sanitize_title( stripslashes( $_REQUEST[ $taxonomy ] ) );
				} else {
					$value = wc_clean( stripslashes( $_REQUEST[ $taxonomy ] ) );
				}

				// Get valid value from variation
				$valid_value = isset( $variation_data[ $taxonomy ] ) ? $variation_data[ $taxonomy ] : '';

				// Allow if valid or show error.
				if ( '' === $valid_value || $valid_value === $value ) {
					$variations[ $taxonomy ] = $value;
				} else {
					throw new Exception( sprintf( __( 'Invalid value posted for %s', 'woocommerce' ), wc_attribute_label( $attribute['name'] ) ) );
				}
			} else {
				$missing_attributes[] = wc_attribute_label( $attribute['name'] );
			}
		}
		if ( ! empty( $missing_attributes ) ) {
			throw new Exception( sprintf( _n( '%s is a required field', '%s are required fields', sizeof( $missing_attributes ), 'woocommerce' ), wc_format_list_of_items( $missing_attributes ) ) );
		}
	} catch ( Exception $e ) {
		wc_add_notice( $e->getMessage(), 'error' );

		return false;
	}

	// Add to cart validation
	$passed_validation = apply_filters( 'woocommerce_add_to_wishlist_validation', apply_filters( 'woocommerce_add_to_cart_validation', true, $product_id, $quantity ), $product_id, $quantity );


	if ( $passed_validation ) {
		if ( WC_Wishlists_Wishlist_Item_Collection::add_item( $wishlist_id, $product_id, $quantity, $variation_id, $variations ) ) {
			$added_to_wishlist = true;
		}
	}

	return $added_to_wishlist;
}

function woocommerce_wishlist_add_to_cart_handler_grouped( $product_id, $wishlist_id ) {
	$was_added_to_cart = false;
	$added_to_cart     = array();

	if ( ! empty( $_REQUEST['quantity'] ) && is_array( $_REQUEST['quantity'] ) ) {
		$quantity_set = false;

		$items_to_add = array();
		foreach ( $_REQUEST['quantity'] as $item => $quantity ) {
			if ( $quantity <= 0 ) {
				continue;
			}
			$quantity_set = true;

			//Add to cart validation
			$passed_validation = apply_filters( 'woocommerce_add_to_wishlist_validation', apply_filters( 'woocommerce_add_to_cart_validation', true, $item, $quantity ), $item, $quantity );

			if ( $passed_validation ) {
				$items_to_add[] = array(
					'product_id' => $item,
					'quantity'   => $quantity
				);
			}
		}

		$was_added_to_cart = true;
		if ( ! empty( $items_to_add ) ) {
			foreach ( $items_to_add as $item_to_add ) {
				if ( WC_Wishlists_Wishlist_Item_Collection::add_item( $wishlist_id, $item_to_add['product_id'], $item_to_add['quantity'] ) ) {
					$was_added_to_cart = $was_added_to_cart & true;
				} else {
					$was_added_to_cart = false;
				}
			}
		} else {
			$quantity_set      = false;
			$was_added_to_cart = false;
		}


		if ( ! $was_added_to_cart && ! $quantity_set ) {
			wc_add_notice( __( 'Please choose the quantity of items you wish to add to your list&hellip;', 'wc_wishlist' ), 'error' );
		} elseif ( ! $was_added_to_cart ) {
			wc_add_notice( __( 'There was a problem adding items to your wishlist.  Please review and try again.&hellip;', 'wc_wishlist' ), 'error' );

		} elseif ( $was_added_to_cart ) {
			return true;
		}
	} elseif ( $product_id ) {
		/* Link on product archives */
		wc_add_notice( __( 'Please choose a product to add to your list&hellip;', 'wc_wishlist' ), 'error' );
	}

	return false;
}

function woocommerce_wishlist_add_to_wishlist_message( $wishlist_id = false ) {
	//Output succss messages
	if ( WC_Wishlists_Settings::get_setting( 'woocommerce_wishlist_redirect_after_add_to_cart', 'yes' ) == 'yes' ) :
		$return_to = ( wp_get_referer() ) ? wp_get_referer() : home_url();
		$message   = sprintf( '<a href="%s" class="button">%s</a> %s', $return_to, __( 'Continue Shopping &rarr;', 'wc_wishlist' ), __( 'Product successfully added to your wishlist.', 'wc_wishlist' ) );
	else :
		$title                  = ( get_the_title( $wishlist_id ) );
		$view_list_url          = WC_Wishlists_Wishlist::get_the_url_view( $wishlist_id );
		$edit_list_url          = WC_Wishlists_Wishlist::get_the_url_edit( $wishlist_id );
		$list_user_settings_url = $edit_list_url . '#tab-wl-settings';
		$success_message        = sprintf( __( 'Product successfully added to %s.', 'wc_wishlist' ), esc_html( $title ) );
		$message                = sprintf( '<a href="%s" class="button">%s</a> %s', $edit_list_url, __( 'Manage Wishlist &rarr;', 'wc_wishlist' ), $success_message );
	endif;
	WC_Wishlist_Compatibility::wc_add_notice( apply_filters( 'woocommerce_wishlist_add_to_wishlist_message', $message, $wishlist_id ) );
}

add_action( 'init', 'woocommerce_wishlist_handle_share_via_email_action', 9 );

function woocommerce_wishlist_handle_share_via_email_action() {
	global $phpmailer;

	if ( ! isset( $_POST['wishlist-action'] ) || ! ( $_POST['wishlist-action'] == 'share-via-email' ) ) {
		return;
	}

	if ( ! WC_Wishlists_Plugin::verify_nonce( 'share-via-email' ) ) {
		return;
	}

	$wishlist_id = filter_input( INPUT_POST, 'wishlist_id', FILTER_SANITIZE_NUMBER_INT );

	if ( ! $wishlist_id ) {
		WC_Wishlist_Compatibility::wc_add_notice( __( 'Action failed. Please refresh the page and retry.', 'woocommerce' ), 'error' );

		return;
	}

	$wishlist = new WC_Wishlists_Wishlist( $wishlist_id );
	if ( ! $wishlist ) {
		WC_Wishlist_Compatibility::wc_add_notice( __( 'Action failed. Please refresh the page and retry.', 'woocommerce' ), 'error' );

		return;
	}

	if ( $wishlist->get_wishlist_sharing() == 'Private' ) {
		WC_Wishlist_Compatibility::wc_add_notice( __( 'Unable to share a private list.', 'woocommerce' ), 'error' );

		return;
	}

	$name = filter_input( INPUT_POST, 'wishlist_email_from', FILTER_SANITIZE_STRING );

	$to      = filter_input( INPUT_POST, 'wishlist_email_to', FILTER_SANITIZE_STRIPPED );
	$content = filter_input( INPUT_POST, 'wishlist_content', FILTER_SANITIZE_STRIPPED );


	$sent = 0;
	if ( $to ) {
		$addresses = explode( ',', $to );
		array_map( 'trim', $addresses );
		$clean_addresses = array();
		foreach ( $addresses as $address ) {
			$clean_addresses[] = filter_var( $address, FILTER_SANITIZE_EMAIL );
		}

		$sent = true;
		if ( count( $clean_addresses ) ) {
			$email = WC_Emails::instance();

			foreach ( $clean_addresses as $clean_address ) {
				$sent = $sent & $email->emails['WC_Wishlists_Mail_Share_List']->trigger( $clean_address, $wishlist, $wishlist_id, $content, $name );
			}
		}
	}

	if ( $sent ) {
		WC_Wishlist_Compatibility::wc_add_notice( __( 'Your email has been sent', 'wc_wishlist' ) );
	} elseif ( $sent === false ) {
		WC_Wishlist_Compatibility::wc_add_notice( __( 'Unable to send mail.  Please check your values and try again.', 'wc_wishlist' ) . ' ' . $phpmailer->ErrorInfo, 'error' );
	} else {
		WC_Wishlist_Compatibility::wc_add_notice( __( 'Unable to send mail.  Please check your values and try again.', 'wc_wishlist' ), 'error' );
	}
}

function woocommerce_wishlist_get_from_address() {
	return sanitize_email( get_option( 'woocommerce_email_from_address' ) );
}

/* == Ajax Actions === */
add_action( 'wp_ajax_woocommerce_remove_wishlist_item', 'woocommerce_wishlist_ajax_remove_item' );

function woocommerce_wishlist_ajax_remove_item() {
	check_ajax_referer( 'wishlist-item', 'security' );

	$wishlist_id       = $_POST['wlid'];
	$wishlist_item_ids = $_POST['wishlist_item_ids'];

	if ( sizeof( $wishlist_item_ids ) > 0 ) {
		foreach ( $wishlist_item_ids as $id ) {
			WC_Wishlists_Wishlist_Item_Collection::remove_item( $wishlist_id, $id );
		}
	}

	die();
}

add_action( 'authenticate', 'woocommerce_wishlists_authenticate' );

function woocommerce_wishlists_authenticate( $user ) {
	global $wishlist_session_key, $woocommerce_wishlist_is_user_switched;

	if ( $woocommerce_wishlist_is_user_switched ) {
		return $user;
	}

	if ( ! is_user_logged_in() ) {
		$wishlist_session_key = WC_Wishlists_User::get_wishlist_key();
	}

	return $user;
}

add_action( 'wp_login', 'woocommerce_wishlists_wp_login', 10, 2 );

function woocommerce_wishlists_wp_login( $user_logon, $user ) {
	if ( ! is_admin() ) {
		global $wpdb, $woocommerce_wishlist_is_user_switched;
		if ( $woocommerce_wishlist_is_user_switched ) {
			return false;
		}

		$wishlist_session_key = WC_Wishlists_User::get_wishlist_key();
		if ( $wishlist_session_key ) {
			$lists = WC_Wishlists_User::get_wishlists( false, $wishlist_session_key );
			if ( $lists && count( $lists ) ) {
				foreach ( $lists as $list ) {
					WC_Wishlists_Wishlist::update_owner( $list->id, $user->ID, $wishlist_session_key );
				}
			}
		}

		$wpdb->query( "DELETE FROM $wpdb->options WHERE option_name LIKE '_transient_wc_wishlists_users_lists%'" );

	}
}

add_action( 'wp_logout', 'woocommerce_wishlists_logout' );

function woocommerce_wishlists_logout() {
	global $woocommerce_wishlist_is_user_switched;

	if ( $woocommerce_wishlist_is_user_switched ) {
		return;
	}

	if ( isset( $_COOKIE['wp-wc_wishlists_user'] ) ) {
		unset( $_COOKIE['wp-wc_wishlists_user'] );
		setcookie( 'wp-wc_wishlists_user', null, - 1, '/' );
	}
}

add_action( 'user_register', 'woocommerce_wishlists_register', 10, 1 );

function woocommerce_wishlists_register( $user_id ) {
	if ( ! is_admin() ) {
		global $woocommerce_wishlist_is_user_switched;
		if ( $woocommerce_wishlist_is_user_switched ) {
			return false;
		}

		$wishlist_session_key = WC_Wishlists_User::get_wishlist_key();
		if ( $wishlist_session_key ) {
			$lists = WC_Wishlists_User::get_wishlists( false, $wishlist_session_key );
			if ( $lists && count( $lists ) ) {
				foreach ( $lists as $list ) {
					WC_Wishlists_Wishlist::update_owner( $list->id, $user_id, $wishlist_session_key );
				}
			}
		}
	}
}

global $woocommerce_wishlist_is_user_switched;
$woocommerce_wishlist_is_user_switched = false;

add_action( 'switch_to_user', 'woocommerce_wishlists_on_user_switched' );
function woocommerce_wishlists_on_user_switched() {
	global $woocommerce_wishlist_is_user_switched;
	$woocommerce_wishlist_is_user_switched = true;
}


add_action( 'switch_back_user', 'woocommerce_wishlists_on_user_switched_back' );
function woocommerce_wishlists_on_user_switched_back() {
	global $woocommerce_wishlist_is_user_switched;
	$woocommerce_wishlist_is_user_switched = false;
}


if ( apply_filters( 'wc_wishlists_remove_auth_hooks', false ) ) {
	remove_action( 'authenticate', 'woocommerce_wishlists_authenticate' );
	remove_action( 'wp_login', 'woocommerce_wishlists_logon', 10, 2 );
	remove_action( 'wp_logout', 'woocommerce_wishlists_logout' );
	remove_action( 'user_register', 'woocommerce_wishlists_register', 10, 1 );
}
