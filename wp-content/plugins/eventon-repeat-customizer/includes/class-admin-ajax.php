<?php
/*
* Admin AJAX
* @version 1.0.2
*/

class EVORC_Admin_Ajax{
	public function __construct(){
		$ajax_events = array(
			'evorc_customizer'=>'evorc_customizer',
			'evorc_get_ri_data'=>'evorc_get_ri_data',
			'evorc_save_ri_data'=>'evorc_save_ri_data',
		);
		foreach ( $ajax_events as $ajax_event => $class ) {				
			add_action( 'wp_ajax_'.  $ajax_event, array( $this, $class ) );
			add_action( 'wp_ajax_nopriv_'.  $ajax_event, array( $this, $class ) );
		}
	}

	// RI selector
	function evorc_customizer(){
		$EV = new EVO_Event( (int)$_POST['event_id']);
		$intervals = $EV->get_prop('repeat_intervals');

		$RC_data = $EV->get_prop('_repeat_data');
		$RC_data = is_array($RC_data)? $RC_data: array();

		ob_start();
		$DD = EVO()->calendar->DD;

		echo "<div class='evorc_customizer' style='padding:5px 0'><h3 style='padding-bottom:10px'>". __('Select a repeating interval to customize'). "</h3>";

		echo "<div class='evorc_ris' data-eid='$EV->ID'>";
		foreach($intervals as $I=>$D){
			$add = array_key_exists($I, $RC_data)? __('customized','evorc') :'';


			$DD->setTimestamp( $D[0]);
			echo "<p class='ri_row evorc_ri_row ".($I == 0?'ev ':'').( !empty($add)?'cus':'')."' data-ri='{$I}'><b>#{$I}</b> <span>".$DD->format('Y-m-d H:i') ."</span>";

			if($I == 0) $add = __('Initial','evorc');
			$DD->setTimestamp( $D[1]);
			echo " - <span data-ri='{$I}'>".$DD->format('Y-m-d H:i') ."</span><em>". $add."</em></p>";
		}
		echo "</div>";
		?>		
		</div>
		<?php
		echo json_encode(array(
			'test'=>$RC_data,
			'html'=> ob_get_clean()
		));
		exit;
	}

