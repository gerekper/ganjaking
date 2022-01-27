<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( class_exists( 'Redux' ) ) {
	if ( ! function_exists( 'agro_dynamic_section' ) ) {
		function agro_dynamic_section($sections) {

			global $agro_pre;

			// create sections in the theme options
			$sections[] = array(
				'title'	=> esc_html__('Theme WooCommerce', 'agro'),
				'id' => 'woosection',
				'icon'	=> 'el el-shopping-cart-sign',
			);

			// SHOP PAGE SECTION
			$sections[] = array(
				'title' => esc_html__( 'Shop Page', 'agro' ),
				'id' => 'shopsection',
				'subsection'=> true,
				'desc' => esc_html__( 'These are main settings for general theme!', 'agro' ),
				'icon' => 'el el-shop',
				'fields' => array(
					array(
					'title' => esc_html__( 'Shop Page Layout', 'agro' ),
					'subtitle' => esc_html__( 'Choose the shop page layout.', 'agro' ),
					'id' => 'shop_layout',
					'type' => 'image_select',
					'options' => array(
						'left-sidebar' => array(
							'alt' => 'Left Sidebar',
							'img' => ReduxFramework::$_url . 'assets/img/2cl.png'
						),
						'full-width' => array(
							'alt' => 'Full Width',
							'img' => ReduxFramework::$_url . 'assets/img/1col.png'
						),
						'right-sidebar' => array(
							'alt' => 'Right Sidebar',
							'img' => ReduxFramework::$_url . 'assets/img/2cr.png'
						),
					),
					'default' => 'right'
				),
                array(
                    'title' => esc_html__( 'Shop Product Loop Column', 'agro' ),
                    'subtitle' => esc_html__( 'Select shop page content loop column.', 'agro' ),
                    'id' => 'shop_loop_columns',
                    'type' => 'select',
                    'customizer'=> true,
                    'options' => array(
                        '' => esc_html__( 'Select a option', 'agro' ),
                        '2' => esc_html__( '2 Columns', 'agro' ),
                        '3' => esc_html__( '3 Columns', 'agro' ),
                        '4' => esc_html__( '4 Columns', 'agro' )
                    ),
                    'default' => '3'
                ),
				array(
					'title' => esc_html__( 'Shop Hero display', 'agro' ),
					'subtitle' => esc_html__( 'You can enable or disable the site shop page hero section with switch option.', 'agro' ),
					'id' => 'shop_hero',
					'type' => 'switch',
					'default' => 1,
					'on' => 'On',
					'off' => 'Off'
				),
				array(
					'title' => esc_html__( 'Shop Hero Alignment', 'agro' ),
					'subtitle' => esc_html__( 'Select shop page hero text alignment.', 'agro' ),
					'id' => 'shop_hero_alignment',
					'type' => 'select',
					'customizer'=> true,
					'options' => array(
						'text-left' => 'Left',
						'text-center' => 'Center',
						'text-right' => 'Right'
					),
					'default' => 'text-left'
				),
				array(
					'title' => esc_html__( 'Shop Hero Background', 'agro' ),
					'id' => 'shop_hero_bg',
					'type' => 'background',
					'output' => array( '.woocommerce-page #nt-hero' ),
					'required' => array( 'shop_hero', '=', '1' )
				),
				array(
					'title' => esc_html__( 'Shop Title', 'agro' ),
					'subtitle' => esc_html__( 'Add your shop page title here.', 'agro' ),
					'id' => 'shop_title',
					'type' => 'text',
					'default' => 'Shop',
					'required' => array( 'shop_hero', '=', '1' )
				),
				array(
					'title' => esc_html__( 'Shop Title Typography', 'agro' ),
					'id' => 'shop_title_typo',
					'type' => 'typography',
					'font-backup' => false,
					'letter-spacing'=> true,
					'all_styles' => true,
					'output' => array( '.woocommerce-page #nt-hero .nt-hero-title' ),
					'default' => array(
						'color' => '',
						'font-style' => '',
						'font-family' => '',
						'google' => true,
						'font-size' => '',
						'line-height' => ''
					),
					'required' => array( 'shop_hero', '=', '1' )
				),
				array(
					'title' => esc_html__( 'Shop Slogan', 'agro' ),
					'subtitle' => esc_html__( 'Add your shop page slogan here.', 'agro' ),
					'id' => 'shop_slogan',
					'type' => 'textarea',
					'default' => 'Shop Completed',
					'required' => array( 'shop_hero', '=', '1' )
				),
				array(
					'title' => esc_html__( 'Shop Slogan Typography', 'agro' ),
					'id' => 'shop_slogan_typo',
					'type' => 'typography',
					'font-backup' => false,
					'letter-spacing'=> true,
					'all_styles' => true,
					'output' => array( '.woocommerce-page #nt-hero .nt-hero-subtitle' ),
					'default' => array(
						'color' => '',
						'font-style' => '',
						'font-family' => '',
						'google' => true,
						'font-size' => '',
						'line-height' => ''
					),
					'required' => array( 'shop_hero', '=', '1' )
				),
				array(
					'title' => esc_html__( 'Shop Description', 'agro' ),
					'subtitle' => esc_html__( 'Add your shop page description here.', 'agro' ),
					'id' => 'shop_desc',
					'type' => 'textarea',
					'default' => '',
					'required' => array( 'shop_hero', '=', '1' )
				),
				array(
					'title' => esc_html__( 'Shop Description Typography', 'agro' ),
					'id' => 'shop_desc_typo',
					'type' => 'typography',
					'font-backup' => false,
					'letter-spacing'=> true,
					'all_styles' => true,
					'output' => array( '.woocommerce-page #nt-hero .nt-hero-description' ),
					'default' => array(
						'color' => '',
						'font-style' => '',
						'font-family' => '',
						'google' => true,
						'font-size' => '',
						'line-height' => ''
					),
					'required' => array( 'shop_hero', '=', '1' )
				),
				array(
					'title' => esc_html__( 'Before Shop Loop Content', 'agro' ),
					'subtitle' => esc_html__( 'Add your custom content before shop loop content', 'agro' ),
					'id' => 'shop_before_loop',
					'type' => 'editor',
					'default' => '',
				),
				array(
					'title' => esc_html__( 'After Shop Loop Content', 'agro' ),
					'subtitle' => esc_html__( 'Add your custom content after shop loop content', 'agro' ),
					'id' => 'shop_after_loop',
					'type' => 'editor',
					'default' => '',
				),
			));

			// SHOP PRODUCT PAGE SECTION
			$sections[] = array(
				'title' => esc_html__( 'Shop Product Page', 'agro' ),
				'id' => 'shopsection',
				'subsection'=> true,
				'desc' => esc_html__( 'These are main settings for general theme!', 'agro' ),
				'icon' => 'el el-shop',
				'fields'	=> array(
					array(
						'title' => esc_html__( 'Shop Product Page Layout', 'agro' ),
						'subtitle' => esc_html__( 'Choose the shop product page layout.', 'agro' ),
						'id' => 'shop_single_layout',
						'type' => 'image_select',
						'options' => array(
							'left-sidebar' => array(
								'alt' => 'Left Sidebar',
								'img' => ReduxFramework::$_url . 'assets/img/2cl.png'
							),
							'full-width' => array(
								'alt' => 'Full Width',
								'img' => ReduxFramework::$_url . 'assets/img/1col.png'
							),
							'right-sidebar' => array(
								'alt' => 'Right Sidebar',
								'img' => ReduxFramework::$_url . 'assets/img/2cr.png'
							)
						),
						'default' => 'right'
					),
					array(
						'title' => esc_html__( 'Shop Product Hero display', 'agro' ),
						'subtitle' => esc_html__( 'You can enable or disable the site shop page hero section with switch option.', 'agro' ),
						'id' => 'shop_product_hero',
						'type' => 'switch',
						'default' => 1,
						'on' => 'On',
						'off' => 'Off'
					),
					array(
						'title' => esc_html__( 'Shop Product Hero Alignment', 'agro' ),
						'subtitle' => esc_html__( 'Select shop page hero text alignment.', 'agro' ),
						'id' => 'shop_product_hero_alignment',
						'type' => 'select',
						'customizer'=> true,
						'options' => array(
							'text-left' => 'Left',
							'text-center' => 'Center',
							'text-right' => 'Right'
						),
						'default' => 'text-left'
					),
					array(
						'title' => esc_html__( 'Shop Product Hero Background', 'agro' ),
						'id' => 'shop_product_hero_bg',
						'type' => 'background',
						'output' => array( '.single-product #nt-hero' ),
						'required' => array( 'shop_product_hero', '=', '1' )
					),
					array(
						'title' => esc_html__( 'Shop Product Title', 'agro' ),
						'subtitle' => esc_html__( 'Add your shop page title here.', 'agro' ),
						'id' => 'shop_product_title',
						'type' => 'text',
						'default' => '',
						'required' => array( 'shop_product_hero', '=', '1' )
					),
					array(
						'title' => esc_html__( 'Shop Product Title Typography', 'agro' ),
						'id' => 'shop_product_title_typo',
						'type' => 'typography',
						'font-backup' => false,
						'letter-spacing'=> true,
						'all_styles' => true,
						'output' => array( '.single-product #nt-hero .nt-hero-title' ),
						'default' => array(
							'color' => '',
							'font-style' => '',
							'font-family' => '',
							'google' => true,
							'font-size' => '',
							'line-height' => ''
						),
						'required' => array( 'shop_product_hero', '=', '1' )
					),
					array(
						'title' => esc_html__( 'Shop Product Slogan', 'agro' ),
						'subtitle' => esc_html__( 'Add your shop page slogan here.', 'agro' ),
						'id' => 'shop_product_slogan',
						'type' => 'textarea',
						'default' => 'Shop Product Completed',
						'required' => array( 'shop_product_hero', '=', '1' )
					),
					array(
						'title' => esc_html__( 'Shop Product Slogan Typography', 'agro' ),
						'id' => 'shop_product_slogan_typo',
						'type' => 'typography',
						'font-backup' => false,
						'letter-spacing'=> true,
						'all_styles' => true,
						'output' => array( '.single-product #nt-hero .nt-hero-subtitle' ),
						'default' => array(
							'color' => '',
							'font-style' => '',
							'font-family' => '',
							'google' => true,
							'font-size' => '',
							'line-height' => ''
						),
						'required' => array( 'shop_product_hero', '=', '1' )
					),
					array(
						'title' => esc_html__( 'Shop Product Description', 'agro' ),
						'subtitle' => esc_html__( 'Add your shop page description here.', 'agro' ),
						'id' => 'shop_product_desc',
						'type' => 'textarea',
						'default' => '',
						'required' => array( 'shop_product_hero', '=', '1' )
					),
					array(
						'title' => esc_html__( 'Shop Product Description Typography', 'agro' ),
						'id' => 'shop_product_desc_typo',
						'type' => 'typography',
						'font-backup' => false,
						'letter-spacing'=> true,
						'all_styles' => true,
						'output' => array( '.single-product #nt-hero .nt-hero-description' ),
						'default' => array(
							'color' => '',
							'font-style' => '',
							'font-family' => '',
							'google' => true,
							'font-size' => '',
							'line-height' => ''
						),
						'required' => array( 'shop_product_hero', '=', '1' )
					),
                    array(
						'title' => esc_html__( 'Related Type', 'agro' ),
						'subtitle' => esc_html__( 'Select related section type.Default type is slider.', 'agro' ),
						'id' => 'shop_single_related_type',
						'type' => 'select',
						'customizer'=> true,
						'options' => array(
							'' => 'Select a option',
							'related-grid' => 'Grid',
							'related-slider' => 'slider',
						),
					),
                    array(
                        'title' => esc_html__('Related Post Count', 'agro'),
                        'subtitle' => esc_html__('You can control related post count with this option.', 'agro'),
                        'id' => 'shop_single_related_count',
                        'type' => 'slider',
                        'customizer'=> true,
                        'default' => 3,
                        'min' => 0,
                        'step' => 1,
                        'max' => 100,
                        'display_value' => 'text',
                    ),
                    array(
                        'title' => esc_html__('Related Post Column', 'agro'),
                        'subtitle' => esc_html__('You can control related post column with this option.', 'agro'),
                        'id' => 'shop_single_related_column',
                        'type' => 'slider',
                        'customizer'=> true,
                        'default' => 3,
                        'min' => 0,
                        'step' => 1,
                        'max' => 4,
                        'display_value' => 'text',
                        'required' => array( 'shop_single_related_type', '=', 'related-grid' )
                    ),
                    array(
						'title' => esc_html__( 'Related Section Title Typography', 'agro' ),
						'id' => 'shop_single_related_title_typo',
						'type' => 'typography',
						'font-backup' => false,
						'letter-spacing'=> true,
						'all_styles' => true,
						'output' => array( '.woocommerce .related.products h2' ),
						'default' => array(
							'color' => '',
							'font-style' => '',
							'font-family' => '',
							'google' => true,
							'font-size' => '',
							'line-height' => ''
						)
					),
                    array(
    					'title' => esc_html__( 'Before Product Content', 'agro' ),
    					'subtitle' => esc_html__( 'Add your custom content before shop product content', 'agro' ),
    					'id' => 'shop_single_before_content',
    					'type' => 'editor',
    					'default' => '',
    				),
    				array(
    					'title' => esc_html__( 'After Product Content', 'agro' ),
    					'subtitle' => esc_html__( 'Add your custom content after shop product content', 'agro' ),
    					'id' => 'shop_single_after_content',
    					'type' => 'editor',
    					'default' => '',
    				),
			));

			// SHOP PAGE SECTION
			$sections[] = array(
				'title' => esc_html__( 'Header Cart Button', 'agro' ),
				'id' => 'shopheadercartbtnsection',
				'subsection'=> true,
				'desc' => esc_html__( 'These are main settings for general theme!', 'agro' ),
				'icon' => 'el el-shop',
				'fields' => array(
					array(
						'title' => esc_html__( 'Hader Cart Button Display', 'agro' ),
						'subtitle' => esc_html__( 'You can enable or disable the site shop page hero section with switch option.', 'agro' ),
						'id' => 'header_shop_cart_display',
						'type' => 'switch',
						'default' => 0,
						'on' => 'On',
						'off' => 'Off',
					),
					array(
						'title' => esc_html__( 'Hader Cart Button Type', 'agro' ),
						'subtitle' => esc_html__( 'Select shop page header cart button type.', 'agro' ),
						'id' => 'header_shop_cart_type',
						'type' => 'select',
						'customizer'=> true,
						'options' => array(
							'' => 'Select a option',
							'icon-price' => 'icon-price',
							'icon-count' => 'icon-count',
							'title-price' => 'title-price',
							'title-count' => 'title-count'

						),
						'required' => array( 'header_shop_cart_display', '=', '1' ),
						'default' => 'icon-price'
					),
					array(
						'title' => esc_html__( 'Cart Button Title', 'agro' ),
						'subtitle' => esc_html__( 'Add your button title here.', 'agro' ),
						'id' => 'header_shop_cart_title',
						'type' => 'text',
						'default' => 'Cart',
						'required' => array(
							array( 'header_shop_cart_display', '=', '1' ),
							array( 'header_shop_cart_type', '!=', '' ),
							array( 'header_shop_cart_type', '!=', 'icon-price' ),
							array( 'header_shop_cart_type', '!=', 'icon-count' )
						)
					),
					array(
						'title' => esc_html__( 'Cart Button Attr Title', 'agro' ),
						'subtitle' => esc_html__( 'Add your button title attribute here.', 'agro' ),
						'id' => 'header_shop_cart_title_attr',
						'type' => 'text',
						'default' => 'View cart',
						'required' => array( 'header_shop_cart_display', '=', '1' )
					),
					array(
						'title' => esc_html__( 'Cart Button Icon Customize', 'agro' ),
						'subtitle' => esc_html__( 'Add your cart button icon here.', 'agro' ),
						'id' => 'header_shop_cart_icon',
						'type' => 'text',
						'default' => '<i class="fas fa-shopping-cart"></i>',
						'required' => array(
							array( 'header_shop_cart_display', '=', '1' ),
							array( 'header_shop_cart_type', '!=', '' ),
							array( 'header_shop_cart_type', '!=', 'title-price' ),
							array( 'header_shop_cart_type', '!=', 'title-count' )
						)
					)
			));

			return $sections;
		}
		add_filter('redux/options/'.$agro_pre.'/sections', 'agro_dynamic_section');
	}
}

