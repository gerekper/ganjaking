<?php
/**
 * AV8_Cart_Action
 *
 */

class AV8_Cart_Action {

	public $link;
	public $label;
	public $color;

	public function __construct( $link, $label, $color = '' ) {
		$this->link  = $link;
		$this->label = $label;
		$this->color = $color;
	}

	public function display() {

		if ( '' !== $this->color ) {
			$color_style = sprintf( " style='color:#%s' ", $this->color );
		} else {
			$color_style = '';
		}

		$ret = " <a href='" . $this->link . "' " . $color_style . ' >' . __(
				$this->label,
				'woocommerce_cart_reports'
			) . '</a> ';

		return $ret;
	}
}


