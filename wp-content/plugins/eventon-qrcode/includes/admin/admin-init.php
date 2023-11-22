<?php
/**
 * checking in users Admin side
 *
 * @author 		AJDE
 * @category 	Admin
 * @package 	eventon-qr/classes
 * @version     0.1
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class evoqr_admin{
	
	public $optRS;
	function __construct(){
		add_action('admin_init',array($this,'init'));
		add_filter('eventon_settings_tab1_arr_content', array( $this, 'qr_settings' ) ,10,1 );
		add_filter('eventon_settings_lang_tab_content', array( $this, 'language' ), 10, 1);
		//add_filter('eventon_core_capabilities', array($this, 'new_capability'),10, 1);
		
		// posts and pages
		add_action('post_submitbox_misc_actions', array($this, 'post_meta_box'),10,1);
		add_filter('display_post_states', array($this,'post_state'),10,2);

		// appearance styles
		add_filter( 'eventon_appearance_add', array($this,'appearance_settings') , 10, 1);
		add_filter( 'eventon_inline_styles_array',array($this,'dynamic_styles') , 1, 1);	
	}

	// INIT
		function init(){
			$content = '[evo_checking_page]';	

			//delete_option('eventon_checkin_page_id');

			// save page ID to eventON QR settings, if not saved
			$cal = EVO()->cal;
			$cal->set_cur('evcal_1');
			if( !$cal->get_prop('eventon_checkin_page_id') ){
				// create checking page at first load
				$page_id = eventon_create_page( 
					esc_sql( _x( 'checkin', 'page_slug', 'eventon' ) ),
					'eventon_checkin_page_id', 
					__( 'Checkin', 'eventon' ), 
					$content, 
					'' 
				);

				$cal->set_prop('eventon_checkin_page_id', $page_id);
			}
			
		}

	// styles
		function appearance_settings($array){
			$new[] = array('id'=>'evoqr','type'=>'hiddensection_open','name'=>'QR Checking Styles','display'=>'none');
			$new[] = array('id'=>'evoqr','type'=>'fontation','name'=>'Checking Page Colors',
				'variations'=>array(
					array('id'=>'evoqr_1', 'name'=>'Default Page Color','type'=>'color', 'default'=>'7ab954'),
					array('id'=>'evoqr_1a', 'name'=>'Default Font Color','type'=>'color', 'default'=>'ffffff'),
					array('id'=>'evoqr_2', 'name'=>'Invalid Page Color','type'=>'color', 'default'=>'ff5c5c'),
					array('id'=>'evoqr_2a', 'name'=>'Invalid Font Color','type'=>'color', 'default'=>'ffffff'),
					array('id'=>'evoqr_3', 'name'=>'Already Checked Page Color','type'=>'color', 'default'=>'25b8ff'),
					array('id'=>'evoqr_3a', 'name'=>'Already Checked Font Color','type'=>'color', 'default'=>'ffffff'),
					array('id'=>'evoqr_4', 'name'=>'Refunded Page Color','type'=>'color', 'default'=>'7d7d7d'),
					array('id'=>'evoqr_4a', 'name'=>'Refunded Font Color','type'=>'color', 'default'=>'ffffff'),
				)
			);
			$new[] = array('id'=>'evoqr','type'=>'hiddensection_close');

			return array_merge($array, $new);
		}

		function dynamic_styles($_existen){
			$new= array(
				array(
					'item'=>'.evo_checkin_page',
					'multicss'=>array(
						array('css'=>'background-color:#$', 'var'=>'evoqr_1','default'=>'7ab954'),
						array('css'=>'color:#$', 'var'=>'evoqr_1a','default'=>'ffffff')
					)						
				),
				array(
					'item'=>'.evo_checkin_page.no',
					'multicss'=>array(
						array('css'=>'background-color:#$', 'var'=>'evoqr_2','default'=>'ff5c5c'),
						array('css'=>'color:#$', 'var'=>'evoqr_2a','default'=>'ffffff')
					)						
				),array(
					'item'=>'.evo_checkin_page.already_checked',
					'multicss'=>array(
						array('css'=>'background-color:#$', 'var'=>'evoqr_3','default'=>'25b8ff'),
						array('css'=>'color:#$', 'var'=>'evoqr_3a','default'=>'ffffff')
					)						
				),array(
					'item'=>'.evo_checkin_page.refunded',
					'multicss'=>array(
						array('css'=>'background-color:#$', 'var'=>'evoqr_4','default'=>'7d7d7d'),
						array('css'=>'color:#$', 'var'=>'evoqr_4a','default'=>'ffffff')
					)						
				)
			);
			return (is_array($_existen))? array_merge($_existen, $new): $_existen;
		}

	// post meta box notices
		function post_meta_box($post){

			$cal = EVO()->cal;
			$cal->set_cur('evcal_1');

			if(!$cal->get_prop('eventon_checkin_page_id')) return false;
			if($cal->get_prop('eventon_checkin_page_id') != $post->ID) return false;

			?>
			<span style='display: block;padding: 10px 10px;background-color:#94c55e;color: #fff;'><?php _e('This is the EventON QR Code checkin page.','evoqr');?></span>
			<?php
		}

		function post_state($states, $post){
			if (  'page' == get_post_type( $post->ID ) &&  $post->post_name == 'checkin'){
		        $states[] = __('EventON QR Checking Page'); 
		    } 

		    return $states;
		}


	// qr code checking capability
		function new_capability($caps){
			$new_caps = $caps;			
			$new_caps[] = 'checkin_guests';		
			return $new_caps;
		}

	// QR code settings into eventon settings
		function qr_settings($array){
			$pages = new WP_Query(array('post_type'=>'page'));
			$_page_ar[]	='--';
			while($pages->have_posts()	){ $pages->the_post();								
				$page_id = get_the_ID();
				$_page_ar[$page_id] = get_the_title($page_id);
			}
			wp_reset_postdata();

			// get all available templates for the theme
				$templates = get_page_templates();
				$_templates_ar['archive-ajde_events.php'] = 'Default Eventon Template';
				$_templates_ar['page.php'] = 'Default Page Template';
			   	foreach ( $templates as $template_name => $template_filename ) {
			       $_templates_ar[$template_filename] = $template_name;
			   	}

			
			// Pages
			$P = array();
			$pages = get_pages(array(
				'post_status'=>'publish'
			));

			foreach($pages as $page){
				$P[$page->ID] = $page->post_title;
			}

			$new_array= $array;
			$new_array[]= array(
				'id'=>'eventon_qr',
				'name'=>'Settings for QR Code checking',
				'display'=>'none',
				'tab_name'=>'QR Code',
				'icon'=>'qrcode',
				'fields'=> apply_filters('evo_qr_setting_fields', array(
					
					array('id'=>'evoqr_001','type'=>'checkboxes','name'=>'Select user roles that is allowed to checkin guests. (Default is administrator)',
						'options'=> $this->_get_user_roles()
					),
					array('id'=>'eventon_checkin_page_id',
						'type'=>'dropdown',
						'name'=>'QR Code Check-in page',
						'options'=> $P
					),					
					array('id'=>'evoqr_checkinurl','type'=>'note',
						'name'=>__('NOTE: If you want to use a custom page as a checking page. Create a page, add shortcode [evo_checking_page] save and select that page as checking page from above menu.','eventon'),
					),
					array('id'=>'evoqr_encrypt_dis','type'=>'yesno',
						'name'=>__('Disable encrypted ticket numbers on ticket','evoqr'),
					),
					array('id'=>'evoqr_mode','type'=>'dropdown','name'=>__('QR code scanning Mode','evoqr'),
						'options'=>array(
							'def'=> __('Using QR Code scanner app (Default)','evoqr'),
							'gun'=> __('QR Code scanner gun','evoqr'),
						),
						'legend'=>__('If you select scanner gun as scanning mode, you will be able to go to checkin page, login with permissions and click on input field and scan QR codes which will submit upon scan complete.','evoqr'),
					),
					array('id'=>'evoqr_show_in_media','type'=>'yesno',
						'name'=>__('Show QR Code Images in Media Page','evoqr'),
					),
				)
				));
			
			return $new_array;
		}


		// get all available user roles
			function _get_user_roles(){
				$roles = array();
				global $wp_roles;

    			foreach ($wp_roles->roles as $role => $details) {
    				$roles[ esc_attr($role)] = $details['name']; 
    			}
    			return apply_filters('evoqr_user_roles', $roles);
			}


	// language
		function language($_existen){
			$new_ar = array(
				array('type'=>'togheader','name'=>'ADDON: QR Codes'),
					array('label'=>'Checkin Page','type'=>'subheader'),
						array('label'=>'Successfully un-checked ticket!','name'=>'evoQR_001',),
						array('label'=>'Ticket already un-checked!','name'=>'evoQR_002',),
						array('label'=>'Successfully Checked!','name'=>'evoQR_003',),
						array('label'=>'Already checked!','name'=>'evoQR_004',),
						array('label'=>'Type in Ticket ID','var'=>1),
						array('label'=>'Type another Ticket','var'=>1),
						array('label'=>'Submit','var'=>1),
						array('label'=>'Ticket has been refunded','var'=>1),
						array('label'=>'Ticket order is not completed','var'=>1),
						array('label'=>'Ticket order is refunded','var'=>1),
						array('label'=>'Ticket order is cancelled','var'=>1),
						array('label'=>'Ticket Order is ','name'=>'evoQR_order1',),
						array('label'=>'Ticket #','var'=>1),
						array('label'=>'You have RSVPed NO!','name'=>'evoQR_003x',),
						array('label'=>'Un-check this ticket','name'=>'evoQR_005',),
						array('label'=>'Enter a New Ticket ID','var'=>1),
						array('label'=>'Invalid Ticket ID','var'=>1),
						array('label'=>'You do not have permission!','name'=>'evoQR_007',),
						array('label'=>'Other Ticket Information','name'=>'evoQR_007a'),
						array('label'=>'Name','name'=>'evoQR_007_name'),
						array('label'=>'Count','name'=>'evoQR_007_count'),
						array('label'=>'Event Name','name'=>'evoQR_007_event-name'),
						array('label'=>'tickets in the same order','var'=>1),
						array('label'=>'Login required to checkin guests, please login','var'=>1),
						array('label'=>'Login Now','var'=>1),
						array('label'=>'Message for RSVP','name'=>'evoQR_008','placeholder'=>'You can use the below QRcode to checkin at the event'),
					array('type'=>'togend'),
				array('type'=>'togend'),
			);
			return (is_array($_existen))? array_merge($_existen, $new_ar): $_existen;
		}

}
new evoqr_admin();
