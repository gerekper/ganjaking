<?php
namespace Happy_Addons_Pro;

defined( 'ABSPATH' ) || die();

class Extensions_Manager {

    public static function init() {
		include_once HAPPY_ADDONS_PRO_DIR_PATH . 'extensions/happy-features.php';

		if ( hapro_is_image_masking_enabled() ) {
			include_once HAPPY_ADDONS_PRO_DIR_PATH . 'extensions/image-masking.php';
		}

		if ( hapro_is_happy_particle_effects_enabled() ) {
			include_once HAPPY_ADDONS_PRO_DIR_PATH . 'extensions/happy-particle-effects.php';
		}

		include(HAPPY_ADDONS_PRO_DIR_PATH . 'extensions/mega-menu.php');

		add_action( 'admin_enqueue_scripts', [ __CLASS__, 'admin_script_register' ] );
	}

	public static function load_display_condition() {
		include_once HAPPY_ADDONS_PRO_DIR_PATH . 'extensions/display-conditions.php';
	    include_once HAPPY_ADDONS_PRO_DIR_PATH . 'extensions/conditions/condition.php';
	}

	public static function admin_script_register() {
		do_action( 'extension_admin_scripts' );
	}
}

Extensions_Manager::init();
