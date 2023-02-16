<?php
/**
 * Admin settings class
 *
 * @author 		AJDE
 * @category 	Admin
 * @package 	eventon-SS/classes
 * @version     2.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class EVOSS_admin{
	
	function __construct(){
		$this->opt = $termMeta = get_option( "evo_tax_meta");
		add_action('admin_init', array($this, 'admin_init'));

		add_action('evo_admin_event_only_page', array($this, 'scripts_styles'));
		//add_action( 'admin_menu', array( $this, 'menu' ),9);
	}

	// INITIATE
		function admin_init(){

			$postType = !empty($_GET['post_type'])? sanitize_text_field($_GET['post_type']): false;	   
	   		if(!$postType && !empty($_GET['post']))  $postType = get_post_type( sanitize_text_field($_GET['post']) );

			$this->evopt1 = get_option('evcal_options_evcal_1');

			// meta box on event edit
			add_filter('add_meta_boxes',array($this, 'event_metabox_add'));
			add_action('eventon_save_meta', array($this, 'save_event_post'), 10, 4);
			add_filter('evo_eventedit_pageload_data', array($this, 'eventedit_content'), 12,3);
			//add_filter('evo_eventedit_pageload_dom_ids', array($this, 'eventedit_dom_ids'), 12,3);

			// language
			add_filter('eventon_settings_lang_tab_content', array($this, 'language_additions'), 10, 1);
			add_filter( 'eventon_appearance_add', array($this, 'appearance_settings' ), 10, 1);
			add_filter( 'eventon_inline_styles_array',array($this, 'dynamic_styles') , 10, 1);
			add_filter( 'evo_styles_primary_font',array($this,'primary_font') ,10, 1);
			add_filter( 'evo_styles_secondary_font',array($this,'secondary_font') ,10, 1);
		
			// taxonomy fields
				add_filter("manage_edit-event_speaker_columns", array($this,'edit_columns')); 
				add_filter("manage_event_speaker_custom_column", array($this,'manage_columns'), 10, 3);
				add_action( 'event_speaker_add_form_fields', array($this,'add_meta_fields'), 10, 2 );
		 		add_action( 'event_speaker_edit_form_fields', array($this,'edit_meta_fields'), 10, 2 );
		 		add_action( 'edited_event_speaker', array($this,'save_tax_meta'), 10, 2 );  
				add_action( 'create_event_speaker', array($this,'save_tax_meta'), 10, 2 );

			add_filter( 'eventon_eventcard_boxes',array($this,'add_toeventcard_order') , 10, 1);
			add_filter( 'eventon_custom_icons',array($this, 'custom_icons') , 10, 1);

			// add speaker as filtering option
			add_filter( 'evo_settings_filtering_taxes',array($this, 'filtering_options') , 10, 1);
			add_filter( 'evo_tax_translated_names',array($this, 'tax_names') , 10, 2);
			add_filter( 'evo_taxonomy_form_fields_array',array($this, 'tax_fields') , 10, 3);
			
		}

	// scripts and styles
		function scripts_styles(){
			wp_register_script('evoss_script_b',EVOSS()->assets_path.'SS_script_b.js', array('jquery','jquery-ui-sortable'), EVOSS()->version, true );
			wp_enqueue_script( 'evoss_script_b');
			
			wp_register_style( 'evoss_styles_b',EVOSS()->assets_path.'SS_styles_b.css');
			wp_enqueue_style( 'evoss_styles_b');
		}

	// Add speakers to filtering tax in settings
		function filtering_options($event_type_options){
			$event_type_options['event_speaker'] = 'Event Speaker';
			return $event_type_options;
		}
		function tax_names($array, $tax){
			if( $tax == 'event_speaker') $array['event_speaker'] = __('Event Speaker','eventon');

			return $array;
		}
		function tax_fields($array, $tax, $event_tax_term){
			if( $tax == 'event_speaker'){
				$array['event_speaker'] = EVOSS()->functions->speaker_fields_processed( $event_tax_term);
			}
			return $array;
		}

	// Event Card 
		function add_toeventcard_order($array){
			$array['evospk']= array('evospk',__('Speakers Box','eventon'));
			$array['evosch']= array('evosch',__('Schedule Box','eventon'));
			return $array;
		}
		function custom_icons($array){
			$array[] = array('id'=>'evcal__evospk_001','type'=>'icon','name'=>'Speakers Icon','default'=>'fa-coffee');
			$array[] = array('id'=>'evcal__evosch_001','type'=>'icon','name'=>'Schedule Icon','default'=>'fa-calendar-check-o');
			return $array;
		}

	// tax columns & fields
		function edit_columns(){
			$new_columns = array(
		        'cb' => '<input type="checkbox" />',
		        'name' => __('Speaker','eventon'),
		        'speaker_details' => __('Title','eventon'),
		        'posts' => __('Count','eventon'),
		        'slug' => __('Slug'),
		    );			    
		    return $new_columns;
		}
		function manage_columns($out, $column_name, $termID){
			switch ($column_name) {
			    case 'speaker_details': 			    	
			    	$termmeta = evo_get_term_meta('event_speaker',$termID, $this->opt);
			    	$out = !empty($termmeta['evo_speaker_title'])? 
			    		stripslashes( $termmeta['evo_speaker_title']) :'-';
			    break;
			}
			return $out;    
		}
		function add_meta_fields(){
			global $evo_speak;

			foreach($evo_speak->functions->speaker_fields() as $field=>$var):
				if(in_array($field, array('evo_spk_img','evo_speaker_desc')) ) continue; // skip fields
			?>
				<div class="form-field">
					<label for="termmeta[<?php echo $field;?>]"><?php echo $var[1]; ?></label>
					<input type="text" name="termmeta[<?php echo $field;?>]" id="termmeta[<?php echo $field;?>]" value="">
					<?php if(!empty($var[2])):?><p class="description"><?php echo $var[2]; ?></p><?php endif;?>
				</div>
			<?php endforeach; ?>
				<div class="form-field evo_metafield_image">
					<label for="termmeta[evo_spk_img]"><?php _e( 'Image', 'eventon' ); ?></label>
					
					<input style='width:auto' class="custom_upload_image_button button <?php echo 'chooseimg';?>" data-txt='<?php echo __('Remove Image','eventon');?>' type="button" value="<?php _e('Choose Image','eventon');?>" /><br/>
					<span class='evo_loc_image_src image_src'><img src='' style='display:none'/></span>
					
					<input class='evo_spk_img evo_meta_img' type="hidden" name="termmeta[evo_spk_img]" id="termmeta[evo_spk_img]" value="">
					<p class="description"><?php _e( 'Event Speaker Image','eventon' ); ?></p>
				</div>
			<?php
		}
		function edit_meta_fields($term){
			global $evo_speak;

			$termID = $term->term_id;
			$termmeta = evo_get_term_meta('event_speaker',$termID, $this->opt);

			foreach($evo_speak->functions->speaker_fields() as $field=>$var){
				if(in_array($field, array('evo_spk_img','evo_speaker_desc')) ) continue; // skip fields			
			?>
				<tr class="form-field">
					<th scope="row" valign="top"><label for="termmeta[<?php echo $field;?>]"><?php echo $var[1]; ?></label></th>
					<td>
						<input type="text" name="termmeta[<?php echo $field;?>]" id="termmeta[<?php echo $field;?>]" value="<?php echo !empty( $termmeta[$field] ) ? 
							stripslashes( esc_attr( $termmeta[$field] ) ): ''; ?>">
						<?php if(!empty($var[2])):?><p class="description"><?php echo $var[2]; ?></p><?php endif;?>
					</td>
				</tr>
			<?php
			}
			?>
			<tr class="form-field">
				<th scope="row" valign="top"><label for="termmeta[evo_spk_img]"><?php _e( 'Image', 'eventon' ); ?></label></th>
				<td class='evo_metafield_image'>
					<?php 
						if(!empty($termmeta['evo_spk_img'])){
							$img_url = wp_get_attachment_image_src($termmeta['evo_spk_img'],'medium');
						}else{ $img_url=false;}

						$__button_text = (!empty($termmeta['evo_spk_img']))? __('Remove Image','eventon'): __('Choose Image','eventon');
						$__button_text_not = (empty($termmeta['evo_spk_img']))? __('Remove Image','eventon'): __('Choose Image','eventon');
						$__button_class = (!empty($termmeta['evo_spk_img']))? 'removeimg':'chooseimg';
					?>						
					<input style='width:auto' class="custom_upload_image_button button <?php echo $__button_class;?>" data-txt='<?php echo $__button_text_not;?>' type="button" value="<?php echo $__button_text;?>" /><br/>
					<span class='evo_spk_image_src image_src'>
						<img src='<?php echo $img_url[0];?>' style='width:auto; margin-top:5px;<?php echo !empty($termmeta['evo_spk_img'])?'':'display:none';?>'/></span>
					
					<input class='evo_spk_img evo_meta_img' type="hidden" name="termmeta[evo_spk_img]" id="termmeta[evo_spk_img]" value="<?php echo !empty( $termmeta['evo_spk_img'] ) ? esc_attr( $termmeta['evo_spk_img'] ) : ''; ?>">
					<p class="description"><?php _e( 'Event Speaker Image','eventon' ); ?></p>
				</td>
			</tr>
			<?php
		}
		function save_tax_meta($term_id){
			if ( isset( $_POST['termmeta'] ) ) {				
				evo_save_term_metas('event_speaker',$term_id, $_POST['termmeta']);
			}
		}

	// meta box
		function event_metabox_add(){
			add_meta_box('evoss_metabox', __('Speakers & Schedule','evoss'), array($this,'metabox_content'),'ajde_events', 'normal', 'high');
		}
		function metabox_content(){
			?>
			<div id='evo_mb_ss' class='eventon_mb'>
				<div id='evo_pageload_data_ss'> <?php EVO()->evo_admin->print_metabox_loading_html();?></div>
			</div>
			<?php
		}
		function eventedit_content($array, $postdata, $EVENT){

			$content = EVO()->evo_admin->metaboxes->process_content(
				$this->event_metabox( $EVENT )
			);

			$array['evoss'] = $content;
			return $array;
		}


		function event_metabox($EVENT){
			$array = array();
			$array[] = array(
				'id'=>'ev_speakers',
				'name'=>__('Event Speakers','eventon'),
				'variation'=>'customfield',	
				'hiddenVal'=>'',	
				'iconURL'=>get_eventON_icon('evcal__evospk_001', 'fa-coffee',$this->evopt1 ),
				'iconPOS'=>'',
				'type'=>'code',
				'content'=>$this->content($EVENT),
				'slug'=>'ev_speakers'
			);
			$array[] = array(
				'id'=>'ev_schedule',
				'name'=>__('Event Schedule','eventon'),
				'variation'=>'customfield',	
				'hiddenVal'=>'',	
				'iconURL'=>get_eventON_icon('evcal__evosch_001', 'fa-calendar-check-o',$this->evopt1 ),
				'iconPOS'=>'',
				'type'=>'code',
				'content'=>$this->content_sch($EVENT),
				'slug'=>'ev_schedule'
			);
			return $array;
		}

		// speaker meta box content
			function content($EVENT){
				global $post, $ajde, $evo_speak;

				wp_enqueue_script('evoss_script_b');
				
				$p_id = $EVENT->ID;

				$help = new evo_helper();

				$tax_name = __('Event Speaker','eventon');
				$tax = 'event_speaker';

				ob_start();
				?>
				<div class='evcal_data_block_style1' data-eventid='<?php echo $p_id;?>'>
					<div class='evcal_db_data'>					
						
						
						<div class='evcal_speaker_data_section'>
							<div class='evo_singular_tax_for_event event_speaker' >
							<?php
								echo EVO()->taxonomies->get_meta_box_content( 'event_speaker',$EVENT->ID, __('Event Speaker','eventon'));
							?>
							</div>						
						</div>
						<p class="evo_tax_msg"></p>
					</div>
				</div>
				<?php
				return ob_get_clean();
			}

		// Schedule
			function content_sch($EVENT){
				
				$name = __('Schedule Block','evoss');
				$help = new evo_helper();

				ob_start();			
				?>
				<div class='evcal_db_data'>
				
					<div id='evoss_schedule_content' class='evo_sch_blocks'>
						<?php
							$blocks = $EVENT->get_prop('_sch_blocks')? $EVENT->get_prop('_sch_blocks'): false;

							echo "<ul class='evosch_blocks_list' data-eventid='{$EVENT->ID}'>";
							// if terms exists
							if ( $blocks ){
								//print_r($blocks);
								echo EVOSS()->functions->get_schedule_html($blocks, $EVENT);
							}
							echo "</ul>";
							echo "<div class='clear'></div>";
						?>
						<input type='hidden' name='_evosch_order' value=''/>
					</div>
					<div class='evoss_actions'>
						<p class='evospk_btns'>
							<?php 
							$data_vals_new = array(
								'lbvals'=> array(
									'lbc'=>'evo_config_schedule',
									't'=> __('Add new Schedule Block','evoss'),
									'ajax'=>'yes',
									'd'=> array(
										'uid'=>'evo_new_schedule_form',
										'type'=>'new',
										'eventid'=> $EVENT->ID,
										'action'=> 'evoss_form_schedule',
										'load_new_content'=> true
									)
								)
							);

							?>
							<a class='evo_btn evolb_trigger' <?php echo $help->array_to_html_data( $data_vals_new );?> ><?php _e('Add new Schedule Block','evoss');?></a>
						</p>										
					</div>

					<div style='margin-top:10px'>
						<?php echo EVO()->elements->get_element(array(
							'type'=>'textarea',
							'id'=>'_evosch_notes',
							'height'=>'200px',
							'name'=> __('Additional notes for the schedule.','evoss'),
							'value'=>$EVENT->get_prop('_evosch_notes')
						));?>
					</div>
				</div>
				<?php
				return ob_get_clean();
			}
			function termmeta($term_meta, $var){
				return !empty( $term_meta[$var] ) ? 
					stripslashes(str_replace('"', "'", (esc_attr( $term_meta[$var] )) )) : 
					null;
			}

	// save event post
		function save_event_post($fields_ar, $post_id, $EVENT, $post_data){	
			if(isset($postdata['_evosch_order']) ) $EVENT->set_prop('_evosch_order', $_POST['_evosch_order']);
			if(isset($postdata['_evosch_notes']) ) 
				update_post_meta($EVENT->ID, '_evosch_notes', $_POST['_evosch_notes']);
				
		}

	// appearance
		function appearance_settings($array){
			
			$new[] = array('id'=>'EVOSS','type'=>'hiddensection_open','name'=>'Speakers & Schedule', 'display'=>'none');
			$new[] = array('id'=>'EVOSS','type'=>'fontation','name'=>'Back to list button',
				'variations'=>array(
					array('id'=>'EVOSS_1', 'name'=>'Border Color','type'=>'color', 'default'=>'ffffff'),
					array('id'=>'EVOSS_2', 'name'=>'Background Color','type'=>'color', 'default'=>'6b6b6b'),
				)
			);	
			$new[] = array('id'=>'EVOSS','type'=>'fontation','name'=>'Text Color',
				'variations'=>array(
					array('id'=>'EVOSS_3', 'name'=>'Item name color in list','type'=>'color', 'default'=>'6b6b6b'),
					array('id'=>'EVOSS_4', 'name'=>'Item other text color','type'=>'color', 'default'=>'8c8c8c'),
					array('id'=>'EVOSS_5', 'name'=>'Item section header text color','type'=>'color', 'default'=>'6b6b6b'),
				)
			);$new[] = array('id'=>'EVOSS','type'=>'fontation','name'=>'Individual Item',
				'variations'=>array(
					array('id'=>'EVOSS_6', 'name'=>'Item background color','type'=>'color', 'default'=>'ffffff'),
					array('id'=>'EVOSS_7', 'name'=>'Item background color (hover)','type'=>'color', 'default'=>'fffbf1'),
					array('id'=>'EVOSS_8', 'name'=>'Arrow color','type'=>'color', 'default'=>'141412'),
				)
			);	
			$new[] = array('id'=>'EVOSS','type'=>'hiddensection_close');
			return array_merge($array, $new);
		}

		function dynamic_styles($_existen){
			$new= array(
				array(
					'item'=>'.EVOSS_back_btn',
					'multicss'=>array(
						array('css'=>'color:#$', 'var'=>'EVOSS_1',	'default'=>'ffffff'),
						array('css'=>'background-color:#$', 'var'=>'EVOSS_2',	'default'=>'6b6b6b'),
					)
				),
				array('item'=>'.EVOSS ul li .inner h2','css'=>'color:#$', 'var'=>'EVOSS_3','default'=>'6b6b6b'),
				array('item'=>'.EVOSS ul li .inner p','css'=>'color:#$', 'var'=>'EVOSS_4','default'=>'8c8c8c'),
				array('item'=>'.EVOSS_section','css'=>'color:#$', 'var'=>'EVOSS_5','default'=>'6b6b6b'),
				array('item'=>'.EVOSS ul li .inner','css'=>'background-color:#$', 'var'=>'EVOSS_6','default'=>'ffffff'),
				array('item'=>'.EVOSS ul li .inner:hover','css'=>'background-color:#$', 'var'=>'EVOSS_7','default'=>'fffbf1'),
				array('item'=>'.EVOSS ul li .inner:after','css'=>'color:#$', 'var'=>'EVOSS_8','default'=>'141412'),
			);
			return (is_array($_existen))? array_merge($_existen, $new): $_existen;
		}
		// Font families
		function primary_font($str){
			$str .= ',.evosch_blocks_list .evosch_nav li, 
			    .evosch_oneday_schedule p em.time, 
			    .evosch_oneday_schedule p span b, 
			    .evosch_oneday_schedule p span i,
			    .eventon_list_event .event_description .evosch_oneday_schedule li.date, 
			    .evo_lightbox .evosch_oneday_schedule li.date';
			return $str;
		}
		function secondary_font($str){
			return $str.',.evoss_lightbox .evospk_info,
    			.evosch_oneday_schedule p span span.evoss_show';
		}

	// Language
		function language_additions($_existen){
			$new_ar = array(
				array('type'=>'togheader','name'=>'ADDON: Speaker & Schedule'),
					array('label'=>'Speakers for this event','name'=>'evoss_001'),				
					array('label'=>'Schedule','name'=>'evoss_002'),				
					array('label'=>'Day','name'=>'evoss_003'),				
					array('label'=>'Speakers','var'=>1),				
					array('label'=>'Event Speaker','var'=>1),				
				array('type'=>'togend'),
			);
			return (is_array($_existen))? array_merge($_existen, $new_ar): $_existen;
		}
	
}
new EVOSS_admin();