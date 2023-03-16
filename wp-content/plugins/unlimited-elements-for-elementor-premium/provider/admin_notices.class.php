<?php

/**
 * @package Unlimited Elements
 * @author UniteCMS http://unitecms.net
 * @copyright Copyright (c) 2016 UniteCMS
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
*/

//no direct accees
defined ('UNLIMITED_ELEMENTS_INC') or die ('restricted aceess');

class UniteCreatorAdminNotices{
	
	const NOTICES_LIMIT = 1;
	const TYPE_ADVANCED = "advanced";
	const TYPE_BANNER = "banner";
	
	private static $isInited = false;
	private static $arrNotices = array();
	
	
	/**
	 * set notice
	 */
	public function setNotice($text, $id, $expire, $params = array()){
		
		//don't let to add more then limited notices
		if(count(self::$arrNotices) >= self::NOTICES_LIMIT)
			return(false);
		
			
		$type = UniteFunctionsUC::getVal($params, "type");
		
		if(empty($text) && $type != self::TYPE_BANNER)
			return(false);
			
		if(empty($id))
			return(false);
		
		$arrNotice = array();
		$arrNotice["text"] = $text;
		$arrNotice["id"] = $id;
		
		if(!empty($params)){
			unset($params["text"]);
			unset($params["id"]);
			unset($params["expire"]);
			
			$arrNotice = array_merge($arrNotice, $params);
		}
		
		if(isset(self::$arrNotices[$id]))
			return(false);
		
		self::$arrNotices[$id] = $arrNotice;
		
		$this->init();
	}
	
	
	/**
	 * get notice html
	 */
	private function getNoticeHtml($text, $id, $isDissmissable = true, $params = array()){
		
		$type = UniteFunctionsUC::getVal($params, "type");
		
		$isNoWrap = UniteFunctionsUC::getVal($params, "no-notice-wrap");
		$isNoWrap = UniteFunctionsUC::strToBool($isNoWrap);
		
		$classWrap = "notice ";
		
		if($isNoWrap == true)
			$classWrap = "";
							
		//set color class
		
		$color = UniteFunctionsUC::getVal($params, "color");
		
		switch($color){
			default:
			case "error":
			case "warning":
			case "info":
				$noticeClass = "notice-$color";
			break;
			case "doubly":
				$noticeClass = "uc-notice-doubly";
			break;
		}
		
		$class = "notice uc-admin-notice $noticeClass";
		
		if($type == self::TYPE_ADVANCED)
			$class .= " uc-notice-advanced";
				
		if($type == self::TYPE_BANNER){
			$class = "notice uc-admin-notice uc-type-banner";
				
			if($isNoWrap == true)
				$class .= " uc-admin-notice--nowrap";
				
		}
		
		$classDissmissable = "is-dismissible";
		$classDissmissable = "";
		
		$htmlDissmiss = "";
		
		if($isDissmissable == true){
			
			$textDissmiss = __("Dismiss", "unlimited-elements-for-elementor");
			$textDissmissLabel = __("Dismiss unlimited elements message","unlimited-elements-for-elementor");
			
			$textDissmiss = esc_attr($textDissmiss);
			$textDissmissLabel = esc_attr($textDissmissLabel);
			
			$addClassDissmiss = "";
							
			$urlDissmiss = GlobalsUC::$current_page_url;
			
			$urlDissmiss = UniteFunctionsUC::addUrlParams($urlDissmiss, "uc_dismiss_notice=$id");
			
			$htmlDissmiss = "\n<a class=\"uc-notice-dismiss\" href=\"{$urlDissmiss}\" aria-label=\"$textDissmissLabel\">$textDissmiss</a>\n";
			
			if($type == self::TYPE_BANNER)
				$htmlDissmiss = "\n<a class=\"uc-notice-dismiss-banner\" href=\"{$urlDissmiss}\" title=\"{$textDissmiss}\" aria-label=\"$textDissmissLabel\">X</a>\n";
			
		}
		
		switch($type){
			
			case self::TYPE_ADVANCED:
				
				$buttonText = UniteFunctionsUC::getVal($params, "button_text");
				$buttonLink = UniteFunctionsUC::getVal($params, "button_link");
							
				$urlLogo = GlobalsUC::$urlPluginImages."logo-circle.svg";
				
				$htmlButton = "";
				
				if(!empty($buttonText)){
					
					$htmlButton = "<a class='button button-primary' href='{$buttonLink}' target='_blank'>{$buttonText}</a>";
				}
				
				$text = "<div class='uc-notice-advanced-wrapper'>
					<span class='uc-notice-advanced__item-logo'>
						<img class='uc-image-logo-ue' width=\"40\" src='$urlLogo'>
					</span>
					<span class='uc-notice-advanced__item-text'>".$text.$htmlButton."</span>
				</div>";
				
			break;
			case self::TYPE_BANNER:
				
				$filename = UniteFunctionsUC::getVal($params, "banner");
				
				if(empty($filename))
					return(false);
				
				$urlBanner = GlobalsUC::$urlPluginImages.$filename;
				
				$buttonLink = UniteFunctionsUC::getVal($params, "button_link");
				
				$text = "<a class='uc-notice-banner-link' href='{$buttonLink}' target='_blank'>
					<img class='uc-notice-banner' src='{$urlBanner}'>
				</a>";
				
			break;
			
		}
		
		$html = "<div class=\"$class $classDissmissable\"><p>";
			$html .= $text."\n";
			$html .= $htmlDissmiss;
		$html .= "</p></div>";
		

		return($html);
	}
	
