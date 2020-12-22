<?php

	/**
	 * Gradient picker Control
	 *
	 * Main options:
	 *  name          => a name of the control
	 *  title         => Заголовок
	 *  colors        => массив цветов для градиента
	 *                   Пример: array("#000 0% 0.5", "#e70303 100% 1")
	 *  filldirection => Направление градиента(top, left)
	 *                   Пример: 90deg
	 *  value         => a value to show in the control
	 *  default       => a default value of the control if the "value" option is not specified
	 *
	 * @author Alex Kovalev <alex.kovalevv@gmail.com>
	 * @copyright (c) 2018, Webcraftic Ltd
	 *
	 * @package core
	 * @since 1.0.0
	 */

	// Exit if accessed directly
	if( !defined('ABSPATH') ) {
		exit;
	}

	if( !class_exists('Wbcr_FactoryForms436_GradientControl') ) {
		class Wbcr_FactoryForms436_GradientControl extends Wbcr_FactoryForms436_Control {

			public $type = 'gradient';

			/**
			 * Shows the html markup of the control.
			 *
			 * @since 1.0.0
			 * @return void
			 */
			public function html()
			{
				$name = $this->getNameOnForm();
				$value = esc_attr($this->getValue());

				if( !empty($value) ) {

					$values = json_decode(stripcslashes(htmlspecialchars_decode($value)));

					$points = '';

					foreach($values->color_points as $split_values) {
						$points .= $split_values . ',';
					}

					$points = rtrim($points, ',');

					$this->addHtmlData('points', $points);
					$this->addHtmlData('directions', $values->filldirection);
				} else {
					$this->addHtmlData('directions', 'top');
				}
				?>
				<script>
					if( !window.factory ) {
						window.factory = {};
					}
					if( !window.factory.res ) {
						window.factory.res = {};
					}
					factory.res.resVertical = '<?php _e( 'vertical', 'wbcr_factory_forms_436' ) ?>';
					factory.res.resHorizontal = '<?php _e( 'horizontal', 'wbcr_factory_forms_436' ) ?>';
				</script>
				<div <?php $this->attrs() ?>>
					<div class="factory-gradient-picker">
						<ul class="gradientPicker-pallets">
							<li class="factory-preset-gradient factory-primary-gradient" data-primary="#1bbc9d" data-secondary="#16a086"></li>
							<li class="factory-preset-gradient factory-primary-gradient" data-primary="#2fcc71" data-secondary="#27ae61"></li>
							<li class="factory-preset-gradient factory-primary-gradient" data-primary="#3598dc" data-secondary="#2a80b9"></li>
							<li class="factory-preset-gradient factory-primary-gradient" data-primary="#9c59b8" data-secondary="#8f44ad"></li>
							<li class="factory-preset-gradient factory-primary-gradient" data-primary="#34495e" data-secondary="#2d3e50"></li>
							<li class="factory-preset-gradient factory-primary-gradient" data-primary="#f1c40f" data-secondary="#f49c14"></li>
							<li class="factory-preset-gradient factory-primary-gradient" data-primary="#e84c3d" data-secondary="#c1392b"></li>
							<li class="factory-preset-gradient factory-primary-gradient" data-primary="#ecf0f1" data-secondary="#bec3c7"></li>
						</ul>
						<canvas class='gradientPicker-preview'></canvas>
						<div class='factory-points'></div>
						<div class='factory-color-picker-container'>
							<div class="factory-slider-container">
								<div class="factory-slider">
									<input type="text" class="factory-input-text factory-color-hex"/>

									<div class="factory-bar"></div>
									<div class="factory-visible-value">100%</div>
								</div>
							</div>
							<div class="factory-color-picker"></div>
						</div>
					</div>
					<input type="hidden" id="<?php echo $name; ?>" class="factory-result" name="<?php echo $name; ?>" value="<?php echo $value; ?>">
				</div>
			<?php
			}
		}
	}