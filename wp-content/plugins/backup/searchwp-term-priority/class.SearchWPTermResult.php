<?php

// exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class SearchWPTermResult  {
	public $term;
	public $name;
	public $slug;
	public $taxonomy;
	public $link;

	function __construct( $term, $taxonomy ) {
		$this->term = get_term_by( 'slug', $term, $taxonomy );
		$this->name = $this->term->name;
		$this->slug = $this->term->slug;
		$this->description  = $this->term->description;
		$this->link = get_term_link( $term, $taxonomy );
		$taxonomyObj = get_taxonomy( $taxonomy );
		$this->taxonomy = $taxonomyObj->labels->singular_name;
	}

}
