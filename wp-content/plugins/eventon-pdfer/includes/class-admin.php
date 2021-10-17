<?php
/**
 * Admin
 * @version 0.1
 */
class evopdf_admin{
	public function __construct(){
		add_action('admin_init', array($this, 'admin_init'));				
	}

	function admin_init(){
		// settings
			add_filter( 'evotix_settings_page_content', array( $this, 'settings_tix' ),10,1);		
			add_filter( 'evors_settings_fields', array( $this, 'settings_rsvp' ),10,1);	

		// Post inclusions
		add_action('evotx_ticketpost_confirmation_end', array($this, 'evotix_post'), 10, 1);	
		add_action('evors_rsvppost_confirmation_end', array($this, 'evorsvp_post'), 10, 1);	

		add_filter('evo_addons_details_list', array($this, 'addons_list_inclusion'), 10, 1);	
	}

	// include reminders in addons list
		function addons_list_inclusion($array){
			$array['eventon-pdfer']= array(
				'id'=>'EVOPDF',
				'name'=>'PDFer',
				'link'=>'http://www.myeventon.com/addons/pdfer',
				'download'=>'http://www.myeventon.com/addons/pdfer',
				'desc'=>'PDF for RSVP and Tickets addons'
			);

			return $array;
		}

	// Post
	// TICKETS
		function evotix_post($order_id){

			$order = new WC_Order( $order_id );	
			
			if( $order->get_status() != 'completed') return false;

			// post type restriction
			$postType = get_post_type($_GET['post']);			
			if( !$postType || $postType != 'evo-tix') return false;

			$pdf_src = false;

			$media_id = get_post_meta($order_id, '_evopdf_media_id', true);

			if($media_id ){
				$pdf_src = wp_get_attachment_url( (int)$media_id );
			}

			if($media_id && $pdf_src):
			?>
			<p>
				<a class='evo_admin_btn btn_prime' target='_blank' href='<?php echo $pdf_src;?>'><?php _e('Download Ticket PDF','evotx');?></a>
				<?php
				$nonce = wp_create_nonce('evopdf_gen_pdf_ajax');
				$pdf = add_query_arg(array(
				    'action' => 'evopdf_gen_pdf',
				    'nonce'=>$nonce,
				    'pid'=>$order_id,
				    'type'=>'ticket',
				    'save'=>true,
				    'aact'=>'ajax',
				), admin_url('admin-ajax.php'));
				?>
				<a class='evo_admin_btn btn_triad'  href='<?php echo $pdf;?>'><?php _e('Re-generate PDF','evotx');?></a>
			</p>
			<?php
			else:
				$nonce = wp_create_nonce('evopdf_gen_pdf_ajax');
				$pdf = add_query_arg(array(
				    'action' => 'evopdf_gen_pdf',
				    'nonce'=>$nonce,
				    'pid'=>$order_id,
				    'type'=>'ticket',
				    'save'=>true,
				    'aact'=>'ajax', // so it refresh page with download pdf link
				), admin_url('admin-ajax.php'));

				?>
				<p><a class='evo_admin_btn btn_prime' target='_blank' href='<?php echo $pdf;?>'><?php _e('Generate Ticket PDF','evotx');?></a></p>
				<?php
			endif;
		}

	// RSVP
		function evorsvp_post($RSVP){
			// post type restriction
			
			if( is_numeric($RSVP)) $RSVP = new EVO_RSVP_CPT( $RSVP);
			
			$pdf_src = false;
			$media_id = $RSVP->get_prop('_evopdf_media_id');

			if($media_id ){
				$pdf_src = wp_get_attachment_url( (int)$media_id );
			}

			if($media_id && $pdf_src):
				?>
				<p>
					<a class='evo_admin_btn btn_prime' target='_blank' href='<?php echo $pdf_src;?>'><?php _e('Download RSVP PDF','evors');?></a>
					<?php
					$nonce = wp_create_nonce('evopdf_gen_pdf_ajax');
					$pdf = add_query_arg(array(
					    'action' => 'evopdf_gen_pdf',
					    'nonce'=>$nonce,
					    'pid'=>$RSVP->ID,
					    'type'=>'rsvp',
					    'save'=>true,
					    'aact'=>'ajax',
					), admin_url('admin-ajax.php'));
					?>
					<a class='evo_admin_btn btn_triad'  href='<?php echo $pdf;?>'><?php _e('Re-generate PDF','evors');?></a>
				</p>
				<?php
			else:

				$nonce = wp_create_nonce('evopdf_gen_pdf_ajax');
				$pdf = add_query_arg(array(
				    'action' => 'evopdf_gen_pdf',
				    'nonce'=>$nonce,
				    'pid'=>$RSVP->rsvp_id,
				    'type'=>'rsvp',
				    'save'=>true,
				    'download_file'=>true,
				    'aact'=>'ajax', // so it refresh page with download pdf link
				), admin_url('admin-ajax.php'));


				?>
				<p><?php _e('PDF File not generated.','evopdf');?></p>
				<p><a class='evo_admin_btn btn_triad' target='' href='<?php echo $pdf;?>'><?php _e('Generate RSVP PDF','evopdf');?></a></p>
				<?php

			endif;
		}

	// settings
		function settings_tix($array){
			$array[] = array(
				'id'=>'evotxpdf',
				'name'=>'PDFer For EventON Ticket',
				'tab_name'=>'PDFer Settings',
				'icon'=>'file',
				'fields'=>array(
					array(
						'id'=>'evopdf_tickets',
						'type'=>'yesno',
						'name'=>'Enable PDF tickets for event tickets',
						'legend'=>'This will generate PDF tickets and attach them to ticket confirmation email.',
					),array(
						'id'=>'evopdf_001',
						'type'=>'note',
						'name'=>'NOTE: Auto generated PDF files will be saved in your media library. This is done so that it can be easily retrieved to resend or download without having to re-generate the PDF file.',
					),
			));
			return $array;
		}
	
		function settings_rsvp($array){
			$array[] = array(
				'id'=>'evotxpdf',
				'name'=>'PDFer For EventON RSVP',
				'tab_name'=>'PDFer Settings',
				'icon'=>'file',
				'fields'=>array(
					array(
						'id'=>'note',
						'type'=>'note',
						'name'=>'NOTE: Once PDF is enabled, only the rsvps created afterward will be effected, not the rsvps already created and sent out. Auto generated PDF files will be saved in your media library. This is done so that it can be easily retrieved to resend or download without having to re-generate the PDF file.'
					),
					array(
						'id'=>'evopdf_rsvp',
						'type'=>'yesno',
						'name'=>'Enable PDF tickets for RSVP confirmation',
						'legend'=>'This will generate PDF rsvp confirmation and attach them to the confirmation email.',
					),
			));
			return $array;
		}
}