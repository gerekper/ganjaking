<?php

namespace ElementPack\Wincher\Controller;

use DateTime;
use WP_REST_Request;
use WP_REST_Response;

/**
 * The Dashboard Controller.
 */
class DashboardController extends RestController {
	/**
	 * Gets the search engines.
	 *
	 * @param WP_REST_Request $request the request object
	 *
	 * @return WP_REST_Response the response object containing the search engines
	 */
	public function getSearchEngines(WP_REST_Request $request) {
		return $this->handleApiResponse($this->client->getSearchEngines());
	}

	/**
	 * Gets the dashboard data.
	 *
	 * @return array the dashboard data
	 */
	public function getDashboardData(WP_REST_Request $request) {
		$dateRange = $this->createDefaultDateRange(
			$request->get_param('start_date'),
			$request->get_param('end_date')
		);

		return $this->client->getDashboardData(
			$request->get_param('id'),
			$dateRange['start'],
			$dateRange['end']
		);
	}

	/**
	 * Gets the ranking history.
	 *
	 * @param WP_REST_Request $request the request object
	 *
	 * @return WP_REST_Response the response object containing the ranking history
	 */
	public function getRankingHistory(WP_REST_Request $request) {
		$dateRange = $this->createDefaultDateRange(
			$request->get_param('start_date'),
			$request->get_param('end_date')
		);

		return $this->handleApiResponse(
			$this->client->getRankingHistory(
				$request->get_param('id'),
				$request->get_param('keyword_ids'),
				$dateRange['start'],
				$dateRange['end']
			)
		);
	}

	/**
	 * Creates the keyword.
	 *
	 * @param WP_REST_Request $request the request object
	 *
	 * @return WP_REST_Response the response object containing the created keyword
	 */
	public function createKeyword(WP_REST_Request $request) {
		// $params = $request->get_json_params();
		$params = $request->get_params();

		return $this->handleApiResponse(
			$this->client->createKeyword($params['domain_id'], $params['keyword'])
		);
	}

	/**
	 * Deletes the passed keyword(s).
	 *
	 * @param WP_REST_Request $request the request object
	 *
	 * @return WP_REST_Response the response object containing the deletion response
	 */
	public function deleteKeywords(WP_REST_Request $request) {
		$params = $request->get_json_params();

		return $this->handleApiResponse(
			$this->client->deleteKeywords($params['domain_id'], $params['keyword_ids'])
		);
	}

	/**
	 * Gets the keywords.
	 *
	 * @param WP_REST_Request $request the request object
	 *
	 * @return WP_REST_Response the response object containing the keywords
	 */
	public function getKeywords(WP_REST_Request $request) {
		$dateRange = $this->createDefaultDateRange(
			$request->get_param('start_date'),
			$request->get_param('end_date')
		);

		// $test = $this->client->getKeywords(
		// 	$request->get_param( 'id' ),
		// 	$dateRange['start'],
		// 	$dateRange['end']
		// );

		// error_log( print_r( $test, true ) );

		return $this->handleApiResponse(
			$this->client->getKeywords(
				$request->get_param('id'),
				$dateRange['start'],
				$dateRange['end']
			)
		);
	}

	/**
	 * Handles the passed response.
	 *
	 * @param array $response the response array
	 *
	 * @return WP_REST_Response the handled API response object
	 */
	private function handleApiResponse($response) {
		return new WP_REST_Response($response['data'], $response['status']);
	}

	/**
	 * Creates a default date range.
	 *
	 * @param string|null $start the start date
	 * @param string|null $end   the end date
	 *
	 * @return array the default date range
	 */
	private function createDefaultDateRange($start = null, $end = null) {
		if (!$start) {
			$start = (new DateTime('-1 month'))->format(DateTime::ATOM);
		}

		if (!$end) {
			$end = (new DateTime())->format(DateTime::ATOM);
		}

		return [
			'start' => $start,
			'end'   => $end,
		];
	}

	/**
	 * Gets the competitors list.
	 * 
	 * @param WP_REST_Request $request the request object
	 *
	 * @return WP_REST_Response the response object containing the keywords
	 */
	public function competitorsList(WP_REST_Request $request) {
		$domain_id = $request->get_param('id');
		return $this->handleApiResponse(
			$this->client->getCompetitors($domain_id)
		);
	}

	/**
	 * Get website data.
	 * 
	 * @param WP_REST_Request $request the request object
	 * 
	 * @return WP_REST_Response the response object containing the website data
	 */
	public function websiteData(WP_REST_Request $request) {
		$domain_id = $request->get_param('id');
		return $this->handleApiResponse(
			$this->client->getWebsiteData($domain_id)
		);
	}

	/**
	 * Save domain.
	 * 
	 * @param WP_REST_Request $request the request object
	 * 
	 */
	public function saveDomain(WP_REST_Request $request) {
		$params = $request->get_params();
		return $this->client->saveDomain($params['domain_id']);
	}

	/**
	 * Get Competitors Ranking Summaries
	 * 
	 * @param WP_REST_Request $request the request object
	 * 
	 */
	public function competitorsRankingSummaries(WP_REST_Request $request) {
		$domain_id = $request->get_param('id');

		$dateRange = $this->createDefaultDateRange(
			$request->get_param('start_date'),
			$request->get_param('end_date')
		);

		return $this->handleApiResponse(
			$this->client->getCompetitorsRankingSummaries(
				$domain_id,
				$dateRange['start'],
				$dateRange['end']
			)
		);
	}
}
