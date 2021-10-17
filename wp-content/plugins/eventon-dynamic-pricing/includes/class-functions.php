<?php
/**
 * functions
 */

class evodp_fnc{
	function get_time_based_block_html($args, $index){
		ob_start();
		global $evodp;

		$__woo_currencySYM = get_woocommerce_currency_symbol();

		$start = !empty($args[0])? $args[0]: $this->get_unix_time($args['sd'] , $args['st'], $args);
		$end = !empty($args[1])? $args[1]: $this->get_unix_time($args['ed'] , $args['et'], $args);

		$date_format = $args['date_format'];
		$date_format= 'Y/m/d';

		$this->set_timezone();

		?>
		<li data-cnt="<?php echo $index;?>" class="new">
			<em alt="Edit" class='evodp_block_item edit ajde_popup_trig' data-popc='evodp_lightbox' data-eid='<?php echo $args['eid'];?>' data-block='<?php echo $args['block'];?>' data-type='edit'><i class='fa fa-pencil'></i></em>
			<em alt="Delete" class='delete' data-eid='<?php echo $args['eid'];?>' data-block='<?php echo $args['block'];?>'>x</em>

			<span><?php _e('Start','eventon');?></span><?php echo date($date_format.' '.$args['time_format'], $start);?> <span class="e"><?php _e('End','eventon');?></span> <?php echo date($date_format.' '.$args['time_format'], $end);?> 
			
			<?php if($args['block'] =='tbp'):?>
				<p><i class='reg_price'><?php _e('Price','eventon');?>: <b><?php echo $this->check_data($args, 'p')? 
					$__woo_currencySYM. ($this->check_data($args, 'p')):'';?></b></i>
				 <i class='mem_price'><?php _e('Member Price','eventon');?>: <b><?php echo $this->check_data($args, 'mp')? 
					$__woo_currencySYM. ($this->check_data($args, 'mp')): __('-same-','eventon');?></b></i>
				</p>
			<?php endif;?>
			
			<?php 

			$hidden_fields = array(
				'0'=> 	$start,
				'1'=>	$end,
			);

			if($args['block']=='tbp'){
				$hidden_fields['p']		=	!empty($args['p'])? $args['p']:0;
				$hidden_fields['mp']	=	!empty($args['mp'])? $args['mp']:0;
			}

			foreach($hidden_fields as $key=>$val){
				?><input type="hidden" name="<?php echo $args['block_key'];?>[<?php echo $index;?>][<?php echo $key;?>]" value="<?php echo $val;?>"><?php
			}?>			
		</li>
		<?php
		return ob_get_clean();
	}

	function check_data($data, $key){
		return !empty($data[$key])? $data[$key]: false;
	}

	function get_unix_time($date, $time, $args){
		global $evodp;

		$this->set_timezone();

		//date_default_timezone_set('UTC');

		// time format
		if(empty($args['time_format']) ){
			$args['time_format'] = get_option('time_format');
		}

		$_wp_date_format = !empty($args['date_format'])? $args['date_format']: get_option('date_format');

		$__ti = date_parse_from_format($_wp_date_format.' '.$args['time_format'], $date.' '.$time);

		return mktime($__ti['hour'], $__ti['minute'],0, $__ti['month'], $__ti['day'], $__ti['year'] );
	}

// SUPPORT
// -- can be get from datetime obj v2.6.1
	function set_timezone(){
		$tzstring = $this->get_timezone_str();
		$tzstring = ($tzstring == 'UTC+0')? 'UTC':$tzstring;
		date_default_timezone_set($tzstring);
	}

	// get local unix now
	function get_local_unix_now(){
		// set local time zone
		$this->set_timezone();

		return time();
	}

	// get the saved time zone value
	function get_timezone_str(){
		$tzstring = get_option('timezone_string');
		$current_offset = get_option('gmt_offset');

		// Remove old Etc mappings. Fallback to gmt_offset.
		if ( false !== strpos($tzstring,'Etc/GMT') )
			$tzstring = '';

		if ( empty($tzstring) ) { // Create a UTC+- zone if no timezone string exists
			$check_zone_info = false;
			if ( 0 == $current_offset )
				$tzstring = 'UTC+0';
			elseif ($current_offset < 0)
				$tzstring = 'UTC' . $current_offset;
			else
				$tzstring = 'UTC+' . $current_offset;
		}

		return $tzstring;
	}
}