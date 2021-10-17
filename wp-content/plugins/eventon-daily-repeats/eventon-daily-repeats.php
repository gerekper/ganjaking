<?php
/*
 Plugin Name: EventON - Daily Repeats
 Plugin URI: http://wordpress.org/extend/plugins/
 Description: Adds the capability to create events on daily basis
 Author: wordpressdotorg
 Version: 0.3
 Author URI: http://www.ashanjay.com/
 */

class EventON_daily_repeats{
	
	public $version='0.3';
	
	public $slug;
	public $plugin_slug ;	
	
	/*
	 * Construct
	 */
	public function __construct(){
		add_filter('evo_repeat_intervals',array($this, 'activate') );
		add_filter('eventon_event_frequency_daily',array($this, 'daily_switch'),10,1);
				
		// get plugin slug
		$this->plugin_slug = plugin_basename(__FILE__);
		list ($t1, $t2) = explode('/', $this->plugin_slug);
        $this->slug = $t1;
		
		
		// Add this addon to addon list
		$this->add_to_eventon_addons_list();
		
		
		// Deactivation
		register_deactivation_hook( __FILE__, array($this,'deactivate'));
		
	}
	
	/*
	 * Show daily repeat as an opption for selection
	 */
	public function activate($repeat_freq){
		
		$repeat_freq['daily'] = 'days';
		
		return $repeat_freq;
	}
	
	/*
	 * Return date calculation values to calendar
	 * front end function
	 */
	public function daily_switch($repeat_multiplier){
		
		$terms['term'] = ($repeat_multiplier>1)? 'days':'day';	
		$terms['term_ar'] = 'rw';
		
		return  $terms;
		
	}
	
	/*
		remove this plugin from myEventON Addons list
	*/
	function deactivate(){
		$eventon_addons_opt = get_option('eventon_addons');
		
		if(is_array($eventon_addons_opt) && array_key_exists($this->slug, $eventon_addons_opt)){
			foreach($eventon_addons_opt as $addon_name=>$addon_ar){
				
				if($addon_name==$this->slug){
					unset($eventon_addons_opt[$addon_name]);
				}
			}
		}
		
		update_option('eventon_addons',$eventon_addons_opt);
	}
	
	
	/** Add this extension's information to EventON addons tab **/
	function add_to_eventon_addons_list(){
		$eventon_addons_opt = get_option('eventon_addons');
		
		$this->plugin_url = path_join(WP_PLUGIN_URL, basename(dirname(__FILE__)));
		$plugin_path = dirname( __FILE__ );
		
		$plugin_details = array(
			'name'=> 		'Daily Repeat for EventON',
			'version'=> 	$this->version,			
			'slug'=>		$this->slug,
			'type'=>'extension'
		);
		
		$eventon_addons_ar[$this->slug]=$plugin_details;
		if(is_array($eventon_addons_opt)){
			$eventon_addons_new_ar = array_merge($eventon_addons_opt, $eventon_addons_ar );
		}else{
			$eventon_addons_new_ar = $eventon_addons_ar;
		}
		
		update_option('eventon_addons',$eventon_addons_new_ar);
		
		//print_r(get_option('eventon_addons'));
	}
}

// Initiate this addon within the plugin
$EventON_daily_repeats = new EventON_daily_repeats;

?>