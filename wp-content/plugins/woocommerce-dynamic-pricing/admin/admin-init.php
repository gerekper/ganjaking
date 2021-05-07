<?php

// Options page caps
add_filter( 'option_page_capability__s_membership_pricing_rules', 'woocommerce_pricing_options_page_caps' );
add_filter( 'option_page_capability__a_category_pricing_rules', 'woocommerce_pricing_options_page_caps' );
add_filter( 'option_page_capability__s_category_pricing_rules', 'woocommerce_pricing_options_page_caps' );
add_filter( 'option_page_capability__a_totals_pricing_rules', 'woocommerce_pricing_options_page_caps' );

add_filter( 'option_page_capability__a_taxonomy_product_brand_pricing_rules', 'woocommerce_pricing_options_page_caps' );
add_filter( 'option_page_capability__s_taxonomy_product_brand_pricing_rules', 'woocommerce_pricing_options_page_caps' );


function woocommerce_pricing_options_page_caps( $capability ) {
	return 'manage_woocommerce';
}

require 'classes/product_pricing_rules_admin.class.php';
require 'classes/category_pricing_rules_admin.class.php';
require 'classes/membership_pricing_rules_admin.class.php';
require 'classes/totals_pricing_rules_admin.class.php';
require 'classes/store_pricing_rules_admin.class.php';
require 'classes/group_pricing_rules_admin.class.php';
require 'classes/taxonomy_pricing_rules_admin.class.php';

global $wc_store_pricing_admin;
$wc_store_pricing_admin = new woocommerce_store_pricing_rules_admin();

global $wc_product_pricing_admin;
$wc_product_pricing_admin = new woocommerce_product_pricing_rules_admin();

function woocommerce_pricing_product_admin_create_empty_ruleset() {
	global $wc_product_pricing_admin;
	$wc_product_pricing_admin->create_empty_ruleset( uniqid( 'set_' ) );
	die();
}

function woocommerce_pricing_category_admin_create_empty_ruleset() {
	global $wc_store_pricing_admin;
	$wc_store_pricing_admin->category_admin->create_empty_ruleset( uniqid( 'set_' ) );
	die();
}

function woocommerce_pricing_taxonomy_admin_create_empty_ruleset() {
    global $wc_store_pricing_admin;
    $wc_store_pricing_admin->taxonomy_admins[ $_POST['taxonomy'] ]->create_empty_ruleset( uniqid( 'set_' ) );
    die();
}

function woocommerce_pricing_totals_admin_create_empty_ruleset() {
	global $wc_store_pricing_admin;
	$wc_store_pricing_admin->totals_admin->create_empty_ruleset( uniqid( 'set_' ) );
	die();
}

add_action( 'wp_ajax_create_empty_ruleset', 'woocommerce_pricing_product_admin_create_empty_ruleset' );
add_action( 'wp_ajax_create_empty_category_ruleset', 'woocommerce_pricing_category_admin_create_empty_ruleset' );
add_action( 'wp_ajax_create_empty_totals_ruleset', 'woocommerce_pricing_totals_admin_create_empty_ruleset' );

add_action( 'wp_ajax_create_empty_taxonomy_ruleset', 'woocommerce_pricing_taxonomy_admin_create_empty_ruleset' );

