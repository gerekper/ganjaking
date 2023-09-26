<?php

namespace ACP\Search\RequestHandler;

use AC;
use AC\Exception;
use AC\ListScreenFactory;
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
	 * @var AC\ListScreen;
	 */
	protected $list_screen;

	public function __construct( Storage $storage, Request $request, ListScreenFactory $list_screen_factory ) {
		parent::__construct( $request );

		$id = $request->get( 'layout' );
		$list_key = (string) $request->get( 'list_screen', '' );

		if ( ListScreenId::is_valid_id( $id ) ) {
			$this->list_screen = $storage->find( new ListScreenId( $id ) );
		} else if ( $list_key && $list_screen_factory->can_create( $list_key ) ) {
			$this->list_screen = $list_screen_factory->create( $list_key );
		}

		if ( ! $this->list_screen instanceof AC\ListScreen ) {
			throw Exception\RequestException::parameters_invalid();
		}
	}

	public function get_options_action() {
		$response = new Response\Json();

		$column_name = (string) $this->request->filter( 'column', null, FILTER_SANITIZE_FULL_SPECIAL_CHARS );

		$column = $this->list_screen->get_column_by_name( $column_name );

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