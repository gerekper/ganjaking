<?php

class SWPQueryTest extends WP_UnitTestCase {
	protected static $factory;

	protected static $post_ids;

	protected static $page_ids;

	protected static $draft_ids;

	protected static $tax_post_ids;
	protected static $tax_terms;
	protected static $taxonomy = 'post_tag';

	protected static $meta_post_ids;
	protected static $meta_key   = 'searchwpmetakey';
	protected static $meta_value = 'this is a searchwpmetavalue test';

	protected static $date_post_ids;
	protected static $date_post_date;

	public static function wpSetUpBeforeClass( $factory ) {
		self::$factory = $factory;

		$post_ids[] = $factory->post->create( [
			'post_title' => 'This is a SWPQUERYTEST Post',
		] );

		$post_ids[] = $factory->post->create( [
			'post_title' => 'This is a SWPQUERYTEST Post',
		] );

		$page_ids[] = $factory->post->create( [
			'post_title' => 'This is a SWPQUERYTEST Page',
			'post_type'  => 'page',
		] );

		$draft_ids[] = $factory->post->create( [
				'post_title'  => 'This is a SWPQUERYTEST Post',
				'post_status' => 'draft',
		] );

		$tax_post_ids[] = $factory->post->create( [
			'post_title' => 'This is a SWPQUERYTESTTAX Taxonomy Post',
		] );

		$tax_terms[] = $factory->tag->create_and_get( [
			'name' => 'swpquerytesttag',
		] );

		self::$factory->tag->add_post_terms( $tax_post_ids[0], [ $tax_terms[0]->term_id ], self::$taxonomy );

		$meta_post_ids[] = $factory->post->create( [
			'post_title' => 'This is a SWPQUERYTESTMETA Post',
		] );

		add_post_meta( $meta_post_ids[0], self::$meta_key, self::$meta_value );

		self::$date_post_date = date( 'Y-m-d H:i:s', strtotime( '-6 months' ) );
		$date_post_ids[] = $factory->post->create( [
			'post_title' => 'This is a SWPQUERYTESTDATE Post',
			'post_date'  => self::$date_post_date,
		] );

		self::$post_ids      = $post_ids;
		self::$page_ids      = $page_ids;
		self::$draft_ids     = $draft_ids;
		self::$tax_post_ids  = $tax_post_ids;
		self::$tax_terms     = $tax_terms;
		self::$meta_post_ids = $meta_post_ids;
		self::$date_post_ids = $date_post_ids;

		$index = new \SearchWP\Index\Controller();

		// Create a Default Engine.
		$engine_model = json_decode( json_encode( new \SearchWP\Engine( 'default' ) ), true );
		\SearchWP\Settings::update_engines_config( [
			'default' => \SearchWP\Utils::normalize_engine_config( $engine_model ),
		] );

		foreach ( self::$post_ids as $post_id ) {
			$index->add( new \SearchWP\Entry( 'post.post', $post_id ) );
		}

		foreach ( self::$page_ids as $page_id ) {
			$index->add( new \SearchWP\Entry( 'post.page', $page_id ) );
		}

		foreach ( self::$tax_post_ids as $tax_post_id ) {
			$index->add( new \SearchWP\Entry( 'post.post', $tax_post_id ) );
		}

		foreach ( self::$meta_post_ids as $meta_post_id ) {
			$index->add( new \SearchWP\Entry( 'post.post', $meta_post_id ) );
		}

		foreach ( self::$date_post_ids as $date_post_id ) {
			$index->add( new \SearchWP\Entry( 'post.post', $date_post_id ) );
		}
	}

	public static function wpTearDownAfterClass() {
		$index = \SearchWP::$index;
		$index->reset();

		\SearchWP\Settings::update_engines_config( [] );
	}

	public function test_that_zero_results_are_returned_for_invalid_search() {
		$results = new SWP_Query( [
			'engine' => 'default',
			's'      => 'invalid_search_string',
			'fields' => 'ids',
		] );

		$this->assertEmpty( $results->posts );
	}

