<?php

namespace ACA\ACF\Search\Comparison\Repeater;

use AC;
use ACA\ACF\Search\Comparison;
use ACP;
use ACP\Search\Comparison\SearchableValues;
use ACP\Search\Operators;

class Media extends Comparison\Repeater
	implements SearchableValues {

	/**
	 * @var string
	 */
	private $mime_type;

	public function __construct( $meta_type, $parent_key, $sub_key, $mime_type = null ) {
		$this->mime_type = $mime_type;

		$operators = new Operators( [
			Operators::EQ,
		] );

		parent::__construct( $meta_type, $parent_key, $sub_key, $operators );
	}

	protected function get_search_entities( $s, $paged ) {
		return new ACP\Helper\Select\Entities\Post( [
			's'              => $s,
			'paged'          => $paged,
			'post_type'      => 'attachment',
			'post_mime_type' => $this->mime_type,
			'orderby'        => 'date',
			'order'          => 'DESC',
		] );
	}

	public function get_values( $s, $paged ) {
		$entities = $this->get_search_entities( $s, $paged );

		return new AC\Helper\Select\Options\Paginated(
			$entities,
			new ACP\Helper\Select\Group\MimeType( new ACP\Helper\Select\Formatter\PostTitle( $entities ) )
		);
	}

}