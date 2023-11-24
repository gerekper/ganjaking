<?php
namespace Elementor;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Control_UC_HR extends Base_Data_Control {

	/**
	 * Retrieve code control type.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string Control type.
	 */
	public function get_type() {
		return 'uc_hr';
	}

	/**
	 * get hr default settings
	 */
	protected function get_default_settings() {
		return [
		];
	}

	/**
	* get content template
	 */
	public function content_template() {
		$control_uid = $this->get_control_uid();
		?>
		<div class="elementor-control-field">
				<hr class="{{{ data.class }}}">
		</div>
		<?php
	}
}
