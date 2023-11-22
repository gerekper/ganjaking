<?php
/*
 * Plugin Name: EventON - PDFer
 * Plugin URI: http://www.myeventon.com/addons/pdfer
 * Description: Generate PDF Event confirmations for RSVP and Tickets
 * Author: Ashan Jay
 * Version: 0.6
 * Author URI: http://www.ashanjay.com/
 * Requires at least: 5.0
 * Tested up to: 5.6.2
 */

class evo_pdf{
	
	public $version='0.6';
	public $eventon_version = '3.0.6';
	public $EVOTX_version = '1.7.5';
	public $EVORS_version = '2.6.1';
	public $name = 'PDFer';

	public $addon_data = array();
	public $slug, $plugin_slug , $plugin_url , $plugin_path ;
	
	// Instanace
		protected static $_instance = null;
		public static function instance() {
			if ( is_null( self::$_instance ) ) {
				self::$_instance = new self();
			}
			return self::$_instance;
		}

	// Construct
	public function __construct(){
		$this->super_init();
		add_action('plugins_loaded', array($this, 'plugin_init'));
	}

	// Init
		public function plugin_init(){		
			// check if eventon exists with addon class
			if( !isset($GLOBALS['eventon']) || !class_exists('evo_addons') ){
				add_action('admin_notices', array($this, 'notice'));
				return false;			
			}			

			$this->addon = new evo_addons($this->addon_data);

			if($this->addon->evo_version_check()){
				if(!function_exists('EVORS') && !class_exists('evotx')){
					add_action('admin_notices', array($this, '_eventon_warning'));
					return false;			
				}

				// Tickets Addon
				$_tix = true;
				if(function_exists('EVOTX') ){
					if( version_compare(EVOTX()->version , $this->EVOTX_version)>=0){
						$_tix = true;
					}else{
						add_action('admin_notices', array($this, '_version_warning_tx'));
						$_tix = false;
					}
				}

				// RSVP Addon
				$_rsvp = true;
				if(function_exists('EVORS') ){
					if( version_compare(EVORS()->version , $this->EVORS_version) >= 0){
						$_rsvp = true;
					}else{
						add_action('admin_notices', array($this, '_version_warning_rs'));
						$_rsvp = false;
					}
				}

				// if both rsvp and tickets are true
				if($_tix && $_rsvp)
					add_action( 'init', array( $this, 'init' ), 0 );
							
			}		
		}

	// SUPER init
		function super_init(){
			// PLUGIN SLUGS			
			$this->addon_data['plugin_url'] = path_join(WP_PLUGIN_URL, basename(dirname(__FILE__)));
			$this->addon_data['plugin_slug'] = plugin_basename(__FILE__);
			list ($t1, $t2) = explode('/', $this->addon_data['plugin_slug'] );
	        $this->addon_data['slug'] = $t1;
	        $this->addon_data['plugin_path'] = dirname( __FILE__ );
	        $this->addon_data['plugin_base'] = basename(dirname(__FILE__));
	        $this->addon_data['evo_version'] = $this->eventon_version;
	        $this->addon_data['version'] = $this->version;
	        $this->addon_data['name'] = $this->name;

	        $this->plugin_url = $this->addon_data['plugin_url'];
	        $this->assets_path = str_replace(array('http:','https:'), '',$this->addon_data['plugin_url']).'/assets/';	        
	        $this->plugin_slug = $this->addon_data['plugin_slug'];
	        $this->slug = $this->addon_data['slug'];
	        $this->plugin_path = $this->addon_data['plugin_path'];
	        $this->plugin_base = $this->addon_data['plugin_base'];
		}

	// INITIATE 
		function init(){		
						
			// Deactivation
			register_deactivation_hook( __FILE__, array($this,'deactivate'));

			include_once( 'includes/class-ajax.php' );
			include_once( 'includes/class-integration-tickets.php' );


			$this->ticket = new EVOPDF_Ticket();

			if ( is_admin() ){
				$this->addon->updater();
				include_once( 'includes/class-admin.php' );
				$this->admin = new evopdf_admin();	
			}	
			
			// TICKETS filter hooks
			add_filter( 'evotx_beforesend_tix_email_data', array( $this, 'evotx_email'), 10, 2 );	

			// RSVP hook
			add_filter('evors_beforesend_email_data', array($this, 'evors_email'), 10, 1);
		}

	// For Tickets
		function evotx_email($data, $order_id){

			// check if PDF enabled
			if(!EVO()->cal->check_yn('evopdf_tickets','evcal_tx'))  return $data;

			// create and get url to pdf file
			$file = $this->gen_pdf_file('ticket', $order_id);

			if($file){
				$data['attachments'] = array($file);
			}

			return $data;
		}

