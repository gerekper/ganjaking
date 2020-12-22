<?php

	/**
	 * Checkbox Control
	 *
	 * Main options:
	 *  name            => a name of the control
	 *  value           => a value to show in the control
	 *  default         => a default value of the control if the "value" option is not specified
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

	if( !class_exists('Wbcr_FactoryForms436_CheckboxControl') ) {

		class Wbcr_FactoryForms436_CheckboxControl extends Wbcr_FactoryForms436_Control {

			public $type = 'checkbox';

			public function getSubmitValue($name, $sub_name)
			{
				$name_on_form = $this->getNameOnForm($name);

				return isset($_POST[$name_on_form]) && $_POST[$name_on_form] != 0
					? 1
					: 0;
			}

			/**
			 * Shows the html markup of the control.
			 *
			 * @since 1.0.0
			 * @return void
			 */
			public function html()
			{

				$events_on_data = $this->getOption('eventsOn', array());
				$events_off_data = $this->getOption('eventsOff', array());

				if( !empty($events_on_data) || !empty($events_off_data) ) {

					$events_on_string_data = json_encode($events_on_data);
					$events_off_string_data = json_encode($events_off_data);

					$name_on_form = $this->getNameOnForm();
					$value = $this->getValue();

					$print_styles = '';

					if( $value ) {
						$current_events_data = $events_on_data;
					} else {
						$current_events_data = $events_off_data;
					}

					foreach($current_events_data as $event_name => $selectors) {
						if( $event_name == 'hide' ) {
							$print_styles .= $selectors . '{display:none;}';
						} else if( $event_name == 'show' ) {
							$print_styles .= $selectors . '{display:block;}';
						}
					}

					echo '<style>' . $print_styles . '</style>';
					?>

					<script>
						// Onepress factory checkbox control events
						if( void 0 === window.__factory_checkbox_control_events_on_data ) {
							window.__factory_checkbox_control_events_on_data = {};
						}
						if( void 0 === window.__factory_checkbox_control_events_off_data ) {
							window.__factory_checkbox_control_events_off_data = {};
						}
						window.__factory_checkbox_control_events_on_data['<?php echo $name_on_form ?>'] = <?= $events_on_string_data ?>;
						window.__factory_checkbox_control_events_off_data['<?php echo $name_on_form ?>'] = <?= $events_off_string_data ?>;
					</script>
				<?php
				}

				if( 'buttons' == $this->getOption('way') ) {
					$this->buttonsHtml();
				} else {
					$this->defaultHtml();
				}
			}

			/**
			 * Shows the Buttons Checkbox.
			 *
			 * @since 1.0.0
			 * @return void
			 */
			protected function buttonsHtml()
			{
				$value = esc_attr($this->getValue());
				$name_on_form = $this->getNameOnForm();

				$this->addCssClass('factory-buttons-way');
				$this->addCssClass('btn-group');

				if( $this->getOption('tumbler', false) ) {
					$this->addCssClass('factory-tumbler');
				}

				$tumbler_function = $this->getOption('tumblerFunction', false);
				if( $tumbler_function ) {
					$this->addHtmlData('tumbler-function', $tumbler_function);
				}

				if( $this->getOption('tumblerHint', false) ) {
					$this->addCssClass('factory-has-tumbler-hint');

					$delay = $this->getOption('tumblerDelay', 3000);
					$this->addHtmlData('tumbler-delay', $delay);
				}


				?>
				<div <?php $this->attrs() ?>>
					<button type="button" class="btn btn-default btn-small btn-sm factory-on <?php if( $value ) {
						echo 'active';
					} ?>"><?php _e('On', 'wbcr_factory_forms_436') ?></button>
					<button type="button" class="btn btn-default btn-small btn-sm factory-off <?php if( !$value ) {
						echo 'active';
					} ?>" data-value="0"><?php _e('Off', 'wbcr_factory_forms_436') ?></button>
					<input type="checkbox" style="display: none" id="<?php echo $name_on_form ?>" class="factory-result" name="<?php echo $name_on_form ?>" value="<?= $value ?>" <?php if( $value ) {
						echo 'checked="checked"';
					} ?>" />
				</div>
				<?php if( $this->getOption('tumblerHint', false) ) { ?>
				<div class="factory-checkbox-tumbler-hint factory-tumbler-hint" style="display: none;">
					<div class="factory-tumbler-content">
						<?php echo $this->getOption('tumblerHint') ?>
					</div>
				</div>
			<?php } ?>
			<?php
			}

			/**
			 * Shows the standart checkbox.
			 *
			 * @since 1.0.0
			 * @return void
			 */
			protected function defaultHtml()
			{
				$value = esc_attr($this->getValue());
				$name_on_form = $this->getNameOnForm();

				$this->addHtmlAttr('type', 'checkbox');
				$this->addHtmlAttr('id', $name_on_form);
				$this->addHtmlAttr('name', $name_on_form);
				$this->addHtmlAttr('value', $value);

				if( $value ) {
					$this->addHtmlAttr('checked', 'checked');
				}
				$this->addCssClass('factory-default-way');

				?>
				<input <?php $this->attrs() ?>/>
			<?php
			}
		}
	}