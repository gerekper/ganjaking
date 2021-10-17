<?php
/**
 * Admin settings class
 *
 * @author 		AJDE
 * @category 	Admin
 * @package 	eventon-photos/classes
 * @version     0.1
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class evoep_admin{
	
	public $optRS;
	function __construct(){
		add_action('admin_init', array($this, 'admin_init'));
		add_action( 'admin_menu', array( $this, 'menu' ),9);
	}

	// INITIATE
		function admin_init(){

			// icon
			add_filter( 'eventon_custom_icons',array($this, 'evoEP_custom_icons') , 10, 1);

			add_filter('evo_eventedit_feature_img_id_check', array($this, 'feature_img_check'),10,1);

			// eventCard inclusion
			add_filter( 'eventon_eventcard_boxes',array($this,'evoEP_add_toeventcard_order') , 10, 1);
			add_filter( 'evo_event_images_max',array($this,'event_max_photos') , 10, 1);

			// language
			add_filter('eventon_settings_lang_tab_content', array( $this, 'language' ), 10, 1);	

			global $pagenow, $typenow, $wpdb, $post;	
			
			if ( $post && $typenow == 'post' && ! empty( $_GET['post'] ) ) {
				$typenow = $post->post_type;
			} elseif ( empty( $typenow ) && ! empty( $_GET['post'] ) ) {
		        $typenow = get_post_type( $_GET['post'] );
		    }
			
			if ( $typenow == '' || $typenow == "ajde_events" ) {
				// Event Post Only
				$print_css_on = array( 'post-new.php', 'post.php', 'edit.php' );
				foreach ( $print_css_on as $page ){
					add_action( 'admin_print_styles-'. $page, array($this,'evoEP_event_post_styles' ));		
				}
			}

			// settings
			add_filter('eventon_settings_tabs',array($this, 'evoEP_tab_array' ),10, 1);
			add_action('eventon_settings_tabs_evcal_ep',array($this, 'evoEP_tab_content' ));		
		}

	// 
		function feature_img_check($thumbnail_id){
			return true;
		}
		function event_max_photos(){
			return 999;
		}

	// other hooks
		function evoEP_event_post_styles(){
			wp_enqueue_style( 'evoEP_admin_post',EVOEP()->plugin_url.'/assets/EP_admin_post.css');
			wp_enqueue_script( 'evoEP_admin_post_script',EVOEP()->plugin_url.'/assets/EP_admin_script.js',array('jquery','jquery-ui-draggable','jquery-ui-sortable'), EVOEP()->version);
			wp_localize_script( 
				'evoEP_admin_post_script', 
				'evoEP_admin_ajax_script', 
				array( 
					'ajaxurl' => admin_url( 'admin-ajax.php' ) , 
					'postnonce' => wp_create_nonce( 'eventonep_nonce' )
				)
			);
		}
		function evoEP_add_toeventcard_order($array){
			$array['evoep']= array('evoep',__('Event Photos','eventon'));
			return $array;
		}

		function evoEP_custom_icons($array){
			$array[] = array('id'=>'evcal__evoEP_001','type'=>'icon','name'=>'Event Photos Icon','default'=>'fa-photo');
			return $array;
		}
		
		// EventON settings menu inclusion
		function menu(){
			add_submenu_page( 'eventon', 'Photos', __('Photos','eventon'), 'manage_eventon', 'admin.php?page=eventon&tab=evcal_ep', '' );
		}
	
	// language
		function language($_existen){
			$new_ar = array(
				array('type'=>'togheader','name'=>'ADDON: Photos'),	
					array('label'=>'Event Photos','name'=>'EVOEP','var'=>1),
				array('type'=>'togend'),
			);
			return (is_array($_existen))? array_merge($_existen, $new_ar): $_existen;
		}

	// TABS SETTINGS
		function evoEP_tab_array($evcal_tabs){
			$evcal_tabs['evcal_ep']='Photos';		
			return $evcal_tabs;
		}
		function evoEP_tab_content(){
			global $eventon;
			$eventon->load_ajde_backender();			
		?>
			<form method="post" action=""><?php settings_fields('evoep_field_group'); 
					wp_nonce_field( AJDE_EVCAL_BASENAME, 'evcal_noncename' );?>
			<div id="evcal_re" class="evcal_admin_meta">	
				<div class="evo_inside">
				<?php
					$cutomization_pg_array = array(
						array(
							'id'=>'evoEP1','display'=>'show',
							'name'=>'General Photos Settings',
							'tab_name'=>'General',
							'fields'=>array(
								array('id'=>'evoEP_skin','type'=>'dropdown','name'=>'Select lightbox theme','options'=>array('default'=>'Dark','light'=>'Light')),
								array('id'=>'evoEP_thumb','type'=>'dropdown','name'=>'EventCard thumbnail size (px)','options'=>array('def'=>'100x100','150'=>'150x150','75'=>'75x75','50'=>'50x50')),
								array('id'=>'evoEP_global_gal','type'=>'yesno',
									'name'=>'Enable separate photo gallery for all events, from additional event images',
									'legend'=> __('This will override individual event settings and create separate photo gallery for all events using additional event images.'),
								),	

						)),
						array(
							'id'=>'evoEPt',
							'name'=>'Basic Troubleshooting',
							'tab_name'=>'Troubleshoot','icon'=>'anchor',
							'fields'=>array(
								array('id'=>'evoEP_troublshooter','type'=>'customcode','code'=>$this->troubleshooter_code()),	
						))
					);							
					$eventon->load_ajde_backender();	
					$evcal_opt = get_option('evcal_options_evcal_ep'); 
					print_ajde_customization_form($cutomization_pg_array, $evcal_opt);	
				?>
			</div>
			</div>
			<div class='evo_diag'>
				<input type="submit" class="evo_admin_btn btn_prime" value="<?php _e('Save Changes') ?>" /><br/><br/>
				<a target='_blank' href='http://www.myeventon.com/support/'><img src='<?php echo AJDE_EVCAL_URL;?>/assets/images/myeventon_resources.png'/></a>
			</div>			
			</form>	
		<?php
		}

	function troubleshooter_code(){		
		$output = '<p><b><i>Photos does not show in eventcard?</i></b> <br/>Go to myEventON Settings > EventCard > Re-arrange the order of eventCard event data boxes -- make sure Event photos row is checked. And move this up or down and click "save changes"</p>';

		$output .= '<br/><p><b><i>How to add captions for images?</i></b> <br/>When you choose an image from Event Photos box in event edit page, in the light box for choose an image, make sure to fill in the <b>Caption</b> section for selected image. The caption for the image you enter is what is shown in frontend eventCard photos lightbox.</p>';

		return $output;
	}
}

new evoep_admin();