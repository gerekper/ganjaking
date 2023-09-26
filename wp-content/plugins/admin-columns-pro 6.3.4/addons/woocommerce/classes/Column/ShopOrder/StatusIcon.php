<?php

namespace ACA\WC\Column\ShopOrder;

use ACA\WC\Column;

/**
 * @since 3.0.3
 */
class StatusIcon extends Column\ShopOrder\Status {

	public function __construct() {
		parent::__construct();

		$this->set_type( 'column-order_status_icon' )
		     ->set_label( 'Status Icon' )
		     ->set_group( 'woocommerce' )
		     ->set_original( false );
	}

	public function get_value( $id ) {
		$label = $this->get_status_label( $this->get_raw_value( $id ) );

		return sprintf( '<mark %s class="%s" style="display: none;">%s</mark>', ac_helper()->html->get_tooltip_attr( $label ), $this->get_raw_value( $id ), $label );
	}

	public function register_settings() {
		$width = $this->get_setting( 'width' );

		$width->set_default( 35 );
		$width->set_default( 'px', 'width_unit' );

		$label = $this->get_setting( 'label' );
		if ( ! $label->get_value() ) {
			$label->set_default( '<span class="status_head">Status</span>' );
		}

	}

	private function get_status_label( $key ) {
		$key = 'wc-' . $key;
		$statuses = $this->get_order_status_options();

		if ( isset( $statuses[ $key ] ) ) {
			return $statuses[ $key ];
		}

		return $key;
	}

}