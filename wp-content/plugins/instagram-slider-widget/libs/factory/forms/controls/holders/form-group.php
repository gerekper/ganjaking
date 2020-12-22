<?php
	/**
	 * The file contains the class of Group Holder.
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

	if( !class_exists('Wbcr_FactoryForms436_FormGroupHolder') ) {
		/**
		 * Group Holder
		 *
		 * @since 1.0.0
		 */
		class Wbcr_FactoryForms436_FormGroupHolder extends Wbcr_FactoryForms436_Holder {

			/**
			 * A holder type.
			 *
			 * @since 1.0.0
			 * @var string
			 */
			public $type = 'form-group';

			/**
			 * Here we should render a beginning html of the tab.
			 *
			 * @since 1.0.0
			 * @return void
			 */
			public function beforeRendering()
			{

				$this->addCssClass('factory-form-group-' . $this->getName());
				$this->addHtmlAttr('id', 'factory-form-group-' . $this->getName());

				?>
				<fieldset <?php $this->attrs() ?>>
				<?php if( $this->hasTitle() ) { ?>
				<legend class='factory-legend'>
					<p class='factory-title'><?php $this->title() ?></p>
					<?php if( $this->hasHint() ) { ?>
						<p class='factory-hint'><?php echo $this->hint() ?></p>
					<?php } ?>
				</legend>
			<?php } ?>
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
				</fieldset>
			<?php
			}
		}
	}