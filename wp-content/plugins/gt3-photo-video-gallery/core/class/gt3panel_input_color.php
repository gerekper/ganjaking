<?php

	/**
	 * Class gt3panel_input_color
	 * @property string $name1
	 * @property string $name2
	 * @property string $data2
	 */
	class gt3panel_input_color extends gt3classStd {
		protected static $fields_list = array(
			'name1' => '',
			'name2' => '',
			'data2' => '',
		);

		public function __toString() {
			$return = '';
			$value = isset( $GLOBALS["gt3_photo_gallery"]['gt3pg_'.$this->name2] ) ? 'value="' . esc_attr($GLOBALS["gt3_photo_gallery"]['gt3pg_'.$this->name2]).'"' : '';
			$return .= '<input name="'.esc_attr($this->name1).'" type="text" />';
			$return .= '<input type="text" class="hidden" name="'.esc_attr($this->name2).'" data-setting="'.esc_attr($this->data2).'" '.$value.' />';
			return $return;
		}
	}