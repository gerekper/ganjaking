<?php
/*
 * This file belongs to the YIT Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */

add_filter( 'yith_wcms_step_button_class', 'yith_wcms_step_button_class_for_theretailer' );
add_filter( 'yith_wcms_frontend_dom_object', 'yith_wcms_frontend_dom_object_for_theretailer' );
add_action( 'wp_enqueue_scripts', 'yith_wcms_enqueue_scripts_for_theretailer', 100 );

if( ! function_exists( 'yith_wcms_step_button_class_for_theretailer' ) ){
	/**
	 * Add a HTML classes for The Retailer
	 *
	 * @since    1.3.13
	 * @return  string css classes
	 */
	function yith_wcms_step_button_class_for_theretailer( $classes ){
		return 'yith-wcms-button alt';
	}
}

if( ! function_exists( 'yith_wcms_frontend_dom_object_for_theretailer' ) ){
	/**
	 * Change dom object to localize script for The Retailer
	 *
	 * @since    1.3.13
	 * @return  array dom element for js script
	 */
	function yith_wcms_frontend_dom_object_for_theretailer( $dom ){
		$dom['button_next'] = '.yith-wcms-button.next';
		$dom['button_prev'] = '.yith-wcms-button.prev';
		return $dom;
	}
}

if( ! function_exists( 'yith_wcms_enqueue_scripts_for_theretailer' ) ){
	/**
	 * Add a css style for The Retailer
	 *
	 * @since    1.3.13
	 * @return  void
	 */
	function yith_wcms_enqueue_scripts_for_theretailer(){
		$css = "
		#form_actions input.yith-wcms-button.alt.prev {
		    margin-right: 5px;
		    display: none;
		}
		#form_actions input.yith-wcms-button.alt {    
			background-color: #a46497;
			color: #fff;
			-webkit-font-smoothing: antialiased;
			font-size: 100%;
			line-height: 1em;
			cursor: pointer;
			position: relative;
			text-decoration: none;
			text-align: center;
			overflow: visible;
			padding: 19px 30px;
			text-decoration: none;
			-webkit-border-radius: 0 !important;
			-moz-border-radius: 0 !important;
			border-radius: 0 !important;
			left: auto;
			color: #fff;
			/* text-shadow: 0 0 0 #ffffff !important; */
			border: 0 !important;
			-webkit-box-shadow: inset 0 0 0 rgba(0,0,0,0.075), inset 0 0 0 rgba(255,255,255,0.3), 0 0 0 rgba(0,0,0,0.1) !important;
			-moz-box-shadow: inset 0 0 0 rgba(0,0,0,0.075), inset 0 0 0 rgba(255,255,255,0.3), 0 0 0 rgba(0,0,0,0.1) !important;
			box-shadow: inset 0 0 0 rgba(0,0,0,0.075), inset 0 0 0 rgba(255,255,255,0.3), 0 0 0 rgba(0,0,0,0.1) !important;
			font-size: 12px !important;
			text-transform: uppercase !important;
			font-weight: 900 !important;
			max-width: 880px !important;
			-webkit-appearance: none;
			transition: all 0.3s ease;
			-webkit-transition: all 0.3s ease;
		}
		#form_actions input.yith-wcms-button.alt:hover{
		    background: #0054a6 !important;
		}";
		wp_add_inline_style( 'stylesheet', $css );
	}
}