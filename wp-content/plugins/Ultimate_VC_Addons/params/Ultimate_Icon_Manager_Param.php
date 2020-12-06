<?php
/**
 * Class Ultimate_Icon_Manager_Param
 *
 * @package Ultimate_Icon_Manager_Param.
 */

if ( ! class_exists( 'Ultimate_Icon_Manager_Param' ) ) {
	/**
	 * Class Ultimate_Icon_Manager_Param
	 *
	 * @class Ultimate_Icon_Manager_Param.
	 */
	class Ultimate_Icon_Manager_Param {
		/**
		 * Initiator __construct.
		 */
		public function __construct() {
			$GLOBALS['pid'] = 0;
				$id         = null;
				$pcnt       = null;
			if ( defined( 'WPB_VC_VERSION' ) && version_compare( WPB_VC_VERSION, 4.8 ) >= 0 ) {
				if ( function_exists( 'vc_add_shortcode_param' ) ) {
					vc_add_shortcode_param( 'icon_manager', array( $this, 'icon_manager' ) );
				}
			} else {
				if ( function_exists( 'add_shortcode_param' ) ) {
					add_shortcode_param( 'icon_manager', array( $this, 'icon_manager' ) );
				}
			}
		}
		/**
		 * Icon_manager.
		 *
		 * @param array  $settings Settings.
		 * @param string $value Value.
		 */
		public function icon_manager( $settings, $value ) {
			$GLOBALS['pid'] = $GLOBALS['pid'] + 1;
			$pcnt           = $GLOBALS['pid'];

			$aio_icon_manager = new AIO_Icon_Manager();
			$font_manager     = $aio_icon_manager->get_font_manager( $pcnt );
			$dependency       = '';

			$params       = wp_parse_url( $_SERVER['HTTP_REFERER'] );
			$vc_is_inline = false;
			if ( isset( $params['query'] ) ) {
				parse_str( $params['query'], $params );
				$vc_is_inline = isset( $params['vc_action'] ) ? true : false;
			}

			$output = '<div class="my_param_block">'
					. '<input name="' . esc_attr( $settings['param_name'] ) . '"
					  class="wpb_txt_icon_value wpb_vc_param_value wpb-textinput ' . esc_attr( $settings['param_name'] ) . ' 
					  ' . esc_attr( $settings['type'] ) . '_field" type="hidden" 
					  value="' . esc_attr( $value ) . '" ' . $dependency . ' id="' . esc_attr( $pcnt ) . '"/>'
					. '</div>';
			if ( $vc_is_inline ) {
				$output .= '<script type="text/javascript">
					var val=jQuery("#' . esc_attr( $pcnt ) . '").val();
					//alert("yes");
					var val=jQuery("#' . esc_attr( $pcnt ) . '").val();
					var pmid="' . esc_attr( $pcnt ) . '";
					var pmid="' . esc_attr( $pcnt ) . '";
					var val=jQuery("#' . esc_attr( $pcnt ) . '").val();
					if(val==""){
							val="none";
						}
						if(val=="icon_color="){
							val="none";
						}

						jQuery(".preview-icon-' . esc_attr( $pcnt ) . '").html("<i class="+val+"></i>");

						jQuery(".icon-list-' . esc_attr( $pcnt ) . ' li[data-icons=\'"+ val+"\']").addClass("selected");

						jQuery(".icons-list li").click(function() {

					var id=jQuery(this).attr("id");
					//alert(id);
                    jQuery(this).attr("class","selected").siblings().removeAttr("class");
                    var icon = jQuery(this).attr("data-icons");

                    jQuery("#"+id).val(icon);
                    jQuery(".preview-icon-"+id).html("<i class=\'"+icon+"\'></i>");
                });

					</script>';
			} else {

				$output .= '<script type="text/javascript">


				jQuery(document).ready(function(){
					var pmid="' . esc_attr( $pcnt ) . '";
					var val=jQuery("#' . esc_attr( $pcnt ) . '").val();
					if(val==""){
						val="none";
					}
					if(val=="icon_color="){
						val="none";
					}

					jQuery(".preview-icon-' . esc_attr( $pcnt ) . '").html("<i class="+val+"></i>");

					jQuery(".icon-list-' . esc_attr( $pcnt ) . ' li[data-icons=\'"+ val+"\']").addClass("selected");
				});
				jQuery(".icons-list li").click(function() {
					var id=jQuery(this).attr("id");
					//alert(id);
                    jQuery(this).attr("class","selected").siblings().removeAttr("class");
                    var icon = jQuery(this).attr("data-icons");

                    jQuery("#"+id).val(icon);
                    jQuery(".preview-icon-"+id).html("<i class=\'"+icon+"\'></i>");
                });
				</script>';
			}
			$output .= '<div class="wpb_txt_icons_block" data-old-icon-value="' . esc_attr( $pcnt ) . '">' . $font_manager . '</div>';
			return $output;
		}

	}
}

if ( class_exists( 'Ultimate_Icon_Manager_Param' ) ) {
	$ultimate_icon_manager_param = new Ultimate_Icon_Manager_Param();
}
