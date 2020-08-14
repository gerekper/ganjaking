<?php

/**
 * Database Table Check
 */

function yith_wcmv_get_paypal_options_array(){
	return array(
		//PayPal Tab
		'payment_gateway'                          => 'masspay',
		'payment_method'                           => 'choose',
		'payment_minimum_withdrawals'              => 1,
		'paypal_sandbox'                           => 'yes',
		'paypal_api_username'                      => '',
		'paypal_api_password'                      => '',
		'paypal_api_signature'                     => '',
		'paypal_payment_mail_subject'              => '',
		'paypal_ipn_notification_url'              => site_url() . '/?paypal_ipn_response=true',
	);
}

//Check if Commission tables are created
function yith_vendors_check_commissions_table() {
	$create_commissions_table = get_option( 'yith_product_vendors_commissions_table_created', false );
	if ( ! $create_commissions_table || isset( $_GET['yith_wcmv_force_create_table'] ) ) {
		/**
		 * Create new Commissions DB table
		 */
		class_exists( 'YITH_Commissions' ) &&  YITH_Commissions::create_commissions_table();
	}

	$create_payments_table = get_option( 'yith_product_vendors_payments_table_created', false );
	if ( ! $create_payments_table || isset( $_GET['yith_wcmv_force_create_table'] ) ) {
		/**
		 * Create new Payments DB table
		 */
		class_exists( 'YITH_Vendors_Payments' ) && YITH_Vendors_Payments::create_transaction_table();
	}
}

add_action( 'admin_init', 'yith_vendors_check_commissions_table' );

//Add support to YITH Product Vendors db version 1.0.1
function yith_vendors_update_db_1_0_1() {
	$vendors_db_option = get_option( 'yith_product_vendors_db_version', '1.0.0' );
	if ( $vendors_db_option && version_compare( $vendors_db_option, '1.0.1', '<' ) ) {
		global $wpdb;
		$termmeta_table   = YITH_Vendors()->termmeta_table;
		$termmeta_term_id = YITH_Vendors()->termmeta_term_id;
		$sql              = "SELECT {$termmeta_term_id} as vendor_id, meta_value as user_id
                    FROM {$termmeta_table} as wtm
                    WHERE wtm.meta_key = %s
                    AND {$termmeta_table} IN (
                        SELECT DISTINCT term_id as vendor_id
                        FROM {$wpdb->term_taxonomy} as tt
                        WHERE tt.taxonomy = %s
                    )";

		$results = $wpdb->get_results( $wpdb->prepare( $sql, 'owner', YITH_Vendors()->get_taxonomy_name() ) );

		foreach ( $results as $result ) {
			$user = get_user_by( 'id', $result->user_id );

			if ( $user ) {
				YITH_Vendors()->update_term_meta( $result->vendor_id, 'registration_date', get_date_from_gmt( $user->user_registered ) );
				YITH_Vendors()->update_term_meta( $result->vendor_id, 'registration_date_gmt', $user->user_registered );
				if ( defined( 'YITH_WPV_PREMIUM' ) ) {
					$user->add_cap( 'view_woocommerce_reports' );
				}
			}
		}

		update_option( 'yith_product_vendors_db_version', '1.0.1' );
	}
}

//Add support to YITH Product Vendors db version 1.0.2
function yith_vendors_update_db_1_0_2() {
	$vendors_db_option = get_option( 'yith_product_vendors_db_version', '1.0.0' );
	if ( $vendors_db_option && version_compare( $vendors_db_option, '1.0.2', '<' ) ) {
		global $wpdb;

		$sql = "ALTER TABLE `{$wpdb->prefix}yith_vendors_commissions` CHANGE `rate` `rate` DECIMAL(5,4) NOT NULL";
		$wpdb->query( $sql );

		update_option( 'yith_product_vendors_db_version', '1.0.2' );
	}
}

//Add support to YITH Product Vendors db version 1.0.3
function yith_vendors_update_db_1_0_3() {
	$vendors_db_option = get_option( 'yith_product_vendors_db_version', '1.0.0' );
	if ( $vendors_db_option && version_compare( $vendors_db_option, '1.0.3', '<' ) ) {
		/**
		 * Create "Become a Vendor" and Terms and Conditions Pages
		 */
		if ( defined( 'YITH_WPV_PREMIUM' ) ) {
			YITH_Vendors_Admin_Premium::create_plugins_page();
		}

		/**
		 * Show Gravatar Option
		 */
		$vendors = YITH_Vendors()->get_vendors();
		foreach ( $vendors as $vendor ) {
			if ( empty( $vendor->show_gravatar ) ) {
				$vendor->show_gravatar = 'yes';
			}
		}
		update_option( 'yith_product_vendors_db_version', '1.0.3' );
	}
}

