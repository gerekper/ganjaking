<?php

namespace ACA\MetaBox\Search\Comparison\Table;

use AC;
use ACP;

class Media extends TableStorage
	implements ACP\Search\Comparison\SearchableValues {

	use MultiMapTrait;

	/**
	 * @var
	 */
	private $mime_type;

	public function __construct( $operators, $table, $column, $mime_type = [], $value_type = null, ACP\Search\Labels $labels = null ) {
		$this->mime_type = $mime_type;

		parent::__construct( $operators, $table, $column, $value_type, $labels );
	}

	public function get_values( $s, $paged ) {
		$args = [
			's'         => $s,
			'paged'     => $paged,
			'post_type' => 'attachment',
			'orderby'   => 'date',
			'order'     => 'DESC',
		];

		if ( $this->mime_type ) {
			$args['post_mime_type'] = $this->mime_type;
		}

		$entities = new ACP\Helper\Select\Entities\Post( $args );

		return new AC\Helper\Select\Options\Paginated(
			$entities,
			new ACP\Helper\Select\Group\MimeType( new ACP\Helper\Select\Formatter\PostTitle( $entities ) )
		);
	}

	protected function get_subquery( $operator, ACP\Search\Value $value ) {
		$_operator = $this->map_operator( $operator );
		$_value = $this->map_value( $value, $operator );

		$where = ACP\Search\Helper\Sql\ComparisonFactory::create( $this->column, $_operator, $_value );

		return "SELECT ID FROM {$this->table} WHERE " . $where->prepare();
	}

}