<?php
/**
* PDF Integration additions to Tickets
*/
class EVOPDF_Ticket{

	public function __construct(){
		add_action('evotx_wc_thankyou_page_end', array($this, 'wc_order_details'),10,1);
	}

	function wc_order_details($order){
		if( $order->get_status() != 'completed') return false;

		$order_id = $order->get_id();

		$media_id = get_post_meta($order_id, '_evopdf_media_id', true);

		if($media_id) $pdf_src = wp_get_attachment_url($media_id);

		if($media_id && $pdf_src):
			?>
			<p>
				<a class='evcal_btn' target='_blank' href='<?php echo $pdf_src;?>'><?php _e('Download Ticket PDF','evotx');?></a>
			</p>
			<?php

		endif;
	}

}