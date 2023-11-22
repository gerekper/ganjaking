<?php
/**
 * EventON Widget
 *
 * @author 		AJDE
 * @category 	Widget
 * @package 	EventON/Classes
 * @version     4.3.1
 */

register_widget( 'EvcalWidget' );
class EvcalWidget extends WP_Widget{	
	function __construct(){
		$widget_ops = array('classname' => 'EvcalWidget', 
			'description' => 'EventON basic or upcoming list Event Calendar widget.' );
		parent::__construct('EvcalWidget', 'EventON Calendar', $widget_ops);
	}
	
	// initials
		function widget_default(){
			return $defaults = array(
				'ev_cal_id'=>'',
				'ev_count'=>'0',
				'ev_type'=>'all',
				'ev_type_2'=>'all',
				'ev_title'=>'',
				'show_upcoming'=>'0',
				'ev_hidepastev'=>'no',
				'hide_mult_occur'=>'no',
				'_is_fixed_time'=>'no',
				'fixed_month'=>'0',
				'fixed_year'=>'0',
				'hide_empty_months'=>'no',
				'number_of_months'=>'1',
				'lang'=>'L1'
			);
		}
		function widget_values($instance){
			$defaults = $this->widget_default();		
			return wp_parse_args( (array) $instance, $defaults);
		}
		
		function process_values($inst){
			$defaults = $this->widget_default();
			
			$send_values = array();		
			foreach($defaults as $f=>$v){			
				$send_values[$f] = (!empty($inst[$f])) ?$inst[$f] : $v;
			}
			
			return $send_values;
		}
	
	function form($instance) {
				
		$instance = $this->widget_values($instance); 
		extract($instance);
		
		?>
		<div id='eventon_widget_settings'>
			<div class='eventon_widget_top'><p></p></div>			
			<div class='evo_widget_outter evowig'>
				<?php
				echo EVO()->elements->process_multiple_elements(
					array(
					array(
						'type'=>'text',
						'id'=>$this->get_field_name('ev_cal_id'),
						'value'=>$ev_cal_id,
						'name'=> __('Widget ID','eventon'),
						'tooltip'=> __('Set a custom ID for widget calendar to separate it from other eventON calendar widgets. Specially if you have more than one eventON calendar widgets. <a href="http://www.myeventon.com/documentation/shortcode-guide/" target="_blank">What should be the ID</a> DO NOT leave blank space.','eventon'),
						'tooltip_position'=>'L'
					),
					array(
						'type'=>'text',
						'id' => $this->get_field_name('ev_title'),
						'value'=> $this->_get_val($ev_title),
						'default'=> __('Widget Title'),
						'name'=> __('Widget Title')
					),
					array(
						'type'=>'text', 'field_type'=>'number',
						'id' => $this->get_field_name('ev_count'),
						'value'=> $this->_get_val($ev_count),
						'name'=> __('Event Count'),
						'tooltip'=> __('If left blank - will display all events for that month'),
						'tooltip_position'=>'L',
					),					
					array(
						'type'=>'yesno', 
						'id' => $this->get_field_name('ev_hidepastev'),
						'value'=> $this->_get_val($ev_hidepastev),
						'name'=> __('Hide past events'),
					),
					array(
						'type'=>'yesno', 
						'id' => $this->get_field_name('show_upcoming'),
						'value'=> $this->_get_val($show_upcoming),
						'name'=> __('Show upcoming events'),
						'afterstatement'=> 'show_upcoming_child',
					),
						array('type'=>'begin_afterstatement','id'=>'show_upcoming_child','value'=>$show_upcoming),
						array(
							'type'=>'text', 'field_type'=>'number',
							'id' => $this->get_field_name('number_of_months'),
							'value'=> $this->_get_val($number_of_months),
							'name'=> __('Show upcoming events'),
							'tooltip'=> __('Use this field to set the number of upcoming months to show'),
							'tooltip_position'=>'L',
						),
						array(
							'type'=>'yesno', 
							'id' => $this->get_field_name('hide_mult_occur'),
							'value'=> $this->_get_val($hide_mult_occur),
							'name'=> __('Hide multiple occurance'),
						),
						array(
							'type'=>'yesno', 
							'id' => $this->get_field_name('hide_empty_months'),
							'value'=> $this->_get_val($hide_empty_months),
							'name'=> __('Hide empty months'),
						),
						array('type'=>'end_afterstatement'),

					array(
						'type'=>'yesno', 
						'id' => $this->get_field_name('_is_fixed_time'),
						'value'=> $this->_get_val($_is_fixed_time),
						'name'=> __('Set fixed month and year'),
						'afterstatement'=> '_is_fixed_time_child',
					),
						array('type'=>'begin_afterstatement','id'=>'_is_fixed_time_child','value'=>$_is_fixed_time),
						array(
							'type'=>'text', 'field_type'=>'number',
							'id' => $this->get_field_name('fixed_month'),
							'value'=> $this->_get_val($fixed_month),'max'=>12,'min'=>1, 'step'=>1,
							'name'=> __('Fixed month number'),
						),	
						array(
							'type'=>'text', 'field_type'=>'number',
							'id' => $this->get_field_name('fixed_year'),
							'value'=> $this->_get_val($fixed_year), 'max'=>3000,'min'=>1900, 'step'=>1,
							'default'=> 2020,
							'name'=> __('Fixed year number'),
						),	
						array('type'=>'end_afterstatement'),					
					array(
						'type'=>'lightbox_select_vals',
						'id' => $this->get_field_name('ev_type'),
						'value'=> $this->_get_val($ev_type),
						'name'=> __('Select Event Type'),
						'taxonomy'=>'event_type'
					),
					array(
						'type'=>'lightbox_select_vals',
						'id' => $this->get_field_name('ev_type_2'),
						'value'=> $this->_get_val($ev_type_2),
						'name'=> __('Select Event Type 2'),
						'taxonomy'=>'event_type_2'
					),
					array(
						'type'=>'dropdown',
						'id'=> $this->get_field_name('lang'),
						'name'=> __('Language','eventon'),
						'options'=> array(
							'L1'=>'L1','L2'=>'L2','L3'=>'L3','L4'=>'L4'
						),	
						'value'=> $this->_get_val($lang)
					)
				));
				?>				
			</div>			
		</div>
		<?php
	}
	
