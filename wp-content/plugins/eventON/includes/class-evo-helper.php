<?php
/** 
 * Helper functions to be used by eventon or its addons
 * front-end only
 *
 * @version 4.0.7
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class evo_helper{	
	public $options2;
	public function __construct(){
		$this->opt2 = get_option('evcal_options_evcal_2'); 		
	}

	// Process permalink appends
		public function process_link($link,  $var, $append, $force_par = false){
			if(strpos($link, '?')=== false ){

				if($force_par){
					if( substr($link,-1) == '/') $link = substr($link,0,-1);
					$link .= "?".$var."=".$append;
				}else{
					if( substr($link,-1) == '/') $link = substr($link,0,-1);
					$link .= "/".$var."/".$append;
				}
				
			}else{
				$link .= "&".$var."=".$append;
			}
			return $link;
		}

	// Create posts 
		function create_posts($args){
			$DATA_Store = new EVO_Data_Store();
			return $DATA_Store->create_new( $args );
		}

		function create_custom_meta($post_id, $field, $value){
			add_post_meta($post_id, $field, $value);
		}

	// check if post exist for a ID
	// @+ 3.0
		function post_exist($ID, $post_status = 'publish'){
			global $wpdb;

			$post_id = $ID;
			$post_exists = $wpdb->get_row(
				$wpdb->prepare("SELECT ID FROM $wpdb->posts WHERE id = %d AND post_status = %s", $post_id, $post_status)
			, 'ARRAY_A');
			return $post_exists? $post_exists['ID']: false;
		}
			
	// Eventon Settings helper
		function get_html($type, $args){
			switch($type){
				case 'email_preview':
					ob_start();
					echo '<div class="evo_email_preview"><p>Headers: '.$args['headers'][0].'</p>';
					echo '<p>To: '.$args['to'].'</p>';
					echo '<p>Subject: '.$args['subject'].'</p>';
					echo '<div class="evo_email_preview_body">'.$args['message'].'</div></div>';
					return ob_get_clean();
				break;
			}
		}

	// ADMIN & Frontend Helper
		// @updated 2.5.2
		public function send_email($args){
			$defaults = array(
				'html'=>'yes',
				'preview'=>'no',
				'to'=>'',
				'from'=>'',
				'from_name'=>'','from_email'=>'',
				'header'=>'',
				'subject'=>'',
				'message'=>'',
				'type'=>'',// bcc
				'attachments'=> array(),
				'return_details'=>false,
				'reply-to' => ''
			);
			$args = is_array($args)? array_merge($defaults, $args): $defaults;

			if($args['html']=='yes'){
				add_filter( 'wp_mail_content_type',array($this,'set_html_content_type'));
				add_filter( 'wp_mail_charset', array($this,'change_mail_charset') );
			}

			$headers = '';

			if(!empty($args['header'])){
				$headers = $args['header'];
			}else{
				$headers = array();
				if(empty($args['from_email'])){
					$headers[] = 'From: '.$args['from'];
				}else{
					$headers[] = 'From: '.(!empty($args['from_name'])? $args['from_name']:'') .' <'.
						$args['from_email'] . '>';
				}
			}	


			// add reply to into headers // @2.8.6
			if(!empty($args['reply-to']) && isset($args['reply-to'])){

				if( is_array($headers)){
					$headers[] = 'Reply-To: '. $args['reply-to'];
				}else{
					$headers .= 'Reply-To: '. $args['reply-to'];
				}
			}

			if($args['html']=='yes'){
				if( is_array($headers)){
					$headers[] = 'Content-Type: text/html; charset=UTF-8';	
				}else{
					$headers .= 'Content-Type: text/html; charset=UTF-8';	
				}
			}

			$return = '';	

			if($args['preview']=='yes'){
				$return = array(
					'to'=>$args['to'],
					'subject'=>$args['subject'],
					'message'=>$args['message'],
					'headers'=>$headers
				);
			// bcc version of things
			}else if(!empty($args['type']) && $args['type']=='bcc' ){
				if(is_array($args['to']) ){
					foreach($args['to'] as $EM){
						$headers[] = "Bcc: ".$EM;
					}
				}else{
					$headers[] = "Bcc: ".$args['to'];
				}

				$return = wp_mail($args['from'], $args['subject'], $args['message'], $headers, $args['attachments']);	
			}else{
				$return = wp_mail($args['to'], $args['subject'], $args['message'], $headers, $args['attachments']);
			}

			if($args['html']=='yes'){
				remove_filter( 'wp_mail_content_type', array($this,'set_html_content_type') );
			} 

			if($args['return_details']){
				// get the errors
				$ts_mail_errors = array();
				if(!$return){
					global $ts_mail_errors;
					global $phpmailer;

					if (!isset($ts_mail_errors)) $ts_mail_errors = array();

					if (isset($phpmailer)) {
						$ts_mail_errors[] = $phpmailer->ErrorInfo;
					}
				}
				return array('result'=>$return, 'error'=>$ts_mail_errors);
			}else{
				return $return;
			}
			
		}	
		
		// set a custom encoding character type for emails
		function change_mail_charset( $charset ) {

			$encoding = EVO()->cal->get_prop('_evo_email_encode','evcal_1');

			if( !$encoding) return $charset;
			if( $encoding == 'def') return $charset;
			if( $encoding == 'utf8') return 'UTF-8';
			if( $encoding == 'utf16') return 'UTF-16';
		}
	 
		function set_html_content_type() {
			return 'text/html';
		}
		function set_charset_type() {
			return 'utf8';
		}

		// GET email body with eventon header and footer for email included
		public function get_email_body_content($message='', $outside = true){
			
			ob_start();
			if($outside) echo EVO()->get_email_part('header');
			echo !empty($message)? $message:'';
			if($outside) echo EVO()->get_email_part('footer');
			return ob_get_clean();
		}

	// YES NO Button
		function html_yesnobtn($args=''){
			return EVO()->elements->yesno_btn( $args );
		}

	// tool tips
		function tooltips($content, $position='', $handleClass=false, $echo = false){
			return EVO()->elements->tooltips( $content, $position, $echo, $handleClass);
		}
		function echo_tooltips($content, $position=''){
			$this->tooltips($content, $position='',true);
		}

	// date time ICS
		public function get_ics_format_from_unix($unix, $separate = true){
			$enviro = new EVO_Environment();

			$unix = $unix - $enviro->get_UTC_offset();

			if( !$separate) return $unix;


			$new_timeT = date("Ymd", $unix);
			$new_timeZ = date("Hi", $unix);

			return $new_timeT.'T'.$new_timeZ.'00Z';
		}

	// template locator
	// pass: paths array, file name, default template with full path and file
		function template_locator($paths, $file, $template){
			foreach($paths as $path){
				if(file_exists($path.$file) ){	
					$template = $path.$file;
					break;
				}
			}				
			if ( ! $template ) { 
				$template = AJDE_EVCAL_PATH . '/templates/' . $file;
			}

			return $template;
		}	
	// Humanly readable time
	// @+ 2.6.13
		function get_human_time($time){

			$output = '';
			$minFix = $hourFix = $dayFix = 0;

			$day = $time/(60*60*24); // in day
			$dayFix = floor($day);
			$dayPen = $day - $dayFix;
			if($dayPen > 0)
			{
				$hour = $dayPen*(24); // in hour (1 day = 24 hour)
				$hourFix = floor($hour);
				$hourPen = $hour - $hourFix;
				if($hourPen > 0)
				{
					$min = $hourPen*(60); // in hour (1 hour = 60 min)
					$minFix = floor($min);
					$minPen = $min - $minFix;
					if($minPen > 0)
					{
						$sec = $minPen*(60); // in sec (1 min = 60 sec)
						$secFix = floor($sec);
					}
				}
			}
			$str = "";
			if($dayFix > 0)
				$str.= $dayFix . " " . ( $dayFix > 1 ? evo_lang('Days') : evo_lang('Day') );
			if($hourFix > 0)
				$str.= $hourFix . ' '. ( $hourFix > 1 ? evo_lang('Hours') : evo_lang('Hour') );
			if($minFix > 0)
				$str.= $minFix . ' '. ( $minFix > 1 ? evo_lang('Minutes') : evo_lang('Minute') );
			//if($secFix > 0)	$str.= $secFix." sec ";
			return $str;
		}

	// Woocommerce related
		function convert_to_currency($price, $symbol = true){		

			extract( apply_filters( 'wc_price_args', wp_parse_args( array(), array(
		        'ex_tax_label'       => false,
		        'currency'           => '',
		        'decimal_separator'  => wc_get_price_decimal_separator(),
		        'thousand_separator' => wc_get_price_thousand_separator(),
		        'decimals'           => wc_get_price_decimals(),
		        'price_format'       => get_woocommerce_price_format(),
		    ) ) ) );

			$sym = $symbol? html_entity_decode(get_woocommerce_currency_symbol($currency)):'';

			$negative = $price < 0;
			$price = floatval($negative? $price *-1: $price);
			$price = apply_filters( 'formatted_woocommerce_price', number_format( $price, $decimals, $decimal_separator, $thousand_separator ), $price, $decimals, $decimal_separator, $thousand_separator );

			

			if ( apply_filters( 'woocommerce_price_trim_zeros', false ) && $decimals > 0 ) {
		        $price = wc_trim_zeros( $price );
		    }

		    $return = ( $negative ? '-' : '' ) . sprintf( $price_format, $sym, $price );

		    if ( $ex_tax_label && wc_tax_enabled() ) {
		        $return .= ' <small class="woocommerce-Price-taxLabel tax_label">' . WC()->countries->ex_tax_or_vat() . '</small>';
		    }


			return $return;
		}
		function get_price_format_data(){
			return array(
				'currencySymbol'=>get_woocommerce_currency_symbol(),
				'thoSep'=> get_option('woocommerce_price_thousand_sep'),
				'curPos'=> get_option('woocommerce_currency_pos'),
				'decSep'=> get_option('woocommerce_price_decimal_sep'),
				'numDec'=> get_option('woocommerce_price_num_decimals')
			);
		}

	// sanitization
		// @+ 4.0.3
		public function sanitize_array($array){
			return $this->recursive_sanitize_array_fields($array);
		}
		public function recursive_sanitize_array_fields($array){
			if(is_array($array)){
				$new_array = array();
				foreach ( $array as $key => $value ) {
		        	if ( is_array( $value ) ) {
		        		$key = sanitize_title($key);
		            	$new_array[ $key ] = $this->recursive_sanitize_array_fields($value);
		        	}
		        	else {
		            	$new_array[ $key ] = sanitize_text_field( $value );
		        	}
	    		}

	    		return $new_array;
	    	}else{
	    		return sanitize_text_field( $array );	    		
	    	}
		}	

		// check ajax submissions for sanitation and nonce verification
		// @+3.1
		public function process_post($array, $nonce_key='', $nonce_code='', $filter = true){
			$array = $this->sanitize_array( $array);

			if( !empty($nonce_key) && !empty($nonce_code)){

				if( !wp_verify_nonce( $array[ $nonce_key], $nonce_code ) ) return false;
			}

			if($filter)	$array = array_filter( $array );
			return $array;
		}	

	// convert array to data element values - 3.1 / U 4.1
		public function array_to_html_data($array){
			$html = '';
			foreach($array as $k=>$v){
				if( is_array($v)) $v = htmlspecialchars( json_encode($v), ENT_QUOTES);
				$html .= 'data-'. $k .'="'. $v .'" ';
			}
			return $html;
		}

	// Timezones
		private function get_default_timezones_array(){
			return array(
				"Pacific/Midway"                 => "(GMT-11:00) Midway Island, Samoa ",
				"Pacific/Pago_Pago"              => "(GMT-11:00) Pago Pago ",
				"Pacific/Honolulu"               => "(GMT-10:00) Hawaii ",
				"America/Anchorage"              => "(GMT-8:00) Alaska ",
				"America/Vancouver"              => "(GMT-7:00) Vancouver ",
				"America/Los_Angeles"            => "(GMT-7:00) Pacific Time (US and Canada)",
				"America/Tijuana"                => "(GMT-7:00) Tijuana ",
				"America/Phoenix"                => "(GMT-7:00) Arizona ",
				"America/Edmonton"               => "(GMT-6:00) Edmonton ",
				"America/Denver"                 => "(GMT-6:00) Mountain Time (US and Canada)",
				"America/Mazatlan"               => "(GMT-6:00) Mazatlan ",
				"America/Regina"                 => "(GMT-6:00) Saskatchewan ",
				"America/Guatemala"              => "(GMT-6:00) Guatemala ",
				"America/El_Salvador"            => "(GMT-6:00) El Salvador ",
				"America/Managua"                => "(GMT-6:00) Managua ",
				"America/Costa_Rica"             => "(GMT-6:00) Costa Rica ",
				"America/Tegucigalpa"            => "(GMT-6:00) Tegucigalpa ",
				"America/Winnipeg"               => "(GMT-5:00) Winnipeg ",
				"America/Chicago"                => "(GMT-5:00) Central Time (US and Canada)",
				"America/Mexico_City"            => "(GMT-5:00) Mexico City ",
				"America/Panama"                 => "(GMT-5:00) Panama ",
				"America/Bogota"                 => "(GMT-5:00) Bogota ",
				"America/Lima"                   => "(GMT-5:00) Lima ",
				"America/Caracas"                => "(GMT-4:30) Caracas ",
				"America/Montreal"               => "(GMT-4:00) Montreal ",
				"America/New_York"               => "(GMT-4:00) Eastern Time (US and Canada)",
				"America/Indianapolis"           => "(GMT-4:00) Indiana (East)",
				"America/Puerto_Rico"            => "(GMT-4:00) Puerto Rico ",
				"America/Santiago"               => "(GMT-4:00) Santiago ",
				"America/Halifax"                => "(GMT-3:00) Halifax ",
				"America/Montevideo"             => "(GMT-3:00) Montevideo ",
				"America/Araguaina"              => "(GMT-3:00) Brasilia ",
				"America/Argentina/Buenos_Aires" => "(GMT-3:00) Buenos Aires, Georgetown ",
				"America/Sao_Paulo"              => "(GMT-3:00) Sao Paulo ",
				"Canada/Atlantic"                => "(GMT-3:00) Atlantic Time (Canada)",
				"America/St_Johns"               => "(GMT-2:30) Newfoundland and Labrador ",
				"America/Godthab"                => "(GMT-2:00) Greenland ",
				"Atlantic/Cape_Verde"            => "(GMT-1:00) Cape Verde Islands ",
				"Atlantic/Azores"                => "(GMT+0:00) Azores ",
				"UTC"                            => "(GMT+0:00) Universal Time UTC ",
				"Etc/Greenwich"                  => "(GMT+0:00) Greenwich Mean Time ",
				"Atlantic/Reykjavik"             => "(GMT+0:00) Reykjavik ",
				"Africa/Nouakchott"              => "(GMT+0:00) Nouakchott ",
				"Europe/Dublin"                  => "(GMT+1:00) Dublin ",
				"Europe/London"                  => "(GMT+1:00) London ",
				"Europe/Lisbon"                  => "(GMT+1:00) Lisbon ",
				"Africa/Casablanca"              => "(GMT+1:00) Casablanca ",
				"Africa/Bangui"                  => "(GMT+1:00) West Central Africa ",
				"Africa/Algiers"                 => "(GMT+1:00) Algiers ",
				"Africa/Tunis"                   => "(GMT+1:00) Tunis ",
				"Europe/Belgrade"                => "(GMT+2:00) Belgrade, Bratislava, Ljubljana ",
				"CET"                            => "(GMT+2:00) Sarajevo, Skopje, Zagreb ",
				"Europe/Oslo"                    => "(GMT+2:00) Oslo ",
				"Europe/Copenhagen"              => "(GMT+2:00) Copenhagen ",
				"Europe/Brussels"                => "(GMT+2:00) Brussels ",
				"Europe/Berlin"                  => "(GMT+2:00) Amsterdam, Berlin, Rome, Stockholm, Vienna ",
				"Europe/Amsterdam"               => "(GMT+2:00) Amsterdam ",
				"Europe/Rome"                    => "(GMT+2:00) Rome ",
				"Europe/Stockholm"               => "(GMT+2:00) Stockholm ",
				"Europe/Vienna"                  => "(GMT+2:00) Vienna ",
				"Europe/Luxembourg"              => "(GMT+2:00) Luxembourg ",
				"Europe/Paris"                   => "(GMT+2:00) Paris ",
				"Europe/Zurich"                  => "(GMT+2:00) Zurich ",
				"Europe/Madrid"                  => "(GMT+2:00) Madrid ",
				"Africa/Harare"                  => "(GMT+2:00) Harare, Pretoria ",
				"Europe/Warsaw"                  => "(GMT+2:00) Warsaw ",
				"Europe/Prague"                  => "(GMT+2:00) Prague Bratislava ",
				"Europe/Budapest"                => "(GMT+2:00) Budapest ",
				"Africa/Tripoli"                 => "(GMT+2:00) Tripoli ",
				"Africa/Cairo"                   => "(GMT+2:00) Cairo ",
				"Africa/Johannesburg"            => "(GMT+2:00) Johannesburg ",
				"Europe/Helsinki"                => "(GMT+3:00) Helsinki ",
				"Africa/Nairobi"                 => "(GMT+3:00) Nairobi ",
				"Europe/Sofia"                   => "(GMT+3:00) Sofia ",
				"Europe/Istanbul"                => "(GMT+3:00) Istanbul ",
				"Europe/Athens"                  => "(GMT+3:00) Athens ",
				"Europe/Bucharest"               => "(GMT+3:00) Bucharest ",
				"Asia/Nicosia"                   => "(GMT+3:00) Nicosia ",
				"Asia/Beirut"                    => "(GMT+3:00) Beirut ",
				"Asia/Damascus"                  => "(GMT+3:00) Damascus ",
				"Asia/Jerusalem"                 => "(GMT+3:00) Jerusalem ",
				"Asia/Amman"                     => "(GMT+3:00) Amman ",
				"Europe/Moscow"                  => "(GMT+3:00) Moscow ",
				"Asia/Baghdad"                   => "(GMT+3:00) Baghdad ",
				"Asia/Kuwait"                    => "(GMT+3:00) Kuwait ",
				"Asia/Riyadh"                    => "(GMT+3:00) Riyadh ",
				"Asia/Bahrain"                   => "(GMT+3:00) Bahrain ",
				"Asia/Qatar"                     => "(GMT+3:00) Qatar ",
				"Asia/Aden"                      => "(GMT+3:00) Aden ",
				"Africa/Khartoum"                => "(GMT+3:00) Khartoum ",
				"Africa/Djibouti"                => "(GMT+3:00) Djibouti ",
				"Africa/Mogadishu"               => "(GMT+3:00) Mogadishu ",
				"Europe/Kiev"                    => "(GMT+3:00) Kiev ",
				"Asia/Dubai"                     => "(GMT+4:00) Dubai ",
				"Asia/Muscat"                    => "(GMT+4:00) Muscat ",
				"Asia/Tehran"                    => "(GMT+4:30) Tehran ",
				"Asia/Kabul"                     => "(GMT+4:30) Kabul ",
				"Asia/Baku"                      => "(GMT+5:00) Baku, Tbilisi, Yerevan ",
				"Asia/Yekaterinburg"             => "(GMT+5:00) Yekaterinburg ",
				"Asia/Tashkent"                  => "(GMT+5:00) Islamabad, Karachi, Tashkent ",
				"Asia/Calcutta"                  => "(GMT+5:30) India ",
				"Asia/Kolkata"                   => "(GMT+5:30) Mumbai, Kolkata, New Delhi ",
				"Asia/Kathmandu"                 => "(GMT+5:45) Kathmandu ",
				"Asia/Novosibirsk"               => "(GMT+6:00) Novosibirsk ",
				"Asia/Almaty"                    => "(GMT+6:00) Almaty ",
				"Asia/Dacca"                     => "(GMT+6:00) Dacca ",
				"Asia/Dhaka"                     => "(GMT+6:00) Astana, Dhaka ",
				"Asia/Krasnoyarsk"               => "(GMT+7:00) Krasnoyarsk ",
				"Asia/Bangkok"                   => "(GMT+7:00) Bangkok ",
				"Asia/Saigon"                    => "(GMT+7:00) Vietnam ",
				"Asia/Jakarta"                   => "(GMT+7:00) Jakarta ",
				"Asia/Irkutsk"                   => "(GMT+8:00) Irkutsk, Ulaanbaatar ",
				"Asia/Shanghai"                  => "(GMT+8:00) Beijing, Shanghai ",
				"Asia/Hong_Kong"                 => "(GMT+8:00) Hong Kong ",
				"Asia/Taipei"                    => "(GMT+8:00) Taipei ",
				"Asia/Kuala_Lumpur"              => "(GMT+8:00) Kuala Lumpur ",
				"Asia/Singapore"                 => "(GMT+8:00) Singapore ",
				"Australia/Perth"                => "(GMT+8:00) Perth ",
				"Asia/Yakutsk"                   => "(GMT+9:00) Yakutsk ",
				"Asia/Seoul"                     => "(GMT+9:00) Seoul ",
				"Asia/Tokyo"                     => "(GMT+9:00) Osaka, Sapporo, Tokyo ",
				"Australia/Darwin"               => "(GMT+9:30) Darwin ",
				"Australia/Adelaide"             => "(GMT+9:30) Adelaide ",
				"Asia/Vladivostok"               => "(GMT+10:00) Vladivostok ",
				"Pacific/Port_Moresby"           => "(GMT+10:00) Guam, Port Moresby ",
				"Australia/Brisbane"             => "(GMT+10:00) Brisbane ",
				"Australia/Sydney"               => "(GMT+10:00) Canberra, Melbourne, Sydney ",
				"Australia/Hobart"               => "(GMT+10:00) Hobart ",
				"Asia/Magadan"                   => "(GMT+10:00) Magadan ",
				"SST"                            => "(GMT+11:00) Solomon Islands ",
				"Pacific/Noumea"                 => "(GMT+11:00) New Caledonia ",
				"Asia/Kamchatka"                 => "(GMT+12:00) Kamchatka ",
				"Pacific/Fiji"                   => "(GMT+12:00) Fiji Islands, Marshall Islands ",
				"Pacific/Auckland"               => "(GMT+12:00) Auckland, Wellington"
			);
		}
		function get_timezone_array( $unix = false , $adjusted = false) {
			
			$tzs = $this->get_default_timezones_array();

			if(!$adjusted) return $tzs;
		
			// adjust GMT values based on daylight savings time
			$DD = new DateTime();
			if($unix) $DD->setTimestamp($unix);
			$updated_zones = array();
			foreach($tzs as $f=>$v){
				$DD->setTimezone( new DateTimeZone( $f ));
				$nv = explode(') ', $v);
				$updated_zones[ $f] = '(GMT'. $DD->format('P').') '. $nv[1];
				
			}
			return $updated_zones;
		}

		function get_timezone_name($key){
			$data = $this->get_timezone_array();

			if( isset( $data[$key] )) return $data[$key];
			return $key;
		}

		// return time offset in seconds
		function get_timezone_offset($key, $event_unix, $opposite = true, $unix = true){
			$data = $this->get_timezone_array();

			if( !isset( $data[$key] )) return $key;

			$str = explode('GMT', $data[$key]);
			$str2 = explode(')', $str[1]);

			$offset_time = $str2[0]; // 12:00
						

			// return unix value
			if($unix){

				// only return the utc offset
				$timezone = new DateTimeZone( $key );	
					
				$DD = new DateTime( );	
				$DD->setTimestamp( $event_unix );
				$DD->setTimezone( $timezone );	

				$offset = $DD->getOffset() * (-1);
								
				return $offset;

			}else{
				if(!$opposite){
					return $offset_time;
				}else{
					if(strpos($offset_time, '+0:') !== false){
						return $offset_time;
					}

					if(strpos($offset_time, '+') !== false){
						$ss = str_replace('+', '-', $offset_time);
					}else{
						$ss = str_replace('-', '+', $offset_time);	
					}
					
					return $ss;
				}				
			}
		}

		// return GMT value
		function get_timezone_gmt($key, $unix = false){

			$DD = new DateTime();
			if($unix) $DD->setTimestamp($unix);
			$DD->setTimezone( new DateTimeZone( $key ));

			return 'GMT'. $DD->format('P');
		}

}