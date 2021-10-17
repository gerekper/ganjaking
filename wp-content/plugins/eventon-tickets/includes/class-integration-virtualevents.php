<?php 
/* Virtual Events Integration */

class EVOTX_Virtual_Events{

	public $user_has_tickets = false;
	public $tx_go = false;
	public $evotx_event;
	public $can_sell_tickets = false;

	public function __construct(){
		add_action('evo_editevent_vir_before_after_event', array($this, 'event_edit_options'),10,1);
		add_action('evo_editevent_vir_after_event_end', array($this, 'after_event_end'),10,1);
		add_action('evovp_editevent_vir_pre_event_end', array($this, 'pre_event_end'),10,1);
		

		add_action('evo_vir_initial_setup', array($this, 'initial_setup'),10, 1);

		add_filter('evo_eventcard_vir_txt_cur', array($this, 'pre_text'),10,3);

		add_filter('evo_eventcard_vir_details_bool', array($this, 'card_vir_show'),10, 2);
		add_filter('evo_eventcard_virtual_livenow_html', array($this, 'card_livenow_html'),10,2);
		add_filter('evo_eventcard_vir_after_details', array($this, 'end_content'),10,1);

		add_filter('evo_eventcard_virtual_after_content', array($this, 'post_content'),10,2);

		// virtual plus
		add_filter('evovp_show_signin_box', array($this, 'signin_box'),10,3);
		add_filter('evovp_signin_user', array($this, 'signin_user'),10,3);
		add_filter('evovp_eventcard_virtual_pre_content', array($this, 'pre_event_content'),10,2);

		add_action('evotix_confirmation_email_data', array($this, 'email_include'),10,7);

	}

// ADMIN
	public function event_edit_options($EVENT){		
		echo EVO()->elements->process_multiple_elements(
			array(
				array(
					'type'=>'yesno_btn',
					'id'=>		'_vir_after_tix', 
					'value'=>		$EVENT->get_prop('_vir_after_tix'),
					'input'=>	true,
					'label'=> 	__('User must purchase a ticket to view virtual event information', 'evotx'),
					'tooltip'=> __('Virtual event information will only appear to user has purchased a ticket.','evotx'),
					'tooltip_position'=>'L',
					'afterstatement' =>'_vir_after_tix_as'
				),
				array(
					'type'=>'begin_afterstatement',
					'value'=>	$EVENT->get_prop('_vir_after_tix'),
					'id'=>	'_vir_after_tix_as',
				),
				array(
					'type'=>'yesno_btn',
					'id'=>		'_vir_hide_tcount', 
					'value'=>		$EVENT->get_prop('_vir_hide_tcount'),
					'input'=>	true,
					'label'=> 	__('Hide ticket guest count and checked-in count', 'evotx'),
					'tooltip'=> __('This will hide guest count and checked-in guest count next to live now button.','evotx'),
					'tooltip_position'=>'L',
				),
				array(
					'type'=>'end_afterstatement',
				),
			)
		);
	}
	public function pre_event_end($EVENT){
		echo EVO()->elements->process_multiple_elements(
			array(
				array(
					'type'=>'yesno_btn',
					'id'=>		'_vir_preevent_tix', 
					'value'=>		$EVENT->get_prop('_vir_preevent_tix'),
					'input'=>	true,
					'label'=> 	__('User must purchase a ticket to view pre-event information', 'evotx'),
					'tooltip'=> __('Pre-event information will only appear to users that have purchased a ticket.','evotx'),
					'tooltip_position'=>'L',
				),				
			)
		);
	}
	public function after_event_end($EVENT){
		echo EVO()->elements->process_multiple_elements(
			array(
				array(
					'type'=>'yesno_btn',
					'id'=>		'_vir_afterevent_tix', 
					'value'=>		$EVENT->get_prop('_vir_afterevent_tix'),
					'input'=>	true,
					'label'=> 	__('User must purchase a ticket to view after event information', 'evotx'),
					'tooltip'=> __('After event information will only appear to users that have purchased a ticket.','evotx'),
					'tooltip_position'=>'L',
				),				
			)
		);
	}

// FRONT
	public function initial_setup($EV){

		$this->tx_go = false;
		$this->user_has_tickets = false;
		$this->can_sell_tickets = false;

		if( !$EV->EVENT->check_yn('evotx_tix')) return false;

		if( !$this->check_tix_to_vir( $EV->event)) return false; 
		if($EV->is_past) return false;

		$this->evotx_event = new evotx_event($EV->EVENT->ID, '', $EV->EVENT->ri);

		if( !$this->evotx_event->is_ticket_active()) return false;
		
		$this->tx_go = true;

		// if can sell tickets
		if( !$this->evotx_event->is_stop_selling_now() ) $this->can_sell_tickets = true;
		
		// if current user has purchased ticket
		$this->user_has_tickets = $this->evotx_event->has_user_purchased_tickets();
	}
	public function good_to_go($EVENT = ''){
		if( !empty($EVENT) && !$EVENT->check_yn('evotx_tix')) return false;
		if( !$this->tx_go ) return false;
		return true;
	}
	public function check_tix_to_vir($EE){
		return ( $EE->check_yn('_vir_after_tix') || $EE->get_prop('_vir_show') == 'after_tix') ? true:  false; 
	}

