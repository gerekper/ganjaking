<?php

	/**
	 * Class gt3textarea
	 *
	 * @property string    $name
	 * @property gt3attr[] $attr
	 */
	class gt3textarea extends gt3classStd {
		protected static $fields_list = array(
			'name' => '',
			'attr' => array(),
		);

		public function __construct( array $new_data = array() ) {
			$this->attr = new ArrayObject();
			parent::__construct( $new_data );
		}

		public function __toString() {
			$return = '';
			$return .= '<textarea name="' . esc_attr($this->name ). '"';

			if ( count( $this->attr ) ) {
				foreach ( $this->attr as $attr ) {
					/* @var gt3attr $attr */
					$return .= ' ' . esc_attr($attr->name) . '="' . esc_attr($attr->value) . '"';
				}
			}
			$return .= '>';
			if ( isset( $GLOBALS["gt3_photo_gallery"] )
			     && isset( $GLOBALS["gt3_photo_gallery"][ $this->name ] ) ) {
				$return .= $GLOBALS["gt3_photo_gallery"][ $this->name ];
			}

			$return .= '</textarea>';
			return $return;
		}
	}