	public function test_that_zero_results_are_returned_for_invalid_engine() {
		$results = new SWP_Query( [
			'engine' => 'invalid',
			's'      => 'SWPQUERYTEST',
			'fields' => 'ids',
		] );

		$this->assertEmpty( $results->posts );
	}

	public function test_that_multiple_results_are_returned() {
		$results = new SWP_Query( [
			'engine'         => 'default',
			's'              => 'SWPQUERYTEST',
			'fields'         => 'ids',
			'posts_per_page' => -1,
		] );

		$this->assertEqualSets(
			array_merge( self::$post_ids, self::$page_ids ),
			$results->posts
		);
	}

	public function test_that_one_result_is_returned() {
		$results = new SWP_Query( [
			'engine'         => 'default',
			's'              => 'SWPQUERYTEST',
			'fields'         => 'ids',
			'posts_per_page' => 1,
		] );

		// That there was 1 result returned.
		$this->assertEquals( 1, count( $results->posts ) );
		$this->assertArrayHasKey( 0, $results->posts );

		// That the result is in our IDs.
		$this->assertContains(
			$results->posts[0],
			array_merge( self::$post_ids, self::$page_ids )
		);
	}

	public function test_that_one_result_is_returned_as_id() {
		$results = new SWP_Query( [
			'engine'         => 'default',
			's'              => 'SWPQUERYTEST',
			'fields'         => 'ids',
			'posts_per_page' => 1,
		] );

		// That there was 1 result returned.
		$this->assertEquals( 1, count( $results->posts ) );
		$this->assertArrayHasKey( 0, $results->posts );
		$this->assertIsNumeric( $results->posts[0] );

		// That the result is in our IDs.
		$this->assertContains(
			$results->posts[0],
			array_merge( self::$post_ids, self::$page_ids )
		);
	}

	public function test_that_one_result_is_returned_as_wp_post() {
		$results = new SWP_Query( [
			'engine'         => 'default',
			's'              => 'SWPQUERYTEST',
			'posts_per_page' => 1,
		] );

		// That there was 1 result returned.
		$this->assertEquals( 1, count( $results->posts ) );
		$this->assertArrayHasKey( 0, $results->posts );
		$this->assertContainsOnlyInstancesOf( 'WP_Post', $results->posts );
		$this->assertContains( $results->posts[0]->ID, array_merge( self::$post_ids, self::$page_ids ) );
	}

	public function test_that_page_arg_paginates() {
		$results = new SWP_Query( [
			'engine'         => 'default',
			's'              => 'SWPQUERYTEST',
			'posts_per_page' => 1,
			'page'           => 2,
		] );

		// That there was 1 result returned.
		$this->assertEquals( 1, count( $results->posts ) );
		$this->assertArrayHasKey( 0, $results->posts );
		$this->assertContainsOnlyInstancesOf( 'WP_Post', $results->posts );
		$this->assertContains( $results->posts[0]->ID, array_merge( self::$post_ids, self::$page_ids ) );
		$this->assertEquals( 3, $results->max_num_pages );

		$next_results = new SWP_Query( [
			'engine'         => 'default',
			's'              => 'SWPQUERYTEST',
			'posts_per_page' => 1,
			'page'           => 3,
		] );

		$this->assertEquals( 1, count( $next_results->posts ) );
		$this->assertArrayHasKey( 0, $next_results->posts );
		$this->assertContainsOnlyInstancesOf( 'WP_Post', $next_results->posts );
		$this->assertContains( $next_results->posts[0]->ID, array_merge( self::$post_ids, self::$page_ids ) );
		$this->assertTrue( $results->posts[0]->ID !== $next_results->posts[0]->ID);
	}

