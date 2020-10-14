<?php

class SearchWP_Native extends WP_UnitTestCase {
	protected static $factory;
	protected static $post_type = 'post' . SEARCHWP_SEPARATOR . 'post';
	protected static $post_ids;

	public static function wpSetUpBeforeClass( $factory ) {
		self::$factory = $factory;

		$post_ids[] = $factory->post->create( [
			'post_title' => 'SearchWP Native Test Title',
		] );

		add_post_meta( $post_ids[0], '_test_meta_key_1', 'swpnative' );

		$post_ids[] = $factory->post->create( [
			'post_title' => 'lorem amet',
		] );

		add_post_meta( $post_ids[1], '_test_meta_key_2', 'swpnative' );

		self::$post_ids = $post_ids;

		\SearchWP\Settings::update_engines_config( [
			'default' => [
				'sources'  => [
					'post.post' => [
						'attributes' => [
							'title'   => 300,
							'meta'  => [
								'_test_meta_key_1' => 5,
							],
						],
						'rules'      => [],
						'options'    => [],
					],
				],
			],
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
	 * SearchWP results are returned for native search.
	 */
	public function test_native_search() {
		$this->go_to( home_url( '/?s=swpnative' ) );
		$results = $GLOBALS['wp_query']->posts;

		$this->assertArrayHasKey( 'searchwp', $GLOBALS['wp_query']->query_vars );
		$this->assertEquals( 'default', $GLOBALS['wp_query']->query_vars['searchwp'] );

		$this->assertEquals( 1, count( $results ) );
		$this->assertArrayHasKey( 0, $results );

		$this->assertContains( $results[0]->ID, self::$post_ids );
	}
}
