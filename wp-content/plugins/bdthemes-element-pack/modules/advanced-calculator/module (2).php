<?php
	namespace ElementPack\Modules\AdvancedCalculator;
	
	use ElementPack\Base\Element_Pack_Module_Base;
	
	if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
	
	class Module extends Element_Pack_Module_Base {
		
		public function get_name() {
			return 'advanced-calculator';
		}
		
		public function get_widgets() {
			
			$widgets = ['Advanced_Calculator'];
			
			return $widgets;
		}
		
		public function __construct() {
			parent::__construct();
		 
		}
		
	}
