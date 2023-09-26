<?php

namespace ACA\WC\Field\ShopSubscription;

use AC\MetaType;
use ACA\WC\Column;
use ACA\WC\Export;
use ACA\WC\Field;
use ACA\WC\Search;
use ACP;

/**
 * @property Column\ShopSubscription\SubscriptionDate $column
 */
abstract class SubscriptionDate extends Field
	implements ACP\Sorting\Sortable, ACP\Search\Searchable, ACP\Export\Exportable, ACP\Editing\Editable {

	public function get_value( $id ) {
		return get_post_meta( $id, $this->get_meta_key(), true );
	}

	abstract public function get_meta_key();

	public function sorting() {
		return new ACP\Sorting\Model\Post\Meta( $this->get_meta_key() );
	}

	public function search() {
		return new Search\Meta\Date\ISO( $this->get_meta_key(), MetaType::POST );
	}

	public function export() {
		return new Export\ShopSubscription\SubscriptionDate( $this->column );
	}

	public function editing() {
		return false;
	}

}