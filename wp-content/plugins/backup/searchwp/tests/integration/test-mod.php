<?php

class SearchWP_Mod extends WP_UnitTestCase {
	protected static $factory;
	protected static $post_type = 'post' . SEARCHWP_SEPARATOR . 'post';
	protected static $post_ids;
	protected static $taxonomy = 'post_tag';
	protected static $tax_terms;

	public static function wpSetUpBeforeClass( $factory ) {
		self::$factory = $factory;

		$post_ids[] = $factory->post->create( [
			'post_title' => 'modtest modtestlorem modtestipsum modtestdatemeta',
			'meta_input' => [ 'custom_date_field' => current_time( 'Ymd' ) ],
		] );

		$tax_terms[] = $factory->tag->create_and_get( [
			'name' => 'modtesttag',
		] );

		self::$factory->tag->add_post_terms( $post_ids[0], [ $tax_terms[0]->term_id ], self::$taxonomy );

		$post_ids[] = $factory->post->create( [
			'post_title' => 'modtest modtestlorem modtestdatemeta',
			'post_date'  => date( 'Y-m-d H:i:s', strtotime( '-1 day' ) ),
			'meta_input' => [ 'custom_date_field' => date( 'Ymd', strtotime( '-5 months' ) ) ],
		] );

		$post_ids[] = $factory->post->create( [
			'post_title' => 'modtestdatemeta',
			'post_date'  => date( 'Y-m-d H:i:s', strtotime( '-1 week' ) ),
		] );

		self::$post_ids  = $post_ids;
		self::$tax_terms = $tax_terms;

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

	public function test_taxonomy_bonus_weight() {
		global $wpdb;

		$mod    = new \SearchWP\Mod();
		$alias = $mod->get_foreign_alias();
		$bonus = [
			'term_id' => self::$tax_terms[0]->term_id,
			'weight'  => 100,
		];

		$mod->weight( "IF((
			SELECT {$wpdb->prefix}posts.ID
			FROM {$wpdb->prefix}posts
			LEFT JOIN {$wpdb->prefix}term_relationships ON (
				{$wpdb->prefix}posts.ID = {$wpdb->prefix}term_relationships.object_id
			)
			WHERE {$wpdb->prefix}posts.ID = {$alias}.id
				AND {$wpdb->prefix}term_relationships.term_taxonomy_id = {$bonus['term_id']}
			LIMIT 1
		) > 0, {$bonus['weight']}, 0)" );

		$query = new \SearchWP\Query( 'modtest', [
			'engine' => 'default',
			'mods'   => [ $mod ],
		] );

		$this->assertEquals( 2, count( $query->results ) );

		// The post with the tax term should rank first and have the bonus (*2 because of two attribute matches, title and slug!).
		$this->assertEquals( self::$post_ids[0], $query->results[0]->id );
		$this->assertEquals( 800, $query->results[0]->relevance );

		// The post without the tax term should rank second and not have the bonus.
		$this->assertEquals( self::$post_ids[1], $query->results[1]->id );
		$this->assertEquals( 600, $query->results[1]->relevance );
	}

	public function test_source_post_id_limiter() {
		$source = \SearchWP\Utils::get_post_type_source_name( 'post' );
		$mod    = new \SearchWP\Mod( $source );

		$mod->set_where( [ [
			'column'  => 'id',
			'value'   => [ self::$post_ids[1] ],
			'compare' => 'IN',
			'type'    => 'NUMERIC',
		] ] );

		$query = new \SearchWP\Query( 'modtest', [
			'engine' => 'default',
			'mods'   => [ $mod ],
		] );

		// Make sure only one post was returned despite two matches due to the limit.
		$this->assertEquals( 1, count( $query->results ) );
		$this->assertEquals( self::$post_ids[1], $query->results[0]->id );
	}

	public function test_adding_bonus_weight_to_source() {
		global $wpdb;

		$source = \SearchWP\Utils::get_post_type_source_name( 'post' );
		$mod    = new \SearchWP\Mod( $source );

		$mod->weight( $wpdb->prepare( "IF(s.source = %s, 99, 0)", $source ) );

		$query = new \SearchWP\Query( 'modtest', [
			'engine' => 'default',
			'mods'   => [ $mod ],
		] );

		$this->assertEquals( 2, count( $query->results ) );

		// Make sure our bonus weight (99 (* 2 for title and slug match)) was added to the calculated weight (600).
		$this->assertEquals( 798, $query->results[0]->relevance );
	}

	public function test_order_by_post_date() {
		$source = \SearchWP\Utils::get_post_type_source_name( 'post' );
		$mod    = new \SearchWP\Mod( $source );

		$mod->order_by( function( $mod ) {
			return $mod->get_local_table_alias() . '.post_date';
		}, 'ASC', 1 );

		$query = new \SearchWP\Query( 'modtest', [
			'engine' => 'default',
			'fields' => 'ids',
			'mods'   => [ $mod ],
		] );

		$this->assertEquals( 2, count( $query->results ) );

		// The 2nd post we added has a date in the past, so it should be returned first.
		$this->assertEquals( $query->results[0], self::$post_ids[1] );
	}

