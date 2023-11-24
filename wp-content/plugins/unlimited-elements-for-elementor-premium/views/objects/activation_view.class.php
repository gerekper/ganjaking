<?php
/**
 * @package Unlimited Elements
 * @author unlimited-elements.com
 * @copyright (C) 2021 Unlimited Elements, All Rights Reserved. 
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * */
defined('UNLIMITED_ELEMENTS_INC') or die('Restricted access');

class UniteCreatorActivationView extends UniteElementsBaseUC{

	const CODE_TYPE_ACTIVATION = "activation";
	const CODE_TYPE_ENVATO = "envato";
	const CODE_TYPE_FREEMIUS = "freemius";
	const CODE_TYPE_UPRESS = "upress";
	
	protected $urlPricing;
	protected $urlSupport;
	protected $textGoPro, $textAndTemplates, $textPasteActivationKey, $textPlaceholder;
	protected $textLinkToBuy, $textDontHave, $textActivationFailed, $textActivationCode;
	protected $codeType = self::CODE_TYPE_ACTIVATION;
	protected $product;
	protected $isExpireEnabled = true, $textSwitchTo;
	protected $writeRefreshPageMessage = true;
	protected $textDontHaveLogin, $textLinkToLogin, $urlLogin;
	protected $textUnleash, $textActivate, $textYourProAccountLifetime;
	protected $showCodeInput = true;
	protected $simpleButtonMode = false;
	
	
	/**
	 * init the variables
	 */
	public function __construct(){
		
		$this->urlPricing = GlobalsUC::URL_BUY;
		$this->urlSupport = GlobalsUC::URL_SUPPORT;
		
		$this->textActivate = esc_html__("Activate Blox Pro", "unlimited-elements-for-elementor");
		
		$this->textGoPro = esc_html__("GO PRO", "unlimited-elements-for-elementor");
		$this->textUnleash = esc_html__("Unleash access to +700 addons,", "unlimited-elements-for-elementor");
		
		$this->textAndTemplates = esc_html__("+100 page templates and +50 section designs", "unlimited-elements-for-elementor");
		
		$this->textPasteActivationKey = esc_html__("Paste your activation key here", "unlimited-elements-for-elementor");
		
		$this->textPlaceholder = "xxxx-xxxx-xxxx-xxxx";
		$this->textLinkToBuy = esc_html__("View our pricing plans", "unlimited-elements-for-elementor");
		
		$this->textDontHave = esc_html__("Don't have a pro activation key?", "unlimited-elements-for-elementor");

		$this->textDontHaveLogin = esc_html__("If you already purchased, get the key from my account?", "unlimited-elements-for-elementor");
		$this->textLinkToLogin = esc_html__("Go to My Account", "unlimited-elements-for-elementor");
		$this->urlLogin = "http://my.unitecms.net";
		
		$this->textActivationFailed = esc_html__("You probably got your activation code wrong", "unlimited-elements-for-elementor");
		
		$this->textYourProAccountLifetime = esc_html__("Your pro account is activated lifetime for this site", "unlimited-elements-for-elementor");
		
	}
	
	
	/**
	 * put pending activation html
	 */
	public function putPendingHTML(){
		?>
		You are using free version of <b>Unlimited Elements</b>. The pro version will be available for sale in codecanyon.net within 5 days.
		<br>
		<br>
		Please follow the plugin updates, and the pro version activation will be revealed.
		<br>
		<br>
		For any quesiton you can turn to: <b>support@blox-builder.com</b>
		<?php 
	}
	
	/**
	 * put popup form
	 */
	protected  function putPopupForm(){
		?>
			<?php if(!empty($this->textPasteActivationKey)):?>
             <label><?php echo esc_html($this->textPasteActivationKey)?>:</label>
              <?php endif?>
              
              <?php if($this->showCodeInput == true):?>
              <input id="uc_activate_pro_code" type="text" placeholder="<?php echo esc_attr($this->textPlaceholder)?>" value="">
              <?php endif?>
              
              <div class="uc-activation-section-wrapper">
                                
	              <input id="uc_button_activate_pro" type="button" class='uc-button-activate' data-codetype="<?php echo esc_attr($this->codeType)?>" data-product="<?php echo esc_attr($this->product)?>" value="<?php echo esc_attr($this->textActivate)?>">
                  
                   <div id="uc_loader_activate_pro" class="uc-loader-activation" style='display:none'>
					
						<span class='loader_text'>	                                	
	                                		<?php esc_html_e("Activating", "unlimited-elements-for-elementor")?>...
	                    </span>
	                   
	               </div>
	                                
               </div>
		
		<?php 
	}
	
