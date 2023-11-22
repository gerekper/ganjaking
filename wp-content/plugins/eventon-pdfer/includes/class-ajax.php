<?php
/**
 * Admin AJAX
 */

class evopdf_ajax{
	public function __construct(){
		$ajax_events = array(
			'evopdf_gen_pdf'=>'generate_pdf',
		);
		foreach ( $ajax_events as $ajax_event => $class ) {				
			add_action( 'wp_ajax_'.  $ajax_event, array( $this, $class ) );
			add_action( 'wp_ajax_nopriv_'.  $ajax_event, array( $this, $class ) );
		}
	}

	// from admin posts
	// has validation check for downloading file from post
	function generate_pdf(){
		// check if admin and loggedin
		if(!is_admin() && !is_user_logged_in()) die('User not loggedin!');
		// verify nonce
		//if(!wp_verify_nonce($_REQUEST['nonce'], 'evopdf_gen_pdf_ajax')) die('Security Check Failed!');

		// additional action TYpe
		$aact = isset($_REQUEST['aact'])? $_REQUEST['aact']: 'none';

		if(!isset($_REQUEST['pid'])) die('Post ID missing!');
		if(!isset($_REQUEST['type'])) die('Type missing!');
		
		$post_id = (int)$_REQUEST['pid'];


		// Initiate the PDF class
		require_once('class-pdfer.php');
		$pdf = new EVO_PDF_generator();

		$type = isset($_REQUEST['type'])? $_REQUEST['type']:'ticket';
		$content = EVOPDF()->gen_content($type, $post_id);
		$save = (isset($_REQUEST['save']) && $_REQUEST['save'] == true) ? true: false;
		
		$pdf_content = $file = $pdf->generate_pdf(array(
			'content'=> $content, 
			'type'=> $type, 
			'post_id'=> $post_id, 
			'save_to_post'=> $save
		));	

		// Download the pdf file
		if($aact == 'download'){
			header("Content-type: application/pdf");
			header("Content-Disposition: attachment; filename=".$type.".pdf");
			header("Pragma: no-cache");
			header("Expires: 0");
			echo $pdf_content;
			exit;
		}

		if($aact == 'ajax'){
			wp_safe_redirect(  wp_get_referer()  );
			exit;
		}

	}
	

}

new evopdf_ajax();