	// RSVP
		function evors_email($email_data){
			if(!isset($email_data['type'])) return $email_data;
			if($email_data['type'] != 'confirmation') return $email_data;
			if(!isset($email_data['args'])) return $email_data;
			if(!isset($email_data['args']['rsvp_id'])) return $email_data;
			if(!isset($email_data['args']['e_id'])) return $email_data;


			// check if PDF enabled
			if(!EVO()->cal->check_yn('evopdf_rsvp','evcal_rs')) return $email_data;

			// get RSVP ID
			$rsvp_id = $email_data['args']['rsvp_id'];
			$file = $this->gen_pdf_file('rsvp',$rsvp_id);

			if($file){
				$email_data['attachments'] = array($file);
			}

			return $email_data;

		}

	// generate PDF file if enabled
		function gen_pdf_file($type, $pid){
			$file_array = array();

			// if PDF already created and saved
			$media_id = get_post_meta($pid, '_evopdf_media_id', true);
			$pdf_src = !empty($media_id)? get_attached_file($media_id): false;


			if( $pdf_src )	return $pdf_src;


			// Generate and save new PDF file
			require_once('includes/class-pdfer.php');
			$pdf = new EVO_PDF_generator();

			$pdf_content = $pdf->generate_pdf(array(
				'content'=> $this->gen_content( $type, $pid ), 
				'type'=> $type, 
				'post_id'=> $pid, 
				'save_to_post'=> true
			));	
	    	
	    	// get newly created pdf file
	    	$media_id = get_post_meta($pid, '_evopdf_media_id', true);
			$pdf_src = !empty($media_id)? get_attached_file($media_id): false;

			//$src = get_attached_file( $media_id );

			//add_post_meta(16154,'aaa',$pdf_src.' '.$src);
			if( $pdf_src) return $pdf_src;
				    
	      	return false;
		}

		
	// GET the PDF file HTML content
		function gen_content($type='ticket', $pid){
			ob_start();

			// EventON email header HTML Styles
				$wrapper = "background-color: #e6e7e8;-webkit-text-size-adjust:none !important;margin:0;padding: 25px 25px 25px 25px;";
				$innner = "background-color: #ffffff;-webkit-text-size-adjust:none !important;margin:0;border-radius:5px;";

			// TICKET
			if($type == 'ticket'):
				
				//$order = new WC_Order( $orderid);
				
				$evotx_tix = new evotx_tix();
				$order_tickets = $evotx_tix->get_ticket_numbers_for_order($pid);
				
				$email_body_arguments = array(
					'orderid'=>$pid,
					'tickets'=>$order_tickets, 
					'customer'=>'Ashan Jay',
					'email'=>'yes'
				);

				$email = new evotx_email();

				?><div style="<?php echo $wrapper; ?>"><div style="<?php echo $innner;?>"><?php
				echo $email->get_ticket_email_body_only($email_body_arguments);
				?></div></div><?php
				
			// RSVP
			elseif( $type == 'rsvp'): 

				?><div style="<?php echo $wrapper; ?>"><div style="<?php echo $innner;?>">

					<?php

				echo EVORS()->email->_get_email_body(
					array('rsvp_id'=> $pid,	), 
					'confirmation_email'
				);
				
				?></div></div><?php

			endif;

			return ob_get_clean();
		}

	// Secondary
		public function _version_warning_tx(){
			?><div class="message error"><p><?php printf(__('EventON %s require EventON Tickets addon version %s or higher to fully function please update Tickets addon!', 'evorm'), $this->name, $this->EVOTX_version); ?></p></div><?php
		}
		public function _version_warning_rs(){
			?><div class="message error"><p><?php printf(__('EventON %s require EventON RSVP addon version %s or higher to fully function please update RSVP addon!', 'evorm'), $this->name, $this->EVORS_version); ?></p></div><?php
		}
		public function _eventon_warning(){
			?><div class="message error"><p><?php printf(__('EventON %s require either EventON RSVP or EventON Tickets addon to function properly. Please install either of those addons!', 'evorm'), $this->name); ?></p></div><?php
		}	
	    public function notice(){
			?><div class="message error"><p><?php printf(__('EventON %s is NOT active! - ','eventon'), $this->name); 
	        	echo "You do not have EventON main plugin, which is REQUIRED.";?></p></div><?php
		}
		function deactivate(){	$this->addon->remove_addon();	}
	   
}

// Initiate this addon within the plugin
$GLOBALS['evo_pdf'] = new evo_pdf();

function EVOPDF(){
	global $evo_pdf;	return $evo_pdf;
}
?>