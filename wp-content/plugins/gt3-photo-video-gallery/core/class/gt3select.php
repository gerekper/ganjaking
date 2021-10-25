<?php

	/**
	 * Class select
	 *
	 * @property gt3options[] $options
	 * @property gt3attr[]    $attr
	 * @property string       $name
	 */
	class gt3select extends gt3classStd {
		protected static $fields_list = array(
			'name'    => '',
			'options' => array(),
			'attr'    => array(),
		);


		public function __construct( array $new_data = array() ) {
			$this->options = new ArrayObject();
			$this->attr    = new ArrayObject();
			parent::__construct( $new_data );

		}

		public function __toString() {
			$return = '';
			$return .= '<select';
			if ( count( $this->attr ) ) {
				foreach ( $this->attr as $attr ) {
					/* @var gt3attr $attr */
					$return .= ' ' . esc_attr($attr->name) . '="' . esc_attr($attr->value) . '"';
				}
			}
			$return .= ' name="' . $this->name . '">' . PHP_EOL;
			if ( count( $this->options) ) {
				/* @var ArrayObject $this->options */
				$this->options->ksort();
				foreach ( $this->data['options'] as $option ) {
					/* @var gt3options $option */
					$return .= '<option';
					if ( count( $option->attr ) ) {
						foreach ( $option->attr as $attr ) {
							/* @var gt3attr $attr */
							$return .= ' ' . esc_attr($attr->name ). '="' . esc_attr($attr->value) . '"';
						}
					}
					$return .= ' value="' . $option->value . '"';
					if ( isset( $GLOBALS["gt3_photo_gallery"] )
					     && isset( $GLOBALS["gt3_photo_gallery"][ $this->name ] ) && ! empty( $GLOBALS["gt3_photo_gallery"][ $this->name ] )
					     && $GLOBALS["gt3_photo_gallery"][ $this->name ] == $option->value ) {
						$return .= ' selected="selected"';
					}

					$return .= '>' . esc_html($option->title ). '</option>' . PHP_EOL;
				}
			}

			$return .= '</select>' . PHP_EOL;
			return $return;
		}
	}
