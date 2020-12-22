<?php
	/**
	 * The file contains the class of Tab Control Holder.
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

	if( !class_exists('Wbcr_FactoryForms436_ControlGroupHolder') ) {

		/**
		 * Tab Control Holder
		 *
		 * @since 1.0.0
		 */
		class Wbcr_FactoryForms436_ControlGroupHolder extends Wbcr_FactoryForms436_ControlHolder {

			/**
			 * A holder type.
			 *
			 * @since 1.0.0
			 * @var string
			 */
			public $type = 'control-group';


			/**
			 * Here we should render a beginning html of the tab.
			 *
			 * @since 1.0.0
			 * @return void
			 */
			public function beforeRendering()
			{
				$name = $this->getNameOnForm();
				$value = $this->getValue();

				$title = $this->getOption('title', null);

				?>
				<div <?php $this->attrs() ?>>
				<input type="hidden" name="<?php echo $name ?>" id="<?php echo $name ?>" class="factory-ui-control-group" value="<?php echo $value ?>"/>

				<?php if( $title ) { ?>
				<strong class="factory-header"><?php echo $title; ?></strong>
			<?php } ?>

				<ul class="factory-control-group-nav">
					<?php
						foreach($this->elements as $element):

							if( $element->options['type'] !== 'control-group-item' ) {
								continue;
							}

							$builder = new Wbcr_FactoryForms436_HtmlAttributeBuilder();

							$builder->addCssClass('factory-control-group-nav-label');
							$builder->addCssClass('factory-control-group-nav-label-' . $element->getOption('name'));
							$builder->addHtmlData('control-id', 'factory-control-group-item-' . $element->getOption('name'));
							$builder->addHtmlData('control-name', $element->getOption('name'));

							if( $value == $element->getOption('name') ) {
								$builder->addCssClass('current');
							}

							?>
							<li <?php $builder->printAttrs(); ?>><?php $element->title(); ?></li>
						<?php endforeach; ?>
				</ul>
				<div class="factory-control-group-body">
			<?php
			}

			/**
			 * Here we should render an end html of the tab.
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
		}
	}