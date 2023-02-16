<?php
/** 
 * Search addon shortcodes
 * @version   0.7
 */
class EVOSR_shortcodes{
	public function __construct(){
		add_shortcode('add_eventon_search', array($this, 'search_content'), 10, 1);
		add_filter('eventon_shortcode_popup',array($this,'add_shortcode_options'), 10, 1);
	}

	function search_content($atts){
		global $eventon_sr, $eventon;
		ob_start(); 

		// enqueue required eventon scripts
		$eventon->frontend->load_default_evo_scripts();
		wp_enqueue_script('eventon_gmaps');
		wp_enqueue_script('eventon_init_gmaps');
		wp_enqueue_script('evcal_gmaps');

		$defaults = array(
			'event_type'=>'',
			'event_type_2'=>'',
			'number_of_months'=>12,
			'lang'=>'L1',
		);

		$data = '';
		foreach($defaults as $def=>$val){
			$val = !empty($atts[$def])? $atts[$def]: $val;
			if(empty($val)) continue;
			$data .= 'data-'.$def .'="' . ($val) .'"';
		}

		$eventon_sr->frontend->enque_script();

		?>
		<div id='evo_search' class='EVOSR_section '>
			<div class="evo_search_entry">
				<p class='evosr_search_box' >
					<input type="text" placeholder='<?php echo evo_lang_get('evoSR_001a','Search Calendar Events');?>' data-role="none">
					<a class='evo_do_search'><i class="fa fa-search"></i></a>
					<span class="evosr_blur"></span>
					<span class="evosr_blur_process"></span>
					<span class="evosr_blur_text"><?php echo evo_lang_get('evoSR_002','Searching');?></span>
					<span style="display:none" class='data' <?php echo $data;?>></span>
				</p>
				<p class='evosr_msg' style='display:none'><?php echo evo_lang_get('evoSR_003','What do you want to search for?');?></p>
			</div>
			<p class="evo_search_results_count" style='display:none'><span>10</span> <?php echo evo_lang_get('evoSR_004','Event(s) found');?></p>
			<div class="evo_search_results"></div>
		</div>
		<?php
		return ob_get_clean();
	}

	function add_shortcode_options($shortcode_array){
		global $evo_shortcode_box;
			
			$new_shortcode_array = array(
				array(
					'id'=>'s_SR',
					'name'=>'Search Box',
					'code'=>'add_eventon_search',
					'variables'=>array(
						array(
							'name'=>'<i>'.__('NOTE: This will allow you to drop an interactive event search field that allow users to search through all current events. You can further filter search results with below options.','eventon') .'</i>',
							'type'=>'note',							
						),
						$evo_shortcode_box->shortcode_default_field('event_type'),
						$evo_shortcode_box->shortcode_default_field('event_type_2'),
						$evo_shortcode_box->shortcode_default_field('lang'),
						$evo_shortcode_box->shortcode_default_field('number_of_months')
					)
				)
			);

			return array_merge($shortcode_array, $new_shortcode_array);
	}
}
new EVOSR_shortcodes();