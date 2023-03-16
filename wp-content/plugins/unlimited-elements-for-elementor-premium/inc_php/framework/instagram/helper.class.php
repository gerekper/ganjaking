<?php
/**
 * @package Unlimited Elements
 * @author unlimited-elements.com
 * @copyright (C) 2021 Unlimited Elements, All Rights Reserved. 
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * */
defined('UNLIMITED_ELEMENTS_INC') or die('Restricted access');


class HelperInstaUC{

	const RENEW_DELAY_SIX_HOURS = 21600;
	const KEY_RENEW_BLOCKED = "unlimited_elements_instagram_renew_blocked";
	private static $instaCheckRunOnce = false;
	
	
	/**
	 * get instagram access data
	 */
	public static function getInstagramSavedAccessData(){
		
		$settings = HelperProviderCoreUC_EL::getGeneralSettingsValues();
		
		$arrData = array();
		$arrData["access_token"] = UniteFunctionsUC::getVal($settings, "instagram_access_token");
		$arrData["user_id"] = UniteFunctionsUC::getVal($settings, "instagram_user_id");
		$arrData["username"] = UniteFunctionsUC::getVal($settings, "instagram_username");
		$arrData["expires"] = UniteFunctionsUC::getVal($settings, "instagram_expires");
		
		return($arrData);
	}
	
	/**
	 * return if access token exists in settings
	 */
	public static function isAccessTokenExists(){
		
		$arrData = self::getInstagramSavedAccessData();
		
		$token = UniteFunctionsUC::getVal($arrData, "access_token");
		
		$isExists = !empty($token);
		
		return($isExists);
	}
	
	
	/**
	 * redirect to general settings
	 */
	public static function redirectToGeneralSettings(){
		
		$urlRedirect = HelperUC::getViewUrl(GlobalsUnlimitedElements::VIEW_SETTINGS_ELEMENTOR,"#tab=instagram");
		UniteFunctionsUC::redirectToUrl($urlRedirect);
		
		exit();
	}
	
	/**
	 * save connect data, from ajax function
	 * redirect to general settings
	 */
	public static function saveInstagramConnectDataAjax($data, $noUser = false, $redirect = true){
		
		$accessToken = UniteFunctionsUC::getVal($data, "access_token");
		$userID = UniteFunctionsUC::getVal($data, "user_id");
		$username = UniteFunctionsUC::getVal($data, "username");
		$expiresIn = UniteFunctionsUC::getVal($data, "expires");
		
		UniteFunctionsUC::validateNumeric($expiresIn, "expires in ");
		
		$expiresAt = time()+$expiresIn;
		
		$expireDate = UniteFunctionsUC::timestamp2Date($expiresAt);
		
		UniteFunctionsUC::validateNotEmpty($accessToken,"instagram access token");
		
		if($noUser == false){
			UniteFunctionsUC::validateNotEmpty($userID,"instagram user id");
			UniteFunctionsUC::validateNotEmpty($userID,"instagram username");
		}
		
		$arrUpdate = array();
		$arrUpdate["instagram_access_token"] = $accessToken;
		$arrUpdate["instagram_expires"] = $expiresAt;

		if($noUser == false){
			$arrUpdate["instagram_user_id"] = $userID;
			$arrUpdate["instagram_username"] = $username;			
		}
		
		HelperUC::$operations->updateUnlimitedElementsGeneralSettings($arrUpdate);
		
		if($redirect == true)
			self::redirectToGeneralSettings();
	}
	
	
	/**
	 * get expires html
	 */
	public static function getHTMLExpires($expiresAt){
		
		if(empty($expiresAt))
			return("");
		
		$stamp = time();
		
		$expiresIn = $expiresAt - $stamp;
		
		$expireDays = $expiresIn / 60 / 60 / 24;
		
		$expireDays = ceil($expireDays);
		
		if($expireDays < 0){
			$expireDays *= -1;
			$html = "<span class='unite-color-red'>".__("The token has expired ","unlimited-elements-for-elementor").$expireDays.__(" ago","unlimited-elements-for-elementor")."</span>";
		}else{
			
			$html = __("The token will expire in ","unlimited-elements-for-elementor").$expireDays .__(" days. Don't worry, it should auto renew.","unlimited-elements-for-elementor");
		}
		
		//add renew link
		
		$htmlLink = null;
		
		if($expireDays < 60){
			$linkRenew = HelperUC::getUrlAjax("renew_instagram_access_token");
			$htmlLink = HelperHtmlUC::getHtmlLink($linkRenew, "renew access token");
		}
		
		if(!empty($htmlLink))
			$html .= " ".$htmlLink;
				
		
		return($html);
	}
	