	public function test_that_post__in_arg_limits_to_result() {
		$results = new SWP_Query( [
			'engine'   => 'default',
			's'        => 'SWPQUERYTEST',
			'post__in' => [ self::$post_ids[1] ],
		] );

		$this->assertEquals( 1, count( $results->posts ) );
		$this->assertArrayHasKey( 0, $results->posts );
		$this->assertContainsOnlyInstancesOf( 'WP_Post', $results->posts );
		$this->assertContains( $results->posts[0]->ID, array_merge( self::$post_ids, self::$page_ids ) );
		$this->assertEquals( $results->posts[0]->ID, self::$post_ids[1] );
	}

	public function test_that_post__not_in_arg_excludes_result() {
		$results = new SWP_Query( [
			'engine'       => 'default',
			's'            => 'SWPQUERYTEST',
			'post__not_in' => [ self::$post_ids[1] ],
			'fields'       => 'ids',
		] );

		$this->assertContainsOnly( 'integer', $results->posts );
		$this->assertTrue( ! in_array( self::$post_ids[1], $results->posts ) );
	}

	public function test_that_post_type_arg_limits_to_post_type() {
		$results = new SWP_Query( [
			'engine'    => 'default',
			's'         => 'SWPQUERYTEST',
			'post_type' => [ 'post', 'page' ],
			'fields'    => 'ids',
		] );

		$this->assertContainsOnly( 'integer', $results->posts );

		$results_ids = $results->posts;
		$sources_ids = array_merge( self::$post_ids, self::$page_ids );

		sort( $results_ids );
		sort( $sources_ids );

		$this->assertEquals(
			$results_ids,
			$sources_ids
		);

		$results = new SWP_Query( [
			'engine'    => 'default',
			's'         => 'SWPQUERYTEST',
			'post_type' => [ 'post' ],
			'fields'    => 'ids',
		] );

		$this->assertContainsOnly( 'integer', $results->posts );

		$results_ids = $results->posts;
		$sources_ids = self::$post_ids;

		sort( $results_ids );
		sort( $sources_ids );

		$this->assertEquals(
			$results_ids,
			$sources_ids
		);

		$results = new SWP_Query( [
			'engine'    => 'default',
			's'         => 'SWPQUERYTEST',
			'post_type' => [ 'page' ],
			'fields'    => 'ids',
		] );

		$this->assertContainsOnly( 'integer', $results->posts );

		$results_ids = $results->posts;
		$sources_ids = self::$page_ids;

		sort( $results_ids );
		sort( $sources_ids );

		$this->assertEquals(
			$results_ids,
			$sources_ids
		);
	}

	public function test_that_post_status_arg_applies() {
		$index = new \SearchWP\Index\Controller();

		// Force a draft into the Index.
		foreach ( self::$draft_ids as $draft_id ) {
			$index->add( new \SearchWP\Entry( 'post.post', $draft_id ) );
		}

		$results = new SWP_Query( [
			'engine' => 'default',
			's'      => 'SWPQUERYTEST',
			'fields' => 'ids',
		] );

		$this->assertTrue( ! in_array( self::$draft_ids[0], $results->posts ) );

		// Publish the Draft.
		self::$factory->post->update_object( self::$draft_ids[0], [
			'post_status' => 'publish',
		] );

		$results = new SWP_Query( [
			'engine'   => 'default',
			's'        => 'SWPQUERYTEST',
			'fields'   => 'ids',
			'nopaging' => true,
		] );

		$this->assertTrue( in_array( self::$draft_ids[0], $results->posts ) );

		// Unpublish the Draft.
		self::$factory->post->update_object( self::$draft_ids[0], [
			'post_status' => 'draft',
		] );
	}

