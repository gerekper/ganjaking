<?php
if( !defined( 'ABSPATH' ) )
    exit;

$description_visible_txt = sprintf( __( 'If checkout, surveys will show in checkout page.%1$sIf product, surveys will
show in product page.%1$sIf other page, surveys can be placed using shortcode or a widget.', 'yith-woocommerce-surveys' ),
    '<br>');

$check_out_handle = array(
    'before_checkout_billing_form'      =>  __( 'Before Checkout Billing Form', 'yith-woocommerce-surveys' ),
    'after_checkout_billing_form'       =>  __( 'After Checkout Billing Form', 'yith-woocommerce-surveys' ),
    'before_checkout_shipping_form'     =>  __( 'Before Checkout Shipping Form','yith-woocommerce-surveys'),
    'after_checkout_shipping_form'      =>  __( 'After Checkout Shipping Form', 'yith-woocommerce-surveys' ),
    'before_order_notes'                =>  __( 'Before Order Notes', 'yith-woocommerce-surveys' ),
    'after_order_notes'                 =>  __( 'After Order Notes', 'yith-woocommerce-surveys' ),
);

$product_handle = array(
    'after_single_product_summary'  =>  __( 'After Single Product Summary', 'yith-woocommerce-surveys' ),
    'before_add_to_cart_form'       =>  __( 'Before Add to Cart button', 'yith-woocommerce-surveys' ),
    'after_add_to_cart_form'        =>  __( 'After Add to Cart button', 'yith-woocommerce-surveys' )

);

$args   =   array(

    'label'    =>   __( 'Survey Options', 'yith-woocommerce-surveys' ),
    'pages'    =>   array( 'yith_wc_surveys' ),
    'context'  =>   'normal',
    'priority' =>   'default',
    'class' => yith_set_wrapper_class(),
    'tabs'     =>   array(

        'settings'  =>  array(

            'label'     =>  __( 'Settings', 'yith-woocommerce-surveys' ),
            'fields'    =>  array(


                'yith_survey_visible_in'    =>  array(
                    'label' =>  __( 'Display Survey in', 'yith-woocommerce-surveys' ),
                    'desc'  => $description_visible_txt,
                    'type'  =>  'select',
                    'options'   =>  get_surveys_type()
                    ),

                /**OPTIONS IF SURVEY VISIBLE IN == CHECKOUT*/
                'yith_survey_wc_handle'	=>	array(
                    'label' =>  __( 'Position in Checkout', 'yith-woocommerce-surveys' ),
                    'desc'  =>  '',
                    'type'  =>  'select',
                    'default'      =>   'after_order_notes',
                    'options'   => $check_out_handle,
                    'deps'     => array(
                        'ids'    => '_yith_survey_visible_in',
                        'values' => 'checkout',
                    ),
                ),

                'yith_survey_required' => array(
                    'label'   =>  __( 'Required', 'yith-woocommerce-surveys' ),
                    'desc'   =>  __( 'If checked, survey is required in checkout', 'yith-woocommerce-surveys'),
                    'type'  =>  'checkbox',
                    'std'   =>  0,
                    'default'   =>  0,
                    'deps'     => array(
                        'ids'    => '_yith_survey_visible_in',
                        'values' => 'checkout',
                    )
                ),
              //END
              //OPTION IF SURVEY VISIBLE IN == PRODUCT
                'yith_survey_products'	=>	array(
                    'label' =>  __( 'Choose Product','yith-woocommerce-surveys' ),
                    'desc'  =>  __( 'Select products in which you want to show surveys. Leave it blank to show the survey on every product.','yith-woocommerce-surveys' ),
                    'type'  =>  'ajax-products',
                    'multiple' => true,
                    'std'      =>   array(),
                    'options'   => array(),
                    'id' => 'ajax_survey_product',
                    'deps'     => array(
                        'ids'    => '_yith_survey_visible_in',
                        'values' => 'product',
                    ),
                ),

                'yith_survey_product_wc_handle'	=>	array(
                    'label' =>  __( 'Position in Product', 'yith-woocommerce-surveys' ),
                    'desc'  =>  '',
                    'type'  =>  'select',
                    'default'      =>   'after_single_product_summary',
                    'options'   => $product_handle,
                    'deps'     => array(
                        'ids'    => '_yith_survey_visible_in',
                        'values' => 'product',
                    ),
                ),

                'yith_survey_sep_1'   => array( 'type'=> 'sep' ),
                'yith_survey_answer' => array(
                    'label' => __( 'Survey Answers', 'yith-woocommerce-surveys' ),
                    'desc'  => '',
                    'type'  => 'survey_answers'
                ),
            ),
        ),
    ),
);

return apply_filters( 'yith_surveys_metabox', $args );