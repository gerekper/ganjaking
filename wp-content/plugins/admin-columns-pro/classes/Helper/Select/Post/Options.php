<?php
declare( strict_types=1 );

namespace ACP\Helper\Select\Post;

use AC\Helper\Select;
use WP_Post;

class Options extends Select\Options {

	/**
	 * @var WP_Post[]
	 */
	private $posts;

	/**
	 * @var array
	 */
	private $labels = [];

	private $formatter;

	public function __construct( array $posts, LabelFormatter $formatter ) {
		$this->formatter = $formatter;
		array_map( [ $this, 'set_post' ], $posts );
		$this->rename_duplicates();

		parent::__construct( $this->get_options() );
	}

	private function set_post( WP_Post $post ): void {
		$this->posts[ $post->ID ] = $post;
		$this->labels[ $post->ID ] = $this->formatter->format_label( $post );
	}

	public function get_post( int $id ): WP_Post {
		return $this->posts[ $id ];
	}

	private function get_options(): array {
		return self::create_from_array( $this->labels )->get_copy();
	}

	protected function rename_duplicates(): void {
		$duplicates = array_diff_assoc( $this->labels, array_unique( $this->labels ) );

		foreach ( $this->labels as $id => $label ) {
			if ( in_array( $label, $duplicates, true ) ) {
				$this->labels[ $id ] = $this->formatter->format_label_unique( $this->get_post( $id ) );
			}
		}
	}

}