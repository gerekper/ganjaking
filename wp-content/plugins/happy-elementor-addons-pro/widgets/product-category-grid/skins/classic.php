<?php
namespace Happy_Addons_Pro\Widget\Skins\Product_Category_Grid;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Classic extends Skin_Base {

	/**
	 * Get widget ID
	 *
	 * @return string
	 */
	public function get_id() {
		return 'classic';
	}

	/**
	 * Get widget title
	 *
	 * @return string widget title
	 */
	public function get_title() {
		return __( 'Classic', 'happy-addons-pro' );
	}

}
