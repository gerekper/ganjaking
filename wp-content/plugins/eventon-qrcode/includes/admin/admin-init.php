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
						'name'=>__('Disable encrypted ticket numbers on ticket'),
					),
					array('id'=>'evoqr_mode','type'=>'dropdown','name'=>'QR code scanning Mode',
						'options'=>array(
							'def'=>'Using QR Code scanner app (Default)',
							'gun'=>'QR Code scanner gun',
						),
						'legend'=>'If you select scanner gun as scanning mode, you will be able to go to checkin page, login with permissions and click on input field and scan QR codes which will submit upon scan complete.',
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
