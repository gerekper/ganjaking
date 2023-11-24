<?php

class BloxViewLayoutOuterProvider extends BloxViewLayoutOuter{
	
	
	/**
	 * set page title
	 */
	public function setPageTitle(){
		
		if(!$this->layoutID)
			$title = esc_html__("New Page", "unlimited-elements-for-elementor");
		else{
			$title = $this->objLayout->getTitle(true);
			$title .= " - ".esc_html__("Edit Page", "unlimited-elements-for-elementor")."";
		}
		
		UniteProviderFunctionsUC::setAdminTitle($title);
		
	}
	
	
	/**
	 * change the auto draft to draft for a new page
	 */
	private function checkModifyNewPage(){
		
		if(empty($this->layoutID))
			return(false);
			
		$post = get_post($this->layoutID);
		
		$status = $post->post_status;
		
		$arrUpdate = array();
		if($status == "auto-draft"){
			$arrUpdate["post_status"] = "draft";
			
			//update title
			$title = UniteFunctionsUC::getGetVar("title", "", UniteFunctionsUC::SANITIZE_TEXT_FIELD);
			if(empty($title)){
				$objLayout = new UniteCreatorLayout();
				$title = $objLayout->getNewLayoutTitle();
			}
			
			$arrUpdate["title"] = $title;
			$arrUpdate["post_name"] = sanitize_title($title);
			
		}
		
		if(empty($arrUpdate))
			return(false);
		
		$arrUpdate["ID"] = $this->layoutID;
		
		wp_update_post($arrUpdate);
	}
	
	
	/**
	 * the constructor
	 */
	public function __construct(){
		parent::__construct();
		
		$this->checkModifyNewPage();
		
		$this->setPageTitle();
		
		$this->display();
	}
	
}