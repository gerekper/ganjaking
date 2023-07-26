<?php
/**
 * 	EventON Webhook integration
 *	@version 2.2
 */

class EVOTX_Webhooks{
	public function __construct(){
		
		add_filter('evo_webhook_triggers', array($this, 'add_webhook_triggers'), 10, 1);

		add_action('evotx_order_with_tickets_created',array($this, 'tickets_created'), 10, 2);
		add_filter('evotx_adjust_orderitem_ticket_stockother',array($this, 'ticket_stock_modified'), 10, 7);

		add_filter('evo_webhooks_data', array($this, 'webhook_data'),10);
	}

	function add_webhook_triggers($array){
		$array['tickets_created'] = 'TICKETS: Event TIckets for an Order is created';
		$array['ticket_stock_modified'] = 'TICKETS: Ticket stock is modified';
		return $array;
	}
	function webhook_data($arr){

		$arr['tickets_created']['fields'] = "type, order_id, ticket_numbers";
		$arr['ticket_stock_modified']['fields'] = "type, order_id, order_item_id, reduce_or_restock, event_id, event_repeat_inde ";
		return $arr;
	}
	function tickets_created($order_id, $order_ticket_numbers){

		if( $webhookurl = EVO()->webhooks->is_hook_active('tickets_created' )){
			EVO()->webhooks->send_webhook( $webhookurl, array(
				'type'=>'tickets_created',
				'order_id'=> $order_id,
				'ticket_numbers'=> $order_ticket_numbers,
			));
		}
	}

	function ticket_stock_modified($stock_reduced, $TIX_EVENT, $order, $item_id, $item, $type, $stage){
		if( $webhookurl = EVO()->webhooks->is_hook_active('ticket_stock_modified' )){
			EVO()->webhooks->send_webhook( $webhookurl, array(
				'type'=>'ticket_stock_modified',
				'order_id'=> $order->get_id(),
				'order_item_id'=> $item_id,
				'reduce_or_restock'=> $type,
				'event_id'=> $TIX_EVENT->ID,
				'event_repeat_index'=> $TIX_EVENT->ri
			));
		}
		return $stock_reduced;
	}
}
new EVOTX_Webhooks();