<?php

namespace WCML\Utilities;

use WPML\FP\Fns;

class Post {

	/**
	 * This allows to create new posts immediately in the
	 * correct language and the correct translation group (if any).
	 *
	 * @param array        $args
	 * @param string|null  $lang
	 * @param int|null     $trid
	 *
	 * @return int|\WP_Error
	 */
	public static function insert( array $args, $lang = null, $trid = null ) {
		$saveInLang = $saveWithTrid = null;

		if ( $lang ) {
			$saveInLang = Fns::always( $lang );
			add_filter( 'wpml_save_post_lang', $saveInLang );
		}

		if ( $trid ) {
			$saveWithTrid = Fns::always( $trid );
			add_filter( 'wpml_save_post_trid_value', $saveWithTrid );
		}

		$newPostId = wp_insert_post( $args );

		remove_filter( 'wpml_save_post_lang', $saveInLang );
		remove_filter( 'wpml_save_post_trid_value', $saveWithTrid );

		return $newPostId;
	}
}
