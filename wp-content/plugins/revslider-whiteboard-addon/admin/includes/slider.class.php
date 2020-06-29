<?php
/**
 * @author    ThemePunch <info@themepunch.com>
 * @link      https://www.themepunch.com/
 * @copyright 2018 ThemePunch
 */

if(!defined('ABSPATH')) exit();

class rs_whiteboard_slider extends RevSliderFunctions {
	
	public static function init(){
		
		add_filter('revslider_slider_addons', array('rs_whiteboard_slider', 'add_whiteboard_settings'), 10, 1);
		
		if(isset($_GET['page']) && $_GET['page'] == 'revslider'){
			add_action('admin_enqueue_scripts', array('rs_whiteboard_slider', 'wb_enqueue_styles'));
			add_action('admin_enqueue_scripts', array('rs_whiteboard_slider', 'wb_enqueue_scripts'));
		}
	}
	
	public static function wb_enqueue_styles(){
		
		if(!isset($_GET['page']) || !isset($_GET['view'])) return;
		if($_GET['page'] !== 'revslider') return;
		
		wp_register_style('revslider-whiteboard-plugin-settings', WHITEBOARD_PLUGIN_URL . 'admin/assets/css/revslider-whiteboard-addon-admin.css', array(), WHITEBOARD_VERSION);
		wp_enqueue_style('revslider-whiteboard-plugin-settings');
		
	}
		
	public static function wb_enqueue_scripts(){
		
		if(!isset($_GET['page']) || !isset($_GET['view'])) return;
		if($_GET['page'] !== 'revslider') return;
		
		wp_register_script('revslider-whiteboard-plugin-js', WHITEBOARD_PLUGIN_URL . 'admin/assets/js/revslider-whiteboard-addon-admin.js', array( 'jquery','revbuilder-admin' ), WHITEBOARD_VERSION);
		wp_enqueue_script('revslider-whiteboard-plugin-js');
		wp_localize_script( 'revslider-whiteboard-plugin-js', 'revslider_whiteboard_addon', self::whiteboard_get_var() );
		
	}
	
	public static function add_whiteboard_settings($settings){						
		$settings['whiteboard'] = array(
			'title'		 => __('Whiteboard', 'rs_whiteboard'),
			'slug'	     => 'revslider-whiteboard-addon'			
		);		
		return $settings;
	}

	/**
	 * Returns the global JS variable
	 *
	 * @since    2.0.0
	 */
	public static function whiteboard_get_var($var='',$slug='revslider-whiteboard-addon') {
	
		if($slug === 'revslider-whiteboard-addon'){
			
			return array(
				'bricks' => array(
					'writehand' => __('Write Hand','revslider-whiteboard-addon'),
					'movehand' => __('Move Hand','revslider-whiteboard-addon'),
					'type' => __('Type','revslider-whiteboard-addon'),
					'direction' => __('Direction','revslider-whiteboard-addon'),
					'jitteringdistance' => __('Jittering Area Height','revslider-whiteboard-addon'),
					'jitteringoffset' => __('Jittering Area Offset','revslider-whiteboard-addon'),
					'jitteringdistancehor' => __('Jittering Area Width','revslider-whiteboard-addon'),
					'jitteringoffsethor' => __('Jittering Area Offset Hor.','revslider-whiteboard-addon'),
					'jitteringrepeat' => __('Jittering Changes','revslider-whiteboard-addon'),
					'handrotation' => __('Hand Rotation','revslider-whiteboard-addon'),
					'maxrotationangle' => __('Max Rotation Angle','revslider-whiteboard-addon'),
					'rotationvariations' => __('Rotation Variations','revslider-whiteboard-addon'),
					'handangle' => __('Writting Angle','revslider-whiteboard-addon'),
					'handanglerepeat' => __('Writting Angle Variations','revslider-whiteboard-addon'),
					'preview' => __('Preview','revslider-whiteboard-addon'),
					'pickorigin' => __('Pick Origin','revslider-whiteboard-addon'),
					'active' => __('Active','revslider-whiteboard-addon'),
					'hand' => __('Hand','revslider-whiteboard-addon'),
					'mode' => __('Mode','revslider-whiteboard-addon'),
					'draw' => __('Draw','revslider-whiteboard-addon'),
					'write' => __('Write','revslider-whiteboard-addon'),
					'move' => __('Move','revslider-whiteboard-addon'),
					'right' => __('Right','revslider-whiteboard-addon'),
					'left' => __('Left','revslider-whiteboard-addon'),
					'ltr' => __('Left to Right','revslider-whiteboard-addon'),
					'rtl' => __('Right to Left','revslider-whiteboard-addon'),
					'ttb' => __('Top to Bottom','revslider-whiteboard-addon'),
					'btt' => __('Bottom to Top','revslider-whiteboard-addon'),
					'gotonextlayer' => __('Move to next Layer','revslider-whiteboard-addon'),
					'hidehand' => __('Hide Hand when Done','revslider-whiteboard-addon'),
					'xoffset' => __('Horizontal Offset','revslider-whiteboard-addon'),
					'yoffset' => __('Vertical Offset','revslider-whiteboard-addon'),
					'whendone' => __('At the End','revslider-whiteboard-addon'),
					'whiteboard' => __('Whiteboard','revslider-whiteboard-addon')
				)
			);
		
		}
		
		return $var;
	
	}
}
?>