<?php

if (!defined('ABSPATH'))
	exit; // Exit if accessed directly

if (!class_exists('WPML_Compatibility')) :
	
	class WPML_Compatibility {
		
		function __construct(){
	        add_action('init', array($this, 'init'),9);
	    }	
	    
	    function init(){
	    	add_action('before_invoice_content', array($this, 'switch_language'), 10, 1);     
	    	add_action('after_invoice_content', array($this, 'reset_language'), 10, 1);  
	    }
	    
	    function switch_language($order_id){
		    global $sitepress;
			$order_lang = get_post_meta( $order_id, 'wpml_language', true );
			if($order_lang!=''){
				$current_language = $sitepress->get_current_language();
				$sitepress->switch_lang($order_lang);
			}
	    }
	    
	    function reset_language(){
		    global $sitepress;
			$default_lang = $sitepress->get_default_language();
		    $sitepress->switch_lang($default_lang);
	    }
	}

endif; // Class exists check
