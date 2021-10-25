<?php

namespace ACP\Search\Controller;

use AC;
use AC\Exception;
use AC\ListScreenRepository\Storage;
use AC\Request;
use AC\Response;
use AC\Type\ListScreenId;
use ACP\Controller;
use ACP\Search;
use ACP\Search\Searchable;
use DomainException;

class Comparison extends Controller {

	/**
	 * @var Storage
	 */
	private $storage;

	/**
	 * @var AC\ListScreen;
	 */
	protected $list_screen;

	public function __construct( Storage $storage, Request $request ) {
		parent::__construct( $request );

		$this->storage = $storage;

		$id = $request->get( 'layout' );

		if ( $id ) {
			$this->list_screen = $this->storage->find( new ListScreenId( $id ) );
		} else {
			$this->list_screen = AC\ListScreenTypes::instance()->get_list_screen_by_key( $request->get( 'list_screen' ) );
		}

		if ( ! $this->list_screen instanceof AC\ListScreen ) {
			throw Exception\RequestException::parameters_invalid();
		}
	}

	public function get_options_action() {
		$column = $this->list_screen->get_column_by_name(
			$this->request->filter( 'column', null, FILTER_SANITIZE_STRING )
		);

		$response = new Response\Json();

		if ( ! $column instanceof Searchable ) {
			$response->error();
		}

		$comparison = $column->search();

		switch ( true ) {
			case $comparison instanceof Search\Comparison\RemoteValues :
				$options = $comparison->get_values();
				$has_more = false;

				break;
			case $comparison instanceof Search\Comparison\SearchableValues :
				$options = $comparison->get_values(
					$this->request->filter( 'searchterm' ),
					$this->request->filter( 'page', null, FILTER_SANITIZE_NUMBER_INT )
				);
				$has_more = ! $options->is_last_page();

				break;
			default :
				throw new DomainException( 'Invalid Comparison type found.' );
		}

		$select = new AC\Helper\Select\Response( $options, $has_more );

		$response
			->set_parameters( $select() )
			->success();
	}

}