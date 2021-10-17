<?php
/**
 * Single Event Template Related Class
 * Initiated only on single event page
 * @version 2.5.4
 */
class evo_sinevent{

	public $RI = 0;
	public $L = 'L1';
	public $EVENT = false;

	public function __construct(){
		$this->evo_opt = get_option('evcal_options_evcal_1');

		// single event template hooks
		add_action('eventon_before_main_content', array($this, 'before_main_content'), 10,1);
		add_action('eventon_single_content_wrapper', array($this, 'content_wrapper'), 10);

		add_action('eventon_single_content', array($this, 'after_content'), 10);
		add_action('eventon_single_after_loop', array($this, 'after_content_loop'), 10);
		add_action('eventon_single_sidebar', array($this, 'sidebar_placement'), 10);
		add_action('eventon_after_main_content', array($this, 'after_main_content'), 10);

		add_action('eventon_oneevent_wrapper', array($this, 'oneevent_wrapper'), 10);
		add_action('eventon_oneevent_evodata', array($this, 'oneevent_evodata'), 10);
		add_action('eventon_oneevent_head', array($this, 'oneevent_head'), 10);
		add_action('eventon_oneevent_repeat_header', array($this, 'oneevent_repeat_header'), 10);
		add_action('eventon_oneevent_event_data', array($this, 'oneevent_event_data'), 10);

		// Set query Repeat instance for page
		global $wp_query, $post;

		if( isset($_GET['ri']))	$this->RI = (int)$_GET['ri'];
		if( isset($_GET['l'])) $this->L = $_GET['l'];


		$this->EVENT = new EVO_Event($post->ID, '', $this->RI );

		// support passing URL like ..../var/ri-2.l-L2/
			if(isset($wp_query->query["var"])){
				$_url_var = $wp_query->query["var"];
				
				$url_var = explode('.', $_url_var);
				$vars = array();
				
				foreach($url_var as $var){
					$split = explode('-', $var);

					// RI
					if($split[0] == 'ri') $this->RI = (int)$split[1];
					if($split[0] == 'l') $this->L = $split[1];

					$this->EVENT->ri = $this->RI;					
				}

				evo_set_global_lang($this->L); // set global language

				// virtual event access
				if($_url_var == 'event_access'){					
					
					$vir_url = $this->EVENT->get_vir_url();
					
					if($vir_url){
						wp_redirect( $vir_url ); exit;
					} 
				}

			}
	}

	// hook for single event page
		function before_main_content($lang = ''){
			// if fixed language value was passed via template
			if(!empty($lang)){
				$this->L = $lang;
			}
			
			// check if language corresponding events is enabled
			if( !empty($this->evo_opt['evo_lang_corresp']) && $this->evo_opt['evo_lang_corresp']=='yes' && $this->EVENT->get_prop('_evo_lang') ){
				$this->L = $this->EVENT->get_prop('_evo_lang');				
			}

			// set global language to be used for rest of the page
			evo_set_global_lang($this->L);

			$this->page_header();
			EVO()->frontend->load_evo_scripts_styles();		
		}
		function after_content(){			
			$this->page_content();
			$this->comments();
		}
		function sidebar_placement(){
			$this->sidebar();
			?><div class="clear"></div><?php
		}
		function after_content_loop(){			
			?></div><!-- #content --><?php
		}
		function after_main_content(){
			get_footer();
		}

		function content_wrapper(){
			?>
			<div class='evo_page_content <?php echo ($this->has_evo_se_sidebar())? 'evo_se_sidarbar':null;?>'>
			<?php
		}