	public function test_title_not_like() {
		$source = \SearchWP\Utils::get_post_type_source_name( 'post' );
		$mod    = new \SearchWP\Mod( $source );

		$mod->set_where( [ [
			'column'  => 'post_title',
			'value'   => 'modtest',
			'compare' => 'NOT LIKE',
		] ] );

		$query = new \SearchWP\Query( 'modtest', [
			'engine' => 'default',
			'mods'   => [ $mod ],
		] );

		// All titles have 'modtest' in them.
		$this->assertEquals( 0, count( $query->results ) );
	}

	public function test_bonus_weight_stored_as_custom_field() {
		global $wpdb;

		// Custom Field name. Needs to store data as YYYYMMDD (ACF does this already).
		$my_meta_key  = 'custom_date_field';

		// NOTE: Metadata was set during init.

		$mod = new \SearchWP\Mod();
		$mod->set_local_table( $wpdb->postmeta );
		$mod->on( 'post_id', [ 'column' => 'id' ] );
		$mod->on( 'meta_key', [ 'value' => $my_meta_key ] );

		$mod->weight( function( $runtime ) use ( $wpdb, $my_meta_key ) { return $wpdb->prepare( "
			COALESCE( ROUND( ( (
				UNIX_TIMESTAMP( {$runtime->get_local_table_alias()}.meta_value )
				- (
					SELECT UNIX_TIMESTAMP( meta_value )
					FROM {$wpdb->postmeta}
					WHERE meta_key = %s
					ORDER BY meta_value ASC
					LIMIT 1
				)
			) / 86400 ), 0 ), 0 )", $my_meta_key );
		} );

		$query = new \SearchWP\Query( 'modtestdatemeta', [
			'engine' => 'default',
			'mods'   => [ $mod ],
		] );

		// We should have two results.
		$this->assertEquals( 3, count( $query->results ) );
		$this->assertContains( $query->results[0]->id, self::$post_ids );
		$this->assertContains( $query->results[1]->id, self::$post_ids );
		$this->assertContains( $query->results[2]->id, self::$post_ids );

		// Determine that weights were in fact changed.
		$this->assertGreaterThan( 900, $query->results[0]->relevance );

		// This should have no weight modification because it's the oldest date.
		$this->assertEquals( 600, $query->results[1]->relevance );

		// This should have no weight modification because there was no meta value.
		$this->assertEquals( 600, $query->results[2]->relevance );
	}

	public function test_bonus_weight_decays_over_time() {
		global $wpdb;

		$weight_adjust = 15;

		$mod = new \SearchWP\Mod();
		$mod->set_local_table( $wpdb->posts );
		$mod->on( 'ID', [ 'column' => 'id' ] );
		$mod->weight( function( $runtime_mod ) use ( $weight_adjust ) {
			return "ROUND(
			( 100 * EXP(
				( 1 - ABS( (
					UNIX_TIMESTAMP( {$runtime_mod->get_local_table_alias()}.post_date )
					- UNIX_TIMESTAMP( NOW() )
				) / 86400 ) ) / 1 )
			* {$weight_adjust} ), 0 )";
		} );

		$query = new \SearchWP\Query( 'modtest', [
			'engine' => 'default',
			'mods'   => [ $mod ],
		] );

		// We should have two results.
		$this->assertEquals( 2, count( $query->results ) );
		$this->assertContains( $query->results[0]->id, self::$post_ids );
		$this->assertContains( $query->results[1]->id, self::$post_ids );

		// Determine that weights were in fact changed.
		// TODO: determine if these change over time since they're time based and the test suite may not run the same every time
		$this->assertGreaterThan( 7500, $query->results[0]->relevance );
		$this->assertGreaterThan( 4140, $query->results[1]->relevance );
	}

	public function test_bonus_weight_recently_published_posts() {
		global $wpdb;

		$mod = new \SearchWP\Mod();
		$mod->set_local_table( $wpdb->posts );
		$mod->on( 'ID', [ 'column' => 'id' ] );
		$mod->weight( function( $runtime ) use ( $wpdb ) { return "
			COALESCE( ROUND( ( (
				UNIX_TIMESTAMP( {$runtime->get_local_table_alias()}.post_date )
				- (
					SELECT UNIX_TIMESTAMP( {$wpdb->posts}.post_date )
					FROM {$wpdb->posts}
					WHERE {$wpdb->posts}.post_status = 'publish'
					ORDER BY {$wpdb->posts}.post_date ASC
					LIMIT 1
				)
			) / 86400 ), 0 ), 0 )";
		} );
		$mods[] = $mod;

		$query = new \SearchWP\Query( 'modtest', [
			'engine' => 'default',
			'mods'   => [ $mod ],
		] );

		// We should have two results.
		$this->assertEquals( 2, count( $query->results ) );
		$this->assertContains( $query->results[0]->id, self::$post_ids );
		$this->assertContains( $query->results[1]->id, self::$post_ids );

		// A bonus weight of 2 should be awarded to the more recently published post.
		$this->assertEquals( 614, $query->results[0]->relevance );
		$this->assertEquals( 612, $query->results[1]->relevance );
	}