/*************************************************
## WOOCOMMERCE HERO FUNCTION
*************************************************/

if(! function_exists('agro_woo_hero_section')){
	function agro_woo_hero_section(){

		global $agro;

		$h_v = isset($agro['shop_hero']) ? $agro['shop_hero'] : '1';
		$h_t = isset($agro['shop_title']) && $agro['shop_title'] != '' ? $agro['shop_title'] : esc_html__( 'Shop', 'agro' );
		$h_s = isset($agro['shop_slogan']) ? $agro['shop_slogan'] : '';
		$h_d = isset($agro['shop_desc']) ? $agro['shop_desc'] : '';
		$h_b = isset($agro['breadcrumbs_onoff']) ? $agro['breadcrumbs_onoff'] : '0';
		$h_a = isset($agro['shop_hero_alignment']) ? $agro['shop_hero_alignment'] : 'text-left';

		if( $h_v != '0' ) {

			echo '<div id="nt-hero" class="page-id-'. get_the_ID() .' hero-container" data-agro-bg="">
				<div class="container ">
					<div class="row">
						<div class="col-md-12">
							<div class="hero-content '. esc_attr($h_a) .'">
								<div class="hero-innner-last-child">';

									// Slogan
									if( $h_s != '' ) {
										echo '<h6 class="nt-hero-subtitle">'. wp_kses( $h_s, agro_allowed_html() ) .'</h6>';
									}

									// Title
									if( $h_t != '' ) {
									echo ' <h1 class="nt-hero-title">'. wp_kses( $h_t, agro_allowed_html() ) .'</h1>';
									}

									// Description
									if ( $h_d != '' ) {
										echo '<p class="nt-hero-description">'. wp_kses( $h_d, agro_allowed_html() ) .'</p>';
									}

                                    if( $h_b != '0' ) {
                                        if ( function_exists( 'bcn_display') ) {
                                            bcn_display();
                                        } else {
                                            agro_breadcrumbs();
                                        }
                                    }

								echo '</div>
			  				</div>
						</div>
					</div><!-- End container -->
				</div><!-- End hero-content -->
			</div>	<!-- End Hero Section -->';

		} // hide hero area
	}
}