//Add support to YITH Product Vendors plugin version 1.8.1
function yith_vendors_update_db_1_0_4() {
	$vendors_db_option = get_option( 'yith_product_vendors_db_version', '1.0.0' );
	if ( $vendors_db_option && version_compare( $vendors_db_option, '1.0.4', '<' ) ) {
		/**
		 * Create "Become a vendor" and "Terms and conditions" Pages
		 */
		if ( defined( 'YITH_WPV_PREMIUM' ) ) {
			YITH_Vendors_Admin_Premium::create_plugins_page();
		}
		update_option( 'yith_product_vendors_db_version', '1.0.4' );
	}
}

//Add support to YITH Product Vendors plugin version 1.9.6
function yith_vendors_update_db_1_0_5() {
	$vendors_db_option = get_option( 'yith_product_vendors_db_version', '1.0.0' );
	if ( $vendors_db_option && version_compare( $vendors_db_option, '1.0.5', '<' ) ) {
		/**
		 * Create new DB table yith_vendors_payments and yith_vendors_payments_relathionship
		 */
		if ( defined( 'YITH_WPV_PREMIUM' ) ) {
			YITH_Commissions::create_transaction_table();
		}
		update_option( 'yith_product_vendors_db_version', '1.0.5' );
	}
}

//Add support to YITH Product Vendors plugin version 1.9.6
function yith_vendors_update_db_1_0_6() {
	$vendors_db_option = get_option( 'yith_product_vendors_db_version', '1.0.0' );
	if ( $vendors_db_option && version_compare( $vendors_db_option, '1.0.6', '<' ) ) {
		$vendor_role_name = YITH_Vendors()->get_role_name();
		$vendor_role      = get_role( $vendor_role_name );
		if ( $vendor_role instanceof WP_Role ) {
			$vendor_role->add_cap( 'edit_posts' );
		}
		update_option( 'yith_product_vendors_db_version', '1.0.6' );
	}

}

//Add support to YITH Product Vendors plugin version 1.11.4
function yith_vendors_update_db_1_0_7() {
	$vendors_db_option = get_option( 'yith_product_vendors_db_version', '1.0.0' );
	if ( $vendors_db_option && version_compare( $vendors_db_option, '1.0.7', '<' ) ) {
		global $wpdb;
		if ( ! empty( $wpdb ) ) {
			$query = $wpdb->prepare( "DELETE FROM {$wpdb->prefix}woocommerce_order_itemmeta WHERE meta_key IN( %s, %s )", '_parent__commission_included_coupon', '_parent__commission_included_tax' );
			$wpdb->query( $query );
		}
		update_option( 'yith_product_vendors_db_version', '1.0.7' );
	}

}

//Add support to YITH Product Vendors plugin version 1.12.0
function yith_vendors_update_db_1_0_8() {
	$vendors_db_option = get_option( 'yith_product_vendors_db_version', '1.0.0' );
	if ( $vendors_db_option && version_compare( $vendors_db_option, '1.0.8', '<' ) ) {
		$skin_option_id       = 'yith_vendors_skin_header';
		$background_option_id = 'yith_skin_background_color';
		$font_color_option_id = 'yith_skin_font_color';

		$old_skin = get_option( $skin_option_id, 'skin1' );

		if ( 'skin1' == $old_skin ) {
			update_option( $background_option_id, '#000000' );
			update_option( $font_color_option_id, '#ffffff' );
		} else {
			update_option( $background_option_id, '#ffffff' );
			update_option( $font_color_option_id, '#000000' );
		}

		update_option( $skin_option_id, 'small-box' );
		update_option( 'yith_product_vendors_db_version', '1.0.8' );
	}
}

//Add support to YITH Product Vendors plugin version 1.12.1
function yith_vendors_update_db_1_0_9() {
	$vendors_db_option = get_option( 'yith_product_vendors_db_version', '1.0.0' );
	if ( $vendors_db_option && version_compare( $vendors_db_option, '1.0.9', '<' ) ) {
		$vendor_role_name = YITH_Vendors()->get_role_name();
		$vendor_role      = get_role( $vendor_role_name );
		if ( $vendor_role instanceof WP_Role ) {
			//Fix: vendor admins can't edit orders
			$vendor_role->add_cap( 'edit_others_shop_orders' );
		}
		update_option( 'yith_product_vendors_db_version', '1.0.9' );
	}
}

