<?php

namespace ACA\WC\Column\ShopOrder;

use AC;
use ACP\ConditionalFormat\Formattable;
use ACP\ConditionalFormat\FormattableConfig;
use ACP\ConditionalFormat\Formatter\FilterHtmlFormatter;
use ACP\ConditionalFormat\Formatter\IntegerFormatter;

/**
 * @since 3.0
 */
class OrderNumber extends AC\Column implements Formattable {

	public function __construct() {
		$this->set_type( 'column-order_number' )
		     ->set_group( 'woocommerce' )
		     ->set_label( __( 'Order Number', 'codepress-admin-columns' ) );
	}

	public function conditional_format(): ?FormattableConfig {
		return new FormattableConfig( new FilterHtmlFormatter( new IntegerFormatter() ) );
	}

	public function get_value( $id ) {
		return ac_helper()->html->link( get_edit_post_link( $id ), $this->get_raw_value( $id ) );
	}

	public function get_raw_value( $id ) {
		return wc_get_order( $id )->get_order_number();
	}

}