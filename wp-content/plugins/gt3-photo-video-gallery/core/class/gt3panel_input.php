<?php
	/**
	 * Class gt3panel_input
	 *
	 * @property string    $name
	 * @property string    $type
	 * @property gt3attr[] $attr
	 */
	class gt3panel_input extends gt3classStd {
		protected static $fields_list = array(
			'name' => '',
//			'value' => '',
			'type' => 'text',
			'attr' => array(),
		);

		public function __construct( array $new_data = array() ) {
			$this->attr = new ArrayObject();
			parent::__construct( $new_data );
		}

		public function __toString() {
			$return = '';
			$return .= '<input type="' . esc_attr($this->type ). '" name="' . esc_attr($this->name ). '"';
			if ( isset( $GLOBALS["gt3_photo_gallery"] )
			     && isset( $GLOBALS["gt3_photo_gallery"][ 'gt3pg_'.$this->name ] ) ) {
				$return .= ' value="' . esc_attr($GLOBALS["gt3_photo_gallery"][ 'gt3pg_'.$this->name ]) . '"';
			}
			if ( count( $this->attr ) ) {
				foreach ( $this->attr as $attr ) {
					/* @var gt3attr $attr */
					$return .= ' ' . esc_html($attr->name ). '="' . esc_attr($attr->value ). '"';
				}
			}
			$return .= ' />';
			return $return;
		}
	}
