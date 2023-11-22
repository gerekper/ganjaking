<?php
/** 
 * EventON Intergration with Visual Composer
 *
 * @version 2.3.23
 */
class evo_vc_intergration{
	public function __construct(){
		add_action( 'vc_before_init', array($this,'your_name_integrateWithVC' ));		
	}
	function your_name_integrateWithVC() {

		if(!function_exists('vc_add_shortcode_param')) return false;

		vc_add_shortcode_param( 'yesnoSelect', array($this, 'custom_param_select_yesno' ));
		vc_add_shortcode_param( 'evoyesno', array($this, 'custom_param_yesno' ));
		vc_add_shortcode_param( 'evonotes', array($this, 'custom_param_note' ));

		global $eventon;

		// scripts and styles used in evo VC
			$scripts = array();		
			$styles = array(
				AJDE_EVCAL_URL.'/assets/css/admin/vc.css'
			);

	   	vc_map( array(
	      "name" => __( "EventON", "eventon" ),
	      "base" => "add_eventon",
	      "description"		=> __("Add Calendar","eventon"),
	      "class" => "evo-calendar",
	      "category" => __( "Content", "eventon"),
	      'admin_enqueue_js' => $scripts,
	      'admin_enqueue_css' => $styles,
	      	"params" => array(
	         	array(
	            	"type" => "evonotes",
	            	"holder" => "div",'param_name'=>'evonotes',
	            	"content" => __( "To use the full range of eventON supported shortcode options, use eventON <a href='http://www.myeventon.com/documentation/shortcode-generator/' target='_blank'>Shortcode Generator</a> to generator an eventON shortcode string and then place that inside a <b>Text Block</b> within Visual Composer. Options available in this Visual Composer element for eventON are only common used options.", "eventon" ),
	         	),array(
	            	"type" => "evoyesno",
	            	"param_name" => "show_et_ft_img",
	            	"value" => '',"holder" => "div",
	            	"label" => __( "Show featured image", "eventon" )
	         	),array(
	            	"type" => "evoyesno",
	            	//"heading" => __( "Show Featured Image", "eventon" ),
	            	"param_name" => "ft_event_priority",
	            	"value" => '',"holder" => "div",
	            	"label" => __( "Feature event priority", "eventon" ),
	            	"guide" => __( "Move feaured events above others", "eventon" ),
	         	),array(
	            	"type" => "evoyesno",
	            	"param_name" => "only_ft",
	            	"value" => '',"holder" => "div",
	            	"label" => __( "Show only featured events", "eventon" ),
	            	"guide" => __( "Display only featured events in the calendar", "eventon" ),
	         	),array(
	            	"type" => "evoyesno",
	            	"holder" => "div","class" => "",
	            	"param_name" => "hide_past","value" => '',
	            	"label" => __( "Hide past events", "eventon" )
	         	),array(
	            	"type" => "dropdown",
	            	"holder" => "div","class" => "",
	            	"param_name" => "hide_past_by","value" => array('Start Date/Time'=>'ss','End Date/Time'=>'ee'),
	            	"heading" => __( "Hide past events by", "eventon" )
	         	),array(
	            	"type" => "dropdown",
	            	"holder" => "div","class" => "",
	            	"param_name" => "event_order","value" => array('ASC','DESC'),
	            	"heading" => __( "Event Order", "eventon" )
	         	),array(
	            	"type" => "textfield",
	            	"holder" => "div","class" => "",
	            	"param_name" => "event_count","value" => '',
	            	"heading" => __( "Event Count Limit (eg. 4)", "eventon" )
	         	),array(
	            	"type" => "textfield",
	            	"holder" => "div","class" => "",
	            	"param_name" => "event_type","value" => '',
	            	"heading" => __( "Event Type Filter (Term IDs separated by commas)", "eventon" )
	         	),array(
	            	"type" => "textfield",
	            	"holder" => "div","class" => "",
	            	"param_name" => "event_type_2","value" => '',
	            	"heading" => __( "Event Type #2 Filter (Term IDs separated by commas)", "eventon" )
	         	),array(
	            	"type" => "evoyesno",
	            	"holder" => "div","class" => "",
	            	"param_name" => "etc_override","value" => '',
	            	"label" => __( "Event Type Color Override", "eventon" ),
	            	"guide" => __( "Select this to override event color with event type color, if set", "eventon" ),
	         	),array(
	            	"type" => "dropdown",
	            	"holder" => "div","class" => "",
	            	"param_name" => "ux_val","value" => array('None'=>0,'Do not Interact'=>'X','Slide Down EventCard'=>'1','Lightbox Popup Window'=>'3'),
	            	"heading" => __( "User Interaction", "eventon" )
	         	),array(
	            	"type" => "evoyesno",
	            	"holder" => "div","class" => "",
	            	"param_name" => "evc_open","value" => '',
	            	"label" => __( "Open EventCards on Load", "eventon" ),
	            	"guide" => __( "Open eventcards when the calendar first load on the page by default. This will override the settings saved for default calendar.", "eventon" ),
	         	),array(
	            	"type" => "evoyesno",
	            	"holder" => "div","param_name" => "jumper","value" => '',
	            	"label" => __( "Show Jump Months Option", "eventon" ),
	            	"guide" => __( "Display month jumper on the calendar", "eventon" ),
	         	),array(
	            	"type" => "evoyesno",
	            	"holder" => "div","param_name" => "hide_so","value" => '',
	            	"label" => __( "Hide Sort Options Section", "eventon" ),
	            	"guide" => __( "This will hide sort options section on the calendar", "eventon" ),
	         	)
	      	)
	    ) );
	}

	// yes no as dropdown field
		function custom_param_select_yesno( $settings, $value ) {	
			$out = '<div class="evovc_dropdown_cont">';   
			$out.= '<select name="' . esc_attr( $settings['param_name'] ) . '" class="wpb_vc_param_value wpb-input wpb-select ' .
					 esc_attr( $settings['param_name'] ) . ' ' .
					 esc_attr( $settings['type'] ) . '_field" type="text" value="' . esc_attr( $value ) . '">' 
			;
		
			foreach( array('yes'=>'Yes', 'no'=>'No') as $field => $label ){
				$out.=sprintf('<option %3$s value="%2$s">%1$s</option>',
					$label,
					$field,
					( $value==$field ? 'selected="selected"' : '' )
				);
				$out.="\n";
			}
				  	
			$out.= '</select>';	
			$out.= '</div>';
				 
			return $out;	
		}

	// eventon yes no field
		function custom_param_yesno( $settings, $value ) {

			$out = "<div class='evovc_dropdown_cont'><p class='yesno_leg_line' style='padding-top:0px'>";
					
			$out.= eventon_html_yesnobtn(
			array(
				'id'=>esc_attr( $settings['param_name'] ), 
				'var'=>esc_attr( $value ),
				'input'=>true,
				'inputAttr'=>array('class'=>'wpb_vc_param_value wpb-textinput'),
				'label'=> esc_attr( $settings['label'] ),
				'guide'=> (!empty($settings['guide'])?esc_attr( $settings['guide'] ):'' )		
			));
				
			$out.= '</p></div>';
			return $out;
		}
	// eventon note row
		function custom_param_note( $settings, $value ) {
			$out = "<div class='evovc_notes'><p>";			
			$out.= "<b>NOTES:</b> ";
			$out.=  $settings['content'];				
			$out.= '</p></div>';
			return $out;
		}


}
new evo_vc_intergration();