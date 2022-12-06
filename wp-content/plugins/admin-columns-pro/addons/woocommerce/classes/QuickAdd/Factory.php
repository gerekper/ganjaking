<?php

namespace ACA\WC\QuickAdd;

use AC;
use ACA\WC\ListScreen;
use ACA\WC\QuickAdd\Create\Coupon;
use ACA\WC\QuickAdd\Create\Product;
use ACP\QuickAdd\Model\ModelFactory;

class Factory implements ModelFactory {

	public function create( AC\ListScreen $list_screen ) {
		switch ( true ) {
			case $list_screen instanceof ListScreen\Product:
				return new Product();
			case $list_screen instanceof ListScreen\ShopCoupon:
				return new Coupon();
		}

		return null;
	}

}