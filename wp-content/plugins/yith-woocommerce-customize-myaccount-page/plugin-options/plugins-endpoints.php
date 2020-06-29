<?php
/**
 * GENERAL PLUGINS ENDPOINTS ARRAY
 */
if ( ! defined( 'YITH_WCMAP' ) ) {
	exit;
} // Exit if accessed directly

$plugins = [];

if ( defined( 'YITH_WCWL' ) && YITH_WCWL ) {
	$plugins['my-wishlist'] = array(
		'slug'    => 'my-wishlist',
		'active'  => true,
		'label'   => __( 'My Wishlist', 'yith-woocommerce-customize-myaccount-page' ),
		'icon'    => 'heart',
		'class'   => '',
		'content' => '[yith_wcwl_wishlist]',
	);
}
if ( defined( 'YITH_WOCC_PREMIUM' ) && YITH_WOCC_PREMIUM ) {
	$plugins['one-click'] = array(
		'slug'    => 'one-click',
		'active'  => true,
		'label'   => __( 'One click checkout', 'yith-woocommerce-customize-myaccount-page' ),
		'icon'    => 'hand-o-up',
		'class'   => '',
		'content' => '[yith_wocc_myaccount]',
	);
}
if ( defined( 'YITH_YWRAQ_PREMIUM' ) && YITH_YWRAQ_PREMIUM ) {
	$plugins['view-quote'] = array(
		'slug'    => 'view-quote',
		'active'  => true,
		'label'   => __( 'My Quotes', 'yith-woocommerce-customize-myaccount-page' ),
		'icon'    => 'pencil',
		'class'   => '',
		'content' => '[yith_ywraq_myaccount_quote]',
	);
}
if ( defined( 'YITH_WCWTL_PREMIUM' ) && YITH_WCWTL_PREMIUM ) {
	$plugins['waiting-list'] = array(
		'slug'    => get_option( 'woocommerce_myaccount_waiting_list_endpoint', 'waiting-list' ),
		'active'  => true,
		'label'   => __( 'My Waiting List', 'yith-woocommerce-customize-myaccount-page' ),
		'icon'    => 'clock-o',
		'class'   => '',
		'content' => '[ywcwtl_waitlist_table]',
	);
}
if ( defined( 'YITH_WCMBS_PREMIUM' ) && YITH_WCMBS_PREMIUM ) {

	$title     = __( 'Membership Plans:', 'yith-woocommerce-membership' );
	$shortcode = '[membership_history title="' . $title . '"]';
	$shortcode = apply_filters( 'yith_wcmbs_membership_history_shortcode_in_my_account', $shortcode, $title );

	$plugins['yith-membership'] = array(
		'slug'    => 'membership-plans',
		'active'  => true,
		'label'   => __( 'Membership Plans', 'yith-woocommerce-customize-myaccount-page' ),
		'icon'    => 'list',
		'class'   => '',
		'content' => $shortcode,
	);
}
if ( defined( 'YITH_YWSBS_PREMIUM' ) && YITH_YWSBS_PREMIUM ) {
	$plugins['yith-subscription'] = array(
		'slug'    => 'my-subscription',
		'active'  => true,
		'label'   => __( 'My Subscriptions', 'yith-woocommerce-customize-myaccount-page' ),
		'icon'    => 'pencil',
		'class'   => '',
		'content' => '[ywsbs_my_account_subscriptions]',
	);
}

if ( class_exists( 'WC_Memberships' ) ) {
	$plugins['members-area'] = array(
		'slug'    => get_option( 'woocommerce_myaccount_members_area_endpoint', 'members-area' ),
		'active'  => true,
		'label'   => __( 'My Membership', 'yith-woocommerce-customize-myaccount-page' ),
		'icon'    => 'list',
		'class'   => '',
		'content' => '',
	);
}

if ( class_exists( 'WC_Subscriptions' ) ) {
	$plugins['woo-subscription'] = array(
		'slug'    => 'my-subscriptions',
		'active'  => true,
		'label'   => __( 'My Subscription', 'yith-woocommerce-customize-myaccount-page' ),
		'icon'    => 'pencil',
		'class'   => '',
		'content' => '[ywcmap_woocommerce_subscription]',
	);
}

