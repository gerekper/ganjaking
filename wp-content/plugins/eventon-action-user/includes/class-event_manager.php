<?php
/**
 * Event Manager object
 * @version 
 */

class EVOAU_Event_Manager{
	private $atts = array();
	function __construct(){
		add_shortcode('evo_event_manager',array($this, 'event_manager'));
	}

	// Frontend event manager
		function event_manager($atts){
			
			EVO()->frontend->load_evo_scripts_styles();	

			do_action('evoau_manager_initiate');	

			if(!empty($atts)) $this->atts = $atts;

			ob_start();				
			echo $this->manager_template_load($atts);			
			return ob_get_clean();
		}

	// user event manager for front-end
	// @version 0.1
		function manager_template_load($atts){
			
			EVOAU()->frontend->print_frontend_scripts();

			if(!$atts || empty($atts)) $atts = array();

			// set global language
				$this->lang = (!empty($atts['lang']))? $atts['lang']:'L1';
				evo_set_global_lang($this->lang);		

			// intial variables
			$current_user = get_user_by( 'id', get_current_user_id() );
			$USERID = is_user_logged_in()? get_current_user_id(): false;
			
			//$current_page_link = get_page_link();
			$current_page_link = 'http' . (isset($_SERVER['HTTPS']) ? 's' : '') . '://' . "{$_SERVER['HTTP_HOST']}/{$_SERVER['REQUEST_URI']}";
			$atts['current_link'] = $current_page_link;

			// loading child templates
				$file_name = 'event_manager.php';
				$paths = array(
					0=> TEMPLATEPATH.'/'. EVO()->template_url.'/actionuser/',
					1=> STYLESHEETPATH.'/'. EVO()->template_url.'/actionuser/',
					2=> EVOAU()->plugin_path.'/templates/',
				);

				foreach($paths as $path){	
					if(file_exists($path.$file_name) ){	
						$template = $path.$file_name;	
						break;
					}
				}

			require_once($template);
		}
	// user created events
		function get_user_events($userid){
			
			// events created by the user
			$events = new WP_Query(array(
				'post_type'=>'ajde_events',
				'posts_per_page'=>-1,
				'post_status'=>'any',
				'author__in'=>array($userid)
			));

			$eventIDs = array();

			if($events->have_posts()){
				while($events->have_posts()): $events->the_post();
					$eventIDs[$events->post->ID] = array(
						$events->post->post_title,
						$events->post->post_status,
						$events->post->ID
					);
				endwhile;
				wp_reset_postdata();
			}

			if(evo_settings_check_yn(EVOAU()->frontend->evoau_opt, 'evoau_assigned_emanager')){

				// events assigned to the user
				$events = new WP_Query(array(
					'post_type'=>'ajde_events',
					'posts_per_page'=>-1,
					'post_status'=>'any',
					'tax_query' => array(
						array(
							'taxonomy' => 'event_users',
							'field'    => 'slug',
							'terms'    => $userid,
						),
					),
				));

				if($events->have_posts()){
					while($events->have_posts()): $events->the_post();
						$eventIDs[$events->post->ID] = array(
							$events->post->post_title,
							$events->post->post_status,
							$events->post->ID
						);
					endwhile;
					wp_reset_postdata();
				}
			}

			return $eventIDs;
			
		}
	
	// footer json data
		function footer_json_data(){
			?>
			<div class='evoau_manager_json' data-js='<?php echo json_encode($this->atts);?>'></div>
			<?php
		}

	// verify is user has events
		function verify_user_events($events){
			if(!isset($atts['hidden_till_events']) ) return true;
			if($atts['hidden_till_events'] == 'no') return true;

			if($events ) return true;
			return false;
		}

	// print event manager styles into the page body
		public function print_em_styles(){
			
			echo "<style type='text/css'>";
			include_once(EVOAU()->plugin_path."/assets/au_eventmanager_styles.php");
			echo "</style>";
		}
}