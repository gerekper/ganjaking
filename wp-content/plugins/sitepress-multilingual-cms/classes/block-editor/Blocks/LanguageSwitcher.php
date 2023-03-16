<?php

namespace WPML\BlockEditor\Blocks;

use WPML\BlockEditor\Loader;
use WPML\Element\API\Languages;
use WPML\FP\Lst;
use WPML\FP\Obj;
use WPML\FP\Relation;
use WPML\LIB\WP\Hooks;
use function WPML\Container\make;
use function WPML\FP\spreadArgs;
use WPML\BlockEditor\Blocks\LanguageSwitcher\Render;

class LanguageSwitcher {

	const BLOCK_LANGUAGE_SWITCHER = 'wpml/language-switcher';
	const BLOCK_NAVIGATION_LANGUAGE_SWITCHER = 'wpml/navigation-language-switcher';

	/** @var Render */
	private $render;

	/**
	 * @param Render $render
	 */
	public function __construct( Render $render ) {
		$this->render = $render;
	}

	public function register() {

		$this->registerLanguageSwitcherBlock();
		$this->registerNavigationLanguageSwitcherBlock();

		Hooks::onAction( 'enqueue_block_editor_assets' )
		     ->then( [ $this, 'registerLanguageSwitcherAssets' ] );
	}

	private function registerLanguageSwitcherBlock() {
		$blockSettings = [
			'render_callback' => [ $this->render, 'render_block' ],
		];

		register_block_type( self::BLOCK_LANGUAGE_SWITCHER, $blockSettings );
	}

	private function registerNavigationLanguageSwitcherBlock() {
		$blockSettings = [
			'render_callback' => [ $this->render, 'render_block' ],
			'attributes'      => [
				'navigationLsHasSubMenuInSameBlock' => [
					'type'    => 'boolean',
					'default' => false,
				],
				'layoutOpenOnClick' => [
					'type'    => 'boolean',
					'default' => false,
				],
				'layoutShowArrow' => [
					'type'    => 'boolean',
					'default' => true,
				],
			],
			'uses_context'    => [
				'layout',
				'showSubmenuIcon',
				'openSubmenusOnClick',
				'style',
				'textColor',
				'customTextColor',
				'backgroundColor',
				'customBackgroundColor',
				'overlayTextColor',
				'customOverlayTextColor',
				'overlayBackgroundColor',
				'customOverlayBackgroundColor',
				'fontSize',
				'customFontSize'
			],
		];

		register_block_type( self::BLOCK_NAVIGATION_LANGUAGE_SWITCHER, $blockSettings );
	}

	public function render() {
		/** @var \WPML_LS_Dependencies_Factory $lsFactory */
		$lsFactory    = make( \WPML_LS_Dependencies_Factory::class );
		$shortcodeAPI = $lsFactory->shortcodes();

		return $shortcodeAPI->callback( [] );
	}

	public function registerLanguageSwitcherAssets() {
		$this->registerLanguageSwitcherGlobalData();
	}

	private function registerLanguageSwitcherGlobalData() {
		$languages = Obj::values( Languages::withFlags( Languages::getActive() ) );
		$activeLanguage = Lst::find( Relation::propEq( 'code', Languages::getCurrentCode() ), $languages );
		$data      = [
			'languages'      => $languages,
			'activeLanguage' => $activeLanguage,
			'isRtl'			 => Languages::isRtl( strval( Obj::prop( 'code', $activeLanguage ) ) ),
		];
		wp_localize_script( Loader::SCRIPT_NAME, 'WPML_LS_SETTINGS', $data );
	}

}