	public function test_orderby() {
		// We're going to force a Source here because a Mod with an ORDER BY should be an Index Mod.
		$mod = new \SearchWP\Mod( \SearchWP\Utils::get_post_type_source_name( 'post' ) );

		$mod->order_by( function( $mod ) {
			$source_order = [
				\SearchWP\Utils::get_post_type_source_name( 'page' ),
				\SearchWP\Utils::get_post_type_source_name( 'post' ),
			];

			return "FIELD({$mod->get_foreign_alias()}.source, "
				. implode( ',', array_filter( array_map( function( $source_name ) {
					global $wpdb;

					return $wpdb->prepare( '%s', $source_name );
				}, $source_order ) ) ) . ')';
		}, '', 1 );

		$mod->order_by( function( $mod ) {
			return $mod->get_local_table_alias() . '.post_date';
		}, 'ASC', 2 );

		$query = new \SearchWP\Query( 'modtest', [
			'engine' => 'default',
			'fields' => 'ids',
			'mods'   => [ $mod ],
		] );

		// We should have two results.
		$this->assertEquals( 2, count( $query->results ) );
		$this->assertContains( $query->results[0], self::$post_ids );
		$this->assertContains( $query->results[1], self::$post_ids );

		// The 2nd post we added has a date in the past, so it should be returned first.
		$this->assertEquals( $query->results[0], self::$post_ids[1] );
	}

	public function test_post_type_taxonomy_limiter() {
		$mod = new \SearchWP\Mod( \SearchWP\Utils::get_post_type_source_name( 'post' ) );

		$tax_args = [
			'taxonomy' => self::$taxonomy,
			'field'    => 'term_id',
			'terms'    => [ self::$tax_terms[0]->term_id ],
		];

		$alias     = 'taxalias';
		$tax_query = new WP_Tax_Query( [ $tax_args ] );
		$tq_sql    = $tax_query->get_sql( $alias, 'ID' );

		$mod->raw_join_sql( function( $runtime ) use ( $tq_sql, $alias ) {
			return str_replace( $alias, $runtime->get_local_table_alias(), $tq_sql['join'] );
		} );

		$mod->raw_where_sql( function( $runtime ) use ( $tq_sql, $alias ) {
			return '1=1 ' . str_replace( $alias, $runtime->get_local_table_alias(), $tq_sql['where'] );
		} );

		$query = new \SearchWP\Query( 'modtest', [
			'engine' => 'default',
			'fields' => 'ids',
			'mods'   => [ $mod ],
		] );

		// Test that we have one result instead of two because only one has the taxonomy term we're limiting to.
		$this->assertEquals( 1, count( $query->results ) );
		$this->assertContains( $query->results[0], self::$post_ids );
	}

	public function test_metrics_buoy() {
		global $wpdb;

		$meta_key = '_test_buoy_key';

		delete_post_meta( self::$post_ids[0], $meta_key );
		add_post_meta( self::$post_ids[0], $meta_key, 3, true );

		$mod = new \SearchWP\Mod();
		$mod->set_local_table( $wpdb->postmeta );
		$mod->on( 'post_id', [ 'column' => 'id' ] );
		$mod->on( 'meta_key', [ 'value' => $meta_key ] );
		$mod->weight( function( $mod, $args ) {
			return "( 10 * ( COALESCE({$mod->get_local_table_alias()}.meta_value, 0) ) )";
		} );

		$query = new \SearchWP\Query( 'modtest', [
			'engine' => 'default',
			'mods'   => [ $mod ],
		] );

		delete_post_meta( self::$post_ids[0], $meta_key );

		$this->assertEquals( 2, count( $query->results ) );
		$this->assertArrayHasKey( 0, $query->results );
		$this->assertArrayHasKey( 1, $query->results );

		$this->assertContains( $query->results[0]->id, self::$post_ids );
		$this->assertContains( $query->results[1]->id, self::$post_ids );

		// First result weight should be 660
		//		Title match 		300
		//		Slug match 			300
		//		Buoy weight 		3 clicks * 10 weight * 2 attribute matches
		$this->assertEquals( 660, $query->results[0]->relevance );

		// Second result weight should be 660
		//		Title match 		300
		//		Slug match 			300
		//		Buoy weight 		0 clicks * 10 weight * 2 attribute matches
		$this->assertEquals( 600, $query->results[1]->relevance );
	}
}
