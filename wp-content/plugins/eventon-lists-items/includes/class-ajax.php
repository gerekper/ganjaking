<?php
/** 
 * EVOLI - ajax
 * @version 0.1
 */
class EVOLI_ajax{
	public function __construct(){
		$ajax_events = array(
				'evoliajax_list'=>'evoliajax_list',
			);
			foreach ( $ajax_events as $ajax_event => $class ) {				
				add_action( 'wp_ajax_'.  $ajax_event, array( $this, $class ) );
				add_action( 'wp_ajax_nopriv_'.  $ajax_event, array( $this, $class ) );
			}
	}
	function evoliajax_list(){

		$status = 'good';
						
		$SC = $_POST['SC'];
		
		$SC[ $SC['cat_type'] ] =  $_POST['termid'];

		$content = EVOLI()->frontend->get_events_list($SC);

		echo json_encode(array(
			'content'=>$content, 'status'=>$status
		));
		exit;
	}
}
new EVOLI_ajax();