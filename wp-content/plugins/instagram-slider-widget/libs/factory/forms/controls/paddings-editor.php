<?php
	/**
	 * Paddings Control
	 *
	 * @author Alex Kovalev <alex.kovalevv@gmail.com>
	 * @copyright (c) 2018, Webcraftic Ltd
	 *
	 * @package factory-forms
	 * @since 1.0.0
	 */

	// Exit if accessed directly
	if( !defined('ABSPATH') ) {
		exit;
	}

	if( !class_exists('Wbcr_FactoryForms436_PaddingsEditorControl') ) {

		class Wbcr_FactoryForms436_PaddingsEditorControl extends Wbcr_FactoryForms436_Control {

			public $type = 'paddings-editor';

			/**
			 * Converting string to integer.
			 *
			 * @since 1.0.0
			 * @return integer
			 */
			public function html()
			{

				$name = $this->getNameOnForm();
				$raw_value = esc_attr($this->getValue());

				$units = $this->getOption('units');
				$values_with_units = explode(' ', $raw_value);

				$values = array();
				foreach($values_with_units as $value_with_unit) {
					$values[] = intval($value_with_unit);
				}

				$unit = $this->getOption('units', 'px');
				$range = $this->getOption('range', array(0, 99));
				$step = $this->getOption('step', 1);

				?>
				<div <?php $this->attrs() ?>
					data-units="<?php echo $unit ?>"
					data-range-start="<?php echo $range[0] ?>"
					data-range-end="<?php echo $range[1] ?>"
					data-step="<?php echo $step ?>">
					<div class="factory-rectangle">
						<div class="factory-side factory-side-top" data-value="<?php echo $values[0] ?>">
							<span class="factory-visible-value"><?php echo $values[0] ?><?php echo $units ?></span>
						</div>
						<div class="factory-side factory-side-bottom" data-value="<?php echo $values[1] ?>">
							<span class="factory-visible-value"><?php echo $values[1] ?><?php echo $units ?></span>
						</div>
						<div class="factory-side factory-side-left" data-value="<?php echo $values[2] ?>">
							<span class="factory-visible-value"><?php echo $values[2] ?><?php echo $units ?></span>
						</div>
						<div class="factory-side factory-side-right" data-value="<?php echo $values[3] ?>">
							<span class="factory-visible-value"><?php echo $values[3] ?><?php echo $units ?></span>
						</div>
						<div class="factory-side factory-side-center" data-value="<?php echo $values[0] ?>"></div>
					</div>
					<div class="factory-slider-container">
						<label class="factory-title">
							<?php _e('Select a side and move the slider to set up:', 'wbcr_factory_forms_436') ?>
						</label>

						<div class="factory-slider">
							<div class="factory-bar"></div>
						</div>
					</div>
					<input type="hidden" class="factory-result" name="<?php echo $name ?>" value="<?php echo $raw_value ?>"/>
				</div>
			<?php
			}
		}
	}