<?php

namespace ACA\WC\Service;

use ACA\WC\Column;
use AC\ListScreen;
use AC\Registerable;

class Columns implements Registerable {

	public function register() {
		add_action( 'ac/column_types', [ $this, 'register_columns' ] );
	}

	public function register_columns( ListScreen $list_screen ) {
		$columns = [];

		if ( $list_screen instanceof ListScreen\User ) {
			$columns = array_merge( $columns, [
				Column\User\Address::class,
				Column\User\Country::class,
				Column\User\CouponsUsed::class,
				Column\User\CustomerSince::class,
				Column\User\FirstOrder::class,
				Column\User\LastOrder::class,
				Column\User\OrderCount::class,
				Column\User\Orders::class,
				Column\User\Products::class,
				Column\User\Ratings::class,
				Column\User\Reviews::class,
				Column\User\TotalSales::class,
			]);
		}

		if ( $list_screen instanceof ListScreen\Comment ) {
			$columns = array_merge( $columns, [
				Column\Comment\ProductReview::class,
				Column\Comment\Rating::class,
			]);
		}

		foreach ( $columns as $column ) {
			$list_screen->register_column_type( new $column );
		}
	}

}