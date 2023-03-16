<?php


namespace WPML\BlockEditor;

use WP_Mock\Hook;
use WPML\BlockEditor\Blocks\LanguageSwitcher;
use WPML\LIB\WP\Hooks;
use WPML\Core\WP\App\Resources;
use function WPML\Container\make;
use function WPML\FP\spreadArgs;

class Loader implements \IWPML_Backend_Action, \IWPML_REST_Action {

	const SCRIPT_NAME = 'wpml-blocks';

	public function add_hooks() {

		Hooks::onAction( 'init' )
			->then( [ $this, 'registerBlocks' ] );

		Hooks::onAction( 'wp_enqueue_scripts' )
		     ->then( [ $this, 'enqueueBlockStyles' ] );

		Hooks::onAction( 'enqueue_block_editor_assets' )
			->then( [ $this, 'enqueueBlockAssets' ] );

		Hooks::onFilter( 'block_categories_all', 10, 2 )
			->then( spreadArgs( [ $this, 'registerCategory' ] ) );
	}

	/**
	 * @param array[] $block_categories
	 * @param \WP_Block_Editor_Context $editor_context
	 * @return mixed
	 */
	public function registerCategory( $block_categories, $editor_context ) {
		if ( ! empty( $editor_context->post ) ) {
			array_push(
				$block_categories,
				[
					'slug'  => 'wpml',
					'title' => __( 'WPML', 'sitepress-multilingual-cms' ),
					'icon'  => null,
				]
			);
		}
		return $block_categories;
	}

	/**
	 * Register blocks that need server side render.
	 */
	public function registerBlocks() {
		make( LanguageSwitcher::class )->register();
	}

	/**
	 * @return void
	 */
	public function enqueueBlockAssets() {
		// Note: this is reused by specific blocks to attach Localized variables to the same Script Handle.
		$this->enqueueBlockScripts();
		$this->enqueueBlockStyles();
	}

	public function enqueueBlockScripts() {
		wp_enqueue_script(
			self::SCRIPT_NAME,
			ICL_PLUGIN_URL . '/dist/js/blocks/app.js',
			[
				'wp-blocks',
				'wp-i18n',
				'wp-element',
				'wp-editor',
			],
			ICL_SITEPRESS_VERSION
		);
	}

	public function enqueueBlockStyles() {
		wp_enqueue_style(
			self::SCRIPT_NAME,
			ICL_PLUGIN_URL . '/dist/css/blocks/styles.css',
			[],
			ICL_SITEPRESS_VERSION
		);
	}
}