	public function test_that_tax_query_arg_applies() {
		$results = new SWP_Query( [
			's'         => 'SWPQUERYTESTTAX',
			'post_type' => 'post',
			'tax_query' => [ [
				'taxonomy' => 'post_tag',
				'terms'    => self::$tax_terms[0]->term_id,
			] ],
		] );

		$this->assertEquals( 1, count( $results->posts ), 'Limit failed' );
		$this->assertArrayHasKey( 0, $results->posts );
		$this->assertContainsOnlyInstancesOf( 'WP_Post', $results->posts );
		$this->assertEquals( $results->posts[0]->ID, self::$tax_post_ids[0] );

		$results = new SWP_Query( [
			's'         => 'SWPQUERYTESTTAX',
			'post_type' => 'post',
			'tax_query' => [ [
				'taxonomy' => 'post_tag',
				'terms'    => self::$tax_terms[0]->term_id,
				'operator' => 'NOT IN'
			] ],
		] );

		$this->assertEquals( 0, count( $results->posts ), 'Exclusion failed' );

		$results = new SWP_Query( [
			's'         => 'SWPQUERYTESTTAX',
			'post_type' => 'post',
			'tax_query' => [ [
				'taxonomy' => 'post_tag',
				'terms'    => self::$tax_terms[0]->term_id,
				'operator' => 'EXISTS'
			] ],
		] );

		$this->assertEquals( 1, count( $results->posts ), 'EXISTS' );

		$results = new SWP_Query( [
			's'         => 'SWPQUERYTESTTAX',
			'post_type' => 'post',
			'tax_query' => [ [
				'taxonomy' => 'post_tag',
				'terms'    => 99,
				'operator' => 'NOT EXISTS'
			] ],
		] );

		$this->assertEquals( 0, count( $results->posts ), 'NOT EXISTS' );

		$results = new SWP_Query( [
			's'         => 'SWPQUERYTESTTAX',
			'post_type' => 'post',
			'tax_query' => [ [
				'taxonomy' => 'post_tag',
				'terms'    => self::$tax_terms[0]->term_id,
				'operator' => 'EXISTS'
			], [
				'taxonomy' => 'post_tag',
				'terms'    => 99,
				'operator' => 'NOT EXISTS'
			] ],
		] );

		$this->assertEquals( 0, count( $results->posts ), 'EXISTS and NOT EXISTS' );

		$results = new SWP_Query( [
			's'         => 'SWPQUERYTESTTAX',
			'post_type' => 'post',
			'tax_query' => [
				'relation' => 'OR',
			[
				'taxonomy' => 'post_tag',
				'terms'    => self::$tax_terms[0]->term_id,
				'operator' => 'EXISTS'
			], [
				'taxonomy' => 'post_tag',
				'terms'    => 99,
				'operator' => 'NOT EXISTS'
			] ],
		] );

		$this->assertEquals( 1, count( $results->posts ), 'EXISTS or NOT EXISTS' );

		$results = new SWP_Query( [
			's'         => 'SWPQUERYTESTTAX',
			'post_type' => 'post',
			'tax_query' => [ [
				'taxonomy' => 'invalid_taxonomy',
				'terms'    => 1,
				'operator' => 'IN'
			] ],
		] );

		$this->assertEquals( 0, count( $results->posts ), 'Invalid Taxonomy' );

		$results = new SWP_Query( [
			's'         => 'SWPQUERYTESTTAX',
			'post_type' => 'post',
			'tax_query' => [ [
				'taxonomy' => 'post_tag',
				'terms'    => 99,
				'operator' => 'IN'
			] ],
		] );

		$this->assertEquals( 0, count( $results->posts ), 'Invalid Taxonomy Term' );

		$results = new SWP_Query( [
			's'         => 'SWPQUERYTESTTAX',
			'post_type' => 'post',
			'tax_query' => [ [
				'taxonomy' => 'post_tag',
				'terms'    => 99,
				'operator' => 'NOT IN'
			], [
				'taxonomy' => 'post_tag',
				'terms'    => self::$tax_terms[0]->term_id,
				'operator' => 'IN'
			]],
		] );

		$this->assertEquals( 1, count( $results->posts ), 'Term IN and NOT IN' );

		$results = new SWP_Query( [
			's'         => 'SWPQUERYTESTTAX',
			'post_type' => 'post',
			'tax_query' => [ [
				'taxonomy' => 'post_tag',
				'terms'    => 99,
				'operator' => 'NOT IN'
			], [
				'taxonomy' => 'post_tag',
				'terms'    => [ self::$tax_terms[0]->term_id, 99 ],
				'operator' => 'IN'
			]],
		] );

		$this->assertEquals( 1, count( $results->posts ), 'Term IN' );

		$results = new SWP_Query( [
			's'         => 'SWPQUERYTESTTAX',
			'post_type' => 'post',
			'tax_query' => [
				'relation' => 'OR',
			[
				'taxonomy' => 'category',
				'terms'    => 99,
				'operator' => 'NOT IN'
			], [
				'taxonomy' => 'post_tag',
				'terms'    => [ self::$tax_terms[0]->term_id, 99 ],
				'operator' => 'IN'
			]],
		] );

		$this->assertEquals( 1, count( $results->posts ), 'Term IN or NOT IN' );

		$results = new SWP_Query( [
			's'         => 'SWPQUERYTESTTAX',
			'post_type' => 'post',
			'tax_query' => [
				'relation' => 'AND',
			[
				'taxonomy' => 'category',
				'terms'    => 99,
				'operator' => 'IN'
			], [
				'taxonomy' => 'post_tag',
				'terms'    => [ self::$tax_terms[0]->term_id, 99 ],
				'operator' => 'IN'
			]],
		] );

		$this->assertEquals( 0, count( $results->posts ), 'Term IN or NOT IN' );

		$results = new SWP_Query( [
			's'         => 'SWPQUERYTESTTAX',
			'post_type' => 'post',
			'tax_query' => [
				'relation' => 'AND',
			[
				'taxonomy' => 'category',
				'terms'    => 99,
				'operator' => 'NOT IN'
			], [
				'taxonomy' => 'post_tag',
				'terms'    => self::$tax_terms[0]->term_id,
				'operator' => 'EXISTS'
			], [
				'taxonomy' => 'post_tag',
				'terms'    => [ self::$tax_terms[0]->term_id, 99 ],
				'operator' => 'IN'
			]],
		] );

		$this->assertEquals( 1, count( $results->posts ), 'AND NOT IN EXISTS IN' );
	}

