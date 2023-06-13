<?php
/**
 * EVORC front-end
 * @version 	1.0.3
 */

class EVORC_Frontend{

	public $day_names = array();
	public $focus_day_data= array();
	public $shortcode_args;

	function __construct(){
		$this->integrate_general();
	}
	// STYLES:  
		public function register_styles_scripts(){
			if(is_admin()) return;
			
			wp_register_style( 'evost_styles',EVORC()->assets_path.'styles.css');			
			wp_register_script('evorc_script',EVORC()->assets_path.'script.js', array('jquery'), EVORC()->version, true );
			wp_localize_script( 
				'evorc_script', 
				'evorc_ajax_script', 
				array( 
					'ajaxurl' => admin_url( 'admin-ajax.php' ) , 
					'postnonce' => wp_create_nonce( 'evorc_nonce' )
				)
			);
			
			$this->print_scripts();
			add_action( 'wp_enqueue_scripts', array($this,'print_styles' ));				
		}
		public function print_scripts(){				
			wp_enqueue_script('evorc_script');		
		}
		function print_styles(){	wp_enqueue_style( 'evost_styles');	}

	// Integrate
		function integrate_general(){
			add_filter('evodata_taxonomy_terms', array($this, 'tax_terms'), 10,4);
			add_filter('evodata_location_term', array($this, 'event_location'), 10,2);
			add_filter('evodata_organizer_term', array($this, 'event_organizer'), 10,2);
			add_filter('evodata_featured', array($this, '_featured'), 10,2);
			add_filter('evodata_completed', array($this, '_completed'), 10,2);
			add_filter('evodata_event_status', array($this, '_event_status'), 10,2);
			add_filter('evodata_event_status_reason', array($this, '_event_status_reason'), 10,3);
			add_filter('evodata_title', array($this, 'title'), 10,2);
			add_filter('evodata_subtitle', array($this, 'subtitle'), 10,2);
			add_filter('evodata_custom_data', array($this, 'customdata'), 10,3);
			add_filter('evodata_custom_data_value', array($this, 'customdata_value'), 10,3);
			add_filter('evodata_image', array($this, 'image'), 10,2);
			add_filter('evodata_hex', array($this, 'color'), 10,2);
			add_filter('evodata_vir_url', array($this, 'vir_url'), 10,2);
			add_filter('evodata_vir_pass', array($this, 'vir_pass'), 10,2);
		}
		function tax_terms( $terms, $tax, $term_id, $EVENT){
			if( $tax != 'event_organizer') return $terms;

			if( $terms && ! is_wp_error( $terms )){
				$output = $this->process_value('term', $tax, $terms, $EVENT, $term_id);

				// adjust for multiple terms returned in array format
				if( !is_array($output)) $output = array(0=>$output);

				return $output;
			}

			return $terms;
		}
		function event_location($V, $E){	return $this->process_value('term', 'event_location', $V, $E); 	}
		function event_organizer($V, $E){	return $this->process_value('term', 'event_organizer', $V, $E); 	}
		function _featured($V, $E){	return $this->process_value('yesno', '_featured', $V, $E); 	}
		function _completed($V, $E){	return $this->process_value('yesno', '_completed', $V, $E); 	}
		function _event_status($V, $E){		return $this->process_value('text', '_status', $V, $E);}
		function _event_status_reason($V, $field, $E){		
			return $this->process_value('text', $field, $V, $E);}
		function title($V, $E){	
			return $this->process_value('text', '_title', $V, $E); 	
		}
		function subtitle($V, $E){	return $this->process_value('text', 'subtitle', $V, $E); 	}
		function customdata($V, $E, $index){	
			return $this->process_value('customdata', $index, $V, $E); 	
		}
		function customdata_value( $V, $E, $index){
			return $this->process_value('customdata_value', $index, $V, $E); 
		}
		function image($V, $E){	
			return $this->process_value('text','event_image', $V, $E); 	
		}
		function color($V, $E){	
			return $this->process_value('text','evcal_event_color', $V, $E); 	
		}

		function vir_url($V, $E){
			return $this->process_value('text','_vir_url', $V, $E); 
		}
		function vir_pass($V, $E){
			return $this->process_value('text','_vir_pass', $V, $E); 
		}

		function process_value($type, $field, $V, $E , $term_id = ''){
			if( $E->ri == 0 ) return $V;
			$RC = new EVORC_Event($E, $E->ri);
			
			$RD = $RC->is_repeat_has_data( $field );

			if(!$RD && ( !in_array($type, array('customdata', 'customdata_value') ) ) ) return $V;

			switch ($type) {
				case 'customdata':
					foreach(array(
						'value'=>"_evcal_ec_f".$field."a1_cus",
						'valueL'=>"_evcal_ec_f".$field."a1_cusL",
						'target'=>"_evcal_ec_f".$field."_onw",
					) as $v=>$f){
						$RD = $RC->is_repeat_has_data( $f );
						if(!$RD) continue;

						$V[$v] = $RD;
					}					
				break;
				case 'customdata_value':
				 	$ff = "_evcal_ec_f".$field."a1_cus";
					$RD = $RC->is_repeat_has_data( $ff );
					if($RD) $V = $RD;					
				break;				
				case 'term':

					$term_id = !empty($term_id) ? $term_id : (int)$RD;

					$O_terms = get_term_by('id', $term_id , $field);
					if ( $O_terms && ! is_wp_error( $O_terms ) ){
						return $O_terms;
					}
				break;				
				case 'text':
					return $RD;
				break;
				case 'yesno':
					return $RD == 'yes'? true: false;
				break;
			}

			
			return $V;
			
		}
}