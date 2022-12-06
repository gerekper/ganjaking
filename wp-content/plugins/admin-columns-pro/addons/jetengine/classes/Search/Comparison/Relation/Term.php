<?php

namespace ACA\JetEngine\Search\Comparison\Relation;

use ACA\JetEngine\Search\Comparison\Relation;
use ACP\Helper\Select;
use Jet_Engine\Relations\Relation as JetEngineRelation;

class Term extends Relation {

	/**
	 * @var string
	 */
	private $taxonomy;

	public function __construct( JetEngineRelation $relation, $is_parent, $taxonomy ) {
		parent::__construct( $relation, $is_parent );

		$this->taxonomy = $taxonomy;
	}

	public function get_values( $search, $page ) {
		return new Select\Paginated\Terms( $search, $page, [ $this->taxonomy ] );
	}

}