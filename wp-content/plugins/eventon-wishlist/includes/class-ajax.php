<?php
/**
 * AJAX Calls
 *
 * @author 		AJDE
 * @category 	Core
 * @version     0.1
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class evowi_ajax{
	public function __construct(){
		$ajax_events = array(
			'evowi_change_wishlist'=>'evowi_change_wishlist',
		);
		foreach ( $ajax_events as $ajax_event => $class ) {				
			add_action( 'wp_ajax_'.  $ajax_event, array( $this, $class ) );
			add_action( 'wp_ajax_nopriv_'.  $ajax_event, array( $this, $class ) );
		}
	}

	function evowi_change_wishlist(){
		if(!is_user_logged_in()){ 

			ob_start();

			$current_page = $_POST['pl'];
				
			$event_id = (int)$_POST['ei'];

			?>
				<div style='text-align:center;padding:20px;'>

					<?php do_action('evowi_nologin_before_content', $event_id);?>

					<p><?php evo_lang_e('You must login to add events to wish list!');?></p>
					<p><a href="<?php echo evo_login_url($current_page);?>" class='evcal_btn'><?php evo_lang_e('Login Now');?></a></p>

					<?php do_action('evowi_nologin_after_content', $event_id);?>

				</div>
			<?php

			echo json_encode(
				array(
					'type'=>'notloggedin',
					'status'=>'bad', 
					'message'=> evo_lang('User not loggedin!'),
					'content'	=> ob_get_clean()
				)
			);	
			exit;
		}

		global $evowi;

		$fnc = new evowi_fnc();

		$userid = get_current_user_id();

		$result = $fnc->change_user_wishlist($_POST['newstatus'], $_POST['ei'], $_POST['ri'], $userid);

		// get new counts
		$wishlist_events = get_option('_evo_wishlist');
		$evOpt = evo_get_options('1');
		$count = $fnc->get_wishlist_count($_POST['ei'], $_POST['ri'], $wishlist_events);

		if( $_POST['newstatus']=='add' ){
			$html = "<span class='evowi_wi_area'>
					<i class='fa ".get_eventON_icon('evcal_evowi_001', 'fa-heart',$evOpt )."'></i>
					<em>".$count."</em>
				</span>".evo_lang('In your wishlist');
		}else{
			$html = "<span class='evowi_wi_area'>
					<i class='fa ".get_eventON_icon('evcal_evowi_002', 'fa-heart-o',$evOpt )."'></i>
					<em>".$count."</em>
				</span>".evo_lang('Add to wishlist');
		}

		echo json_encode(array(
			'status'=> ($result?'good':'bad'), 
			'message'=> ($result?
				( $_POST['newstatus']=='add'?'Added to wishlist':'Removed from wishlist')
				:'Please try again later!'),
			'html'=> $html
		));	
		exit;
	}
}

new evowi_ajax();

?>