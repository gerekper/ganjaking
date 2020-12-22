<?php
	/**
	 * The file contains the class of Columns Holder.
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

	if( !class_exists('Wbcr_FactoryForms436_ColumnsHolder') ) {
		/**
		 * Columns Holder
		 *
		 * @since 1.0.0
		 */
		class Wbcr_FactoryForms436_ColumnsHolder extends Wbcr_FactoryForms436_Holder {

			/**
			 * A holder type.
			 *
			 * @since 1.0.0
			 * @var string
			 */
			public $type = 'columns';

			public function __construct($options, $form)
			{
				$columns_items = array();

				// calculates the number of columns

				$this->columns_count = 0;

				foreach($options['items'] as $item) {
					$i = (!isset($item['column'])
							? 1
							: intval($item['column'])) - 1;
					$columns_items[$i][] = $item;

					if( $i > $this->columns_count ) {
						$this->columns_count = $i + 1;
					}
				}
				// calculates the number of rows

				$this->rows_count = 0;
				foreach($columns_items as $items) {
					$count = count($items);
					if( $count > $this->rows_count ) {
						$this->rows_count = $count;
					}
				}

				// creates elements

				parent::__construct($options, $form);

				// groups the created by columns

				$element_index = 0;
				$this->columns = array();

				foreach($columns_items as $column_index => $columnItems) {
					$count = count($columnItems);
					for($k = 0; $k < $count; $k++) {
						$this->columns[$column_index][] = $this->elements[$element_index];
						$element_index++;
					}
				}
			}


			public function render()
			{
				$this->beforeRendering();

				for($n = 0; $n < $this->rows_count; $n++) {

					$this->form->layout->startRow($n, $this->rows_count);

					for($i = 0; $i < $this->columns_count; $i++) {
						$control = $this->columns[$i][$n];
						$this->form->layout->startColumn($control, $i, $this->columns_count);
						$this->columns[$i][$n]->render();
						$this->form->layout->endColumn($control, $i, $this->columns_count);
					}

					$this->form->layout->endRow($n, $this->rows_count);
				}
			}
		}
	}