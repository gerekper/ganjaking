<?php
/**
 * Porto Customizer Selective Refresh
 *
 * @author     Porto Themes
 * @category   Admin Functions
 * @since      4.8.0
 */

defined( 'ABSPATH' ) || exit;

function porto_customizer_current_state_options() {
	global $reduxPortoSettings, $porto_settings;
	$new_options    = get_option( $reduxPortoSettings->args['opt_name'] );
	$porto_settings = wp_parse_args( $new_options, $porto_settings );
}
add_action( 'wp_loaded', 'porto_customizer_current_state_options', 99 );

function porto_customizer_refresh_partials( WP_Customize_Manager $wp_customize ) {
	if ( ! isset( $wp_customize->selective_refresh ) ) {
		return;
	}

	/* header */
	$settings = array( 'porto_settings[logo]', 'porto_settings[logo-retina]', 'porto_settings[sticky-logo]', 'porto_settings[sticky-logo-retina]', 'porto_settings[logo-overlay]', 'porto_settings[show-header-top]', 'porto_settings[change-header-logo]', 'porto_settings[minicart-type]', 'porto_settings[welcome-msg]', 'porto_settings[header-contact-info]', 'porto_settings[header-copyright]', 'porto_settings[show-header-tooltip]', 'porto_settings[header-tooltip]', 'porto_settings[logo-retina-width]', 'porto_settings[logo-retina-height]', 'porto_settings[logo-overlay-width]', 'porto_settings[header-type-select]', 'porto_settings[header-type]', 'porto_settings[wpml-switcher]', 'porto_settings[wpml-switcher-pos]', 'porto_settings[wpml-switcher-html]', 'porto_settings[wcml-switcher]', 'porto_settings[wcml-switcher-pos]', 'porto_settings[wcml-switcher-html]', 'porto_settings[show-header-socials]', 'porto_settings[show-searchform]', 'porto_settings[menu-login-pos]', 'porto_settings[menu-enable-register]', 'porto_settings[menu-show-login-icon]', 'porto_settings[menu-block]', 'porto_settings[show-sticky-contact-info]' );
	$wp_customize->selective_refresh->add_partial(
		'header',
		array(
			'selector'            => '#header',
			'container_inclusive' => true,
			'settings'            => $settings,
			'render_callback'     => function() {
				return get_template_part( 'header/header' );
			},
		)
	);
	$wp_customize->selective_refresh->add_partial(
		'header_social_links',
		array(
			'selector'            => '#header .share-links',
			'container_inclusive' => false,
			'settings'            => array( 'porto_settings[header-socials-nofollow]', 'porto_settings[header-social-facebook]', 'porto_settings[header-social-twitter]', 'porto_settings[header-social-rss]', 'porto_settings[header-social-pinterest]', 'porto_settings[header-social-youtube]', 'porto_settings[header-social-instagram]', 'porto_settings[header-social-skype]', 'porto_settings[header-social-linkedin]', 'porto_settings[header-social-vk]', 'porto_settings[header-social-xing]', 'porto_settings[header-social-tumblr]', 'porto_settings[header-social-reddit]', 'porto_settings[header-social-vimeo]', 'porto_settings[header-social-telegram]', 'porto_settings[header-social-yelp]', 'porto_settings[header-social-flickr]', 'porto_settings[header-social-whatsapp]', 'porto_settings[header-social-wechat]', 'porto_settings[header-social-tiktok]' ),
			'render_callback'     => function() {
				return porto_header_socials();
			},
		)
	);
	$wp_customize->selective_refresh->add_partial(
		'searchform',
		array(
			'selector'            => '#header .searchform-popup',
			'container_inclusive' => true,
			'settings'            => array( 'porto_settings[search-layout]', 'porto_settings[search-type]', 'porto_settings[search-cats]', 'porto_settings[search-cats-mobile]', 'porto_settings[search-sub-cats]' ),
			'render_callback'     => function() {
				return porto_search_form();
			},
		)
	);
	$wp_customize->selective_refresh->add_partial(
		'breadcrumb',
		array(
			'selector'            => '.page-top',
			'container_inclusive' => true,
			'settings'            => array( 'porto_settings[breadcrumbs-parallax]', 'porto_settings[breadcrumbs-parallax-speed]', 'porto_settings[show-pagetitle]', 'porto_settings[show-breadcrumbs]', 'porto_settings[pagetitle-archives]', 'porto_settings[pagetitle-parent]', 'porto_settings[breadcrumbs-prefix]', 'porto_settings[breadcrumbs-blog-link]', 'porto_settings[breadcrumbs-shop-link]', 'porto_settings[breadcrumbs-archives-link]', 'porto_settings[breadcrumbs-categories]', 'porto_settings[breadcrumbs-delimiter]', 'porto_settings[breadcrumbs-type]', 'porto_settings[blog-title]', 'porto_settings[breadcrumbs-css-class]' ),
			'render_callback'     => function() {
				return get_template_part( 'breadcrumbs' );
			},
		)
	);
	$wp_customize->selective_refresh->add_partial(
		'footer',
		array(
			'selector'            => '#footer',
			'container_inclusive' => true,
			'settings'            => array( 'porto_settings[footer-parallax]', 'porto_settings[footer-parallax-speed]', 'porto_settings[footer-logo]', 'porto_settings[footer-ribbon]', 'porto_settings[footer-copyright-pos]', 'porto_settings[footer-payments]', 'porto_settings[footer-payments-image]', 'porto_settings[footer-payments-image-alt]', 'porto_settings[footer-payments-link]', 'porto_settings[show-footer-tooltip]', 'porto_settings[footer-tooltip]', 'porto_settings[footer-type]', 'porto_settings[footer-customize]', 'porto_settings[footer-widget1]', 'porto_settings[footer-widget2]', 'porto_settings[footer-widget3]', 'porto_settings[footer-widget4]', 'porto_settings[blog-footer_view]' ),
			'render_callback'     => function() {
				return get_template_part( 'footer/footer' );
			},
		)
	);
	$wp_customize->selective_refresh->add_partial(
		'blog-content_top',
		array(
			'selector'            => '#content-top',
			'container_inclusive' => false,
			'fallback_refresh'    => false,
			'settings'            => array( 'porto_settings[blog-content_top]' ),
			'render_callback'     => function() {
				$result = '';
				$content_top = porto_get_meta_value( 'content_top' );
				if ( $content_top ) {
					foreach ( explode( ',', $content_top ) as $block ) {
						$result .= do_shortcode( '[porto_block name="' . $block . '"]' );
					}
				}
				return $result;
			},
		)
	);
	$wp_customize->selective_refresh->add_partial(
		'blog-content_bottom',
		array(
			'selector'            => '#content-bottom',
			'container_inclusive' => false,
			'fallback_refresh'    => false,
			'settings'            => array( 'porto_settings[blog-content_bottom]' ),
			'render_callback'     => function() {
				$result = '';
				$content_bottom = porto_get_meta_value( 'content_bottom' );
				if ( $content_bottom ) {
					foreach ( explode( ',', $content_bottom ) as $block ) {
						$result .= do_shortcode( '[porto_block name="' . $block . '"]' );
					}
				}
				return $result;
			},
		)
	);
	$wp_customize->selective_refresh->add_partial(
		'blog-content_inner_top',
		array(
			'selector'            => '#content-inner-top',
			'container_inclusive' => false,
			'fallback_refresh'    => false,
			'settings'            => array( 'porto_settings[blog-content_inner_top]' ),
			'render_callback'     => function() {
				$result = '';
				$content_inner_top = porto_get_meta_value( 'content_inner_top' );
				if ( $content_inner_top ) {
					foreach ( explode( ',', $content_inner_top ) as $block ) {
						$result .= do_shortcode( '[porto_block name="' . $block . '"]' );
					}
				}
				return $result;
			},
		)
	);
	$wp_customize->selective_refresh->add_partial(
		'blog-content_inner_bottom',
		array(
			'selector'            => '#content-inner-bottom',
			'container_inclusive' => false,
			'fallback_refresh'    => false,
			'settings'            => array( 'porto_settings[blog-content_inner_bottom]' ),
			'render_callback'     => function() {
				$result = '';
				$content_inner_bottom = porto_get_meta_value( 'content_inner_bottom' );
				if ( $content_inner_bottom ) {
					foreach ( explode( ',', $content_inner_bottom ) as $block ) {
						$result .= do_shortcode( '[porto_block name="' . $block . '"]' );
					}
				}
				return $result;
			},
		)
	);
	$wp_customize->selective_refresh->add_partial(
		'single-post',
		array(
			'selector'            => '.single-post article.post',
			'container_inclusive' => true,
			'fallback_refresh'    => false,
			'settings'            => array( /*'porto_settings[post-slideshow]',*/ 'porto_settings[post-title]', 'porto_settings[post-share]', 'porto_settings[post-share-position]', 'porto_settings[post-author]', 'porto_settings[post-comments]', 'porto_settings[post-replace-pos]' ),
			'render_callback'     => function() {
				if ( is_singular( 'post' ) ) {
					global $post, $porto_settings;
					$post_layout = get_post_meta( $post->ID, 'post_layout', true );
					$post_layout = ( 'default' == $post_layout || ! $post_layout ) ? $porto_settings['post-content-layout'] : $post_layout;

					add_filter(
						'the_content',
						function() {
							global $post;
							return do_shortcode( $post->post_content );
						}
					);

					return get_template_part( 'content', 'post-' . $post_layout );
				}
				return '';
			},
		)
	);
	$wp_customize->selective_refresh->add_partial(
		'mobile-panel-add-switcher',
		array(
			'selector'            => '#side-nav-panel',
			'container_inclusive' => false,
			'fallback_refresh'    => true,
			'settings'            => array( 'porto_settings[mobile-panel-add-switcher]', 'porto_settings[mobile-panel-add-search]' ),
			'render_callback'     => function() {
				return get_template_part( 'panel' );
			},
		)
	);

	// woocommerce
	if ( class_exists( 'Woocommerce' ) ) :
		$wp_customize->selective_refresh->add_partial(
			'archive-product',
			array(
				'selector'            => 'body.archive.woocommerce #primary.content-area',
				'fallback_refresh'    => false,
				'container_inclusive' => true,
				'settings'            => array( 'porto_settings[category-item]', 'porto_settings[shop-product-cols]', 'porto_settings[shop-product-cols-mobile]', 'porto_settings[product-cols]', 'porto_settings[product-cols-mobile]', 'porto_settings[cat-view-type]', 'porto_settings[category-image-hover]', 'porto_settings[product-stock]', 'porto_settings[category-addlinks-convert]', 'porto_settings[category-addlinks-pos]', 'porto_settings[add-to-cart-notification]', 'porto_settings[show_swatch]', 'porto_settings[product-categories]', 'porto_settings[product-review]', 'porto_settings[product-price]', 'porto_settings[product-desc]', 'porto_settings[product-wishlist]', 'porto_settings[product-quickview]', 'porto_settings[product-compare]', 'porto_settings[product-labels]', 'porto_settings[product-sale-label]', 'porto_settings[product-sale-percent]', 'porto_settings[product-new-days]' ),
				'render_callback'     => function() {
					if ( defined( 'PORTO_SHORTCODES_URL' ) ) {
						wp_register_script( 'countdown', PORTO_SHORTCODES_URL . 'assets/js/countdown.min.js', array( 'jquery' ), PORTO_SHORTCODES_VERSION, true );
						wp_register_script( 'porto_shortcodes_countdown_loader_js', PORTO_SHORTCODES_URL . 'assets/js/countdown-loader.min.js', array( 'jquery' ), PORTO_SHORTCODES_VERSION, true );
						wc_get_template_part( 'archive-product-content' );
					}
				},
			)
		);
		$wp_customize->selective_refresh->add_partial(
			'single-product',
			array(
				'selector'            => '.single-product #content > .product',
				'fallback_refresh'    => false,
				'container_inclusive' => true,
				'settings'            => array( 'porto_settings[product-nav]', 'porto_settings[product-custom-tabs-count]', 'porto_settings[product-short-desc]', 'porto_settings[product-tabs-pos]', 'porto_settings[product_variation_display_mode]', 'porto_settings[product-attr-desc]', 'porto_settings[product-tab-title]', 'porto_settings[product-tab-block]', 'porto_settings[product-tab-priority]', 'porto_settings[product-labels]', 'porto_settings[product-sale-label]', 'porto_settings[product-sale-percent]', 'porto_settings[product-new-days]', 'porto_settings[product-share]', 'porto_settings[product-thumbs]', 'porto_settings[product-thumbs-count]', 'porto_settings[product-zoom]', 'porto_settings[product-zoom-mobile]', 'porto_settings[product-image-popup]', 'porto_settings[zoom-type]', 'porto_settings[zoom-scroll]', 'porto_settings[zoom-lens-size]', 'porto_settings[zoom-lens-shape]', 'porto_settings[zoom-contain-lens]', 'porto_settings[zoom-lens-border]', 'porto_settings[zoom-border]', 'porto_settings[zoom-border-color]' ),
				'render_callback'     => function() {
					if ( is_product() ) {
						global $post, $product, $porto_layout, $page;
						if ( ! $page ) {
							$page = 1;
						}
						$product = wc_get_product( $post->ID );
						if ( ! $porto_layout ) {
							$porto_layout = porto_meta_layout();
							$porto_layout = $porto_layout[0];
						}
						add_filter(
							'the_content',
							function() {
								global $post;
								return do_shortcode( $post->post_content );
							}
						);
						if ( defined( 'PORTO_SHORTCODES_URL' ) ) {
							wp_register_script( 'countdown', PORTO_SHORTCODES_URL . 'assets/js/countdown.min.js', array( 'jquery' ), PORTO_SHORTCODES_VERSION, true );
							wp_register_script( 'porto_shortcodes_countdown_loader_js', PORTO_SHORTCODES_URL . 'assets/js/countdown-loader.min.js', array( 'jquery' ), PORTO_SHORTCODES_VERSION, true );
							wc_get_template_part( 'content', 'single-product' );
						}
					}
				},
			)
		);
		$wp_customize->selective_refresh->add_partial(
			'single-product-meta',
			array(
				'selector'            => '.single-product .product_meta',
				'fallback_refresh'    => false,
				'container_inclusive' => true,
				'settings'            => array( 'porto_settings[product-metas]' ),
				'render_callback'     => function() {
					if ( is_product() ) {
						global $post, $product;
						$product = wc_get_product( $post->ID );
						wc_get_template( 'single-product/meta.php' );
					}
				},
			)
		);
		$wp_customize->selective_refresh->add_partial(
			'single-product-related',
			array(
				'selector'            => '.single-product .related.products',
				'fallback_refresh'    => false,
				'container_inclusive' => true,
				'settings'            => array( 'porto_settings[product-related]', 'porto_settings[product-related-count]', 'porto_settings[product-related-cols]' ),
				'render_callback'     => function() {
					if ( is_product() ) {
						global $post, $product;
						$product = wc_get_product( $post->ID );
						woocommerce_output_related_products();
					}
				},
			)
		);
		$wp_customize->selective_refresh->add_partial(
			'single-product-upsells',
			array(
				'selector'            => '.single-product .upsells.products',
				'fallback_refresh'    => false,
				'container_inclusive' => true,
				'settings'            => array( 'porto_settings[product-upsells]', 'porto_settings[product-upsells-count]', 'porto_settings[product-upsells-cols]' ),
				'render_callback'     => function() {
					if ( is_product() ) {
						global $post, $product;
						$product = wc_get_product( $post->ID );
						woocommerce_upsell_display();
					}
				},
			)
		);
		$wp_customize->selective_refresh->add_partial(
			'cart-product-cross-sells',
			array(
				'selector'            => '.cross-sells',
				'fallback_refresh'    => false,
				'container_inclusive' => true,
				'settings'            => array( 'porto_settings[product-crosssell]', 'porto_settings[product-crosssell-count]' ),
				'render_callback'     => function() {
					if ( is_cart() ) {
						woocommerce_cross_sell_display();
					}
				},
			)
		);
		$wp_customize->selective_refresh->add_partial(
			'minicart',
			array(
				'selector'            => '#mini-cart',
				'container_inclusive' => true,
				'settings'            => array( 'porto_settings[minicart-icon]', 'porto_settings[minicart-text]', 'porto_settings[minicart-content]' ),
				'render_callback'     => function() {
					return porto_minicart();
				},
			)
		);
		$wp_customize->selective_refresh->add_partial(
			'sticky-nav-bar',
			array(
				'selector'            => '.porto-sticky-navbar',
				'container_inclusive' => true,
				'settings'            => array( 'porto_settings[show-icon-menus-mobile]' ),
				'render_callback'     => function() {
					get_template_part( 'footer/sticky-bottom' );
				},
			)
		);
	endif;

	// Refresh custom styling / Colors etc.
	$wp_customize->selective_refresh->add_partial(
		'refresh_css_header',
		array(
			'selector'            => 'head > style#porto-style-inline-css',
			'container_inclusive' => false,
			'settings'            => array( 'porto_settings[header-link-color]', 'porto_settings[header-top-border]', 'porto_settings[sticky-header-bg]', 'porto_settings[sticky-header-bg-gcolor]', 'porto_settings[sticky-header-opacity]', 'porto_settings[mainmenu-wrap-bg-color-sticky]', 'porto_settings[mainmenu-bg-color]', 'porto_settings[mainmenu-toplevel-link-color]', 'porto_settings[mainmenu-toplevel-hbg-color]', 'porto_settings[mainmenu-toplevel-padding1]', 'porto_settings[mainmenu-toplevel-padding2]', 'porto_settings[mainmenu-toplevel-padding3]', 'porto_settings[mainmenu-popup-top-border]', 'porto_settings[mainmenu-popup-bg-color]', 'porto_settings[mainmenu-popup-text-color]', 'porto_settings[mainmenu-popup-text-hbg-color]', 'porto_settings[mainmenu-toplevel-hbg-color]', 'porto_settings[breadcrumbs-bg]', 'porto_settings[footer-ribbon-bg-color]', 'porto_settings[switcher-hbg-color]', 'porto_settings[searchform-bg-color]', 'porto_settings[searchform-text-color]', 'porto_settings[header-type-select]', 'porto_settings[header-type]', 'porto_settings[search-border-radius]', 'porto_settings[breadcrumbs-type]', 'porto_settings[woo-account-login-style]', 'porto_settings[body-bg-gradient]', 'porto_settings[header-wrap-bg-gradient]', 'porto_settings[header-bg-gradient]', 'porto_settings[content-bg-gradient]', 'porto_settings[content-bottom-bg-gradient]', 'porto_settings[breadcrumbs-bg-gradient]', 'porto_settings[footer-bg-gradient]', 'porto_settings[footer-main-bg-gradient]', 'porto_settings[footer-top-bg-gradient]', 'porto_settings[footer-bottom-bg-gradient]', 'porto_settings[minicart-type]', 'porto_settings[search-layout]', 'porto_settings[menu-type]', 'porto_settings[product-attr-desc]', 'porto_settings[submenu-arrow]', 'porto_settings[form-ih]', 'porto_settings[form-fs]', 'porto_settings[form-color]', 'porto_settings[form-field-bgc]', 'porto_settings[form-field-bw]', 'porto_settings[form-field-bc]', 'porto_settings[form-field-bcf]', 'porto_settings[form-br]', 'porto_settings[sidebar-bw]', 'porto_settings[sidebar-bc]', 'porto_settings[sidebar-pd]', 'porto_settings[account-menu-font]', 'porto_settings[css-type]', 'porto_settings[color-dark]', 'porto_settings[thumb-padding]', 'porto_settings[button-style]', 'porto_settings[skin-color]', 'porto_settings[skin-color-inverse]', 'porto_settings[secondary-color]', 'porto_settings[secondary-color-inverse]', 'porto_settings[tertiary-color]', 'porto_settings[tertiary-color-inverse]', 'porto_settings[quaternary-color]', 'porto_settings[quaternary-color-inverse]', 'porto_settings[dark-color]', 'porto_settings[dark-color-inverse]', 'porto_settings[light-color]', 'porto_settings[light-color-inverse]', 'porto_settings[body-font]', 'porto_settings[shortcode-testimonial-font]', 'porto_settings[menu-side-font]', 'porto_settings[social-color]' ),
			'render_callback'     => function() {
				global $porto_dynamic_style;
				if ( $porto_dynamic_style ) {
					return $porto_dynamic_style->output_dynamic_styles( true );
				}
			},
		)
	);

}
add_action( 'customize_register', 'porto_customizer_refresh_partials' );
