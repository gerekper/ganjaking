<?php

namespace ACP\Migrate\Export\Response;

use AC\ListScreenCollection;
use ACP\Migrate\Export\Response;
use ACP\Storage\ListScreen\Encoder;
use ACP\Storage\ListScreen\Serializer\PhpSerializer;

final class Screen implements Response {

	/**
	 * @var ListScreenCollection
	 */
	private $list_screens;

	/**
	 * @var Encoder
	 */
	private $encoder;

	public function __construct( ListScreenCollection $list_screens, Encoder $encoder ) {
		$this->list_screens = $list_screens;
		$this->encoder = $encoder;
	}

	/**
	 * @return void
	 */
	public function send() {
		if ( ! $this->list_screens->count() ) {
			wp_send_json_error( [
				'message' => __( 'No screens selected for export.', 'codepress-admin-columns' ),
			] );
		}

		$serializer = new PhpSerializer();
		$output = '';

		foreach ( $this->list_screens as $list_screen ) {
			$output .= $serializer->serialize( $this->encoder->encode( $list_screen ) );
		}

		$output = sprintf( "add_action( 'ac/ready', function() { \n  ac_load_columns( %s );\n});", $output );

		wp_send_json_success( [
			'export' => $output,
		] );
	}

}