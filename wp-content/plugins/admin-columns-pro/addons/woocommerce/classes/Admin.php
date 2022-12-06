<?php

namespace ACA\WC;

use AC;
use AC\Registerable;
use ACA\WC\ListScreen\Product;
use ACA\WC\ListScreen\ShopOrder;
use ACA\WC\ListScreen\Subscriptions;
use ACA\WC\Settings\HideOnScreen\FilterOrderCustomer;
use ACA\WC\Settings\HideOnScreen\FilterProductCategory;
use ACA\WC\Settings\HideOnScreen\FilterProductStockStatus;
use ACA\WC\Settings\HideOnScreen\FilterProductType;
use ACA\WC\Settings\HideOnScreen\FilterSubscriptionCustomer;
use ACA\WC\Settings\HideOnScreen\FilterSubscriptionPayment;
use ACA\WC\Settings\HideOnScreen\FilterSubscriptionProduct;
use ACP\Settings\ListScreen\HideOnScreen\FilterPostDate;
use ACP\Settings\ListScreen\HideOnScreenCollection;

class Admin implements Registerable {

	/**
	 * @var AC\Asset\Location\Absolute
	 */
	private $location;

	public function __construct( AC\Asset\Location\Absolute $location ) {
		$this->location = $location;
	}

	public function register() {
		add_action( 'acp/admin/settings/hide_on_screen', [ $this, 'add_hide_on_screen' ], 10, 2 );
		add_action( 'ac/admin_scripts', [ $this, 'admin_scripts' ] );
	}

	public function admin_scripts() {
		$script = new Asset\Script\Admin( 'aca-wc-admin', $this->location );
		$script->enqueue();
	}

	public function add_hide_on_screen( HideOnScreenCollection $collection, AC\ListScreen $list_screen ) {

		switch ( true ) {
			case $list_screen instanceof ShopOrder :
				$collection->add( new FilterOrderCustomer(), 34 );

				break;
			case $list_screen instanceof Product :
				$collection->add( new FilterProductCategory(), 32 )
				           ->add( new FilterProductStockStatus(), 32 )
				           ->add( new FilterProductType(), 32 );

				$collection->remove( new FilterPostDate() );

				break;
			case $list_screen instanceof Subscriptions :
				$collection->add( new FilterSubscriptionProduct(), 34 )
				           ->add( new FilterSubscriptionPayment(), 34 )
				           ->add( new FilterSubscriptionCustomer(), 34 );

				break;
		}
	}

}