<?php

namespace ACP\Search\Comparison\Post;

use AC\Helper\Select\Options;
use ACP\Helper\Select\OptionsFactory;
use ACP\Search\Comparison\Values;
use ACP\Search\Operators;

class Status extends PostField implements Values {

	private $post_type;

	public function __construct( string $post_type ) {
		parent::__construct( new Operators( [
			Operators::EQ,
			Operators::NEQ,
		] ) );

		$this->post_type = $post_type;
	}

	protected function get_field(): string {
		return 'post_status';
	}

	public function get_values(): Options {
		return ( new OptionsFactory\PostStatus() )->create( $this->post_type );
	}

}