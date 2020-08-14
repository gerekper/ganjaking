<?php
if( !defined('ABSPATH'))
    exit;

$woocommerce_hook = array(
    'template_single_title' => __('Before product name', 'yith-woocommerce-role-based-prices' ),
    'template_single_price' => __('Before price', 'yith-woocommerce-role-based-prices'),
    'template_single_excerpt'     => __('Before product excerpt', 'yith-woocommerce-role-based-prices' ),
    'template_single_add_to_cart' => __('Before Add to Cart','yith-woocommerce-role-based-prices' ),
    'template_single_meta' => __('Before meta', 'yith-woocommerce-role-based-prices' ),
    'template_single_sharing'=> __('Before sharing buttons', 'yith-woocommerce-role-based-prices' )
);

$setting    =    array(

    'text-role'  =>  array(

        'text_role_section_start' => array(
            'name' => __( 'Label settings', 'yith-woocommerce-role-based-prices' ),
            'id' => 'ywcrbp_label_section_start',
            'type'  => 'title'
        ),
        'regular_price_txt' => array(
            'name' => __('Regular Price text','yith-woocommerce-role-based-prices'),
            'id' => 'ywcrbp_regular_price_txt',
            'type'=>'text',
            'desc' => __('This text appears next to regular price', 'yith-woocommerce-role-based-prices' ),
            'default' =>  __( 'Regular Price', 'yith-woocommerce-role-based-prices' )
        ),
        'sale_price_txt' => array(
            'name' => __('On Sale Price text','yith-woocommerce-role-based-prices'),
            'id' => 'ywcrbp_sale_price_txt',
            'type'=>'text',
            'desc' => __('This text appears next to on sale price', 'yith-woocommerce-role-based-prices' ),
            'default' =>  __( 'On sale price', 'yith-woocommerce-role-based-prices' )
        ),
        'your_price_txt' => array(
            'name' => __('Role Price text','yith-woocommerce-role-based-prices'),
            'id' => 'ywcrbp_your_price_txt',
            'type'=>'text',
            'desc' => __('This text appears next to role-based price', 'yith-woocommerce-role-based-prices' ),
            'default' =>  __( 'role-based price', 'yith-woocommerce-role-based-prices' )
        ),
        'their_price_txt' => array(
        	'name' => __( 'Their price', 'yith-woocommerce-role-based-prices' ),
	        'id' => 'ywcrbp_their_price_txt',
	        'type' => 'text',
	        'desc' => __('Enter here the text to show the price of a different user role. Recommended if you want to encourage users to get a different role and buy at a better price', 'yith-woocommerce-role-based-prices' ),
	        'desc_tip' => __('{role_name} will be replaced with the User role name', 'yith-woocommerce-role-based-prices' ),
	        'default' => __(' If you become {role_name} you\'ll be able to buy at this price', 'yith-woocommerce-role-based-prices')

        ),
        'price_incl_suffix' => array(
            'name' => __('Price Display incl. tax Suffix:', 'yith-woocommerce-role-based-prices' ),
            'id' => 'ywcrbp_price_incl_suffix',
            'type' => 'text',
            'default' => 'incl.VAT',
            'desc_tip' => __('Define text to show after price','yith-woocommerce-role-based-prices')
        ),
        'price_excl_suffix' => array(
            'name' => __('Price Display excl. tax Suffix:', 'yith-woocommerce-role-based-prices' ),
            'id' => 'ywcrbp_price_excl_suffix',
            'type' => 'text',
            'default' => 'excl.VAT',
            'desc_tip' => __('Define text to show after price','yith-woocommerce-role-based-prices')
        ),
        'hide_price_mess_txt_user' => array(

            'name' => __( 'Alternative text shown to users',
                'yith-woocommerce-role-based-prices'),
            'id'   => 'ywcrbp_message_user',
            'type'  => 'textarea',
            'desc'  => __( 'This text will be shown in place of price if all prices (regular,on sale and role-based price) are hidden to a specific user role', 'yith-woocommerce-role-based-prices' ),
            'css' => 'max-width:350px;width:100%;height:70px;resize: none;overflow-x: hidden;overflow-y: auto;',
            'default' => ''
        ),

        'price_mess_txt_user_hook' => array(
            'name' => __(' Show in', 'yith-woocommerce-role-based-prices' ),
            'type' => 'select',
            'options'  => $woocommerce_hook,
            'default' => 'template_single_price',
            'id'       => 'ywcrbp_position_user_txt',
            'css' => 'max-width:350px;width:100%;'
        ),

        'price_mess_color_txt_user' => array(

            'name' => __( 'Alternative text color', 'yith-woocommerce-role-based-prices'),
            'id'   => 'ywcrbp_message_color_user',
            'type'  => 'color',
            'desc'  =>'',
            'default' => '#ff0000'

        ),
        'text_role_section_end' => array(

            'type'  => 'sectionend'
        ),

        'total_discount_markup_section_start' => array(
            'name' => __('Total discount/markup settings', 'yith-woocommerce-role-based-prices' ),
            'type'  => 'title'
        ),
        'total_discount_mess_txt_user' => array(

            'name' => __( 'Total discount message','yith-woocommerce-role-based-prices'),
            'id'   => 'ywcrbp_total_discount_mess',
            'type'  => 'textarea',
            'desc'  => __( 'This text appears below the role-based price (if any) and shows the total discount users with a specific role will see on the product.', 'yith-woocommerce-role-based-prices' ),
            'css' => 'max-width:350px;width:100%;height:70px;resize: none;overflow-x: hidden;overflow-y: auto;',
            'default' => sprintf( '%s %s', _x( 'Your total discount is', 'Your total discount is 30%', 'yith-woocommerce-role-based-prices' ), '{ywcrbp_total_discount}'),
            'desc_tip' => __( '{ywcrbp_total_discount} replaced with the total discount', 'yith-woocommerce-role-based-prices' )
        ),
        'total_markup_mess_txt_user' => array(

            'name' => __( 'Total markup message','yith-woocommerce-role-based-prices'),
            'id'   => 'ywcrbp_total_markup_mess',
            'type'  => 'textarea',
            'desc'  => __( 'This text appears below the role based price (if any) and shows the total markup users with a specific role will see on the product.', 'yith-woocommerce-role-based-prices' ),
            'css' => 'max-width:350px;width:100%;height:70px;resize: none;overflow-x: hidden;overflow-y: auto;',
            'default' => sprintf( '%s %s', _x( 'The total markup on this product is', 'The total markup on this product is 30%', 'yith-woocommerce-role-based-prices' ), '{ywcrbp_total_markup}' ),
            'desc_tip' => __( '{ywcrbp_total_markup} replaced with the total markup', 'yith-woocommerce-role-based-prices' )
        ),
        'total_discount_markup_section_end' => array(

            'type'  => 'sectionend'
        )



    )
);

return $setting;