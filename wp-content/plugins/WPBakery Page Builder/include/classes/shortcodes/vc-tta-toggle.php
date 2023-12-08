<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

/**
 * WPBakery Page Builder shortcodes
 *
 * @package WPBakeryPageBuilder
 * @since 7.0
 */

VcShortcodeAutoloader::getInstance()->includeClass( 'WPBakeryShortCode_Vc_Tta_Pageable' );

/**
 * Class WPBakeryShortCode_Vc_Tta_Pageable
 * @since 7.0
 */
class WPBakeryShortCode_Vc_Tta_Toggle extends WPBakeryShortCode_Vc_Tta_Pageable {

	/**
	 * Unique toggle id
	 * @var string
	 * @since 7.0
	 */
	public $toggle_id;

	/**
	 * Editor controls list
	 * @var string
	 * @since 7.0
	 */
	protected $controls_list = [
		'edit',
		'clone',
		'copy',
		'delete',
	];

	/**
	 * Template file name
	 * @return string
	 * @since 7.0
	 */
	public function getFileName() {
		return 'vc_tta_toggle';
	}

	/**
	 * Toggle is on top only if tabs are at bottom
	 * @since 7.0
	 *
	 * @param array $atts
	 *
	 * @return string|null
	 */
	public function getParamToggleTop( $atts ) {
		if ( empty( $atts['tab_position'] ) || 'bottom' !== $atts['tab_position'] ) {
			return null;
		}

		return $this->getParamToggle();
	}

	/**
	 * Toggle is at bottom only if tabs are on top
	 * @since 7.0
	 *
	 * @param array $atts
	 *
	 * @return string|null
	 */
	public function getParamToggleBottom( $atts ) {
		if ( empty( $atts['tab_position'] ) || 'top' !== $atts['tab_position'] ) {
			return null;
		}

		return $this->getParamToggle();
	}

	/**
	 * Get toggle html
	 * @since 7.0
	 *
	 * @return string
	 */
	public function getParamToggle() {
		VcShortcodeAutoloader::getInstance()->includeClass( 'WPBakeryShortCode_Vc_Tta_Toggle_Section' );
		$section_info = WPBakeryShortCode_Vc_Tta_Toggle_Section::$section_info;
		$title_before = '';
		$title_after = '';

		if ( [] === $section_info ) {
			$title_before = esc_html__( 'Monthly', 'js_composer' );
			$title_after = esc_html__( 'Yearly', 'js_composer' );
		}
		if ( ! empty( $section_info[0]['title'] ) ) {
			$title_before = esc_html( $section_info[0]['title'] );
		}
		if ( ! empty( $section_info[1]['title'] ) ) {
			$title_after = esc_html( $section_info[1]['title'] );
		}

		$html = '<div id="' . esc_attr( $this->toggle_id ) . '" class="wpb-tta-toggle-wrapper">';
		$html .= '<span class="wpb-tta-toggle-title">' . $title_before . '</span>';
		$html .= '<button class="wpb-tta-toggle"></button>';
		$html .= '<span class="wpb-tta-toggle-title">' . $title_after . '</span>';
		$html .= '</div>';

		return $html;
	}

	/**
	 * Add wrapper class related to toggle shortcode.
	 * @since 7.0
	 *
	 * @return string
	 */
	public function getTtaContainerClasses() {
		$classes = array();
		$classes[] = 'vc_tta-container';
		$classes[] = 'wpb-wrapper-tta-toggle';

		$position = isset( $this->atts['tab_position'] ) ? $this->atts['tab_position'] : 'top';
		$classes[] = 'wpb-toggle-position-' . $position;

		return implode( ' ', apply_filters( 'vc_tta_container_classes', array_filter( $classes ), $this->getAtts() ) );
	}

	/**
	 * Get element styles classes attribute.
	 * @since 7.0
	 *
	 * @param array $atts
	 *
	 * @return string
	 */
	public function getTtaToggleStyle( $atts ) {
		$color = empty( $atts['color'] ) ? '#5188F1' : $atts['color'];
		$hover_color = empty( $atts['hover_color'] ) ? '#898989' : esc_attr( $atts['hover_color'] );

		$style = '<style>';
		$style .=
			'#' . esc_attr( $this->toggle_id ) .
			' .wpb-tta-toggle {background: ' .
			esc_attr( $color )
			. '}';
		$style .=
			'#' . esc_attr( $this->toggle_id ) .
			' .wpb-tta-toggle.wpb-tta-toggle-active {background: ' .
			esc_attr( $hover_color ) .
			'}';

		$style .= '</style>';

		return $style;
	}

	/**
	 * Get pagination
	 * @since 7.0
	 *
	 * @param array $atts
	 * @param string $content
	 * @return string|null
	 */
	public function getParamPaginationList( $atts, $content ) {
		if ( empty( $atts['pagination_style'] ) ) {
			return null;
		}
		$isPageEditable = vc_is_page_editable();

		$html = array();
		$html[] = '<ul class="' . $this->getTtaPaginationClasses() . '">';

		if ( ! $isPageEditable ) {
			VcShortcodeAutoloader::getInstance()->includeClass( 'WPBakeryShortCode_Vc_Tta_Toggle_Section' );
			foreach ( WPBakeryShortCode_Vc_Tta_Toggle_Section::$section_info as $nth => $section ) {
				$active_section = $this->getActiveSection( $atts, false );

				$classes = array( 'vc_pagination-item' );
				if ( ( $nth + 1 ) === $active_section ) {
					$classes[] = $this->activeClass;
				}

				$a_html = '<a href="#' . $section['tab_id'] . '" class="vc_pagination-trigger" data-vc-tabs data-vc-container=".vc_tta"></a>';
				$html[] = '<li class="' . implode( ' ', $classes ) . '" data-vc-tab>' . $a_html . '</li>';
			}
		}

		$html[] = '</ul>';

		return implode( '', $html );
	}

	/**
	 * Set global section info
	 * @since 7.0
	 *
	 * @return bool
	 * @throws Exception
	 */
	public function setGlobalTtaInfo() {
		$this->toggle_id = uniqid( 'vc-tta-toggle-' );
		$sectionClass = wpbakery()->getShortCode( 'vc_tta_section' )->shortcodeClass();
		$this->sectionClass = $sectionClass;

		/** @var WPBakeryShortCode_Vc_Tta_Toggle_Section $sectionClass */
		if ( is_object( $sectionClass ) ) {
			VcShortcodeAutoloader::getInstance()->includeClass( 'WPBakeryShortCode_Vc_Tta_Toggle_Section' );
			WPBakeryShortCode_Vc_Tta_Toggle_Section::$tta_base_shortcode = $this;
			WPBakeryShortCode_Vc_Tta_Toggle_Section::$self_count = 0;
			WPBakeryShortCode_Vc_Tta_Toggle_Section::$section_info = array();

			return true;
		}

		return false;
	}

	/**
	 * Get active section
	 * @since 7.0
	 *
	 * @param array $atts
	 * @param bool $strict_bounds
	 * @return int
	 */
	public function getActiveSection( $atts, $strict_bounds = false ) {
		$active_section = intval( $atts['active_section'] );

		if ( $strict_bounds ) {
			VcShortcodeAutoloader::getInstance()->includeClass( 'WPBakeryShortCode_Vc_Tta_Toggle_Section' );
			if ( $active_section < 1 ) {
				$active_section = 1;
			} elseif ( $active_section > WPBakeryShortCode_Vc_Tta_Toggle_Section::$self_count ) {
				$active_section = WPBakeryShortCode_Vc_Tta_Toggle_Section::$self_count;
			}
		}

		return $active_section;
	}
}
