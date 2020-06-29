<?php

namespace ACP\Export\Model\Post;

use AC;
use ACP\Export\Model;

/**
 * Last modified author column exportability model
 * @since 4.1
 */
class LastModifiedAuthor extends Model\Value {

	/**
	 * @param AC\Column\Post\LastModifiedAuthor $column
	 */
	public function __construct( AC\Column\Post\LastModifiedAuthor $column ) {
		parent::__construct( $column );
	}

}