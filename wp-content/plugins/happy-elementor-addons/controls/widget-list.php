<?php
/**
 * Widget List control extended from select2
 *
 * @package Happy_Addons
 */
namespace Happy_Addons\Elementor\Controls;

use Elementor\Control_Select2;

defined( 'ABSPATH' ) || die();

class Widget_List extends Control_Select2 {

	const TYPE = 'widget-list';

	public function get_type() {
		return self::TYPE;
	}
}
