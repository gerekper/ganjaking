<?php 
/**
* ADMIN Class
* @version 1.0.3
*/

class EVORC_Admin{
	public function __construct(){
		add_action('admin_init',array($this, 'admin_init'));	
	}

	
	function admin_init(){

		add_filter( 'evo_addons_details_list', array( $this, 'eventon_addons_list' ), 10, 1 );
		add_action( 'add_meta_boxes', array($this, '_meta_boxes') );

		global $pagenow, $typenow, $wpdb, $post;	
			
		if ( $typenow == 'post' && ! empty( $_GET['post'] ) && $post){
			$typenow = $post->post_type;
		} elseif ( empty( $typenow ) && ! empty( $_GET['post'] ) ) {
	        $typenow = get_post_type( $_GET['post'] );
	    }

	    if ( $typenow == '' || $typenow == "ajde_events") {
			// Event Post Only
			$print_css_on = array( 'post-new.php', 'post.php' );
			foreach ( $print_css_on as $page ){
				add_action( 'admin_print_styles-'. $page, array($this,'event_post_styles' ));		
			}
		}		
	}
	// styles and scripts
		function event_post_styles(){			
			wp_enqueue_style( 'evorc_styles',EVORC()->assets_path.'styles.css');			
			wp_enqueue_script( 'evorc_script',EVORC()->assets_path.'script.js',array('jquery','jquery-ui-resizable','jquery-ui-draggable'), EVORC()->version);
			wp_localize_script( 
				'evorc_script', 
				'evorc_admin_ajax_script', 
				array( 
					'ajaxurl' => admin_url( 'admin-ajax.php' ) , 
					'postnonce' => wp_create_nonce( 'eventonst_nonce' )
				)
			);
		}

	
	function _meta_boxes(){
		add_meta_box('evorc_mb',__('Repeat Customizer','evors'), array($this, '_metabox_content'),'ajde_events', 'normal', 'high');
	}
	function _metabox_content(){
		global $post;

		$event_id = (int)$post->ID;
		$EVENT = new EVO_Event( $event_id);

		if($EVENT->is_repeating_event()){
			?>
			<div class='eventon_mb' style="padding:20px;">
				<a class='button_evo ajde_popup_trig rep_customizer_lb' data-eid='<?php echo $event_id;?>' data-popc='evorc_lightbox'><?php _e('Open Repeat Customizer');?></a>
			</div>
			<?php

			global $ajde;
			echo $ajde->wp_admin->lightbox_content(array(
				'class'=>'evorc_lightbox', 
				'content'=>"<p class='evo_lightbox_loading'></p>", 
				'title'=>__('Repeat Customizer','evors'),
				'type'=>'padded',
				'width'=> 700
				)
			);
		}else{
			?>
			<div class='eventon_mb' style="padding:20px;">
				<?php _e('This is only available for repeating events');?>
			</div>
			<?php
		}
		
	}

// extras
	function eventon_addons_list($default){

		$default['eventon-repeat-customizer'] = array(
			'id'=> EVORC()->id,
			'name'=> EVORC()->name,
			'link'=>'http://www.myeventon.com/addons/repeat-customizer',
			'download'=>'http://www.myeventon.com/addons/repeat-customizer',
			'desc'=>'Customize repeating event data for individual repeat instance separately',
		);
		return $default;
	}


// Repeat event instance customizing view

		function other(){
			$ajax_events = array(
				'cancel_repeats_view'	=>'cancel_repeats_view',
			);
			foreach ( $ajax_events as $ajax_event => $class ) {
				$prepend = 'eventon_';
				add_action( 'wp_ajax_'. $prepend . $ajax_event, array( $this, $class ) );
				add_action( 'wp_ajax_nopriv_'. $prepend . $ajax_event, array( $this, $class ) );
			}


			if( $event->is_repeating_event()){
				echo "<span class='evo_btn ajde_popup_trig' data-popc='evo_repeat_cancels' data-ajax='yes' data-d='". json_encode(array('action'=>'eventon_cancel_repeats_view', 'eid'=> $event->ID))."'>". __('Cancel Repeating Event')."</span>";
			}else{
				echo $ajde->wp_admin->html_yesnobtn(
					array(
						'id'=>'_cancel', 
						'var'=> $event->get_prop('_cancel'),
						'input'=>true,
						'label'=>__('Cancel Event','eventon'),
						'guide'=>__('Cancel this event','eventon'),
						'guide_position'=>'L',
						'attr'=>array('afterstatement'=>'evo_editevent_cancel_text')
					));
			}

			echo $ajde->wp_admin->lightbox_content(array(
				'class'=>'evo_repeat_cancels', 
				'content'=> "<p class='evo_lightbox_loading'></p>",
				'title'=>__('Cancel Repeating Event','evotx'), 
				'max_height'=>500 
			));
			echo $ajde->wp_admin->lightbox_content(array(
				'class'=>'evo_customize_repeat_event', 
				'content'=> "<p class='evo_lightbox_loading'></p>",
				'title'=>__('Customize Repeat Instance of the Event','evotx'), 
				'max_height'=>500, 'width'=>500
			));
		}
		function cancel_repeats_view(){

			$event_id = (int)$_POST['eid'];
			$EVENT = new EVO_Event( $event_id);

			ob_start();
			if( $EVENT->is_repeating_event()){
				date_default_timezone_set('UTC');	
				$date_time_format = get_option('date_format').' '.get_option('time_format');

				$repeats = $EVENT->get_repeats();

				echo "<div class='evo_editevent_repeat_customization'>";
				foreach($repeats as $in=>$R){
					 echo "<p class='repeat'><em>".$in.'</em> '. date($date_time_format, $R[0]).' - '. date($date_time_format, $R[0]) ."<span class='actions'><i class='fa fa-pencil ajde_popup_trig' data-popc='evo_customize_repeat_event'></i><i class='fa fa-trash'></i></span></p>";
				}
				echo "</div>";
			}else{
				echo  __('This is not a repeating event','eventon');
			}

			$html = ob_get_clean();
			echo json_encode(array(
				'status'=>'good','html'=> $html
			)); exit;
		}

		// single instance customize
		function one_repeat_customize(){
			
		}
}