//ALTER TABLE `wp_yith_vendors_payments` ;
//Add support to shipping module
function yith_vendors_update_db_1_1_0() {
	$vendors_db_option = get_option( 'yith_product_vendors_db_version', '1.0.0' );
	if ( $vendors_db_option && version_compare( $vendors_db_option, '1.1.0', '<' ) ) {
		global $wpdb;

		$sql = "ALTER TABLE `{$wpdb->prefix}yith_vendors_commissions` ADD `type` VARCHAR(30) NOT NULL DEFAULT 'product' AFTER `status`";
		$wpdb->query( $sql );

		update_option( 'yith_product_vendors_db_version', '1.1.0' );
	}
}


//change the old option to new one for New tax management for vendor's commission
function yith_vendors_update_db_1_1_1() {
	$vendors_db_option = get_option( 'yith_product_vendors_db_version', '1.0.0' );
	if ( $vendors_db_option && version_compare( $vendors_db_option, '1.1.1', '<' ) ) {
		$old_option_value = get_option( 'yith_wpv_include_tax', 'no' );
		$new_option_value = 'no' == $old_option_value ? 'website' : 'split';

		/**
		 * Set the new option value
		 */
		update_option( 'yith_wpv_commissions_tax_management', $new_option_value );
		delete_option( 'yith_wpv_include_tax' );

		update_option( 'yith_product_vendors_db_version', '1.1.1' );
	}
}

//change the old user meta $vendor->website to $vendor->socials['website']
function yith_vendors_update_db_1_1_2() {
	$vendors_db_option = get_option( 'yith_product_vendors_db_version', '1.0.0' );
	if ( $vendors_db_option && version_compare( $vendors_db_option, '1.1.2', '<' ) ) {
		$vendors = YITH_Vendors()->get_vendors( array( 'fields' => 'ids' ) );
		if ( ! empty( $vendors ) ) {
			foreach ( $vendors as $vendor_id ) {
				$website = YITH_Vendors()->is_wc_2_6 || YITH_Vendors()->is_wc_2_7_or_greather ? get_term_meta( $vendor_id, 'website', true ) : get_metadata( 'woocommerce_term', $vendor_id, 'website', true );
				if ( ! empty( $website ) ) {
					$socials            = YITH_Vendors()->is_wc_2_6 || YITH_Vendors()->is_wc_2_7_or_greather ? get_term_meta( $vendor_id, 'socials', true ) : get_metadata( 'woocommerce_term', $vendor_id, 'socials', true );
					$socials['website'] = $website;
					update_term_meta( $vendor_id, 'socials', $socials );
					delete_term_meta( $vendor_id, 'website', $website );
				}
			}
		}

		if ( 'yes' == get_option( 'yith_wpv_vendor_show_vendor_website', 'no' ) ) {
			update_option( 'yith_wpv_vendor_show_vendor_website', 'header' );
		}

		update_option( 'yith_product_vendors_db_version', '1.1.2' );
	}
}

//change the old option to new one for New tax management for vendor's commission
function yith_vendors_update_db_1_1_3() {
	$vendors_db_option = get_option( 'yith_product_vendors_db_version', '1.0.0' );
	if ( $vendors_db_option && version_compare( $vendors_db_option, '1.1.3', '<' ) ) {
		$old_option_value = get_option( 'yith_vendors_related_products', 'vendor' );
		if ( 'disable' == $old_option_value ) {
			/**
			 * Set the new option value
			 */
			update_option( 'yith_vendors_related_products', 'disabled' );
		}

		update_option( 'yith_product_vendors_db_version', '1.1.3' );
	}
}

//Add support to YITH Product Vendors db version 1.1.4
function yith_vendors_update_db_1_1_4() {
	$vendors_db_option = get_option( 'yith_product_vendors_db_version', '1.0.0' );
	if ( $vendors_db_option && version_compare( $vendors_db_option, '1.1.4', '<' ) ) {
		$paypal_options = yith_wcmv_get_paypal_options_array();
		$options_backup = array();

		foreach ( $paypal_options as $option => $default ) {
			$options_backup[ $option ] = get_option( $option, $default );
			delete_option( $option );
		}

		add_option( 'yith_wcmv_deprecated_paypal_options', $options_backup );
		update_option( 'yith_product_vendors_db_version', '1.1.4' );
	}
}

