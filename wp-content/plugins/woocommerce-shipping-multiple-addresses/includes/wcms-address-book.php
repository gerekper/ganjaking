<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class WC_MS_Address_Book {
    private $wcms;

    public function __construct( WC_Ship_Multiple $wcms ) {
        $this->wcms = $wcms;

        // delete address request
        add_action( 'template_redirect', array($this, 'delete_address') );

        add_action( 'template_redirect', array( $this, 'save_addresses' ) );
        add_action( 'template_redirect', array( $this, 'save_addresses_book_from_post' ) );
        add_action( 'template_redirect', array( $this, 'address_book' ) );

        add_action( 'wp_ajax_wc_ms_delete_address', array( $this, 'ajax_delete_address' ) );
        add_action( 'wp_ajax_nopriv_wc_ms_delete_address', array( $this, 'ajax_delete_address' ) );
        add_action( 'wp_ajax_wc_save_to_address_book', array( $this, 'save_address_book' ) );
        add_action( 'wp_ajax_nopriv_wc_save_to_address_book', array( $this, 'save_address_book' ) );
    }

    /**
     * Get the user's default address
     * @param int $user_id
     * @return array
     */
    public function get_user_default_address( $user_id ) {
        $default_address = array(
            'shipping_first_name' 	=> get_user_meta( $user_id, 'shipping_first_name', true ),
            'shipping_last_name'	=> get_user_meta( $user_id, 'shipping_last_name', true ),
            'shipping_company'		=> get_user_meta( $user_id, 'shipping_company', true ),
            'shipping_address_1'	=> get_user_meta( $user_id, 'shipping_address_1', true ),
            'shipping_address_2'	=> get_user_meta( $user_id, 'shipping_address_2', true ),
            'shipping_city'			=> get_user_meta( $user_id, 'shipping_city', true ),
            'shipping_state'		=> get_user_meta( $user_id, 'shipping_state', true ),
            'shipping_postcode'		=> get_user_meta( $user_id, 'shipping_postcode', true ),
            'shipping_country'		=> get_user_meta( $user_id, 'shipping_country', true ),
            'default_address'       => true
        );

        // backwards compatibility
        $default_address['first_name'] 	= $default_address['shipping_first_name'];
        $default_address['last_name']	= $default_address['shipping_last_name'];
        $default_address['company']		= $default_address['shipping_company'];
        $default_address['address_1']	= $default_address['shipping_address_1'];
        $default_address['address_2']	= $default_address['shipping_address_2'];
        $default_address['city']		= $default_address['shipping_city'];
        $default_address['state']		= $default_address['shipping_state'];
        $default_address['postcode']	= $default_address['shipping_postcode'];
        $default_address['country']     = $default_address['shipping_country'];

        return apply_filters( 'wc_ms_default_user_address', $default_address );
    }

    public function delete_address() {

        $user = wp_get_current_user();

        if ( isset($_REQUEST['address-delete']) && isset($_REQUEST['id']) ) {
            $id         = $_REQUEST['id'];
            $addresses  = $this->get_user_addresses( $user );

            if ($user->ID != 0) {
                $addresses = get_user_meta($user->ID, 'wc_other_addresses', true);

                if (! $addresses) {
                    $addresses = array();
                }

                $default_address = $this->get_user_default_address( $user->ID );

                if ( $default_address['address_1'] && $default_address['postcode'] ) {
                    array_unshift($addresses, $default_address);
                }

                if ( $id == 0 ) {
                    $default_address = $addresses[0];

                    if ( $default_address['shipping_address_1'] && $default_address['shipping_postcode'] ) {
                        update_user_meta( $user->ID, 'shipping_first_name', '' );
                        update_user_meta( $user->ID, 'shipping_last_name',  '' );
                        update_user_meta( $user->ID, 'shipping_company',    '' );
                        update_user_meta( $user->ID, 'shipping_address_1',  '' );
                        update_user_meta( $user->ID, 'shipping_address_2',  '' );
                        update_user_meta( $user->ID, 'shipping_city',       '' );
                        update_user_meta( $user->ID, 'shipping_state',      '' );
                        update_user_meta( $user->ID, 'shipping_postcode',   '' );
                        update_user_meta( $user->ID, 'shipping_country',    '' );
                    }
                } else {
                    unset( $addresses[ $id ] );
                }

                unset( $addresses[0] );

                update_user_meta($user->ID, 'wc_other_addresses', $addresses);

            } else {
                // guests
                unset( $addresses[ $id ] );
                wcms_session_set( 'user_addresses', $addresses );

            }

            if ( function_exists('wc_add_notice') )
                wc_add_notice(__('Address deleted', 'wc_shipping_multiple_address'), 'success');
            else
                WC()->add_message( __('Address deleted', 'wc_shipping_multiple_address') );

            wp_redirect( get_permalink( wc_get_page_id('multiple_addresses') ) );
            exit;

        }
    }

    public function save_addresses() {

        if (isset($_POST['shipping_address_action']) && $_POST['shipping_address_action'] == 'save' ) {
            /* @var $cart WC_Cart */
            $cart       = WC()->cart;
            $checkout   = WC()->checkout;

            $user_addresses = $this->get_user_addresses( get_current_user_id() );

            $fields = WC()->countries->get_address_fields( WC()->countries->get_base_country(), 'shipping_' );

            $cart->get_cart_from_session();
            $cart_items = wcms_get_real_cart_items();

            $data   = array();
            $rel    = array();

            if ( isset($_POST['items']) ) {

                $items = $_POST['items'];

                // handler for delete requests
                if ( isset($_POST['delete_line']) ) {
                    $delete     = $_POST['delete'];
                    $cart_key   = $delete['key'];
                    $index      = $delete['index'];

                    // trim the quantity by 1 and remove the corresponding address
                    $cart_items = wcms_get_real_cart_items();
                    $item_qty   = $cart_items[$cart_key]['quantity'] - 1;
                    $cart->set_quantity( $cart_key, $item_qty );

                    if ( isset($items[$cart_key]['qty'][$index]) ) {
                        unset( $items[$cart_key]['qty'][$index] );
                    }

                    if ( isset($items[$cart_key]['address'][$index]) ) {
                        unset( $items[$cart_key]['address'][$index] );
                    }
                }

                // handler for quantities update
                foreach ( $items as $cart_key => $item ) {
                    $qtys           = $item['qty'];
                    $item_addresses = $item['address'];

                    foreach ( $item_addresses as $idx => $item_address ) {
                        $cart_items     = wcms_get_real_cart_items();
                        $new_qty        = false;

                        if ( $qtys[ $idx ] == 0 ) {
                            // decrement the cart item quantity by one
                            $current_qty = $cart_items[ $cart_key ]['quantity'];
                            $new_qty        = $current_qty - 1;
                            $cart->set_quantity( $cart_key, $new_qty );
                        } elseif ( $qtys[ $idx ] > 1 ) {
                            $qty_to_add = $qtys[$idx] - 1;
                            $item_qty   = $cart_items[$cart_key]['quantity'];
                            $new_qty    = $item_qty + $qty_to_add;
                            $cart->set_quantity( $cart_key, $new_qty );
                        }

                    }

                }

                $cart_items = wcms_get_real_cart_items();
                foreach ( $items as $cart_key => $item ) {
                    $qtys           = $item['qty'];
                    $item_addresses = $item['address'];

                    $product_id = $cart_items[$cart_key]['product_id'];
                    $sig        = $cart_key .'_'. $product_id .'_';
                    $_sig       = '';

                    foreach ( $item_addresses as $idx => $item_address ) {
                        $address_id = $item_address;
                        $user_address = $user_addresses[ $address_id ];

                        $i = 1;
                        for ( $x = 0; $x < $qtys[$idx]; $x++ ) {

                            $rel[ $address_id ][]  = $cart_key;

                            while ( isset($data['shipping_first_name_'. $sig . $i]) ) {
                                $i++;
                            }
                            $_sig = $sig . $i;

                            if ( $fields ) foreach ( $fields as $key => $field ) :
                                $data[$key .'_'. $_sig] = $user_address[ $key ];
                            endforeach;
                        }

                    }

                    $cart_address_ids_session = (array)wcms_session_get( 'cart_address_ids' );

                    if ( !empty($_sig) && !wcms_session_isset( 'cart_address_ids' ) || ! in_array($_sig, $cart_address_ids_session) ) {
                        $cart_address_sigs_session = wcms_session_get( 'cart_address_sigs' );
                        $cart_address_sigs_session[$_sig] = $address_id;
                        wcms_session_set( 'cart_address_sigs', $cart_address_sigs_session);
                    }

                }

            }

            wcms_session_set( 'cart_item_addresses', $data );
            wcms_session_set( 'address_relationships', $rel );
            wcms_session_set( 'wcms_item_addresses', $rel );

            if ( isset($_POST['update_quantities']) || isset($_POST['delete_line']) ) {
                $next_url = get_permalink( wc_get_page_id( 'multiple_addresses' ) );
            } else {
                // redirect to the checkout page
                $next_url = wc_get_checkout_url();
            }

            $this->wcms->clear_packages_cache();

            wp_redirect($next_url);
            exit;
        } elseif (isset($_POST['shipping_account_address_action']) && $_POST['shipping_account_address_action'] == 'save' ) {
            $user   = wp_get_current_user();
            $idx    = $_POST['idx'];

            $addresses = get_user_meta( $user->ID, 'wc_other_addresses', true );

            if ( !is_array( $addresses ) ) {
                $addresses = array();
            }

            if ( $idx == -1 ) {
                $idx = count( $addresses );

                while ( array_key_exists( $idx, $addresses ) ) {
                    $idx++;
                }
            }

            unset($_POST['shipping_account_address_action'], $_POST['set_addresses'], $_POST['idx']);

            foreach ($_POST as $key => $value) {
                $addresses[ $idx ][ $key ] = $value;
            }


            update_user_meta( $user->ID, 'wc_other_addresses', $addresses );

            if ( function_exists('wc_add_notice') )
                wc_add_notice( __( 'Address saved', 'wc_shipping_multiple_address' ), 'success' );
            else
                WC()->add_message(__( 'Address saved', 'wc_shipping_multiple_address' ) );

            $page_id = wc_get_page_id( 'myaccount' );
            wp_redirect(get_permalink($page_id));
            exit;
        }
    }

    public function save_addresses_book_from_post() {
        if ( !empty( $_POST['action'] ) && $_POST['action'] == 'save_to_address_book' ) {

            $user       = wp_get_current_user();
            $id         = $_POST['id'];
            $address    = $_POST['address'];
            $addresses  = $this->get_user_addresses( $user );
            $shipFields = WC()->countries->get_address_fields( $address['shipping_country'], 'shipping_' );
            $errors     = array();
            $redirect_url   = (isset($_POST['next'])) ? $_POST['next'] : get_permalink( wc_get_page_id('multiple_addresses') );

            foreach ( $shipFields as $key => $field ) {

                if ( isset($field['required']) && $field['required'] && empty($address[$key]) ) {
                    if ( 'shipping_state' == $key && empty( WC()->countries->get_states( $address['shipping_country'] ) ) ) {
                        continue;
                    }

                    $errors[] = $key;
                }

                if (! empty($address[$key]) ) {

                    // Validation rules
                    if ( ! empty( $field['validate'] ) && is_array( $field['validate'] ) ) {
                        foreach ( $field['validate'] as $rule ) {
                            switch ( $rule ) {
                                case 'postcode' :
                                    $address[ $key ] = trim( $address[ $key ] );

                                    if ( ! WC_Validation::is_postcode( $address[ $key ], $address[ 'shipping_country' ] ) ) :
                                        $errors[] = $key;
                                        wc_add_notice( __( 'Please enter a valid postcode/ZIP.', 'wc_shipping_multiple_address' ), 'error' );
                                    else :
                                        $address[ $key ] = wc_format_postcode( $address[ $key ], $address[ 'shipping_country' ] );
                                    endif;
                                    break;
                                case 'phone' :
                                    $address[ $key ] = wc_format_phone_number( $address[ $key ] );

                                    if ( ! WC_Validation::is_phone( $address[ $key ] ) ) {
                                        $errors[] = $key;

                                        if ( function_exists('wc_add_notice') )
                                            wc_add_notice( '<strong>' . $field['label'] . '</strong> ' . __( 'is not a valid phone number.', 'wc_shipping_multiple_address' ), 'error' );
                                        else
                                            WC()->add_error('<strong>' . $field['label'] . '</strong> ' . __( 'is not a valid phone number.', 'wc_shipping_multiple_address' ));
                                    }

                                    break;
                                case 'email' :
                                    $address[ $key ] = strtolower( $address[ $key ] );

                                    if ( ! is_email( $address[ $key ] ) ) {
                                        $errors[] = $key;

                                        if ( function_exists('wc_add_notice') )
                                            wc_add_notice( '<strong>' . $field['label'] . '</strong> ' . __( 'is not a valid email address.', 'wc_shipping_multiple_address' ), 'error' );
                                        else
                                            WC()->add_error( '<strong>' . $field['label'] . '</strong> ' . __( 'is not a valid email address.', 'wc_shipping_multiple_address' ) );
                                    }

                                    break;
                                case 'state' :
                                    // Get valid states
                                    $valid_states = WC()->countries->get_states( $address[ 'shipping_country' ] );
                                    if ( $valid_states )
                                        $valid_state_values = array_flip( array_map( 'strtolower', $valid_states ) );

                                    // Convert value to key if set
                                    if ( isset( $valid_state_values[ strtolower( $address[ $key ] ) ] ) )
                                        $address[ $key ] = $valid_state_values[ strtolower( $address[ $key ] ) ];

                                    // Only validate if the country has specific state options
                                    if ( is_array($valid_states) && sizeof( $valid_states ) > 0 )
                                        if ( ! in_array( $address[ $key ], array_keys( $valid_states ) ) ) {
                                            $errors[] = $key;

                                            if ( function_exists('wc_add_notice') )
                                                wc_add_notice( '<strong>' . $field['label'] . '</strong> ' . __( 'is not valid. Please enter one of the following:', 'wc_shipping_multiple_address' ) . ' ' . implode( ', ', $valid_states ), 'error' );
                                            else
                                                WC()->add_error('<strong>' . $field['label'] . '</strong> ' . __( 'is not valid. Please enter one of the following:', 'wc_shipping_multiple_address' ) . ' ' . implode( ', ', $valid_states ));
                                        }

                                    break;
                            }
                        }
                    }

                }

            }

            if ( count($errors) > 0 ) {
                if ( function_exists( 'wc_add_notice' ) ) {
                    wc_add_notice( __( 'Please enter the complete address', 'wc_shipping_multiple_address' ), 'error' );
                } else {
                    WC()->add_error( __( 'Please enter the complete address', 'wc_shipping_multiple_address' ) );
                }
                $next = add_query_arg( $address, $redirect_url );
                wp_redirect( add_query_arg('address-form', 1, $next ) );
                exit;
            }

            // address is unique, save!
            if ( $id == -1 ) {
                $vals = '';
                foreach ($address as $key => $value) {
                    $vals .= $value;
                }
                $md5 = md5($vals);

                foreach ($addresses as $addr) {
                    $vals = '';
                    if( !is_array($addr) ) { continue; }
                    foreach ($addr as $key => $value) {
                        $vals .= $value;
                    }
                    $addrMd5 = md5($vals);

                    if ($md5 == $addrMd5) {
                        // duplicate address!
                        if ( function_exists( 'wc_add_notice' ) ) {
                            wc_add_notice( __( 'Address is already in your address book', 'wc_shipping_multiple_address' ), 'error' );
                        } else {
                            WC()->add_error( __( 'Address is already in your address book', 'wc_shipping_multiple_address' ) );
                        }
                        $next = add_query_arg( $address, $redirect_url );
                        wp_redirect( add_query_arg('address-form', 1, $next ) );
                        exit;
                    }
                }

                $addresses[] = $address;
            } else {
                $addresses[$id] = $address;
            }

            // update the default address and remove it from the $addresses array
            if ( $user->ID > 0 ) {
                if ( $id == 0 ) {
                    $default_address = $addresses[0];
                    unset( $addresses[0] );

                    if ( $default_address['shipping_address_1'] && $default_address['shipping_postcode'] ) {
                        update_user_meta( $user->ID, 'shipping_first_name', $default_address['shipping_first_name'] );
                        update_user_meta( $user->ID, 'shipping_last_name',  $default_address['shipping_last_name'] );
                        update_user_meta( $user->ID, 'shipping_company',    $default_address['shipping_company'] );
                        update_user_meta( $user->ID, 'shipping_address_1',  $default_address['shipping_address_1'] );
                        update_user_meta( $user->ID, 'shipping_address_2',  $default_address['shipping_address_2'] );
                        update_user_meta( $user->ID, 'shipping_city',       $default_address['shipping_city'] );
                        update_user_meta( $user->ID, 'shipping_state',      $default_address['shipping_state'] );
                        update_user_meta( $user->ID, 'shipping_postcode',   $default_address['shipping_postcode'] );
                        update_user_meta( $user->ID, 'shipping_country',    $default_address['shipping_country'] );
                    }
                    unset( $addresses[0] );
                }

            }

            $this->save_user_addresses( $user->ID, $addresses );

            if ( $id >= 0 ) {
                $next = add_query_arg( 'updated', '1', $redirect_url );
            } else {
                $next = add_query_arg( 'new', '1', $redirect_url );
            }

            wp_redirect( $next );
            exit;
        }
    }

    public function address_book() {
        $user = wp_get_current_user();

        if ($user->ID == 0) {
            return;
        }

        if (isset($_GET['addressbook']) && $_GET['addressbook'] == 1) {
            $addresses = get_user_meta($user->ID, 'wc_other_addresses', true);
            ?>
            <p></p>
            <h2><?php _e( 'Address Book', 'wc_shipping_multiple_address' ); ?></h2>
            <?php
            if (!empty($addresses)):
                foreach ($addresses as $addr) {
                    if ( empty($addr) ) continue;

                    echo '<div style="float: left; width: 200px;">';
                    $address = array(
                        'first_name'    => $addr['shipping_first_name'],
                        'last_name'     => $addr['shipping_last_name'],
                        'company'       => $addr['shipping_company'],
                        'address_1'     => $addr['shipping_address_1'],
                        'address_2'     => $addr['shipping_address_2'],
                        'city'          => $addr['shipping_city'],
                        'state'         => $addr['shipping_state'],
                        'postcode'      => $addr['shipping_postcode'],
                        'country'       => $addr['shipping_country']
                    );
                    $formatted_address  = wcms_get_formatted_address( $address );
                    $json_address       = wp_json_encode( $address );

                    if (!$formatted_address) _e( 'You have not set up a shipping address yet.', 'wc_shipping_multiple_address' ); else echo '<address>'.$formatted_address.'</address>';
                    echo '  <textarea style="display:none;">'. $json_address .'</textarea>';
                    echo '  <p><button type="button" class="button address-use">'. __( 'Use this address', 'wc_shipping_multiple_address' ) .'</button></p>';
                    echo '</div>';
                }
                echo '<div class="clear: both;"></div>';
            else:
                echo '<h4>'. __( 'You have no shipping addresses saved.', 'wc_shipping_multiple_address' ) .'</h4>';
            endif;
            ?>
            <script type="text/javascript">
                jQuery(document).ready(function() {
                    jQuery( '.address-use' ).click(function() {
                        var address = jQuery.parseJSON(jQuery(this).parents( 'p' ).prev( 'textarea' ).val());
                        jQuery(this).prop( 'disabled', true);

                        setAddress(address, '<?php echo $_GET['sig']; ?>' );
                        tb_remove();
                    });
                });
            </script>
            <?php
            exit;
        }
    }

    public function ajax_delete_address() {
        $user       = wp_get_current_user();
        $idx        = absint( $_POST['idx'] );
        $addresses  = $this->get_user_addresses( $user );

        unset( $addresses[ $idx ] );

        $this->save_user_addresses( $user->ID, $addresses );

        wp_send_json( array('ack' => 'OK') );
        exit;
    }

    function get_user_addresses( $user, $include_default = true ) {
        if (! $user instanceof WP_User ) {
            $user = new WP_User( $user );
        }

        if ($user->ID != 0) {
            $addresses = get_user_meta($user->ID, 'wc_other_addresses', true);

            if (! $addresses) {
                $addresses = array();
            }

	        if ( $include_default ) {
		        $default_address = $this->get_user_default_address( $user->ID );

		        if ( $default_address['address_1'] && $default_address['postcode'] ) {
			        $addresses += array( $default_address );
		        }
	        }
        } else {
            // guest address - using sessions to store the address
            $addresses = ( wcms_session_isset('user_addresses') ) ? wcms_session_get('user_addresses') : array();
        }

        return $this->array_sort( $addresses, 'shipping_first_name' );
    }

    function array_sort($array, $on, $order=SORT_ASC) {
        $new_array = array();
        $sortable_array = array();

        if (count($array) > 0) {
            foreach ($array as $k => $v) {
                if (is_array($v)) {
                    foreach ($v as $k2 => $v2) {
                        if ($k2 == $on) {
                            $sortable_array[$k] = $v2;
                        }
                    }
                } else {
                    $sortable_array[$k] = $v;
                }
            }

            switch ($order) {
                case SORT_ASC:
                    asort( $sortable_array, SORT_NATURAL | SORT_FLAG_CASE );
                    break;
                case SORT_DESC:
                    arsort( $sortable_array, SORT_NATURAL | SORT_FLAG_CASE  );
                    break;
            }

            foreach ($sortable_array as $k => $v) {
                $new_array[$k] = $array[$k];
            }
        }

        return $new_array;
    }

	/**
	 * Save user addresses to account or session
	 * Removes the default addresses and any duplicate addresses
	 *
	 * @param  integer  $user_id    Customer user ID
	 * @param  array    $addresses  List of user addresses
	 */
	public function save_user_addresses( $user_id, $addresses ) {

		$keys = array();
		foreach ( $addresses as $index => $address ) {
			if ( ! empty( $address['default_address'] ) ) {
				// Remove default address
				unset( $addresses[ $index ] );
			} elseif ( $key = $this->unique_address_key( $address ) ) {
				// Save unique address key
				$keys[ $index ] = $key;
			} else {
				// Remove empty address
				unset( $addresses[ $index ] );
			}
		}

		// Remove any duplicate addresses
		$duplicates = array_diff_assoc( $keys, array_unique( $keys ) );
		foreach( array_keys( $duplicates ) as $index ) {
			unset( $addresses[ $index ] );
		}

		if ( $user_id > 0 ) {
			update_user_meta( $user_id, 'wc_other_addresses', $addresses );
		} else {
			wcms_session_set( 'user_addresses', $addresses );
		}
	}

	/**
	 * Generate a unique key for an address
	 *
	 * @param  array   $address  Address field values
	 * @return string            Unique key
	 */
	public function unique_address_key( $address ) {

		if ( empty( $address ) || ! is_array( $address ) ) {
			return false;
		}

		return md5( implode( '_', $address ) );
	}

    public function save_address_book() {

        $this->wcms->cart->load_cart_files();

        $checkout   = WC()->checkout;
        $user       = wp_get_current_user();

        $address    = $_POST['address'];
        $shipFields = WC()->countries->get_address_fields( $address['shipping_country'], 'shipping_' );
        $errors     = array();

        foreach ( $shipFields as $key => $field ) {

            if ( isset($field['required']) && $field['required'] && empty($address[$key]) ) {
                if ( 'shipping_state' == $key && empty( WC()->countries->get_states( $address['shipping_country'] ) ) ) {
                    continue;
                }

                $errors[] = $key;
            }

            if (! empty($address[$key]) ) {

                // Validation rules
                if ( ! empty( $field['validate'] ) && is_array( $field['validate'] ) ) {
                    foreach ( $field['validate'] as $rule ) {
                        switch ( $rule ) {
                            case 'postcode' :
                                $address[ $key ] = trim( $address[ $key ] );

                                if ( ! WC_Validation::is_postcode( $address[ $key ], $address[ 'shipping_country' ] ) ) :
                                    $errors[] = $key;
                                    wc_add_notice( __( 'Please enter a valid postcode/ZIP.', 'wc_shipping_multiple_address' ), 'error' );
                                else :
                                    $address[ $key ] = wc_format_postcode( $address[ $key ], $address[ 'shipping_country' ] );
                                endif;
                                break;
                            case 'phone' :
                                $address[ $key ] = wc_format_phone_number( $address[ $key ] );

                                if ( ! WC_Validation::is_phone( $address[ $key ] ) ) {
                                    $errors[] = $key;

                                    if ( function_exists('wc_add_notice') )
                                        wc_add_notice( '<strong>' . $field['label'] . '</strong> ' . __( 'is not a valid phone number.', 'wc_shipping_multiple_address' ), 'error' );
                                    else
                                        WC()->add_error('<strong>' . $field['label'] . '</strong> ' . __( 'is not a valid phone number.', 'wc_shipping_multiple_address' ));
                                }

                                break;
                            case 'email' :
                                $address[ $key ] = strtolower( $address[ $key ] );

                                if ( ! is_email( $address[ $key ] ) ) {
                                    $errors[] = $key;

                                    if ( function_exists('wc_add_notice') )
                                        wc_add_notice( '<strong>' . $field['label'] . '</strong> ' . __( 'is not a valid email address.', 'wc_shipping_multiple_address' ), 'error' );
                                    else
                                        WC()->add_error( '<strong>' . $field['label'] . '</strong> ' . __( 'is not a valid email address.', 'wc_shipping_multiple_address' ) );
                                }

                                break;
                            case 'state' :
                                // Get valid states
                                $valid_states = WC()->countries->get_states( $address[ 'shipping_country' ] );
                                if ( $valid_states )
                                    $valid_state_values = array_flip( array_map( 'strtolower', $valid_states ) );

                                // Convert value to key if set
                                if ( isset( $valid_state_values[ strtolower( $address[ $key ] ) ] ) )
                                    $address[ $key ] = $valid_state_values[ strtolower( $address[ $key ] ) ];

                                // Only validate if the country has specific state options
                                if ( is_array($valid_states) && sizeof( $valid_states ) > 0 )
                                    if ( ! in_array( $address[ $key ], array_keys( $valid_states ) ) ) {
                                        $errors[] = $key;

                                        if ( function_exists('wc_add_notice') )
                                            wc_add_notice( '<strong>' . $field['label'] . '</strong> ' . __( 'is not valid. Please enter one of the following:', 'wc_shipping_multiple_address' ) . ' ' . implode( ', ', $valid_states ), 'error' );
                                        else
                                            WC()->add_error('<strong>' . $field['label'] . '</strong> ' . __( 'is not valid. Please enter one of the following:', 'wc_shipping_multiple_address' ) . ' ' . implode( ', ', $valid_states ));
                                    }

                                break;
                        }
                    }
                }

            }

        }

        if ( count($errors) > 0 ) {
            die( wp_json_encode( array( 'ack' => 'ERR', 'errors' => $errors, 'message' => __( 'Please enter the complete address', 'wc_shipping_multiple_address' ) ) ) );
        }

        $id  = $_POST['id'];

        $addresses  = $this->get_user_addresses( $user );

        $redirect_url = (isset($_POST['next'])) ? $_POST['next'] : get_permalink( wc_get_page_id('multiple_addresses') );

        if ( $id >= 0 )
            $next = add_query_arg( 'updated', '1', $redirect_url );
        else
            $next = add_query_arg( 'new', '1', $redirect_url );

        // address is unique, save!
        if ( $id == -1 ) {
            $vals = '';
            foreach ($address as $key => $value) {
                $vals .= $value;
            }
            $md5 = md5($vals);

            foreach ($addresses as $addr) {
                $vals = '';
                if( !is_array($addr) ) { continue; }
                foreach ($addr as $key => $value) {
                    $vals .= $value;
                }
                $addrMd5 = md5($vals);

                if ($md5 == $addrMd5) {
                    // duplicate address!
                    die( wp_json_encode( array( 'ack' => 'ERR', 'message' => __( 'Address is already in your address book', 'wc_shipping_multiple_address' ) ) ) );
                }
            }

            $addresses[] = $address;
        } else {
            $addresses[$id] = $address;
        }

        // update the default address and remove it from the $addresses array
        if ( $user->ID > 0 ) {
            if ( $id == 0 ) {
                $default_address = $addresses[0];
                unset( $addresses[0] );

                if ( $default_address['shipping_address_1'] && $default_address['shipping_postcode'] ) {
                    update_user_meta( $user->ID, 'shipping_first_name', $default_address['shipping_first_name'] );
                    update_user_meta( $user->ID, 'shipping_last_name',  $default_address['shipping_last_name'] );
                    update_user_meta( $user->ID, 'shipping_company',    $default_address['shipping_company'] );
                    update_user_meta( $user->ID, 'shipping_address_1',  $default_address['shipping_address_1'] );
                    update_user_meta( $user->ID, 'shipping_address_2',  $default_address['shipping_address_2'] );
                    update_user_meta( $user->ID, 'shipping_city',       $default_address['shipping_city'] );
                    update_user_meta( $user->ID, 'shipping_state',      $default_address['shipping_state'] );
                    update_user_meta( $user->ID, 'shipping_postcode',   $default_address['shipping_postcode'] );
                    update_user_meta( $user->ID, 'shipping_country',    $default_address['shipping_country'] );
                }
                unset( $addresses[0] );
            }

        }

        $this->save_user_addresses( $user->ID, $addresses );

        foreach ( $address as $key => $value ) {
            $new_key = str_replace( 'shipping_', '', $key);
            $address[$new_key] = $value;
        }

        $formatted_address  = wcms_get_formatted_address( $address );
        $json_address       = wp_json_encode( $address );

        if (!$formatted_address) return;

        if ( isset($_POST['return']) && $_POST['return'] == 'list' ) {
            $html = '<option value="'. $id .'">'. $formatted_address .'</option>';
        } else {
            $html = '
                    <div class="account-address">
                        <address>'. $formatted_address .'</address>
                        <div style="display: none;">';

            ob_start();
            foreach ($shipFields as $key => $field) :
                $val = (isset($address[$key])) ? $address[$key] : '';
                $key .= '_'. $id;

                woocommerce_form_field( $key, $field, $val );
            endforeach;

            do_action( 'woocommerce_after_checkout_shipping_form', $checkout);
            $html .= ob_get_clean();

            $html .= '
                            <input type="hidden" name="addresses[]" value="'. $id .'" />
                        </div>

                        <ul class="items-column" id="items_column_'. $id .'">
                            <li class="placeholder">' . __( 'Drag items here', 'wc_shipping_multiple_address' ) . '</li>
                        </ul>
                    </div>
                    ';
        }

        $return = wp_json_encode( array( 'ack' => 'OK', 'id' => $id, 'html' => $html, 'return' => $_POST['return'], 'next' => $next ) );
        die($return);

    }
}
