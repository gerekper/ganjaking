<?php
/**
 * checking in users
 *
 * @author 		AJDE
 * @category 	Admin
 * @package 	eventon-qr/classes
 * @version     1.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
class evoqr_checkin{
	
	public $optQR;
	public $opt2;
	private $checkin_page_url = false;

	public $enable_custom_dir = false;

	public $qr_code_size = 200;

	function __construct(){
		$this->optQR = get_option('evcal_options_evcal_1');
		$this->opt2 = get_option('evcal_options_evcal_2');

		$this->cal = EVO()->cal;
		$this->cal->set_cur('evcal_1');

		$this->checkin_page_url = $this->get_checking_page_url();

		$this->evocal = new EVO_Calendar('evcal_1');

		add_action( 'init', array( $this, 'init' ));	
		add_filter( 'template_include', array( $this, 'template_loader' ) , 99);


		// add QR code to eventon addons
		//rsvp addon
			add_action('eventonrs_rsvp_post_table', array($this, 'show_qr_rsvp_post'), 10, 2);
			add_action('eventonrs_confirmation_email', array($this, 'show_qr_rsvp_email'), 10, 2);
			add_action('evors_confirmation_email_before', array($this, 'generate_qr_image_rsvp'), 10, 1);
		// event tickets addon
			add_filter('evotx_tixPost_tixid', array($this, 'show_qr_code_TX'), 10, 2);
			add_filter('evotx_email_tixid_list', array($this, 'show_qr_code_TX2'), 10, 3);
			add_action('evotx_one_ticket_extra', array($this, 'show_qr_code_TX3'), 10, 2);

		// modify uploads
			add_filter('upload_dir', array($this, 'custom_upload_dir'));
			add_filter( 'wp_unique_filename', array( $this, 'update_filename' ), 10, 3 );
			add_filter('posts_where', array($this,'media_library_hide_qr_images'));
	}

	

	// initiate things
		public function init(){			
			// styles
			wp_register_style('evo_checkin',EVOQR()->assets_path.'checkin_styles.css' );
		}

	// checking page content filtering
		function _print_page_content(){
			global $post;

			$content = $post->post_content;
			$get_shortcode = $this->_get_between($content, '[',']');

			echo apply_filters('the_content', '['.$get_shortcode.']');
		}
		private function _get_between($string, $start, $end){
			$string = ' ' . $string;
		    $ini = strpos($string, $start);
		    if ($ini == 0) return '';
		    $ini += strlen($start);
		    $len = strpos($string, $end, $ini) - $ini;
		    return substr($string, $ini, $len);
		}


	// template loading
		public function template_loader( $template ) {
			global $eventon_qr, $post, $eventon;
			
			$file='';
			
			// Paths to check
			$paths = apply_filters('eventon_template_paths', array(
				0=>TEMPLATEPATH.'/',
				1=>TEMPLATEPATH.'/'.EVO()->template_url.'checkin/', // eg. E:\xampp\htdocs\WP/wp-content/themes/twentythirteen/eventon/checkin/
			));

			
			$checkin_page_id = $this->cal->get_prop('eventon_checkin_page_id');
			//$checkin_page_id = get_option('eventon_checkin_page_id');

			// check if this page is checkin page
			if( !empty($post) && !empty($checkin_page_id) &&  $post->ID == $checkin_page_id ) {
				$file 	= 'checking.php';					
				$paths[] 	= $eventon_qr->addon_data['plugin_path']  . '/templates/';

				// add special class to body				
				add_filter('body_class',array($this,'browser_body_class'));

				// styles for this page
				wp_enqueue_style( 'evo_checkin');
			}
			

			// FILE Exist
			if ( $file ) {				
				// each path
				foreach($paths as $path){				
					if(file_exists($path.$file) ){	
						$template = $path.$file;	
						break;
					}
				}
					
				if ( ! $template ) { 
					$template = $eventon_qr->addon_data['plugin_path'] . '/templates/' . $file;				
				}
			}

			//echo $checkin_page_id;
			//print_r($template);
			
			return $template;
		}

		// add checking page only body class
		function browser_body_class($classes=''){			
			$classes[] = 'evocheckin';
			return $classes;
		}	

	// QR Code 
		public function get_qr_code($ticket_number, $repeat_interval='', $size=150, $post_id = ''){	

			// if there is a post set to assign 
			$gen_qr = true;

			//echo $ticket_number.' '. $this->evo_crypt( $ticket_number );

			if(!empty($post_id)){
				$p_url = get_post_meta($post_id, '_qrimg_'.$ticket_number.'_'.$repeat_interval, true);

				if($p_url){
					$gen_qr = false;
					return '<img src="'.$p_url.'"/>';
				}
			}
			
			if($gen_qr){
				$checkURL = $this->checkin_page_url;

				$row_ticket_number = $ticket_number; // row tn
				$encryptTN = $this->encrypt_TN($ticket_number); // encrypt tn if encrypt enabled

				$siteurl = $checkURL.'?id='.$encryptTN. ( !empty($repeat_interval)? '&ri='.$repeat_interval: null);
				$chl = urlencode($siteurl);
				
				$imgUrl = 'https://chart.googleapis.com/chart?chs='.$size.'x'.$size.'&cht=qr&chl='.$chl;
				//global $eventon_qr;
				//$imgUrl = $eventon_qr->plugin_url. "/includes/barcode.php?codetype=Code39&text={$chl}";

				$u_url = $this->get_uploaded_qr_code_image($imgUrl, $encryptTN, $post_id);

				if($u_url && !empty($post_id)){
					update_post_meta($post_id, '_qrimg_'.$row_ticket_number.'_'.$repeat_interval, $u_url);
					$imgUrl = $u_url;
				}
			}

			return '<img src="'.$imgUrl.'"/>';
		}

		// return the uploaded QR code image url
			function get_uploaded_qr_code_image($qr_url, $ticket_number, $post_id=''){
				if(empty($qr_url)) return false;

				$_POST['type'] = 'eventon_qr_code';
				$this->enable_custom_dir = true;

				$file_array = array();
				$desc = "QR Code Image for post ID:".$post_id." on ". date('Y-m-d', time());
		    				
				// Need to require these files
				if ( !function_exists('media_handle_upload') ) {
					require_once(ABSPATH . "wp-admin" . '/includes/image.php');
					require_once(ABSPATH . "wp-admin" . '/includes/file.php');
					require_once(ABSPATH . "wp-admin" . '/includes/media.php');
				}

				$tmp = download_url( $qr_url );
				
				$file_array['name'] = "qr_code.png";
		      	$file_array['tmp_name'] = $tmp_name = $tmp;

				if( is_wp_error( $tmp ) ){
					@unlink($file_array['tmp_name']);
	        		$file_array['tmp_name'] = '';
				}

				// Set variables for storage
				if(empty($post_id)) $post_id = 1;

		      	// do the validation and storage stuff	      
		      	$result = media_handle_sideload( $file_array, $post_id, $desc );


		      	// If error storing permanently, unlink
			    if ( is_wp_error($result) ) {
			         @unlink($file_array['tmp_name']);
			         return false;
			    }

			    $_POST['type'] = '';
			    $this->enable_custom_dir = false;

			    return  wp_get_attachment_url( $result );

			}

			// alter upload folder only for qr code images
			function custom_upload_dir($pathdata){

				if( !$this->enable_custom_dir) return $pathdata;
				if( !isset($_POST['type']) ) return $pathdata;
				if( $_POST['type'] != 'eventon_qr_code') return $pathdata;

				
				$custom_dir = 'evo_qr_codes';
				$pathdata['path'] = $pathdata['basedir'] . '/'. $custom_dir;
				$pathdata['url'] = $pathdata['url'] . '/'. $custom_dir;
				$pathdata['subdir'] = '/'. $custom_dir;
				

				return $pathdata;
			}

			// change file name for qr code and prepend uniqu chars
			function update_filename($full_filename, $ext, $dir){
				if ( ! isset( $_POST['type'] ) || ! 'eventon_qr_code' === $_POST['type'] ) {
					return $full_filename;
				}

				if ( ! strpos( $dir, 'evo_qr_codes' ) ) return $full_filename;

				return $this->unique_filename( $full_filename, $ext );
			}

			// change file name to append random chars
			public function unique_filename( $full_filename, $ext ) {
				$ideal_random_char_length = 6;   // Not going with a larger length because then downloaded filename will not be pretty.
				$max_filename_length      = 255; // Max file name length for most file systems.
				$length_to_prepend        = min( $ideal_random_char_length, $max_filename_length - strlen( $full_filename ) - 1 );

				if ( 1 > $length_to_prepend ) {
					return $full_filename;
				}

				$suffix   = strtolower( wp_generate_password( $length_to_prepend, false, false ) );
				$filename = $full_filename;

				if ( strlen( $ext ) > 0 ) {
					$filename = substr( $filename, 0, strlen( $filename ) - strlen( $ext ) );
				}

				$full_filename = str_replace(
					$filename,
					"$filename-$suffix",
					$full_filename
				);

				return $full_filename;
			}

			// hide qr code images from media library
			public function media_library_hide_qr_images($where){

				if ( ! is_admin() )    return $where;

				if( EVO()->cal->check_yn('evoqr_show_in_media','evcal_1')) return $where;

				global $wpdb, $pagenow;
				
				$execute = false;

				if( !empty($pagenow) && $pagenow == 'upload.php') $execute = true;
				if( isset($_REQUEST['action']) && $_REQUEST['action'] == 'query-attachments') $execute = true;


				if($execute) 
					$where .= ' AND ' . $wpdb->posts . '.post_title NOT LIKE \'QR Code Image%\'';
			   
			    return $where;
			}
			

	// show QR code for addons
		// RSVP addon
			public function show_qr_rsvp_post($rsvpid, $rsvp_pmv){
				$repeat_interval = (!empty($rsvp_pmv['repeat_interval']))? $rsvp_pmv['repeat_interval'][0]:0;
				echo "<tr><td>QR Code: </td><td>
					<em class='evoqr_qr' style='float:left; padding-right:10px'>".
						$this->get_qr_code($rsvpid, $repeat_interval, $this->qr_code_size, $rsvpid)."</em>
					<em class='evoqr_code' style='padding-top:25px; display:inline-block'># ".$rsvpid."</em>
					</td></tr>";
			}
			// generate qr code image for rsvp before sending email
			function generate_qr_image_rsvp($rsvp_id){

				$rsvp_pmv = get_post_custom($rsvp_id);
				
				$repeat_interval = (!empty($rsvp_pmv['repeat_interval']))? $rsvp_pmv['repeat_interval'][0]:0;							
				// if RSVP status is no then stop
				if(!empty($rsvp_pmv['rsvp']) && $rsvp_pmv['rsvp'][0] == 'n') return false;
				
				// Generate QR Code image for RSVP
				$this->get_qr_code($rsvp_id, $repeat_interval, $this->qr_code_size, $rsvp_id);
			}

			public function show_qr_rsvp_email($RSVP, $eRSVP){
				$repeat_interval = $RSVP->repeat_interval();							
				
				// if RSVP status is no then stop
				if( $RSVP->get_prop('rsvp') == 'y'){
				
				?>
					<p style="color:#303030; text-transform:uppercase; font-size:18px; font-style:italic; padding-bottom:0px; margin-bottom:0px; line-height:110%;">QR Code</p>
					<p style="color:#afafaf; font-style:italic;font-size:14px; margin:0 0 10px 0; padding-bottom:10px;"><?php echo eventon_get_custom_language($this->opt2, 'evoQR_008', 'You can use the below QRcode to checkin at the event');?></p>
					<p><?php echo $this->get_qr_code($RSVP->ID, $repeat_interval, $this->qr_code_size, $RSVP->ID);?></p>
					<?php
				}
			}
		//Ticket addon
			public function show_qr_code_TX($ticket_number, $TD){
				return $ticket_number;					
			}
			// for emails and order details page
			public function show_qr_code_TX2($encrypt_TN, $ticket_number, $this_ticket){

				if( isset($this_ticket['s']) && $this_ticket['s'] == 'refunded') 
					return $this->encrypt_TN( $ticket_number );

				$evo_tix_id = explode('-', $ticket_number);
				$evo_tix_id = (int)$evo_tix_id[0];

				// get the qr code image size
				$qr_code_size = apply_filters('evotx_qrcode_email_size', $this->qr_code_size);

				return "<em style=''>".$this->get_qr_code($ticket_number,'',$qr_code_size, $evo_tix_id)."</em><em style='display:block; line-height:100%; padding-top:10px; padding-bottom:5px; font-style:normal;font-size:14px'>". $this->encrypt_TN($ticket_number) ."</em>";
			}
			// on one ticket
			function show_qr_code_TX3($TN, $TD){

				if( isset($TD['oS']) && $TD['oS'] != 'completed') return false;
				if( isset($TD['s']) && $TD['s'] == 'refunded') return false;

				$evo_tix_id = explode('-', $TN);
				$evo_tix_id = (int)$evo_tix_id[0];

				// get the qr code image size
				$qr_code_size = apply_filters('evotx_qrcode_size', $this->qr_code_size);

				echo "<em class='evotxVA_qrcode'>".$this->get_qr_code($TN,'',$qr_code_size, $evo_tix_id)."</em>";

			}
	// actual checking page data
		function checkin_page_content($atts=''){

			// if language value passed set it as global language for eventon
				if(isset($atts['lang']))	evo_set_global_lang($atts['lang']);
			
			$checking_page = $this->checkin_page_url;

			// process ticket number
			$tixid = $this->process_ticket_number();

			// login check
			if(!is_user_logged_in()){
				$this->_views('notloggedin');	
				return false;
			}

			// permission check
			if(!$this->is_user_have_permission_to_checkin()){
				$this->_views('nopermissions');	
				return false;
			}

			// check for ticket ID 
			if(!$tixid){
				$this->_views('noticket_id');	
				return false;
			}

			// validate ticket numbers
			if( !$this->validate_tickets( $tixid )){
				$this->_views('invalid_ticket_id');	
				return false;
			}


			// if loggedin and have permission
			$content = $this->get_page_data();
			if($content) extract($content);


			?>	
			<div class='evo_checkin_page <?php echo !empty($classes) ? $classes :'';?>'>
			
				<?php if(!empty($tixid)):?>
					<h2 class='tix_id'><span style='display:inline-block;opacity:0.5'><?php echo evo_lang('Ticket #');?></span><span><?php echo $tixid;?></span>
					</h2>
					<?php 

					// input field for scanner gun
					if($this->evocal->get_prop('evoqr_mode')=='gun'):?>
					<?php $this->_views('scanner_gun_js');?>
					<div class='evpqr_content' style='padding:0 0 20px'>
						<form action='<?php echo $checking_page;?>' method='GET'><p><input placeholder='<?php evo_lang_e('Type another Ticket');?> #' type='text' name='id' class='another_id'/><button class='evcal_btn' type='submit' data-url='<?php echo $checking_page;?>'><?php evo_lang_e('Submit');?></button></p></form>
					</div>
					<?php endif;?>
				<?php endif;?>

				<p class='sign'><i></i></p>
				<h4><?php echo !empty($msg)?$msg:'';?></h4>

				<?php
					// other ticket information
					if(!empty($otherdata)):

						echo "<h5>".eventon_get_custom_language($this->opt2, 'evoQR_007a', 'Other Ticket Information')."</h5>";
						echo "<ul>";
						foreach($otherdata as $field=>$value){
							if(empty($value)) continue;

							$label = eventon_get_custom_language($this->opt2, 'evoQR_007_'.$field, str_replace('-',' ', $field));

							// convert order id to a button
							if( $field == 'order-id'){
								$value = '<a class="evcal_btn" href="'.get_edit_post_link($value).'" target="_blank">'.$value.'</a>';
							}
							echo "<li><span>".$label.':</span>'.$value.'</li>';
						}
						echo "</ul>";

					endif;

					// after
					echo !empty($after)? $after: '';

					// other HTML content
					if(!empty($html)):
						echo "<div class='evpqr_content'>";
						echo $html;
						echo "</div>";
					endif;
				?>
			</div>
			<?php
		}

	// validate the ticket number for both tix and rsvp @+ 1.1.7
		public function validate_tickets( $tixid ){

			if( empty($tixid) ) return false;

			// differentiate ID type
			if(strpos($tixid, '-')){
				$tt = explode('-', $tixid);
				$post_exists = (get_post_status($tt[0] ) !== FALSE)? true: false;
				$id_type = get_post_type($tt[0]);
			}else{
				$post_exists = (get_post_status($tixid ) !== FALSE)? true: false;
				$id_type = get_post_type($tixid	);
			}

			if( !$post_exists ) return false;			
				
			// for tickets
			if($id_type=='evo-tix'){

				$ET =  new evotx_tix();
				$evotix_id = $ET->get_evotix_id_by_ticketnumber($tixid);
				

				$TIX_CPT = new EVO_Evo_Tix_CPT( $evotix_id );
				$saved_tn = $TIX_CPT->get_ticket_number();


				if($saved_tn != $tixid) return false;

			// for rsvp
			}else{					
				// post exists value checks for this
			}

			return true;

		}

	// PAGE DATA
		function get_page_data(){
			// process ticket number
			$ticket_number = $tixid = $this->process_ticket_number();
			
			$post_exists = false;
			$checking_page = $this->checkin_page_url;
			
			$ticket_meta_data = '';
			$classes = array();
			$output = array(
				'classes'=>'',
				'otherdata'=>'',
				'after'=>'',
				'msg'=>'',
				'tixid'=>$ticket_number
			);

			// differentiate ID type
				if(strpos($ticket_number, '-')){
					$tt = explode('-', $ticket_number);
					$post_exists = (get_post_status($tt[0] ) !== FALSE)? true: false;
					$id_type = get_post_type($tt[0]);
				}else{
					$post_exists = (get_post_status($ticket_number ) !== FALSE)? true: false;
					$id_type = get_post_type($ticket_number	);
				}
			

			// if a valid ticket id
			if(!empty($ticket_number) && $post_exists && ($id_type=='evo-rsvp' || $id_type=='evo-tix' )){

				// Tickets
				if($id_type=='evo-tix'){


					// pre process
					$ET = new evotx_tix();
					$ticket_post_id = $ET->evo_tix_id = $ET->get_evotix_id_by_ticketnumber( $ticket_number );

					$TIX = new EVO_Evo_Tix_CPT( $ticket_post_id );

					if( !$TIX->get_order_id() ) return false;

					
					$OrderStatus = false;

					$current_status = $TIX->get_status();


					// uncheck ticket
					if(!empty($_GET['action']) && $_GET['action']=='unc'){
						// change status to check-in
						if(!empty($current_status) && $current_status =='checked'){
							
							$TIX->set_status( 'check-in');

							$classes[] = 'yes';
							$output['msg'] = eventon_get_custom_language($this->opt2, 'evoQR_001', 'Successfully un-checked ticket!');
							//echo '01';
						}else{
							$classes[] = 'yes'; 
							$output['msg'] = eventon_get_custom_language($this->opt2, 'evoQR_002', 'Ticket already un-checked!');
							//echo '02';
						}
					}else{


						$order_id = $TIX->get_order_id();				
						$event_id = $TIX->get_event_id();

						$order = new WC_Order($order_id);
						$OrderStatus = $order->get_status();


						if( $OrderStatus == 'completed'){
							//check in a ticket 					
							if(empty($current_status) || $current_status =='check-in'){
								
								$TIX->set_status('checked');

								$classes[] = 'yes';
								$output['msg'] = eventon_get_custom_language($this->opt2, 'evoQR_003', 'Successfully Checked!');	

								$output['after'] = "<p class='mart20'><a class='btn' href='?id={$tixid}&action=unc'>".eventon_get_custom_language($this->opt2, 'evoQR_005', 'Un-check this ticket')."</a> <a class='btn' href='".$checking_page ."'>".evo_lang('Enter a New Ticket ID')."</a></p>";										

							// refunded order
							}elseif($current_status == 'refunded'){
								$classes[] = 'refunded'; 										
								$output['msg'] = evo_lang( 'Ticket has been refunded!');
								$output['after'] = "";
								
							}else{// already checkedin status = not-empty|checked
								$classes[] = 'yes'; 
								$classes[] = 'already_checked'; 
								
								$output['msg'] = eventon_get_custom_language($this->opt2, 'evoQR_004', 'Already checked!');
								$output['after'] = "<p class='mart20'><a class='btn' href='?id={$tixid}&action=unc'>".eventon_get_custom_language($this->opt2, 'evoQR_005', 'Un-check this ticket')."</a> <a class='btn' href='".$checking_page ."'>".evo_lang('Enter a New Ticket ID')."</a></p>";	
							}	
						}else{// order is not complete
							$classes[] = $OrderStatus; 
							$classes[] = 'no';
							switch($OrderStatus){
								case 'refunded': $_m = evo_lang('Ticket order is refunded!'); break;
								case 'cancelled': $_m = evo_lang('Ticket order is cancelled!'); break;
								default: $_m = evo_lang('Ticket order is not completed!'); break;
							}										
							$output['msg'] = $_m;
							$output['after'] = "";
						}				
					}

					// Show attendee and event information
						$tix_meta = $TIX->get_props();
						$output['otherdata'] = $this->get_other_event_data($tix_meta, 'tx');
						if($OrderStatus) $output['otherdata']['order-status'] = $OrderStatus;

					// other tickets in the same order
						$EA = new EVOTX_Attendees();
						$TH = $EA->_get_tickets_for_order($order_id);


						// if there are more than one other tickets
						if($TH && isset($TH[$event_id])){

							if( isset($TH[$event_id][$ticket_number])) 
								$ticket_meta_data = $TH[$event_id][$ticket_number];
							
							if(  count($TH[$event_id])>1 ){
								$html = "<h5>".evo_lang('tickets in the same order')."</h5>";
								foreach($TH[$event_id] as $_ticket_number=>$td){
									
									if($_ticket_number != $ticket_number){
										$html .= "<p>".$_ticket_number." <a style='margin-left:8px;' class='evcal_btn evoqr_other_tickets {$td['s']}' href='". $checking_page. "?id={$_ticket_number}'>".$td['s'].'</a></p>';
									}
								}
								
								$output['html'] = $html;
							}
						}

				}else{ // RSVP 

					$RSVP = new EVO_RSVP_CPT($tixid);
					//$rsvpMETA = $RSVP->pmv;

					$checkin_status = $RSVP->status();
					
					// uncheck a checked ticket
					if(!empty($_GET['action']) && $_GET['action']=='unc'){

						// change status to check-in
						if( $checkin_status =='checked'){
							update_post_meta($tixid, 'status','check-in');
							$classes[] = 'yes';
							$output['msg'] = eventon_get_custom_language($this->opt2, 'evoQR_001', 'Successfully un-checked ticket!');
						}else{
							$classes[] = 'yes';
							$output['msg'] = eventon_get_custom_language($this->opt2, 'evoQR_002', 'Ticket already un-checked!');
						}
					}else{ // check in a RSVP guest
						
						if(!$checkin_status || $checkin_status =='check-in'){
							// check whether coming to the event
							$rsvp_status = $RSVP->get_rsvp_status();
							if($rsvp_status !='n'){
								update_post_meta($tixid, 'status','checked');
								$classes[] = 'yes';
								$output['msg'] = eventon_get_custom_language($this->opt2, 'evoQR_003', 'Successfully Checked!');
							}else{// not coming to the event
								$classes[] = 'no';
								$output['msg'] = eventon_get_custom_language($this->opt2, 'evoQR_003x', 'You have RSVPed NO!');
							}
							
						}else{// already checkedin status = not-empty|checked
							$classes[] = 'yes'; 
							$classes[] = 'already_checked'; 

							$output['msg'] = eventon_get_custom_language($this->opt2, 'evoQR_004', 'Already checked!');
							$output['after'] = "<p class='mart20'><a class='btn' href='?id={$tixid}&action=unc'>".eventon_get_custom_language($this->opt2, 'evoQR_005', 'Un-check this ticket')."</a> <a class='btn' href='". $checking_page. "'>".evo_lang('Enter a New Ticket ID')."</a></p>";					
						}							
					}

					// Show attendee and event information
						$output['otherdata'] = $this->get_other_event_data($RSVP, 'rsvp');
				}

			}else{ // invalid ticket ID
				
				$classes[] = 'no';
				$output['msg'] = evo_lang('Invalid Ticket ID');
				
			}
			
			// process output
			$classes_str = (sizeof($classes)>0)? implode(' ', $classes):'';
			$output['classes'] = $classes_str;
			
			return apply_filters('evoqr_data_output', $output, $ticket_number, $id_type, $ticket_meta_data);
		}

	// Supporting functions
		function _views($type){
		
			switch($type){
				case 'notloggedin':
					$evo_login_link = $this->evocal->get_prop('evo_login_link');
					$checking_page = $this->get_checking_page_url();
					$tixid = (!empty($_GET['id'])? $_GET['id']: null);

					$classes[] = 'no';
					$login_url = $evo_login_link? $evo_login_link: wp_login_url($checking_page . (!empty($tixid)? '?id='.$tixid:'') );

					$msg = evo_lang('Login required to checkin guests, please login');
					$msg .= sprintf("<p><a href='%s' class='evcal_btn'>%s</a></p>", $login_url, evo_lang('Login Now') );
					?>
					<div class='evo_checkin_page no'>
						<p class='sign'><i></i></p>
						<h4><?php echo $msg;?></h4>
					</div>
					<?php
				break;
				case 'nopermissions':
					?><div class='evo_checkin_page no'>
					<p class='sign'><i></i></p>
					<h4><?php echo evo_lang_get('evoQR_007', 'You do not have permission!','',$this->opt2);?></h4>
					</div>
					<?php
				break;
				case 'noticket_id':
					
					$checking_page = $this->get_checking_page_url();

					?><div class='evo_checkin_page no'>
					<p class='sign'><i></i></p>
					<h4><?php echo evo_lang('Type in Ticket ID');?></h4>
					<div class='evpqr_content'>
						<?php $this->_views('scanner_gun_js');?>
						<form action='<?php echo $this->checkin_page_url;?>' method='GET'>
						<p>
						<input class='another_id' type='text' name='id'/>
						<button class='evcal_btn' type='submit' data-url='<?php echo $checking_page;?>'><?php echo evo_lang('Submit');?></button>
						</p></form>
					</div>
					</div>
					<?php
				break;
				case 'invalid_ticket_id':
					?><div class='evo_checkin_page no'>
					<p class='sign'><i></i></p>
					<h4><?php echo evo_lang('This is an invalid ticket ID !');?></h4>
					</div>
					<?php
				break;
				case 'scanner_gun_js':
					if($this->evocal->get_prop('evoqr_mode')=='gun'):?>
						<script type="text/javascript">
							jQuery(document).ready(function($){
								INPUT = $('body').find('input.another_id');
								INPUT.focus();
								INPUT.on('keyup',function(){
									if( e.keyCode == 13 && INPUT.val() != ''){
										INPUT.siblings('button').trigger('click');
									}
								});
							});
						</script>
					<?php endif;
				break;
			}
		}
			
		// process tn ecrypt or not
		function encrypt_TN($TN){
			$dis_encrypt = EVO()->cal->check_yn('evoqr_encrypt_dis','evcal_1');

			// if said to disable encryption
			if($dis_encrypt) return $TN;

			return base64_encode($TN);
		}

		// process ticket number if encoded
		function process_ticket_number(){

			$tn = (!empty($_GET['id'])? $_GET['id']: false);
			if(!$tn) return false;

			// remove # sign
			$tn = str_replace('#', '', $tn);

			// if a full url passed as ticket number
			if(strpos($tn, 'http')!== false){
				$tn_1 = explode('http', $tn);
				$tn_2 = explode('?id=', $tn_1[1]);
				$tn = $tn_2[1];
			}elseif( is_numeric($tn)){
				return $tn;
			}


			return $this->decrypt_ticket_number( $tn );

		}		
			// decrypt a ticket number if encrypted 
			// @version 2.0
			public function decrypt_ticket_number( $ticket_number ){

				if( $this->_is_base64encoded( $ticket_number)){
					return base64_decode($ticket_number);
				}

				return $ticket_number;
				
			}
			function _is_base64encoded($data){
				if (preg_match('%^[a-zA-Z0-9/+]*={0,2}$%', $data)) {
			       return TRUE;
			    } else {
			       return FALSE;
			    }
			}
		// custom decrypt and encrypt function
			function evo_crypt($code, $action = 'e'){
				$secret_key = 'evotx_secret_key';
			    $secret_iv = 'evotx_secret_iv';
			 
			    $output = false;
			    $encrypt_method = "AES-256-CBC";
			    $key = hash( 'sha256', $secret_key );
			    $iv = substr( hash( 'sha256', $secret_iv ), 0, 16 );
			 
			    if( $action == 'e' ) {
			        $output = base64_encode( openssl_encrypt( $code, $encrypt_method, $key, 0, $iv ) );
			    }
			    else if( $action == 'd' ){
			        $output = openssl_decrypt( base64_decode( $code ), $encrypt_method, $key, 0, $iv );
			    }
			 
			    return $output;
			}

		// check if user has permissions
			function is_user_have_permission_to_checkin(){
				$_permission_grants = false;

				if(is_user_logged_in()){
					global $current_user, $wp_roles;
					$allowed_roles = array('administrator');

					// add other user roles set via settings for checking in guests
					if(!empty($this->optQR['evoqr_001'])){
						$allowed_roles = array_merge($allowed_roles, $this->optQR['evoqr_001']);
					}
					
					//print_r($allowed_roles);
					foreach($allowed_roles as $role){
						if(array_key_exists($role, $current_user->caps)){
							$_permission_grants = true;
						}
					}
				}
				return $_permission_grants;
			}

		// Checking page
			function get_checking_page_url(){

				$page_id = $this->cal->get_prop('eventon_checkin_page_id');

				return $page_id ? get_permalink($page_id):	get_bloginfo('url').'/checkin/';
			}

		// get other event data
			function get_other_event_data($arr, $type){
				$output = array();

				$event_id = $terms_str = '';
				$event_ri = 0;

				// TICKETS
				if($type == 'tx'){
					if(!empty($arr['_eventid'])) $event_id = $arr['_eventid'][0];	
					if(!empty($arr['name']) ) $output['name'] = $arr['name'][0];	
					$output['count'] = (!empty($arr['qty']) )?  $arr['qty'][0] : 1;	

					// order id
						if(isset($arr['_orderid'])) $output['order-id'] = $arr['_orderid'][0];				
				
				// RSVP
				}else{

					$event_id = $arr->event_id();
					if(!$event_id) $event_id = null;

					$output['name'] = ($arr->first_name()? $arr->first_name().' ':'').($arr->last_name()? $arr->last_name():'');
					$output['count'] = ($arr->count())?  $arr->count() : 1;	

					$event_ri = $arr->repeat_interval();
				}


				if(!empty($event_id)){

					$EVENT = new EVO_Event($event_id,'',$event_ri);

					// event name
					$output['event-name'] = $EVENT->get_title();
			

					// event time
					$output['event-time'] = $EVENT->get_formatted_smart_time();

					// event type terms
					$terms = wp_get_post_terms($event_id,'event_type');
					$term_vals = array();
					if($terms){
						foreach($terms as $term){
							$term_vals[] = $term->name;
						}
						$terms_str = implode(', ', $term_vals);
						$output['event-type'] = $terms_str;
					}
				}
				

				return apply_filters('evoqr_checkin_otherdata_ar', $output, $arr, $type);
			}
}