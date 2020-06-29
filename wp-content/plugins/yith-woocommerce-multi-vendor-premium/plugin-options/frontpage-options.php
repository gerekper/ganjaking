<?php
/*
 * This file belongs to the YIT Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */

$vendor_name_style = get_option('yith_wpv_vendor_name_style', 'theme');
$wc_account_settings_uri = esc_url(add_query_arg(array('page' => 'wc-settings', 'tab' => 'account'), admin_url('admin.php')));
$vat_ssn_label = get_option( 'yith_vat_label', __( 'VAT/SSN', 'yith-woocommerce-product-vendors' ) );
return array(

    'frontpage' => array(

        'frontpage_wc_options_start' => array(
            'type' => 'sectionstart',
        ),

        'frontpage_wc_options_title' => array(
            'title' => __('WooCommerce Pages', 'yith-woocommerce-product-vendors'),
            'type' => 'title',
        ),

        'frontpage_loop_vendor_name' => array(
            'title' => __('Show vendor\'s name in shop page', 'yith-woocommerce-product-vendors'),
            'type' => 'checkbox',
            'desc' => __('Select if you want to show vendor\'s name below products in Shop page.', 'yith-woocommerce-product-vendors'),
            'id' => 'yith_wpv_vendor_name_in_loop',
            'default' => 'yes'
        ),

        'frontpage_single_product_vendor_name' => array(
            'title' => __('Show vendor\'s name in single product page', 'yith-woocommerce-product-vendors'),
            'type' => 'checkbox',
            'desc' => __('Select if you want to show vendor\'s name below products in Single product page.', 'yith-woocommerce-product-vendors'),
            'id' => 'yith_wpv_vendor_name_in_single',
            'default' => 'yes'
        ),

        'frontpage_categories_vendor_name' => array(
            'title' => __('Show vendor\'s name in product category page', 'yith-woocommerce-product-vendors'),
            'type' => 'checkbox',
            'desc' => __('Select if you want to show vendor\'s name below products in Product category page.', 'yith-woocommerce-product-vendors'),
            'id' => 'yith_wpv_vendor_name_in_categories',
            'default' => 'yes'
        ),

        'frontpage_item_sold' => array(
            'title' => __('Show "Item sold" information in single product page', 'yith-woocommerce-product-vendors'),
            'type' => 'checkbox',
            'desc' => __('Select if you want to show the text "Item sold" in single product page among category and tag information', 'yith-woocommerce-product-vendors'),
            'id' => 'yith_wpv_vendor_show_item_sold',
            'default' => 'no'
        ),

        'frontpage_report_abuse' => array(
            'title' => __('Show "Report abuse" link', 'yith-woocommerce-product-vendors'),
            'type' => 'select',
            'desc' => __('Choose if you want to show the "Report abuse" link under product thumbnails in single product page.', 'yith-woocommerce-product-vendors'),
            'id' => 'yith_wpv_report_abuse_link',
            'options' => array(
                'none' => __('Disabled', 'yith-woocommerce-product-vendors'),
                'all' => __('All products', 'yith-woocommerce-product-vendors'),
                'vendor' => __("Only for vendor's products", 'yith-woocommerce-product-vendors')
            ),
            'default' => 'none'
        ),

        'frontpage_report_abuse_text' => array(
            'title' => __('"Report abuse" link text', 'yith-woocommerce-product-vendors'),
            'type' => 'text',
            'desc' => __('The report abuse link text.', 'yith-woocommerce-product-vendors'),
            'id' => 'yith_wpv_report_abuse_link_text',
            'default' => __("Report abuse", 'yith-woocommerce-product-vendors')
        ),

        'frontpage_vendor_tab' => array(
            'title' => __('Show "Vendor" tab in single product page', 'yith-woocommerce-product-vendors'),
            'type' => 'checkbox',
            'desc' => __('Select if you want to show vendor\'s tab in single product page.', 'yith-woocommerce-product-vendors'),
            'id' => 'yith_wpv_show_vendor_tab_in_single',
            'default' => 'yes'
        ),

        'frontpage_vendor_tab_text' => array(
            'title' => __('Vendor Tab', 'yith-woocommerce-product-vendors'),
            'type' => 'text',
            'desc' => __('Change the label of "Vendor Tab" in single product page.', 'yith-woocommerce-product-vendors'),
            'id' => 'yith_wpv_vendor_tab_text_text',
            'default' => YITH_Vendors()->get_vendors_taxonomy_label('singular_name'),
        ),

        'frontpage_tab_position' => array(
            'title' => __('Vendor tab position in single product page', 'yith-woocommerce-product-vendors'),
            'type' => 'select',
            'desc' => __('Select the position for "Vendor" tab in single product page. You can set to show the tab before or after WooCommerce tabs', 'yith-woocommerce-product-vendors'),
            'id' => 'yith_vendors_tab_position',
            'options' => array(
                1 => __('First tab', 'yith-woocommerce-product-vendors'),
                99 => __('After WooCommerce tabs', 'yith-woocommerce-product-vendors')
            ),
            'default' => 99
        ),

        'frontpage_shipping_tab_text' => array(
            'title' => __('Shipping info Tab', 'yith-woocommerce-product-vendors'),
            'type' => 'text',
            'desc' => __('Change the label of "Shipping info Tab" in single product page.', 'yith-woocommerce-product-vendors'),
            'id' => 'yith_wpv_shipping_tab_text_text',
            'default' => _x( 'Shipping Info', '[Single Product Page]: Tab name for shipping information', 'yith-woocommerce-product-vendors' ),
        ),

        'frontpage_vendor_product_on_vacation' => array(
	        'title' => __('Show products on Vacation Mode', 'yith-woocommerce-product-vendors'),
	        'type' => 'checkbox',
	        'desc' => __('Select if you want to show vendor\'s products during holiday closings in WooCommerce archive pages.', 'yith-woocommerce-product-vendors'),
	        'id' => 'yith_wpv_show_vendors_products_on_vacation',
	        'default' => 'no'
        ),

        'frontpage_wc_options_end' => array(
            'type' => 'sectionend',
        ),

        'frontpage_wc_registration_options_start' => array(
            'type' => 'sectionstart',
        ),

        'frontpage_wc_registration_options_title' => array(
            'title' => __("Vendor's registration page", 'yith-woocommerce-product-vendors'),
            'type' => 'title',
            'id' => 'yith_wpv_wc_registration_options_title'
        ),

        'frontpage_vendors_registration_page' => array(
            'title' => __('Enable Vendors registration in "My Account" page', 'yith-woocommerce-product-vendors'),
            'type' => 'checkbox',
            'desc' => sprintf(__('To make this option available you have to enable registration from "My Account" page in <a href="%s">WooCommerce > Settings > Account</a>', 'yith-woocommerce-product-vendors'), $wc_account_settings_uri),
            'id' => 'yith_wpv_vendors_my_account_registration',
            'default' => 'no'
        ),

        'frontpage_vendors_registration_auto_approve' => array(
            'title' => __('Auto enable vendor account', 'yith-woocommerce-product-vendors'),
            'type' => 'checkbox',
            'desc' => __('After registration, the seller is entitled to sell. If you disable this option, the administrator must enable the vendor account manually', 'yith-woocommerce-product-vendors'),
            'id' => 'yith_wpv_vendors_my_account_registration_auto_approve',
            'default' => 'no'
        ),

        'frontpage_vendors_registration_vat' => array(
            //string added @version 1.13.2
            'title' => $vat_ssn_label,
            'type' => 'checkbox',
            'desc' => __('Mark this field required', 'yith-woocommerce-product-vendors'),
            'id' => 'yith_wpv_vendors_my_account_required_vat',
            'default' => 'no'
        ),

        'frontpage_vendors_registration_terms_and_conditions' => array(
            'title' => __('Terms and conditions', 'yith-woocommerce-product-vendors'),
            'type' => 'checkbox',
            'desc' => __('Mark this field required', 'yith-woocommerce-product-vendors'),
            'id' => 'yith_wpv_vendors_registration_required_terms_and_conditions',
            'default' => 'no'
        ),

        'frontpage_vendors_registration_show_paypal_email' => array(
	        'title' => __('Show PayPal Email', 'yith-woocommerce-product-vendors'),
	        'type' => 'checkbox',
	        'desc' => __('If checked, add the PayPal Email fields to registration form and vendor profile information', 'yith-woocommerce-product-vendors'),
	        'id' => 'yith_wpv_vendors_registration_show_paypal_email',
	        'default' => 'yes'
        ),

        'frontpage_vendors_registration_paypal_email' => array(
	        'title' => __('PayPal Email', 'yith-woocommerce-product-vendors'),
	        'type' => 'checkbox',
	        'desc' => __('Mark this field required', 'yith-woocommerce-product-vendors'),
	        'id' => 'yith_wpv_vendors_registration_required_paypal_email',
	        'default' => 'no'
        ),

        'frontpage_wc_registration_options_end' => array(
            'type' => 'sectionend',
        ),

        'frontpage_become_a_vendor_start' => array(
            'type' => 'sectionstart',
        ),

        'frontpage_become_a_vendor_title' => array(
            'title' => __("Become a vendor page", 'yith-woocommerce-product-vendors'),
            'type' => 'title',
            'id' => 'yith_wpv_wc_become_a_vendor_options_title'
        ),

        'frontpage_become_a_vendor_page' => array(
            'title' => __('"Become a vendor" page', 'yith-woocommerce-product-vendors'),
            'id' => 'yith_wpv_become_a_vendor_page_id',
            'type' => 'single_select_page',
            'default' => 0,
            'class' => 'wc-enhanced-select-nostd',
            'css' => 'min-width:300px;',
            'desc_tip' => __('This sets the page where add the "become a vendor" form will be shown.', 'yith-woocommerce-product-vendors'),
        ),

        'frontpage_become_a_vendor_style' => array(
            'title'   => __( "Become a vendor page style", 'yith-woocommerce-product-vendors' ),
            'id'      => 'yith_wpv_become_a_vendor_style',
            'type'    => 'select',
            'options' => array(
                'myaccount'   => __( 'My Account Style: with login form and "Register as a vendor" checkbox', 'yith-woocommerce-product-vendors' ),
                'multivendor' => __( 'MultiVendor Style: Show only become a vendor form', 'yith-woocommerce-product-vendors' ),
            ),
            'default' => 'myaccount'
        ),

        'frontpage_become_a_vendor_end' => array(
            'type' => 'sectionend',
        ),

        'frontpage_terms_and_conditions_start' => array(
            'type' => 'sectionstart',
        ),

        'frontpage_terms_and_conditions_title' => array(
            'title' => __("Terms and conditions page", 'yith-woocommerce-product-vendors'),
            'type' => 'title',
            'id' => 'yith_wpv_wc_terms_and_conditions_options_title'
        ),

        'frontpage_terms_and_conditions_page' => array(
            'title' => __('"Terms and conditions" page', 'yith-woocommerce-product-vendors'),
            'id' => 'yith_wpv_terms_and_conditions_page_id',
            'type' => 'single_select_page',
            'default' => 0,
            'class' => 'wc-enhanced-select-nostd',
            'css' => 'min-width:300px;',
            'desc_tip' => __('This sets the page for vendors Terms and conditions.', 'yith-woocommerce-product-vendors'),
        ),

        'frontpage_terms_and_conditions_end' => array(
            'type' => 'sectionend',
        ),

        'frontpage_options_start' => array(
            'type' => 'sectionstart',
        ),

        'frontpage_options_title' => array(
            'title' => __('Vendor\'s Store Page', 'yith-woocommerce-product-vendors'),
            'type' => 'title',
            'id' => 'yith_wpv_vendors_options_title'
        ),

        'frontpage_rewrite_rules' => array(
            'title' => __('Vendor store slug prefix', 'yith-woocommerce-product-vendors'),
            'type' => 'text',
            'desc' => __('Change the vendor store slug prefix. I.E.: http://mywebsite.com/{store_slug}/vendor_name', 'yith-woocommerce-product-vendors'),
            'id' => 'yith_wpv_vendor_taxonomy_rewrite',
            'default' => 'vendor'
        ),

        'frontpage_name_options' => array(
            'title' => __('Store link', 'yith-woocommerce-product-vendors'),
            'type' => 'select',
            'desc' => __('Select the style you want to use:', 'yith-woocommerce-product-vendors'),
            'options' => array(
                'theme' => __('Theme style', 'yith-woocommerce-product-vendors'),
                'custom' => __('Custom style', 'yith-woocommerce-product-vendors'),
            ),
            'id' => 'yith_wpv_vendor_name_style',
            'default' => 'theme'
        ),

        'frontpage_color_name' => array(
            'title' => __('Vendor\'s name label color', 'yith-woocommerce-product-vendors'),
            'type' => 'color',
            'desc' => __('Use it in shop page and single product page', 'yith-woocommerce-product-vendors'),
            'id' => 'yith_vendors_color_name',
            'default' => '#bc360a',
            'custom_attributes' => 'theme' == $vendor_name_style ? array('readonly' => 'readonly') : array(),
        ),

        'frontpage_color_name_hover' => array(
            'title' => __('Vendor\'s name label color (on hover)', 'yith-woocommerce-product-vendors'),
            'type' => 'color',
            'desc' => __('Use it in shop page and single product page (on hover)', 'yith-woocommerce-product-vendors'),
            'id' => 'yith_vendors_color_name_hover',
            'default' => '#ea9629',
            'custom_attributes' => 'theme' == $vendor_name_style ? array('readonly' => 'readonly') : array(),
        ),


        'frontpage_header_skin' => array(
            'title' => __('Style for header image in vendor store page', 'yith-woocommerce-product-vendors'),
            'type' => 'select',
            'desc' => __('Select the vendor store page header style', 'yith-woocommerce-product-vendors'),
            'id' => 'yith_vendors_skin_header',
            'options' => array(
                'small-box'     => __('Small Box', 'yith-woocommerce-product-vendors'),
                'double-box'    => __('Double Box', 'yith-woocommerce-product-vendors'),
            ),
            'default' => 'small-box'
        ),

        'frontpage_header_image' => array(
	        'title'   => __( 'HTML for header image on vendor store page', 'yith-woocommerce-product-vendors' ),
	        'type'    => 'select',
	        'desc'    => __( 'Select the HTML format for vendor store header image', 'yith-woocommerce-product-vendors' ),
	        'id'      => 'yith_vendors_skin_hmtl_header_image_format',
	        'options' => array(
	        	//@translators: Don't translate this
		        'image'      =>  'Image tag',
		        'background' =>  'Css Background'
	        ),
	        'default' => 'image'
        ),

        'frontpage_background_skin_color' => array(
            'title' => __('Vendor\'s skin background color', 'yith-woocommerce-product-vendors'),
            'type' => 'color',
            'desc' => __('Skin Background color', 'yith-woocommerce-product-vendors'),
            'id' => 'yith_skin_background_color',
            'default' => '#000000',
        ),

        'frontpage_background_skin_color_opacity' => array(
	        'title'             => __( 'Vendor\'s skin background color opacity', 'yith-woocommerce-product-vendors' ),
	        'type'              => 'number',
	        'desc'              => __( 'Skin Background color opacity', 'yith-woocommerce-product-vendors' ),
	        'id'                => 'yith_skin_background_color_opacity',
	        'default'           => 0.5,
	        'custom_attributes' => array(
		        'step' => 0.1,
		        'min'  => 0.1,
		        'max'  => 1,
	        ),
	        'css'               => 'width: 70px'
        ),

        'frontpage_font_skin_color' => array(
            'title' => __('Vendor\'s skin font color', 'yith-woocommerce-product-vendors'),
            'type' => 'color',
            'desc' => __('Skin font color', 'yith-woocommerce-product-vendors'),
            'id' => 'yith_skin_font_color',
            'default' => '#ffffff',
        ),

        'frontpage_header_show_gravatar_image' => array(
            'title' => __('Show vendor logo in vendor store page', 'yith-woocommerce-product-vendors'),
            'type' => 'select',
            'desc' => __('Enable/Disable the vendor logo (user avatar) in vendor store page.', 'yith-woocommerce-product-vendors'),
            'id' => 'yith_vendors_show_gravatar_image',
            'options' => array(
                'enabled' => __('Show vendor logo', 'yith-woocommerce-product-vendors'),
                'disabled' => __("Don't show vendor logo", 'yith-woocommerce-product-vendors'),
                'vendor' => __('Let vendors decide', 'yith-woocommerce-product-vendors'),
            ),

        ),

        'frontpage_header_gravatar_image_size' => array(
            'title' => __('Image size for vendor logo in vendor store page', 'yith-woocommerce-product-vendors'),
            'type' => 'number',
            'desc' => __('Change the default image size for logo (Default: 62 px).', 'yith-woocommerce-product-vendors'),
            'id' => 'yith_vendors_gravatar_image_size',
            'default' => '62',
            'css' => 'width: 70px'
        ),

        'frontpage_header_image_size_width' => array(
            'title'  => __('Image width for header image in vendor store page', 'yith-woocommerce-product-vendors'),
            'type'   => 'number',
            'desc'   => __('Change the default image width for header. Set width to zero to use original image width (Default: 0 px).', 'yith-woocommerce-product-vendors'),
            'id'     => 'yith_vendors_header_image_width',
            'default' => 0,
            'css' => 'width: 70px'
        ),

        'frontpage_header_image_size_height' => array(
            'title'  => __('Image height for header image in vendor store page', 'yith-woocommerce-product-vendors'),
            'type'   => 'number',
            'desc'   => __('Change the default image height size for header. Set height to zero to use original image height (Default: 0 px).', 'yith-woocommerce-product-vendors'),
            'id'     => 'yith_vendors_header_image_height',
            'default' => 0,
            'css' => 'width: 70px'
        ),

        'frontpage_related_products' => array(
            'title' => __('Settings for vendor\'s "Related products"', 'yith-woocommerce-product-vendors'),
            'type' => 'select',
            'desc' => __('Select related products to show in single product pages:', 'yith-woocommerce-product-vendors'),
            'id' => 'yith_vendors_related_products',
            'options' => array(
                'disabled' => __('Do not show related products', 'yith-woocommerce-product-vendors'),
                'default' => __('Related products from the entire store', 'yith-woocommerce-product-vendors'),
                'vendor' => __("Related products from vendor's shop", 'yith-woocommerce-product-vendors'),
            ),
            'default' => 'vendor'
        ),

        'frontpage_default_header_image' => array(
	        'title' => __('Use the default image for the vendor shop page header', 'yith-woocommerce-product-vendors'),
	        'type' => 'checkbox',
	        'desc' => __('Use the default placeholder if the vendor doesn\'t set an image for the shop page header', 'yith-woocommerce-product-vendors'),
	        'id' => 'yith_wpv_vendor_store_default_header_image',
	        'default' => 'no'
        ),

        'frontpage_description' => array(
            'title' => __('Show vendor\'s description in store page', 'yith-woocommerce-product-vendors'),
            'type' => 'checkbox',
            'desc' => __('Select if you want to show vendor\'s description after the header of Store page.', 'yith-woocommerce-product-vendors'),
            'id' => 'yith_wpv_vendor_store_description',
            'default' => 'no'
        ),

        'frontpage_page_name' => array(
            'title' => __('Show vendor\'s name in store page', 'yith-woocommerce-product-vendors'),
            'type' => 'checkbox',
            'desc' => __('Select if you want to show vendor\'s name below products in Store page.', 'yith-woocommerce-product-vendors'),
            'id' => 'yith_wpv_vendor_name_in_store',
            'default' => 'yes'
        ),

        'frontpage_total_sales' => array(
            'title' => __('Show total vendor\'s sales in store page', 'yith-woocommerce-product-vendors'),
            'type' => 'checkbox',
            'desc' => __('Select if you want to show total vendor\'s sales in the header of Store page.', 'yith-woocommerce-product-vendors'),
            'id' => 'yith_wpv_vendor_total_sales',
            'default' => 'no'
        ),

        'frontpage_vat' => array(
            //string added @version 1.13.2
            'title' => sprintf( "%s %s %s",
                _x( 'Show', 'part of: VAT/SSN number', 'yith-woocommerce-product-vendors'),
                $vat_ssn_label,
                _x( 'in store page', 'part of: VAT/SSN number', 'yith-woocommerce-product-vendors')
            ),
            'type' => 'checkbox',
            'desc' => sprintf( '%s %s %s',
                _x('Select if you want to show the', '[admin] part of: Select if you want to show the VAT/SSN information in the header of Store page', 'yith-woocommerce-product-vendors'),
                $vat_ssn_label,
                _x('information in the header of Store page', '[admin] part of: Select if you want to show the VAT/SSN information in the header of Store page', 'yith-woocommerce-product-vendors')
            ),
            'id' => 'yith_wpv_vendor_show_vendor_vat',
            'default' => 'yes'
        ),

        //string added @version 1.13.2
        'frontpage_label_vat_ssn' => array(
            'title'             => __( 'VAT/SSN', 'yith-woocommerce-product-vendors' ),
            'type'              => 'text',
            'default'           => __( 'VAT/SSN', 'yith-woocommerce-product-vendors' ),
            'desc'              => __( 'Change the standard VAT/SSN label with your local tax wording', 'yith-woocommerce-product-vendors' ),
            'id'                => 'yith_vat_label',
        ),

        'frontpage_website' => array(
            'title' => __('Show website in store page', 'yith-woocommerce-product-vendors'),
            'type' => 'select',
            'desc' => __('Select if you want to allow vendor to add external website information in the header of Store page.', 'yith-woocommerce-product-vendors'),
            'id' => 'yith_wpv_vendor_show_vendor_website',
	        'options' => array(
	        	'no'     => _x( 'Disabled', 'admin option', 'yith-woocommerce-product-vendors' ),
	        	'header' => _x( 'Show in Header Area', 'admin option', 'yith-woocommerce-product-vendors' ),
	        	'social' => _x( 'Shwo in Socials Area', 'admin option', 'yith-woocommerce-product-vendors' ),
	        ),
            'default' => 'no'
        ),
        //@since version 2.0.0
        'frontpage_awerage_rating' => array(
            'title' => __('Show reviews average rating', 'yith-woocommerce-product-vendors'),
            'type' => 'checkbox',
            'desc' => __('Select if you want to show reviews average ratings in the header of Store page.', 'yith-woocommerce-product-vendors'),
            'id' => 'yith_wpv_vendor_show_average_ratings',
            'default' => 'yes'
        ),

        'frontpage_options_end' => array(
            'type' => 'sectionend',
        ),

        'frontpage_gmaps_key_start' => array(
            'type' => 'sectionstart',
        ),

        'frontpage_gmaps_key_title' => array(
            'title' => __("Google API Key", 'yith-woocommerce-product-vendors'),
            'type' => 'title',
            'id' => 'yith_wpv_wc_registration_options_title'
        ),

        'frontpage_gmaps_key' => array(
            'title' => __('Google Maps API Key', 'yith-woocommerce-product-vendors'),
            'type' => 'text',
            'desc' => sprintf('%s %s. %s <a href="%s" target="_blank">%s</a>',
                __('If you have an API KEY for Google Maps, you can add it', 'yith-woocommerce-product-vendors'),
                _x('here', '[admin] placeholder link', 'yith-woocommerce-product-vendors'),
                __('Don’t know what an API KEY is or how to use it? For further information, please click', 'yith-woocommerce-product-vendors'),
                esc_url('//developers.google.com/maps/documentation/javascript/get-api-key'),
                _x('here', '[admin] placeholder link', 'yith-woocommerce-product-vendors')
            ),
            'id' => 'yith_wpv_frontpage_gmaps_key',
            'default' => '',
            'css' => 'width: 330px;'
        ),

        'frontpage_gmaps_link' => array(
	        'title' => __('Show on Google Maps Link', 'yith-woocommerce-product-vendors'),
	        'type' => 'checkbox',
	        'desc' => __( 'Add the "Show on Google Maps" link under gmaps widget', 'yith-woocommerce-product-vendors' ),
	        'id' => 'yith_wpv_frontpage_show_gmaps_link',
	        'default' => 'yes',
        ),



        'frontpage_gmaps_key_end' => array(
            'type' => 'sectionend',
        ),
    )
);