/*************************************************
## WOOCOMMERCE HERO FUNCTION
*************************************************/

if(! function_exists('agro_woo_single_hero_section')){
	function agro_woo_single_hero_section()
	{

		global $agro;

		$h_v = isset($agro['shop_product_hero']) ? $agro['shop_product_hero'] : '1';
		$h_t = isset($agro['shop_product_title']) && $agro['shop_product_title'] != '' ? $agro['shop_product_title'] : get_the_title();
		$h_s = isset($agro['shop_product_slogan']) ? $agro['shop_product_slogan'] : '';
		$h_d = isset($agro['shop_product_desc']) ? $agro['shop_product_desc'] : '';
		$h_b = isset($agro['breadcrumbs_onoff']) ? $agro['breadcrumbs_onoff'] : '0';
		$h_a = isset($agro['shop_product_hero_alignment']) ? $agro['shop_product_hero_alignment'] : 'text-left';

		if( $h_v != '0' ) {

			echo '<div id="nt-hero" class="page-id-'. get_the_ID() .' hero-container">
				<div class="container ">
					<div class="row">
						<div class="col-md-12">
							<div class="hero-content '. esc_attr($h_a) .'">
								<div class="hero-innner-last-child">';

                                // Slogan
                                if( $h_s != '' ) {
                                    echo '<h6 class="nt-hero-subtitle">'. wp_kses( $h_s, agro_allowed_html() ) .'</h6>';
                                }

                                // Title
                                if( $h_t != '' ) {
                                    echo ' <h1 class="nt-hero-title">'. wp_kses( $h_t, agro_allowed_html() ) .'</h1>';
                                }

                                // Description
                                if ( $h_d != '' ) {
                                    echo '<p class="nt-hero-description">'. wp_kses( $h_d, agro_allowed_html() ) .'</p>';
                                }

                                if( $h_b != '0' ) {
                                    if ( function_exists( 'bcn_display') ) {
                                        bcn_display();
                                    } else {
                                        agro_breadcrumbs();
                                    }
                                }

								echo '</div>
			  				</div>
						</div>
					</div><!-- End container -->
				</div><!-- End hero-content -->
			</div>	<!-- End Hero Section -->';

		} // hide hero area
	}
}

