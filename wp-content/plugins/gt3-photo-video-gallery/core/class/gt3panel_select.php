<?php

	/**
	 * Class select
	 *
	 * @property ArrayObject[gt3options] $options
	 * @property gt3attr[] $attr
	 * @property string    $name
	 */
	class gt3panel_select extends gt3classStd {
		protected static $fields_list = array(
			'name'    => '',
			'options' => array(),
			'attr'    => array(),
		);


		public function __construct( array $new_data = array() ) {
			/* @var ArrayObject $this ->options */
			$this->options = new ArrayObject();
			$this->attr    = new ArrayObject();
			parent::__construct( $new_data );

		}

		public function __toString() {
			$return = '';
			$return .= '<select class="' . esc_attr($this->name) . '" name="' . esc_attr($this->name ). '" data-setting="' . esc_attr($this->name ). '"';
			if ( count( $this->attr ) ) {
				foreach ( $this->attr as $attr ) {
					/* @var gt3attr $attr */
					$return .= ' ' . esc_html($attr->name ). '="' . esc_attr($attr->value ). '"';
				}
			}
			$return .=  '<# if ( data.userSettings ) { #> data-user-setting="'.esc_attr($this->name).'"<# } #>';
			$return .= '>';
			if ( ! empty( $this->data['options'] ) ) {
				/* @var ArrayObject $this ->options */
				$this->options->ksort();
				foreach ( $this->data['options'] as $option ) {
					/* @var gt3options $option */
					$return .= '<option';
					if ( count( $option->attr ) ) {
						foreach ( $option->attr as $attr ) {
							/* @var gt3attr $attr */
							$return .= ' ' . esc_html($attr->name ). '="' . esc_attr($attr->value ). '"';
						}
					}
					$return .= ' value="' . esc_attr($option->value ). '"';
					if ( $option->value == 'default' ) {
						$return .= ' <# if ( ! wp.media.galleryDefaults.' . $this->name . ' || "default" == wp.media.galleryDefaults.' . $this->name . ' ) {#>selected="selected"<# } #>';
					} else {
						$return .= ' <# if ( "' . $option->value . '" == wp.media.galleryDefaults.' . $this->name . ' ) {\#>selected="selected"<# } #>';
					}

					$return .= '>' . esc_html($option->title ). '</option>';
				}
			}

			$return .= '</select>';
			return $return;
		}
	}