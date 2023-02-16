<?php 
/**
 * Count down timer intergration with tickets addon
 * @version 1.3
 */
class EVOTX_countdown{
	public function __construct(){
		if(is_admin()){
			add_filter('evocd_timer_expire_options', array($this, 'expire_options'), 10, 2);
		}

		if(!is_admin()){
			add_action('ecocd_timer_expired', array($this, 'timer_expired_action'), 10, 2);
		}

	}
	function expire_options($options, $event_pmv){
		if(!empty($event_pmv['evotx_tix']) && $event_pmv['evotx_tix'][0]=='yes'){
			$options['tx']=__('Make Ticket Out of Stock', 'evotx');
		}		
		return $options;
	}

	// make stock out of stock for ticket enabled events
	function timer_expired_action($ex_ux, $event_pmv){
		if(!empty($event_pmv['evotx_tix']) && $event_pmv['evotx_tix'][0]=='yes' && $ex_ux=='tx' && !empty($event_pmv['tx_woocommerce_product_id'])){
			$wc_ticket_product_id = $event_pmv['tx_woocommerce_product_id'][0];

			update_post_meta($wc_ticket_product_id, '_stock_status', 'outofstock');
		}
	}
}
new EVOTX_countdown();