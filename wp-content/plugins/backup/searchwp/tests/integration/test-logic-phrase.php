<?php

class SearchWP_Logic_Phrase extends WP_UnitTestCase {
	protected static $factory;
	protected static $post_ids;

	public static function wpSetUpBeforeClass( $factory ) {
		self::$factory = $factory;

		$post_ids[] = $factory->post->create( [
			'post_title' => 'Phrase match post',
		] );

		$post_ids[] = $factory->post->create( [
			'post_title' => 'Phrase no match post flag',
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

	/**
	 * There is one post with a phrase match and another post with a word that disrupts a phrase match.
	 */
	public function test_that_phrase_match_returns_correct_result() {
		add_filter( 'searchwp\query\logic\phrase', '__return_true' );

		$results = new \SWP_Query( [
			'engine' => 'default',
			's'      => '"phrase match"',
			'fields' => 'ids',
		] );

		remove_filter( 'searchwp\query\logic\phrase', '__return_true' );

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
	 * Test that a phrase match with additional missing tokens still yields result
	 */
	public function test_phrase_match_with_extra_missing_token() {
		add_filter( 'searchwp\query\logic\phrase', '__return_true' );

		$results = new \SWP_Query( [
			'engine' => 'default',
			's'      => '"phrase match" notoken',
			'fields' => 'ids',
		] );

		remove_filter( 'searchwp\query\logic\phrase', '__return_true' );

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
	 * Test that a phrase mismatch with a matching token yields a result.
	 */
	public function test_phrase_mismatch_with_extra_matching_token() {
		add_filter( 'searchwp\query\logic\phrase', '__return_true' );

		$results = new \SWP_Query( [
			'engine' => 'default',
			's'      => '"alpha beta" flag',
			'fields' => 'ids',
		] );

		remove_filter( 'searchwp\query\logic\phrase', '__return_true' );

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
	 * Test that a phrase mismatch falls back to finding multiple results.
	 */
	public function test_phrase_mismatch_falls_back_to_token_search() {
		add_filter( 'searchwp\query\logic\phrase', '__return_true' );

		$results = new \SWP_Query( [
			'engine' => 'default',
			's'      => '"phrase broken match"',
			'fields' => 'ids',
		] );

		remove_filter( 'searchwp\query\logic\phrase', '__return_true' );

		$this->assertEquals( 2, count( $results->posts ) );

		// That the result is in our IDs.
		$this->assertContains(
			$results->posts[0],
			self::$post_ids
		);
	}

	/**
	 * Test that a phrase mismatch fails when set to strict.
	 */
	public function test_phrase_mismatch_strict_fails() {
		add_filter( 'searchwp\query\logic\phrase', '__return_true' );
		add_filter( 'searchwp\query\logic\phrase\strict', '__return_true' );

		$results = new \SWP_Query( [
			'engine' => 'default',
			's'      => '"phrase broken match"',
			'fields' => 'ids',
		] );

		remove_filter( 'searchwp\query\logic\phrase', '__return_true' );
		remove_filter( 'searchwp\query\logic\phrase\strict', '__return_true' );

		$this->assertTrue( empty( $results->posts ) );
	}
}
