<?php 
/**
 * Admin Ajax
 * @version 0.1
 */
class evobo_admin_ajax{
	public function __construct(){
		$ajax_events = array(	
			'evobo_arrange_block'=>'evobo_arrange_block',
		);
		foreach ( $ajax_events as $ajax_event => $class ) {
			add_action( 'wp_ajax_'.  $ajax_event, array( $this, $class ) );
			add_action( 'wp_ajax_nopriv_'.  $ajax_event, array( $this, $class ) );
		}
	}

// Arrange blocks
	function evobo_arrange_block(){
		$ORDER = $_POST['index'];

		$BLOCKS = new EVOBO_Blocks($_POST['eid'], $_POST['wcid']);			

		if($ORDER && is_array($ORDER) && isset($_POST['eid']) && isset($_POST['wcid'])){			
			$BLOCKS->reorder_blocks($ORDER);
		}

		echo json_encode(array( 
			'json'=> json_decode($BLOCKS->get_backend_block_json(true,true, true)), 
			'status'=>	'good',
			'msg'=>	__('Successfully Updated Blocks','eventon')
		)); exit;
	}

// SUPPROTIVE
	function get_time_format(){
		$wp_time_format = get_option('time_format');
		return (strpos($wp_time_format, 'H')!==false || strpos($wp_time_format, 'G')!==false)? 'H:i':'h:i:A';
	}

}
new evobo_admin_ajax();