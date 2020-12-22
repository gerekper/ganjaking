<?php
	/**
	 * The file contains a form layout based on Twitter Bootstrap 3
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

	if( !class_exists('Wbcr_FactoryForms436_Bootstrap3FormLayout') ) {
		/**
		 * A form layout based on Twitter Bootstrap 3
		 */
		class Wbcr_FactoryForms436_Bootstrap3FormLayout extends Wbcr_FactoryForms436_FormLayout {

			public $name = 'default';

			/**
			 * Creates a new instance of a bootstrap3 form layout.
			 *
			 * @since 1.0.0
			 * @param array $options A holder options.
			 * @param Wbcr_FactoryForms436_Form $form A parent form.
			 */
			public function __construct($options, $form)
			{
				parent::__construct($options, $form);

				$this->addCssClass('factory-bootstrap');
				if( isset($options['cssClass']) ) {
					$this->addCssClass($options['cssClass']);
				}
			}

			/**
			 * Renders a beginning of a form.
			 *
			 * @since 1.0.0
			 * @return void
			 */
			public function beforeRendering()
			{
				?>
				<div <?php $this->attrs() ?>>
				<div class="form-horizontal">
			<?php
			}

			/**
			 * Renders the end of a form.
			 *
			 * @since 1.0.0
			 * @return void
			 */
			public function afterRendering()
			{
				?>
				</div>
				</div>
			<?php
			}

			/**
			 * @param Wbcr_FactoryForms436_Control $control
			 */
			public function beforeControl($control)
			{
				if( $control->getType() == 'hidden' ) {
					return;
				}

				$theme_class = '';
				if( isset($control->options['theme']) ) {
					$theme_class = $control->options['theme'];
				}

				$control_name = $control->getOption('name');
				$control_name_class = $control_name
					? 'factory-control-' . $control_name
					: '';

				$col_left = $control->getLayoutOption('column-left', '2');
				$col_right = $control->getLayoutOption('column-right', '10');
				?>
				<div class="form-group form-group-<?php echo $control->getType() ?> <?php echo $theme_class ?> <?php echo $control_name_class ?>">
				<label for="<?php $control->printNameOnForm() ?>" class="col-sm-<?= $col_left ?> control-label">
					<?php if( $control->hasIcon() ) { ?>
						<img class="control-icon" src="<?php $control->icon() ?>"/>
					<?php } ?>
					<?php
						$hint_type = $control->getLayoutOption('hint-type', 'default');

						$control->title();
						if( $control->hasHint() ) {
							if( $hint_type == 'icon' ): ?>
								<?php $hint_icon_color = $control->getLayoutOption('hint-icon-color', 'green'); ?>
								<span class="factory-hint-icon factory-hint-icon-<?= $hint_icon_color ?>" data-toggle="factory-tooltip" data-placement="right" title="<?php $control->hint(true) ?>">
							<img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAkAAAAJCAQAAABKmM6bAAAAUUlEQVQIHU3BsQ1AQABA0X/komIrnQHYwyhqQ1hBo9KZRKL9CBfeAwy2ri42JA4mPQ9rJ6OVt0BisFM3Po7qbEliru7m/FkY+TN64ZVxEzh4ndrMN7+Z+jXCAAAAAElFTkSuQmCC" alt=""/>

						</span>
							<?php endif; ?>
							<?php if( $control->getLayoutOption('hint-position', 'bottom') == 'left' ): ?>
								<div class="help-block"><?php $control->hint() ?></div>
							<?php endif; ?>
						<?php } ?>
				</label>
				<div class="control-group col-sm-<?= $col_right ?>">
			<?php
			}

			/**
			 * @param Wbcr_FactoryForms436_Control $control
			 */
			public function afterControl($control)
			{
				if( $control->getType() == 'hidden' ) {
					return;
				}
				?>
				<?php if( $control->getOption('after', false) ) { ?>
				<span class="factory-after">
            <?php $control->option('after') ?>
        </span>
			<?php } ?>

				<?php
				$hint_type = $control->getLayoutOption('hint-type', 'default');
				if( $control->hasHint() && $hint_type == 'default' && $control->getLayoutOption('hint-position', 'bottom') == 'bottom' ):
					?>
					<div class="help-block">
						<?php $control->hint() ?>
					</div>
				<?php endif; ?>
				</div>
				</div>
			<?php
			}

			/**
			 * @param int $index
			 * @param int $total
			 */
			public function startRow($index, $total)
			{
				?>
				<div class='factory-row factory-row-<?php echo $index ?> factory-row-<?php echo $index ?>-of-<?php echo $total ?>'>
				<div class="form-group form-group">
			<?php
			}

			/**
			 * @param int $index
			 * @param int $total
			 */
			public function endRow($index, $total)
			{
				?>
				</div>
				</div>
			<?php
			}

			/**
			 * @param Wbcr_FactoryForms436_Control $control
			 * @param int $index
			 * @param int $total
			 */
			public function startColumn($control, $index, $total)
			{
				$index = $total == 2
					? 4
					: 3;
				$name = $control->getNameOnForm();
				?>
				<label for="<?php echo $name ?>" class="col-sm-2 control-label control-label-<?php echo $name ?>">
					<?php if( $control->hasIcon() ) { ?>
						<img class="control-icon" src="<?php $control->icon() ?>"/>
					<?php } ?>
					<?php $control->title() ?>
					<?php if( $control->hasHint() && $control->getLayoutOption('hint-position', 'bottom') == 'left' ) { ?>
						<div class="help-block"><?php $control->hint() ?></div>
					<?php } ?>
				</label>
				<div class="control-group control-group-<?php echo $name ?> col-sm-<?php echo $index ?>">
			<?php
			}

			/**
			 * @param Wbcr_FactoryForms436_Control $control
			 * @param int $index
			 * @param int $total
			 */
			public function endColumn($control, $index, $total)
			{
				?>
				<?php if( $control->getOption('after', false) ) { ?>
				<span class="factory-after">
                    <?php $control->option('after') ?>
                </span>
			<?php } ?>
				<?php if( $control->hasHint() && $control->getLayoutOption('hint-position', 'bottom') == 'bottom' ) { ?>
				<div class="help-block">
					<?php $control->hint() ?>
				</div>
			<?php } ?>
				</div>
			<?php
			}
		}
	}
