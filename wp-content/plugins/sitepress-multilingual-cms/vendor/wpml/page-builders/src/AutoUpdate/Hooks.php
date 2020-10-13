<?php

namespace WPML\PB\AutoUpdate;

use WPML\FP\Fns;
use WPML\FP\Logic;
use WPML\FP\Lst;
use WPML\FP\Maybe;
use WPML\FP\Relation;
use function WPML\FP\invoke;
use function WPML\FP\partialRight;
use function WPML\FP\pipe;

class Hooks implements \IWPML_Backend_Action, \IWPML_Frontend_Action, \IWPML_DIC_Action {

	const HASH_SEP = '-';

	/** @var \WPML_PB_Integration $pbIntegration */
	private $pbIntegration;

	/** @var \WPML_Translation_Element_Factory $elementFactory */
	private $elementFactory;

	/** @var array $savePostQueue */
	private $savePostQueue = [];

	public function __construct(
		\WPML_PB_Integration $pbIntegration,
		\WPML_Translation_Element_Factory $elementFactory
	) {
		$this->pbIntegration             = $pbIntegration;
		$this->elementFactory            = $elementFactory;
	}

	public function add_hooks() {
		if ( $this->isTmLoaded() ) {
			add_action( 'init', [ $this, 'init' ] );
			add_action( 'wpml_tm_save_post', [ $this, 'addToSavePostQueue' ], 10, 2 );
			add_filter( 'wpml_tm_post_md5_content', [ $this, 'getMd5ContentFromPackageStrings' ], 10, 2 );
			add_action( 'shutdown', [ $this, 'afterRegisterAllStringsInShutdown' ], \WPML\PB\Shutdown\Hooks::PRIORITY_REGISTER_STRINGS + 1 );
		}
	}

	public function isTmLoaded() {
		return defined( 'WPML_TM_VERSION' );
	}

	/**
	 * We remove the action callback because it will be
	 * called manually for each of the saved posts in the
	 * shutdown when the original strings are registered.
	 */
	public function init() {
		remove_action( 'wpml_tm_save_post', 'wpml_tm_save_post', 10 );
	}

	/**
	 * @param int      $postId
	 * @param \WP_Post $post
	 */
	public function addToSavePostQueue( $postId, $post ) {
		$this->savePostQueue[ $postId ] = $post;
	}

	/**
	 * @param string   $content
	 * @param \WP_Post $post
	 *
	 * @return string
	 */
	public function getMd5ContentFromPackageStrings( $content, $post ) {
		// $joinPackageStringHashes :: \WPML_Package → string
		$joinPackageStringHashes = pipe(
			invoke( 'get_package_strings' )->with( true ),
			Lst::pluck( 'value' ),
			Lst::sort( Relation::gt() ),
			Lst::join( self::HASH_SEP )
		);

		return Maybe::of( $post->ID )
			->map( [ self::class, 'getPackages' ] )
			->map( Fns::map( $joinPackageStringHashes ) )
			->filter()
			->map( Lst::join( self::HASH_SEP ) )
			->getOrElse( $content );
	}

	/**
	 * @param int $postId
	 *
	 * @return \WPML_Package[]
	 */
	public static function getPackages( $postId ) {
		return apply_filters( 'wpml_st_get_post_string_packages', [], $postId );
	}

	/**
	 * We need to call `wpml_tm_save_post` after string registration
	 * to make sure we build the content hash with the new strings.
	 */
	public function afterRegisterAllStringsInShutdown() {
		if ( $this->savePostQueue ) {
			do_action( 'wpml_cache_clear' );

			foreach ( $this->savePostQueue as $post ) {
				wpml_tm_save_post( $post->ID, $post );
				$this->resaveTranslations( $post->ID );
			}
		}
	}

	/**
	 * @param int $postId
	 */
	private function resaveTranslations( $postId ) {
		if ( ! self::getPackages( $postId ) ) {
			return;
		}

		// $ifOriginal :: \WPML_Post_Element → bool
		$ifOriginal = pipe( invoke( 'get_source_language_code' ), Logic::not() );

		// $ifCompleted :: \WPML_Post_Element → bool
		$ifCompleted = pipe( [ TranslationStatus::class, 'get' ], Relation::equals( ICL_TM_COMPLETE ) );

		// $resaveElement :: \WPML_Post_Element → null
		$resaveElement = \WPML\FP\Fns::unary( partialRight( [ $this->pbIntegration, 'resave_post_translation_in_shutdown' ], false ) );

		wpml_collect( $this->elementFactory->create_post( $postId )->get_translations() )
			->reject( $ifOriginal )
			->filter( $ifCompleted )
			->each( $resaveElement );
	}
}
