<?php

defined('UNLIMITED_ELEMENTS_INC') or die('Restricted access');

class UCEmptyTemplate{

	const SHOW_DEBUG = false;
	
	private $templateID;
	
	
	/**
	 * construct
	 */
	public function __construct(){
		$this->init();
	}
	
	/** 
	 * put error message
	 */
	private function putErrorMessage($message = null){
		
		if(self::SHOW_DEBUG == true)
			dmp($message);
		
		dmp("no output");
		
	}
	
	/**
	 * render header debug
	 */
	private function renderHeader(){
		?>
		<header class="site-header">
			<p class="site-title">
				<a href="<?php echo esc_url( home_url( '/' ) ); ?>">
					<?php bloginfo( 'name' ); ?>
				</a>
			</p>
			<p class="site-description"><?php bloginfo( 'description' ); ?></p>
		</header>
		<?php 
	}
	
	/**
	 * render regular post body
	 */
	private function renderRegularBody(){
		
  	$this->renderHeader();
  	
	if ( have_posts() ) :
			
				while ( have_posts() ) :
			
					the_post();
					the_content();
					
				endwhile;
		endif;
	}
	
	/**
	 * validate that template exists
	 */
	private function validateTemplateExists(){
		
		if(empty($this->templateID))
			UniteFunctionsUC::throwError("no template found");
		
		$template = get_post($this->templateID);
		if(empty($template))	
			UniteFunctionsUC::throwError("template not found");
		
		$postType = $template->post_type;
		
		if($postType != "elementor_library")
			UniteFunctionsUC::throwError("bad template");
			
	}
	
	/**
	 * render header part
	 */
	private function renderHeaderPart(){
		?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
  <head>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="profile" href="https://gmpg.org/xfn/11">
    <?php wp_head(); ?>
  </head>
  <body <?php body_class(); ?>>
		
		<?php 
	}
	
	/**
	 * render footer part
	 */
	private function renderFooter(){
		wp_footer();
		
		?>
			</body>
		</html>
		<?php 
	}
	
	/**
	 * render template
	 */
	private function renderTemplate(){

		$this->validateTemplateExists();
		
		$content = HelperProviderCoreUC_EL::getElementorTemplate($this->templateID, true);
		
		
		$this->renderHeaderPart();
		
		//$this->renderRegularBody();
		
		echo $content;
		
		$this->renderFooter();
		
}
	
	
	
	/**
	 * init the template
	 */
	private function init(){
		
		try{
			
  			show_admin_bar(false);
			
			$renderTemplateID = UniteFunctionsUC::getGetVar("ucrendertemplate","",UniteFunctionsUC::SANITIZE_ID);
			
			if(empty($renderTemplateID))
				UniteFunctionsUC::throwError("template id not found");
			
			if(is_singular() == false)
				UniteFunctionsUC::throwError("not singlular");

			
			$this->templateID = $renderTemplateID;
				
			$this->renderTemplate();
			
			
		}catch(Exception $e){
			
			$message = $e->getMessage();
			
			$this->putErrorMessage($message);
			
		}
		
	}
	
}

new UCEmptyTemplate();