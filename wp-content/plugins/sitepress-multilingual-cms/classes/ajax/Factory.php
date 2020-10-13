<?php

namespace WPML\Ajax;

use WPML\Collect\Support\Collection;
use WPML\FP\Either;
use WPML\FP\Json;
use WPML\FP\Logic;
use WPML\FP\Maybe;
use WPML\FP\System\System;
use WPML\LIB\WP\Hooks;
use WPML\LIB\WP\Nonce;
use function WPML\FP\invoke;
use function WPML\FP\System\getFilterFor as filter;
use function WPML\FP\System\getValidatorFor as validate;

class Factory implements \IWPML_AJAX_Action {

	public function add_hooks() {
		// :: Collection -> Collection
		$filterEndPoint = filter( 'endpoint' )->using( 'wp_unslash' );

		// :: Collection -> Collection
		$decodeData = filter( 'data' )->using( Json::toCollection() )->defaultTo( 'wpml_collect' );

		// :: Collection -> Either::Left( string ) | Either::Right( Collection )
		$validateData = validate( 'data' )->using( Logic::isNotNull() )->error( 'Invalid json data' );

		// $handleRequest :: Collection -> Either::Left(string) | Either::Right(mixed)
		$handleRequest = function ( Collection $postData ) {
			return Maybe::of( $postData->get( 'endpoint' ) )
			            ->map( 'WPML\Container\make' )
			            ->map( invoke( 'run' )->with( $postData->get( 'data' ) ) )
			            ->getOrElse( Either::left( 'End point not found' ) );
		};

		Hooks::onAction( 'wp_ajax_wpml_action' )
			->then( System::getPostData() ) //Either::right(Collection)
			->then( $filterEndPoint )
			->then( Nonce::verifyEndPoint() )
			->then( $decodeData )
			->then( $validateData )
			->then( $handleRequest )
			->then( 'wp_send_json_success' )
			->onError( 'wp_send_json_error' );
	}
}
