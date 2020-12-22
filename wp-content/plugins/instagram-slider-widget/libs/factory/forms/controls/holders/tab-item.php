<?php
	/**
	 * The file contains the class of Tab Item Control Holder.
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

	if( !class_exists('Wbcr_FactoryForms436_TabItemHolder') ) {
		/**
		 * Tab Item Control Holder
		 *
		 * @since 1.0.0
		 */
		class Wbcr_FactoryForms436_TabItemHolder extends Wbcr_FactoryForms436_Holder {

			/**
			 * A holder type.
			 *
			 * @since 1.0.0
			 * @var string
			 */
			public $type = 'tab-item';

			/**
			 * Here we should render a beginning html of the tab.
			 *
			 * @since 1.0.0
			 * @return void
			 */
			public function beforeRendering()
			{

				$this->addCssClass('tab-' . $this->getName());
				$this->addHtmlAttr('id', $this->getName());

				$this->addCssClass('tab-pane');

				if( isset($this->options['isFirst']) && $this->options['isFirst'] ) {
					$this->addCssClass('active');
				}

				?>
				<div <?php $this->attrs() ?>>
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
			<?php
			}
		}
	}