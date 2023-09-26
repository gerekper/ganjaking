<?php

namespace ACA\JetEngine\Search\Comparison\Relation;

use ACA\JetEngine\Search\Comparison\Relation;
use ACP\Helper\Select;
use Jet_Engine\Relations\Relation as JetEngineRelation;

class Post extends Relation {

	/**
	 * @var string
	 */
	private $post_type;

	public function __construct( JetEngineRelation $relation, $is_parent, $post_type ) {
		parent::__construct( $relation, $is_parent );

		$this->post_type = $post_type;
	}

	public function get_values( $search, $page ) {
		$args = [
			'post_type'     => $this->post_type,
			'search_fields' => [ 'post_title', 'ID' ],
		];

		return new Select\Paginated\Posts( $search, $page, $args );
	}

}