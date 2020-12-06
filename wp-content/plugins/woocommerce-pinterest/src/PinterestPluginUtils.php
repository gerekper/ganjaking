<?php namespace Premmerce\WooCommercePinterest;

final class PinterestPluginUtils {
	
	public static function isYoastWooCommerceActive() {
		return class_exists('Yoast_WooCommerce_SEO');
	}

	public static function isYoastActive() {
		return defined( 'WPSEO_FILE' );
	}

}
