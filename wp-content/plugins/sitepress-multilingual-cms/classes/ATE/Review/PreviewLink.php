<?php

namespace WPML\TM\ATE\Review;

use WPML\API\Sanitize;
use WPML\Collect\Support\Traits\Macroable;
use WPML\FP\Fns;
use WPML\FP\Obj;
use WPML\FP\Str;
use WPML\TM\API\Jobs;
use function WPML\FP\curryN;

/**
 * Class PreviewLink
 *
 * @package WPML\TM\ATE\Review
 *
 * @method static callable|string getWithSpecifiedReturnUrl( ...$returnUrl, ...$translationPostId, ...$jobId ) : Curried:: int->int->string
 * @method static callable|string get( ...$translationPostId, ...$jobId ) : Curried:: int->int->string
 * @method static callable|string getWithLanguagesParam( ...$languages, ...$translationPostId, ...$jobId ) : Curried:: int->int->string
 * @method static callable|string getByJob( ...$job ) : Curried:: \stdClass->string
 * @method static Callable|string getNonceName( ...$translationPostId ) : Curried:: int->string
 */
class PreviewLink {
	use Macroable;

	public static function init() {
		self::macro( 'getWithSpecifiedReturnUrl', curryN( 3, function ( $returnUrl, $translationPostId, $jobId ) {

			/**
			 * Returns TRUE if post_type of post is among public post type and FALSE otherwise.
			 *
			 * @param $postId
			 *
			 * @return bool
			 */
			$isPublicPostType = function ( $postId ) {
				$publicPostTypes = get_post_types( [ 'public' => true ] );
				$postType        = get_post_type( $postId );

				return in_array( $postType, $publicPostTypes );
			};

			$args = [
				'preview_id'    => $translationPostId,
				'preview_nonce' => \wp_create_nonce( self::getNonceName( $translationPostId ) ),
				'preview'       => true,
				'jobId'         => $jobId,
				'returnUrl'     => urlencode( $returnUrl ),
			];

			/**
			 * @see https://onthegosystems.myjetbrains.com/youtrack/issue/wpmltm-4273
			 * @see https://onthegosystems.myjetbrains.com/youtrack/issue/wpmldev-1366/Translate-Everything-Incorrect-template-when-reviewing-a-translated-page
			 */
			if ( !$isPublicPostType( $translationPostId ) ) {
				// Add 'p' URL parameter only if post type isn't public
				$args['p'] = $translationPostId;
			}

			return \add_query_arg(
				NonPublicCPTPreview::addArgs( $args ),
				\get_permalink( $translationPostId )
			);
		} ) );

		self::macro( 'getWithLanguagesParam', curryN( 3, function ( $languages, $translationPostId, $jobId ) {
			$returnUrl = Sanitize::string( Obj::propOr( Obj::prop( 'REQUEST_URI', $_SERVER ), 'returnUrl', $_GET ) );
			$url = self::getWithSpecifiedReturnUrl( $returnUrl, $translationPostId, $jobId );

			if ( $languages ) {
				$url = \add_query_arg(['targetLanguages' => urlencode( join( ',', $languages ) ),], $url);
			}
			return $url;
		} ) );

		self::macro( 'get', curryN( 2, function ( $translationPostId, $jobId ) {
			$returnUrl = Sanitize::string( Obj::propOr( Obj::prop( 'REQUEST_URI', $_SERVER ), 'returnUrl', $_GET ) );

			return self::getWithSpecifiedReturnUrl( $returnUrl, $translationPostId, $jobId );
		} ) );

		self::macro( 'getByJob', curryN( 1, Fns::converge(
			self::get(),
			[
				Jobs::getTranslatedPostId(),
				Obj::prop( 'job_id' ),
			]
		) ) );

		self::macro( 'getNonceName', Str::concat( 'post_preview_' ) );
	}

}

PreviewLink::init();
