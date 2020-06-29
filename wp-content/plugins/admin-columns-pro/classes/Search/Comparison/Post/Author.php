<?php

namespace ACP\Search\Comparison\Post;

use AC;
use ACP\Helper\Select;
use ACP\Helper\Select\Formatter;
use ACP\Helper\Select\Group;
use ACP\Search\Comparison\SearchableValues;
use ACP\Search\Operators;

class Author extends PostField
	implements SearchableValues {

	/**
	 * @var string
	 */
	private $post_type;

	public function __construct( $post_type ) {
		$operators = new Operators( [
			Operators::EQ,
		] );

		$this->post_type = $post_type;

		parent::__construct( $operators );
	}

	/**
	 * @inheritdoc
	 */
	protected function get_field() {
		return 'post_author';
	}

	/**
	 * @param string $post_type
	 *
	 * @return int[]
	 */
	private function get_author_ids( $post_type ) {
		global $wpdb;

		return $wpdb->get_col( $wpdb->prepare( "SELECT DISTINCT post_author FROM {$wpdb->posts} WHERE post_type = %s;", $post_type ) );
	}

	public function get_values( $search, $paged ) {
		$entities = new Select\Entities\User( [
			'search'  => $search,
			'paged'   => $paged,
			'include' => $this->get_author_ids( $this->post_type ),
		] );

		return new AC\Helper\Select\Options\Paginated(
			$entities,
			new Group\UserRole(
				new Formatter\UserName( $entities )
			)
		);
	}

}