	/**
	 * put activation html
	 */
	public function putActivationHtml(){
					
		
		?>
		   <div class="uc-activation-view">
		   	   
	           <div class="uc-popup-container uc-start">
	                <div class="uc-popup-content">
	                    <div class="uc-popup-holder">
	                        <div class="xlarge-title"><?php echo esc_html($this->textGoPro)?></div>
	                        
	                        <div class="popup-text"><?php echo esc_html($this->textUnleash)?><br> <?php echo esc_html($this->textAndTemplates)?></div>
	                        <div class="popup-form">
	                        		
	                            <?php $this->putPopupForm()?>
	                                
	                        </div>
	                        
	                        <div class="bottom-text">
	                        	<?php echo $this->textDontHave?>
	                        	<br>
	                        	<a href="<?php echo esc_attr($this->urlPricing)?>" target="_blank" class="blue-text"><?php echo esc_html($this->textLinkToBuy)?></a>
	                        </div>
	                        
	                        <?php if(!empty($this->textDontHaveLogin)):?>
	                        
	                        <div class="bottom-text">
	                        	<?php echo esc_html($this->textDontHaveLogin)?>
	                        	<br>
	                        	<a href="<?php echo esc_attr($this->urlLogin)?>" target="_blank" class="blue-text"><?php echo esc_html($this->textLinkToLogin)?></a>
	                        </div>
	                        
	                        <?php endif?>
	                        
							<?php if(!empty($this->textSwitchTo)):?>
	                        <div class="bottom-text">
	                        	<?php echo $this->textSwitchTo?><br>
	                        </div>
	                        <?php endif?>
	                        
	                	</div>
	            	</div>
	            </div>
	            
	            <!-- failed dialog -->
	            
	            <div class="uc-popup-container uc-fail hidden">
	                <div class="uc-popup-content">
	                    <div class="uc-popup-holder">
	                        <div class="large-title"><?php esc_html_e("Ooops", "unlimited-elements-for-elementor")?>.... <br><?php esc_html_e("Activation Failed", "unlimited-elements-for-elementor")?> :(</div>
	                        <div class="popup-error"></div>
	                        <div class="popup-text"><?php echo esc_html($this->textActivationFailed)?> <br>to try again <a id="activation_link_try_again" href="javascript:void(0)">click here</a></div>
	                        <div class="bottom-text"><?php esc_html_e("or contact our","unlimited-elements-for-elementor")?> <a href="<?php echo esc_attr($this->urlSupport)?>" target="_blank"><?php esc_html_e("support center", "unlimited-elements-for-elementor")?></a></div>
	                    </div>
	                </div>
	            </div>
	            
	            <!-- activated dialog -->
	            
	            <div class="uc-popup-container uc-activated hidden">
	                <div class="uc-popup-content">
	                    <div class="uc-popup-holder">
	                        <div class="xlarge-title"><?php esc_html_e("Hi Five", "unlimited-elements-for-elementor")?>!</div>
	                        
	                        <?php if($this->isExpireEnabled == true):?>
	                        	<div class="popup-text small-padding"><?php echo esc_html($this->textYourProAccountLifetime)?></div>
		                        <div class="days"></div>
		                        <span><?php esc_html_e("DAYS", "unlimited-elements-for-elementor")?></span>
		                        <br><br>
		                        
		                        <?php if($this->writeRefreshPageMessage == true):?>
		                        <a href="javascript:location.reload()" class="btn"><?php esc_html_e("Refresh page to View Your Pro Catalog", "unlimited-elements-for-elementor")?></a>
		                        <?php endif?>
		                        
	                        <?php else:?>
	                        	
	                        	<div class="popup-text small-padding"><?php esc_html_e("Your pro account is activated lifetime for this site","unlimited-elements-for-elementor")?>!</div>
		                       	
	                        	<div class="popup-text small-padding"><?php esc_html_e("Thank you for purchasing from us and good luck", "unlimited-elements-for-elementor")?>!</div>
	                        	
	                        <?php endif?>
	                        
	                    </div>
	                </div>
	            </div>
		</div>
		
		<?php 
	}
	
	/**
	 * put deactivate html
	 */
	public function putHtmlDeactivate(){
		
		?>
		<h2><?php esc_html_e("This pro version is active!", "unlimited-elements-for-elementor")?></h2>
		
		<a href="javascript:void(0)" class="uc-link-deactivate unite-button-primary" data-product="<?php echo esc_attr($this->product)?>"><?php esc_html_e("Deactivate Pro Version", "unlimited-elements-for-elementor")?></a>
		
		<?php 
	}
	
	
	/**
	 * put initing JS
	 */
	public function putJSInit(){
		?>
		
		<script>

		jQuery("document").ready(function(){

			if(!g_ucAdmin)
				var g_ucAdmin = new UniteAdminUC();
			
			g_ucAdmin.initActivationDialog(true);
			
			
		});
		
		</script>
		
		<?php 
	}
	
	/**
	 * put activation HTML
	 */
	public function putHtmlPopup(){
		
		$title = esc_html__("Activate Your Pro Account", "unlimited-elements-for-elementor");
		
		?>
           <div class="activateProDialog" title="<?php echo esc_attr($title)?>" style="display:none">
           		
           		<?php $this->putActivationHtml(true) ?>
            	
            </div>
		
		<?php 		
	}
	
}

