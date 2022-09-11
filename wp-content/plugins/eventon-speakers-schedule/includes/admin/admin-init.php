<?php
/**
 * Admin settings class
 *
 * @author 		AJDE
 * @category 	Admin
 * @package 	eventon-SS/classes
 * @version     0.8
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class EVOSS_admin{
	
	function __construct(){
		$this->opt = $termMeta = get_option( "evo_tax_meta");
		add_action('admin_init', array($this, 'admin_init'));
		//add_action( 'admin_menu', array( $this, 'menu' ),9);
	}

	// INITIATE
		function admin_init(){
			global $evo_speak;

			$this->evopt1 = get_option('evcal_options_evcal_1');

			add_filter('eventon_event_metaboxs',array($this, 'event_metabox'), 10, 1);
			add_action('eventon_save_meta', array($this, 'save_event_post'), 10, 2);

			// language
			add_filter('eventon_settings_lang_tab_content', array($this, 'language_additions'), 10, 1);
			add_filter( 'eventon_appearance_add', array($this, 'appearance_settings' ), 10, 1);
			add_filter( 'eventon_inline_styles_array',array($this, 'dynamic_styles') , 10, 1);
			add_filter( 'evo_styles_primary_font',array($this,'primary_font') ,10, 1);
			add_filter( 'evo_styles_secondary_font',array($this,'secondary_font') ,10, 1);

			wp_register_script('evoss_script_b',$evo_speak->assets_path.'SS_script_b.js', array('jquery','jquery-ui-sortable'), $evo_speak->version, true );
			wp_localize_script( 
				'evoss_script_b', 
				'evoss_ajax_script', 
				array( 
					'evoss_ajaxurl' => admin_url( 'admin-ajax.php' ) , 
					'postnonce' => wp_create_nonce( 'evoss_nonce' )
				)
			);
			wp_register_style( 'evoss_styles_b',$evo_speak->assets_path.'SS_styles_b.css');
			wp_enqueue_style( 'evoss_styles_b');

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
		}

	// Add speakers to filtering tax in settings
		function filtering_options($event_type_options){
			$event_type_options['event_speaker'] = 'Event Speaker';
			return $event_type_options;
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
		function event_metabox($array){
			$array[] = array(
				'id'=>'ev_speakers',
				'name'=>__('Event Speakers','eventon'),
				'variation'=>'customfield',	
				'hiddenVal'=>'',	
				'iconURL'=>get_eventON_icon('evcal__evospk_001', 'fa-coffee',$this->evopt1 ),
				'iconPOS'=>'',
				'type'=>'code',
				'content'=>$this->content(),
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
				'content'=>$this->content_sch(),
				'slug'=>'ev_schedule'
			);
			return $array;
		}

		// speaker meta box content
		function content(){
			global $post, $ajde, $evo_speak;

			wp_enqueue_script('evoss_script_b');
			
			$p_id = isset($_REQUEST['post'])? $_REQUEST['post']: $post->ID;
			$ev_vals = get_post_custom($p_id);

			ob_start();
			?>
			<div class='evcal_data_block_style1' data-eventid='<?php echo $p_id;?>'>
				<div class='evcal_db_data'>
					
					<div class='evo_tax_saved_terms evo_spk_terms'>
						<?php
							$speaker_terms = wp_get_post_terms($p_id, 'event_speaker');
							$existing_tax_ids = array();

							// if terms exists
							if ( $speaker_terms && ! is_wp_error( $speaker_terms ) ){

								$termMeta = get_option( "evo_tax_meta");

								foreach($speaker_terms as $speakerTerm){
									$existing_tax_ids[] = $speakerTerm->term_id;
									echo $evo_speak->functions->get_selected_item_html($speakerTerm, $termMeta);
								}
								echo "<div class='clear'></div>";
							}
						?>
					</div>
					<?php
						// get all available tax terms
						$existingTerms = get_terms('event_speaker', array('hide_empty'=>false) );
					?>	
					<div class='evcal_speaker_data_section'>
						<?php
							// used data
								$tax_name = __('Event Speaker','eventon');

							// initial message
								if(count($existing_tax_ids)==0 && count($existingTerms)==0):?>
									<p style='text-align:center'><?php _e('To get started, please add new event speakers to this event.','eventon');?></p>
							<?php elseif(count($existing_tax_ids)==0 &&  count($existingTerms)>0):?>
								<p style='text-align:center'><?php _e('To get started, please add new event speakers or select from the list.','eventon');?></p>
							<?php endif;
						
							if(count($existingTerms)>0):
							// list of all terms
						?>
							<div class='evo_meta_inside_row'>
								<p class='evospk_btns'>
									<a class='evo_btn'><?php _e('Select From List','eventon');?> <?php echo $tax_name;?></a></p>
							</div>
							<div class='evo_tax_list_terms sections' style='display:none'>
								<p><?php printf(__('Select %s(s) from the previously saved list.','eventon'),$tax_name);?></p>							
								<p class='evo_tax_list_terms_items'>
								<?php	
									$selectedSpeakers = array();
									foreach ( $existingTerms as $term ) {
										$checked = (count($existing_tax_ids)>0 && in_array($term->term_id, $existing_tax_ids))?
											'dot-circle-o': 'circle-o';
										echo $evo_speak->functions->get_tax_select_list($term, $checked);
										if($checked=='dot-circle-o') $selectedSpeakers[] = $term->term_id;
									}
								?>
									<input type="hidden" class='evo_tax_selected_list_values' name='event_speakers' value='<?php echo implode(',',$selectedSpeakers);?>'/>
								</p>
								
								<p><span class="evo_tax_list_terms_items_save evo_btn" data-eventid='<?php echo $p_id;?>'><?php _e('Save Changes','eventon');?></span> <i><a style='color:#afafaf' href='<?php echo admin_url( 'edit-tags.php?taxonomy=event_speaker&post_type=ajde_events' );?>'><?php _e('Edit/delete items from the list','eventon');?></a></i></p>
							</div>
						<?php endif;?>

						<div class='evo_meta_inside_row'>
							<p class='evospk_btns'>								
								<a class='evospk_open_speaker_form evo_btn ajde_popup_trig' data-dynamic_c='1' 
								data-content_id='evospk_new_block_form'
								data-popc='evospk_new_block'><?php _e('Add new','eventon');?> <?php echo $tax_name;?></a>
							</p>
						</div>
						<?php

							// use global AJDE library for lightbox the submission form
							global $ajde;
							$ajde->wp_admin->lightbox_content(array(
								'content'=>"<p class='evo_lightbox_loading'></p>",
								'class'=>'evospk_new_block',
								'title'=>__('Enter Speaker Information ','eventon'),
								'outside_click'=>false
							));
						?>
							
						<!-- Lightbox Content -->
						<div id='evospk_new_block_form' style='display:none'>
							<div class='evo_tax_entry evoselectfield_saved_data  sections'>
							<?php
								foreach($evo_speak->functions->speaker_fields() as $field=>$var){
									if($field=='evo_spk_img') continue;

									if($field=='evo_speaker_desc'):
										?>
										<p><textarea class='evoss_field <?php echo $var[0];?>' name="<?php echo $field;?>" rows="4" style='width:100%'></textarea><label for='<?php echo $field;?>'><?php echo $var[1];?></label></p>
										<?php
										continue;
									endif;
									?>
									<p><input type='text' class='evoss_field <?php echo $var[0];?>' name='<?php echo $field;?>' value="" style='width:100%' placeholder='<?php echo !empty($var[2])? $var[2]:'';?>'/><label for='<?php echo $field;?>'><?php echo $var[1];?></label></p>
									<?php
								}
							?>
							<div class='evo_metafield_image' style='padding-top:10px'>				
								<p >
									<input id='evo_speakerIMGID' class='evoss_field evo_spk_img custom_upload_image evo_meta_img' name="evo_spk_img" type="hidden" value="" /> 
		                    		<input class="custom_upload_image_button chooseimg evo_spk_img_btn" data-txt='<?php _e('Remove Image','eventon');?>' type="button" value="Select Image" /><br/>
		                    		<span class='evoss_image_src image_src'>
		                    			<img class='evospk_profile_image' src='' style='display:none'/>
		                    		</span>
		                    		<label><?php _e('Event Speaker Image','eventon');?></label>
		                    	</p>
		                    </div>
		                    <p>
		                    	<span class="evoss_add_new_speaker evo_btn" data-eventid='<?php echo $p_id;?>' data-termid=''><?php _e('Save Speaker','eventon');?></span>
		                    	<?php /*<span class="evoss_reset evo_btn" style='opacity:0.6'><?php _e('Reset Form','eventon');?></span>*/?>
		                    </p>
							</div>
						</div><!-- endform-->
						
					</div>
					<p class="evo_tax_msg"></p>
				</div>
			</div>
			<?php
			return ob_get_clean();
		}

		// Schedule
		function content_sch(){
			global $evo_speak, $post;
			
			$p_id = isset($_REQUEST['post'])? $_REQUEST['post']: $post->ID;
			$ev_vals = get_post_custom($p_id);
			$name = 'Schedule Block';

			ob_start();			
			?>
			<div class='evcal_data_block_style1' >
				<div class='evcal_db_data'>
					<?php
					// initial requirement test
					?>
					<div class='evo_sch_blocks'>
						<?php
							//delete_post_meta($p_id, '_sch_blocks');
							$blocks = !empty($ev_vals['_sch_blocks'])? unserialize($ev_vals['_sch_blocks'][0]): false;

							echo "<ul class='evosch_blocks_list' data-eventid='{$p_id}'>";
							// if terms exists
							if ( $blocks ){
								//print_r($blocks);
								echo $evo_speak->functions->get_schedule_html($blocks);
							}
							echo "</ul>";
							echo "<div class='clear'></div>";
						?>
						<input type='hidden' name='_evosch_order' value=''/>
					</div>
					<div class='evcal_speaker_data_section'>
						<div class='evo_meta_inside_row'>
							<p class='evospk_btns'>
								<a class='evosch_open_schedule_form evo_btn ajde_popup_trig' data-eventid='<?php echo $p_id;?>' data-popc='evosch_new_block'><?php _e('Add new','eventon');?> <?php echo $name;?></a></p>
						</div>
						<?php

							// use global AJDE library for lightbox the submission form
							global $ajde;
							$ajde->wp_admin->lightbox_content(array(
								'preloading'=>true,
								'content'=> "<p class='evo_lightbox_loading'></p>",
								'class'=>'evosch_new_block',
								'title'=>__('Enter Event Schedule Block Information ','eventon')
							));

						?>						
					</div>
					<p class="evo_tax_msg"></p>
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
		function save_event_post($fields, $post_id){	
			if(isset($_POST['_evosch_order']))
				update_post_meta($post_id, '_evosch_order', $_POST['_evosch_order']);		
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