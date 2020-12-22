<?php

namespace WBCR\FactoryClearfy228\Pages;

/**
 * Step
 * @author Webcraftic <wordpress.webraftic@gmail.com>
 * @copyright (c) 23.07.2020, Webcraftic
 * @version 1.0
 */
class Step_Custom extends Step {

	public function get_title()
	{
		return 'Custom step';
	}

	public function render_button($continue = true, $skip = false, $custom_title = null, $align = 'right')
	{
		$this->set_button_handler();
		$button_title = !empty($custom_title) ? $custom_title : __('Continue', 'wbcr_factory_clearfy_228');

		if( !$this->get_next_id() ) {
			$button_title = __('Finish', 'wbcr_factory_clearfy_228');
		}

		if( !in_array($align, ['center', 'left', 'right']) ) {
			$align = 'right';
		}

		?>
		<form method="post" id="w-factory-clearfy-228__setup-form-<?php echo $this->get_id() ?>" class="form-horizontal">
			<div class="w-factory-clearfy-228__form-buttons" style="text-align: <?php echo esc_attr($align); ?>">
				<?php if( $skip ): ?>
					<input type="submit" name="skip_button_<?php echo $this->get_id() ?>" class="button-primary button button-large w-factory-clearfy-228__skip-button" value="<?php _e('Skip', 'wbcr_factory_clearfy_228') ?>">
				<?php endif; ?>
				<?php if( $continue ): ?>
					<input type="submit" name="continue_button_<?php echo $this->get_id() ?>" class="button-primary button button-large w-factory-clearfy-228__continue-button" value="<?php echo $button_title; ?>">
				<?php endif; ?>
			</div>
		</form>
		<?php
	}

	protected function set_button_handler()
	{
		if( isset($_POST['continue_button_' . $this->get_id()]) ) {
			$this->continue_step();
		}

		if( isset($_POST['skip_button_' . $this->get_id()]) ) {
			$this->skip_step();
		}
	}

	public function html()
	{
		/// nothing
	}

}