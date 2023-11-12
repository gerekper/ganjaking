<?php

class PAFE_Controls_Manager{

    private static $_instance = null;

    const TAB_PAFE = 'tab_pafe';

    public static function instance()
    {
        if(is_null(self::$_instance)){
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    private function __construct()
    {
		if(version_compare(ELEMENTOR_VERSION,'1.5.5')){
			add_filter( 'elementor/init', [ $this, 'add_pafe_tab'], 50,1);
		}else{
			add_filter( 'elementor/controls/get_available_tabs_controls', [ $this, 'add_pafe_tab'], 50,1);
		}
    }

    public function add_pafe_tab($tabs){
        if(version_compare(ELEMENTOR_VERSION,'1.5.5')){
			\Elementor\Controls_Manager::add_tab(self::TAB_PAFE, __( 'PAFE', 'pafe' ));
		}else{
			$tabs[self::TAB_PAFE] = __( 'PAFE', 'pafe' );
		}    
        return $tabs;
    }
}

PAFE_Controls_Manager::instance();