	// data for individual RI
	function evorc_get_ri_data(){

		global $ajde;
		$RI = (int)$_POST['ri'];
		$EVC = new EVORC_Event( (int)$_POST['eid'], $RI);
		$EV = $EVC->event;
		$intervals = $EV->get_prop('repeat_intervals');
		$RC_data = $EVC->get_all_repeat_data();

		$this_data = isset($RC_data[ $RI ])? $RC_data[ $RI ]: false;

		$DD = EVO()->calendar->DD;
		$time = '';
		$DD->setTimestamp( $intervals[$RI][0] ); $time .= $DD->format('Y-m-d H:i');
		$DD->setTimestamp( $intervals[$RI][1] ); $time .= ' - '. $DD->format('Y-m-d H:i');

		$evo_opt_1 = EVO()->frontend->evo_options;


		ob_start();
		?>
		<div class='evorc_ri_item_data form' data-eid='<?php echo $EV->ID;?>' data-ri='<?php echo $RI;?>'>
			<p class='back_to_all_repeats'><i class='fa fa-chevron-circle-left'></i> <?php _e('All Repeats','evorc');?></p>
			<p class='current_repeat'><b>#<?php echo $RI;?></b><?php echo $time;?></p>

			<?php

			// event status value
				$def_event_status = $EV->get_event_status();
				$_status = $EVC->is_repeat_has_data('_status');	
				if(!$_status) $_status = $def_event_status;

			// event location
				if($RD = $EVC->is_repeat_has_data('event_location')){
					$location_term_id = $RD;
				}else{
					$location_term_id = $EV->get_location_term_id();
				}	

			// event organizer
				if($RRO = $EVC->is_repeat_has_data('event_organizer')){
					$organizer_term_id = $RRO;
				}else{
					$organizer_term_id = $EV->get_organizer_term_id();
				}	

			echo EVO()->elements->process_multiple_elements(
				apply_filters('evorc_event_edit_fields_array', array( 
					array(
						'type'=>'text',
						'name'=> __('Event Title','eventon'),
						'id'=>'_title',
						'value'=> $EV->get_title(),
					),
					array(
						'type'=>'text',
						'name'=> __('Event Subtitle','eventon'),
						'id'=>'subtitle',
						'value'=> $EV->get_subtitle(),
					),
					array(
						'type'=>'yesno_btn',
						'id'=>'_featured','input'=>true,
						'default'=> ($EV->is_featured()?'yes':'no'),
						'value'=> ($EV->is_featured()?'yes':'no'),
						'label'=>__('Feature Event','eventon') ,
						'nesting'=>'row'
					),
					array(
						'type'=>'yesno_btn',
						'id'=>'_completed','input'=>true,
						'default'=> ($EV->is_completed()?'yes':'no'),
						'value'=> ($EV->is_completed()?'yes':'no'),
						'label'=>__('Event Completed','eventon'),
						'nesting'=>'row'
					),
					array(
						'type'=>'dropdown',
						'id'=>'_status',
						'row_class'=>'evorc_ev_status',
						'value'=> $_status,
						'options'=> $EV->get_status_array(),
						'name'=> __('Event Status','eventon')
					),
					array(
						'type'=>'custom_code',
						'content'=> $this->custom_html($_status, $EVC)
					),
					array(
						'type'=>'dropdown',
						'id'=>'event_location',
						'value'=> $location_term_id,
						'options'=> $this->get_term_items_array('event_location'),
						'name'=> __('Event Location','eventon')
					),
					array(
						'type'=>'dropdown',
						'id'=>'event_organizer',
						'value'=> $organizer_term_id,
						'options'=> $this->get_term_items_array('event_organizer'),
						'name'=> __('Event Organizer','eventon')
					),
					array(
						'type'=>'colorpicker',
						'id'=>'evcal_event_color',
						'value'=> $EV->get_hex(),
						'name'=> __('Event Color','eventon')
					),
					array(
						'type'=>'text',
						'id'=>'_vir_url',
						'value'=> $EV->get_virtual_url(),
						'name'=> __('Virtual Event URL','eventon')
					),
					array(
						'type'=>'text',
						'id'=>'_vir_pass',
						'value'=> $EV->get_virtual_pass(),
						'name'=> __('Virtual Event Pass','eventon')
					),
					array(
						'type'=>'custom_code',
						'content'=> $this->custom_html_img($EV, $EVC)
					),
				),$EV, $EVC)
			);

			?>			
			

			<?php
				$_cmf_count = evo_retrieve_cmd_count($evo_opt_1);
				
				for($x =1; $x<$_cmf_count+1; $x++){
					if(!eventon_is_custom_meta_field_good($x)) continue;
					if( !isset($evo_opt_1['evcal_ec_f'.$x.'a1'])) continue;
					if( !isset($evo_opt_1['evcal_ec_f'.$x.'a2'])) continue;



					$label = $evo_opt_1['evcal_ec_f'.$x.'a1'].' <em>'. __('Custom Field','eventon')." #{$x}</em>";
					$field_type = $evo_opt_1['evcal_ec_f'.$x.'a2'];

					$event_field_data = $EV->get_custom_data($x);
					$__field_id = '_evcal_ec_f'.$x.'a1_cus';

					switch($field_type){
						case 'text':
							?>
							<p class='row txt'>
								<label><?php echo $label;?></label>
								<input type="text" name="<?php echo $__field_id;?>" value='<?php echo $event_field_data['value'];?>'>
							</p>
							<?php
						break;
						case 'textarea':						
						case 'textarea_basic':						
							?>
							<p class='row txta'>
								<label><?php echo $label;?></label>
								<textarea style='width:100%' name="<?php echo $__field_id;?>"><?php echo $event_field_data['value'];?></textarea>
							</p>
							<?php
						break;
						case 'button':
							?>
							<div class='row but'>
								<label><?php echo $label;?></label>
								<input style='margin-bottom: 5px;' type="text" name="<?php echo $__field_id;?>" value='<?php echo $event_field_data['value'];?>' placeholder='<?php _e('Button Text','eventon');?>'>
								<input type="text" name="_evcal_ec_f<?php echo $x;?>a1_cusL" value='<?php echo $event_field_data['valueL'];?>' placeholder='<?php _e('Button Link','eventon');?>'>
								<?php echo $ajde->wp_admin->html_yesnobtn(array(
									'id'=>'_evcal_ec_f'.$x . '_onw',
									'input'=>true,
									'var'=> $event_field_data['target'],
									'label'=>__('Open in New window','eventon'),
									'nesting'=>true
								));?>
							</div>
							<?php
						break;
					}
				}

			?>

			<p class='action_row'><a class='evo_btn evorc_save'><?php _e('Save Changes','evorc');?></a>
		</div>
		<?php

		echo json_encode(array(
			'html'=> ob_get_clean(),
			'json'=> $this_data
		));
		exit;
	}	
		public function custom_html_img($EV){
			ob_start();
			?>
			<div class='row event_image'>
				<p><label><?php _e('Event Image','eventon');?></label></p>
				<div class='evorc_img_holder'>
					<div class='evorc_event_image_holder'>
						<?php

						$images = $EV->get_image_urls();

						if($images && isset($images['thumbnail']) && isset($images['id'])){
							echo "<span><input type='hidden' name='event_image' value='{$images['id']}'/><b class='remove_event_add_img'>X</b><img title='' src='{$images['thumbnail']}'></span>";
						}

						?>
					</div>
					<a class='evo_admin_btn btn_triad evorc_select_image'><?php _e('Select Event Image','eventon');?></a>
				</div>

			</div>
			<?php return ob_get_clean();
		}

