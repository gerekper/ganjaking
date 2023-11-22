<?php
/**
 * Premium Grid Condition.
 */

namespace PremiumAddonsPro\Includes\GridBuilder;

// use ElementorPro\Modules\ThemeBuilder\Module;
// use ElementorPro\Core\Utils;
// use ElementorPro\Modules\ThemeBuilder\Conditions\Post;

use ElementorPro\Modules\ThemeBuilder\Conditions\Condition_Base;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Premium_Grid_Conditions extends Condition_Base {

	protected $sub_conditions = array();

	public static function get_type() {
		return 'premium_grid';
	}

	public function get_name() {
		return 'premium_grid';
	}

	public static function get_priority() {
		return 60;
	}

	public function get_label() {
		return __( 'Premium Grid', 'premium-addons-pro' );
	}

	public function get_all_label() {
		return __( 'No Conditions', 'premium-addons-pro' );
	}

	public function check( $args ) {
		return false;
	}

	public function register_sub_conditions() {
		$this->sub_conditions[] = 'not_found404';
	}
}

add_action(
	'elementor/theme/register_conditions',
	function( $conditions_manager ) {
		$conditions_manager->get_condition( 'general' )->register_sub_condition( new Premium_Grid_Conditions() );
	},
	100
);
