<?php
// Exit if accessed directly
!defined( 'YITH_WCMBS' ) && exit();

$shortcode = '<code>[membership_download_product_links]</code>';

$plans                = YITH_WCMBS_Manager()->get_plans();
$all_membership_plans = array();

if ( $plans && is_array( $plans ) ) {
    foreach ( $plans as $plan ) {
        $all_membership_plans[ $plan->ID ] = $plan->post_title;
    }
}


$tab = array(
    'settings' => array(
        'membership-options'                           => array(
            'title' => __( 'Membership Options', 'yith-woocommerce-membership' ),
            'type'  => 'title',
            'desc'  => '',
            'id'    => 'yith-wcmbs-membership-options',
        ),
        'membership-hide-contents'                     => array(
            'name'     => __( 'Hide Contents', 'yith-woocommerce-membership' ),
            'type'     => 'select',
            'desc'     => __( 'Select how you want to manage contents.', 'yith-woocommerce-membership' ),
            'id'       => 'yith-wcmbs-hide-contents',
            'options'  => array(
                'all'                 => __( 'Hide all', 'yith-woocommerce-membership' ),
                'alternative_content' => __( 'Show alternative contents', 'yith-woocommerce-membership' ),
                'redirect'            => __( 'Show and redirect users', 'yith-woocommerce-membership' ),
            ),
            'default'  => 'all',
            'multiple' => false,
        ),
        'membership-default-alternative-content'       => array(
            'name'      => __( 'Default Alternative Content', 'yith-woocommerce-membership' ),
            'type'      => 'yith-field',
            'yith-type' => 'textarea-editor',
            'desc'      => __( 'Set the default alternative content.', 'yith-woocommerce-membership' ),
            'id'        => 'yith-wcmbs-default-alternative-content',
            'default'   => '',
            'deps'      => array(
                'id'    => 'yith-wcmbs-hide-contents',
                'value' => 'alternative_content'
            )
        ),
        'membership-redirect-link'                     => array(
            'name'    => __( 'Redirect to', 'yith-woocommerce-membership' ),
            'type'    => 'text',
            'desc'    => __( 'Insert redirecting link', 'yith-woocommerce-membership' ),
            'id'      => 'yith-wcmbs-redirect-link',
            'default' => '',
        ),
        'show-membership-history-in-my-account'        => array(
            'name'    => __( 'Show membership history in My Account page', 'yith-woocommerce-membership' ),
            'type'    => 'checkbox',
            'desc'    => __( 'Select if you want to display the history of all user memberships in My Account page', 'yith-woocommerce-membership' ),
            'id'      => 'yith-wcmbs-show-history-in-my-account',
            'default' => 'yes',
        ),
        'enable-guest-checkout-for-memberships'        => array(
            'name'    => __( 'Allow guests to purchase membership related products', 'yith-woocommerce-membership' ),
            'type'    => 'checkbox',
            'desc'    => __( 'Allow guest users to purchase a product linked to a membership without creating an account. Note: these guests won\'t have access to the membership plan linked to the product.', 'yith-woocommerce-membership' ),
            'id'      => 'yith-wcmbs-enable-guest-checkout',
            'default' => 'no',
        ),
        'advanced-membership-admin'                    => array(
            'name'    => __( 'Membership advanced management', 'yith-woocommerce-membership' ),
            'type'    => 'checkbox',
            'desc'    => __( 'Enable membership advanced management to delete memberships or to change whatever settings in users\' memberships (expiration date, status, credits etc...). Please note: ensure you use this option only when necessary.', 'yith-woocommerce-membership' ),
            'id'      => 'yith-wcmbs-advanced-membership-admin',
            'default' => 'no',
        ),
        'memberships-on-user-register'                 => array(
            'name'    => __( 'Membership plan on user registration', 'yith-woocommerce-membership' ),
            'type'    => 'multiselect',
            'class'   => 'wc-enhanced-select',
            'desc'    => __( 'Associate automatically the above membership plans to newly registered users', 'yith-woocommerce-membership' ),
            'id'      => 'yith-wcmbs-memberships-on-user-register',
            'options' => $all_membership_plans,
            'default' => array(),
        ),
        'membership-options-end'                       => array(
            'type' => 'sectionend',
            'id'   => 'yith-wcmbs-membership-options'
        ),
        'products-in-membership-options'               => array(
            'title' => __( 'Product in membership options', 'yith-woocommerce-membership' ),
            'type'  => 'title',
            'desc'  => '',
            'id'    => 'yith-wcmbs-products-in-membership-options'
        ),
        'products-in-membership'                       => array(
            'name'     => __( 'Products in membership', 'yith-woocommerce-membership' ),
            'type'     => 'select',
            'desc'     => __( 'Select how you want manage products for membership.', 'yith-woocommerce-membership' ),
            'id'       => 'yith-wcmbs-products-in-membership-management',
            'options'  => array(
                'hide_products'  => __( 'Show products only to members', 'yith-woocommerce-membership' ),
                'allow_download' => __( 'Show products to all. Allow downloads for members', 'yith-woocommerce-membership' ),
            ),
            'default'  => 'hide_products',
            'multiple' => false,
        ),
        'download-link-position'                       => array(
            'name'    => __( 'Download link position', 'yith-woocommerce-membership' ),
            'type'    => 'select',
            'desc'    => sprintf( __( 'Choose where you want to show the download link in single product page or use the shortcode %s.', 'yith-woocommerce-membership' ), $shortcode ),
            'id'      => 'yith-wcmbs-download-link-position',
            'options' => array(
                'tab'                => __( 'Download Tab', 'yith-woocommerce-membership' ),
                'before_summary'     => __( 'Before summary', 'yith-woocommerce-membership' ),
                'before_description' => __( 'Before description', 'yith-woocommerce-membership' ),
                'after_description'  => __( 'After description', 'yith-woocommerce-membership' ),
                'after_add_to_cart'  => __( 'After "Add to Cart" Button', 'yith-woocommerce-membership' ),
                'after_summary'      => __( 'After summary', 'yith-woocommerce-membership' ),
                'use_shortcode'      => __( 'Use Shortcode', 'yith-woocommerce-membership' ),
            ),
            'default' => 'tab'
        ),
        'hide-price-and-add-to-cart'                   => array(
            'name'    => __( 'Hide price and add to cart', 'yith-woocommerce-membership' ),
            'type'    => 'checkbox',
            'desc'    => __( 'Hide price and "add to cart" button in Single Product Page if members can download the product.', 'yith-woocommerce-membership' ),
            'id'      => 'yith-wcmbs-hide-price-and-add-to-cart',
            'default' => 'no',
        ),
        'products-in-membership-options-end'           => array(
            'type' => 'sectionend',
            'id'   => 'yith-wcmbs-products-in-membership-options'
        ),
        'report-options'                               => array(
            'title' => __( 'Reports', 'yith-woocommerce-membership' ),
            'type'  => 'title',
        ),
        'show-memebership-info-in-reports'             => array(
            'name'    => __( 'Show Membership info in Reports', 'yith-woocommerce-membership' ),
            'type'    => 'checkbox',
            'desc'    => __( 'Show Membership info column in "downloads by user" reports', 'yith-woocommerce-membership' ),
            'id'      => 'yith-wcmbs-show-membership-info-in-reports',
            'default' => 'yes',
        ),
        'use-external-services-to-get-user-ip-address' => array(
            'name'    => __( 'Use external services to get user IP Address', 'yith-woocommerce-membership' ),
            'type'    => 'checkbox',
            'desc'    => __( 'If enabled, get user IP Address using external services.', 'yith-woocommerce-membership' ),
            'id'      => 'yith-wcmbs-use-external-services-to-get-user-ip-address',
            'default' => 'yes',
        ),
        'report-options-end'                           => array(
            'type' => 'sectionend',
        ),
    )
);

