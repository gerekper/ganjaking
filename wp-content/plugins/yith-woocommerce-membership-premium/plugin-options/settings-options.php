<?php
// Exit if accessed directly
! defined( 'YITH_WCMBS' ) && exit();

$alternative_contents_url = add_query_arg( array( 'post_type' => YITH_WCMBS_Post_Types::$alternative_contents ), admin_url( 'edit.php' ) );

$tab = array(
	'settings' => array(
		'general-options' => array(
			'title' => __( 'General Options', 'yith-woocommerce-membership' ),
			'type'  => 'title',
			'desc'  => '',
		),

		'enable-memberships-on-user-register' => array(
			'id'        => 'yith-wcmbs-memberships-on-user-register-enabled',
			'name'      => __( 'Automatically assign new users to specific membership plan(s)', 'yith-woocommerce-membership' ),
			'type'      => 'yith-field',
			'yith-type' => 'onoff',
			'desc'      => __( 'Enable if you want to automatically assign all newly registered users to one or more specific plans.', 'yith-woocommerce-membership' ),
			'default'   => yith_wcmbs_settings()->get_default( 'yith-wcmbs-memberships-on-user-register-enabled' ),
		),

		'memberships-on-user-register' => array(
			'id'        => 'yith-wcmbs-memberships-on-user-register',
			'name'      => __( 'Choose which plan(s) will be assigned to all new customers', 'yith-woocommerce-membership' ),
			'type'      => 'yith-field',
			'yith-type' => 'ajax-posts',
			'multiple'  => true,
			'data'      => array(
				'placeholder' => __( 'Search plans...', 'yith-woocommerce-membership' ),
				'post_type'   => YITH_WCMBS_Post_Types::$plan,
			),
			'deps'      => array(
				'id'    => 'yith-wcmbs-memberships-on-user-register-enabled',
				'value' => 'yes',
				'type'  => 'fadeIn',
			),
			'desc'      => __( 'Choose the membership plan(s) that will automatically be assigned to all newly registered users.', 'yith-woocommerce-membership' ),
			'default'   => yith_wcmbs_settings()->get_default( 'yith-wcmbs-memberships-on-user-register' ),
		),

		'membership-hide-contents' => array(
			'id'        => 'yith-wcmbs-hide-contents',
			'name'      => __( 'How to manage access to restricted content', 'yith-woocommerce-membership' ),
			'type'      => 'yith-field',
			'yith-type' => 'radio',
			'options'   => array(
				'all'                 => __( 'Hide content - Members will view the restricted contents, non-members will be redirected to a 404 page.', 'yith-woocommerce-membership' ),
				'redirect'            => __( 'Show items and redirect - Non-members will be able to see the items on archive pages, but will be redirected to a specific URL when trying to open them and see the full content.', 'yith-woocommerce-membership' ),
				'alternative_content' => __( 'Show alternative content - The full content will be accessible to members only and you can set alternative content for non-members.', 'yith-woocommerce-membership' ),
			),
			'default'   => yith_wcmbs_settings()->get_default( 'yith-wcmbs-hide-contents' ),
			'desc'      => __( 'Choose how to manage access to restricted content for members and non-members.', 'yith-woocommerce-membership' ),
		),

		'membership-default-alternative-content-mode' => array(
			'id'        => 'yith-wcmbs-default-alternative-content-mode',
			'name'      => __( 'Default alternative content for non-members', 'yith-woocommerce-membership' ),
			'type'      => 'yith-field',
			'yith-type' => 'radio',
			'options'   => array(
				'set'  => __( 'Set an alternative content here', 'yith-woocommerce-membership' ),
				'load' => __( 'Load an alternative content block', 'yith-woocommerce-membership' ),
			),
			'default'   => yith_wcmbs_settings()->get_default( 'yith-wcmbs-default-alternative-content-mode' ),
			'deps'      => array(
				'id'    => 'yith-wcmbs-hide-contents',
				'value' => 'alternative_content',
				'type'  => 'fadeIn',
			),
			'desc'      => implode( '<br />', array(
				__( 'Choose which alternative content will be shown for non-members.', 'yith-woocommerce-membership' ),
				__( 'You can enter a custom one in the editor below or choose a previously created alternative content block to load.', 'yith-woocommerce-membership' ),
			) ),
		),

		'membership-default-alternative-content' => array(
			'id'        => 'yith-wcmbs-default-alternative-content',
			'name'      => __( 'Default Alternative Content', 'yith-woocommerce-membership' ),
			'type'      => 'yith-field',
			'yith-type' => 'textarea-editor',
			'default'   => yith_wcmbs_settings()->get_default( 'yith-wcmbs-default-alternative-content' ),
			'desc'      => __( 'Set a default alternative content for all contents.', 'yith-woocommerce-membership' ),
		),

		'membership-default-alternative-content-id' => array(
			'id'        => 'yith-wcmbs-default-alternative-content-id',
			'name'      => __( 'Default Alternative Content Block', 'yith-woocommerce-membership' ),
			'type'      => 'yith-field',
			'yith-type' => 'ajax-posts',
			'data'      => array(
				'placeholder' => __( 'Search Alternative Content Block', 'yith-woocommerce-membership' ),
				'post_type'   => YITH_WCMBS_Post_Types::$alternative_contents,
				'allow_clear' => true,
			),
			'default'   => yith_wcmbs_settings()->get_default( 'yith-wcmbs-default-alternative-content-id' ),
			'desc'      => implode( '<br />',
									array(
										__( 'Choose the alternative content block to be shown to non-members.', 'yith-woocommerce-membership' ),
										sprintf(
										// translators: %s is the text (with link) of the "YITH > Membership > Alternative Content Blocks" menu
											__( 'You can create alternative content blocks in "%s".', 'yith-woocommerce-membership' ),
											'<a href="' . $alternative_contents_url . '" target="_blank">YITH > Membership > ' . _x( 'Alternative Content Blocks', 'Tab title in plugin settings panel', 'yith-woocommerce-membership' ) . '</a>'
										),
									)
			),
		),

		'membership-redirect-link' => array(
			'id'        => 'yith-wcmbs-redirect-link',
			'name'      => __( 'By default, redirect non-members to', 'yith-woocommerce-membership' ),
			'type'      => 'yith-field',
			'yith-type' => 'text',
			'default'   => yith_wcmbs_settings()->get_default( 'yith-wcmbs-redirect-link' ),
			'deps'      => array(
				'id'    => 'yith-wcmbs-hide-contents',
				'value' => 'redirect',
				'type'  => 'fadeIn',
			),
			'desc'      => __( 'Enter the URL where to redirect non-member users. You can set a different URL in each post/page/product.', 'yith-woocommerce-membership' ),
		),

		'show-memberships-menu-in-my-account' => array(
			'id'        => 'yith-wcmbs-show-memberships-menu-in-my-account',
			'name'      => __( 'Show Memberships menu in My Account page', 'yith-woocommerce-membership' ),
			'type'      => 'yith-field',
			'yith-type' => 'onoff',
			'default'   => yith_wcmbs_settings()->get_default( 'yith-wcmbs-show-memberships-menu-in-my-account' ),
			'desc'      => __( 'Enable to show the Memberships menu in the "My Account" page, of your customers.', 'yith-woocommerce-membership' ),
		),

		'general-options-end' => array(
			'type' => 'sectionend',
		),

		'shop-options' => array(
			'title' => __( 'Shop Options', 'yith-woocommerce-membership' ),
			'type'  => 'title',
			'desc'  => '',
		),

		'enable-guest-checkout-for-memberships' => array(
			'id'        => 'yith-wcmbs-enable-guest-checkout',
			'name'      => __( 'Allow guests to buy products linked to a membership plan', 'yith-woocommerce-membership' ),
			'type'      => 'yith-field',
			'yith-type' => 'onoff',
			'default'   => yith_wcmbs_settings()->get_default( 'yith-wcmbs-enable-guest-checkout' ),
			'desc'      => __( 'Allow guest users to purchase a product linked to a membership without creating an account. Note: these guests won\'t have access to the membership plan linked to the product.', 'yith-woocommerce-membership' ),
		),

		'products-in-membership' => array(
			'id'        => 'yith-wcmbs-products-in-membership-management',
			'name'      => __( 'How to manage access to restricted products', 'yith-woocommerce-membership' ),
			'type'      => 'yith-field',
			'yith-type' => 'radio',
			'options'   => array(
				'hide_products'  => __( 'Only members can view products', 'yith-woocommerce-membership' ),
				'allow_download' => __( 'Everybody can view products, but only members can download them (for free or by using credits)', 'yith-woocommerce-membership' ),
			),
			'default'   => yith_wcmbs_settings()->get_default( 'yith-wcmbs-products-in-membership-management' ),
			'desc'      => __( 'Choose how to manage access to restricted products for members and non-members.', 'yith-woocommerce-membership' ),
		),

		'download-link-position' => array(
			'id'        => 'yith-wcmbs-download-link-position',
			'name'      => __( 'Download link position', 'yith-woocommerce-membership' ),
			'type'      => 'yith-field',
			'yith-type' => 'select',
			'desc'      => sprintf( __( 'Choose where you want to show the download link in single product page or use the shortcode %s.', 'yith-woocommerce-membership' ), '<code>[membership_download_product_links]</code>' ),
			'options'   => array(
				'tab'                => __( 'Download Tab', 'yith-woocommerce-membership' ),
				'before_summary'     => __( 'Before summary', 'yith-woocommerce-membership' ),
				'before_description' => __( 'Before description', 'yith-woocommerce-membership' ),
				'after_description'  => __( 'After description', 'yith-woocommerce-membership' ),
				'after_add_to_cart'  => __( 'After "Add to Cart" Button', 'yith-woocommerce-membership' ),
				'after_summary'      => __( 'After summary', 'yith-woocommerce-membership' ),
				'use_shortcode'      => __( 'Use Shortcode', 'yith-woocommerce-membership' ),
			),
			'deps'      => array(
				'id'    => 'yith-wcmbs-products-in-membership-management',
				'value' => 'allow_download',
				'type'  => 'fadeIn',
			),
			'default'   => yith_wcmbs_settings()->get_default( 'yith-wcmbs-download-link-position' ),
		),

		'default-credits-for-product' => array(
			'id'                    => 'yith-wcmbs-default-credits-for-product',
			'name'                  => __( 'If you use a credit plan, by default, each product download equals to', 'yith-woocommerce-membership' ),
			'type'                  => 'yith-field',
			'yith-type'             => 'number',
			'desc'                  => __( 'Set how many credits will be decreased for each product download. You can override this value in each product.', 'yith-woocommerce-membership' ),
			'min'                   => 1,
			'default'               => 1,
			'class'                 => 'yith-wcmbs-short-inline-field',
			'yith-wcmbs-after-text' => '<span class="yith-wcmbs-after-field-text">' . _x( 'credits', 'Text after numeric field', 'yith-woocommerce-membership' ) . '</span>',
			'deps'                  => array(
				'id'    => 'yith-wcmbs-products-in-membership-management',
				'value' => 'allow_download',
				'type'  => 'fadeIn',
			),
		),

		'hide-price-and-add-to-cart' => array(
			'id'        => 'yith-wcmbs-hide-price-and-add-to-cart',
			'name'      => __( 'Hide price and add to cart buttons for members', 'yith-woocommerce-membership' ),
			'type'      => 'yith-field',
			'yith-type' => 'onoff',
			'desc'      => __( 'Enable to hide prices and add to cart buttons for members that can download the products.', 'yith-woocommerce-membership' ),
			'default'   => yith_wcmbs_settings()->get_default( 'yith-wcmbs-hide-price-and-add-to-cart' ),
		),

		'retrieve-membership-discount-settings-from' => array(
			'id'        => 'yith-wcmbs-retrieve-membership-discount-settings',
			'name'      => __( 'Discounts applied are', 'yith-woocommerce-membership' ),
			'type'      => 'yith-field',
			'yith-type' => 'radio',
			'desc'      => implode( '<br />', array(
				__( 'Choose how to handle discounts for members.', 'yith-woocommerce-membership' ),
				__( "This is useful, for instance, when you edit the discount percentage of a plan after a while: you will be able to choose whether the members will benefit of the original discount (the one of when they bought the plan) or the new percentage that you've set up later.", 'yith-woocommerce-membership' ),
			) ),
			'options'   => array(
				'membership' => __( 'discounts set when the customer purchases the membership plan.', 'yith-woocommerce-membership' ),
				'plan'       => __( 'discounts currently set in the membership plan.', 'yith-woocommerce-membership' ),
			),
			'default'   => yith_wcmbs_settings()->get_default( 'yith-wcmbs-retrieve-membership-discount-settings' ),
		),

		'shop-options-end' => array(
			'type' => 'sectionend',
		),


		'reports-options' => array(
			'title' => __( 'Reports', 'yith-woocommerce-membership' ),
			'type'  => 'title',
		),

		'show-membership-info-in-reports' => array(
			'id'        => 'yith-wcmbs-show-membership-info-in-reports',
			'name'      => __( 'Show Membership info in Reports', 'yith-woocommerce-membership' ),
			'type'      => 'yith-field',
			'yith-type' => 'onoff',
			'desc'      => __( 'Enable to show Membership info column in "downloads by user" reports.', 'yith-woocommerce-membership' ),
			'default'   => yith_wcmbs_settings()->get_default( 'yith-wcmbs-show-membership-info-in-reports' ),
		),

		'use-external-services-to-get-user-ip-address' => array(
			'id'        => 'yith-wcmbs-use-external-services-to-get-user-ip-address',
			'name'      => __( 'Use external services to get user IP Address', 'yith-woocommerce-membership' ),
			'type'      => 'yith-field',
			'yith-type' => 'onoff',
			'desc'      => __( 'Enable to get user IP Address using external services.', 'yith-woocommerce-membership' ),
			'default'   => yith_wcmbs_settings()->get_default( 'yith-wcmbs-use-external-services-to-get-user-ip-address' ),
		),

		'reports-options-end' => array(
			'type' => 'sectionend',
		),
	),
);

if ( defined( 'YITH_WCMBS_RESTRICTED_CPT_MANAGEMENT_ENABLED' ) && YITH_WCMBS_RESTRICTED_CPT_MANAGEMENT_ENABLED ) {
	$restricted_cpt_settings = array(
		'custom-post-types-options'               => array(
			'title' => __( 'Custom Post Types', 'yith-woocommerce-membership' ),
			'type'  => 'title',
			'desc'  => '',
		),
		'membership-restricted-custom-post-types' => array(
			'id'      => 'yith-wcmbs-membership-restricted-custom-post-types',
			'name'    => __( 'Membership Restricted Custom Post types', 'yith-woocommerce-membership' ),
			'type'    => 'multiselect',
			'desc'    => __( 'Select the custom post types you want enable for Membership', 'yith-woocommerce-membership' ),
			'options' => yith_wcmbs_get_other_custom_post_types( 'id=>name' ),
			'class'   => 'wc-enhanced-select',
		),
		'custom-post-types-options-end'           => array(
			'type' => 'sectionend',
		),
	);

	$tab['settings'] = array_merge( $tab['settings'], $restricted_cpt_settings );
}

return apply_filters( 'yith_wcmbs_panel_settings_options', $tab );