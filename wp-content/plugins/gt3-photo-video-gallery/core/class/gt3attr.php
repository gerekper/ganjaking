<?php

	/**
	 * Class gt3attr
	 *
	 * @property string $name
	 * @property string $value
	 */
	class gt3attr extends gt3classStd {
		protected static $fields_list = array(
			'name'  => '',
			'value' => '',
		);

		public function __construct( $name = '', $value = '' ) {
			if ( $name != '' && $value != '' ) {
				$this->name  = $name;
				$this->value = $value;
			}
		}
	}