	// hook for one event inside loop
	// @~2.6.13
		function oneevent_wrapper(){
			$rtl = evo_settings_check_yn($this->evo_opt, 'evo_rtl');

			$event_id = get_the_ID();
			$json = apply_filters('evo_event_json_data',array(), $event_id);

			// eventtop style
			$etsty = EVO()->cal->get_prop('evosm_eventtop_style');
			if(!$etsty) $etsty = 'color';

			?>
			<div id='evcal_single_event_<?php echo get_the_ID();?>' class='ajde_evcal_calendar eventon_single_event evo_sin_page<?php echo ($rtl?'evortl':'') .' '. $etsty;?>' data-eid='<?php echo $event_id;?>' data-l='<?php echo $this->L;?>' data-j='<?php echo json_encode($json);?>'>
			<?php
		}
		function oneevent_evodata(){
			// deprecating ?><div class='evo-data' <?php echo $this->get_evo_data();?>></div>
			<div class='evo_cal_data' data-sc='<?php echo json_encode($this->get_cal_event_JSON());?>'></div>
			<?php
		}
		function oneevent_head(){

			$repeati = $this->RI;
			$lang = $this->L;	

			$formatted_time = eventon_get_formatted_time( $this->EVENT->get_event_time() );	
			$header_text =  get_eventon_cal_title_month($formatted_time['n'], $formatted_time['Y'], $lang);


			?><div id='evcal_head' class='calendar_header'><p id='evcal_cur'><?php echo $header_text;?></p></div><?php
		}
		function oneevent_repeat_header(){
			$repeati = $this->RI;
			$this->repeat_event_header($repeati, get_the_ID() );
		}

		function oneevent_event_data(){
			$repeati = $this->RI;
			$lang = $this->L;

			// eventtop style
			$etsty = EVO()->cal->get_prop('evosm_eventtop_style');
			if(!$etsty) $etsty = 'color';

			$single_events_args = apply_filters('eventon_single_event_page_data',array(
				'etc_override'=>'yes',
				'eventtop_style'=> ($etsty == 'color'? 2:0),
			));

			$content =  EVO()->calendar->get_single_event_data( get_the_ID(), $lang, $repeati, $single_events_args);		
			
			echo $content[0]['content'];
		}

	function page_header(){
		wp_enqueue_style( 'evo_single_event');	
		global $post;
			
		get_header(apply_filters('evo_header_template',null));
	}

	// page content
		function page_content(){
			// only loggedin users can see single events
			$onlylogged_cansee = (!empty($this->evo_opt['evosm_loggedin']) && $this->evo_opt['evosm_loggedin']=='yes') ? true:false;

			if(!$this->EVENT) return false;
			
			$thisevent_onlylogged_cansee = $this->EVENT->check_yn('_onlyloggedin');

			// pluggable access restriction to event
				$continue_with_page_content = apply_filters('evo_single_page_access', true, $onlylogged_cansee );

			if(!$continue_with_page_content) return false;

			if( (!$onlylogged_cansee || ($onlylogged_cansee && is_user_logged_in() ) ) && 
				( !$thisevent_onlylogged_cansee || $thisevent_onlylogged_cansee && is_user_logged_in())  
			){				
				eventon_get_template_part( 'content', 'single-event' );	

			}else{
				echo "<p>".evo_lang('You must login to see this event')."<br/><a class='button' href=". wp_login_url() ." title='".evo_lang('Login')."'>".evo_lang('Login')."</a></p>";
			}
	
		}
	// sidebar 
		function sidebar(){
			// sidebar
			if(!evo_settings_check_yn($this->evo_opt, 'evosm_1')) return false;	
				
			if ( is_active_sidebar( 'evose_sidebar' ) ){

				?>
				<?php //get_sidebar('evose_sidebar'); ?>
				<div class='evo_page_sidebar'>
					<ul id="sidebar">
						<?php dynamic_sidebar( 'evose_sidebar' ); ?>
					</ul>
				</div>
				<?php
			}
		}
		public function has_evo_se_sidebar(){
			return evo_settings_check_yn($this->evo_opt, 'evosm_1')? true: false;
		}

	// comments
		function comments(){
			if(evo_settings_check_yn($this->evo_opt, 'evosm_comments_hide')) return;	
			?>
			<div id='eventon_comments'>
			<?php comments_template( '', true );	?>
			</div>
			<?php
		}

	// redirect script
		function redirect_script(){
			ob_start();
			?>
			<script> 
				href = window.location.href;
				var cleanurl = href.split('#');
				hash =  window.location.hash.substr(1);
				hash_ri = hash.split('=');

				if(hash_ri[1]){
					repeatInterval = parseInt(hash_ri[1]);
					if(href.indexOf('?') >0){
						redirect = cleanurl[0]+'&ri='+repeatInterval;
					}else{
						redirect = cleanurl[0]+'?ri='+repeatInterval;
					}
					window.location.replace( redirect );
				}
			</script>
			<?php

			echo ob_get_clean();
		}

