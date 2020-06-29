<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

/**
 * Class WPBakeryShortCodesContainer
 */
abstract class WPBakeryShortCodesContainer extends WPBakeryShortCode {
	/**
	 * @var array
	 */
	protected $predefined_atts = array();
	protected $backened_editor_prepend_controls = true;

	/**
	 * @return string
	 */
	public function customAdminBlockParams() {
		return '';
	}

	/**
	 * @param $width
	 * @param $i
	 *
	 * @return string
	 * @throws \Exception
	 */
	public function mainHtmlBlockParams( $width, $i ) {
		$sortable = ( vc_user_access_check_shortcode_all( $this->shortcode ) ? 'wpb_sortable' : $this->nonDraggableClass );

		return 'data-element_type="' . esc_attr( $this->settings['base'] ) . '" class="wpb_' . esc_attr( $this->settings['base'] ) . ' ' . esc_attr( $sortable ) . '' . ( ! empty( $this->settings['class'] ) ? ' ' . esc_attr( $this->settings['class'] ) : '' ) . ' wpb_content_holder vc_shortcodes_container"' . $this->customAdminBlockParams();
	}

	/**
	 * @param $width
	 * @param $i
	 *
	 * @return string
	 */
	public function containerHtmlBlockParams( $width, $i ) {
		return 'class="' . $this->containerContentClass() . '"';
	}

	/**
	 *
	 * @return string
	 */
	public function containerContentClass() {
		return 'wpb_column_container vc_container_for_children vc_clearfix';
	}/** @noinspection PhpMissingParentCallCommonInspection */

	/**
	 * @param string $controls
	 * @param string $extended_css
	 *
	 * @return string
	 * @throws \Exception
	 */
	public function getColumnControls( $controls = 'full', $extended_css = '' ) {
		$controls_html = array();

		$controls_html['start'] = '<div class="vc_controls vc_controls-visible controls_column' . ( ! empty( $extended_css ) ? " {$extended_css}" : '' ) . '">';
		$controls_html['end'] = '</div>';

		if ( 'bottom-controls' === $extended_css ) {
			$controls_html['title'] = sprintf( esc_attr__( 'Append to this %s', 'js_composer' ), strtolower( $this->settings( 'name' ) ) );
		} else {
			$controls_html['title'] = sprintf( esc_attr__( 'Prepend to this %s', 'js_composer' ), strtolower( $this->settings( 'name' ) ) );
		}

		$controls_html['move'] = '<a class="vc_control column_move vc_column-move" data-vc-control="move" href="#" title="' . sprintf( esc_attr__( 'Move this %s', 'js_composer' ), strtolower( $this->settings( 'name' ) ) ) . '"><i class="vc-composer-icon vc-c-icon-dragndrop"></i></a>';
		$moveAccess = vc_user_access()->part( 'dragndrop' )->checkStateAny( true, null )->get();
		if ( ! $moveAccess ) {
			$controls_html['move'] = '';
		}
		$controls_html['add'] = '<a class="vc_control column_add" data-vc-control="add" href="#" title="' . $controls_html['title'] . '"><i class="vc-composer-icon vc-c-icon-add"></i></a>';
		$controls_html['edit'] = '<a class="vc_control column_edit" data-vc-control="edit" href="#" title="' . sprintf( esc_html__( 'Edit this %s', 'js_composer' ), strtolower( $this->settings( 'name' ) ) ) . '"><i class="vc-composer-icon vc-c-icon-mode_edit"></i></a>';
		$controls_html['clone'] = '<a class="vc_control column_clone" data-vc-control="clone" href="#" title="' . sprintf( esc_html__( 'Clone this %s', 'js_composer' ), strtolower( $this->settings( 'name' ) ) ) . '"><i class="vc-composer-icon vc-c-icon-content_copy"></i></a>';
		$controls_html['delete'] = '<a class="vc_control column_delete" data-vc-control="delete" href="#" title="' . sprintf( esc_html__( 'Delete this %s', 'js_composer' ), strtolower( $this->settings( 'name' ) ) ) . '"><i class="vc-composer-icon vc-c-icon-delete_empty"></i></a>';
		$controls_html['full'] = $controls_html['move'] . $controls_html['add'] . $controls_html['edit'] . $controls_html['clone'] . $controls_html['delete'];

		$editAccess = vc_user_access_check_shortcode_edit( $this->shortcode );
		$allAccess = vc_user_access_check_shortcode_all( $this->shortcode );

		if ( ! empty( $controls ) ) {
			if ( is_string( $controls ) ) {
				$controls = array( $controls );
			}
			$controls_string = $controls_html['start'];
			foreach ( $controls as $control ) {
				if ( ( $editAccess && 'edit' === $control ) || $allAccess ) {
					if ( isset( $controls_html[ $control ] ) ) {
						$controls_string .= $controls_html[ $control ];
					}
				}
			}

			return $controls_string . $controls_html['end'];
		}

		if ( $allAccess ) {
			return $controls_html['start'] . $controls_html['full'] . $controls_html['end'];
		} elseif ( $editAccess ) {
			return $controls_html['start'] . $controls_html['edit'] . $controls_html['end'];
		}

		return $controls_html['start'] . $controls_html['end'];
	}

	/**
	 * @param $atts
	 * @param null $content
	 *
	 * @return string
	 * @throws \Exception
	 */
	public function contentAdmin( $atts, $content = null ) {
		$width = '';

		$atts = shortcode_atts( $this->predefined_atts, $atts );
		extract( $atts );
		$this->atts = $atts;
		$output = '';

		$output .= '<div ' . $this->mainHtmlBlockParams( $width, 1 ) . '>';
		if ( $this->backened_editor_prepend_controls ) {
			$output .= $this->getColumnControls( $this->settings( 'controls' ) );
		}
		$output .= '<div class="wpb_element_wrapper">';

		if ( isset( $this->settings['custom_markup'] ) && '' !== $this->settings['custom_markup'] ) {
			$markup = $this->settings['custom_markup'];
			$output .= $this->customMarkup( $markup );
		} else {
			$output .= $this->outputTitle( $this->settings['name'] );
			$output .= '<div ' . $this->containerHtmlBlockParams( $width, 1 ) . '>';
			$output .= do_shortcode( shortcode_unautop( $content ) );
			$output .= '</div>';
			$output .= $this->paramsHtmlHolders( $atts );
		}

		$output .= '</div>';
		if ( $this->backened_editor_prepend_controls ) {
			$output .= $this->getColumnControls( 'add', 'bottom-controls' );

		}
		$output .= '</div>';

		return $output;
	}

	/**
	 * @param $title
	 *
	 * @return string
	 */
	protected function outputTitle( $title ) {
		$icon = $this->settings( 'icon' );
		if ( filter_var( $icon, FILTER_VALIDATE_URL ) ) {
			$icon = '';
		}
		$params = array(
			'icon' => $icon,
			'is_container' => $this->settings( 'is_container' ),
			'title' => $title,
		);

		return '<h4 class="wpb_element_title"> ' . $this->getIcon( $params ) . '</h4>';
	}

	/**
	 * @return string
	 */
	/**
	 * @return string
	 */
	public function getBackendEditorChildControlsElementCssClass() {
		return 'vc_element-name';
	}
}