	// pre event text
	public function pre_text($text, $EVENT, $ismod){
		if(  !$this->good_to_go($EVENT) ) return $text;
		
		return '';
	}


	// html adds for live now in eventcard
	public function card_livenow_html($html ,$EVENT){
		// check if show details after ticket purchase
		
		if(  !$this->tx_go ) return $html;

		// show ticket guest and checked in count
			if( !$EVENT->check_yn('_vir_hide_tcount') ){
				$GL = $this->evotx_event->get_guest_list();
				if($GL) extract($GL);
				
				if($GL){
					$html .= "<span class='evo_live_now_tag evotx_virtual_guests'>{$count} ". evo_lang('Guests')."</span>";
					if( !empty($checked) && $checked>0) 
						$html .="<span class='evo_live_now_tag evotx_virtual_checked'>{$checked} ". evo_lang('Signed in') ."</span>";
				}
			}

		return $html;
	}

	// at the end content
	public function end_content($EV){

		if( $EV->EVENT->virtual_type() == 'jitsi' && $EV->_is_user_moderator) return false;
		if( !$this->good_to_go($EV->EVENT)) return false;

		$content = '';

		
		if( $this->can_sell_tickets){
			// if user has tickets
			if( $this->user_has_tickets){
				$content.= "<span class='evotx_virtual_purchase hasticket evo_vir_confim' style=''>". evo_lang('You have purchased a ticket for this event') ."!</span>";
			}else{
				// if user is not moderator
				if( !$EV->_is_user_moderator){
					$content.= "<span class='evotx_virtual_purchase' data-vir_tix='y' style=''>". evo_lang('Purchase ticket now to join'). "!</span>";
				}
			}
			
		}else{
			$content.= "<span class='evotx_virtual_purchase' style=''>". evo_lang('Ticket sales are closed now') ."!</span>";
		}

		if( !empty($content)) echo "<div style='padding-top:10px'>". $content. "</div>";
	}

	// only alter passed value of true
	function card_vir_show($bool, $EV){
		// jitsi mod
		if( $EV->EVENT->virtual_type() == 'jitsi' && $EV->_is_user_moderator) return $bool;

		if(!$this->tx_go) return $bool;
		
		return ( $bool && $this->tx_go && $this->user_has_tickets) ? true : false;
	}

	// AFTER event content
	public function post_content($content, $EVENT){
		
		if(!$EVENT->check_yn('_vir_afterevent_tix')) return $content;

		// if user must purchase ticket to see post content and has not purchased
		if( $this->user_has_tickets) return $content;

		return  evo_lang('Event has already taken place');
	}

// virtual plus 
	public function pre_event_content($html, $EV){
		if( $EV->event->check_yn('_vir_preevent_tix')){
			return $this->user_has_tickets ? $html : '';
		}

		return $html;
	}
	public function signin_user($classdata, $EE, $PP){

		if( !$this->check_tix_to_vir($EE) ) return $classdata;

		$current_user = wp_get_current_user();
		if(!$current_user) return $classdata;

		$TIX = new evotx_event( $EE->ID, '', $EE->ri);

		// check if loggedin user has purchased ticket
		$purchased_tix = $TIX->get_ticket_post_id_by_uid( $current_user->ID);
		if(!$purchased_tix) return $classdata;

		update_post_meta($purchased_tix, 'signin', 'y');

		$classdata['force'] = 'yy';

		return $classdata;


	}
	public function signin_box($bool, $EE, $current_user){
		if( !$this->check_tix_to_vir($EE) ) return $bool;

		// check if current user has signedin
		$TIX = new evotx_event( $EE->ID, '', $EE->ri);

		if( $TIX->is_user_signedin( $current_user->ID )) return false;

		return true;
	}

// EMAIL
	function email_include($ticket_item_id, $ticket_pmv,$styles,$ticket_number, $tix_holder_index,$event_id,$EVENT){

		// if show after bought ticket or it virtual info is always visible
		if( $this->check_tix_to_vir($EVENT) || $EVENT->get_prop('_vir_show') == 'always'){

			$eventtx = new evotx_event($EVENT->ID, '', $EVENT->ri);
			if( !$eventtx->is_ticket_active()) return;

			$link = $EVENT->virtual_url();
			if(!$link) return;

			$pass = ($v_pass = $EVENT->get_virtual_pass() ) ? ' ('. evo_lang('Pass').': '.$v_pass.')' :'';
			
			?>			
			<div>
				<p style="<?php echo $styles['005'].$styles['pb5'].$styles['pt10'].$styles['wbbw'];?>"><a href='<?php echo $link;?>'><?php echo $link;?></a> <?php echo $pass;?></p>
				<p style="<?php echo $styles['004'].$styles['pb5'];?>"><?php echo evo_lang( 'Virtual Event Access Information');?></p>
			</div>		
			<?php 

		}


	}
}
new EVOTX_Virtual_Events();