	// get month year for event header // DEPRECATING
		function get_single_event_header($event_id, $repeat_interval='', $lang='L1'){			
			return false;
		}

	// get repeat event page header
		function repeat_event_header($ri, $eventid){	

			if( !$this->EVENT->is_repeating_event() ) return false;			

			$repeat_count = $this->EVENT->get_repeats_count();

			// if there is only one time range in the repeats that means there are no repeats
			if($repeat_count == 0) return false;
			
			$date = new evo_datetime();

			$ev_vals = $this->EVENT->get_data();

			global $EVOLANG;
			
			echo "<div class='evose_repeat_header'><p><span class='title'>".evo_lang('This is a repeating event'). "</span>";
			echo "<span class='ri_nav'>";

			// previous link
			if($ri>0){ 

				$prev_unixs = $this->EVENT->is_repeat_index_exists( $ri -1 );

				if($prev_unixs && isset($prev_unixs[0]) && $prev_unixs[0] > 0){

					$prev_unix = (int)$prev_unixs[0];

					$text = '';

					if($this->EVENT->is_year_long()){
						$text = date('Y', $prev_unix);
					}elseif( $this->EVENT->is_month_long() ){
						$text = $date->get_readable_formatted_date( $prev_unix, 'F, Y');
					}else{
						$text = $date->get_readable_formatted_date( $prev_unix );
					}


					$prev_link = $this->EVENT->get_permalink( ($ri-1), $this->L);
					
					echo "<a href='{$prev_link}' class='prev' title='{$text}'><b class='fa fa-angle-left'></b><em>{$text}</em></a>";
				}				

			}

			// next link 
			if($ri<$repeat_count){
				$ri = (int)$ri;

				$next_unixs = $this->EVENT->is_repeat_index_exists( $ri +1 );

				if($next_unixs && isset($next_unixs[0])){

					$next_unix = (int)$next_unixs[0];

					$text = '';

					if($this->EVENT->is_year_long()){
						$text = date('Y', $next_unix);
					}elseif( $this->EVENT->is_month_long() ){
						$text = $date->get_readable_formatted_date( $next_unix, 'F, Y');
					}else{
						$text = $date->get_readable_formatted_date( $next_unix );
					}

					//print_r($next); 
					$next_link = $this->EVENT->get_permalink( ($ri+1), $this->L );

					echo "<a href='{$next_link}' class='next' title='{$text}'><em>{$text}</em><b class='fa fa-angle-right'></b></a>";

				}				
				
			}
			
			echo "</span><span class='clear'></span></p></div>";
		}

		function get_cal_event_JSON(){
			$evopt1 = $this->evo_opt;
			$sin_event_evodata = apply_filters('evosin_evodata_vals',array(
				'mapformat'=> ((!empty($evopt1['evcal_gmap_format'])) ? $evopt1['evcal_gmap_format']:'roadmap'),
				'mapzoom'=> ( ( !empty($evopt1['evcal_gmap_zoomlevel']) ) ? $evopt1['evcal_gmap_zoomlevel']:'12' ),
				'mapscroll'=> ( !evo_settings_val('evcal_gmap_scroll' ,$evopt1)?'true':'false'),
				'evc_open'=>'yes',
				'mapiconurl'=> ( !empty($evopt1['evo_gmap_iconurl'])? $evopt1['evo_gmap_iconurl']:''),
				'maps_load'=> (!EVO()->calendar->google_maps_load ? 'yes':'no'),
			));
			return $sin_event_evodata;
		}
		function get_evo_data(){			
			$_cd = '';
			foreach ($this->get_cal_event_JSON() as $f=>$v){
				$_cd .='data-'.$f.'="'.$v.'" ';
			}
			return $_cd;
		}

		function get_event_data($event_id){
			$output = array();

			$output['name'] = get_the_title($event_id);
			return $output;
		}


}

