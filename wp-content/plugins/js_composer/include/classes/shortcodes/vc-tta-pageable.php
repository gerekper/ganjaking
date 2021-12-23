<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

VcShortcodeAutoloader::getInstance()->includeClass( 'WPBakeryShortCode_Vc_Tta_Tabs' );

/**
 * Class WPBakeryShortCode_Vc_Tta_Pageable
 */
class WPBakeryShortCode_Vc_Tta_Pageable extends WPBakeryShortCode_Vc_Tta_Tabs {

	public $layout = 'tabs';

	/**
	 * @return string
	 */
	public function getTtaContainerClasses() {
		$classes = parent::getTtaContainerClasses();

		$classes .= ' vc_tta-o-non-responsive';

		return $classes;
	}

	/**
	 * @return mixed|string
	 */
	public function getTtaGeneralClasses() {
		$classes = parent::getTtaGeneralClasses();

		$classes .= ' vc_tta-pageable';

		// tabs have pagination on opposite side of tabs. pageable should behave normally
		if ( false !== strpos( $classes, 'vc_tta-tabs-position-top' ) ) {
			$classes = str_replace( 'vc_tta-tabs-position-top', 'vc_tta-tabs-position-bottom', $classes );
		} else {
			$classes = str_replace( 'vc_tta-tabs-position-bottom', 'vc_tta-tabs-position-top', $classes );

		}

		return $classes;
	}

	/**
	 * Disable all tabs
	 *
	 * @param $atts
	 * @param $content
	 *
	 * @return string
	 */
	public function getParamTabsList( $atts, $content ) {
		return '';
	}
}
