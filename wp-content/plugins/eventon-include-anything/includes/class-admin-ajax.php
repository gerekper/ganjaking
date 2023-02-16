<?php
/*
* Admin AJAX
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

			<div class='row'>
				<p><label><?php _e('Event Subtitle','eventon');?></label>
					<input class='F' type="text" name="subtitle" value='<?php echo $EV->get_subtitle();?>'></p>
			</div>
			<div class='row'>
				<?php echo $ajde->wp_admin->html_yesnobtn(array(
					'id'=>'_featured','input'=>true,
					'default'=> ($EV->is_featured()?'yes':'no'),
					'label'=>__('Feature Event','eventon'),
					'nesting'=>true
				));?>
			</div>
			<div class='row'>
				<?php echo $ajde->wp_admin->html_yesnobtn(array(
					'id'=>'_completed','input'=>true,
					'default'=> ($EV->is_completed()?'yes':'no'),
					'label'=>__('Event Completed','eventon'),
					'nesting'=>true
				));?>
			</div>
			<div class='row'>
				<p><label><?php _e('Event Status','eventon');?></label>
				<?php
					$def_event_status = $EV->get_event_status();
					$_status = $EVC->is_repeat_has_data('_status');	
					if(!$_status) $_status = $def_event_status;				
				?>
				<select class='event_status' name='_status'>
					<?php 
					foreach( $EV->get_status_array() as $f=>$v){
						echo "<option value='{$f}' ".( $f==$_status ? 'selected':'').">". $v ."</option>";
					}
					?>					
				</select></p>
			</div>
			<div class='row _status_reason'>
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
				<textarea style='width:100%; display:<?php echo $_status!='scheduled' ? 'block':'none'?>'; name='<?php echo $vari;?>'><?php echo $val;?></textarea>
			</div>
			<div class='row'>
				<p><label><?php _e('Event Location','eventon');?></label>
				<?php
					if($RD = $EVC->is_repeat_has_data('event_location')){
						$location_term_id = $RD;
					}else{
						$location_term_id = $EV->get_location_term_id();
					}					
				?>
				<select name='event_location'><?php $this->get_term_list('event_location',$location_term_id);?></select></p>
			</div>

			<?php
				if($RD = $EVC->is_repeat_has_data('event_organizer')){
					$organizer_term_id = $RD;
				}else{
					$organizer_term_id = $EV->get_organizer_term_id();
				}							
			?>
			<div class='row'>
				<p><label><?php _e('Event Organizer','eventon');?></label>				
				<select name='event_organizer'><?php $this->get_term_list('event_organizer',$organizer_term_id);?></select></p>
			</div>

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
						<p class='row'>
							<label><?php echo $label;?></label>
							<input type="text" name="<?php echo $__field_id;?>" value='<?php echo $event_field_data['value'];?>'>
						</p>
						<?php
						break;
						case 'textarea':
						?>
						<p class='row'>
							<label><?php echo $label;?></label>
							<textarea style='width:100%' name="<?php echo $__field_id;?>"><?php echo $event_field_data['value'];?></textarea>
						</p>
						<?php
						break;
						case 'button':
						?>
						<p class='row'>
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
						</p>
						<?php
						break;
					}
				}

			?>

			<p><a class='evo_btn evorc_save'><?php _e('Save Changes','evorc');?></a>
		</div>
		<?php

		echo json_encode(array(
			'html'=> ob_get_clean(),
			'json'=> $this_data
		));
		exit;
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