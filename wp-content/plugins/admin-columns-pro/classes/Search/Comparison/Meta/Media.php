<?php

namespace ACP\Search\Comparison\Meta;

use AC;
use ACP\Helper\Select;
use ACP\Search\Comparison;
use ACP\Search\Comparison\Meta;
use ACP\Search\Operators;

class Media extends Meta
	implements Comparison\SearchableValues {

	/** @var string */
	protected $post_type;

	public function __construct( $meta_key, $meta_type, $post_type = false ) {
		$operators = new Operators( [
			Operators::EQ,
			Operators::IS_EMPTY,
			Operators::NOT_IS_EMPTY,
		] );

		$this->post_type = $post_type;

		parent::__construct( $operators, $meta_key, $meta_type );
	}

	public function get_values( $s, $paged ) {
		$entities = [];

		$ids = AC\Helper\Select\MetaValuesFactory::create( $this->meta_type, $this->meta_key, $this->post_type );

		if ( $ids ) {
			$entities = new Select\Entities\Post( [
				's'         => $s,
				'paged'     => $paged,
				'post_type' => 'attachment',
				'orderby'   => 'date',
				'order'     => 'DESC',
				'post__in'  => $ids,
			] );
		}

		return new AC\Helper\Select\Options\Paginated(
			$entities,
			new Select\Group\MimeType( new Select\Formatter\PostTitle( $entities ) )
		);
	}

}