<?php

namespace ACP\Column;

use AC;
use AC\MetaType;
use ACP\Editing;
use ACP\Filtering;
use ACP\Sorting;

/**
 * @since 4.0
 */
abstract class Meta extends AC\Column\Meta
	implements Sorting\Sortable, Editing\Editable, Filtering\Filterable {

	/**
	 * @return Sorting\AbstractModel
	 */
	public function sorting() {
		return $this->get_meta_key() && $this->get_meta_type()
			? ( new Sorting\Model\MetaFactory() )->create( $this->get_meta_type(), $this->get_meta_key() )
			: new Sorting\Model\Disabled();
	}

	public function editing() {
		return new Editing\Service\Basic(
			( new Editing\View\Text() )->set_clear_button( true ),
			new Editing\Storage\Meta( $this->get_meta_key(), new MetaType( $this->get_meta_type() ) )
		);
	}

	/**
	 * @return Filtering\Model\Meta|Filtering\Model\Disabled
	 */
	public function filtering() {
		if ( ! $this->get_meta_key() ) {
			return new Filtering\Model\Disabled( $this );
		}

		return new Filtering\Model\Meta( $this );
	}

}