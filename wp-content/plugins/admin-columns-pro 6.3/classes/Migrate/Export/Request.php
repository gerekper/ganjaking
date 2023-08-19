<?php

namespace ACP\Migrate\Export;

use AC;
use AC\Capabilities;
use AC\ListScreenRepository\Storage;
use AC\Type\ListScreenId;
use ACP\Search\SegmentCollection;
use ACP\Search\SegmentRepository;
use ACP\Search\Type\SegmentKey;

final class Request implements AC\Registerable {

	const ACTION = 'acp-export';
	const NONCE_NAME = 'acp_export_nonce';

	/**
	 * @var Storage
	 */
	private $storage;

	/**
	 * @var ResponseFactory
	 */
	private $response_factory;

    /**
     * @var SegmentRepository\Storage
     */
    private $segment_storage;

    public function __construct( Storage $storage, SegmentRepository\Storage $segment_storage, ResponseFactory $response_factory ) {
		$this->storage = $storage;
		$this->response_factory = $response_factory;
        $this->segment_storage = $segment_storage;
    }

	public function register(): void
    {
		add_action( 'admin_init', [ $this, 'handle_request' ] );
	}

	/**
	 * @return void
	 */
	public function handle_request() {
		$data = (object) filter_input_array( INPUT_POST, [
			'action'          => FILTER_SANITIZE_FULL_SPECIAL_CHARS,
			'list_screen_ids' => [
				'filter' => FILTER_SANITIZE_FULL_SPECIAL_CHARS,
				'flags'  => FILTER_REQUIRE_ARRAY,
			],
            'segments' => [
                'filter' => FILTER_SANITIZE_FULL_SPECIAL_CHARS,
                'flags'  => FILTER_REQUIRE_ARRAY,
            ],
			self::NONCE_NAME  => FILTER_DEFAULT,
		] );

		if ( ! isset( $data->action ) || $data->action !== self::ACTION ) {
			return;
		}

		if ( ! wp_verify_nonce( $data->{self::NONCE_NAME}, $data->action ) ) {
			return;
		}

		if ( ! current_user_can( Capabilities::MANAGE ) ) {
			return;
		}

		if ( empty( $data->list_screen_ids ) ) {
			return;
		}

		$response = $this->response_factory->create(
			$this->get_list_screens_from_request( $data->list_screen_ids ),
            $this->get_segments_from_request( $data->segments ?? [] )
		);

		$response->send();
	}

	/**
	 * @param array $ids
	 *
	 * @return AC\ListScreenCollection
	 */
	protected function get_list_screens_from_request( array $ids ) {
		$list_screens = new AC\ListScreenCollection();

		foreach ( $ids as $id ) {
			$list_screen = $this->storage->find( new ListScreenId( $id ) );

			if ( $list_screen ) {
				$list_screens->add( $list_screen );
			}
		}

		return $list_screens;
	}

    protected function get_segments_from_request( array $keys ): SegmentCollection {
        $segments = [];

        foreach ( $keys as $key ) {
            $segment = $this->segment_storage->find( new SegmentKey( $key ) );

            if ( $segment ) {
                $segments[] = $segment;
            }
        }

        return new SegmentCollection( $segments );
    }


}