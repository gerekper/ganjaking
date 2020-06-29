<?php 
	if ( ! defined( 'ABSPATH' ) ) { exit; }
/*----------------------gradient color function--------------------------*/
if(!function_exists('pt_theplus_gradient_color')){
	function pt_theplus_gradient_color($overlay_color1,$overlay_color2,$overlay_gradient) {
$gradient_style='';
if($overlay_gradient=='horizontal'){
	   $gradient_style ='background: -moz-linear-gradient(left, '.$overlay_color1.' 0%, '.$overlay_color2.' 100%);background: -webkit-gradient(linear, left top, right top, color-stop(0%,'.$overlay_color1.'), color-stop(100%,'.$overlay_color2.'));background: -webkit-linear-gradient(left, '.$overlay_color1.' 0%,'.$overlay_color2.' 100%);background: -o-linear-gradient(left, '.$overlay_color1.' 0%,'.$overlay_color2.' 100%);background: -ms-linear-gradient(left, '.$overlay_color1.' 0%,'.$overlay_color2.' 100%);background: linear-gradient(to right, '.$overlay_color1.' 0%,'.$overlay_color2.' 100%);';
}elseif($overlay_gradient=='vertical'){
 $gradient_style ='background: -moz-linear-gradient(top, '.$overlay_color1.' 0%, '.$overlay_color2.' 100%);background: -webkit-gradient(linear, left top, left bottom, color-stop(0%,'.$overlay_color1.'), color-stop(100%,'.$overlay_color2.'));background: -webkit-linear-gradient(top, '.$overlay_color1.' 0%,'.$overlay_color2.' 100%);background: -o-linear-gradient(top, '.$overlay_color1.' 0%,'.$overlay_color2.' 100%);background: -ms-linear-gradient(top, '.$overlay_color1.' 0%,'.$overlay_color2.' 100%);background: linear-gradient(to bottom, '.$overlay_color1.' 0%,'.$overlay_color2.' 100%);';
}elseif($overlay_gradient=='diagonal'){
$gradient_style ='background: -moz-linear-gradient(45deg, '.$overlay_color1.' 0%, '.$overlay_color2.' 100%);background: -webkit-gradient(linear, left bottom, right top, color-stop(0%,'.$overlay_color1.'), color-stop(100%,'.$overlay_color2.'));background: -webkit-linear-gradient(45deg, '.$overlay_color1.' 0%,'.$overlay_color2.' 100%);background: -o-linear-gradient(45deg, '.$overlay_color1.' 0%,'.$overlay_color2.' 100%);background: -ms-linear-gradient(45deg, '.$overlay_color1.' 0%,'.$overlay_color2.' 100%);background: linear-gradient(45deg, '.$overlay_color1.' 0%,'.$overlay_color2.' 100%);';
}elseif($overlay_gradient=='radial'){
 $gradient_style ='background: -moz-radial-gradient(center, ellipse cover, '.$overlay_color1.' 0%, '.$overlay_color2.' 100%);background: -webkit-gradient(radial, center center, 0px, center center, 100%, color-stop(0%,'.$overlay_color1.'), color-stop(100%,'.$overlay_color2.'));background: -webkit-radial-gradient(center, ellipse cover, '.$overlay_color1.' 0%,'.$overlay_color2.' 100%);background: -o-radial-gradient(center, ellipse cover, '.$overlay_color1.' 0%,'.$overlay_color2.' 100%);background: -ms-radial-gradient(center, ellipse cover, '.$overlay_color1.' 0%,'.$overlay_color2.' 100%);background: radial-gradient(ellipse at center, '.$overlay_color1.' 0%,'.$overlay_color2.' 100%);';
}
	   return $gradient_style; 
	}
}
/*----------------------gradient color function--------------------------*/
if(!function_exists('pt_plus_get_api_check')){
	function pt_plus_get_api_check() {		
		$home_url=get_home_url();
		$generate_key=plus_simple_crypt( $home_url, 'ey' );
		 $purchase_option=get_option( 'theplus_purchase_code' );
		if(isset($purchase_option['tp_api_key']) && !empty($purchase_option['tp_api_key'])){
			return pt_plus_api_check($purchase_option['tp_api_key'],$generate_key);
		}else{
			return false;
		}
	}
}
if(!function_exists('pt_plus_api_check')){
	function pt_plus_api_check($tp_api_key='',$generate_key='') {
		if(isset($tp_api_key) && !empty($tp_api_key) && !empty($generate_key)){
			$get_url='http://theplusaddons.com/theplus-verify/';
			$method='verify';
			$get_json='';
			if( ini_get('allow_url_fopen') ) {
			$get_json=file_get_contents($get_url.'?url=verify_api/'.$method.'/'.$tp_api_key.'/'.$generate_key);
			} else {
				die('<div style="margin-bottom:40px;position: relative;display: inline-block;width: 100%;"><div style="margin-top: 10px;margin-left: 30px;margin-right: 30px;color: #a94442;background-color: #f2dede;border-color: #ebccd1;padding: 15px;border: 1px solid transparent;border-radius: 4px;"><strong>Error :</strong> allow_url_fopen must be on in your PHP.ini to work this verification.</div></div>');
				return false;
			}
			$json_decode=json_decode($get_json, true);			
			$option_name = 'plus_key';			
			if(!empty($json_decode) && $json_decode['verified-puchased']['verify_key']=='1' && $json_decode['verified-puchased']['message']=='Verified Purchased'){
				$value = '1' ;
				if ( get_option( $option_name ) !== false ) {
					update_option( $option_name, $value );
				} else {
					$deprecated = null;
					$autoload = 'no';
					add_option( $option_name,'0', $deprecated, $autoload );
				}
				return true;
			}else{
				$value = '0' ;
				if ( get_option( $option_name ) !== false ) {
					update_option( $option_name, $value );
				} else {
					$deprecated = null;
					$autoload = 'no';
					add_option( $option_name,'0', $deprecated, $autoload );
				}
				return false;
				
			}
		}else{
			return false;
		}
	}
}

