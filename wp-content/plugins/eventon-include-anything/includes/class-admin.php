<?php 
/**
* ADMIN Class
* @version 0.6
*/

class EVOIA_Admin{
	public function __construct(){
		add_action('admin_init',array($this, 'admin_init'));
		add_action( 'add_meta_boxes', array($this, '_meta_boxes') );
		add_action( 'save_post', array($this,'save_meta_data'), 1, 2 );

		add_filter( 'evo_addons_details_list', array( $this, 'eventon_addons_list' ), 10, 1 );
	}

	
	function admin_init(){
		global $pagenow, $typenow, $wpdb, $post;	

		$print_css_on = array( 'post-new.php', 'post.php' );
		foreach ( $print_css_on as $page ){
			add_action( 'admin_print_styles-'. $page, array($this,'evoia_post_styles' ));	
		}
	}
	
	function _meta_boxes(){
		$my_post_types = get_post_types();

		foreach ( $my_post_types as $my_post_type ) {
			if($my_post_type == 'ajde_events') continue;

			EVO()->elements->load_colorpicker();

			add_meta_box(
				'evoia_mb',
				__('Include in Events','evoia'), 
				array($this, '_metabox_content'),
				$my_post_type, 'normal', 'high');
		}		
		
	}

	public function get_post_meta_fields(){
		return array('evcal_srow','evcal_erow','_evo_inc','_evoia_stitle','_evoia_title','_evoia_clk','_evoia_apr','evcal_exlink','_evcal_exlink_target','_evcal_exlink_option','evcal_event_color');
	}

	function _metabox_content(){
		global $post;

		$POST = new EVO_Data_Store();
		$POST->ID = (int)$post->ID;

		$POST->load_certain_meta( $this->get_post_meta_fields() );

		echo EVO()->elements->get_element( array(
			'type'=>'yesno_btn',
			'id'=>'_evo_inc',
			'value'=> $POST->get_meta('_evo_inc'),
			'label'=>'Include this post in Event Calendar',
			'afterstatement'=>'evo_include'
		));
	

		EVO()->elements->_print_date_picker_values();
		$wp_time_format = get_option('time_format');
		$wp_date_format = get_option('date_format');

		// Minute increment	
		$minIncre = EVO()->cal->get_prop('evo_minute_increment','evcal_1');
		if(empty($minIncre)) $minIncre = 1;
		$minIncre = 60/ $minIncre;	

		?>
		<div id='evo_include' class='evo_meta_elements' style='display:<?php echo $POST->check_yn('_evo_inc') ?'block':'none';?>; background: #efefef;border-radius:10px;padding: 20px;'>

			<h4><?php _e('Select the date range to include','evoia');?></h4>
			
			<div class='evo_edit_field_box' style='background-color: #f5c485; background: linear-gradient(45deg, #f9d29f, #ffae5b); border-radius: 20px; padding: 15px; margin: 8px 0;'>
				<p style='padding: 0; margin: 0 0 10px;font-size: 14px;'><?php _e('Start Date for inclusion','evoia');?></p>
				<?php

				$rand = 457973;


				$wp_time_format = get_option('time_format');
				$hr24 = (strpos($wp_time_format, 'H')!==false || strpos($wp_time_format, 'G')!==false)? true:false;	
				$used_timeFormat = $hr24?'24h':'12h';

				
				EVO()->elements->print_date_time_selector(
					array(
						'date_format_hidden'=>'Y/m/d',
						'date_format'=> $wp_date_format,
						'time_format'=> $wp_time_format,
						'unix'=> $POST->get_meta('evcal_srow'),
						'type'=>'start',
						'rand'=> $rand,
						'minute_increment'=> $minIncre,
					)
				);

				?><p style='padding: 0;margin: 0 0 10px; font-size: 14px;'><?php _e('End Date for inclusion','evoia');?></p>

				<input type="hidden" name="_evo_date_format" value='Y/m/d'/>
				<input type="hidden" name="_evo_time_format" value='<?php echo $used_timeFormat;?>'/>
				<div style=''>
				<?php

				EVO()->elements->print_date_time_selector(
					array(
						'date_format_hidden'=>'Y/m/d',
						'date_format'=> $wp_date_format,
						'time_format'=> $wp_time_format,
						'unix'=> $POST->get_meta('evcal_erow'),
						'type'=>'end',
						'rand'=> $rand,
						'minute_increment'=> $minIncre,
					)
				);

				echo "</div>";

			echo "</div>";

			// title value
			$title = '';
			if( !empty($post->post_title) ) $title = $post->post_title;
			if( $t = $POST->get_meta('_evoia_title') )
				$title = $t;


			echo EVO()->elements->process_multiple_elements(
				array(
					array(
						'type'=>'text',
						'name'=> __('Title for this post', 'evoia'),
						'id'=> '_evoia_title',
						'value'=> $title ,
					),
					array(
						'type'=>'text',
						'name'=> __('Sub title for this post', 'evoia'),
						'id'=> '_evoia_stitle',
						'value'=> $POST->get_meta('_evoia_stitle'),
					),
					array(
						'id'=>'evcal_event_color',
						'type'=>'colorpicker',
						'support_input'=> true,
						'name'=> __('Color to use for this post', 'evoia'),
						'value'=> $POST->get_meta('evcal_event_color'),
					),
					array(
						'type'=>'hidden',
						'id'=> '_evcal_exlink_option',
						'value'=> '2',
					),
					array(
						'type'=>'text',
						'name'=> __('Link for this post', 'evoia'),
						'tooltip'=> __('By default this post will link to permalink of this post','evoia'),
						'id'=> 'evcal_exlink',
						'value'=> $POST->get_meta('evcal_exlink'),
					),
					array(
						'type'=>'yesno_btn',
						'label'=> __('Open the link in a new window', 'evoia'),						
						'id'=> '_evcal_exlink_target',
						'value'=> $POST->get_meta('_evcal_exlink_target'),
					),
					array(
						'type'=>'textarea',
						'name'=> __('(Optional) Tracking Code', 'evoia'),
						'tooltip'=> __('This tracking code will be included in the calendar everytime this post is shown on calendar.','evoia'),
						'id'=> '_tracking_code',
						'value'=> $POST->get_meta('_tracking_code'),
					),
				)
			);

			?>
			
		</div>

		<?php
		
	}

