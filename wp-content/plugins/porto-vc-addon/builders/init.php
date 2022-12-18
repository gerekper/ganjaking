<?php

// don't load directly
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

/**
 * Porto builders library
 *
 * @since 2.3.0
 */

class Porto_VC_Builders {

	/**
	 * Constructor
	 */
	public function __construct() {
		// enable post types
		if ( is_admin() && current_user_can( PortoBuilders::BUILDER_CAP ) ) {
			$support_types = get_option( 'vcv-post-types', array() );
			if ( ! in_array( PortoBuilders::BUILDER_SLUG, $support_types ) ) {
				$support_types[] = PortoBuilders::BUILDER_SLUG;
				update_option( 'vcv-post-types', $support_types );
			}
		}

		if ( is_admin() ) {
			add_action(
				'vcv:api',
				function( $api ) {
					$builder_type = '';
					if ( function_exists( 'porto_is_vc_preview' ) && porto_is_vc_preview() && isset( $_GET['vcv-source-id'] ) ) {
						$builder_type = get_post_meta( (int) $_GET['vcv-source-id'], PortoBuilders::BUILDER_TAXONOMY_SLUG, true );
					} elseif ( is_singular( 'product' ) ) {
						$builder_type = 'product';
					} elseif ( ! empty( $_REQUEST['post'] ) && PortoBuilders::BUILDER_SLUG == get_post_type( $_REQUEST['post'] ) ) {
						$builder_type = get_post_meta( (int) $_REQUEST['post'], PortoBuilders::BUILDER_TAXONOMY_SLUG, true );
					}

					if ( 'header' == $builder_type ) {
						$base_url = PORTO_VC_ADDON_URL . '/builders/header';

						/**
						 * @var \VisualComposer\Modules\Elements\ApiController $elementsApi
						*/
						$elements_api = $api->elements;

						foreach ( PortoBuildersHeader::$elements as $tag ) {
							$tag           = 'portoHeader' . ucfirst( str_replace( '-', '', $tag ) );
							$manifest_path = PORTO_VC_ADDON_PATH . '/builders/header/' . $tag . '/manifest.json';
							$element_url   = $base_url . '/' . $tag;
							$elements_api->add( $manifest_path, $element_url );
						}

						if ( class_exists( 'Woocommerce' ) ) {
							foreach ( PortoBuildersHeader::$woo_elements as $tag ) {
								$tag           = 'portoHeader' . ucfirst( str_replace( '-', '', $tag ) );
								$manifest_path = PORTO_VC_ADDON_PATH . '/builders/header/' . $tag . '/manifest.json';
								$element_url   = $base_url . '/' . $tag;
								$elements_api->add( $manifest_path, $element_url );
							}
						}
					} elseif ( 'product' == $builder_type && class_exists( 'Woocommerce' ) ) {
						$base_url     = PORTO_VC_ADDON_URL . '/builders/product/';
						$elements_api = $api->elements;
						foreach ( PortoCustomProduct::$shortcodes as $shortcode ) {
							$manifest_path = PORTO_VC_ADDON_PATH . '/builders/product/' . $shortcode . '/manifest.json';
							$element_url   = $base_url . $shortcode;
							$elements_api->add( $manifest_path, $element_url );
						}
					} elseif ( 'shop' == $builder_type && class_exists( 'Woocommerce' ) ) {
						$base_url = PORTO_VC_ADDON_URL . '/builders/shop/';

						/**
						 * @var \VisualComposer\Modules\Elements\ApiController $elementsApi
						*/
						$elements_api = $api->elements;

						foreach ( PortoBuildersShop::$elements as $tag ) {
							$tag           = 'portoShop' . ucfirst( str_replace( '-', '', $tag ) );
							$manifest_path = PORTO_VC_ADDON_PATH . '/builders/shop/' . $tag . '/manifest.json';
							$element_url   = $base_url . $tag;
							$elements_api->add( $manifest_path, $element_url );
						}
					}
				}
			);
		}
	}
}

new Porto_VC_Builders;
