<?php

defined('UNLIMITED_ELEMENTS_INC') or die;

class UniteCreatorLayoutsViewProvider extends UniteCreatorLayoutsView{

	/**
	 * display blocking text
	 */
	private function displayNotAvailableText(){
		?>
		<h2>
		<br>
		<br>
		The layouts list displayed via WP special links, if you entered this page, something must be wrong. 
		<br>
		<br>
		Please turn to developers in order to fix.
		
		</h2>
		<?php 
		
	}
	
	
	/**
	 * block display
	 */
	public function display(){
		
		if($this->objLayoutType->displayType != UniteCreatorAddonType_Layout::DISPLAYTYPE_MANAGER)
			$this->displayNotAvailableText();	
		else
			parent::display();
		
	}
		
}