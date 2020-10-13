<?php
/**
 * Update Data to 20160506
 *  - Move all request products into the new wc_warranty_products table
 */
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

$statuses = warranty_get_statuses();
$found = false;

foreach ( $statuses as $status ) {
    if ( $status->name == 'Reviewing' ) {
        $found = true;
        break;
    }
}

if ( !$found ) {
    wp_insert_term( 'Reviewing', 'shop_warranty_status' );
}

$q = new WP_Query(array(
    'post_type'     => 'warranty_request',
    'nopaging'      => true,
    'fields'        => 'ids',
    'tax_query'     => array(
        array(
            'taxonomy'  => 'shop_warranty_status',
            'operator'  => 'NOT EXISTS'
        )
    )
));
$posts = $q->get_posts();

foreach ( $posts as $post_id ) {
    wp_set_object_terms( $post_id, 'new', 'shop_warranty_status' );
}

delete_option( 'warranty_needs_update' );
update_option( 'warranty_db_version', '20160506' );
wp_redirect( admin_url('admin.php?page=warranties&view=updater&act=migrate_products') );
exit;