//Add support to YITH Product Vendors db version 1.1.5
function yith_vendors_update_db_1_1_5() {
	$vendors_db_option = get_option( 'yith_product_vendors_db_version', '1.0.0' );
	if ( $vendors_db_option && version_compare( $vendors_db_option, '1.1.5', '<' ) ) {
		delete_option( 'yith_wpv_vendors_option_adaptive_payment' );
		update_option( 'yith_product_vendors_db_version', '1.1.5' );
	}
}

//Add support to YITH Product Vendors db version 1.1.6
function yith_vendors_update_db_1_1_6() {
	$vendors_db_option = get_option( 'yith_product_vendors_db_version', '1.0.0' );
	if ( $vendors_db_option && version_compare( $vendors_db_option, '1.1.6', '<' ) ) {
		foreach( YITH_Vendors()->get_vendors() as $vendor ){
			if( $vendor instanceof YITH_Vendor ){
				$vendor_owner = $vendor->get_owner();
				if( empty( $vendor_owner ) ){
					$vendor->owner = '';
				}
			}
		}
		update_option( 'yith_product_vendors_db_version', '1.1.6' );

	}
}

//Add support to YITH Product Vendors db version 1.1.7
function yith_vendors_update_db_1_1_7() {
	$vendors_db_option = get_option( 'yith_product_vendors_db_version', '1.0.0' );
	if ( $vendors_db_option && version_compare( $vendors_db_option, '1.1.7', '<' ) ) {
		global $wpdb;
		$old_table_name = "{$wpdb->prefix}yith_vendors_payments_relathionship";
		$new_table_name = "{$wpdb->prefix}yith_vendors_payments_relationship";
		$sql = "RENAME TABLE {$old_table_name} TO {$new_table_name}";
		$wpdb->query( $sql );

		update_option( 'yith_product_vendors_db_version', '1.1.7' );
	}
}

//Add support to YITH Product Vendors db version 1.1.8
function yith_vendors_update_db_1_1_8() {
	$vendors_db_option = get_option( 'yith_product_vendors_db_version', '1.0.0' );
	if ( $vendors_db_option && version_compare( $vendors_db_option, '1.1.8', '<' ) ) {
		global $wpdb;

		$sql = "ALTER TABLE `{$wpdb->prefix}yith_vendors_payments_relationship` DROP COLUMN `ID`";
		$wpdb->query( $sql );

		$sql = "ALTER TABLE `{$wpdb->prefix}yith_vendors_payments_relationship`ADD PRIMARY KEY( payment_id, commission_id )";
		$wpdb->query( $sql );

		update_option( 'yith_product_vendors_db_version', '1.1.8' );
	}
}

//Add support to YITH Product Vendors db version 1.1.9
function yith_vendors_update_db_1_1_9() {
	$vendors_db_option = get_option( 'yith_product_vendors_db_version', '1.0.0' );
	if ( $vendors_db_option && version_compare( $vendors_db_option, '1.1.9', '<' ) ) {
		global $wpdb;

		$sql = "ALTER TABLE `{$wpdb->prefix}yith_vendors_payments` ADD `currency` VARCHAR(10) NOT NULL AFTER `amount`";
		$wpdb->query( $sql );

		update_option( 'yith_product_vendors_db_version', '1.1.9' );
	}
}

//Add support to YITH Product Vendors db version 1.1.10
function yith_vendors_update_db_1_1_10() {
	$vendors_db_option = get_option( 'yith_product_vendors_db_version', '1.0.0' );
	if ( $vendors_db_option && version_compare( $vendors_db_option, '1.1.10', '<' ) ) {
		global $wpdb;

		$sql = "ALTER TABLE `{$wpdb->prefix}yith_vendors_payments` ADD `note` text AFTER `status`";
		$wpdb->query( $sql );

		update_option( 'yith_product_vendors_db_version', '1.1.10' );
	}
}

//Add support to YITH Product Vendors db version 1.1.9
function yith_vendors_update_db_1_1_11() {
	$vendors_db_option = get_option( 'yith_product_vendors_db_version', '1.0.0' );
	if ( $vendors_db_option && version_compare( $vendors_db_option, '1.1.11', '<' ) ) {
		global $wpdb;

		$sql = "ALTER TABLE `{$wpdb->prefix}yith_vendors_payments` ADD `gateway_id` varchar(100)";
		$wpdb->query( $sql );

		update_option( 'yith_product_vendors_db_version', '1.1.11' );
	}
}

