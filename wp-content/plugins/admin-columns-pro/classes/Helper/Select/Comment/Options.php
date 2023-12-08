<?php
declare( strict_types=1 );

namespace ACP\Helper\Select\Comment;

use AC\Helper\Select;
use WP_Comment;

class Options extends Select\Options {

	/**
	 * @var WP_Comment[]
	 */
	private $comments;

	/**
	 * @var array
	 */
	private $labels = [];

	private $formatter;

	public function __construct( array $comments, LabelFormatter $formatter ) {
		$this->formatter = $formatter;
		array_map( [ $this, 'set_comment' ], $comments );
		$this->rename_duplicates();

		parent::__construct( $this->get_options() );
	}

	private function set_comment( WP_Comment $comment ): void {
		$this->comments[ $comment->comment_ID ] = $comment;
		$this->labels[ $comment->comment_ID ] = $this->formatter->format_label( $comment );
	}

	public function get_comment( int $id ): WP_Comment {
		return $this->comments[ $id ];
	}

	private function get_options(): array {
		return self::create_from_array( $this->labels )->get_copy();
	}

	protected function rename_duplicates(): void {
		$duplicates = array_diff_assoc( $this->labels, array_unique( $this->labels ) );

		foreach ( $this->labels as $id => $label ) {
			if ( in_array( $label, $duplicates, true ) ) {
				$this->labels[ $id ] = $this->formatter->format_label_unique( $this->get_comment( $id ) );
			}
		}
	}

}