	/**
	 * put connect with instagram button to general settings
	 */
	public static function putConnectWithInstagramButton(){
		
		$urlAuthorize = InstagramAPIOfficialUC::URL_AUTHORIZE;
		$clientID = InstagramAPIOfficialUC::APP_CLIENT_ID;
		$urlConnect = InstagramAPIOfficialUC::URL_APP_CONNECT;
		
		$urlReturn = HelperUC::getUrlAjax("save_instagram_connect_data");
		
		$urlReturn = UniteFunctionsUC::encodeContent($urlReturn);
		
		$urlConnect = "{$urlAuthorize}?client_id={$clientID}&scope=user_profile,user_media&response_type=code&redirect_uri={$urlConnect}&state=$urlReturn";
		
		$buttonText = __("Connect With Instagram", "unlimited-elements-for-elementor");
		
		$htmlButton = HelperHtmlUC::getHtmlLink($urlConnect, "", "", "uc-button-connect-instagram");
		
		//put access data as well
		$data = self::getInstagramSavedAccessData();
		$accessToken = UniteFunctionsUC::getVal($data, "access_token");
		
		$expiresAt = UniteFunctionsUC::getVal($data, "expires");
		
		if(!empty($accessToken)){
		
			$expiresHTML = self::getHTMLExpires($expiresAt);

			$urlTestView = HelperUC::getViewUrl("instagram-test");
			$linkTest = HelperHtmlUC::getHtmlLink($urlTestView, "Test Instagram Data");
			
			?>
			<div id="uc_instagram_reconnect_message" class="instagram-reconnect-message">
				<?php _e("The instagram access token are already set up", "unlimited-elements-for-elementor")?>. 
				<a id="uc_button_delete_insta_data" href="javascript:void(0)" class="unite-button-secondary"> <?php _e("Clear Access Data","unlimited-elements-for-elementor")?></a>
					
				<br>
				&nbsp;<?php echo $linkTest?>							
				
			</div>
			<div id="uc_instagram_connect_button_wrapper" class="uc-instagram-connect-button-wrapper" style="display:none">
				<?php echo $htmlButton?>
			</div>
			<br>
			<div class="uc-instagram-message-expire"><?php echo $expiresHTML?></div>
			<?php
		}else{
			echo $htmlButton;		
			
		}
		?>
		<br><br>
		<?php 
	}
	
	
	/**
	 * renew the access token
	 * redirect to settings later
	 */
	public static function renewAccessToken(){
		
		$accessData = self::getInstagramSavedAccessData();
		
		$accessToken = UniteFunctionsUC::getVal($accessData, "access_token");
		
		if(empty($accessToken))
			return(false);
		
		//get new access token
		$objAPI = new InstagramAPIOfficialUC();
		$response = $objAPI->renewToken($accessToken);
		
		$data = array();
		$data["access_token"] = UniteFunctionsUC::getVal($response, "access_token");
		$data["expires"] = UniteFunctionsUC::getVal($response, "expires_in");
		
		self::saveInstagramConnectDataAjax($data, true, false);
		
		return(true);
	}
	
	
	/**
	 * check and renew access token if needed
	 */
	public static function checkRenewAccessToken(){
		
		$accessData = self::getInstagramSavedAccessData();
		
		$accessToken = UniteFunctionsUC::getVal($accessData, "access_token");
				
		if(empty($accessToken))
			return(false);
		
		$expires = UniteFunctionsUC::getVal($accessData, "expires");
		
		if(empty($expires))
			return(false);
		
		if(is_numeric($expires) == false)
			return(false);
			
		//$strTime = UniteFunctionsUC::timestamp2DateTime($expires);
		
		$currentStamp = time();
		
		$diff = $expires - $currentStamp;
		
		$month = 60*60*24*30;
		
		if($diff > $month)
			return(false);
		
		$isRenewed = false;
		
		try{
			
			$isRenewed = self::renewAccessToken();
			
		}catch(Exception $e){}
		
		return($isRenewed);
	}
	
	
	/**
	 * check transient once a day
	 */
	public static function checkRenewAccessToken_onceInAWhile(){
		
		if(self::$instaCheckRunOnce == true)
			return(false);
		
		self::$instaCheckRunOnce = true;
		
		$value = UniteProviderFunctionsUC::getTransient(self::KEY_RENEW_BLOCKED);
				
		if(!empty($value))
			return(false);
		
		UniteProviderFunctionsUC::setTransient(self::KEY_RENEW_BLOCKED, true, self::RENEW_DELAY_SIX_HOURS);
		
		set_transient(self::KEY_RENEW_BLOCKED, true, self::RENEW_DELAY_SIX_HOURS);
		
		$isRenewed = self::checkRenewAccessToken();
	}
	
	
	/**
	 * convert title to handle
	 */
	public static function convertTitleToHandle($title, $removeNonAscii = true){
		
		$handle = strtolower($title);
	
		$handle = str_replace(array("ä", "Ä"), "a", $handle);
		$handle = str_replace(array("å", "Å"), "a", $handle);
		$handle = str_replace(array("ö", "Ö"), "o", $handle);
	
		if($removeNonAscii == true){
	
			// Remove any character that is not alphanumeric, white-space, or a hyphen
			$handle = preg_replace("/[^a-z0-9\s\_]/i", " ", $handle);
	
		}
	
		// Replace multiple instances of white-space with a single space
		$handle = preg_replace("/\s\s+/", " ", $handle);
		// Replace all spaces with underscores
		$handle = preg_replace("/\s/", "_", $handle);
		// Replace multiple underscore with a single underscore
		$handle = preg_replace("/\_\_+/", "_", $handle);
		// Remove leading and trailing underscores
		$handle = trim($handle, "_");
	
		return($handle);
	}
	
	
	/**
	 * convert number to textual representation
	 */
	public static function convertNumberToText($num){
		
		$x = round($num);
					
		$x_number_format = number_format($x);
		
		if($x < 10000)
			return($x_number_format);
		
		$x_array = explode(',', $x_number_format);
		$x_parts = array('k', 'm', 'b', 't');
		$x_count_parts = count($x_array) - 1;
				
		$x_display = $x_array[0];
				
		$x_display .= $x_parts[$x_count_parts - 1];
		
		return $x_display;
	}
	
	
	/**
	 * validate instagram user
	 */
	public static function validateInstance($user, $instance="user"){
	
		UniteFunctionsUC::validateNotEmpty($user,"instagram $instance");
	
		if(preg_match('/^[a-zA-Z0-9._]+$/', $user) == false)
			UniteFunctionsUC::throwError("The instagram $instance is incorrect");
	
	}
	
	
	/**
	 * sanitize insta user
	 */
	public static function sanitizeUser($user){
	
		$user = str_replace("@","",$user);
	
		return($user);
	}
	
	
	/**
	 * sanitize insta user
	 */
	public static function sanitizeTag($tag){
	
		$tag = str_replace("#","", $tag);
		
		return($tag);
	}
	
	
	/**
	 * cache response
	 * $cacheTimeSeconds - 600 sec - 10 min.
	 */
	public static function cacheResponse($cacheKey, $response, $cacheTimeSeconds = 600){
		
		if(empty($response))
			return(false);
		
		if(empty($cacheTimeSeconds))
			$cacheTimeSeconds = 600;
		
		UniteProviderFunctionsUC::setTransient($cacheKey, $response, $cacheTimeSeconds);
	}
	
	
	/**
	 * get response from cache
	 */
	public static function getFromCache($cacheKey){
		
		$response = UniteProviderFunctionsUC::getTransient($cacheKey);
	
		return($response);
	}
	
	
	/**
	 * get simple remote url
	 */
	public static function getRemoteUrl($url, $arrHeaders = null, $params = null, $debug = false){
			
	        $curl = curl_init();
			
	        if(is_array($arrHeaders) == false)
				$arrHeaders = array();
	        							
	        //create get string
			$strGet = '';
			if(!empty($params)){
			foreach($params as $key=>$value){
				
				if(!empty($strGet))
					$strGet .= "&";
				
				if(is_array($value))
					$value = json_encode($value);
				
				$value = urlencode($value);
				$strGet .= "$key=$value";
			}
		   }
		    
		   
		   if(!empty($strGet))
		   	  $url = UniteFunctionsUC::addUrlParams($url, $strGet);
			

		   $curl_options = array(
	            CURLOPT_RETURNTRANSFER => true,
	            CURLOPT_HEADER => true,
	            CURLOPT_URL => $url,
	            CURLOPT_HTTPHEADER => $arrHeaders,
	            CURLOPT_SSL_VERIFYPEER => false,
	            CURLOPT_CONNECTTIMEOUT => 15,
	            CURLOPT_TIMEOUT => 60
	        );

		   if($debug == true){
		   		
		   		dmp($curl_options);
		   }
		   	
	        curl_setopt_array($curl, $curl_options);
			
	        $response = curl_exec($curl);
	        $arrInfo = curl_getinfo($curl);
						
	        if($debug == true){
	        	dmp($response);
	        	dmp($arrInfo);
	        	exit();
	        }
	        
			$code =  UniteFunctionsUC::getVal($arrInfo, "http_code");
			switch($code){
				case 200:
				case 400:
				break;
				default:
					
					$error = null;
					
					if(!empty($curl))
						$error = @curl_error($curl);
					
					if(empty($error)){
						$error = $response;
					}
					
					UniteFunctionsUC::throwError("request error: ".$error.", code: $code");
				break;
			}
	        
			curl_close($curl);
			
			//cut the header 
			$headerSize = UniteFunctionsUC::getVal($arrInfo, "header_size");
			if(!empty($headerSize))
				$response = substr($response, $headerSize);
			
			return($response);
	}
	
	
	/**
	 * containing - cotnain the txtopen adn txtclose or not
	 */
	public static function getTextPart($contents, $txtOpen, $txtClose, $containing = false, $numTimes = 1){
	
		$pos1 = strpos($contents,$txtOpen);
		if($numTimes>1) {
			for($i=1;$i<$numTimes;$i++){
				$pos1 = strpos($contents,$txtOpen,$pos1+1);
			}
		}
	
		if($pos1 === FALSE)
			return(false);
	
		if($containing == false)
			$pos1 += strlen($txtOpen);
	
		$pos2 = strpos($contents,$txtClose,$pos1);
		if($pos2 === false)
			return(false);
	
		if($containing == true)
			$pos2 += strlen($txtClose);
	
		$trans = substr($contents,$pos1,$pos2-$pos1);
	
		$trans = trim($trans);
	
		return($trans);
	}
	
	
	/**
	 * convert stamp to date
	 */
	public static function stampToDate($stamp){
		
		if(is_numeric($stamp) == false)
			return("");
		
		$dateText = date("d F y, h:i", $stamp);
		
		return($dateText);
	}
	
	
	/**
	 * get time sinse the event
	 */
	public static function getTimeSince($time_stamp){
		
		$time_difference = strtotime('now') - $time_stamp;
		
		//year
		if ($time_difference >= 60 * 60 * 24 * 365.242199)
			return self::get_time_ago_string($time_stamp, 60 * 60 * 24 * 365.242199, 'y');
		
		//month
		if ($time_difference >= 60 * 60 * 24 * 30.4368499)
			return self::get_time_ago_string($time_stamp, 60 * 60 * 24 * 30.4368499, 'mon');
		
		//week
		if ($time_difference >= 60 * 60 * 24 * 7)
			return self::get_time_ago_string($time_stamp, 60 * 60 * 24 * 7, 'w');
		
		//day
		if ($time_difference >= 60 * 60 * 24)
			return self::get_time_ago_string($time_stamp, 60 * 60 * 24, 'd');
		
		//hour
		if($time_difference >= 60 * 60)
			return self::get_time_ago_string($time_stamp, 60 * 60, 'h');
		
		//minute
		return self::get_time_ago_string($time_stamp, 60, 'min');
	}
	
	
	/**
	 * get time ago string
	 */
	private static function get_time_ago_string($time_stamp, $divisor, $time_unit){
		
		$time_difference = strtotime("now") - $time_stamp;
		$time_units      = floor($time_difference / $divisor);
		
		settype($time_units, 'string');
		
		if ($time_units === '0')
			return '1' . $time_unit;
		
		return $time_units . $time_unit;
	}	
	
	
	
}