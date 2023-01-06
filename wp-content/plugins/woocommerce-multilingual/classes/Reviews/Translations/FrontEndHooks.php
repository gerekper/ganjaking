<?php

namespace WCML\Reviews\Translations;

use IWPML_Action;
use WPML\FP\Obj;
use WPML\FP\Relation;

class FrontEndHooks implements IWPML_Action {

	const CONTEXT      = 'wcml-reviews';
	const COMMENT_TYPE = 'review';

	public function add_hooks() {
		/**
		 * Allows disabling product reviews translation.
		 *
		 * @param bool Whether translation of product reviews should be enabled
		 */
		if ( apply_filters( 'wcml_enable_product_review_translation', true ) ) {
			add_action( 'wp_insert_comment', [ $this, 'insertCommentAction' ], 10, 2 );
			add_action( 'woocommerce_review_before', [ $this, 'translateReview' ] );
		}
	}

	/**
	 * @param int         $commentId
	 * @param \WP_Comment $comment
	 */
	public function insertCommentAction( $commentId, $comment ) {
		self::registerReviewString( $comment );
	}

	/**
	 * @param \WP_Comment|\stdClass $comment
	 */
	public function translateReview( $comment ) {
		if ( self::isNonEmptyReview( $comment ) ) {
			$reviewTranslation = apply_filters(
				'wpml_translate_single_string',
				$comment->comment_content,
				self::CONTEXT,
				self::getReviewStringName( $comment )
			);

			if ( $reviewTranslation !== $comment->comment_content ) {
				$comment->is_translated   = true;
				$comment->comment_content = $reviewTranslation;
			}
		}
	}

	/**
	 * @param \WP_Comment|\stdClass $review
	 */
	public static function registerReviewString( $review ) {
		if ( self::isNonEmptyReview( $review ) ) {
			do_action(
				'wpml_register_single_string',
				self::CONTEXT,
				self::getReviewStringName( $review ),
				$review->comment_content,
				false,
				Obj::prop( 'language_code', $review ) ?: apply_filters( 'wpml_current_language', null )
			);
		}
	}

	/**
	 * @param \WP_Comment|\stdClass $comment
	 *
	 * @return bool
	 */
	private static function isNonEmptyReview( $comment ) {
		return Obj::prop( 'comment_content', $comment )
		       && Relation::propEq( 'comment_type', self::COMMENT_TYPE, $comment );
	}
	/**
	 * @param \WP_Comment|\stdClass $review
	 *
	 * @return string (e.g. "product/123/review/456")
	 */
	private static function getReviewStringName( $review ) {
		return 'product-' . Obj::prop( 'comment_post_ID', $review ) . '-review-' . Obj::prop( 'comment_ID', $review );
	}
}
