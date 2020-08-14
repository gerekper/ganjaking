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
    'post_status'    => 'publish'
);
$badges      = get_posts( $args );

foreach ( $badges as $badge ) {
    $badge_array[ $badge->ID ] = get_the_title( $badge->ID );
}

$rules = YITH_WCBM_Dynamic_Pricing_Compatibility::get_instance()->get_rules();
$rules = !empty( $rules ) ? $rules : array();

$dynamic_link = add_query_arg( array(
    'page' => 'yith_woocommerce_dynamic_pricing_and_discounts',
    'tab'  => 'pricing'
), admin_url('admin.php') );

$dynamic_link_html = "<a href='$dynamic_link'>" . __( 'Dynamic Pricing Settings', 'yith-woocommerce-badges-management' ) . "</a>";
$description       = sprintf( __( 'Select the Badge for Dynamic Pricing Rules. Please Note: you should create rules in %s before', 'yith-woocommerce-badges-management' ), $dynamic_link_html );

$list_dynamic_opt = array(
    'dynamic-pricing-badge-options' => array(
        'title' => __( 'Dynamic Pricing Badges', 'yith-woocommerce-badges-management' ),
        'type'  => 'title',
        'desc'  => $description,
        'id'    => 'yith-wcbm-dynamic-pricing-badge-options'
    )
);

foreach ( $rules as $rule_id => $rule ) {
    $id   = $rule_id;
    $name = isset(  $rule[ 'description' ] ) ?  $rule[ 'description' ] : get_the_title( $rule['id'] );

    $list_dynamic_opt[ 'dynamic-pricing-badge-' . $id ] = array(
        'name'              => $name,
        'type'              => 'select',
        'desc'              => sprintf( __( 'Select the Badge for all products of Dynamic Pricing Rule %s', 'yith-woocommerce-badges-management' ), $name ),
        'id'                => 'yith-wcbm-dynamic-pricing-badge-' . $id,
        'options'           => $badge_array,
        'custom_attributes' => $custom_attributes,
        'default'           => 'none'
    );
}

$list_dynamic_opt[ 'dynamic-pricing-badge-options-end' ] = array(
    'type' => 'sectionend',
    'id'   => 'yith-wcbm-dynamic-pricing-badge-options'
);

$settings = array(
    'dynamic-pricing' => $list_dynamic_opt
);

return $settings;