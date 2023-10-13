<?php

namespace WCML\AdminNotices;

use WPML\FP\Fns;
use WPML\FP\Logic;
use WPML\FP\Obj;
use WPML\FP\Str;
use function WPML\FP\pipe;

class CachePlugins implements \IWPML_Backend_Action, \IWPML_DIC_Action {

	const NOTICE_ID = 'wcml-cache-plugins';

	/** @var \WPML_Notices $notices */
	private $notices;

	public function __construct( \WPML_Notices $notices ) {
		$this->notices = $notices;
	}

	public function add_hooks() {
		if (
			wcml_is_multi_currency_on()
			&& ! $this->notices->get_notice( self::NOTICE_ID )
			&& self::hasActiveCachePlugin()
		) {
			add_action( 'admin_init', [ $this, 'addNotice' ] );
		}
	}

	public function addNotice() {
		$text  = '<h2>' . __( 'WooCommerce Multilingual & Multicurrency detected an active cache plugin on your site.', 'woocommerce-multilingual' ) . '</h2>';
		$text .= '<p>' . __( 'Caching may cause currency display issues for your customers if you are using the multicurrency feature.', 'woocommerce-multilingual' ) . '</p>';
		$text .= '<p>' . __( 'To avoid this, set your cache plugin to not cache pages for visitors that have a cookie set in their browser.', 'woocommerce-multilingual' ) . '</p>';

		$notice = $this->notices->create_notice( self::NOTICE_ID, $text );
		$notice->set_css_class_types( 'notice-warning' );
		$notice->set_restrict_to_screen_ids( RestrictedScreens::get() );
		$notice->set_dismissible( true );

		$this->notices->add_notice( $notice );
	}

	/**
	 * @return bool
	 */
	private static function hasActiveCachePlugin() {
		if ( ! function_exists( 'is_plugin_active' ) ) {
			include_once ABSPATH . 'wp-admin/includes/plugin.php';
		}

		// $isActive :: ( array, string ) -> bool
		$isActive = pipe(
			Fns::nthArg( 1 ), // array index.
			'is_plugin_active'
		);

		// $isAboutCaching :: array -> bool
		$isAboutCaching = pipe(
			Obj::prop( 'Description' ),
			Logic::anyPass(
				[
					Str::includes( 'cache' ),
					Str::includes( 'caching' ),
				]
			)
		);

		// $isHandled :: array -> bool
		$isHandled = function( $plugin ) {
			return in_array(
				$plugin['Name'],
				apply_filters( 'wcml_multicurrency_supported_cache_plugins', [] ),
				true
			);
		};

		return (bool) wpml_collect( get_plugins() )
			->filter( $isActive )
			->filter( $isAboutCaching )
			->reject( $isHandled )
			->first();
	}
}
