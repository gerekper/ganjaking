<?php
	/**
	 * The file contains the base class for all control holder
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

	if( !class_exists('Wbcr_FactoryForms436_Holder') ) {

		/**
		 * The base class for control holders.
		 *
		 * @since 1.0.0
		 */
		abstract class Wbcr_FactoryForms436_Holder extends Wbcr_FactoryForms436_FormElement {

			/**
			 * Holder Elements.
			 *
			 * @since 1.0.0
			 * @var Wbcr_FactoryForms436_Control[]
			 */
			protected $elements = array();

			/**
			 * Is this element a control holder?
			 *
			 * @since 1.0.0
			 * @var bool
			 */
			public $is_holder = true;

			/**
			 * Creates a new instance of control holder.
			 *
			 * @since 1.0.0
			 * @param mixed[] $options A holder options.
			 * @param Wbcr_FactoryForms436_Form $form A parent form.
			 */
			public function __construct($options, $form)
			{
				parent::__construct($options, $form);
				$this->elements = $form->createElements($options['items']);
			}

			/**
			 * Returns holder elements.
			 *
			 * @since 1.0.0
			 * @return Wbcr_FactoryForms436_Control[].
			 */
			public function getElements()
			{
				return $this->elements;
			}

			/**
			 * Renders the form or a given control holder.
			 *
			 * @since 1.0.0
			 * @return void
			 */
			function render()
			{

				$this->beforeRendering();

				$is_first_item = true;

				foreach($this->elements as $element) {

					$element->setOption('isFirst', $is_first_item);

					if( $is_first_item ) {
						$is_first_item = false;
					}

					do_action('wbcr_factory_439_form_before_element_' . $element->getOption('name'));

					// if a current item is a control holder
					if( $element->is_holder ) {

						$this->form->layout->beforeHolder($element);
						$element->render();
						$this->form->layout->afterHolder($element);
						// if a current item is an input control
					} elseif( $element->is_control ) {
						$this->form->layout->beforeControl($element);
						$element->render();
						$this->form->layout->afterControl($element);
						// if a current item is a custom form element
					} elseif( $element->is_custom ) {

						$element->render();
						// otherwise, show the error
					} else {
						echo('[ERROR] Invalid item.');
					}

					do_action('wbcr_factory_form_after_element_' . $element->getOption('name'));
				}

				$this->afterRendering();
			}

			/**
			 * Rendering a beginning of a holder.
			 *
			 * @since 1.0.0
			 * @return void
			 */
			protected function beforeRendering()
			{
			}

			/**
			 * Rendering an end of a holder.
			 *
			 * @since 1.0.0
			 * @return void
			 */
			protected function afterRendering()
			{
			}

			/**
			 * Rendering some html before an inner holder.
			 *
			 * @since 1.0.0
			 * @return void
			 */
			protected function beforeInnerHolder()
			{
			}

			/**
			 * Rendering some html after an inner holder.
			 *
			 * @since 1.0.0
			 * @return void
			 */
			protected function afterInnerHolder()
			{
			}


			protected function beforeInnerElement()
			{
			}

			/**
			 * Rendering some html after an inner element.
			 *
			 * @since 1.0.0
			 * @return void
			 */
			protected function afterInnerElement()
			{
			}
		}
	}