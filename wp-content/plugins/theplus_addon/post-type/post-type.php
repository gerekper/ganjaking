<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }
$client_post=pt_plus_get_option('post_type','client_post_type');
if(isset($client_post) && !empty($client_post) &&  $client_post=='plugin'){
/*------------------------------------clients post type--------------------------------*/
function theplus_clients_function() {
	$post_name=pt_plus_get_option('post_type','client_plugin_name');	
	if(isset($post_name) && !empty($post_name)){
		$post_name=pt_plus_get_option('post_type','client_plugin_name');
	}else{
		$post_name='theplus_clients';
	}
	$labels = array(
		'name'                  => _x( 'Tp Clients', 'Post Type General Name', 'pt_theplus' ),
		'singular_name'         => _x( 'Tp Clients', 'Post Type Singular Name', 'pt_theplus' ),
		'menu_name'             => __( 'Tp Clients', 'pt_theplus' ),
		'name_admin_bar'        => __( 'Tp Client', 'pt_theplus' ),
		'archives'              => __( 'Item Archives', 'pt_theplus' ),
		'attributes'            => __( 'Item Attributes', 'pt_theplus' ),
		'parent_item_colon'     => __( 'Parent Item:', 'pt_theplus' ),
		'all_items'             => __( 'All Items', 'pt_theplus' ),
		'add_new_item'          => __( 'Add New Item', 'pt_theplus' ),
		'add_new'               => __( 'Add New', 'pt_theplus' ),
		'new_item'              => __( 'New Item', 'pt_theplus' ),
		'edit_item'             => __( 'Edit Item', 'pt_theplus' ),
		'update_item'           => __( 'Update Item', 'pt_theplus' ),
		'view_item'             => __( 'View Item', 'pt_theplus' ),
		'view_items'            => __( 'View Items', 'pt_theplus' ),
		'search_items'          => __( 'Search Item', 'pt_theplus' ),
		'not_found'             => __( 'Not found', 'pt_theplus' ),
		'not_found_in_trash'    => __( 'Not found in Trash', 'pt_theplus' ),
		'featured_image'        => __( 'Featured Image', 'pt_theplus' ),
		'set_featured_image'    => __( 'Set featured image', 'pt_theplus' ),
		'remove_featured_image' => __( 'Remove featured image', 'pt_theplus' ),
		'use_featured_image'    => __( 'Use as featured image', 'pt_theplus' ),
		'insert_into_item'      => __( 'Insert into item', 'pt_theplus' ),
		'uploaded_to_this_item' => __( 'Uploaded to this item', 'pt_theplus' ),
		'items_list'            => __( 'Items list', 'pt_theplus' ),
		'items_list_navigation' => __( 'Items list navigation', 'pt_theplus' ),
		'filter_items_list'     => __( 'Filter items list', 'pt_theplus' ),
	);
	$args = array(
		'label'                 => __( 'Clients', 'pt_theplus' ),
		'description'           => __( 'Post Type Description', 'pt_theplus' ),
		'labels'                => $labels,
		'supports'              => array( 'title', 'editor', 'thumbnail','revisions' ),
		'hierarchical'          => false,
		'public'                => true,
		'show_ui'               => true,
		'show_in_menu'          => true,
		'menu_position'         => 5,
		'show_in_admin_bar'     => true,
		'show_in_nav_menus'     => true,
		'can_export'            => true,
		'has_archive'           => true,		
		'exclude_from_search'   => false,
		'publicly_queryable'    => true,
		'capability_type'       => 'page',
	);
	register_post_type( $post_name, $args );

}
add_action( 'init', 'theplus_clients_function', 0 );

if ( ! function_exists( 'theplus_clients_category' ) ) {
function theplus_clients_category() {
	$post_name=pt_plus_get_option('post_type','client_plugin_name');	
	if(isset($post_name) && !empty($post_name)){
		$post_name=pt_plus_get_option('post_type','client_plugin_name');
	}else{
		$post_name='theplus_clients';
	}
	$category_name=pt_plus_get_option('post_type','client_category_plugin_name');
	if(isset($category_name) && !empty($category_name)){
		$category_name=pt_plus_get_option('post_type','client_category_plugin_name');
	}else{
		$category_name='theplus_clients_cat';
	}
	$labels = array(
		'name'                       => 'Tp Clients Categories',
		'singular_name'              => 'Tp Clients Category',
		'menu_name'                  => 'Tp Clients Category',
		'all_items'                  => 'All Items',
		'parent_item'                => 'Parent Item',
		'parent_item_colon'          => 'Parent Item:',
		'new_item_name'              => 'New Item Name',
		'add_new_item'               => 'Add New Item',
		'edit_item'                  => 'Edit Item',
		'update_item'                => 'Update Item',
		'view_item'                  => 'View Item',
		'separate_items_with_commas' => 'Separate items with commas',
		'add_or_remove_items'        => 'Add or remove items',
		'choose_from_most_used'      => 'Choose from the most used',
		'popular_items'              => 'Popular Items',
		'search_items'               => 'Search Items',
		'not_found'                  => 'Not Found',
	);
	$args = array(
		'labels'                     => $labels,
		'hierarchical'               => true,
		'public'                     => true,
		'show_ui'                    => true,
		'show_admin_column'          => true,
		'show_in_nav_menus'          => true,
		'show_tagcloud'              => true,
	);
	register_taxonomy( $category_name, array( $post_name ), $args );

}
add_action( 'init', 'theplus_clients_category', 0 );
}
/*------------------------------------clients post type-------------------------*/
}
$testimonial_post=pt_plus_get_option('post_type','testimonial_post_type');
if(isset($testimonial_post) && !empty($testimonial_post) && $testimonial_post=='plugin'){
/*------------------------------------testimonials post type -----------------------*/
function theplus_testimonials_func() {
$post_name=pt_plus_get_option('post_type','testimonial_plugin_name');	
	if(isset($post_name) && !empty($post_name)){
		$post_name=pt_plus_get_option('post_type','testimonial_plugin_name');
	}else{
		$post_name='theplus_testimonial';
	}
	$labels = array(
		'name'                  => _x( 'TP Testimonials', 'Post Type General Name', 'pt_theplus' ),
		'singular_name'         => _x( 'TP Testimonials', 'Post Type Singular Name', 'pt_theplus' ),
		'menu_name'             => __( 'TP Testimonials', 'pt_theplus' ),
		'name_admin_bar'        => __( 'TP Testimonial', 'pt_theplus' ),
		'archives'              => __( 'Item Archives', 'pt_theplus' ),
		'parent_item_colon'     => __( 'Parent Item:', 'pt_theplus' ),
		'all_items'             => __( 'All Items', 'pt_theplus' ),
		'add_new_item'          => __( 'Add New Item', 'pt_theplus' ),
		'add_new'               => __( 'Add New', 'pt_theplus' ),
		'new_item'              => __( 'New Item', 'pt_theplus' ),
		'edit_item'             => __( 'Edit Item', 'pt_theplus' ),
		'update_item'           => __( 'Update Item', 'pt_theplus' ),
		'view_item'             => __( 'View Item', 'pt_theplus' ),
		'search_items'          => __( 'Search Item', 'pt_theplus' ),
		'not_found'             => __( 'Not found', 'pt_theplus' ),
		'not_found_in_trash'    => __( 'Not found in Trash', 'pt_theplus' ),
		'featured_image'        => __( 'Profile Image', 'pt_theplus' ),
		'set_featured_image'    => __( 'Set profile image', 'pt_theplus' ),
		'remove_featured_image' => __( 'Remove profile image', 'pt_theplus' ),
		'use_featured_image'    => __( 'Use as profile image', 'pt_theplus' ),
		'insert_into_item'      => __( 'Insert into item', 'pt_theplus' ),
		'uploaded_to_this_item' => __( 'Uploaded to this item', 'pt_theplus' ),
		'items_list'            => __( 'Items list', 'pt_theplus' ),
		'items_list_navigation' => __( 'Items list navigation', 'pt_theplus' ),
		'filter_items_list'     => __( 'Filter items list', 'pt_theplus' ),
	);
	$args = array(
		'label'                 => __( 'TP Testimonials', 'pt_theplus' ),
		'description'           => __( 'Post Type Description', 'pt_theplus' ),
		'labels'                => $labels,
		'supports'              => array( 'title', 'thumbnail','revisions' ),
		'hierarchical'          => false,
		'public'                => true,
		'show_ui'               => true,
		'show_in_menu'          => true,
		'menu_icon'				=> 'dashicons-testimonial',
		'menu_position'         => 5,
		'show_in_admin_bar'     => true,
		'show_in_nav_menus'     => true,
		'can_export'            => true,
		'has_archive'           => true,		
		'exclude_from_search'   => false,
		'publicly_queryable'    => true,
		'capability_type'       => 'page',
	);
	register_post_type( $post_name, $args );

}
add_action( 'init', 'theplus_testimonials_func', 0 );

if ( ! function_exists( 'theplus_testimonial_category' ) ) {
function theplus_testimonial_category() {
$post_name=pt_plus_get_option('post_type','testimonial_plugin_name');	
	if(isset($post_name) && !empty($post_name)){
		$post_name=pt_plus_get_option('post_type','testimonial_plugin_name');
	}else{
		$post_name='theplus_testimonial';
	}
$category_name=pt_plus_get_option('post_type','testimonial_category_plugin_name');
	if(isset($category_name) && !empty($category_name)){
		$category_name=pt_plus_get_option('post_type','testimonial_category_plugin_name');
	}else{
		$category_name='theplus_testimonial_cat';
	}
	$labels = array(
		'name'                       => 'TP Testimonials Categories',
		'singular_name'              => 'TP Testimonials Category',
		'menu_name'                  => 'TP Testimonials Category',
		'all_items'                  => 'All Items',
		'parent_item'                => 'Parent Item',
		'parent_item_colon'          => 'Parent Item:',
		'new_item_name'              => 'New Item Name',
		'add_new_item'               => 'Add New Item',
		'edit_item'                  => 'Edit Item',
		'update_item'                => 'Update Item',
		'view_item'                  => 'View Item',
		'separate_items_with_commas' => 'Separate items with commas',
		'add_or_remove_items'        => 'Add or remove items',
		'choose_from_most_used'      => 'Choose from the most used',
		'popular_items'              => 'Popular Items',
		'search_items'               => 'Search Items',
		'not_found'                  => 'Not Found',
	);
	$args = array(
		'labels'                     => $labels,
		'hierarchical'               => true,
		'public'                     => true,
		'show_ui'                    => true,
		'show_admin_column'          => true,
		'show_in_nav_menus'          => true,
		'show_tagcloud'              => true,
	);
	register_taxonomy( $category_name, array( $post_name ), $args );

}
add_action( 'init', 'theplus_testimonial_category', 0 );
}
/*------------------------------------testimonials post type -----------------------*/
}