/**************************************************************
## OUR HOOKED IN FUNCTION – $FIELDS IS PASSED VIA THE FILTER!
**************************************************************/

function agro_paypal_img_url( $variablen ) {

	$url = get_theme_file_uri().'/images/paypal.png';

	return $url;

}
add_filter( 'woocommerce_paypal_express_checkout_button_img_url' , 'agro_paypal_img_url' );


/*************************************************
## ADD CUSTOM CSS FOR WOOCOMMERCE
*************************************************/


if ( !function_exists( 'agro_woo_scripts' ) ) {
	function agro_woo_scripts() {
        $rtl = is_rtl() ? '-rtl' : '';
		wp_enqueue_style( 'agro-woocommerce-custom',get_template_directory_uri() . '/woocommerce/woocommerce-custom'.$rtl.'.css',false, '1.0');

	}
	add_action( 'wp_enqueue_scripts', 'agro_woo_scripts' );
}


/*************************************************
## WOOCOMMERCE CART BUTTON FOR HEADER
*************************************************/

if(!function_exists('agro_woo_header_cart_button')) {

	function agro_woo_header_cart_button() {

		ob_start();

		$navstyle = is_page() ? rwmb_meta('agro_page_header_style') : agro_settings('header_style', '1');
		if ($navstyle == '2') {
			$btnstyle = ' custom-btn--style-2';
		} elseif ($navstyle == '3') {
			$btnstyle = ' custom-btn--style-5';
		} else {
			$btnstyle = ' custom-btn--style-4';
		}

		$cart_title_btn = agro_settings('header_shop_cart_title', 'Cart');
		$cart_title_attr = agro_settings('header_shop_cart_title_attr', 'View cart');
		$cart_icon = agro_settings('header_shop_cart_icon', '<i class="fas fa-shopping-cart"></i>');


		if ( WC()->cart->cart_contents_count > 0 ) {  ?>

			<li class="menu-item li-btn-cart">

			<?php if ( 'title-price' == agro_settings('header_shop_cart_type', 'title-count')  ) { ?>

				<a class="cart-contents title-price custom-btn custom-btn--small<?php echo esc_attr($btnstyle); ?>" href="<?php echo wc_get_cart_url(); ?>" title="<?php echo esc_attr($cart_title_attr); ?>"><?php echo wp_kses($cart_title_btn,agro_allowed_html());?> - <?php echo WC()->cart->cart_contents_count; ?></a>

			<?php } elseif ( 'title-count' == agro_settings('header_shop_cart_type') ) { ?>

				<a class="cart-contents title-count custom-btn custom-btn--small<?php echo esc_attr($btnstyle); ?>" href="<?php echo wc_get_cart_url(); ?>" title="<?php echo esc_attr($cart_title_attr); ?>"><?php echo wp_kses($cart_title_btn,agro_allowed_html());?> - <?php echo sprintf(_n('%d item', '%d items', WC()->cart->cart_contents_count, 'agro'), WC()->cart->cart_contents_count);?></a>

			<?php } elseif ( 'icon-price' == agro_settings('header_shop_cart_type') ) { ?>

				<a class="cart-contents icon-price custom-btn custom-btn--small<?php echo esc_attr($btnstyle); ?>" href="<?php echo wc_get_cart_url(); ?>" title="<?php echo esc_attr($cart_title_attr); ?>"><?php echo wp_kses($cart_icon,agro_allowed_html());?> - <?php echo WC()->cart->get_cart_total(); ?></a>

			<?php } elseif ( 'icon-count' == agro_settings('header_shop_cart_type') ) { ?>

				<a class="cart-contents icon-count custom-btn custom-btn--small<?php echo esc_attr($btnstyle); ?>" href="<?php echo wc_get_cart_url(); ?>" title="<?php echo esc_attr($cart_title_attr); ?>"><?php echo wp_kses($cart_icon,agro_allowed_html());?> - <?php echo sprintf(_n('%d item', '%d items', WC()->cart->cart_contents_count, 'agro'), WC()->cart->cart_contents_count);?></a>

			<?php } else { ?>

				<a class="cart-contents custom-btn custom-btn--small<?php echo esc_attr($btnstyle); ?>" href="<?php echo wc_get_cart_url(); ?>" title="<?php echo esc_attr($cart_title_attr); ?>"><?php echo sprintf(_n('%d item', '%d items', WC()->cart->cart_contents_count, 'agro'), WC()->cart->cart_contents_count);?> - <?php echo WC()->cart->get_cart_total(); ?></a>

			<?php }  ?>

			</li>

		<?php
		}

	return ob_get_clean();
	}
}


