<?php
/**
 * FUE API Campaigns Class
 *
 * Handles requests to the /campaigns endpoint
 *
 * @author      75nineteen
 * @since       4.1
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class FUE_API_Campaigns extends FUE_API_Resource {

	/** @var string $base the route base */
	protected $base = '/campaigns';

	/**
	 * Register the routes for this class
	 *
	 * GET /campaigns
	 *
	 * @since 4.1
	 *
	 * @param array $routes
	 *
	 * @return array
	 */
	public function register_routes( $routes ) {

		# GET /campaigns
		$routes[ $this->base ] = array(
			array( array( $this, 'get_campaigns' ), FUE_API_Server::READABLE )
		);

		# GET /campaigns/<id>
		$routes[ $this->base . '/(?P<slug>\w+)' ] = array(
			array( array( $this, 'get_campaign_emails' ), FUE_API_SERVER::READABLE )
		);

		return $routes;
	}

	/**
	 * List all available campaigns
	 *
	 * @since 4.1
	 * @param array $filter
	 * @param int $page
	 * @return array
	 */
	public function get_campaigns( $filter = array(), $page = 1) {
		$campaigns  = get_terms( 'follow_up_email_campaign', array('hide_empty' => false) );
		$result     = array();

		foreach ( $campaigns as $campaign ) {
			$result[] = $this->get_campaign( $campaign );
		}

		// set the pagination data
		// not really used but just to be uniform
		$query = array(
			'page'        => 1,
			'single'      => count( $result ) == 1,
			'total'       => count( $result ),
			'total_pages' => 1
		);
		$this->server->add_pagination_headers( $query );

		return apply_filters( 'fue_get_campaigns', $result, $campaigns, $filter );
	}

	/**
	 * List emails under the given campaign
	 *
	 * @since 4.1
	 * @param string $slug Campaign slug
	 * @param array $filter
	 * @param int $page
	 * @return array
	 */
	public function get_campaign_emails( $slug, $filter = array(), $page = 1 ) {
		$filter['campaign'] = $slug;

		return Follow_Up_Emails::instance()->api->FUE_API_Emails->get_emails( $filter, $page );
	}

	/**
	 * Return a well-formed campaign structure for outputting
	 *
	 * @since 4.1
	 * @param stdClass $data
	 * @return array
	 */
	private function get_campaign( $data ) {
		return apply_filters( 'fue_api_get_campaign', array(
			'campaign' => array(
				'id'            => $data->slug,
				'name'          => $data->name,
				'description'   => $data->description,
				'count'         => $data->count
			)
		), $data );
	}

}