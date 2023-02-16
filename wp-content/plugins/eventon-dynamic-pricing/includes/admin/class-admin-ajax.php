<?php
/**
 * Admin Ajax
 * @version 0.1
 */

class evodp_admin_ajax{
	public function __construct(){
		$ajax_events = array(
			'add_new_time_block'=>'add_new_time_block',
			'get_form'=>'get_form',
		);
		foreach ( $ajax_events as $ajax_event => $class ) {
			add_action( 'wp_ajax_'.  $ajax_event, array( $this, $class ) );
			add_action( 'wp_ajax_nopriv_'.  $ajax_event, array( $this, $class ) );
		}
	}


	// get add or edit form for blocks
	function get_form(){		

		ob_start();

		// if unix sent
		$dates_sent = false;
		if(!empty($_POST['start']) && !empty($_POST['end'])){
			
			$fnc = new evodp_fnc();
			$fnc->set_timezone();

			$dates_sent = true;
		}

		$block_key = $_POST['block']=='una'? '_evodp_una':'_evodp_prices';

		?>
		<div class="evodp_add_una_block evodp_item_block_container" >
			<?php if($_POST['block'] == 'tbp'):
				
				$__woo_currencySYM = get_woocommerce_currency_symbol();

				$event_start = get_post_meta($_POST['eid'], 'evcal_srow',true);
				$event_end = get_post_meta($_POST['eid'], 'evcal_erow',true);

				$datetime_format = 'Y/m/d H:i';
			?>
				<p><span><?php _e('Event Start','eventon');?>:</span> <b class='event_times'><?php echo date($datetime_format,$event_start);?></b></p>		
				<p><span><?php _e('Event End','eventon');?>:</span> <b class='event_times'><?php echo date($datetime_format,$event_end);?></b></p>	
				<p><span><?php _e('Price','eventon');?>: * (<?php echo $__woo_currencySYM;?>)</span><input name='p' type="text" value='<?php echo !empty($_POST['price'])? $_POST['price']:'';?>'></p>

				<?php if($_POST['mprice_status'] == 'yes'):?>
					<p class='mprice'><span><?php _e('Member Price','eventon');?>: (<?php echo $__woo_currencySYM;?>)</span><input name='mp' type="text" value='<?php echo !empty($_POST['mprice'])? $_POST['mprice']:'';?>'></p>
				<?php endif;?>
			<?php endif;?>
			<p>
				<span><?php _e('Start','eventon');?>: *</span>
				<span class='evodp_lb_times'> <input type="text" name='sd' value='<?php echo $dates_sent? date('Y/m/d', $_POST['start']):'';?>' placeholder=''/>
					<input type="hidden" name='sd_'/>
					<input type="text" name='st' value='<?php echo $dates_sent? date( $_POST['time_format'], $_POST['start']):'';?>'/>
				</span>
				<br/>
				<span><?php _e('End','eventon');?>: *</span>
				<span class='evodp_lb_times'>
					<input type="text" name='ed' value='<?php echo $dates_sent? date('Y/m/d', $_POST['end']):'';?>'/>
					<input type="hidden" name='ed_'/>
					<input type="text" name='et' value='<?php echo $dates_sent? date( $_POST['time_format'], $_POST['end']):'';?>'/>
				</span>
			</p>
			
			<?php
				$attrs = '';
				foreach(array(
					'data-bkey'=>$block_key,
					'data-type'=>$_POST['type'],
					'data-index'=> (!empty($_POST['index'])? $_POST['index']:''),
					'data-block'=>$_POST['block'],
					'data-eid'=>$_POST['eid'],
				) as $key=>$val){
					$attrs .= $key .'="'. $val .'" ';
				}
			?>
			<p><a class='evodp_form_submission evo_btn' <?php echo $attrs;?>><?php echo $_POST['type']=='new'? 'Add New':'Save';?></a></p>
			<p class="message"></p>
		</div>
		<?php

		echo json_encode(array(
			'content'=> ob_get_clean(),
			'status'=>'good'
		)); exit;
	}

	// Add new time block
	function add_new_time_block(){

		$post = array();
		foreach($_POST as $key=>$val){
			$post[$key] = urldecode($val);
		}
		
		$fnc = new evodp_fnc();
		$fnc->set_timezone();

		$html = $fnc->get_time_based_block_html($post, $post['index']);

		$start = $fnc->get_unix_time($post['sd'], $post['st'], $post);
		$end = $fnc->get_unix_time($post['ed'], $post['et'], $post);

		echo json_encode(array(
			'html'=>	$html, 
			'status'=>	'good',
			'timenow'=>	time(),
			'start'=>	$start,
			'end'=>		$end,
			'msg'=>	__('Successfully Added New Item','eventon')
		)); exit;
	}

}
new evodp_admin_ajax();