/*************************************************
## RETURN CART FRAGMENT
*************************************************/

if(!function_exists('agro_woo_header_add_to_cart_custom_fragment')) {

	function agro_woo_header_add_to_cart_custom_fragment( $fragments ) {

		ob_start();

		$navstyle = is_page() ? rwmb_meta('agro_page_header_style') : agro_settings('header_style', '1');
		if ($navstyle == '2') {
			$btnstyle = ' custom-btn--style-2';
		} elseif ($navstyle == '3') {
			$btnstyle = ' custom-btn--style-5';
		} else {
			$btnstyle = ' custom-btn--style-4';
		}
		$cart_title_btn = agro_settings('header_shop_cart_title', 'Cart');
		$cart_title_attr = agro_settings('header_shop_cart_title_attr', 'View cart');
		$cart_icon = agro_settings('header_shop_cart_icon', '<i class="fa fa-shopping-cart"></i>');


		if ( 'title-price' == agro_settings('header_shop_cart_type', 'title-price')  ) { ?>

			<a class="cart-contents title-price custom-btn custom-btn--small<?php echo esc_attr($btnstyle); ?>" href="<?php echo wc_get_cart_url(); ?>" title="<?php echo esc_attr($cart_title_attr); ?>"><?php echo wp_kses($cart_title_btn,agro_allowed_html());?> - <?php echo WC()->cart->get_cart_total(); ?></a>

		<?php } elseif ( 'title-count' == agro_settings('header_shop_cart_type') ) { ?>

			<a class="cart-contents title-count custom-btn custom-btn--small<?php echo esc_attr($btnstyle); ?>" href="<?php echo wc_get_cart_url(); ?>" title="<?php echo esc_attr($cart_title_attr); ?>"><?php echo wp_kses($cart_title_btn,agro_allowed_html());?> - <?php echo sprintf(_n('%d item', '%d items', WC()->cart->cart_contents_count, 'agro'), WC()->cart->cart_contents_count);?></a>

		<?php } elseif ( 'icon-price' == agro_settings('header_shop_cart_type') ) { ?>

			<a class="cart-contents icon-price custom-btn custom-btn--small<?php echo esc_attr($btnstyle); ?>" href="<?php echo wc_get_cart_url(); ?>" title="<?php echo esc_attr($cart_title_attr); ?>"><?php echo wp_kses($cart_icon,agro_allowed_html());?> - <?php echo WC()->cart->get_cart_total(); ?></a>

		<?php } elseif ( 'icon-count' == agro_settings('header_shop_cart_type') ) { ?>

			<a class="cart-contents icon-count custom-btn custom-btn--small<?php echo esc_attr($btnstyle); ?>" href="<?php echo wc_get_cart_url(); ?>" title="<?php echo esc_attr($cart_title_attr); ?>"><?php echo wp_kses($cart_icon,agro_allowed_html());?> - <?php echo sprintf(_n('%d item', '%d items', WC()->cart->cart_contents_count, 'agro'), WC()->cart->cart_contents_count);?></a>

		<?php } else { ?>

			<a class="cart-contents custom-btn custom-btn--small<?php echo esc_attr($btnstyle); ?>" href="<?php echo wc_get_cart_url(); ?>" title="<?php echo esc_attr($cart_title_attr); ?>"><?php echo sprintf(_n('%d item', '%d items', WC()->cart->cart_contents_count, 'agro'), WC()->cart->cart_contents_count);?> - <?php echo WC()->cart->get_cart_total(); ?></a>

		<?php }

		$fragments['a.cart-contents'] = ob_get_clean();

		return $fragments;

	}
	add_filter('woocommerce_add_to_cart_fragments', 'agro_woo_header_add_to_cart_custom_fragment');
}


