<?php
/**
 * Styles for the calendar
 */

class EVOIA_Styles{
	public function __construct(){
		add_action('eventon_inline_styles', array($this, 'styles'));
	}

	// dynamic styles
		public function styles(){
			?>
			#evcal_list .eventon_list_event.anypost a.sin_val.hasFtIMG .evcal_desc{padding-left: 100px;}
			#evcal_list .eventon_list_event.anypost a.sin_val .evcal_desc{padding-left: 15px;}

			<?php
		}
}