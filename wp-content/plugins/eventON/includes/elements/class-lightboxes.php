<?php 
/**
 * EventON Lightboxes for back and front
 * @version 2.9
 */

class EVO_Lightboxes{
	private $content = '';

	public function __construct(){
		add_action( 'evo_page_footer', array( $this, 'frontend_page_footer' ) ,15);
		add_action('admin_footer', array($this, 'admin_footer'));
		add_action('wp_footer', array($this, 'page_footer'));
	}

	// page footer
	public function page_footer(){
		echo "<div class='evo_elms'><em class='evo_tooltip_box'></em></div>";
	}

	// frontend lightbox
		function frontend_page_footer(){
			$lightboxWindows = apply_filters('evo_frontend_lightbox', array(
				'eventcard'=> array(
					'id'=>'',
					'classes'=>'eventon_events_list',
					'CLin'=>'eventon_list_event evo_pop_body evcal_eventcard',
					'CLclosebtn',
					'content'=>''
				)
			));

			if(is_array($lightboxWindows) && count($lightboxWindows)>0){

				$display = (EVO()->cal->check_yn('evo_load_scripts_only_onevo','evcal_1') && !EVO()->cal->check_yn('evo_load_all_styles_onpages','evcal_1') )? 'none':'block';

				echo "<div class='evo_lightboxes' style='display:{$display}'>";
				foreach($lightboxWindows as $key=>$lb){
					?>
					<div class='evo_lightbox <?php echo $key;?> <?php echo !empty($lb['classes'])? $lb['classes']:'';?>' id='<?php echo !empty($lb['id'])? $lb['id']:'';?>' >
						<div class="evo_content_in">													
							<div class="evo_content_inin">
								<div class="evo_lightbox_content">
									<div class='evo_lb_closer'>
										<a class='evolbclose <?php echo !empty($lb['CLclosebtn'])? $lb['CLclosebtn']:'';?>'>X</a>
									</div>
									<div class='evo_lightbox_body <?php echo !empty($lb['CLin'])? $lb['CLin']:'';?>'><?php echo !empty($lb['content'])? $lb['content']:'';?> </div>
								</div>
							</div>							
						</div>
					</div>
					<?php
				} // endforeach
				echo "</div>";
			}
		}

	// ADMIN
	function admin_footer($content=''){
		echo "<div class='ajde_admin_lightboxes'>";		
		echo $this->content;
		echo "</div><div class='evo_elms'><em class='evo_tooltip_box'></em></div>";
	}

	function admin_lightbox_content($arg){
		$defaults = array(
			'content'=>'',
			'class'=>'regular',
			'attr'=>'',
			'title'=>'',
			'subtitle'=>'',
			'type'=>'normal',
			'hidden_content'=>'',
			'width'=>'',
			'outside_click'=>true,
			'preloading'=>false, // preloading will replace content with loading text
		);
		$args = (!empty($arg) && is_array($arg) && count($arg)>0) ? 
			array_merge($defaults, $arg) : $defaults;

		$ajdeCLASSES = array();

		// ajde_popup classes
			if(!empty($args['type']) && $args['type']=='padded')	$ajdeCLASSES[] = 'padd';
			if(!$args['outside_click']) $ajdeCLASSES[] = 'nooutside';
			
			$ajdeCLASSES[] = $args['class'];				

			$ajdeCLASSES = implode(' ', $ajdeCLASSES);

		//print_r($args);
		$content='';
		$content .= 
			"<div id='ajde_popup_outter' class='ajde_admin_lightbox {$ajdeCLASSES}'>
			<div class='evo_content_in'>
			<div class='evo_content_inin'>
			<div class='ajde_popup {$ajdeCLASSES}' {$args['attr']} style='". ( (!empty($args['width']))? 'width:'.$args['width'].'px;':null )."'>	
				<div class='ajde_header'>
					<a class='ajde_backbtn' style='display:none'><i class='fa fa-angle-left'></i></a>
					<p id='ajde_title' class='ajde_lightbox_title'>{$args['title']}</p>
					". ( (!empty($args['subtitle']))? "<p id='ajde_subtitle'>{$args['subtitle']}</p>":null) ."
					<a class='ajde_close_pop_btn'>X</a>
				</div>							
				<div id='ajde_loading'></div>";
			// preloading
				$innner = ($args['preloading'])? '<p class="loading">Loading</p>':$args['content'];

			$content .= (!empty($args['max_height']))? "<div class='ajde_lightbox_outter maxbox' >":null;
			$content .= "<div class='ajde_popup_text'>{$innner}</div>";
			$content .= (!empty($args['max_height']))? "</div>":null;
			$content .= "<p class='message'></p>
				
			</div>
			</div>
			</div>
			</div>";
		
		$this->content .= $content;
		
	}


}