/*************************************************
 ## ADD WOOCOMMERCE CART TO MENU ITEM
*************************************************/

if(!function_exists('agro_add_woo_cart_btn_to_header')) {

	function agro_add_woo_cart_btn_to_header ( $items, $args ) {

		if ( '1' == agro_settings('header_shop_cart_display', '1') ) {
			$items .=  agro_woo_header_cart_button();
		}

		return $items;
	}
	add_filter('wp_nav_menu_items', 'agro_add_woo_cart_btn_to_header', 10, 2);
}


/**
 * @snippet       Checkbox to display Custom Product Badge Part 1 - WooCommerce
 * @how-to        Get CustomizeWoo.com FREE
 * @source        https://businessbloomer.com/?p=73566
 * @author        Rodolfo Melogli
 * @testedwith    Woo 3.5.1
 * @donate $9     https://businessbloomer.com/bloomer-armada/
 */

// -----------------------------------------
// 1. Add new checkbox to product edit page (General tab)

add_action( 'woocommerce_product_options_general_product_data', 'agro_add_badge_checkbox_to_products' );
function agro_add_badge_checkbox_to_products() {
	woocommerce_wp_select(
		array(
			'id'          => 'agro_new_badge',
			'label'       => __( 'Agro Badge?', 'agro' ),
			'options'     => array(
				'' => 'Select a badge',
				'new' => __( 'New', 'agro' ),
				'hot' => __( 'Hot', 'agro' ),
			),
			'desc_tip'    => false,
			'description' => __( 'This options for agro shortcode.', 'agro' ),
		)
	);
}