	/**
	 * check condition
	 */
	private function isConditionAllowed($notice){
		
		$condition = UniteFunctionsUC::getVal($notice, "condition");
		
		if(empty($condition))
			return(true);
		
		switch($condition){
			case "no_doubly":
				
				if(defined("DOUBLY_INC"))
					return(false);
			break;
		}
		
		return(true);
	}
	
	
	/**
	 * put admin notices
	 */
	public function putAdminNotices(){
		
		if(empty(self::$arrNotices))
			return(false);
		
		foreach(self::$arrNotices as $notice){
			
			$text = UniteFunctionsUC::getVal($notice, "text");
			$id = UniteFunctionsUC::getVal($notice, "id");
			
			$isDissmissed = $this->isNoticeDissmissed($id);

			if($isDissmissed == true)
				continue;
			
			//check condition
			$isAllowed = $this->isConditionAllowed($notice);
			
			if($isAllowed == false)
				return(false);
						
			$htmlNotices = $this->getNoticeHtml($text, $id, true, $notice);			
			
			if(empty($htmlNotices))
				continue;
			
			echo $htmlNotices;
		}
		
	}
	
	/**
	 * put styles
	 */
	public function putAdminStyles(){
		
		?>
		<!--  unlimited elements notices styles -->
		<style type="text/css">
			
			.uc-admin-notice{
				position:relative;
			}
			
			.uc-admin-notice.uc-notice-advanced{
				font-size:16px;
			}
			
			.uc-admin-notice--nowrap{
				padding:0px !important;
				border:none !important;
				background-color:transparent !important;
			}
			
			.uc-admin-notice.uc-type-banner{
				border-left-width:1px !important;
			}
			
			.uc-admin-notice .uc-notice-advanced-wrapper span{
				display:table-cell;
				vertical-align:middle;
			}
			
			.uc-admin-notice .uc-notice-advanced-wrapper .button{
				vertical-align:middle;
				margin-left:10px;
			}
			
			.uc-admin-notice .uc-notice-advanced__item-logo{
				padding-right:15px;
			}
			
			.uc-admin-notice .uc-notice-dismiss{
				position: absolute;
				top: 0px;
				right: 10px;
				padding: 10px 15px 10px 21px;
				font-size: 13px;
				text-decoration: none;			
			}
			
			.uc-admin-notice .uc-notice-dismiss::before{
				position: absolute;
				top: 10px;
				left: 0px;
				transition: all .1s ease-in-out;
				
				background: none;
				color: #72777c;
				content: "\f153";
				display: block;
				font: normal 16px/20px dashicons;
				speak: none;
				height: 20px;
				text-align: center;
				width: 20px;
			}
			
			.uc-admin-notice .uc-notice-dismiss:focus::before,
			.uc-admin-notice .uc-notice-dismiss:hover::before{
				color: #c00;			
			}
			
			.uc-notice-banner-link{
				display:block;
			}
			.uc-notice-banner{
				width:100%;
			}
 			
 			.uc-notice-dismiss-banner{
 				background-color:#000000;
 				position:absolute;
 				top:20px;
 				right:23px;
 				height:20px;
 				width:20px;
 				border-radius:20px;
 				font-size:12px;
 				text-decoration:none;
 				color:#ffffff;
 				text-align:center;
 			}
 			
 			.uc-notice-dismiss-banner:hover{
 				color:#ffffff;
				background-color:#c00;
 			}
 			
 			.uc-notice-dismiss-banner:focus,
 			.uc-notice-dismiss-banner:visited{
 				color:#ffffff; 				
 			}
 			
 			.uc-notice-doubly{
 				border-left-color:#ff6a00 !important;
 				border-color:#ff6a00 !important;
 			}
 			
 			.uc-notice-header{
 				font-weight:bold;
 				font-size:16px;
 			}
 			
 			.uc-notice-middle{
 				padding-top:10px;
 				padding-bottom:18px;
 			}
 			
 			.uc-notice-wrapper{
 				display:flex;
 			}
 			
 			.uc-notice-left{
 				padding-left:15px;
 				padding-right:30px;
 			}
 			
		</style>
		<?php 
	}
	
	/**
	 * check if some notice dissmissed
	 */
	private function isNoticeDissmissed($key){

		$userID = get_current_user_id();
		if(empty($userID))
			return(false);

		$value = get_user_meta($userID, "uc_notice_dissmissed_".$key, true);

		$value = UniteFunctionsUC::strToBool($value);
		
		return($value);
	}


	/**
	* check dissmiss action
	*/
	public function checkDissmissAction(){
				
		$dissmissKey = UniteFunctionsUC::getPostGetVariable("uc_dismiss_notice","", UniteFunctionsUC::SANITIZE_KEY);
		if(empty($dissmissKey))
			return(false);
		
		$userID = get_current_user_id();
		
		if(empty($userID))
			return(false);
		
		$metaKey = "uc_notice_dissmissed_".$dissmissKey;
		
		delete_user_meta($userID, $metaKey);
		add_user_meta($userID, $metaKey, "true", true);		
	}
	

	/**
	 * init
	 */
	private function init(){
				
		if(self::$isInited == true)
			return(false);	
					
		if(GlobalsUC::$is_admin == false)
			return(false);
		
		$this->checkDissmissAction();
		
		
		UniteProviderFunctionsUC::addFilter("admin_notices", array($this, "putAdminNotices"),10,3);
		
		UniteProviderFunctionsUC::addAction("admin_print_styles", array($this, "putAdminStyles"));
		
		self::$isInited = true;
	}
	
}
