<?php

namespace ACP\Sorting\Model\Post;

use ACP\Sorting\AbstractModel;
use ACP\Sorting\FormatValue;
use ACP\Sorting\Model\SqlOrderByFactory;
use ACP\Sorting\Model\WarningAware;
use ACP\Sorting\Sorter;
use ACP\Sorting\Strategy\Post;
use ACP\Sorting\Type\DataType;

/**
 * @property Post $strategy
 */
class FeaturedImageSize extends AbstractModel implements WarningAware {

	/**
	 * @var string
	 */
	private $meta_key;

	public function __construct( $meta_key ) {
		parent::__construct( new DataType( DataType::NUMERIC ) );

		$this->meta_key = $meta_key;
	}

	public function get_sorting_vars() {
		add_filter( 'posts_clauses', [ $this, 'sorting_clauses_callback' ] );

		return [
			'suppress_filters' => false,
		];
	}

	public function sorting_clauses_callback( $clauses ) {
		remove_filter( 'posts_clauses', [ $this, __FUNCTION__ ] );

		global $wpdb;

		$clauses['orderby'] = SqlOrderByFactory::create_with_ids( "$wpdb->posts.ID", $this->get_sorted_ids(), $this->get_order() ) ?: $clauses['orderby'];

		return $clauses;
	}

	private function get_sorted_ids() {
		global $wpdb;

		$sql = $wpdb->prepare( "
			SELECT pp.ID AS id, pm2.meta_value AS file_path 
			FROM $wpdb->posts AS pp
			LEFT JOIN $wpdb->postmeta AS pm1 ON pm1.post_id = pp.ID 
			    AND pm1.meta_key = %s
			LEFT JOIN $wpdb->postmeta AS pm2 ON pm1.meta_value = pm2.post_id
				AND pm2.meta_key = '_wp_attached_file'
			WHERE pp.post_type = %s
				AND pm2.meta_value != ''
		",
			$this->meta_key,
			$this->strategy->get_post_type()
		);

		$status = $this->strategy->get_post_status();

		if ( $status ) {
			$sql .= sprintf( " AND pp.post_status IN ( '%s' )", implode( "','", array_map( 'esc_sql', $status ) ) );
		}

		$results = $wpdb->get_results( $sql );

		if ( ! $results ) {
			return [];
		}

		$values = [];

		foreach ( $results as $row ) {
			$values[ $row->id ] = ( new FormatValue\FileSize() )->format_value( $row->file_path );
		}

		return ( new Sorter() )->sort( $values, $this->data_type );
	}

}