	public function _get_val($V){
		return empty($V)? null: $V;
	}

	// update the new values for widget
	function update($new_instance, $old_instance) {
		$instance = $old_instance;		
		foreach($this->widget_default() as $defv=>$def){
			if($defv!='ev_type_2')
				$instance[$defv] = strip_tags($new_instance[$defv]);
		}		
		return $instance;
	}
	
	// add new default shortcode arguments
	public function event_list_shortcode_defaults($arr){		
		return array_merge($arr, array(
			'hide_empty_months'=>'no',
		));		
	}
	
	// CONTENT
	public function widget($widget_args, $instance) {
		
		// make sure styles and scripts get loaded
		EVO()->frontend->load_evo_scripts_styles();
		
		// DEFAULTS
		$fixed_month = $fixed_year = 0;
		
		// extract widget specific variables
		extract($widget_args, EXTR_SKIP);		
				
		$values = $this->process_values($instance);		
		extract($values);
		
		
		// HIDE EMPTY months
		if($hide_empty_months =='yes')
			add_filter('eventon_shortcode_defaults', array($this,'event_list_shortcode_defaults'), 10, 1);
		
		// CALENDAR ARGUMENTS
		$args = apply_filters('evo_main_widget_args',array(
			'cal_id'=>$ev_cal_id,
			'event_count'=>$ev_count,
			'show_upcoming'=>$show_upcoming,
			'number_of_months'=> ($show_upcoming =='yes'? $number_of_months:1),
			'event_type'=> $ev_type,
			'event_type_2'=> $ev_type_2,
			'lang'=> $lang,
			'event_type_2'=> 'all',
			'fixed_month'=> ($_is_fixed_time =='yes'? $fixed_month:''),
			'fixed_year'=> ($_is_fixed_time =='yes'? $fixed_year:''),
			'hide_past'=>$ev_hidepastev,
			'hide_mult_occur'=> ($show_upcoming =='yes'? $hide_mult_occur: 'no'),
			'hide_empty_months'=> ($show_upcoming =='yes'? $hide_empty_months: 'no'),
		));

		// Check for event type filterings called for from widget settings
		if($ev_type!='all'){
			$filters['filters'][]=array(
				'filter_type'=>'tax',
				'filter_name'=>'event_type',
				'filter_val'=>$args['event_type']
			);
			$args = array_merge($args,$filters);
		}
		if($ev_type_2!='all'){
			$filters['filters'][]=array(
				'filter_type'=>'tax',
				'filter_name'=>'event_type_2',
				'filter_val'=>$args['event_type_2']
			);
			$args = array_merge($args,$filters);
		}
		
		
		// WIDGET
		if(has_action('eventon_before_widget')){
			do_action('eventon_before_widget');
		}else{
			echo $before_widget;
		}
						
		
		// widget title
		if ( $title = apply_filters( 'widget_title', empty( $instance['ev_title'] ) ? '' : $instance['ev_title'], $instance) ) {
			echo $widget_args['before_title']. $title. $widget_args['after_title'];
		}

		$content = EVO()->calendar->_get_initial_calendar( $args );
		echo "<div id='evcal_widget' class='evo_widget'>".$content."</div>";
		
		
		if(has_action('eventon_after_widget')){
			do_action('eventon_after_widget');
		}else{
			echo $after_widget;
		}
		
	}
}

