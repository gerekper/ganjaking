<?php

namespace ElementorControls;

if (!defined('ABSPATH')) exit;

class PAFE_Custom_Controls {

	public function includes() {
		require_once( __DIR__ . '/select-control.php' );
		require_once( __DIR__ . '/select-files-control.php' );
	}

	public function register_controls() {
		$this->includes();
		$controls_manager = \Elementor\Plugin::$instance->controls_manager;
        if(version_compare(ELEMENTOR_VERSION, '3.9.0', '<')){
		    $controls_manager->register_control(\Elementor\PafeCustomControls\Select_Control::Select, new \Elementor\PafeCustomControls\Select_Control());
		    $controls_manager->register_control(\Elementor\PafeCustomControls\Select_Files_Control::Select_Files, new \Elementor\PafeCustomControls\Select_Files_Control());
        }else{
            $controls_manager->register(new \Elementor\PafeCustomControls\Select_Control());
            $controls_manager->register(new \Elementor\PafeCustomControls\Select_Files_Control());
        }
	}

	public function __construct() {
        if(version_compare(ELEMENTOR_VERSION, '3.9.0', '<')){
		    add_action('elementor/controls/controls_registered', [$this, 'register_controls']);
        }else{
            add_action('elementor/controls/register', [$this, 'register_controls']);
        }
	}

}

new PAFE_Custom_Controls();