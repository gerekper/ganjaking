<?php
/**
 * @author    ThemePunch <info@themepunch.com>
 * @link      http://www.themepunch.com/
 * @copyright 2016 ThemePunch
 */

if( !defined( 'ABSPATH') ) exit();

class rs_whiteboard_slide {
	
	
	public static function init(){
		
		add_filter('revslider_slide_addons', array('rs_whiteboard_slide', 'add_whiteboard_settings'), 10, 3);
		add_action('revslider_fe_javascript_option_output', array('rs_whiteboard_slide', 'add_fe_javascript_options'));
		
	}
	
	
	public static function add_whiteboard_settings($settings, $slide, $slider){
		if($slider->getParam("wb_enable","off") === 'off'){ //disabled for this slider
			echo '<script type="text/javascript">';
			echo 'var wb_loaded = false;';
			echo '</script>';
			
			return $settings;
		}
		
		$handvals = array();
		$hands = array("movehand","writehand");
		for ($i=0;$i<count($hands); $i++) {
			switch($slider->getParam("wb_".$hands[$i]."_source","1")){
				case '1':
					if ($hands[$i]=="movehand")
						$handvals[$hands[$i]]['src'] = WHITEBOARD_PLUGIN_URL.'assets/images/hand_point_right.png';
					else
						$handvals[$hands[$i]]['src'] = WHITEBOARD_PLUGIN_URL.'assets/images/write_right_angle.png';
				break;
				case '2':
					if ($hands[$i]=="movehand")
						$handvals[$hands[$i]]['src'] = WHITEBOARD_PLUGIN_URL.'assets/images/hand_point_right.png';
					else
						$handvals[$hands[$i]]['src'] = WHITEBOARD_PLUGIN_URL.'assets/images/write_right_angle.png';
				break;
				case '3':
					if ($hands[$i]=="movehand")
						$handvals[$hands[$i]]['src'] = WHITEBOARD_PLUGIN_URL.'assets/images/hand_point_right.png';
					else
						$handvals[$hands[$i]]['src'] = WHITEBOARD_PLUGIN_URL.'assets/images/write_right_angle.png';
				break;
				case "custom":
					$handvals[$hands[$i]]['src'] = $slider->getParam('wb_'.$hands[$i].'_source_custom','');
				break;
			}
			$handvals[$hands[$i]]['type'] = $slider->getParam('wb_'.$hands[$i].'_type','right');
			$handvals[$hands[$i]]['width'] = $slider->getParam('wb_'.$hands[$i].'_width','200');
			$handvals[$hands[$i]]['height'] = $slider->getParam('wb_'.$hands[$i].'_height','200');
			$handvals[$hands[$i]]['origin_x'] = $slider->getParam('wb_'.$hands[$i].'_origin_x','0');
			$handvals[$hands[$i]]['origin_y'] = $slider->getParam('wb_'.$hands[$i].'_origin_y','0');
			
			$handvals[$hands[$i]]['jitter'] = $slider->getParam('wb_global_'.$hands[$i].'_jitter','80');
			$handvals[$hands[$i]]['jitter_horizontal'] = $slider->getParam('wb_global_'.$hands[$i].'_jitter_horizontal','100');
			$handvals[$hands[$i]]['jitter_repeat'] = $slider->getParam('wb_global_'.$hands[$i].'_jitter_repeat','5');
			$handvals[$hands[$i]]['jitter_offset'] = $slider->getParam('wb_global_'.$hands[$i].'_jitter_offset','10');
			$handvals[$hands[$i]]['jitter_offset_horizontal'] = $slider->getParam('wb_global_'.$hands[$i].'_jitter_offset_horizontal','0');
			
			$handvals[$hands[$i]]['angle'] = $slider->getParam('wb_global_'.$hands[$i].'_angle','10');
			$handvals[$hands[$i]]['angle_repeat'] = $slider->getParam('wb_global_'.$hands[$i].'_angle_repeat','3');
			
			if($hands[$i] == 'writehand')
				$handvals[$hands[$i]]['direction'] = $slider->getParam('wb_'.$hands[$i].'_direction','ltr');
		}
		
		$settings['whiteboard'] = array(
			'title'		=> __('Whiteboard', 'rs_whiteboard'),
			'markup'	=> 
'<div>
	<span class="rs-layer-toolbar-box" style="border-left: 0px !important;">
		<i class="rs-mini-layer-icon eg-icon-brush rs-toolbar-icon tipsy_enabled_top" original-title="Hand Use To"></i>		
		<select id="wb-hand_function" name="wb-hand_function" class="rs-layer-input-field" style="width:100px">		
			<option value="off">'.__('Off', 'rs_whiteboard').'</option>
			<option value="write">'.__('Write', 'rs_whiteboard').'</option>
			<option value="draw">'.__('Draw', 'rs_whiteboard').'</option>
			<option value="move">'.__('Move', 'rs_whiteboard').'</option>
		</select>
	</span>

	<span class="rs-layer-toolbar-box" id="wb-hand_type_wrapper">
		<i class="rs-mini-layer-icon eg-icon-right-hand rs-toolbar-icon tipsy_enabled_top" original-title="Which Hand"></i>			
		<select id="wb-hand_type" name="wb-hand_type" class="rs-layer-input-field" style="width:100px">
			<option value="left">'.__('Left Hand', 'rs_whiteboard').'</option>
			<option value="right">'.__('Right Hand', 'rs_whiteboard').'</option>		
		</select>
	</span>

	<span class="rs-layer-toolbar-box" id="wb_direction-wrapper">
			<i class="rs-mini-layer-icon eg-icon-move rs-toolbar-icon tipsy_enabled_top" original-title="Flow Direction"></i>				
			<select id="wb-hand_direction" name="wb-hand_direction" class="rs-layer-input-field" style="width:100px">
				<option value="left_to_right">'.__('Left to Right', 'rs_whiteboard').'</option>
				<option value="right_to_left">'.__('Right to Left', 'rs_whiteboard').'</option>
				<option value="top_to_bottom">'.__('Top to Bottom', 'rs_whiteboard').'</option>
				<option value="bottom_to_top">'.__('Bottom to Top', 'rs_whiteboard').'</option>		
			</select>
	</span>

	<span class="rs-layer-toolbar-box" id="wb_reset-to-preset">
			<i class="rs-mini-layer-icon eg-icon-magic rs-toolbar-icon tipsy_enabled_top" original-title="Presets"></i>				
			<select id="wb-hand_preset" name="wb-hand_preset" class="rs-layer-input-field" style="width:125px">
				<option value="none">'.__('Reset To Preset', 'rs_whiteboard').'</option>
				<option class="write-preset" value="write_quick">'.__('Write Quick', 'rs_whiteboard').'</option>
				<option class="write-preset" value="write_normal">'.__('Write Normal', 'rs_whiteboard').'</option>
				<option class="write-preset" value="write_slow">'.__('Write Slow', 'rs_whiteboard').'</option>
				<option class="draw-preset" value="draw_from_left">'.__('Draw From Left', 'rs_whiteboard').'</option>
				<option class="draw-preset" value="draw_from_right">'.__('Draw From Right', 'rs_whiteboard').'</option>
				<option class="draw-preset" value="draw_from_top">'.__('Draw From Top', 'rs_whiteboard').'</option>
				<option class="draw-preset" value="draw_from_bottom">'.__('Draw From Bottom', 'rs_whiteboard').'</option>
				<option class="move-preset" value="move_from_left">'.__('Move From Left', 'rs_whiteboard').'</option>
				<option class="move-preset" value="move_from_right">'.__('Move From Right', 'rs_whiteboard').'</option>
				<option class="move-preset" value="move_from_top">'.__('Move From Top', 'rs_whiteboard').'</option>
				<option class="move-preset" value="move_from_bottom">'.__('Move From Bottom', 'rs_whiteboard').'</option>
			</select>
	</span>

	
</div>
<div style="border-top:1px solid #ddd;">
	<span class="rs-layer-toolbar-box" id="wb-jitter-wrapper" >
		<i class="rs-mini-layer-icon wb-icon-jitter rs-toolbar-icon tipsy_enabled_top" original-title="jitter Distance"></i>
		<span style="position:relative"><span class="jitter_suffix">%</span><input type="text" style="width:75px;" class="textbox-caption rs-layer-input-field" id="wb-hand_jitter_distance" name="wb-hand_jitter_distance" value="80"></span>		
		<span class="rs-layer-toolbar-space" style="margin-left:15px"></span>
		<i class="rs-mini-layer-icon rs-icon-yoffset rs-toolbar-icon tipsy_enabled_top" original-title="Offset of Jitter Area Vertical"></i>
		<span style="position:relative"><span class="jitter_suffix">%</span><input type="text" style="width:75px;" class=" textbox-caption rs-layer-input-field" id="wb-hand_jitter_offset" name="wb-hand_jitter_offset" value="10"></span>
						
		<span id="wb-jitter-wrapper-horizontal" style="display:none">	
			<span class="rs-layer-toolbar-space" style="margin-left:15px"></span>		
			<i class="rs-mini-layer-icon wb-icon-jitter rs-toolbar-icon tipsy_enabled_top" original-title="jitter Distance Horizontal"></i>
			<span style="position:relative"><span class="jitter_suffix">%</span><input type="text" style="width:75px;" class="textbox-caption rs-layer-input-field" id="wb-hand_jitter_distance_horizontal" name="wb-hand_jitter_distance_horizontal" value="100"></span>
				
			<span class="rs-layer-toolbar-space" style="margin-left:15px"></span>
			<i class="rs-mini-layer-icon rs-icon-xoffset rs-toolbar-icon tipsy_enabled_top" original-title="Offset of Jitter Area Horizontal"></i>
			<span style="position:relative"><span class="jitter_suffix">%</span><input type="text" style="width:75px;" class=" textbox-caption rs-layer-input-field" id="wb-hand_jitter_offset_horizontal" name="wb-hand_jitter_offset_horizontal" value="0"></span>
		</span>

		<span class="rs-layer-toolbar-space" style="margin-left:15px"></span>
		<i class="rs-mini-layer-icon rs-icon-clock rs-toolbar-icon tipsy_enabled_top" original-title="Variations of Jitter Distances during the Write Process "></i>
		<input type="text" style="width:75px;" class=" textbox-caption rs-layer-input-field" id="wb-hand_jitter_repeat" name="wb-hand_jitter_repeat" value="5">

	</span>
	
	<span class="rs-layer-toolbar-box" id="wb-angle-wrapper">
		<i class="rs-mini-layer-icon wb-icon-angle rs-toolbar-icon tipsy_enabled_top" original-title="Angle of Hand during Write Process"></i>
		<input type="text" style="width:75px;" class=" textbox-caption rs-layer-input-field" id="wb-hand_angle" name="wb-hand_angle" value="10" >
		<span class="rs-layer-toolbar-space" style="margin-left:15px"></span>
		<i class="rs-mini-layer-icon rs-icon-clock rs-toolbar-icon tipsy_enabled_top" original-title="Variations of Angle during the Write Process "></i>
		<input type="text" style="width:75px;" class=" textbox-caption rs-layer-input-field" id="wb-hand_angle_repeat" name="wb-hand_angle_repeat" value="3">
	</span>

	<span class="rs-layer-toolbar-box" id="wb-full-rotation-wrapper">
		<i class="rs-mini-layer-icon rs-icon-rotationz rs-toolbar-icon tipsy_enabled_top" original-title="Full Rotation"></i>			
		<input type="text" style="width:75px;" class=" textbox-caption rs-layer-input-field" id="wb-hand_full_rotation" name="wb-hand_full_rotation" value="0"></span>
		<input type="text" style="width:75px;display:none !important" class=" textbox-caption rs-layer-input-field" id="wb-hand_full_rotation_angle" name="wb-hand_full_rotation_angle" value="0"></span>
	</span>

	<span id="wb_xy_offset_wrapper">
		<span class="rs-layer-toolbar-box" >
			<i class="rs-mini-layer-icon rs-icon-xoffset rs-toolbar-icon tipsy_enabled_top" original-title="X Offset"></i>		
			<input type="text" style="width:75px;" class="textbox-caption rs-layer-input-field" id="wb-hand_x_offset" name="wb-hand_x_offset" value="0"/>
			<span class="rs-layer-toolbar-space" style="margin-left:15px"></span>
			<i class="rs-mini-layer-icon rs-icon-yoffset rs-toolbar-icon tipsy_enabled_top" original-title="Y Offset"></i>		
			<input type="text" style="width:75px;" class="textbox-caption rs-layer-input-field" id="wb-hand_y_offset" name="wb-hand_y_offset" value="0"/>
		</span>
	</span>

	<span class="rs-layer-toolbar-box" id="wb-goto_wrapper">
		<i class="rs-mini-layer-icon eg-icon-link-ext rs-toolbar-icon tipsy_enabled_top" original-title="Go To Next Layer After Animation"></i>			
		<select id="wb-gotolayer" name="wb-gotolayer" class="rs-layer-input-field" style="width:100px">
			<option value="on">'.__('Go To Next Layer', 'rs_whiteboard').'</option>
			<option value="off">'.__('Hide Hand', 'rs_whiteboard').'</option>		
		</select>
	</span>


		

</div>


',
		   'javascript'=> '
			var wb_loaded = true;
		   
			var wb_writehand_sources = {
				handtype:"'.esc_attr($handvals['writehand']['type']).'",
				direction:"'.esc_attr($handvals['writehand']['direction']).'",
				src:"'.esc_attr($handvals['writehand']['src']).'",
				width:"'.esc_attr($handvals['writehand']['width']).'",
				height:"'.esc_attr($handvals['writehand']['height']).'",
				origin_x:"'.esc_attr($handvals['writehand']['origin_x']).'",
				origin_y:"'.esc_attr($handvals['writehand']['origin_y']).'",
				jitter:"'.esc_attr($handvals['writehand']['jitter']).'",
				jitter_horizontal:"'.esc_attr($handvals['writehand']['jitter_horizontal']).'",
				jitter_repeat:"'.esc_attr($handvals['writehand']['jitter_repeat']).'",
				jitter_offset:"'.esc_attr($handvals['writehand']['jitter_offset']).'",
				jitter_offset_horizontal:"'.esc_attr($handvals['writehand']['jitter_offset_horizontal']).'",
				angle:"'.esc_attr($handvals['writehand']['angle']).'",
				angle_repeat:"'.esc_attr($handvals['writehand']['angle_repeat']).'"
			};
			   
			var wb_movehand_sources = {
				handtype:"'.esc_attr($handvals['movehand']['type']).'",
				src:"'.esc_attr($handvals['movehand']['src']).'",
				width:"'.esc_attr($handvals['movehand']['width']).'",
				height:"'.esc_attr($handvals['movehand']['height']).'",
				origin_x:"'.esc_attr($handvals['movehand']['origin_x']).'",
				origin_y:"'.esc_attr($handvals['movehand']['origin_y']).'",
				jitter:"'.esc_attr($handvals['movehand']['jitter']).'",
				jitter_horizontal:"'.esc_attr($handvals['movehand']['jitter_horizontal']).'",
				jitter_repeat:"'.esc_attr($handvals['movehand']['jitter_repeat']).'",
				jitter_offset:"'.esc_attr($handvals['writehand']['jitter_offset']).'",
				jitter_offset_horizontal:"'.esc_attr($handvals['writehand']['jitter_offset_horizontal']).'",
				angle:"'.esc_attr($handvals['movehand']['angle']).'",
				angle_repeat:"'.esc_attr($handvals['movehand']['angle_repeat']).'"
			};'
		);
		
		return $settings;
	}
	
	
	public static function add_fe_javascript_options($slider){
		
	}
}
?>