// EventON Second widget
	class EvcalWidget_SC extends WP_Widget{		
		function __construct(){
			$widget_ops = array('classname' => 'EvcalWidget_SC', 
				'description' => 'EventON shortcode executor in the widget.' );
			parent::__construct('EvcalWidget_SC', 'EventON Shortcode Executor (ESE)', $widget_ops);
		}



		function form($instance) {

			extract($instance);

			$evo_title = (!empty($evo_title))? $evo_title: null;
			$evo_shortcodeW = (!empty($evo_shortcodeW))? $evo_shortcodeW: null;
			// HTML

			
			?>
			<div id='eventon_widget_settings' class='eventon_widget_settings'>
				<div class='eventon_widget_top'><p></p></div>
				
				<div class='evo_widget_outter evowig'>				
					<div class='evo_wig_item'>					
						<input id="<?php echo $this->get_field_id('evo_title'); ?>" name="<?php echo $this->get_field_name('evo_title'); ?>" type="text" 
						value="<?php echo esc_attr($evo_title); ?>" placeholder='Widget Title' title='Widget Title'/>					
					</div>
				</div>
				
				<p><a id="evo_shortcode_btn" class="evo_admin_btn btn_prime evolb_trigger_shortcodegenerator" style='color:#fff;'>[ ] <?php _e('Generate shortcode','eventon');?></a>
				</p>
				


				<p class='evo_widget_textarea'>
					<textarea name="<?php echo $this->get_field_name('evo_shortcodeW'); ?>" id="<?php echo $this->get_field_id('evo_shortcodeW'); ?>"><?php echo esc_attr($evo_shortcodeW); ?></textarea>
				</p>
			
			</div>
			<?php
		}

		// update the new values for widget
			function update($new_instance, $old_instance) {
				$instance = $old_instance;
				
				$instance['evo_shortcodeW'] = strip_tags($new_instance['evo_shortcodeW']);
				$instance['evo_title'] = strip_tags($new_instance['evo_title']);
				
				return $instance;
			}

		// CONTENT
			public function widget($args, $instance) {
						
				// extract widget specific variables
				extract($args, EXTR_SKIP);		
				
				
				/*	 WIDGET */	
				if(has_action('eventon_before_widget_SC')){
					do_action('eventon_before_widget_SC');
				}else{	echo $before_widget;}	

				$title = isset($instance['evo_title']) ?
					apply_filters('widget_title', $instance['evo_title'] ) : '';  

				// widget title
				if(!empty($instance['evo_title']) ){
					echo $before_title. $title .$after_title;
				}

				// shortcode
				if(!empty($instance['evo_shortcodeW'])){
					echo "<div id='evcal_widget' class='evo_widget'>";
					echo do_shortcode( $instance['evo_shortcodeW']) ;	
					echo "</div>";		
				}

				if(has_action('eventon_after_widget_SC')){
					do_action('eventon_after_widget_SC');
				}else{
					echo $after_widget;
				}
				
			}

		public function _get_val($V){
			return empty($V)? null: $V;
		}
	}
	register_widget( 'EvcalWidget_SC' );

