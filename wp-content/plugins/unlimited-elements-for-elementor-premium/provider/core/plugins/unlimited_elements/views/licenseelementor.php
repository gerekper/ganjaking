<?php
/**
 * @package Unlimited Elements
 * @author unlimited-elements.com
 * @copyright (C) 2021 Unlimited Elements, All Rights Reserved. 
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * */
defined('UNLIMITED_ELEMENTS_INC') or die('Restricted access');

require HelperUC::getPathViewObject("activation_view.class");


class UnlimitedElementsLicenceView extends UniteCreatorActivationView{
	
	/**
	 * init by upress
	 */
	private function initByUpress(){
		
		$this->codeType = self::CODE_TYPE_UPRESS;
		
		$this->textUnleash = esc_html__("Activate Unlimited Elements Plugin UPress version", "unlimited-elements-for-elementor");
		
		$this->textGoPro = __("Activate In UPress","unlimited-elements-for-elementor");
		
		$this->textDontHave = null;
		
		$this->textLinkToBuy = null;

		$this->textPasteActivationKey = null;
		
		$this->showCodeInput = false;
		
	}
	
	/**
	 * init by envato
	 */
	private function initByEnvato(){
		
		$this->codeType = self::CODE_TYPE_ENVATO;
		$this->textPasteActivationKey = esc_html__("Paste your envato activation key here", "unlimited-elements-for-elementor");

		$urlActivation = HelperUC::getViewUrl(GlobalsUnlimitedElements::VIEW_LICENSE_ELEMENTOR);
		
		//$this->textSwitchTo = "For back to regular activation <br> 
			//<a href='$urlActivation'>Click Here</a>
		//";
		
		$this->textDontHave = __("The activation key (product key) is located in <br> downloads section in CodeCanyon.","unlimited-elements-for-elementor");
		
		$this->textLinkToBuy = null;
		
	}
	
	
	
	
	/**
	 * put popup form
	 */
	protected function putPopupForm(){
		
		if($this->codeType == self::CODE_TYPE_ENVATO || $this->codeType == self::CODE_TYPE_UPRESS){
			parent::putPopupForm();
			return(false);
		}
				
		?>
		<span class="activate-license unlimited_elements_for_elementor">
             
	         <a href="javascript:void(0)" class='uc-button-activate'><?php echo esc_attr($this->textActivate)?></a>
		</span>             
             <br>
             <br>
		<?php
	}
	
	/**
	 * init by freemius
	 */
	private function initByFreemius(){
		
		$this->codeType = self::CODE_TYPE_FREEMIUS;
		$this->simpleButtonMode = true;
		$this->simpleButtonCssClass = "";

		$urlCCodecanyon = HelperUC::getViewUrl(GlobalsUnlimitedElements::VIEW_LICENSE_ELEMENTOR, "envato=true");
		
		$this->textSwitchTo = "Have old codecanyon activation key? <br> 
			<a href='$urlCCodecanyon'>Go here</a> and activate
		";
	}
	
	/**
	 * constructor
	 */
	public function __construct(){
		
		parent::__construct();
		
		//$isEnvato = UniteFunctionsUC::getGetVar("envato", "", UniteFunctionsUC::SANITIZE_TEXT_FIELD);
		//$isEnvato = UniteFunctionsUC::strToBool($isEnvato);

		$this->textActivate = esc_html__("Activate Unlimited Elements", "unlimited-elements-for-elementor");
		
		$this->urlPricing = GlobalsUnlimitedElements::LINK_BUY;
		
		$this->textPlaceholder = esc_html__("xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx","unlimited-elements-for-elementor");
		
		$this->textUnleash = esc_html__("Unleash access to +700 widgets for Elementor", "unlimited-elements-for-elementor");
		
		$this->textAndTemplates = "";
		//$this->textPasteActivationKey = esc_html__("Paste your envato activation key here", "unlimited-elements-for-elementor");
				
		$this->textDontHaveLogin = "";
		
		$type = "envato";
		if(defined("UNLIMITED_ELEMENTS_UPRESS_VERSION"))
			$type = "upress";
		
		switch($type){
			case "freemius":
				$this->initByFreemius();
			break;
			case "envato":
				$this->initByEnvato();
			break;
			case "upress":
				$this->initByUpress();
			break;
			default:
				UniteFunctionsUC::throwError("Wrong type");
			break;
		}
		
		$this->isExpireEnabled = false;
		$this->product = GlobalsUnlimitedElements::PLUGIN_NAME;
		
		
		$this->textYourProAccountLifetime = esc_html__("Unlimited Elements is activated lifetime for this site!", "unlimited-elements-for-elementor");
		
	}
	
	
	/**
	 * put header html
	 */
	protected function putHeaderHtml(){
				
		$headerTitle = esc_html__(" License", "unlimited-elements-for-elementor");
		
		require HelperUC::getPathTemplate("header");
		
	}
	
	
	/**
	 * put freemius scripts
	 */
	private function putFsScripts(){
		
		 $moduleID = GlobalsUnlimitedElements::FREEMIUS_PLUGIN_ID;
		
         $vars = array(
                'id' => $moduleID,
         );
		
        fs_require_template( 'forms/license-activation.php', $vars );
        fs_require_template( 'forms/resend-key.php', $vars );
	}
	
	
	/**
	 * put upress active message
	 */
	private function putUpressMessage($isActive){
		
		if($isActive == true){
			$message = __("The Unlimited Elements UPress license is active","unlimited-elements-for-elementor");
			$class = "uc-license-message-active";
		}
		else{
			$class = "uc-license-message-notactive";
			$message = __("The Unlimited Elements UPress license is not active","unlimited-elements-for-elementor");
		}
		?>
		
		<h2 class="<?php echo $class?>">
			<?php echo $message?>
		</h2>
		<?php 
		
	}
	
	
	/**
	 * put html deactivation
	 */
	public function putHtmlDeactivate(){
		
		if($this->codeType == self::CODE_TYPE_UPRESS){
			$this->putUpressMessage(true);
			return(false);
		}
		
		$isActiveByFreemius = HelperProviderUC::isActivatedByFreemius();
		
		if($isActiveByFreemius == false){
			parent::putHtmlDeactivate();
			return(false);
		}

		?>
		
		<h2>
		
			Your license has been activated for this site.
		
		</h2>
		
		<br>
		
		<span class="activate-license unlimited_elements_for_elementor">
             
	         <a href="javascript:void(0)" class="unite-button-primary"><?php _e("Change License", "unlimited-elements-for-elementor")?></a>
		</span>             
             <br>
             <br>
		<?php
		
	}
	
	/**
	 * put the view
	 */
	public function display(){
				
		$this->putHeaderHtml();
		
		$webAPI = new UniteCreatorWebAPI();
		
		if(!empty($this->product))
			$webAPI->setProduct($this->product);
		
		$isActive = $webAPI->isProductActive();
		
		
		?>
		<div class="unite-content-wrapper">
		<?php 
		
		if($isActive == true)		//active
			$this->putHtmlDeactivate();
		else{						//not active
			
			if($this->codeType == self::CODE_TYPE_UPRESS)
				$this->putUpressMessage(false);
			else
				$this->putActivationHtml();
		}
		
		$this->putJSInit();		
		
		?>
		</div>
		<?php 
		
		if($this->codeType == self::CODE_TYPE_FREEMIUS)
			$this->putFSScripts();
		
	}
	
	
}

//require "licensefs.php";

$objLicense = new UnlimitedElementsLicenceView();
$objLicense->display();

