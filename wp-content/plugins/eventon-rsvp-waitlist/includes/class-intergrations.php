<?php
/**
 * Intergration with other addons
 * @version 1.1
 */

class EVORSW_Intergrations{

	public $waitlist = false;

	public function __construct(){

		// action user event manager
		add_action('evors_au_eventmanager_statbox', array($this, 'au_em_statbox'), 10, 1);
		add_action('evors_au_eventmanager_attendees_end', array($this, 'au_em_waitlist'), 10, 1);
	}

	// action user
	function au_em_statbox($EVENT){
		$this->waitlist = new EVORSW_Waitlist($EVENT);

		if( $this->waitlist->is_waitlist_active() ){
			$WL_size = $this->waitlist->get_waitlist_size();
			if(!$WL_size) $WL_size = 0;

			echo "<p class='num waitlist'>{$WL_size}<em>". evo_lang('Wailist') ."</em></p>";
			
		}
	}

	function au_em_waitlist($EVENT){
		if( !$this->waitlist) $this->waitlist = new EVORSW_Waitlist($EVENT);

		if( $this->waitlist->is_waitlist_active() ){

			$RSVP_LIST = $EVENT->GET_rsvp_list('waitlist');
			//print_r($RSVP_LIST);

			?>
			<div id='evorsau_attendee_list' class='evors_list evorsau_waitlist'>
				<h4 style='margin:0'><i class='fa fa-clipboard-list marr10'></i> <?php evo_lang_e('Waitlist');?></h4>
			

				<div class='evoau_tile'>
					<?php
					
					if(!empty($RSVP_LIST['y']) && count($RSVP_LIST['y'])>0){
						echo "<ul>";

						foreach($RSVP_LIST['y'] as $_id=>$rsvp){

							$phone = !empty($rsvp['phone'])? $rsvp['phone']:false;
							$_status = (!empty($rsvp['status']))? $rsvp['status']:'check-in';

							?>
							<li data-rsvpid='<?php echo $_id;?>'>
								<?php echo '#'.$_id;?></em>
								<?php echo ' '. $rsvp['name'].' <i style="padding-left:10px">('.$rsvp['email'].( $phone? ' '. evo_lang('PHONE') .':'.$phone:'').')</i>';?>
														
								
								<span class='count'><?php echo $rsvp['count'];?></span>
								<?php 
								// if RSVP have other names show those as well
								if($rsvp['names']!= 'na'):?>
									<span class='other_names'><?php 
										echo implode(', ', $rsvp['names']);
									?></span>
								<?php endif;?>
							</li>
							<?php
						}
						echo "</ul>";
					}else{	
						echo "<p class='noone'>".evo_lang('Waitlist is empty')."</p>";	
					}	
				?>
				</div>	
			</div>

			<?php
		}
	}

}