	public function test_that_meta_query_arg_applies() {
		$results = new SWP_Query( [
			's'          => 'SWPQUERYTESTMETA',
			'post_type'  => 'post',
			'meta_query' => [ [
				'key'   => self::$meta_key,
				'value' => self::$meta_value,
			] ],
		] );

		$this->assertEquals( 1, count( $results->posts ) );
		$this->assertArrayHasKey( 0, $results->posts );
		$this->assertContainsOnlyInstancesOf( 'WP_Post', $results->posts );
		$this->assertEquals( $results->posts[0]->ID, self::$meta_post_ids[0] );

		$results = new SWP_Query( [
			's'          => 'SWPQUERYTESTMETA',
			'post_type'  => 'post',
			'meta_query' => [ [
				'key'     => self::$meta_key,
				'value'   => self::$meta_value,
				'compare' => '!=',
			] ],
		] );

		$this->assertEquals( 0, count( $results->posts ) );

		$results = new SWP_Query( [
			's'          => 'SWPQUERYTESTMETA',
			'post_type'  => 'post',
			'meta_query' => [ [
				'key'     => self::$meta_key,
				'value'   => self::$meta_value,
				'compare' => '!=',
			], [
				'key'     => self::$meta_key,
				'value'   => self::$meta_value,
				'compare' => '=',
			] ],
		] );

		$this->assertEquals( 0, count( $results->posts ) );

		$results = new SWP_Query( [
			's'          => 'SWPQUERYTESTMETA',
			'post_type'  => 'post',
			'meta_query' => [
				'relation' => 'OR',
			[
				'key'     => self::$meta_key,
				'value'   => self::$meta_value,
				'compare' => '!=',
			], [
				'key'     => self::$meta_key,
				'value'   => self::$meta_value,
				'compare' => '=',
			] ],
		] );

		$this->assertEquals( 1, count( $results->posts ) );

		$results = new SWP_Query( [
			's'          => 'SWPQUERYTESTMETA',
			'post_type'  => 'post',
			'meta_query' => [
				'relation' => 'AND',
			[
				'key'     => self::$meta_key,
				'value'   => self::$meta_value,
				'compare' => '=',
			], [
				'key'     => 'invalid_key',
				'compare' => 'NOT EXISTS',
			] ],
		] );

		$this->assertEquals( 1, count( $results->posts ) );
	}

