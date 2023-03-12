<?php

namespace ACA\WC\Column\ShopCoupon;

use AC;
use ACA\WC\Editing;
use ACA\WC\Export;
use ACP;

/**
 * Class Column\ShopCoupon\CouponDescription
 * Custom Implementation of the description column
 */
class CouponDescription extends AC\Column
	implements ACP\Sorting\Sortable, ACP\Editing\Editable, ACP\Export\Exportable, ACP\Search\Searchable, ACP\ConditionalFormat\Formattable {

	use ACP\ConditionalFormat\ConditionalFormatTrait;

	public function __construct() {
		$this->set_type( 'column-wc-coupon_description' )
		     ->set_label( __( 'Description', 'codepress-admin-columns' ) )
		     ->set_group( 'woocommerce' );
	}

	public function get_raw_value( $post_id ) {
		return get_post_field( 'post_excerpt', $post_id, 'raw' );
	}

	public function register_settings() {
		$this->add_setting( new AC\Settings\Column\WordLimit( $this ) );
	}

	public function editing() {
		return new Editing\ShopCoupon\Description();
	}

	public function sorting() {
		return new ACP\Sorting\Model\Post\PostField( 'post_excerpt' );
	}

	public function search() {
		return new ACP\Search\Comparison\Post\Excerpt();
	}

	public function export() {
		return new Export\ShopCoupon\Description();
	}

}