//Add support to YITH Product Vendors db version 1.1.9
function yith_vendors_update_db_1_1_12() {
	$vendors_db_option = get_option( 'yith_product_vendors_db_version', '1.0.0' );
	if ( $vendors_db_option && version_compare( $vendors_db_option, '1.1.12', '<' ) ) {
		if( function_exists( 'YITH_Vendors' ) ){
			$vendors = YITH_Vendors()->get_vendors();

			foreach( $vendors as $vendor ){
				update_term_meta( $vendor->id, 'owner', $vendor->get_owner() );
			}
		}

		update_option( 'yith_product_vendors_db_version', '1.1.12' );
	}
}

//Add support to YITH Product Vendors db version 1.1.13
function yith_vendors_update_db_1_1_13() {
	$vendors_db_option = get_option( 'yith_product_vendors_db_version', '1.0.0' );
	if ( $vendors_db_option && version_compare( $vendors_db_option, '1.1.13', '<' ) ) {
		global $wpdb;

		$sql = "ALTER TABLE `{$wpdb->prefix}yith_vendors_commissions` ADD `amount_refunded` DOUBLE(15,4) NOT NULL DEFAULT '0' AFTER `amount`";
		$wpdb->query( $sql );

		update_option( 'yith_product_vendors_db_version', '1.1.13' );
	}
}


add_action( 'admin_init', 'yith_vendors_update_db_1_0_1' );
add_action( 'admin_init', 'yith_vendors_update_db_1_0_2' );
add_action( 'admin_init', 'yith_vendors_update_db_1_0_3' );
add_action( 'admin_init', 'yith_vendors_update_db_1_0_4' );
add_action( 'admin_init', 'yith_vendors_update_db_1_0_5' );
add_action( 'admin_init', 'yith_vendors_update_db_1_0_6' );
add_action( 'admin_init', 'yith_vendors_update_db_1_0_7' );
add_action( 'admin_init', 'yith_vendors_update_db_1_0_8' );
add_action( 'admin_init', 'yith_vendors_update_db_1_0_9' );
add_action( 'admin_init', 'yith_vendors_update_db_1_1_0' );
add_action( 'admin_init', 'yith_vendors_update_db_1_1_1' );
add_action( 'admin_init', 'yith_vendors_update_db_1_1_2' );
add_action( 'admin_init', 'yith_vendors_update_db_1_1_3' );
add_action( 'admin_init', 'yith_vendors_update_db_1_1_4' );
add_action( 'admin_init', 'yith_vendors_update_db_1_1_5' );
add_action( 'admin_init', 'yith_vendors_update_db_1_1_6' );
add_action( 'admin_init', 'yith_vendors_update_db_1_1_7' );
add_action( 'admin_init', 'yith_vendors_update_db_1_1_8' );
add_action( 'admin_init', 'yith_vendors_update_db_1_1_9' );
add_action( 'admin_init', 'yith_vendors_update_db_1_1_10' );
add_action( 'admin_init', 'yith_vendors_update_db_1_1_11' );
add_action( 'admin_init', 'yith_vendors_update_db_1_1_12', 99 ); //Execute it after create taxonomy
add_action( 'admin_init', 'yith_vendors_update_db_1_1_13' ); //Execute it after create taxonomy

/**
 * Plugin Version Update
 */

//Add support to YITH Product Vendors plugin version 1.8.1
function yith_vendors_plugin_update_1_8_1() {
	$plugin_version = get_option( 'yith_wcmv_version', '1.0.0' );
	if ( version_compare( $plugin_version, YITH_Vendors()->version, '<' ) ) {
		// _money_spent and _order_count may be out of sync - clear them
		delete_metadata( 'user', 0, '_money_spent', '', true );
		delete_metadata( 'user', 0, '_order_count', '', true );
	}
}

//priority set to 20 after vendor register taxonomy
add_action( 'admin_init', 'yith_vendors_plugin_update_1_8_1', 20 );

/**
 * PayPal MassPay and PayPal Adaptive Payments Message
 */