// EventON Next months event
	class EvcalWidget_next_month extends WP_Widget{	
		function __construct(){
			$month = date('F');
			$widget_ops = array('classname' => 'EvcalWidget_next_month', 
				'description' => 'This widget will show events from next month.' );
			parent::__construct('EvcalWidget_next_month', 'EventON Events from Next Month', $widget_ops);
		}

		function form($instance) {
			
			extract($instance);

			$evo_title = (!empty($evo_title))? $evo_title: null;
			// HTML
			?>
			<div id='eventon_widget_settings' class='eventon_widget_settings'>
				<div class='eventon_widget_top'><p></p></div>			
				<div class='evo_widget_outter evowig'>	
					<?php
					echo EVO()->elements->process_multiple_elements(
						array(						
						array(
							'type'=>'text',
							'id' => $this->get_field_name('evo_title'),
							'value'=> $evo_title,
							'default'=> __('Widget Title'),
							'name'=> __('Widget Title')
						),
						array(
							'type'=>'notice',
							'name'=> __('This widget will show from next month. If there are no events for this month it will show as "No Events"','eventon')
						)
					));
					?>
				</div>				
			</div>
			<?php
		}

		// update the new values for widget
			function update($new_instance, $old_instance) {
				$instance = $old_instance;			
				$instance['evo_title'] = strip_tags($new_instance['evo_title']);			
				return $instance;
			}

		// The actuval widget
			public function widget($args, $instance) {
						
				// extract widget specific variables
				extract($args, EXTR_SKIP);	
				
				/*	 WIDGET	*/	
				if(has_action('eventon_before_widget_SC')){
					do_action('eventon_before_widget_SC');
				}else{	echo $before_widget;	}

				$title = apply_filters('widget_title', $instance['evo_title'] );  

				// widget title
				if(!empty($instance['evo_title']) ){
					echo $before_title. $title .$after_title;
				}

				// calendar
				$shortcode = '[add_eventon_list number_of_months="1" month_incre="+1" ]';
				echo "<div id='evcal_widget' class='evo_widget'>";
				echo do_shortcode( $shortcode) ;	
				echo "</div>";	

				if(has_action('eventon_after_widget_SC')){
					do_action('eventon_after_widget_SC');
				}else{	echo $after_widget;	}
				
			}
		public function _get_val($V){
			return empty($V)? null: $V;
		}
	}
	register_widget( 'EvcalWidget_next_month' );

// EventON Upcoming Events Widget
	class EvcalWidget_three extends WP_Widget{	
		function __construct(){
			$month = date('F');
			$widget_ops = array('classname' => 'EvcalWidget_three', 
				'description' => 'This widget will show all upcoming events for the current month ('.$month.').' );
			parent::__construct('EvcalWidget_three', 'EventON Upcoming Events', $widget_ops);
		}

		function form($instance) {
			
			extract($instance);

			$evo_title = (!empty($evo_title))? $evo_title: null;
			// HTML
			?>
			<div id='eventon_widget_settings' class='eventon_widget_settings'>
				<div class='eventon_widget_top'><p></p></div>			
				<div class='evo_widget_outter evowig'>
					<?php
					echo EVO()->elements->process_multiple_elements(
						array(						
						array(
							'type'=>'text',
							'id' => $this->get_field_name('evo_title'),
							'value'=> $this->_get_val($evo_title),
							'default'=> __('Widget Title'),
							'name'=> __('Widget Title')
						),
						array(
							'type'=>'notice',
							'name'=> __('This widget will show future events for the current month. If there are no events upcoming for this month it will show as "No Events"','eventon')
						)
					));
					?>	
				</div>
			</div>
			<?php
		}

		// update the new values for widget
			function update($new_instance, $old_instance) {
				$instance = $old_instance;			
				$instance['evo_title'] = strip_tags($new_instance['evo_title']);			
				return $instance;
			}

		// The actuval widget
			public function widget($args, $instance) {
						
				// extract widget specific variables
				extract($args, EXTR_SKIP);	
				
				/*	 WIDGET	*/	
				if(has_action('eventon_before_widget_SC')){
					do_action('eventon_before_widget_SC');
				}else{	echo $before_widget;	}

				
				// widget title
				if(!empty($instance['evo_title']) ){
					$title = apply_filters('widget_title', $instance['evo_title'] );  
					echo $before_title. $title .$after_title;
				}

				// calendar
				$shortcode = '[add_eventon hide_past="yes"]';
				echo "<div id='evcal_widget' class='evo_widget'>";
				echo do_shortcode( $shortcode) ;	
				echo "</div>";	

				if(has_action('eventon_after_widget_SC')){
					do_action('eventon_after_widget_SC');
				}else{	echo $after_widget;	}
				
			}
		public function _get_val($V){
			return empty($V)? null: $V;
		}
	}
	register_widget( 'EvcalWidget_three' );

