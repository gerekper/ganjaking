<?php
/*
 * This file belongs to the YIT Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */

if ( !function_exists( 'yith_wcpv_get_template' ) ) {
    /**
     * Get Plugin Template
     *
     * It's possible to overwrite the template from theme.
     * Put your custom template in woocommerce/product-vendors folder
     *
     * @param        $filename
     * @param array  $args
     * @param string $section
     *
     * @use   wc_get_template()
     * @since 1.0
     * @return void
     */
    function yith_wcpv_get_template( $filename, $args = array(), $section = '' ) {

        $ext           = strpos( $filename, '.php' ) === false ? '.php' : '';
        $template_name = $section . '/' . $filename . $ext;
        $template_path = WC()->template_path();
        $default_path  = YITH_WPV_TEMPLATE_PATH;

        if ( defined( 'YITH_WPV_PREMIUM' ) ) {
            $premium_template = str_replace( '.php', '-premium.php', $template_name );
            $located_premium  = wc_locate_template( $premium_template, $template_path, $default_path );
            $template_name    = file_exists( $located_premium ) ? $premium_template : $template_name;
        }

        wc_get_template( $template_name, $args, $template_path, $default_path );
    }
}

if ( !function_exists( 'yith_wcpv_check_duplicate_term_name' ) ) {
    /**
     * Check for duplicate vendor name
     *
     * @author   Andrea Grillo <andrea.grillo@yithemes.com>
     *
     * @param $term     string The term name
     * @param $taxonomy string The taxonomy name
     *
     * @return mixed term object | WP_Error
     * @since    1.0
     */
    function yith_wcpv_check_duplicate_term_name( $term, $taxonomy ) {
        $duplicate = get_term_by( 'name', $term, $taxonomy );

        return $duplicate ? true : false;
    }
}

if ( !function_exists( 'yith_wcmv_is_premium' ) ) {
    /**
     * Check if this is the premium version
     *
     * @author Leanza Francesco <leanzafrancesco@gmail.com>
     * @since  1.0
     * @return bool
     */
    function yith_wcmv_is_premium() {
        return defined( 'YITH_WPV_PREMIUM' ) && YITH_WPV_PREMIUM;
    }
}

if ( !function_exists( 'yith_wcmv_create_capabilities' ) ) {
    /**
     * create a capability array
     *
     * @author Leanza Francesco <leanzafrancesco@gmail.com>
     * @since  1.0
     * @return array
     */
    function yith_wcmv_create_capabilities( $capability_type ) {
        if ( !is_array( $capability_type ) )
            $capability_type = array( $capability_type, $capability_type . 's' );

        list( $singular_base, $plural_base ) = $capability_type;

        $capabilities = array(
            'edit_' . $singular_base           => true,
            'read_' . $singular_base           => true,
            'delete_' . $singular_base         => true,
            'edit_' . $plural_base             => true,
            'edit_others_' . $plural_base      => true,
            'publish_' . $plural_base          => true,
            'read_private_' . $plural_base     => true,
            'delete_' . $plural_base           => true,
            'delete_private_' . $plural_base   => true,
            'delete_published_' . $plural_base => true,
            'delete_others_' . $plural_base    => true,
            'edit_private_' . $plural_base     => true,
            'edit_published_' . $plural_base   => true,
        );

        return $capabilities;
    }
}

