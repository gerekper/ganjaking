<?php
if ( !defined( 'ABSPATH' ) ) {
    exit;
} // Exit if accessed directly

$custom_attributes = defined( 'YITH_WCBM_PREMIUM' ) ? '' : array( 'disabled' => 'disabled' );

// Create Array for badge select
$badge_array = array( 'none' => __( 'None', 'yith-woocommerce-badges-management' ) );
$args        = array(
    'posts_per_page' => -1,
    'post_type'      => 'yith-wcbm-badge',
    'orderby'        => 'title',
    'order'          => 'ASC',
    'post_status'    => 'publish',
    'fields'         => 'ids'
);
$badge_ids   = get_posts( $args );

foreach ( $badge_ids as $badge_id ) {
    $badge_array[ $badge_id ] = get_the_title( $badge_id );
}

$auction_statuses = array(
    'not-started' => __( 'Not Started', 'yith-woocommerce-auctions' ),
    'started'     => __( 'Started', 'yith-woocommerce-auctions' ),
    'finished'    => __( 'Finished', 'yith-woocommerce-auctions' )
);

$list_auctions_opt = array(
    'auctions-badge-options' => array(
        'title' => __( 'Auction Badges', 'yith-woocommerce-badges-management' ),
        'type'  => 'title',
        'desc'  => __( 'Select the badges for auction statuses', 'yith-woocommerce-badges-management' ),
    )
);

foreach ( $auction_statuses as $status_slug => $status_name ) {
    $list_auctions_opt[ 'auction-badge-' . $status_slug ] = array(
        'name'              => $status_name,
        'type'              => 'select',
        'desc'              => sprintf( __( 'Select the badge for all auctions marked as "%s"', 'yith-woocommerce-badges-management' ), $status_name ),
        'id'                => 'yith-wcbm-auction-badge-' . $status_slug,
        'options'           => $badge_array,
        'custom_attributes' => $custom_attributes,
        'default'           => 'none'
    );
}

$list_auctions_opt[ 'auctions-badge-options-end' ] = array(
    'type' => 'sectionend',
);

$settings = array(
    'auctions' => $list_auctions_opt
);

return $settings;