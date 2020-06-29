<?php

namespace ACP\Migrate\Export\Response;

use AC\ListScreenCollection;
use ACP\Migrate\Export\Response;
use ACP\Migrate\MessageTrait;
use ACP\Storage\ListScreen\Encoder;
use ACP\Storage\ListScreen\Serializer\JsonSerializer;

final class File implements Response {

	use MessageTrait;

	/**
	 * @var string
	 */
	private $type;

	/**
	 * @var ListScreenCollection
	 */
	private $list_screens;

	/**
	 * @var Encoder
	 */
	private $encoder;

	public function __construct( $type, ListScreenCollection $list_screens, Encoder $encoder ) {
		$this->type = $type;
		$this->list_screens = $list_screens;
		$this->encoder = $encoder;
	}

	/**
	 * @return void
	 */
	public function send() {
		if ( ! $this->list_screens->count() ) {
			$this->set_message( __( 'No screens selected for export.', 'codepress-admin-columns' ) );

			return;
		}

		$output = [];

		foreach ( $this->list_screens as $list_screen ) {
			$output[] = $this->encoder->encode( $list_screen );
		}

		$headers = [
			'content-disposition' => 'attachment; filename="' . $this->get_file_name() . '"',
			'content-type'        => 'application/json',
		];

		foreach ( $headers as $header => $value ) {
			header( $header . ': ' . $value );
		}

		echo ( new JsonSerializer() )->serialize( $output );

		exit;
	}

	private function get_file_name() {
		return sprintf(
			'%s-%s.%s',
			'admin-columns-export',
			date( 'Y-m-d-Hi' ),
			$this->type
		);
	}

}