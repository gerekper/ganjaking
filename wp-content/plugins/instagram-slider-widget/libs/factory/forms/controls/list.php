<?php

	/**
	 * Multiselect List Control
	 *
	 * Main options:
	 *  name            => a name of the control
	 *  value           => a value to show in the control
	 *  default         => a default value of the control if the "value" option is not specified
	 *  items           => a callback to return items or an array of items to select
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

	if( !class_exists('Wbcr_FactoryForms436_ListControl') ) {

		class Wbcr_FactoryForms436_ListControl extends Wbcr_FactoryForms436_Control {

			public $type = 'list';

			/**
			 * Returns a set of available items for the list.
			 *
			 * @since 1.0.0
			 * @return mixed[]
			 */
			private function getItems()
			{

				$data = $this->getOption('data', array());

				// if the data options is a valid callback for an object method
				if( (is_array($data) && count($data) == 2 && is_object($data[0])) || is_string($data) ) {

					return call_user_func($data);
				}

				// if the data options is an array of values
				return $data;
			}

			/**
			 * Returns true, if the data should be loaded via ajax.
			 *
			 * @since 1.0.0
			 * @return bool
			 */
			protected function isAjax()
			{

				$data = $this->getOption('data', array());

				return is_array($data) && isset($data['ajax']);
			}

			/**
			 * Shows the html markup of the control.
			 *
			 * @since 1.0.0
			 * @return void
			 */
			public function html()
			{

				$way = $this->getOption('way', 'default');
				$this->addHtmlData('way', $way);

				if( $this->isAjax() ) {

					$data = $this->getOption('data', array());
					$ajax_id = 'factory-list-' . rand(1000000, 9999999);

					$value = $this->getValue(null, true);

					if( empty($value) || empty($value[0]) ) {
						$value = array();
					}

					?>
					<div class="factory-ajax-loader <?php echo $ajax_id . '-loader'; ?>"></div>
					<script>
						window['<?php echo $ajax_id ?>'] = {
							'loader': '.<?php echo $ajax_id . '-loader' ?>',
							'url': '<?php echo $data['url'] ?>',
							'data': <?php echo json_encode( $data['data'] ) ?>,
							'selected': <?php echo json_encode( $value ) ?>,
							'emptyList': '<?php echo $this->getOption('empty', __('The list is empty.', 'wbcr_factory_forms_436') ) ?>'
						};
					</script>
					<?php

					$this->addHtmlData('ajax', true);
					$this->addHtmlData('ajax-data-id', $ajax_id);
					$this->addCssClass('factory-hidden');
				}

				if( 'checklist' == $way ) {
					$this->checklistHtml();
				} else {
					$this->defaultHtml();
				}
			}

			/**
			 * Shows the Buttons Dropdown.
			 *
			 * @since 1.0.0
			 * @return void
			 */
			protected function checklistHtml()
			{
				$items = $this->getItems();

				$value = explode(',', $this->getValue());
				if( empty($value) || empty($value[0]) ) {
					$value = array();
				}

				$name_on_form = $this->getNameOnForm();

				$this->addCssClass('factory-checklist-way');
				$this->addHtmlData('name', $name_on_form);

				$errors_callback = $this->getOption('errors');
				$errors = !empty($errors_callback)
					? call_user_func($errors_callback)
					: array();

				$is_empty = $this->isAjax() || empty($items);
				$emptyList = $this->getOption('empty', __('The list is empty.', 'wbcr_factory_forms_436'));

				if( $is_empty ) {
					$this->addCssClass('factory-empty');
				}

				?>
				<ul <?php $this->attrs() ?>>
					<?php if( $is_empty ) { ?>
						<li><?php echo $emptyList ?></li>
					<?php } else { ?>
						<?php foreach($items as $item) { ?>
							<li>
								<label for="factory-checklist-<?php echo $name_on_form ?>-<?php echo $item[0] ?>" class="<?php if( !empty($errors[$item[0]]) ) {
									echo 'factory-has-error';
								} ?>">
									<?php if( !empty($errors[$item[0]]) ) { ?>
										<span class="factory-error">
                            <i class="fa fa-exclamation-triangle"></i>
                            <div class='factory-error-text'><?php echo $errors[$item[0]] ?></div>
                        </span>
									<?php } else { ?>
										<span>
                            <input
	                            type="checkbox"
	                            name="<?php echo $name_on_form ?>[]"
	                            value="<?php echo $item[0] ?>"
	                            id="factory-checklist-<?php echo $name_on_form ?>-<?php echo $item[0] ?>"
	                            <?php if( in_array($item[0], $value) ) {
		                            echo 'checked="checked"';
	                            } ?> />
                        </span>
									<?php } ?>

									<span><?php echo $item[1] ?></span>
								</label>
							</li>
						<?php } ?>
					<?php } ?>
				</ul>
			<?php
			}

			/**
			 * Shows the standart dropdown.
			 *
			 * @since 1.3.1
			 * @return void
			 */
			protected function defaultHtml()
			{

				$items = $this->getItems();
				$value = $this->getValue();

				$name_on_form = $this->getNameOnForm();

				$this->addHtmlAttr('id', $name_on_form);
				$this->addHtmlAttr('name', $name_on_form);
				$this->addCssClass('form-control');

				?>
        <select multiple="multiple" <?php $this->attrs() ?>/>
        <?php foreach($items as $item) {
				if( count($item) == 3 ) {
					?>
					<optgroup label="<?php echo $item[1] ?>">
						<?php foreach($item[2] as $subitem) { ?>
							<?php $selected = ($subitem[0] == $value)
								? 'selected="selected"'
								: ''; ?>
							<option value='<?php echo $subitem[0] ?>' <?php echo $selected ?>>
								<?php echo $subitem[1] ?>
							</option>
						<?php } ?>
					</optgroup>
				<?php
				} else {
					$selected = ($item[0] == $value)
						? 'selected="selected"'
						: '';
					?>
					<option value='<?php echo $item[0] ?>' <?php echo $selected ?>>
						<?php echo $item[1] ?>
					</option>
				<?php } ?>
			<?php } ?>
        </select>
        <?php
			}
		}
	}