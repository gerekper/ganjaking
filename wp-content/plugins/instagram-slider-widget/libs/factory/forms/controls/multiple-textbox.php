<?php

	/**
	 * Control multiple textbox
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

	if( !class_exists('Wbcr_FactoryForms436_MultipleTextboxControl') ) {

		class Wbcr_FactoryForms436_MultipleTextboxControl extends Wbcr_FactoryForms436_Control {

			public $type = 'multiple-textbox';

			/**
			 * Preparing html attributes before rendering html of the control.
			 *
			 * @since 1.0.0
			 * @return void
			 */
			protected function beforeHtml()
			{

				$name_on_form = $this->getNameOnForm();

				if( $this->getOption('maxLength', false) ) {
					$this->addHtmlAttr('maxlength', intval($this->getOption('maxLength')));
				}

				if( $this->getOption('placeholder', false) ) {
					$this->addHtmlAttr('placeholder', $this->getOption('placeholder'));
				}

				$this->addCssClass('form-control');
				$this->addHtmlAttr('type', 'text');
				//$this->addHtmlAttr('id', $name_on_form);
				$this->addCssClass(str_replace('_', '-', $name_on_form));
				$this->addHtmlAttr('name', $name_on_form . '[]');
			}

			/**
			 * Shows the html markup of the control.
			 *
			 * @since 1.0.0
			 * @return void
			 */
			public function html()
			{

				$values = $this->getValue();

				if( !empty($values) ) {
					$values = explode('{%spr%}', $values);
				} else {
					$values = array();
				}

				?>
				<div class="factory-multiple-textbox-group">
					<div class="factory-mtextbox-items">
						<?php if( empty($values) ): ?>
							<div class="factory-mtextbox-item">
								<input <?php $this->attrs() ?>/>
							</div>
						<?php else: ?>
							<?php $counter = 0; ?>
							<?php foreach($values as $value): ?>
								<div class="factory-mtextbox-item">
									<input value="<?= esc_attr($value) ?>"<?php $this->attrs() ?>/>
									<?php if( $counter >= 1 ): ?>
										<button class="btn btn-default btn-small factory-mtextbox-remove-item">
											<i class="fa fa-times" aria-hidden="true"></i></button>
									<?php endif; ?>
								</div>
								<?php $counter++; ?>
							<?php endforeach; ?>
						<?php endif; ?>
					</div>
					<button class="btn btn-default btn-small factory-mtextbox-add-item">
						<i class="fa fa-plus" aria-hidden="true"></i> <?php _e('Add new', 'wbcr_factory_forms_436') ?>
					</button>
				</div>

			<?php
			}

			/**
			 * Returns a submit value of the control by a given name.
			 *
			 * @since 1.0.0
			 * @return mixed
			 */
			public function getSubmitValue($name, $subName)
			{
				$name_on_form = $this->getNameOnForm($name);

				$value = isset($_POST[$name_on_form])
					? $_POST[$name_on_form]
					: null;

				if( is_array($value) ) {
					$value = array_map('sanitize_text_field', $value);
					$value = implode('{%spr%}', $value);
				}

				$value = sanitize_text_field($value);

				return $value;
			}
		}
	}