/*------------Portfolio post type--------------------------------*/
$portfolio_post=pt_plus_get_option('post_type','portfolio_post_type');
if(isset($portfolio_post) && !empty($portfolio_post) && $portfolio_post=='plugin'){
	
function theplus_portfolio_post_type() {
	$post_name=pt_plus_get_option('post_type','portfolio_plugin_name');	
	if(isset($post_name) && !empty($post_name)){
		$post_name=pt_plus_get_option('post_type','portfolio_plugin_name');
	}else{
		$post_name='theplus_portfolio';
	}
	$labels = array(
		'name'                  => _x( 'TP Portfolio', 'Post Type General Name', 'pt_theplus' ),
		'singular_name'         => _x( 'TP Portfolio', 'Post Type Singular Name', 'pt_theplus' ),
		'menu_name'             => __( 'TP Portfolio', 'pt_theplus' ),
		'name_admin_bar'        => __( 'TP Portfolio', 'pt_theplus' ),
		'archives'              => __( 'Item Archives', 'pt_theplus' ),
		'parent_item_colon'     => __( 'Parent Item:', 'pt_theplus' ),
		'all_items'             => __( 'All Items', 'pt_theplus' ),
		'add_new_item'          => __( 'Add New Item', 'pt_theplus' ),
		'add_new'               => __( 'Add New', 'pt_theplus' ),
		'new_item'              => __( 'New Item', 'pt_theplus' ),
		'edit_item'             => __( 'Edit Item', 'pt_theplus' ),
		'update_item'           => __( 'Update Item', 'pt_theplus' ),
		'view_item'             => __( 'View Item', 'pt_theplus' ),
		'search_items'          => __( 'Search Item', 'pt_theplus' ),
		'not_found'             => __( 'Not found', 'pt_theplus' ),
		'not_found_in_trash'    => __( 'Not found in Trash', 'pt_theplus' ),
		'featured_image'        => __( 'Featured Image', 'pt_theplus' ),
		'set_featured_image'    => __( 'Set featured image', 'pt_theplus' ),
		'remove_featured_image' => __( 'Remove featured image', 'pt_theplus' ),
		'use_featured_image'    => __( 'Use as featured image', 'pt_theplus' ),
		'insert_into_item'      => __( 'Insert into item', 'pt_theplus' ),
		'uploaded_to_this_item' => __( 'Uploaded to this item', 'pt_theplus' ),
		'items_list'            => __( 'Items list', 'pt_theplus' ),
		'items_list_navigation' => __( 'Items list navigation', 'pt_theplus' ),
		'filter_items_list'     => __( 'Filter items list', 'pt_theplus' ),
	);
	$args = array(
		'label'                 => __( 'Portfolio', 'pt_theplus' ),
		'description'           => __( 'Portfolio Description', 'pt_theplus' ),
		'labels'                => $labels,
		'supports'              => array( 'title', 'editor', 'thumbnail','revisions' ),
		'taxonomies'            => array(),
		'hierarchical'          => false,
		'public'                => true,
		'show_ui'               => true,
		'show_in_menu'          => true,
		'menu_position'         => 5,
		'show_in_admin_bar'     => true,
		'show_in_nav_menus'     => true,
		'can_export'            => true,
		'has_archive'           => true,		
		'menu_icon'   => 'dashicons-portfolio',
		'exclude_from_search'   => false,
		'publicly_queryable'    => true,
		'capability_type'       => 'page',
	);
	register_post_type( $post_name, $args );

}
add_action( 'init', 'theplus_portfolio_post_type', 0 );

if ( ! function_exists( 'theplus_register_portfolio_category' ) ) {
function theplus_register_portfolio_category() {
	$post_name=pt_plus_get_option('post_type','portfolio_plugin_name');	
	if(isset($post_name) && !empty($post_name)){
		$post_name=pt_plus_get_option('post_type','portfolio_plugin_name');
	}else{
		$post_name='theplus_portfolio';
	}
	$category_name=pt_plus_get_option('post_type','portfolio_category_plugin_name');
	if(isset($category_name) && !empty($category_name)){
		$category_name=pt_plus_get_option('post_type','portfolio_category_plugin_name');
	}else{
		$category_name='theplus_portfolio_category';
	}
	$labels = array(
		'name'                       => 'TP Portfolio Categories',
		'singular_name'              => 'TP Portfolio Category',
		'menu_name'                  => 'TP Portfolio Category',
		'all_items'                  => 'All Items',
		'parent_item'                => 'Parent Item',
		'parent_item_colon'          => 'Parent Item:',
		'new_item_name'              => 'New Item Name',
		'add_new_item'               => 'Add New Item',
		'edit_item'                  => 'Edit Item',
		'update_item'                => 'Update Item',
		'view_item'                  => 'View Item',
		'separate_items_with_commas' => 'Separate items with commas',
		'add_or_remove_items'        => 'Add or remove items',
		'choose_from_most_used'      => 'Choose from the most used',
		'popular_items'              => 'Popular Items',
		'search_items'               => 'Search Items',
		'not_found'                  => 'Not Found',
	);
	$args = array(
		'labels'                     => $labels,
		'hierarchical'               => true,
		'public'                     => true,
		'show_ui'                    => true,
		'show_admin_column'          => true,
		'show_in_nav_menus'          => true,
		'show_tagcloud'              => true,
	);
	register_taxonomy( $category_name, array( $post_name ), $args );

}
add_action( 'init', 'theplus_register_portfolio_category', 0 );
}
}
/*------------Portfolio post type--------------------------------*/
/*------------------------------------Team member post type-------------------------*/
$team_member_post=pt_plus_get_option('post_type','team_member_post_type');
if(isset($team_member_post) && !empty($team_member_post) && $team_member_post=='plugin'){
function theplus_team_member_function() {
$post_name=pt_plus_get_option('post_type','team_member_plugin_name');	
	if(isset($post_name) && !empty($post_name)){
		$post_name=pt_plus_get_option('post_type','team_member_plugin_name');
	}else{
		$post_name='theplus_team_member';
	}
	$labels = array(
		'name'                  => _x( 'TP Team Members', 'Post Type General Name', 'pt_theplus' ),
		'singular_name'         => _x( 'TP Team Member', 'Post Type Singular Name', 'pt_theplus' ),
		'menu_name'             => __( 'TP Team Member', 'pt_theplus' ),
		'name_admin_bar'        => __( 'TP Team Member', 'pt_theplus' ),
		'archives'              => __( 'Item Archives', 'pt_theplus' ),
		'attributes'            => __( 'Item Attributes', 'pt_theplus' ),
		'parent_item_colon'     => __( 'Parent Item:', 'pt_theplus' ),
		'all_items'             => __( 'All Items', 'pt_theplus' ),
		'add_new_item'          => __( 'Add New Item', 'pt_theplus' ),
		'add_new'               => __( 'Add New', 'pt_theplus' ),
		'new_item'              => __( 'New Item', 'pt_theplus' ),
		'edit_item'             => __( 'Edit Item', 'pt_theplus' ),
		'update_item'           => __( 'Update Item', 'pt_theplus' ),
		'view_item'             => __( 'View Item', 'pt_theplus' ),
		'view_items'            => __( 'View Items', 'pt_theplus' ),
		'search_items'          => __( 'Search Item', 'pt_theplus' ),
		'not_found'             => __( 'Not found', 'pt_theplus' ),
		'not_found_in_trash'    => __( 'Not found in Trash', 'pt_theplus' ),
		'featured_image'        => __( 'Featured Image', 'pt_theplus' ),
		'set_featured_image'    => __( 'Set featured image', 'pt_theplus' ),
		'remove_featured_image' => __( 'Remove featured image', 'pt_theplus' ),
		'use_featured_image'    => __( 'Use as featured image', 'pt_theplus' ),
		'insert_into_item'      => __( 'Insert into item', 'pt_theplus' ),
		'uploaded_to_this_item' => __( 'Uploaded to this item', 'pt_theplus' ),
		'items_list'            => __( 'Items list', 'pt_theplus' ),
		'items_list_navigation' => __( 'Items list navigation', 'pt_theplus' ),
		'filter_items_list'     => __( 'Filter items list', 'pt_theplus' ),
	);
	$args = array(
		'label'                 => __( 'TP Team Member', 'pt_theplus' ),
		'description'           => __( 'Post Type Description', 'pt_theplus' ),
		'labels'                => $labels,
		'supports'              => array( 'title', 'editor', 'excerpt', 'author', 'thumbnail','revisions', 'custom-fields', ),
		'hierarchical'          => false,
		'public'                => true,
		'show_ui'               => true,
		'show_in_menu'          => true,
		'menu_position'         => 5,
		'show_in_admin_bar'     => true,
		'show_in_nav_menus'     => true,
		'can_export'            => true,
		'has_archive'           => true,	
		'menu_icon'   => 'dashicons-id-alt',
		'exclude_from_search'   => false,
		'publicly_queryable'    => true,
		'capability_type'       => 'page',
	);
	register_post_type( $post_name, $args );

}
add_action( 'init', 'theplus_team_member_function', 0 );

if ( ! function_exists( 'theplus_register_team_member_category' ) ) {
function theplus_register_team_member_category() {
$post_name=pt_plus_get_option('post_type','team_member_plugin_name');	
	if(isset($post_name) && !empty($post_name)){
		$post_name=pt_plus_get_option('post_type','team_member_plugin_name');
	}else{
		$post_name='theplus_team_member';
	}
	$category_name=pt_plus_get_option('post_type','team_member_category_plugin_name');
	if(isset($category_name) && !empty($category_name)){
		$category_name=pt_plus_get_option('post_type','team_member_category_plugin_name');
	}else{
		$category_name='theplus_team_member_cat';
	}
	$labels = array(
		'name'                       => 'Team Member Categories',
		'singular_name'              => 'Team Member Category',
		'menu_name'                  => 'TP Team Member Category',
		'all_items'                  => 'All Items',
		'parent_item'                => 'Parent Item',
		'parent_item_colon'          => 'Parent Item:',
		'new_item_name'              => 'New Item Name',
		'add_new_item'               => 'Add New Item',
		'edit_item'                  => 'Edit Item',
		'update_item'                => 'Update Item',
		'view_item'                  => 'View Item',
		'separate_items_with_commas' => 'Separate items with commas',
		'add_or_remove_items'        => 'Add or remove items',
		'choose_from_most_used'      => 'Choose from the most used',
		'popular_items'              => 'Popular Items',
		'search_items'               => 'Search Items',
		'not_found'                  => 'Not Found',
	);
	$args = array(
		'labels'                     => $labels,
		'hierarchical'               => true,
		'public'                     => true,
		'show_ui'                    => true,
		'show_admin_column'          => true,
		'show_in_nav_menus'          => true,
		'show_tagcloud'              => true,
	);
	register_taxonomy( $category_name, array( $post_name ), $args );

}
add_action( 'init', 'theplus_register_team_member_category', 0 );
}
}
/*------------------------------------team meamber post type End ------------------*/