//Add support to YITH Product Vendors plugin version 2.5.0
function yith_vendors_plugin_update_2_5_0() { ?>
	<div id="yith_wcmv_dismissable_notice" class="notice notice-error is-dismissable" style="position: relative;">
		<span class="yith_wcmv_notice_dismiss notice-dismiss"></span>
		<p style="line-height: 25px;">
			<strong>Please note!</strong>
			<br/>
			From December 2017, PayPal deprecated <i><b>MassPay</b></i> and <i><b>Adaptive Payments</b></i> methods.
			<br/>
			This implies <b>it is no longer possible to request for the activation of these services for new accounts</b>. However, those who have the services already active will still be able to use them.
			<br/>
			For this reason, from next version 2.5.0 of our plugin YITH WooCommerce Multi Vendor, <b><u>all the options related to these payment methods will be removed</u></b>.
			<br/>
			If you are a former client using <i>MassPay or Adaptive Payments</i> service, we invite you to read this article on our Help Center:
			<br/>
			<a href='http://bit.ly/2GWeD1j' target="_blank">Multi Vendor: Support for Deprecated PayPal Service</a>
		</p>
	</div>
	<?php
}

if( ! function_exists( 'yith_wcmv_dismissable_notice' ) ){
	function yith_wcmv_dismissable_notice(){
		if( ! wp_script_is( 'js-cookie', 'registered' ) ){
			$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
			wp_register_script( 'js-cookie', WC()->plugin_url() . '/assets/js/js-cookie/js.cookie' . $suffix . '.js', array(), WC_VERSION, true );
		}

		wp_enqueue_script( 'js-cookie' );
		$js = "jQuery(document).on( 'click', '.yith_wcmv_notice_dismiss', function(){jQuery( '#yith_wcmv_dismissable_notice' ).animate({ opacity: 0.25, height: 'toggle' }, 650 ); Cookies.set( 'yith_paypal_deprecated_notice', 'dismiss', {path: '/'} );});";
		wp_add_inline_script( 'js-cookie', $js );
	}
}

global $pagenow;
$is_plugin_page      = 'plugins.php' == $pagenow;
$is_mv_section       = ( 'admin.php' == $pagenow || 'edit-tags.php' == $pagenow );
$is_mv_page          = $is_mv_section && ! empty( $_GET['page'] ) && ( 'yith_wpv_panel' == $_GET['page'] || 'yith_vendor_commissions' == $_GET['page'] || 'yith_plugins_activation' == $_GET['page'] );
$is_mv_taxonomy_page = $is_mv_section && ! empty( $_GET['taxonomy'] ) && 'yith_shop_vendor' == $_GET['taxonomy'];

if ( defined( 'YITH_WPV_VERSION' ) && version_compare( YITH_WPV_VERSION, '2.5.0', '<' )
     &&
     empty( $_COOKIE['yith_paypal_deprecated_notice'] )
	&&
	 ( $is_plugin_page || $is_mv_page || $is_mv_taxonomy_page )
) {


	add_action( 'admin_notices', 'yith_vendors_plugin_update_2_5_0', 20 );
	add_action( 'admin_enqueue_scripts', 'yith_wcmv_dismissable_notice', 20 );
}

function yith_vendors_plugin_update_2_6_0(){
	if ( version_compare( YITH_Vendors()->version, '2.6.0', '>=' ) ) {
		$to_remove = yith_wcmv_get_paypal_options_array();
		$to_remove['yith_wcmv_deprecated_paypal_options'] = $to_remove['yith_wcmv_deprecated_paypal_options_restored'] = '';

		foreach( $to_remove as $key => $value ){
		    delete_option( $key );
        }
    }
}

add_action( 'admin_notices', 'yith_vendors_plugin_update_2_6_0', 20 );

/**
 * Regenerate Vendor Role Capabilities after update by FTP
 */

function yith_vendors_plugin_update() {
	$plugin_version = get_option( 'yith_wcmv_version', '1.0.0' );
	if ( version_compare( $plugin_version, YITH_Vendors()->version, '<' ) ) {
		/* Check if Vendor Role Exists */
		YITH_Vendors::add_vendor_role();
		/* Add Vendor Role to vendor owner and admins */
		YITH_Vendors::setup( 'add_role' );
		update_option( 'yith_wcmv_version', YITH_Vendors()->version );
	}
}

//priority set to 30 after vendor register taxonomy and other actions
add_action( 'admin_init', 'yith_vendors_plugin_update', 30 );

// Deprecated hook yith_wcmv_show_vendor_profile. Look: includes/class.yith-vendors-admin-premium.php:185
add_filter( 'yith_wcmv_hide_vendor_profile', 'yith_deprecated_show_vendor_profile_hook', 9999 );
function yith_deprecated_show_vendor_profile_hook( $value ) {
	return apply_filters( 'yith_wcmv_show_vendor_profile', $value );
}