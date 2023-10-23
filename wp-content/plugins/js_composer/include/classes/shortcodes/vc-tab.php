<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

define( 'TAB_TITLE', esc_attr__( 'Tab', 'js_composer' ) );
require_once vc_path_dir( 'SHORTCODES_DIR', 'vc-column.php' );

/**
 * Class WPBakeryShortCode_Vc_Tab
 */
class WPBakeryShortCode_Vc_Tab extends WPBakeryShortCode_Vc_Column {
	protected $controls_css_settings = 'tc vc_control-container';
	protected $controls_list = array(
		'add',
		'edit',
		'clone',
		'copy',
		'delete',
	);
	protected $controls_template_file = 'editors/partials/backend_controls_tab.tpl.php';

	/**
	 * @return string
	 */
	public function customAdminBlockParams() {
		return ' id="tab-' . $this->atts['tab_id'] . '"';
	}

	/**
	 * @param $width
	 * @param $i
	 * @return string
	 * @throws \Exception
	 */
	public function mainHtmlBlockParams( $width, $i ) {
		$sortable = ( vc_user_access_check_shortcode_all( $this->shortcode ) ? 'wpb_sortable' : $this->nonDraggableClass );

		return 'data-element_type="' . $this->settings['base'] . '" class="wpb_' . $this->settings['base'] . ' ' . $sortable . ' wpb_content_holder"' . $this->customAdminBlockParams();
	}

	/**
	 * @param $width
	 * @param $i
	 * @return string
	 */
	public function containerHtmlBlockParams( $width, $i ) {
		return 'class="wpb_column_container vc_container_for_children"';
	}

	/**
	 * @param $controls
	 * @param string $extended_css
	 * @return string
	 * @throws \Exception
	 */
	public function getColumnControls( $controls, $extended_css = '' ) {
		return $this->getColumnControlsModular( $extended_css );
	}
}

/**
 * @param $settings
 * @param $value
 *
 * @return string
 * @since 4.4
 */
function vc_tab_id_settings_field( $settings, $value ) {
	return sprintf( '<div class="vc_tab_id_block"><input name="%s" class="wpb_vc_param_value wpb-textinput %s %s_field" type="hidden" value="%s" /><label>%s</label></div>', $settings['param_name'], $settings['param_name'], $settings['type'], $value, $value );
}

vc_add_shortcode_param( 'tab_id', 'vc_tab_id_settings_field' );
