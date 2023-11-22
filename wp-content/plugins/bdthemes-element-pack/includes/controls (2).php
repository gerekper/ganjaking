<?php
namespace ElementPack;

use Elementor\Base_Control;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Control_Choose extends Base_Control {

 // here is some base default settings set by the `choose` control
  protected function get_default_settings() {
     return [
		'options'     => [],
		'label_block' => true,
		'toggle'      => true,
     ];
  }
}