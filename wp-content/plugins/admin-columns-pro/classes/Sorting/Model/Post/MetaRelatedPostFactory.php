<?php

namespace ACP\Sorting\Model\Post;

use AC\Settings\Column\Post;
use ACP;
use ACP\Sorting\AbstractModel;
use ACP\Sorting\Type\DataType;

/**
 * For sorting a post list table on a meta_key that holds a Post ID (single).
 * @since 5.2
 */
class MetaRelatedPostFactory {

	/**
	 * @param string $post_property The post property to sort on (e.g. title, ID)
	 * @param string $meta_key      The meta key that contains the post ID
	 *
	 * @return AbstractModel|null
	 */
	public function create( $post_property, $meta_key ) {

		switch ( $post_property ) {
			case Post::PROPERTY_TITLE :
				return new RelatedMeta\PostField( 'post_title', $meta_key );
			case Post::PROPERTY_ID :
				return new Meta( $meta_key, new DataType( DataType::NUMERIC ) );
		}

		return null;
	}

}