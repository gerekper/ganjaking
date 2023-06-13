<?php
/**
 * Wait list extension for event
 * @version 1.0
 */

class EVORSW_Waitlist{
	private $RSVP;
	private $ri;
	public function __construct($EVENT, $RI=0){
		$this->RSVP = $EVENT;
		if( is_numeric($EVENT)) $this->RSVP = new EVORS_Event( $EVENT, $RI);

		if(!$this->RSVP) return false;

		$this->ri = $RI;
	}

	function is_waitlist_active(){
		return $this->RSVP->event->check_yn('_evorsw_waitlist_on');
	}
	

	// check if event's rsvp capacity set and if its reached
	function is_capacity_reached(){

		// if there is no capacity limit.  
		if(!$this->RSVP->is_capacity_limit_set()) return false;
		$remaining_rsvp = $this->RSVP->remaining_rsvp();
		return $remaining_rsvp>0? false: true;

	}

	function get_waitlist($ri = 'all'){
		$meta_query = array(
			array('key' => 'rsvp_type','value' => 'waitlist'),
			array('key' => 'e_id','value' => $this->RSVP->event->ID),
		);

		if($ri != 'all')
			$meta_query[] = array('key' => 'repeat_interval','value' => $ri);

		$WLs = new WP_Query( array(
			'posts_per_page'=>-1,
			'post_type' => 'evo-rsvp',
			'orderby'=>'date', 'order'=>'ASC',
			'meta_query' => $meta_query
		));

		return ($WLs->have_posts()) ? $WLs : false;
	}

	function get_waitlist_size(){
		$WL = $this->get_waitlist($this->ri);

		if(!$WL) return false;

		$C = 0;		
		foreach($WL->posts as $post){
			$R = new EVO_RSVP_CPT($post->ID);
			$C += $R->count();
		}

		return $C;
	}

	// Offer the available space to waitlist guest
	function offer_space_to_waitlist($available_spaces, $WL = null){
		if( empty($WL) ) $WL = $this->get_waitlist($this->ri);

		$total_spaces_offered_waitlist = 0;
		$space_offered_to_someone = false;

		foreach($WL->posts as $post){
			if($available_spaces== 0) continue;
			$RR = new EVO_RSVP_CPT($post->ID);

			$this_rsvp_spaces = $RR->count();

			// if this rsvp fits available space offer them RSVP
			if($available_spaces >= $this_rsvp_spaces ){

				// add this guest to event attendee list
				$this->add_to_event_attendee_list($RR);

				// EMAILING
					// Send rsvp confirmation email
					EVORS()->email->send_email(array(
						'rsvp_id'=> $RR->ID,
					), 'confirmation');

					// notify attendee
					EVORS()->email->send_email(array(
						'rsvp_id'=> $RR->ID,
						'notice_title'=> evo_lang('You have been offered confirmed space!'),
						'notice_message'=> evo_lang('Your waitlist status has been moved to confirmed. We look forward to seeing you at the event.')
					), 'attendee_notification');

					// Admin Notification
					EVORS()->email->send_email( array(
						'rsvp_id'=> $RR->ID,
						'notice_title'=> evo_lang('Attendee Offered Space'),
						'notice_message'=> evo_lang('Waitlist attendee has been offered space to the event')
					),
					'notification');

				$available_spaces = $available_spaces - $this_rsvp_spaces;
				$space_offered_to_someone = true;
				$total_spaces_offered_waitlist += $this_rsvp_spaces;
			}
		}

		// if the space was offered to someone else
		if($space_offered_to_someone){
			// sync counts
			$this->RSVP->sync_rsvp_count('waitlist_refresh');
		}
		
		return $total_spaces_offered_waitlist;

	}

	// Add an attendee to waitlist
	function add_to_waitlist($RR){
		$RR->set_prop( 'status', 'waitlist');
		$RR->set_prop('rsvp_type','waitlist');

		// make a note
			$RR->create_note('Added to waitlist.','na');

		// Notify attendee -- admin is notification is modified in class-frontend
			EVORS()->email->send_email(
				array(
					'rsvp_id'=> $RR->ID,
					'notice_title'=> evo_lang('Added to waitlist'), 
					'notice_message'=> evo_lang('You have been added to our waitlist. You will be offered space as soon as ample space open up'),
				), 'attendee_notification'
			);
	}

	//remove from waitlist and add to event attendee list
	function add_to_event_attendee_list($RR, $uid=''){
		$RR->set_prop( 'status', 'check-in');
		$RR->set_prop('rsvp_type','normal');

		if(empty($uid)) $uid = 'na';

		// make a note
			$RR->create_note('Moved out of waitlist & added to event attendees list.', $uid);
	}	

	// remove from waitlist and trash RSVP
	// when guest chose to be removed from waitlist
	function remove_from_waitlist($RR){

	}

	// sync attendance count on RSVP object
	function sync_rsvp_count(){
		$this->RSVP->sync_rsvp_count();
	}
	
}