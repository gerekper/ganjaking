<?php
/**
 * Template Loader
 *
 * @class 		EVO_Template_Loader 
 * @version		2.5
 * @package		Eventon/Classes
 * @category	Class
 * @author 		AJDE
 */
class EVO_Template_Loader {
	public function __construct() {
		add_filter( 'template_include', array( $this, 'template_loader' ) , 99);
	}

	public function template_loader( $template ) {
		global $eventon_sin_event, $eventon;

				
		$file='';
		$sure_path = AJDE_EVCAL_PATH . '/templates/';	

		// get child theme path incase user is using child theme
		$childThemePath = get_stylesheet_directory();	
		
		// Paths to check
		$paths = apply_filters('eventon_template_paths', array(
			0=>TEMPLATEPATH.'/',
			1=>TEMPLATEPATH.'/'.$eventon->template_url,
			2=>$childThemePath.'/',
			3=>$childThemePath.'/'.$eventon->template_url,
		));
		
		$evOpt = evo_get_options('1');
		$events_page_id = evo_get_event_page_id($evOpt);
		
		// single and archive events page
		if( is_single() && get_post_type() == 'ajde_events' ) {

			// check if ditch single event template is enabled
			if(!empty($evOpt['evo_ditch_sin_template']) && $evOpt['evo_ditch_sin_template']=='yes')
				return $template;

			include_once('class-single-event.php');
			new evo_sinevent();
			
			$paths[] 	= AJDE_EVCAL_PATH . '/templates/';
			$file 	= 'single-ajde_events.php';

		// if this page is event archive page
		}elseif ( is_post_type_archive( 'ajde_events' )  ) {
			$file__ = evo_get_event_template($evOpt);
			$file 	= $file__;
			$paths[] 	= ($file__ == 'archive-ajde_events.php')?
				AJDE_EVCAL_PATH . '/templates/': get_template_directory();
		}
		// Event type taxonomy archive page
		elseif( is_tax(apply_filters('evo_tempload_et_types', array('event_type', 'event_type_2', 'event_type_3', 'event_type_4','event_type_5','event_type_6',
			'event_type_7','event_type_8','event_type_9','event_type_10'))) ){

			$this->pass_lang();
			$file 	= 'taxonomy-event_type.php';
			$paths[] 	= AJDE_EVCAL_PATH . '/templates/';
		}

		// Event location taxonomy
		elseif( is_tax(array('event_location'))){
			$this->pass_lang();

			$file 	= 'taxonomy-event_location.php';
			$paths[] 	= AJDE_EVCAL_PATH . '/templates/';
		}
		// Event organizer taxonomy
		elseif( is_tax(array('event_organizer'))){
			$this->pass_lang();
			$file 	= 'taxonomy-event_organizer.php';
			$paths[] 	= AJDE_EVCAL_PATH . '/templates/';
		}

		$file = apply_filters('evo_template_loader_file', $file);

		// FILE Exist
			if ( $file ) {			
				// each path
				foreach($paths as $path){
					//echo $path.$file.'<br/>';
					if(file_exists($path.$file) ){						
						$template = $path.$file;
						break;
					}
				}		
				
				// if template file path not found	
				if ( ! $template ) { 
					$template = AJDE_EVCAL_PATH . '/templates/' . $file;
				}
			}		
		
		return $template;
	}

	
	
	// language value for the archive pages
	function pass_lang(){
		if( isset($_GET['l'])) EVO()->lang = $_GET['l'];

	}
}
new EVO_Template_Loader();