// -----------------------------------------
// 2. Save checkbox via custom field
add_action( 'save_post', 'agro_save_badge_checkbox_to_post_meta' );

function agro_save_badge_checkbox_to_post_meta( $product_id ) {
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
        return;
    if ( isset( $_POST['agro_new_badge'] ) ) {
            update_post_meta( $product_id, 'agro_new_badge', $_POST['agro_new_badge'] );
    } else {
		delete_post_meta( $product_id, 'agro_new_badge' );
	}
}
/**
 * Change number of related products output
 */

add_filter( 'woocommerce_output_related_products_args', 'agro_woo_related_products_limit', 20 );
function agro_woo_related_products_limit( $args ) {

	$args['posts_per_page'] = agro_settings('shop_single_related_count', '6');
	$args['columns'] = agro_settings('shop_single_related_column', '3');
	return $args;
}

/**
 * Change number of products that are displayed per page (shop page)
 */
add_filter( 'loop_shop_per_page', 'agro_loop_shop_per_page', 20 );

function agro_loop_shop_per_page( $cols ) {
  // $cols contains the current number of products per page based on the value stored on Options -> Reading
  // Return the number of products you wanna show per page.
  $cols = 12;
  return $cols;
}


/*************************************************
## ADD THEME SUPPORT FOR WOOCOMMERCE
*************************************************/

function agro_woo_theme_setup() {

	add_theme_support( 'woocommerce'  );
	add_theme_support( 'wc-product-gallery-zoom' );
	add_theme_support( 'wc-product-gallery-lightbox' );
	add_theme_support( 'wc-product-gallery-slider' );

}
add_action( 'after_setup_theme', 'agro_woo_theme_setup' );



/*************************************************
## REGISTER SIDEBAR FOR WOOCOMMERCE
*************************************************/


