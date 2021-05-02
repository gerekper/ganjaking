<?php

namespace ACP\Sorting\Model\Post;

use ACP\Sorting\AbstractModel;
use ACP\Sorting\FormatValue;
use ACP\Sorting\Sorter;
use ACP\Sorting\Strategy\Post;
use ACP\Sorting\Type\DataType;

/**
 * @property Post $strategy
 */
class FeaturedImageSize extends AbstractModel {

	/**
	 * @var string
	 */
	private $meta_key;

	/**
	 * @var FormatValue\FileSize
	 */
	private $formatter;

	public function __construct( $meta_key ) {
		parent::__construct();

		$this->meta_key = $meta_key;
		$this->formatter = new FormatValue\FileSize();
	}

	public function get_sorting_vars() {
		return [
			'ids' => ( new Sorter() )->sort( $this->get_featured_image_sizes(), $this->get_order(), new DataType( DataType::NUMERIC ), $this->show_empty ),
		];
	}

	private function get_featured_image_sizes() {
		global $wpdb;

		$join_type = $this->show_empty
			? 'LEFT'
			: 'INNER';

		$where = ! $this->show_empty
			? "AND pm1.meta_value <>''"
			: '';

		$sql = $wpdb->prepare( "
			SELECT pp.ID AS id, pm2.meta_value AS file_path 
			FROM {$wpdb->posts} AS pp
			{$join_type} JOIN {$wpdb->postmeta} AS pm1 ON pm1.post_id = pp.ID 
			    AND pm1.meta_key = %s {$where}
			{$join_type} JOIN {$wpdb->postmeta} AS pm2 ON pm1.meta_value = pm2.post_id
				AND pm2.meta_key = '_wp_attached_file'
			WHERE pp.post_type = %s
		", $this->meta_key, $this->strategy->get_post_type() );

		$status = $this->strategy->get_post_status();

		if ( $status ) {
			$sql .= sprintf( " AND pp.post_status IN ( '%s' )", implode( "','", array_map( 'esc_sql', $status ) ) );
		}

		$results = $wpdb->get_results( $sql );

		if ( ! $results ) {
			return [];
		}

		$values = [];
		foreach ( $results as $object ) {
			$values[ $object->id ] = $this->formatter->format_value( $object->file_path );
		}

		return $values;
	}

}