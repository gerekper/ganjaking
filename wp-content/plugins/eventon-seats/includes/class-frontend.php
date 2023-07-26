<?php
/**
 * Event seats front end class
 *
 * @author 		AJDE
 * @category 	Admin
 * @package 	EventON-st/classes
 * @version     1.2.1
 */
class evost_front{
	public $evopt1, $evopt2, $opt_tx;
	function __construct(){
		global $evost;

		$this->evopt1 = get_option('evcal_options_evcal_1');
		$this->evopt2 = get_option('evcal_options_evcal_2');
		$this->opt_tx = get_option('evcal_options_evcal_tx');

		// scripts and styles 
		add_action( 'evo_register_other_styles_scripts', array( $this, 'register_styles_scripts' ) );
		add_action('eventon_enqueue_scripts', array($this, 'enqueue_scripts'));
		add_filter('evo_ajax_script_data', array($this, 'evo_script_data'), 10, 1);

	}

	// page script data
		function evo_script_data($data){

			$data['text']['evost_1'] = evo_lang('Hover over a seat to see the pricing information');

			return $data;
		}

	// STYLES:  
		public function register_styles_scripts(){

			if(is_admin()) return;
			
			wp_register_style( 'evost_styles',EVOST()->assets_path.'ST_styles.css');
			
			wp_register_script('evost_draw',EVOST()->assets_path.'evost_map_draw.js', array('jquery'), EVOST()->version, true );
			wp_register_script( 'evost_handlebars',EVOST()->assets_path.'handlebars.js',array('jquery'), EVOST()->version, true);
			wp_register_script('evost_script',EVOST()->assets_path.'ST_script.js', array('jquery'), EVOST()->version, true );
			wp_localize_script( 
				'evost_script', 
				'evost_ajax_script', 
				array( 
					'ajaxurl' => admin_url( 'admin-ajax.php' ) , 
					'postnonce' => wp_create_nonce( 'evost_nonce' )
				)
			);
							
		}
		public function print_scripts(){				
			//wp_enqueue_script('evost_handlebars');			
			wp_enqueue_script('evost_draw');			
			wp_enqueue_script('evost_script');
		}
		function enqueue_scripts(){
			$this->print_scripts();	
			$this->print_styles();	
		}
		function print_styles(){	
			wp_enqueue_style( 'evost_styles');	
		}

	// SUPPORT functions
		// initial seat content
			function print_init_html_content(){
				?>
				<div class='evo_loading_bar_holder h100'>
					<div class="evo_loading_bar hi_50"></div>
					<div class="evo_loading_bar hi_50"></div>
					<div class="evo_loading_bar hi_50"></div>
					<div class="evo_loading_bar wid_40 hi_50"></div>
				</div>
				<div class="evost_inline_seat_map evost_seat_selection"></div>	
				<div class="evost_seats_preview" style="display: none;"></div>
				<div class='evost_seats_in_cart' style="display: none;"></div>
				<div class='evost_seats_msg'><p class='evost_msg'></p></div>
				<?php
			}

		// get seat type @since 1.2
			function get_seat_type($slug, $readable = false){
				if(is_array($slug)) return false;
				
				// if slug has whitespaces = its not the correct slug
				if( strpos($slug, ' ') !== false ) return false;

				$seat_type = 'seat';

				// una
				if( strpos($slug, '-') === false ) $seat_type = 'unaseat';
				if( strpos($slug, 'B') !== false ) $seat_type = 'booseat';

				if( $readable ) $seat_type = $this->get_seat_type_readable( $seat_type  );
				
				return $seat_type;
			}
			function get_seat_type_readable( $seat_type , $end = 'client' ){

				$return = $end == 'admin' ? __('Regular Seat') : evo_lang('Regular Seat');
				if( $seat_type =='unaseat') 
					$return = $end == 'admin' ? __('Unassigned Seat') : evo_lang('Unassigned Seat');
				if( $seat_type =='booseat') 
					$return = $end == 'admin' ? __('Booth') : evo_lang('Booth');

				return $return;
			}

		// RETURN: language
			function lang($variable, $default_text){
				return EVOST()->lang($variable, $default_text);
			}		
}