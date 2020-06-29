<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

require_once vc_path_dir( 'SHORTCODES_DIR', 'vc-basic-grid.php' );

/**
 * Class WPBakeryShortCode_Vc_Masonry_Grid
 */
class WPBakeryShortCode_Vc_Masonry_Grid extends WPBakeryShortCode_Vc_Basic_Grid {
	/**
	 * @return mixed|string
	 */
	protected function getFileName() {
		return 'vc_basic_grid';
	}

	public function shortcodeScripts() {
		parent::shortcodeScripts();
		wp_register_script( 'vc_masonry', vc_asset_url( 'lib/bower/masonry/dist/masonry.pkgd.min.js' ), array(), WPB_VC_VERSION, true );
	}

	public function enqueueScripts() {
		wp_enqueue_script( 'vc_masonry' );
		parent::enqueueScripts();
	}

	public function buildGridSettings() {
		parent::buildGridSettings();
		$this->grid_settings['style'] .= '-masonry';
	}

	/**
	 * @param $grid_style
	 * @param $settings
	 * @param $content
	 * @return string
	 */
	protected function contentAllMasonry( $grid_style, $settings, $content ) {
		return parent::contentAll( $grid_style, $settings, $content );
	}

	/**
	 * @param $grid_style
	 * @param $settings
	 * @param $content
	 * @return string
	 */
	protected function contentLazyMasonry( $grid_style, $settings, $content ) {
		return parent::contentLazy( $grid_style, $settings, $content );
	}

	/**
	 * @param $grid_style
	 * @param $settings
	 * @param $content
	 * @return string
	 */
	protected function contentLoadMoreMasonry( $grid_style, $settings, $content ) {
		return parent::contentLoadMore( $grid_style, $settings, $content );
	}
}
