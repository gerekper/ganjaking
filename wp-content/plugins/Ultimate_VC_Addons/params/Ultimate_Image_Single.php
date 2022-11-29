<?php
/**
 * -----------------------------------------------------------------------------------------
 * # HOW TO USE
 * -----------------------------------------------------------------------------------------
 * 1]  DEFINE STRUCTURE?
 *
 * - Define below structure in module which you want.
 *
 * e.g.  array(
 *        "type" => "ult_img_single",
 *        "heading" => "Upload Image",
 *        "param_name" => "icon_image",
 *        "description" => __("description for image single.", "ultimate_vc"),
 *  ),
 * -----------------------------------------------------------------------------------------
 * 2]  USE FILTER?
 *
 * - Return url, array or json.
 *
 * e.g.  apply_filters('ult_get_img_single', $PARAM_NAME, 'url', 'size');    // {size} [optional] - thumbnail, full, * medium etc. - default: full
 *
 *  apply_filters('ult_get_img_single', $PARAM_NAME, 'array');
 *  apply_filters('ult_get_img_single', $PARAM_NAME, 'json');
 *
 * -----------------------------------------------------------------------------------------
 * 3]  OUTPUT
 *
 * - Output of two image uploader fields.
 *
 * http://i.imgur.com/csfJvKV.png
 * -----------------------------------------------------------------------------------------
 *
 * @package Ultimate_VC_Addons_Image_Single.
 */

if ( ! class_exists( 'Ultimate_VC_Addons_Image_Single' ) ) {
	/**
	 * Class Ultimate_VC_Addons_Image_Single
	 *
	 * @class Ultimate_VC_Addons_Image_Single.
	 */
	class Ultimate_VC_Addons_Image_Single {

		/**
		 * Initiator __construct.
		 */
		public function __construct() {
			add_action( 'admin_enqueue_scripts', array( $this, 'image_single_scripts' ) );

			if ( defined( 'WPB_VC_VERSION' ) && version_compare( WPB_VC_VERSION, 4.8 ) >= 0 ) {
				if ( function_exists( 'vc_add_shortcode_param' ) ) {
					vc_add_shortcode_param( 'ult_img_single', array( $this, 'ult_img_single_callback' ), UAVC_URL . 'admin/vc_extend/js/ultimate-image_single.js' );
				}
			} else {
				if ( function_exists( 'add_shortcode_param' ) ) {
					add_shortcode_param( 'ult_img_single', array( $this, 'ult_img_single_callback' ), UAVC_URL . 'admin/vc_extend/js/ultimate-image_single.js' );
				}
			}

			add_action( 'wp_ajax_ult_get_attachment_url', array( $this, 'get_attachment_url_init' ) );
		}
		/**
		 * Get_attachment_url_init.
		 */
		public function get_attachment_url_init() {

			if ( ! current_user_can( 'manage_options' ) ) {
				return false;
			}

			check_ajax_referer( 'uavc-get-attachment-url-nonce', 'security' );

			$id    = intval( $_POST['attach_id'] );
			$thumb = wp_get_attachment_image_src( $id, 'thumbnail' );
			echo esc_url( reset( $thumb ) );

			die();
		}
		/**
		 * Ult_img_single_callback.
		 *
		 * @param array  $settings Settings.
		 * @param string $value Value.
		 */
		public function ult_img_single_callback( $settings, $value ) {
			$dependency = '';

			$uid = 'ult-image_single-' . wp_rand( 1000, 9999 );

			$html = '<div class="ult-image_single" id="' . esc_attr( $uid ) . '">';

			$html .= '<div class="ult_selected_image">';
			$html .= '  <ul class="ult_selected_image_list">';
			$html .= '    <li class="">';
			$html .= '      <div class="inner" style="width: 75px; height: 75px; overflow: hidden;text-align: center;">';
			$html .= '        <div class="spinner ult_img_single_spinner"></div>';
			$html .= '        <img src="">';
			$html .= '      </div>';
			$html .= '      <a title="Remove Footer Image" href="javascript:;" id="remove-thumbnail" class="icon-remove"></a>';
			$html .= '    </li>';
			$html .= '  </ul>';
			$html .= '</div>';
			$html .= '<a class="ult_add_image" href="#" title="Add image">Add image</a>';

			$html .= '  <input type="hidden" name="' . esc_attr( $settings['param_name'] ) . '" class="wpb_vc_param_value ult-image_single-value ' . esc_attr( $settings['param_name'] ) . ' ' . esc_attr( $settings['type'] ) . '_field" value="' . esc_attr( $value ) . '" ' . $dependency . ' />';
			$html .= '</div>';
			return $html;
		}
		/**
		 * Image_single_scripts.
		 */
		public function image_single_scripts() {
			wp_enqueue_media();
			wp_enqueue_style( 'ultimate-vc-addons_image_single_css', UAVC_URL . 'admin/vc_extend/css/ultimate_image_single.css', null, ULTIMATE_VERSION );
		}
	}
}



if ( class_exists( 'Ultimate_VC_Addons_Image_Single' ) ) {
	$ult_image_single = new Ultimate_VC_Addons_Image_Single();
}