		public function custom_html($_status, $EVC){
			ob_start();
			?>
			<div class='row _status_reason' style='display:<?php echo $_status!='scheduled' ? 'block':'none'?>'>
				<?php 
				$R = array(
					'cancelled'=> 		array('_cancel_reason', __('Reason for cancelling','evorc') ),
					'movedonline'=> 	array('_movedonline_reason', __('More details for online event','evorc') ),
					'postponed'=> 		array('_postponed_reason', __('More details about postpone','evorc') ),
					'rescheduled'=> 	array('_rescheduled_reason', __('More details about reschedule','evorc') ),
				);
				$vari = isset($R[$_status]) ? $R[$_status][0]:'';
				$val = $EVC->is_repeat_has_data($vari);	

				?>
				<p><label data-l="<?php echo htmlentities(json_encode($R));?>"><?php echo isset($R[$_status]) ? $R[$_status][1]:'';	?></label>
				<textarea style='width:100%;'; name='<?php echo $vari;?>'><?php echo $val;?></textarea>
			</div>
			<?php 
			return ob_get_clean();
		}


	// SAVE
		function evorc_save_ri_data(){
			$RI = (int)$_POST['ri'];
			$EV = new EVORC_Event( (int)$_POST['eid'], $RI);

			// sanitize
			$D = array();
			foreach($_POST['D'] as $F=>$V){
				$D[$F] = sanitize_text_field($V);
			}

			$O = $EV->save_one_repeat_data( $D);

			echo json_encode(array(
				'status'=> 'good',
				'msg'=> __('Successfully Saved Customized Data','evorc')
			));
			exit;
		}

	// SUPPORT
		public function get_term_items_array($tax){
			$terms = get_terms(
				$tax,
				array(
					'orderby'           => 'name', 
				    'order'             => 'ASC',
				    'hide_empty'=>false
				) 
			);
			if(is_array($terms) && count($terms)>0){
				$o = array(
					''=>'None'
				);
				foreach ( $terms as $term ) $o[ $term->term_id] = $term->name;

				return $o;
			}
			return array();
		}
		function get_term_list($tax, $term_id){
			$terms = get_terms(
				$tax,
				array(
					'orderby'           => 'name', 
				    'order'             => 'ASC',
				    'hide_empty'=>false
				) 
			);
			if(count($terms)>0){	
				foreach ( $terms as $term ) {
					$selected = ($term_id && $term->term_id == $term_id)? 'selected="selected"':'';
					?><option <?php echo $selected;?> value="<?php echo $term->term_id;?>"><?php echo $term->name;?></option><?php
				}
			}
		}


}
new EVORC_Admin_Ajax();