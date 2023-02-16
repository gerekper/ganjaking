<?php
/**
 * 	EventON Webhook integration
 *	@version 2.8
 */

class EVORS_Webhooks{
	public function __construct(){
		
		add_filter('evo_webhook_triggers', array($this, 'add_webhook_triggers'), 10, 1);

		add_action('evors_new_rsvp_saved',array($this, 'new_rsvp'), 10, 4);
		add_action('evors_checkin_guest',array($this, 'status_changed'), 10, 3);
	}

	function add_webhook_triggers($array){
		$array['new_rsvp'] = 'RSVP: When new RSVP is received';
		$array['rsvp_status_changed'] = 'RSVP: When guest checkin status changed';
		return $array;
	}
	function new_rsvp($created_rsvp_id, $args, $RR, $rs_events_class){

		if( $webhookurl = EVO()->webhooks->is_hook_active('new_rsvp' )){
			EVO()->webhooks->send_webhook( $webhookurl, array(
				'type'=>'new_rsvp',
				'new_rsvp_id'=> $created_rsvp_id,
				'rsvp_status'=> $RR->get_rsvp_status(),
				'first_name'=> $RR->first_name(),
				'last_name'=> $RR->last_name(),
				'email'=> $RR->email(),
				'count'=> $RR->count(),
				'event_id'=> $RR->event_id(),
			));
		}
	}

	function status_changed($ID, $status, $RR){
		if( $webhookurl = EVO()->webhooks->is_hook_active('rsvp_status_changed' )){
			EVO()->webhooks->send_webhook( $webhookurl, array(
				'type'=>'rsvp_status_changed',
				'rsvp_id'=> $ID,
				'rsvp_status'=> $RR->get_rsvp_status(),
				'first_name'=> $RR->first_name(),
				'last_name'=> $RR->last_name(),
				'email'=> $RR->email(),
				'count'=> $RR->count(),
				'event_id'=> $RR->event_id(),
			));
		}
	}
}