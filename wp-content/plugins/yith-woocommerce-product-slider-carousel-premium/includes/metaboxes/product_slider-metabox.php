<?php
if( !defined( 'ABSPATH' ) ){
    exit;
}
$animations =   ywcps_animations_list();
$minimum_input_length = apply_filters( 'ywcps_minimum_input_length', 3 );
$data_ajax_search = array( 'minimum_input_length' => $minimum_input_length );

$args   =   array(
    'label'    => __( 'Product Slider', 'yith-woocommerce-product-slider-carousel' ),
    'pages'    => 'yith_wcps_type', //or array( 'post-type1', 'post-type2')
    'context'  => 'normal', //('normal', 'advanced', or 'side')
    'priority' => 'default',
    'tabs'     => array(
        'settings' => array(
            'label'  => __( 'Settings', 'yith-woocommerce-product-slider-carousel' ),
            'fields' => array_merge(

	            array(
                    'ywcps_all_cat' => array(
                      'label'   =>  __( 'Product Category', 'yith-woocommerce-product-slider-carousel'),
                      'desc'    =>  __( 'Select products category: All or select them manually', 'yith-woocommerce-product-slider-carousel' ),
                       'type'   =>  'select',
                        'options' => array(
                            'all' => __( 'All Categories', 'yith-woocommerce-product-slider-carousel' ),
                            'custom_category' => __( 'Select your category', 'yith-woocommerce-product-slider-carousel' ),
                            'exclude_category' => __( 'Exclude categories from slider', 'yith-woocommerce-product-slider-carousel')
                        )
                    ),

                    'ywcps_categories' => array(
                        'label' =>  __('Choose Product Category','yith-woocommerce-product-slider-carousel'),
                        'desc'  =>  __('Choose product categories. Leave this field empty if you want all categories to be shown in the slider','yith-woocommerce-product-slider-carousel'),
                        'type'  =>  'ajax-terms',

                        'multiple' => true,
                        'deps'  =>  array(
                            'ids'   =>  '_ywcps_all_cat',
                            'values' =>  'custom_category'
                        ),
	                    'data' => array(
	                    	'taxonomy' => 'product_cat',
		                    'placeholder'   => __('Choose product categories', 'yith-woocommerce-product-slider-carousel'),
		                    'minimum_input_length'  => $minimum_input_length,
		                    'term_field' => 'slug'

	                    ),
                    ),
                    'ywcps_exclude_categories' => array(
                        'label' =>  __('Exclude Product Category','yith-woocommerce-product-slider-carousel'),
                        'desc'  =>  __('Exclude product categories. Leave this field empty if you want all categories to be excluded in the slider','yith-woocommerce-product-slider-carousel'),
                        'type'  =>  'ajax-terms',
                        'multiple' => true,
                        'deps'  =>  array(
                            'ids'   =>  '_ywcps_all_cat',
                            'values' =>  'exclude_category'
                        ),
                        'data' => array(
	                        'taxonomy' => 'product_cat',
	                        'placeholder'   => __('Exclude product categories', 'yith-woocommerce-product-slider-carousel'),
	                        'minimum_input_length'  => $minimum_input_length,
	                        'term_field' => 'slug'

                        ),
                    ),
	            ),

				! defined( 'YITH_WCBR_PREMIUM_INIT' ) ? array() : array(
					'ywcps_all_brand' => array(
						'label'   =>  __( 'Product Brand', 'yith-woocommerce-product-slider-carousel'),
						'desc'    =>  __( 'Select products brand: All or select them manually', 'yith-woocommerce-product-slider-carousel' ),
						'type'   =>  'select',
						'options' => array(
							'all' => __( 'All brands', 'yith-woocommerce-product-slider-carousel' ),
							'custom_brand' => __( 'Select your brand', 'yith-woocommerce-product-slider-carousel' )
						)
					),

					'ywcps_brands' => array(
						'label' =>  __('Choose Product Brand','yith-woocommerce-product-slider-carousel'),
						'desc'  =>  __('Choose product brands. Leave this field empty if you want all brands to be shown in the slider','yith-woocommerce-product-slider-carousel'),
						'type'  =>  'ajax-terms',


						'multiple' => true,
						'deps'  =>  array(
							'ids'   =>  '_ywcps_all_brand',
							'values' =>  'custom_brand'
						),
						'data' => array(
							'placeholder'   => __('Choose product brands', 'yith-woocommerce-product-slider-carousel'),
							'minimum_input_length'  => $minimum_input_length,
							'term_field' => 'slug',
							'taxonomy' => YITH_WCBR::$brands_taxonomy

						)
					),
				),

				array(
					'ywcps_product_type'    =>  array(
						'label' =>  __('Products to show', 'yith-woocommerce-product-slider-carousel'),
						'desc'  =>  __('Select products to show in the slider: "On Sale", "Best Sellers" etc. or select them manually.', 'yith-woocommerce-product-slider-carousel'),
						'type'  =>  'select',
						'options'   =>  array(
							'all' => __( 'All Products', 'yith-woocommerce-product-sldier-carousel' ),
							'on_sale'       =>  __('On Sale', 'yith-woocommerce-product-slider-carousel'),
							'best_seller'   =>  __('Best Sellers', 'yith-woocommerce-product-slider-carousel'),
							'free'          =>  __('Free', 'yith-woocommerce-product-slider-carousel'),
							'last_ins'      =>  __('Last Added', 'yith-woocommerce-product-slider-carousel'),
							'featured'      =>  __('Featured', 'yith-woocommerce-product-slider-carousel'),
							'top_rated' => __( 'Top Rated', 'yith-woocommerce-product-slider-carousel' ),
							'custom_select'   =>  __('Select your product by Name', 'yith-woocommerce-product-slider-carousel'),
							'custom_select_tag'   =>  __('Select your product by Tag', 'yith-woocommerce-product-slider-carousel'),
						),
						'default' => 'all',

					),
                    'ywcps_products'	=>	array(
                        'label' =>  __('Choose Product','yith-woocommerce-product-slider-carousel'),
                        'desc'  =>  __('Choose the Products that you want to show in the slider. Leave this field empty if you want all categories to be shown in the slider','yith-woocommerce-product-slider-carousel'),
                        'type'  =>  'ajax-products',
                        'multiple' => true,
                        'deps'     => array(
                            'ids'    => '_ywcps_product_type',
                            'values' => 'custom_select',
                        ),
                        'data' => array(
	                        'placeholder'   => __('Search for a product', 'yith-woocommerce-product-slider-carousel' ),
	                        'minimum_input_length'  => $minimum_input_length,
                        ),
                    ),
                    'ywcps_product_tag'	=>	array(
                    				'label' =>  __('Choose Tag','yith-woocommerce-product-slider-carousel'),
                    				'desc'  =>  __('Choose the Product Tags that you want to show in the slider. Leave this field empty if you want all tags to be shown in the slider','yith-woocommerce-product-slider-carousel'),
                    				'type'  =>  'ajax-terms',
                    				'multiple' => true,
                    				'deps'     => array(
                    						'ids'    => '_ywcps_product_type',
                    						'values' => 'custom_select_tag',
                    				),
				                    'data' => array(
					                    'taxonomy' => 'product_tag',
					                    'placeholder'   => __( 'Search for a tag', 'yith-woocommerce-product-slider-carousel' ),
					                    'minimum_input_length'  => $minimum_input_length,
					                    'term_field' => 'slug'

				                    ),

                    ),

                    'ywcps_sep_1'   => array( 'type'=> 'sep' ),

                    'ywcps_title_content_setting'   =>  array( 'type'=>'title', 'desc'=> __('Content Settings', 'yith-woocommerce-product-slider-carousel') ),

                    'ywcps_layout_type' =>  array(
                        'label' =>  __('Slider Template', 'yith-woocommerce-product-slider-carousel'),
                        'desc'  =>  __('Choose a template for your Product Slider', 'yith-woocommerce-product-slider-carousel'),
                        'type'  =>  'select',
                        'options'    =>  array(
                            'default'   =>  'WooCommerce Loop',
                            'tmp1'      =>  'Style 1',
                            'tmp2'      =>  'Style 2',
                            'tmp3'      =>  'Style 3'
                            ),
                        'std' =>    'default'
                    ),

                'ywcp_show_title'   =>  array(
                  'label'   =>  __('Show Title', 'yith-woocommerce-product-slider-carousel'),
                   'desc'   =>  __('Show or Hide Product Slider title', 'yith-woocommerce-product-slider-carousel'),
                    'type'  =>  'checkbox',
                    'std'   =>  1,
                    'default'   =>  1
                ),

                    'ywcps_hide_add_to_cart'    =>  array(
                        'label' =>  __('Hide "Add to cart"', 'yith-woocommerce-product-slider-carousel'),
                        'desc'  =>  __('Hide "Add to cart" in slider', 'yith-woocommerce-product-slider-carousel'),
                        'type'  =>  'checkbox',
                        'std'   => 0,
                        'default'   =>  0
                    ),

                    'ywcps_hide_price'    =>  array(
                        'label' =>  __('Hide price', 'yith-woocommerce-product-slider-carousel'),
                        'desc'  =>  __('Hide product price in slider', 'yith-woocommerce-product-slider-carousel'),
                        'type'  =>  'checkbox',
                        'std'   => 0,
                        'default'   =>  0
                    ),
            		'ywcps_hide_on_sale_product' => array(
            			'label' => __('Hide On Sale product', 'yith-woocommerce-product-slider-carousel' ),
            			'desc' => __( 'Exclude "on sale" product in slider', 'yith-woocommerce-product-slider-carousel' ),
            			'type' => 'checkbox',
            			'default' => 0,
            				'deps'     => array(
            						'ids'    => '_ywcps_product_type',
            						'values' => 'all,free,custom_select,featured,best_seller,last_ins,custom_select_sku,custom_select_tag,top_rated',
            				),
            		),
            		'ywcps_hide_out_stock_product' => array(
            				'label' => __('Hide Out of stock product', 'yith-woocommerce-product-slider-carousel' ),
            				'desc' => __( 'Exclude product "out of stock" in slider', 'yith-woocommerce-product-slider-carousel' ),
            				'type' => 'checkbox',
            				'default' => 0,
            		),
                    'ywcps_image_per_row'   => array(
                        'label' =>  __('Images for row', 'yith-woocommerce-product-slider-carousel'),
                        'desc'  =>  '',
                        'type'  =>  'number',
                        'std'   =>  1,
                        'min'   =>  1,
                        /*'max'   =>  6*/
                        ),

                    'ywcps_order_by'    =>  array(
                        'label'     =>  __('Order By', 'yith-woocommerce-product-slider-carousel'),
                        'type'      =>  'select',
                        'desc'  =>  '',
                        'options'   =>  array(
                            'name'      =>  __('Name', 'yith-woocommerce-product-slider-carousel'),
                            'price'     =>  __('Price', 'yith-woocommerce-product-slider-carousel'),
                            'date'  =>  __('Date', 'yith-woocommerce-product-slider-carousel'),
                            'rand'  => __( 'Random', 'yith-woocommerce-product-slider-carousel' )
                        ),
                        'deps'     => array(
                            'ids'    => '_ywcps_product_type',
                            'values' => 'all,on_sale,free,custom_select,featured,custom_select_tag',
                        ),
                    ),

                    'ywcps_order_type'   => array(
                        'label' =>  __('Order Type', 'yith-woocommerce-product-slider-carousel'),
                        'type'  =>  'select',
                        'desc'  =>  '',
                        'options'   =>  array(
                            'asc'   =>  'ASC',
                            'desc'  =>  'DESC'
                        ),
                        'deps'     => array(
                            'ids'    => '_ywcps_product_type',
                            'values' => 'all,on_sale,free,custom_select,featured,custom_select_tag',
                        ),
                    ),
                    'ywcps_sep_2'   => array( 'type'=> 'sep' ),

                    'ywcps_title_slider_setting'   =>  array( 'type'=>'title', 'desc'=> __('Slider Settings', 'yith-woocommerce-product-slider-carousel') ),


                    'ywcps_check_loop' =>  array(
                        'label' =>  __('Loop slider', 'yith-woocommerce-product-slider-carousel'),
                        'desc'  =>  __('Choose if you want your slider to scroll products continuously', 'yith-woocommerce-product-slider-carousel'),
                        'type'  =>  'checkbox',
                        'std'   =>  0,
                        'default'   =>  0
                     ),

                    'ywcps_pagination_speed' =>  array(
                        'label' =>  __('Pagination Speed', 'yith-woocommerce-product-slider-carousel'),
                        'desc'  =>  __('Pagination speed in milliseconds', 'yith-woocommerce-product-slider-carousel'),
                        'type'  =>  'text',
                        'std'   =>  '800',
                        'default'   =>  '800'
                    ),


                    'ywcps_auto_play' =>  array(
                        'label' =>  __('AutoPlay', 'yith-woocommerce-product-slider-carousel'),
                        'desc'  =>  __('Insert the autoplay value in milliseconds, enter 0 to disable it', 'yith-woocommerce-product-slider-carousel'),
                        'type'  =>  'text',
                        'std'   =>  '5000',
                        'default'   =>  '5000'
                    ),

                    'ywcps_stop_hover'  =>  array(
                        'label' =>  __('Stop on Hover', 'yith-woocommerce-product-slider-carousel'),
                        'desc'  =>  __('Stop autoplay on mouse hover', 'yith-woocommerce-product-slider-carousel'),
                        'type'  =>  'checkbox',
                        'std'   => 0,
                        'default'   => 0
                    ),

                    'ywcps_show_navigation'  =>  array(
                        'label' =>  __('Show Navigation', 'yith-woocommerce-product-slider-carousel'),
                        'desc'  =>  __('Display "prev" and "next" button', 'yith-woocommerce-product-slider-carousel'),
                        'type'  =>  'checkbox',
                        'std'   => 0,
                        'default'   => 0
                    ),

                    'ywcps_show_dot_navigation' =>  array(
                        'label' =>  __('Show Dots Navigation' ,'yith-woocommerce-product-slider-carousel'),
                        'desc'  =>  __('Show or Hide dots navigation', 'yith-woocommerce-product-slider-carousel'),
                        'type'  =>  'checkbox',
                        'std'   =>  0,
                        'default'   => 0
                    ),

                    'ywcps_animate_in'  =>  array(
                        'label' =>  __('Animation IN', 'yith-woocommerce-product-slider-carousel'),
                        'desc'  =>  __('Choose entrance animation for a new slide.<br>*Animation functions work only if there is just one item in the slider and only in browsers that support perspective property', 'yith-woocommerce-product-slider-carousel'),
                        'type'  =>  'select-group',
                        'options'   =>  $animations
                    ),
                    'ywcps_animate_out'  =>  array(
                        'label' =>  __('Animation OUT', 'yith-woocommerce-product-slider-carousel'),
                        'desc'  =>  __('Choose exit animation for a slide.<br>*Animation functions work only if there is just one item in the slider and only in browsers that support perspective property', 'yith-woocommerce-product-slider-carousel'),
                        'type'  =>  'select-group',
                        'options'   =>  $animations
                    ),

                    'ywcps_animation_speed' =>  array(
                        'label' =>  __('Animation Speed', 'yith-woocommerce-product-slider-carousel'),
                        'desc'  =>  __('Enter animation duration in milliseconds', 'yith-woocommerce-product-slider-carousel'),
                        'type'  =>  'text',
                        'std'   =>  450,
                        'default'   => 450
                    )
            )

            )
        ),
    ),
);

return apply_filters( 'yith_product_slider_metabox', $args );
