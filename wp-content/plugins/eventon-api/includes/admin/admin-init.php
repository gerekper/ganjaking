<?php
/**
 * Admin settings class
 *
 * @author 		AJDE
 * @category 	Admin
 * @package 	eventon-api/classes
 * @version     0.1
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class EVOAP_admin{
	
	public $optSL;
	function __construct(){
		add_action('admin_init', array($this, 'admin_init'));
		add_action( 'admin_menu', array( $this, 'menu' ),9);
	}

	// INITIATE
		function admin_init(){			
			// settings
			add_filter('eventon_settings_tabs',array($this, 'evoAP_tab_array' ),10, 1);
			add_action('eventon_settings_tabs_evcal_ap',array($this, 'evoAP_tab_content' ));		
		}

	// other hooks		
		// EventON settings menu inclusion
		function menu(){
			add_submenu_page( 'eventon', 'EventON API', __('EventON API','eventon'), 'manage_eventon', 'admin.php?page=eventon&tab=evcal_ap', '' );
		}
	
	// TABS SETTINGS
		function evoAP_tab_array($evcal_tabs){
			$evcal_tabs['evcal_ap']='EventON API';		
			return $evcal_tabs;
		}
		function evoAP_tab_content(){
			global $eventon;
			$eventon->load_ajde_backender();			
		?>
			<form method="post" action=""><?php settings_fields('evoAP_field_group'); 
					wp_nonce_field( AJDE_EVCAL_BASENAME, 'evcal_noncename' );?>
			<div id="evcal_ap" class="evcal_admin_meta">	
				<div class="evo_inside">
				<?php
					$cutomization_pg_array = array(
						array(
							'id'=>'evoapi','display'=>'show',
							'name'=>'Primary EventON API Calendar Settings',
							'tab_name'=>'Primary',
							'fields'=>array(
								array('id'=>'evoAP_months','type'=>'dropdown','name'=>'Number of months to show in API',
									'options'=>array(
										1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20
									)),
								array('id'=>'evoAP_event_limit','type'=>'dropdown','name'=>'Limit maximum number of events to show in API',
									'options'=>array(
										'All',1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,20,25
									)),
								array('id'=>'evoSL_sep_month','type'=>'yesno',
									'name'=> __('Separate events by month name','eventon')
								),		
								array('id'=>'code', 'type'=>'note','name'=>'NOTE: Below code will show list of events on external website. User interaction is limited to few hover effects to cut down back and forth communication with this website to external website except initial loading.'),
								array('id'=>'code', 'type'=>'customcode','code'=>$this->_js_code_toadd('calendar')),
								

						)),
						array(
							'id'=>'evoapi_one', 'icon'=>'calendar-o',
							'name'=>'One Event API',
							'tab_name'=>'One Event',
							'fields'=>array(
								array('id'=>'evoAP_event_id',
									'type'=>'text',
									'name'=>'ID of the event to use (This value can also be passed via API URL)',
									'default'=>'eg. 17'),
								array('id'=>'code', 'type'=>'customcode','code'=>$this->_js_code_toadd('oneevent'))
						)),array(
							'id'=>'evoapi_json',
							'name'=>'JSON events',
							'tab_name'=>'JSON','icon'=>'jsfiddle',
							'fields'=>array(
								array('id'=>'evoSL_json','type'=>'customcode','code'=>$this->json_code()),		
						)),array(
							'id'=>'evoapi_trouble',
							'name'=>'Basic Troubleshooting',
							'tab_name'=>'Troubleshoot','icon'=>'anchor',
							'fields'=>array(
								array('id'=>'evoSL_troublshooter','type'=>'customcode','code'=>$this->troubleshooter_code()),	
						))
					);							
					$eventon->load_ajde_backender();	
					$evcal_opt = get_option('evcal_options_evcal_ap'); 
					print_ajde_customization_form($cutomization_pg_array, $evcal_opt);	
				?>
			</div>
			</div>
			<div class='evo_diag'>
				<input type="submit" class="evo_admin_btn btn_prime" value="<?php _e('Save Changes') ?>" /><br/><br/>
				<a target='_blank' href='http://www.myeventon.com/support/'><img src='<?php echo AJDE_EVCAL_URL;?>/assets/images/myeventon_resources.png'/></a>
			</div>			
			</form>	
		<?php
		}

// calendar code
	function _js_code_toadd($section=''){
		global $EVOAP;
		ob_start();
		$siteUrl = get_site_url();
?>
<textarea style='width:100%; height:250px'>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<script type='text/javascript' src='<?php echo $siteUrl;?>/wp-content/plugins/eventon-api/eventon.js?ver=<?php echo $EVOAP->version;?>'></script>
<script type='text/javascript'>
	jQuery(document).ready(function($){
		$('#eventoncontent').evoCalendar({
			api: '<?php echo $siteUrl;?>/wp-json/eventon/<?php echo $section;?>',
			calendar_url: '<?php echo $siteUrl;?>',
			new_window: false,
			loading_text: 'Loading Calendar...',
		});
	});
</script>
<div id='eventoncontent' style="height:100%; width:100%"></div>
</textarea>
<h3 style='padding-top:40px'>evoCalendar() Javascript Function Options</h3>
<p><b>api:</b> The URL for the API which will pass the events data for external site</p>
<p><b>calendar_url:</b> URL to calendar page on this website to redirect when clicked on an event on the API Calendar. If a URL is not provided the clicks on events would redirect to single event pages. If you want the event clicks to redirect to single events page, leave <code>calendar_url:'',</code> as blank.<br/><br/>
</p>
<p><b>new_window:</b> <code>true/false</code>	this option set whether clicks on API calendar would open the events in same window or in new window.</p>
<p><b>loading_text:</b> You can type a custom text for this. This text will show when the code is first run during the time it takes to load API calendar.</p>

<h3 style='padding-top:20px'>Additional Calendar Parameters via API URL</h3>
<p>You can pass additional calendar parameters using the api URL inside javascript code.</p>

<p>Additional parameter values passed via API URL will override saved option values.</p>

<?php if($section=='calendar'):?>
<p><b style='font-size:15px'>Supported Additional Parameters:</b><br/>
<table>
<?php
	foreach(array(
		'event_type'=> '(int) event type category term ID(s) separated by commas',
		'event_type_{x}'=> '(int) event type {x} category term ID(s) separated by commas',
		'number_of_months'=> '(int) number of months to get events from (override above value)',
		'event_count'=> '(int) number of events to show (override above value)',
		'show_et_ft_img'=> '(yes/no) show featured event image',
		'hide_mult_occur'=> '(yes/no) hide multiple occurances of repeating event instances',
		'etc_override'=> '(yes/no) override event color with event type color (if set)',
		'focus_start_date_range'=> '(int) unix timestamp of event start date range',
		'focus_end_date_range'=> '(int) unix timestamp of event end date range',
		'lang'=> 'L1, L2 etc. language value',
	) as $key=>$value){
		echo "<tr><td>". $key . "</td><td>" . $value . '</td></tr>';
	}
?>
</table>
</p>
<p>Example URL with additional parameters: <code><?php echo $siteUrl;?>/wp-json/eventon/calendar<b>?event_type=6,12&number_of_months=6</b></code></p>

<?php else:?>
<p><b>Supported Additinoal Parameters:</b><table>
<?php
	foreach(array(
		'event_id '=> '(int) post ID for the event',
		'repeat_interval'=> '(int) repeating instance number for the event',
		'lang'=> 'L1, L2 etc. language value',
	) as $key=>$value){
		echo "<tr><td>". $key . "</td><td>" . $value . '</td></tr>';
	}
?>
</table></p>

<p>Example URL with additional parameters: <code><?php echo $siteUrl;?>/wp-json/eventon/oneevent<b>?event_id=6</b></code></p>
<?php endif;?>
<?php
		$code = ob_get_clean();
		$append = "<p>".__('Code to paste the below code into external website, where you want the eventON calendar to show.','eventon') .'</p>';
		//$code = htmlentities($code);
		return  $append. $code;
	}	

	// json tab content
		function json_code(){
			ob_start();
			
			echo  "<p><b>JSON event data link:</b> <code>". get_site_url() . "/wp-json/eventon/events</code></p>
			<p>The above URL can be used to pull a JSON data string of all your eventON event data. This can be used in external applications and software to fetch event data from this website and show them in those applications.";
			?>
				<p style='padding-top:10px'><b><?php _e('Getting Filtered JSON events','eventon');?></b></p>
				<p>You can get filtered events by event type categories by passing filter parameters in the JSON event data link as mentioned below.</p>
				<p>Example: <code><?php echo get_site_url()?>/wp-json/eventon/events?event_type=3,17&event_type_2=43</code> Using this JSON URL will give you events that will fall into those event type categories.</p>


				<p style='padding-top:10px'><b><?php _e('JSON event data structure:','eventon');?></b><br/>
					{events: {event-id:{name, start, end, details, repeats, event_timezone, color, event_subtitle, learnmore_link, featured, all_day_event,year_long_event, month_long_event, image_url, location_tax, location_name, location_address, location_lat, location_lon, organizer_tax, organizer_name, organizer_address, organizer_contact,  customfield_{X}, event_type{_X} } } }					
				</p>


				<p><a class='evo_admin_btn btn_triad' href="http://www.myeventon.com/documentation/json-data-structure/" target='_blank'><?php _e('Documentation on data structure','eventon');?></a></p>

				<p style='padding-top:10px;'><b><?php _e('Extending JSON Data (Advance Use Only)','eventon');?></b><br/>
				You can use the below pluggable function to add more data into JSON data stream using the event's post meta value passed on to pluggable hook.
				<br/>
				<br/>
				<code>add_filter('evoapi_event_data', 'extend_evoapi', 10, 3);<br/>
					function extend_evoapi($event_data, $event_id, $event_pmv){
					<br/>return $event_data;
					<br/>}</code>
				</p>
				<p><a class='evo_admin_btn btn_triad' href="http://www.myeventon.com/documentation/add-additional-event-data-json-output/" target='_blank'><?php _e('Documentation on additional fields','eventon');?></a></p>

			<?php

			return ob_get_clean();
		}

	function troubleshooter_code(){		
		$output = '<p><b><i>How to show eventon calendar in an external site?</i></b> <br/>Go to EventON API > General. Copy the javascript code and place it in the footer of external website. Create a new div with ID eventoncontent, such as <code>&lt;div id="eventoncontent" style="height:100%; width:100%" &gt;&lt;/div&gt;</code> - and place this HTML code into your external website, exactly where you want the calendar to show. You can edit the height and width in here to set values or it will extend 100%</p>';

		$output .= '<br/><p><b><i>How to edit the number of months to show in API calendar</i></b> <br/>Go to EventON API > General and under number of months to show in API, change this value.</p>';

		return $output;
	}
}

new EVOAP_admin();