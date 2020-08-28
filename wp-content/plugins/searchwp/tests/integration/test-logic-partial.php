<?php

class SearchWP_Logic_Partial extends WP_UnitTestCase {
	protected static $factory;
	protected static $post_ids;

	public static function wpSetUpBeforeClass( $factory ) {
		self::$factory = $factory;

		$post_ids[] = $factory->post->create( [
			'post_title' => 'Partial test fishing post',
		] );

		self::$post_ids = $post_ids;

		// Create a Default Engine.
		$engine_model = json_decode( json_encode( new \SearchWP\Engine( 'default' ) ), true );
		\SearchWP\Settings::update_engines_config( [
			'default' => \SearchWP\Utils::normalize_engine_config( $engine_model ),
		] );

		foreach ( self::$post_ids as $post_id ) {
			\SearchWP::$index->add( new \SearchWP\Entry( 'post' . SEARCHWP_SEPARATOR . 'post', $post_id ) );
		}
	}

	public static function wpTearDownAfterClass() {
		$index = \SearchWP::$index;
		$index->reset();

		\SearchWP\Settings::update_engines_config( [] );
	}

	public function test_that_partial_match_returns_correct_result() {
		add_filter( 'searchwp\query\partial_matches', '__return_true' );

		$results = new \SWP_Query( [
			'engine' => 'default',
			's'      => 'fish',
			'fields' => 'ids',
		] );

		remove_filter( 'searchwp\query\partial_matches', '__return_true' );

		// That there was 1 result returned.
		$this->assertEquals( 1, count( $results->posts ) );
		$this->assertArrayHasKey( 0, $results->posts );

		// That the result is in our IDs.
		$this->assertContains(
			$results->posts[0],
			self::$post_ids
		);
	}

	/**
	 * Test that a fuzzy match is not found when fuzzy matching is disabled.
	 */
	public function test_that_partial_mismatch_returns_no_result() {
		add_filter( 'searchwp\query\partial_matches', '__return_true' );
		add_filter( 'searchwp\query\partial_matches\fuzzy', '__return_false' );

		$results = new \SWP_Query( [
			'engine' => 'default',
			's'      => 'fishy',
		] );

		remove_filter( 'searchwp\query\partial_matches\fuzzy', '__return_false' );
		remove_filter( 'searchwp\query\partial_matches', '__return_true' );

		$this->assertTrue( empty( $results->posts ) );
	}
}
