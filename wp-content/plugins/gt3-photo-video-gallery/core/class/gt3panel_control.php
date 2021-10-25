<?php

	/**
	 * Class gt3panel_control
	 *
	 * @property string             $title
	 * @property string             $name
	 * @property gt3attr[]          $attr
	 * @property gt3select|gt3input $option
	 */

	class gt3panel_control extends gt3classStd {
		protected static $fields_list = array(
			'title'  => '',
			'option' => null,
			'attr'   => array(),
			'name'   => '',
		);

		public function __construct( array $new_data = array() ) {
			$this->attr = new ArrayObject();
			parent::__construct( $new_data );
		}

		public function __toString() {
			$return = '';
			$return .= '\'<label';
			if ( count( $this->attr ) ) {
				foreach ( $this->attr as $attr ) {
					/* @var gt3attr $attr */
					$return .= ' ' . esc_attr($attr->name) . '="' . esc_attr($attr->value) . '"';
				}
			}
			$return .= '><span>' . esc_html($this->title) . '</span>';
			$return .= $this->option;
			$return .= '</label>\' +'.PHP_EOL;
			return $return;
		}
	}