if(!function_exists('plus_simple_crypt')){
	function plus_simple_crypt( $string, $action = 'dy' ) {
	    $secret_key = 'PO$_key';
	    $secret_iv = 'PO$_iv';
	    $output = false;
	    $encrypt_method = "AES-128-CBC";
	    $key = hash( 'sha256', $secret_key );
	    $iv = substr( hash( 'sha256', $secret_iv ), 0, 16 );
	 
	    if( $action == 'ey' ) {
	        $output = base64_encode( openssl_encrypt( $string, $encrypt_method, $key, 0, $iv ) );
	    }
	    else if( $action == 'dy' ){
	        $output = openssl_decrypt( base64_decode( $string ), $encrypt_method, $key, 0, $iv );
	    }
	 
	    return $output;
	}
}
if(!function_exists('pt_plus_message_display')){
	function pt_plus_message_display() {
		$check=pt_plus_get_api_check();
		if($check==1){
			echo '<div style="margin-bottom:40px;position: relative;display: inline-block;width: 100%;"><div  style="margin-top: 10px;margin-left: 30px;margin-right: 30px;color: #3c763d;background-color: #dff0d8;border-color: #d6e9c6;padding: 15px;border: 1px solid transparent;border-radius: 4px;"><strong>Wow...</strong> You are verified Now. Enjoy fully featured ThePlus Addon Now.</div></div>';
		}else{
			echo '<div style="margin-bottom:40px;position: relative;display: inline-block;width: 100%;"><div style="margin-top: 10px;margin-left: 30px;margin-right: 30px;color: #a94442;background-color: #f2dede;border-color: #ebccd1;padding: 15px;border: 1px solid transparent;border-radius: 4px;"><strong>Psss...</strong> Verification Failed. Please Enter Again or Regenerate key.</div></div>';
		}
	}
}