	public function test_that_date_query_arg_applies() {
		$results = new SWP_Query( [
			's'          => 'SWPQUERYTESTDATE',
			'post_type'  => 'post',
			'date_query' => [ [
				'year'  => (int) date( 'Y', strtotime( self::$date_post_date ) ),
				'month' => (int) date( 'n', strtotime( self::$date_post_date ) ),
				'day'   => (int) date( 'j', strtotime( self::$date_post_date ) ),
			] ],
		] );

		$this->assertEquals( 1, count( $results->posts ) );
		$this->assertArrayHasKey( 0, $results->posts );
		$this->assertContainsOnlyInstancesOf( 'WP_Post', $results->posts );
		$this->assertEquals( $results->posts[0]->ID, self::$date_post_ids[0] );

		$results = new SWP_Query( [
			's'          => 'SWPQUERYTESTDATE',
			'post_type'  => 'post',
			'date_query' => [ [
				'year'  => (int) date( 'Y', strtotime( self::$date_post_date ) ),
				'month' => (int) date( 'n', strtotime( self::$date_post_date ) ),
				'day'   => (int) date( 'j', strtotime( self::$date_post_date ) ) - 1,
			] ],
		] );

		$this->assertEquals( 0, count( $results->posts ) );
	}

	public function test_that_tax_query_and_meta_query_and_date_query_args_apply() {
		$results = new SWP_Query( [
			's'          => 'SWPQUERYTESTDATE',
			'post_type'  => 'any',
			'tax_query' => [ [
				'taxonomy' => 'post_tag',
				'terms'    => self::$tax_terms[0]->term_id,
			] ],
			'meta_query' => [ [
				'key'   => self::$meta_key,
				'value' => self::$meta_value,
			] ],
			'date_query' => [ [
				'year'  => (int) date( 'Y', strtotime( self::$date_post_date ) ),
				'month' => (int) date( 'n', strtotime( self::$date_post_date ) ),
				'day'   => (int) date( 'j', strtotime( self::$date_post_date ) ),
			] ],
		] );

		$this->assertEquals( 0, count( $results->posts ) );
	}

	public function test_that_orderby_order_args_apply() {
		$results = new SWP_Query( [
			's'              => 'SWPQUERYTEST SWPQUERYTESTDATE', // Our date post will be the flag here.
			'post_type'      => 'post',
			'orderby'        => 'date',
			'order'          => 'ASC',
			'posts_per_page' => 1,
		] );

		$this->assertEquals( 1, count( $results->posts ) );
		$this->assertArrayHasKey( 0, $results->posts );
		$this->assertContainsOnlyInstancesOf( 'WP_Post', $results->posts );
		$this->assertEquals( $results->posts[0]->ID, self::$date_post_ids[0] );

		$results = new SWP_Query( [
			's'              => 'SWPQUERYTEST SWPQUERYTESTDATE', // Our date post will be the flag here.
			'post_type'      => 'post',
			'orderby'        => 'date',
			'order'          => 'DESC',
			'posts_per_page' => 1,
		] );

		$this->assertEquals( 1, count( $results->posts ) );
		$this->assertArrayHasKey( 0, $results->posts );
		$this->assertContainsOnlyInstancesOf( 'WP_Post', $results->posts );
		$this->assertNotEquals( $results->posts[0]->ID, self::$date_post_ids[0] );
	}
}
