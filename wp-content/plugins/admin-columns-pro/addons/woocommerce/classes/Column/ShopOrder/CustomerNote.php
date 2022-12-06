<?php

namespace ACA\WC\Column\ShopOrder;

use ACA\WC\Export;
use ACA\WC\Filtering;
use ACA\WC\Settings;
use ACA\WC\Sorting;
use ACP;

class CustomerNote extends ACP\Column\Post\Excerpt {

	public function __construct() {
		parent::__construct();

		$this->set_type( 'column-wc-order_customer_note' )
		     ->set_label( __( 'Customer Note', 'codepress-admin-columns' ) )
		     ->set_group( 'woocommerce' );
	}

	public function get_raw_value( $id ) {
		return ac_helper()->post->get_raw_field( 'post_excerpt', $id );
	}

	public function register_settings() {
		$this->add_setting( new Settings\Product\UseIcon( $this ) );
	}

	public function sorting() {
		return new ACP\Sorting\Model\Post\PostField( 'post_excerpt' );
	}

	public function filtering() {
		return new Filtering\ShopOrder\CustomerMessage( $this );
	}

	public function export() {
		return new Export\ShopOrder\CustomerMessage( $this );
	}

}