if ( !function_exists( 'agro_woo_widgets_init' ) ) {
	function agro_woo_widgets_init() {

		//Shop page sidebar
		register_sidebar( array(
			'name' => esc_html__( 'Shop Page Sidebar', 'agro' ),
			'id' => 'shop-sidebar-1',
			'description' => esc_html__( 'These widgets for the Blog page.','agro' ),
			'before_widget' => '<div class="nt-sidebar-inner-widget  %2$s">',
			'after_widget' => '</div>',
			'before_title' => '<h4 class="nt-sidebar-inner-widget-title"><span>',
			'after_title' => '</span></h4>'
		) );
		//Single product sidebar
		register_sidebar( array(
			'name' => esc_html__( 'Shop Single Page Sidebar', 'agro' ),
			'id' => 'product-sidebar-1',
			'description' => esc_html__( 'These widgets for the Blog page.','agro' ),
			'before_widget' => '<div class="nt-sidebar-inner-widget %2$s">',
			'after_widget' => '</div>',
			'before_title' => '<h4 class="nt-sidebar-inner-widget-title"><span>',
			'after_title' => '</span></h4>'
		) );
	}
	add_action( 'widgets_init', 'agro_woo_widgets_init' );
}


/************************************************************
## ADD THEME CSS SETTINGS TO WOOCOMMERCE AND WP INLINE STYLE
*************************************************************/


if ( !function_exists( 'agro_woo_theme_css_options' ) ) {
    function agro_woo_theme_css_options() {

        /* CSS to output */
        global $agro;

        $theCSS = '';

        $theme_color = $agro['theme_main_color'];

		if ( $theme_color !='' ) {

    		// css color
    		$theCSS .= '.product.t-left:hover p.paragraph, .woocommerce div.product .woocommerce-tabs ul.tabs li a:hover, .woocommerce #reviews #comments ol.commentlist li .comment-text p.meta .woocommerce-review__published-date , .woocommerce-info::before, a.showcoupon,.woocommerce .price ins,.woocommerce .price del { color: '.$theme_color.'!important; }';

            //css border color
    		$theCSS .= '.woocommerce-account .woocommerce-MyAccount-content p a, .woocommerce-error, .woocommerce-info, .woocommerce-message, .woocommerce div.product.sale div.images.woocommerce-product-gallery, .woocommerce div.product .woocommerce-tabs ul.tabs li a:hover, .woocommerce div.product .woocommerce-tabs ul.tabs li.active a, .woocommerce-Address-title .edit ,.woocommerce div.product .woocommerce-tabs ul.tabs li.active a{border-color:'.$theme_color.'!important;}';
    		$theCSS .= '.stack-title a:hover, .woocommerce div.product .woocommerce-tabs ul.tabs li a:hover, .woocommerce div.product .woocommerce-tabs ul.tabs li.active  { border-color:' .$theme_color. '; }';

            //css background color
    		$theCSS .= '.woocommerce span.onsale ,.woocommerce div.product .woocommerce-tabs ul.tabs li.active,.wc-proceed-to-checkout a.button.alt:hover { background-color: '.$theme_color.'!important;}';

    		//theme button bg color
    		$theCSS .= '.button, .woocommerce #respond input#submit, .woocommerce a.button, .woocommerce button.button, .woocommerce input.button, .woocommerce #respond input#submit.alt, .woocommerce a.button.alt, .woocommerce button.button.alt, .woocommerce input.button.alt, .single-product .form-submit input#submit,a.added_to_cart{ background-color:'. $theme_color .'!important;border-color:'. $theme_color .'!important!important; }';

    		$theCSS .= '.woocommerce #respond input#submit:hover, .woocommerce a.button:hover, .woocommerce button.button:hover, .woocommerce input.button:hover, .woocommerce #respond input#submit.alt:hover, .woocommerce a.button.alt:hover, .woocommerce button.button.alt:hover, .woocommerce input.button.alt:hover, .single-product .form-submit input#submit:hover, a.added_to_cart:hover{border-color:'. $theme_color .'!important;}';

    		$theCSS .= '.button, .woocommerce #respond input#submit:hover, .woocommerce a.button:hover, .woocommerce button.button:hover, .woocommerce input.button:hover, .woocommerce #respond input#submit.alt:hover, .woocommerce a.button.alt:hover, .woocommerce button.button.alt:hover, .woocommerce input.button.alt:hover, .single-product .form-submit input#submit:hover, a.added_to_cart:hover{border-color:'. $theme_color .'!important;}';

        }

		/* Add CSS to agro-custom-style.css */
		wp_register_style( 'agro-woo-style', false );
		wp_enqueue_style( 'agro-woo-style' );
		wp_add_inline_style( 'agro-woo-style', $theCSS );

	}
	add_action( 'wp_enqueue_scripts', 'agro_woo_theme_css_options' );
}
