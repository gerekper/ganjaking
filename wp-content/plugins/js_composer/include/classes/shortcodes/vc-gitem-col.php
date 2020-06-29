<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

require_once vc_path_dir( 'SHORTCODES_DIR', 'vc-column.php' );

/**
 * Class WPBakeryShortCode_Vc_Gitem_Col
 */
class WPBakeryShortCode_Vc_Gitem_Col extends WPBakeryShortCode_Vc_Column {
	public $nonDraggableClass = 'vc-non-draggable-column';

	/**
	 * @param $width
	 * @param $i
	 * @return string
	 * @throws \Exception
	 */
	public function mainHtmlBlockParams( $width, $i ) {
		$sortable = ( vc_user_access_check_shortcode_all( $this->shortcode ) ? ' wpb_sortable ' : ' ' . $this->nonDraggableClass . ' ' );

		return 'data-element_type="' . $this->settings['base'] . '" data-vc-column-width="' . wpb_vc_get_column_width_indent( $width[ $i ] ) . '" class="wpb_vc_column wpb_' . $this->settings['base'] . $sortable . $this->templateWidth() . ' wpb_content_holder"' . $this->customAdminBlockParams();
	}

	/**
	 * @return string
	 */
	public function outputEditorControlAlign() {
		$alignment = array(
			array(
				'name' => 'left',
				'label' => esc_html__( 'Left', 'js_composer' ),
			),
			array(
				'name' => 'center',
				'label' => esc_html__( 'Center', 'js_composer' ),
			),
			array(
				'name' => 'right',
				'label' => esc_html__( 'Right', 'js_composer' ),
			),
		);
		$output = '<span class="vc_control vc_control-align"><span class="vc_control-wrap">';
		foreach ( $alignment as $data ) {
			$attr = esc_attr( $data['name'] );
			$output .= sprintf( '<a href="#" data-vc-control-btn="align" data-vc-align="%s" class="vc_control vc_control-align-%s" title="%s"><i class="vc_icon vc_icon-align-%s"></i></a>', esc_attr( $attr ), $attr, esc_html( $data['label'] ), $attr );
		}

		return $output . '</span></span>';
	}
}
