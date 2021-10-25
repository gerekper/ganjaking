<?php

	/**
	 * Class gt3options
	 *
	 * @property string  $value
	 * @property string  $title
	 * @property gt3attr $attr
	 */
	class gt3options extends gt3classStd {
		protected static $fields_list = array(
			'value' => '',
			'title' => '',
			'attr'  => array(),
		);

		public function __construct( $title = array(), $value = null, $attr = null) {
			$this->attr = new ArrayObject();
			if (is_array($title)) {
				parent::__construct( $title );
			} else {
				$this->title = $title;
				$this->value = $value;
				if ($attr != null && is_array($attr) && count($attr)) {
					foreach ($attr as $val) {
						$this->attr[] = $val;
					}
				}
			}
		}
	}