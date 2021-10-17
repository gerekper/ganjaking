<?php
/**
 * EventON full cal Widget
 *
 * @author 		AJDE
 * @category 	Widget
 * @package 	EventON-FC: Classes
 * @version     0.1
 */

 
class evoFC_Widget extends WP_Widget{

	function __construct(){
		$widget_ops = array(
			'classname' => 'evoFC_Widget', 
			'description' => 'EventON calendar with month grid style Full Calendar widget.'
		);
		parent::__construct('evoFC_Widget', 'EventON FullCal', $widget_ops);
	}
		
	// default widget values
	function widget_default(){
		return $defaults = array(
			'ev_cal_id'=>'',
			'ev_fc_title'=>'',
			'_is_fixed_time'=>'',
			'fixed_month'=>'',
			'fixed_year'=>'',
			'ev_type'=>'',
		);
	}
	function widget_values($instance){
		$defaults = $this->widget_default();
		
		return wp_parse_args( (array) $instance, $defaults);
	}
	
	function form($instance) {
		global $eventon;
				
		$instance = $this->widget_values($instance); 
		extract($instance);
		
	?>
		<div id='eventon_widget_settings'>
			<div class='eventon_widget_top'><p></p></div>
			
			<div class='evo_widget_outter evowig'>
				<div class='evo_wig_item'>					
					<input id="<?php echo $this->get_field_id('ev_cal_id'); ?>" name="<?php echo $this->get_field_name('ev_cal_id'); ?>" type="text" 
					value="<?php echo esc_attr($ev_cal_id); ?>" placeholder='Widget ID' title='Widget ID'/>
				</div>
			</div>
			<div class='evo_widget_outter evowig'>
				<div class='evo_wig_item'>					
					<input id="<?php echo $this->get_field_id('ev_fc_title'); ?>" name="<?php echo $this->get_field_name('ev_fc_title'); ?>" type="text" 
					value="<?php echo esc_attr($ev_fc_title); ?>" placeholder='Widget Title' title='Widget Title'/>
				</div>
			</div>
			
			<p class='divider'></p>
			<div class='evo_widget_outter evowig'>
				<div class='evo_wig_item' connection=''>					
					
					<input id="<?php echo $this->get_field_id('_is_fixed_time'); ?>" type='hidden' name='<?php echo $this->get_field_name('_is_fixed_time'); ?>' value='<?php echo esc_attr($_is_fixed_time); ?>'/>
					<p class='evowig_chbx <?php echo ($_is_fixed_time=='yes')?'selected':null; ?>'></p>
					<p>Set fixed month/year</p>
					<div class='clear'></div>
				</div>
				
				<div class='evo_wug_hid' <?php echo ($_is_fixed_time=='yes')?'style="display:block"':null; ?>>
					<div class='evo_wig_item'>
						<input id="<?php echo $this->get_field_id('fixed_month'); ?>" name="<?php echo $this->get_field_name('fixed_month'); ?>" type="text" 
						value="<?php echo esc_attr($fixed_month); ?>" placeholder='Fixed month number' title='Fixed month number'/>					
					</div><div class='evo_wig_item'>
						<input id="<?php echo $this->get_field_id('fixed_year'); ?>" name="<?php echo $this->get_field_name('fixed_year'); ?>" type="text" 
						value="<?php echo esc_attr($fixed_year); ?>" placeholder='Fixed year number' title='Fixed year number'/>					
					</div>
				</div>
			</div>
			
			<p class='divider'></p>
			 
			<!-- Event Types --> 
			<div class='evo_widget_outter evowig'>
				<div class='evo_wig_item'>
					<input id="<?php echo $this->get_field_id('ev_type'); ?>" name="<?php echo $this->get_field_name('ev_type'); ?>" type="text" 
					value="<?php echo esc_attr($ev_type); ?>" placeholder='Event Types' title='Event Types'/>
					<em>Leave blank for all event types, else type <a href='edit-tags.php?taxonomy=event_type&post_type=ajde_events'>event type ID</a> separated by commas)</em>
				</div>						
			</div>
			
		</div>
		<?php
	}
	
	// update the new values for widget
		function update($new_instance, $old_instance) {
			$instance = $old_instance;
			
			foreach($this->widget_default() as $defv=>$def){
				$instance[$defv] = strip_tags($new_instance[$defv]);
			}
			
			return $instance;
		}
	
	// WIDGET CONTENT
	public function widget($args, $instance) {
				
		// DEFAULTS
		$fixed_month = $fixed_year = 0;
		
		extract($args, EXTR_SKIP);				
		
		// Variables
		$event_type = empty($instance['ev_type']) ? 'all' : $instance['ev_type'];
		$ev_cal_id = empty($instance['ev_cal_id']) ? uniqid() : $instance['ev_cal_id'];
		
		// Fixed month year
		if(!empty($instance['_is_fixed_time']) && $instance['_is_fixed_time']=='yes'){
			$fixed_month = (!empty($instance['fixed_month']))? $instance['fixed_month']:0;
			$fixed_year = (!empty($instance['fixed_year']))? $instance['fixed_year']:0;
		}
		
		// CALENDAR ARGUMENTS
		$args = array(
			'cal_id'=>'evo_fc_'.$ev_cal_id,
			'fixed_month'=>$fixed_month,
			'fixed_year'=>$fixed_year,
			'event_type'=> $event_type,
		);		
		
		// BEFORE
			if(has_action('eventonFC_before_widget')){
				do_action('eventonFC_before_widget');
			}else{	echo $before_widget;	}
		
		// widget title
		if(!empty($instance['ev_fc_title']) ){
			echo "<h3 class='widget-title'>".$instance['ev_fc_title']."</h3>";
		}
		
		// GET calendar content
		$content = EVOFC()->frontend->getCAL($args);		
		
		echo "<div id='evcal_widget' class='evo_fc_widget'>".$content."</div>";
			
		// AFTER
			if(has_action('eventonFC_after_widget')){
				do_action('eventonFC_after_widget');
			}else{	echo $after_widget;	}		
	}
}
 
 
 
 
 
 
 
?>