if ( defined( 'YITH_FUNDS_PREMIUM' ) && YITH_FUNDS_PREMIUM ) {

	$plugins['make-a-deposit'] = array(
		'slug'    => 'make-a-deposit',
		'active'  => true,
		'label'   => __( 'Make a Deposit', 'yith-woocommerce-customize-myaccount-page' ),
		'icon'    => 'money',
		'class'   => '',
		'content' => '[yith_ywf_make_a_deposit_endpoint]',
	);

	$plugins['income-expenditure-history'] = array(
		'slug'    => 'income-expenditure-history',
		'active'  => true,
		'label'   => __( 'Income/Expenditure History', 'yith-woocommerce-customize-myaccount-page' ),
		'icon'    => 'list-ol',
		'class'   => '',
		'content' => '[yith_ywf_show_history pagination="yes"]',
	);
	/*$plugins['redeem-funds']               = array(
		'slug'    => 'redeem-funds',
		'active'  => true,
		'label'   => __( 'Redeem Funds', 'yith-woocommerce-customize-myaccount-page' ),
		'icon'    => 'list-ol',
		'class'   => '',
		'content' => '[yith_ywf_redeem_vendor_funds]',
	);*/


}

if ( defined( 'YITH_YWGC_PREMIUM' ) && YITH_YWGC_PREMIUM ) {

	$plugins['gift-cards'] = array(
		'slug'    => 'gift-cards',
		'active'  => true,
		'label'   => __( 'Gift Cards', 'yith-woocommerce-customize-myaccount-page' ),
		'icon'    => 'gift',
		'class'   => '',
		'content' => '[yith_wcgc_show_gift_card_list]',
	);
}

if ( defined( 'YITH_PAYOUTS_PREMIUM' ) && YITH_PAYOUTS_PREMIUM ) {

	$plugins['payouts'] = array(
		'slug'    => 'payouts',
		'active'  => true,
		'label'   => __( 'Payouts', 'yith-woocommerce-customize-myaccount-page' ),
		'icon'    => 'money',
		'class'   => '',
		'content' => '',
	);
}

if ( defined( 'YITH_WCSC_PREMIUM' ) && YITH_WCSC_PREMIUM ) {
	$plugins['stripe-connect'] = array(
		'slug'    => 'stripe-connect',
		'active'  => true,
		'label'   => __( 'Stripe Connect', 'yith-woocommerce-customize-myaccount-page' ),
		'icon'    => 'money',
		'class'   => '',
		'content' => '',
	);
}

if ( class_exists( 'WT_MyAccount_SmartCoupon' ) ) {
	$plugins['wt-smart-coupon'] = array(
		'slug'    => WT_MyAccount_SmartCoupon::$endpoint,
		'active'  => true,
		'label'   => __( 'My Coupons', 'yith-woocommerce-customize-myaccount-page' ),
		'icon'    => 'ticket',
		'class'   => '',
		'content' => '',
	);
}

if ( class_exists( 'TInvWL' ) && shortcode_exists( 'ti_wishlistsview' ) ) {
	$plugins['tinv-wishlist'] = array(
		'slug'    => 'my-wishlist',
		'active'  => true,
		'label'   => __( 'My Wishlist', 'yith-woocommerce-customize-myaccount-page' ),
		'icon'    => 'heart',
		'class'   => '',
		'content' => '[ti_wishlistsview]',
	);
}

if ( class_exists( 'WooCommerce_API_Manager' ) ) {
	$plugins['api-keys']      = array(
		'slug'    => 'api-keys',
		'active'  => true,
		'label'   => __( 'API Keys', 'yith-woocommerce-customize-myaccount-page' ),
		'icon'    => '',
		'class'   => '',
		'content' => '',
	);
	$plugins['api-downloads'] = array(
		'slug'    => 'api-downloads',
		'active'  => true,
		'label'   => __( 'API Downloads', 'yith-woocommerce-customize-myaccount-page' ),
		'icon'    => '',
		'class'   => '',
		'content' => '',
	);
}

if ( defined( 'YITH_WCARS_PREMIUM' ) ) {
	$plugins['refund-requests'] = array(
		'slug'    => YITH_Advanced_Refund_System_My_Account::$my_refund_requests_endpoint,
		'active'  => true,
		'label'   => __( 'Refund Requests', 'yith-woocommerce-customize-myaccount-page' ),
		'icon'    => 'times-circle',
		'class'   => '',
		'content' => '[ywcars_refund_requests]',
	);
}

if ( defined( 'YITH_WCBK_PREMIUM' ) ) {
	$plugins['bookings'] = array(
		'slug'    => 'bookings',
		'active'  => true,
		'label'   => __( 'Bookings', 'yith-woocommerce-customize-myaccount-page' ),
		'icon'    => 'times-circle',
		'class'   => '',
		'content' => '',
	);
}


return apply_filters( 'yith_wcmap_get_plugins_endpoints_array', $plugins );
