<?php
/**
 * AJDE wp-admin all the other required parts for wp-admin
 * @version 2.8
 * LEGACY
 */

if(class_exists('ajde_wp_admin')) return;

class ajde_wp_admin{
	public $content = '';
	public function __construct(){}	

	// date time selector
		function print_date_time_selector($A){	EVO()->elements->print_date_time_selector( $A );	}

	// ONLY time selector
		function print_time_selector($A){ EVO()->elements->print_time_selector( $A );		}
		function _get_date_picker_data(){	return EVO()->elements->_get_date_picker_data();	}
		function _print_date_picker_values(){	EVO()->elements->_print_date_picker_values();	}

	// icon selector
		function icons(){	return EVO()->elements->get_icon_html();		}
		function get_font_icons_data(){	return EVO()->elements->get_font_icons_data();	}

	// wp admin tables
		function start_table_header($id, $column_headers, $args=''){
			EVO()->elements->start_table_header($id, $column_headers, $args);
		}
		function table_row($data='', $args=''){	EVO()->elements->table_row($data, $args);	}
		function table_footer(){	EVO()->elements->table_footer();	}

	// select row
		function _print_row_select($A){
			echo EVO()->elements->get_element(array(
				'row_class'=> $A['class'],
				'name'=>$A['name'],
				'value'=> $A['def_val'],
				'options'=> $A['options'],
			)); 
		}

		// tool tips
		function tooltips($content, $position='', $echo = false){
			$content = EVO()->elements->tooltips($content, $position);
			if($echo){ echo $content;  }else{ return $content; }	
		}
		function echo_tooltips($content, $position=''){		$this->tooltips($content, $position,true);	}
	// YES NO Button
		function html_yesnobtn($args=''){	return EVO()->elements->yesno_btn($args);	}	

	// lightbox content box
		function lightbox_content($arg){	EVO()->lightbox->admin_lightbox_content($arg);	}
		

}