if ( !function_exists( 'yith_wcmv_get_wpml_vendor_id' ) ) {
    /**
     * Get original vendor id
     *
     * @author   Andrea Grillo <andrea.grillo@yithemes.com>
     *
     * @param $vendor mixed vendor id or vendor object
     *
     * @return  string vendor id
     * @since   1.11.2
     */
    function yith_wcmv_get_wpml_vendor_id( $vendor_id, $id_type = 'original_language' ) {
        /**
         * WPML Support
         */
	    $has_wpml = apply_filters( 'wpml_setting', false, 'setup_complete' );
	    if( $has_wpml ){
		    $type    = apply_filters( 'wpml_element_type', YITH_Vendors()->get_taxonomy_name() );
		    $trid    = apply_filters( 'wpml_element_trid', null, $vendor_id, $type );
		    $vendors = apply_filters( 'wpml_get_element_translations', array(), $trid, $type );
		    $current_language = apply_filters( 'wpml_current_language', '' );

		    if( 'original_language' == $id_type ){
			    foreach ( $vendors as $vendor ) {
				    if ( isset( $vendor->original ) && true === $vendor->original ) {
					    $vendor_id = $vendor->element_id;
				    }
			    }
		    }

		    elseif( 'current_language' == $id_type ){
		    	if( ! empty( $vendors[ $current_language ] ) ){
		    		$vendor = $vendors[ $current_language ];
				    $vendor_id = $vendor->element_id;
			    }
		    }
	    }

	    return $vendor_id;
    }
}

if( ! function_exists( 'yith_wcmv_get_email_order_number' ) ){
    /**
     * Get order number
     *
     * @author   Andrea Grillo <andrea.grillo@yithemes.com>
     *
     * @return  WC_Order
     * @since   1.12
     */
    function yith_wcmv_get_email_order_number( $order, $parent = false ){
        $order_number = '';
        if( $parent  ){
            $order_id = yit_get_prop( $order, 'id' );
            $parent_order_id = get_post_field( 'post_parent', $order_id );
            $parent_order_id = !empty( $parent_order_id ) ? $parent_order_id : $order_id;
            $parent_order = wc_get_order( $parent_order_id );
            if( $parent_order instanceof WC_Order ){
	            $order_number = $parent_order->get_order_number();
            }
        }

        else {
	        if( $order instanceof WC_Order ){
		        $order_number = $order->get_order_number();

	        }
        }

        return $order_number;
    }
}

if( ! function_exists( 'yith_wcmv_show_gravatar' ) ){
    /**
     * Show avatar or not in frontend vendor page
     *
     * @author Andrea Grillo <andrea.grillo@yithemes.com>
     * @param null $vendor
     * @return bool
     */
    function yith_wcmv_show_gravatar( $vendor = null, $where = 'admin' ){
        $show_gravatar = false;

        if( ! $vendor ){
            $vendor = yith_get_vendor( 'current', 'user' );
        }

        switch( get_option( 'yith_vendors_show_gravatar_image', 'enabled' ) ){
            case 'enabled':
                $show_gravatar = true;
                break;

            case 'disabled':
                $show_gravatar = false;
                break;

            case 'vendor':
                $show_gravatar = 'admin' == $where ? true : 'yes' == $vendor->show_gravatar;
                break;
        }

        return $show_gravatar;
    }
}

if( ! function_exists( 'yith_wcmv_get_order_status' ) ){
    /**
     * Get the order status for retro compatibility
     *
     * @author Andrea Grillo <andrea.grillo@yithemes.com>
     * @param $order
     * @param string $context
     * @return string order status
     */
    function yith_wcmv_get_order_status( $order, $context = 'edit' ){
        $order_status = yit_get_prop( $order, 'post_status', true );

        if( YITH_Vendors()->is_wc_2_7_or_greather ){
            //set the status in old wc style
            $order_status = 'wc-' . $order_status;
        }

        if( 'display' == $context ){
            $wc_order_status = wc_get_order_statuses();
            $order_status = isset( $wc_order_status[ $order_status ] ) ? $wc_order_status[ $order_status ] : $order_status;
        }

        return $order_status;
    }
}

if( ! function_exists( 'yith_wcmv_get_order_currency' ) ){
    /**
     * Get the order currency for retro compatibility
     *
     * @author Andrea Grillo <andrea.grillo@yithemes.com>
     * @param $order
     * @return string order currency
     */
    function yith_wcmv_get_order_currency( $order ){
        $get_currency = YITH_Vendors()->is_wc_2_7_or_greather ? 'get_currency' : 'get_order_currency';
        return $order->$get_currency();
    }
}

