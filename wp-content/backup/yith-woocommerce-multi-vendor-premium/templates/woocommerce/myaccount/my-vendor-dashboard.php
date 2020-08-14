<?php
/**
 * My Account page
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
?>
<h2>
    <?php printf( '%s %s %s', _x( 'My', '[Part of] My Vendor Dashboard', 'yith-woocommerce-product-vendors' ), YITH_Vendors()->get_singular_label( 'ucfirst' ), _x( 'Dashboard', '[Part of] My Vendor Dashboard', 'yith-woocommerce-product-vendors' ) ); ?>
</h2>

<p class="myaccount_vendor_dashboard">
	<?php
    if( $is_pending ){
        _e( "You'll be able to access your dashboard as soon as the administrator approves your vendor account.", 'yith-woocommerce-product-vendors' );
        echo '<br/>';
    }

    printf( '%s %s %s, %s.',
		_x( 'From your ', '[Part of] From your vendor dashboard', 'yith-woocommerce-product-vendors' ),
		YITH_Vendors()->get_singular_label( 'strtolower' ),
		_x( 'dashboard you can view your recent commissions', '[Part of] From your vendor dashboard you can...', 'yith-woocommerce-product-vendors' ),
		_x( 'view the sales report and manage your store and payment settings', '[Part of] From your vendor dashboard you can. you can view your recent commissions, view...', 'yith-woocommerce-product-vendors' )
	);

    if( ! $is_pending ){
	    printf( '<br/>%s <a href="%s">%s</a> %s <strong>%s %s</strong>.',
		    _x( 'Click', '[Part of] Click here', 'yith-woocommerce-product-vendors' ),
		    apply_filters( 'yith_wcmv_my_vendor_dashboard_uri', esc_url( admin_url() ) ),
		    _x( 'here', '[Part of] Click here', 'yith-woocommerce-product-vendors' ),
		    _x( 'to access', '[Part of] Click here to access your dashboard', 'yith-woocommerce-product-vendors' ),
		    $vendor_name,
		    _x( 'dashboard', '[Part of] Click here to access your dashboard', 'yith-woocommerce-product-vendors' )
	    );
    }

    ?>
</p>