// EventON Events from categories Widget
	class EvcalWidget_four extends WP_Widget{
		
		function __construct(){
			$widget_ops = array('classname' => 'EvcalWidget_four', 
				'description' => 'Show events from only certain event type categories using this widget.' );
			parent::__construct('EvcalWidget_four', 'EventON Event Type Calendar', $widget_ops);
		}

		function form($instance) {
			
			extract($instance);

			$evOpt = get_option('evcal_options_evcal_1');
			$event_type_names = evo_get_ettNames($evOpt);

			//print_r($instance);
			?>
			<div id='eventon_widget_settings' class='eventon_widget_settings'>
				<div class='eventon_widget_top'><p></p></div>				
				<div class='evo_widget_outter evowig'>
					<?php

					$evo_title = (!empty($evo_title))? $evo_title: null;

					$evo_wig_ett_1 = ( !empty($evo_wig_ett_1)) ? 
						(is_array($evo_wig_ett_1)? implode(',', $evo_wig_ett_1): $evo_wig_ett_1): null;

					$evo_wig_ett_2 = ( !empty($evo_wig_ett_2)) ? 
						(is_array($evo_wig_ett_2)? implode(',', $evo_wig_ett_2): $evo_wig_ett_2): null;


					echo EVO()->elements->process_multiple_elements(
						array(						
						array(
							'type'=>'text',
							'id' => $this->get_field_name('evo_title'),
							'value'=> $this->_get_val($evo_title),
							'default'=> __('Widget Title'),
							'name'=> __('Widget Title')
						),
						array(
							'type'=>'lightbox_select_vals',
							'id' => $this->get_field_name('evo_wig_ett_1'),
							'value'=> $evo_wig_ett_1,
							'name'=> __('Select Event Type'),
							'taxonomy'=>'event_type'
						),
						array(
							'type'=>'lightbox_select_vals',
							'id' => $this->get_field_name('evo_wig_ett_2'),
							'value'=> $evo_wig_ett_2,
							'name'=> __('Select Event Type 2'),
							'taxonomy'=>'event_type_2'
						),
						array(
							'type'=>'notice',
							'name'=> __('Selecting event type categories above will show events fall into all those categories for the current month.','eventon')
						)
					));
					?>	
				</div>
				<p style='opacity:0.6'><i><?php _e('If you are not able to achieve what you desire, try','eventon')?> <a href='http://www.myeventon.com/documentation/use-eventon-shortcode-executor-widget/' target='_blank'><?php _e('EventON Shortcode Executor Widget','eventon');?></a></i></p>	
			</div>
			<?php
		}

		// update the new values for widget
			function update($new_instance, $old_instance) {
				$instance = $old_instance;
				
				$instance['evo_title'] = strip_tags($new_instance['evo_title']);

				$instance['evo_wig_ett_1'] = empty($new_instance['evo_wig_ett_1'])? '': $new_instance['evo_wig_ett_1'];	
				$instance['evo_wig_ett_2'] = empty($new_instance['evo_wig_ett_2'])? '':$new_instance['evo_wig_ett_2'];
				
				return $instance;
			}

		// The actuval widget - FRONT END
			public function widget($args, $instance) {
						
				// extract widget specific variables
				extract($args, EXTR_SKIP);	
				
				/*	 WIDGET	*/	
				if(has_action('eventon_before_widget_SC')){
					do_action('eventon_before_widget_SC');
				}else{	echo $before_widget;	}			
				

				$title = apply_filters('widget_title', $instance['evo_title'] );  

				// widget title
				if(!empty($instance['evo_title']) ){
					echo $before_title. $title .$after_title;
				}

				
				// even type
					$shortcode_var ='';
					foreach(array('1','2') as $ett){
						if(!empty($instance['evo_wig_ett_'.$ett]) ){

							$ab = ($ett=='1')? '':'_'.$ett;
							$shortcode_var.= 'event_type'.$ab.'="'. $instance['evo_wig_ett_'.$ett] .'" ';
						}
					}

				$shortcode = '[add_eventon '.$shortcode_var.']';
				echo "<div id='evcal_widget' class='evo_widget'>";
				echo do_shortcode( $shortcode) ;	
				echo "</div>";	


				if(has_action('eventon_after_widget_SC')){
					do_action('eventon_after_widget_SC');
				}else{	echo $after_widget;	}
				
			}
		public function _get_val($V){
			return empty($V)? null: $V;
		}
	}
	register_widget( 'EvcalWidget_four' );