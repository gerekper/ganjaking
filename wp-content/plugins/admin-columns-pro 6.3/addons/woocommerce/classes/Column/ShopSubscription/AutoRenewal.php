<?php

namespace ACA\WC\Column\ShopSubscription;

use AC;
use ACA\WC\Search;
use ACP;

/**
 * @since 3.4
 */
class AutoRenewal extends AC\Column
	implements ACP\Search\Searchable {

	public function __construct() {
		$this->set_type( 'column-wc-subscription_auto_renewal' )
		     ->set_label( __( 'Auto Renewal', 'codepress-admin-columns' ) )
		     ->set_group( 'woocommerce' );
	}

	public function get_value( $id ) {
		return ac_helper()->icon->yes_or_no( ! wcs_get_subscription( $id )->is_manual() );
	}

	public function search() {
		return new Search\ShopSubscription\AutoRenewal();
	}

}