	// save post data
	public function save_meta_data($post_id, $post){
		
		// validation
			// Stop WP from clearing custom fields on autosave
			if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE)
				return;

			// Prevent quick edit from clearing custom fields
			if (defined('DOING_AJAX') && DOING_AJAX)
				return;
			
			// verify this came from the our screen and with proper authorization,
			// because save_post can be triggered at other times
			if( isset($_POST['evo_noncename']) ){
				if ( !wp_verify_nonce( $_POST['evo_noncename'], plugin_basename( __FILE__ ) ) ){
					return;
				}
			}
			// Check permissions
			if ( !current_user_can( 'edit_post', $post_id ) )
				return;	

			// only allowed on certain pages
				global $pagenow;
				$_allowed = array( 'post-new.php', 'post.php' );
				if(!in_array($pagenow, $_allowed)) return;

		// if include anything is turned off > dont save any meta values
			if( isset($_POST['_evo_inc']) && $_POST['_evo_inc'] == 'no') return;

		// save fields
			foreach( $this->get_post_meta_fields()as $f){
				if(!isset( $_POST[ $f ] )) continue;
				if( $f == 'evcal_srow' || $f == 'evcal_erow') continue;

				update_post_meta( $post_id, $f, $_POST[ $f ]);
			}

			$date_range = evoadmin_get_unix_time_fromt_post();

			update_post_meta( $post_id, 'evcal_srow', $date_range['unix_start']);
			update_post_meta( $post_id, 'evcal_erow', $date_range['unix_end']);
	}

// STYLES
	function evoia_post_styles(){	
		global $wp_scripts;

		//wp_enqueue_style( 'evorc_styles',EVOIA()->assets_path.'styles.css');			
		$protocol = is_ssl() ? 'https' : 'http';
		

			// JQ UI styles
		$jquery_version = isset( $wp_scripts->registered['jquery-ui-core']->ver ) ? $wp_scripts->registered['jquery-ui-core']->ver : '1.10.4';		
		wp_enqueue_style("jquery-ui-css", $protocol."://ajax.googleapis.com/ajax/libs/jqueryui/{$jquery_version}/themes/smoothness/jquery-ui.min.css");
		wp_enqueue_script( 'evoia_script',EVOIA()->assets_path.'script.js',array('jquery','jquery-ui-datepicker'), EVOIA()->version);

	}

// SUPPORTIVE
	function eventon_addons_list($default){
		$default['eventon-include-anything'] = array(
			'id'=> EVOIA()->id,
			'name'=> EVOIA()->name,
			'link'=>'https://www.myeventon.com/addons/include-anything/',
			'download'=>'https://www.myeventon.com/addons/include-anything/',
			'desc'=>'Include any posts inside eventON calendar seamlessly',
		);
		return $default;
	}
}