if ( defined( 'YITH_WCMBS_RESTRICTED_CPT_MANAGEMENT_ENABLED' ) && YITH_WCMBS_RESTRICTED_CPT_MANAGEMENT_ENABLED ) {
    $restricted_cpt_settings = array(
        'custom-post-types-options'               => array(
            'title' => __( 'Custom Post Types', 'yith-woocommerce-membership' ),
            'type'  => 'title',
            'desc'  => '',
            'id'    => 'yith-wcmbs-custom-post-types-options'
        ),
        'membership-restricted-custom-post-types' => array(
            'name'    => __( 'Membership Restricted Custom Post types', 'yith-woocommerce-membership' ),
            'type'    => 'multiselect',
            'desc'    => __( 'Select the custom post types you want enable for Membership', 'yith-woocommerce-membership' ),
            'id'      => 'yith-wcmbs-membership-restricted-custom-post-types',
            'options' => yith_wcmbs_get_other_custom_post_types( 'id=>name' ),
            'class'   => 'yith-wcmbs-select2'
        ),
        'custom-post-types-options-end'           => array(
            'type' => 'sectionend',
            'id'   => 'yith-wcmbs-custom-post-types-options'
        ),
    );

    $tab[ 'settings' ] = array_merge( $tab[ 'settings' ], $restricted_cpt_settings );
}

return $tab;