if( ! function_exists( 'yith_wcmv_get_meta_field' ) ){
    /**
     * get meta fields wrapper for wc 2.6 or lower
     *
     * @author Andrea Grillo <andrea.grillo@yithemes.com>
     * @param $meta
     * @return  array meta order value
     */
    function yith_wcmv_get_meta_field( $meta ){

        if( YITH_Vendors()->is_wc_2_7_or_greather && is_object ( $meta ) ){
            $meta = array(
                'meta_id'       => $meta->id,
                'meta_key'      => $meta->key,
                'meta_value'    => $meta->value
            );
        }

        return $meta;
    }
}

if( ! function_exists( 'yith_wcmv_get_font_awesome_icons' ) ){
	/**
	 * Get the correct classes for font awesome icons with Font Awesome 5 or greather
	 *
	 * @author Andrea Grillo <andrea.grillo@yithemes.com>
	 * @return  array font awesome classes
	 */
	function yith_wcmv_get_font_awesome_icons(){
		return apply_filters( 'yith_wcmv_header_icons_class', array(
				'rating'        => 'fas fa-star',
				'sales'         => 'fas fa-credit-card',
				'vat'           => 'fas fa-file-alt',
				'legal_notes'   => 'fas fa-gavel',
				'website'       => 'fas fa-link',
				'location'      => 'fas fa-location-arrow',
				'telephone'     => 'fas fa-phone',
				'store_email'   => 'fas fa-envelope',
			)
		);
	}
}

if( ! function_exists( '__yith_wcmv_return_yes' ) ){
	/**
	 * Return 'yes' string to change default value for panel options
	 *
	 * @author Andrea Grillo <andrea.grillo@yithemes.com>
	 * @return  string 'yes' value
	 */
	function __yith_wcmv_return_yes(){
		return 'yes';
	}
}

if( ! function_exists( 'yith_wcmv_switch_back_redirection_url' ) ){
	/**
	 * Fix redirect URL when a user switches to another user or switches back.
	 *
	 * If the administrator click on Switch Back in vendor's profile page
	 * we force to redirect it to default admin_url() to prevent to show message
	 * that he haven't permissions to see the current page.
	 * Please note: The vendor's settings page doesn't exists for administrator
	 *
	 * @param string  $redirect_to           The redirect destination URL.
	 * @param string  $requested_redirect_to The requested redirect destination URL passed as a parameter.
	 * @param WP_User $new_user              The WP_User object for the user that's being switched to.
	 *
	 * @return   string Url to redirect
	 * @since    3.2.2
	 * @author   Andrea Grillo <andrea.grillo@yithemes.com>
	 * @use      This matches the WordPress core filter in wp-login.php. user-switching/user-switching.php at line 318
	 */
	function yith_wcmv_switch_back_redirection_url( $redirect_to, $requested_redirect_to, $new_user ) {
		global $user_switching;
		if ( ! empty( $user_switching ) && class_exists( 'user_switching' ) && $user_switching instanceof user_switching ) {
			$is_vendor_settings_page     = strpos( $requested_redirect_to, 'yith_vendor_settings' ) !== false;
			$is_switch_to_olduser_action = isset( $_GET['action'] ) && 'switch_to_olduser' == $_GET['action'];
			$redirect_to                 = $is_switch_to_olduser_action && $is_vendor_settings_page ? admin_url() : $redirect_to;
		}

		return $redirect_to;
	}
}

if( ! function_exists( 'yith_wcmv_string_is_url' ) ){
	/**
	 * Check if current string is a valid URL
	 *
	 * @param $url string
	 *
	 * @return true on success, false otherwise
	 * @author Andrea Grillo <andrea.grillo@yithemes.com>
	 * @since 3.4.0
	 */
	function yith_wcmv_string_is_url( $url ){
		$pattern = '|^http(s)?://[a-z0-9-]+(.[a-z0-9-]+)*(:[0-9]+)?(/.*)?$|i';
		return preg_match( $pattern, $url );
	}
}