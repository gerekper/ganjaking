<?php

class SearchWP_Logic_And extends WP_UnitTestCase {
	protected static $factory;
	protected static $post_type = 'post' . SEARCHWP_SEPARATOR . 'post';
	protected static $post_ids;

	public static function wpSetUpBeforeClass( $factory ) {
		self::$factory = $factory;

		$post_ids[] = $factory->post->create( [
			'post_title' => 'lorem ipsum dolor sit amet',
		] );

		$post_ids[] = $factory->post->create( [
			'post_title' => 'lorem amet',
		] );

		self::$post_ids = $post_ids;

		// Create a Default Engine.
		$engine_model = json_decode( json_encode( new \SearchWP\Engine( 'default' ) ), true );
		\SearchWP\Settings::update_engines_config( [
			'default' => \SearchWP\Utils::normalize_engine_config( $engine_model ),
		] );

		foreach ( self::$post_ids as $post_id ) {
			\SearchWP::$index->add(
				new \SearchWP\Entry( self::$post_type, $post_id )
			);
		}
	}

	public static function wpTearDownAfterClass() {
		$index = \SearchWP::$index;
		$index->reset();

		\SearchWP\Settings::update_engines_config( [] );
	}

	/**
	 * Both posts have both words, so both should be returned.
	 */
	public function test_multiple_matches() {
		$results = new \SWP_Query( [
			'engine'         => 'default',
			's'              => 'lorem amet',
			'fields'         => 'ids',
		] );

		$this->assertEquals( 2, count( $results->posts ) );
		$this->assertArrayHasKey( 0, $results->posts );
		$this->assertArrayHasKey( 1, $results->posts );

		$this->assertContains( $results->posts[0], self::$post_ids );
		$this->assertContains( $results->posts[1], self::$post_ids );
	}

	/**
	 * Only one post has both words so it should be the only result.
	 */
	public function test_single_match() {
		$results = new \SWP_Query( [
			'engine'         => 'default',
			's'              => 'lorem ipsum',
			'fields'         => 'ids',
		] );

		$this->assertEquals( 1, count( $results->posts ) );
		$this->assertArrayHasKey( 0, $results->posts );

		$this->assertContains( $results->posts[0], self::$post_ids );
	}

	/**
	 * Forces AND logic but includes a missing token, should yield zero results.
	 */
	public function test_forced_failure() {
		add_filter( 'searchwp\query\logic\and\strict', '__return_true' );

		$results = new \SWP_Query( [
			'engine' => 'default',
			's'      => 'lorem ipsum notfound',
			'fields' => 'all',
		] );

		remove_filter( 'searchwp\query\logic\and\strict', '__return_true' );

		$this->assertTrue( empty( $results->posts ) );
	}
}
