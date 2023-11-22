<?php
/**
 * @version 0.1
 */

// include autoloader
require_once 'dompdf/autoload.inc.php';

// reference the Dompdf namespace
use Dompdf\Dompdf;


class EVO_PDF_generator{

	function generate_pdf($data){

		extract($data);

		$file_name = ($type=='ticket'? "Ticket_order_{$post_id}.pdf":"RSVP_conf_{$post_id}.pdf");

		// instantiate and use the dompdf class
		$dompdf = new Dompdf();

		//$content = mb_convert_encoding($content, 'HTML-ENTITIES','UTF-8').'ETKİNLİK BİLETİ';
		$dompdf->loadHtml($content, 'UTF-8');

		// (Optional) Setup the paper size and orientation
		
		if($type == 'ticket'){
			$dompdf->setPaper('A4', 'portrait');
		}else{
			$dompdf->setPaper('A4', 'portrait');
		}	
		
		//$dompdf->set_option('defaultPaperSize', 'A4');
		//$dompdf->set_option('defaultPaperOrientation', 'landscape');
		$dompdf->set_option('defaultFont', 'helvetica');
		$dompdf->set_option('isRemoteEnabled', true);
	
		// Render the HTML as PDF
		$dompdf->render();

		// Output the generated PDF to Browser
		//$dompdf->stream();
		
		$output =  $dompdf->output();


		// save the PDF file to the post and media
		if($save_to_post){
			WP_Filesystem();
			global $wp_filesystem;
			$uploads = wp_upload_dir();

			$aq_uploads_dir = AJDE_EVCAL_DIR . '/'. EVOPDF()->plugin_base.  '/files/'; 
			$file_url = $aq_uploads_dir. $file_name;
			$file_url = $file_name;
			file_put_contents( $file_url , $output);

			$file_array = array(
	            'name' => $file_name,
	            'tmp_name' => $file_url
	        );

			$id = media_handle_sideload( $file_array, $post_id );
			//$src = get_attached_file( $id );

			update_post_meta($post_id, '_evopdf_media_id', $id);

			// remove temp file			
			$wp_filesystem->delete( $file_url );
		}

		return $output;
		
      	//$src = wp_get_attachment_url( $id );
	}
}

