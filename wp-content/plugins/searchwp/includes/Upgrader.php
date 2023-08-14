<?php

/**
 * SearchWP Upgrader.
 *
 * @package SearchWP
 * @author  Jon Christopher
 */

namespace SearchWP;

use SearchWP\Utils;
use SearchWP\Settings;

/**
 * Class Upgrader is responsible for executing upgrade routines.
 *
 * @since 4.0
 */
class Upgrader {

	/**
	 * Runs installation, upgrades, etc.
	 *
	 * @since 4.0
	 */
	public static function run( $network_wide = false ) {
		global $wpdb;

		$current_version = Settings::get( 'version' );

		if ( $current_version == SEARCHWP_VERSION ) {
			return;
		}

		if ( is_multisite() && $network_wide ) {
			foreach ( $wpdb->get_col( "SELECT blog_id FROM $wpdb->blogs" ) as $site_id ) {
				switch_to_blog( $site_id );
				self::upgrade( true );
				restore_current_blog();
			}
		} else {
			self::upgrade();
		}

		// Maybe redirect to Welcome screen on activated fresh install.
		if (
			( ! is_multisite() || ( is_multisite() && ! $network_wide ) )
			&& \SearchWP\Settings::get( 'new_activation' )
		) {
			// Disable redirection for subsequent page loads.
			\SearchWP\Settings::update( 'new_activation', false );

			wp_safe_redirect(
				add_query_arg(
					[ 'page' => 'searchwp-welcome' ],
					esc_url( admin_url( 'index.php' ) )
				)
			);
		}
	}

	/**
	 * Activation routine.
	 *
	 * @since 4.0
	 * @return void
	 */
	private static function activate() {
		// Ensure autoloaded Settings are autoloaded.
		foreach ( Settings::get_autoload_keys() as $key ) {
			if ( false === Settings::get( $key, 'boolean' ) ) {
				Settings::update( $key, '0' ); // Use '0' to get around the update short circuit.
			}
		}
	}

	/**
	 * Upgrade routine.
	 *
	 * @return void
	 */
	private static function upgrade( $network_wide = false ) {
		$current_version = Settings::get( 'version' );

		if ( empty( $current_version ) ) {
			self::install( $network_wide );
			self::activate();
		}

		do_action( 'searchwp\upgrader\before', [
			'upgraded_from' => $current_version,
			'upgraded_to'   => SEARCHWP_VERSION,
		] );

		// Maybe execute upgrade routine(s).
		self::execute( $current_version );

		// Update current version.
		if ( SEARCHWP_VERSION !== $current_version ) {
			Settings::update( 'upgraded_from', $current_version );
			Settings::update( 'version', SEARCHWP_VERSION );
		}

		do_action( 'searchwp\upgrader\after', [
			'upgraded_from' => $current_version,
			'upgraded_to'   => SEARCHWP_VERSION,
		] );
	}

	/**
	 * Upgrade routines to execute.
	 *
	 * @since 4.0
	 * @return void
	 */
	private static function execute( $upgrading_from) {
		if ( version_compare( $upgrading_from, '3.99.0', '<' ) ) {
			self::migrate_from_3x_to_4_0_0();
			self::activate();
		}

		if ( version_compare( $upgrading_from, '4.1.5', '<' ) ) {
			// See IndexTable for schema change.
		}

		if ( version_compare( $upgrading_from, '4.1.14', '<' ) ) {
			// Add baseline for cron health check.
			update_site_option( SEARCHWP_PREFIX . 'last_health_check', current_time( 'timestamp' ) );
		}
	}

	/**
	 * Migration from SearchWP 3 to SearcWP 4.
	 *
	 * @since 4.0
	 * @return void
	 */
	private static function migrate_from_3x_to_4_0_0() {
		/**
		 * Migrate Engines.
		 */
		$legacy_engines = get_option( 'searchwp_settings' );

		if ( $legacy_engines && is_array( $legacy_engines ) && array_key_exists( 'engines', $legacy_engines ) ) {
			// Migrate Engine models.
			$migrated_engines = call_user_func_array( 'array_merge', array_map(
				function( $engine, $config ) {
					return [ $engine => self::migrate_legacy_engine( $engine, $config ) ];
				},
				array_keys( $legacy_engines['engines'] ),
				array_values( $legacy_engines['engines'] )
			) );

			\SearchWP\Settings::update_engines_config( $migrated_engines );
		}

		/**
		 * Migrate Advanced Settings.
		 */
		$legacy_advanced = get_option( 'searchwp_advanced' );

		if ( ! empty( $legacy_advanced['debugging'] ) ) {
			\SearchWP\Settings::update( 'debug', true );
		}

		if ( empty( $legacy_advanced['exclusive_regex_matches'] ) ) {
			\SearchWP\Settings::update( 'tokenize_pattern_matches', true );
		}

		if ( ! empty( $legacy_advanced['parse_shortcodes'] ) ) {
			\SearchWP\Settings::update( 'parse_shortcodes', true );
		}

		if ( ! empty( $legacy_advanced['do_suggestions'] ) ) {
			\SearchWP\Settings::update( 'do_suggestions', true );
		}

		if ( ! empty( $legacy_advanced['quoted_search_support'] ) ) {
			\SearchWP\Settings::update( 'quoted_search_support', true );
		}

		if ( ! empty( $legacy_advanced['highlight_terms'] ) ) {
			\SearchWP\Settings::update( 'highlighting', true );
		}

		if ( ! empty( $legacy_advanced['partial_matches'] ) ) {
			\SearchWP\Settings::update( 'partial_matches', true );
		}

		if ( ! empty( $legacy_advanced['min_word_length'] ) ) {
			\SearchWP\Settings::update( 'remove_min_word_length', true );
		}

		if ( ! empty( $legacy_advanced['nuke_on_delete'] ) ) {
			\SearchWP\Settings::update( 'nuke_on_delete', true );
		}

		// Stopwords use the same storage.
		// Do not migrate Statistics, this is offloaded to a separate process invoked by the user.

		// Update Synonym storage.
		$legacy_synonyms = get_option( 'swp_termsyn_settings' );
		if ( ! empty( $legacy_synonyms ) ) {
			$synonyms = new \SearchWP\Logic\Synonyms();

			$updated = array_filter( array_map( function( $synonym ) {
				if (
					( ! isset( $synonym['term'] ) || empty( trim( $synonym['term'] ) ) )
					|| ( ! isset( $synonym['synonyms'] ) || ! is_array( $synonym['synonyms'] ) || empty( $synonym['synonyms'] ) )
				) {
					return false;
				} else {
					return [
						'sources'  => $synonym['term'],
						'synonyms' => implode( ', ', $synonym['synonyms'] ),
						'replace'  => ! empty( $synonym['replace'] ),
					];
				}
			}, (array) $legacy_synonyms ) );

			$synonyms->save( $updated );
		}

		/**
		 * Migrate license key.
		 */
		$license_key = trim( get_option( SEARCHWP_PREFIX . 'license_key' ) );
		$license_key = sanitize_text_field( $license_key );

		if ( ! empty( $license_key ) ) {
			\SearchWP\License::activate( $license_key );
		}
	}

	/**
	 * Converts a legacy (3.x) Engine model to a SearchWP 4-compatible model configuration.
	 *
	 * @since 4.0
	 * @param string $legacy_engine The name of the legacy engine.
	 * @param array  $legacy_config The legacy engine config.
	 * @return array
	 */
	public static function migrate_legacy_engine( $legacy_engine, $legacy_config ) {
		$new_config = [
			'sources'  => [],
			'settings' => [ 'stemming' => false, 'adminengine' => false ],
			'label'    => array_key_exists( 'searchwp_engine_label', $legacy_config )
							? $legacy_config['searchwp_engine_label'] : __( 'Default', 'searchwp' ),
		];

		foreach ( $legacy_config as $post_type => $post_type_config ) {
			if ( 'searchwp_engine_label' === $post_type ) {
				continue;
			}

			if ( empty( $post_type_config['enabled' ] ) ) {
				continue;
			}

			$source_config = [];

			// Migrate Native Attributes.
			foreach ( [ 'title', 'content', 'excerpt', 'slug', 'comment' ] as $attribute ) {
				if ( empty( $post_type_config['weights'][ $attribute ] ) ) {
					continue;
				}

				$weight = $post_type_config['weights'][ $attribute ];
				if ( 'comment' === $attribute ) {
					$attribute = 'comments';
				}

				$source_config['attributes'][ $attribute ] = absint( $weight );
			}

			// Migrate Meta.
			if ( ! empty( $post_type_config['weights']['cf'] ) ) {
				$meta = [];
				foreach ( $post_type_config['weights']['cf'] as $meta_pair ) {
					// Any Meta Key.
					if ( 'searchwpcfdefault' === $meta_pair['metakey'] ) {
						$meta_pair['metakey'] = '*';
					}

					// Document Content.
					if ( 'searchwp_content' === $meta_pair['metakey'] ) {
						$source_config['attributes']['document_content'] = absint( $meta_pair['weight'] );
						continue;
					}

					// PDF Metadata.
					if ( 'searchwp_pdf_metadata' === $meta_pair['metakey'] ) {
						$source_config['attributes']['pdf_metadata'] = absint( $meta_pair['weight'] );
						continue;
					}

					$meta[ $meta_pair['metakey'] ] = absint( $meta_pair['weight'] );
				}

				if ( ! empty( $meta ) ) {
					$source_config['attributes']['meta'] = $meta;
				}
			}

			// Migrate taxonomies.
			if ( ! empty( $post_type_config['weights']['tax'] ) ) {
				$source_config['attributes']['taxonomy'] = array_filter( $post_type_config['weights']['tax'] );
			}

			// Migrate Weight Transfer.
			if ( ! empty( $post_type_config['options']['attribute_to'] ) ) {
				$source_config['options']['weight_transfer'] = [
					'enabled' => true,
					'option'  => 'id',
					'value'   => $post_type_config['options']['attribute_to'],
				];
			} else if ( ! empty( $post_type_config['options']['attribute_to'] ) ) {
				$source_config['options']['weight_transfer'] = [
					'enabled' => true,
					'option'  => 'col',
					'value'   => null,
				];
			}

			// Keyword stemming was per post type in 3.x but now it's per Engine.
			if ( ! empty( $post_type_config['options']['stem'] ) ) {
				$new_config['settings']['stemming'] = true;
			}

			// Admin Engine is now stored alongside Engine.
			$legacy_advanced = get_option( 'searchwp_advanced' );
			if ( ! empty( $legacy_advanced['admin_search'] ) && ! empty( $legacy_advanced['admin_engine'] ) ) {
				$new_config['settings']['adminengine'] = $legacy_advanced['admin_engine'];
			}

			// Migrate Rules.
			$rules = [];

			if ( ! empty( $post_type_config['options']['exclude'] ) ) {
				$rules[] = [
					'type'  => 'IN',
					'rules' => [ [
						'option'    => null,
						'condition' => 'NOT IN',
						'rule'      => 'post_id',
						'value'     => array_map( 'absint', array_map( 'trim',
											explode( ',', $post_type_config['options']['exclude'] )
										) ),
					] ]
				];
			}

			if ( ! empty( $post_type_config['options'] ) ) {
				foreach( $post_type_config['options'] as $option => $value ) {
					if ( 0 !== strpos( $option, 'limit_to_' ) && 0 !== strpos( $option, 'exclude_' ) ) {
						continue;
					}

					if ( 0 === strpos( $option, 'limit_to_' ) ) {
						$logic    = 'IN';
						$taxonomy = substr( $option, 9 ); // limit_to_
					} else {
						$logic    = 'NOT IN';
						$taxonomy = substr( $option, 8 ); // exclude_
					}

					if ( ! taxonomy_exists( $taxonomy ) ) {
						continue;
					}

					$rules[] = [
						'type'  => 'IN',
						'rules' => [ [
							'option'    => $taxonomy,
							'condition' => $logic,
							'rule'      => 'taxonomy',
							'value'     => explode( ',', $value ),
						] ]
					];
				}
			}

			if ( ! empty( $rules ) ) {
				$source_config['rules'] = $rules;
			}

			$new_config['sources'][ 'post' . SEARCHWP_SEPARATOR . $post_type ] = $source_config;
		}

		$engine = new \SearchWP\Engine( $legacy_engine, $new_config );

		return \SearchWP\Utils::normalize_engine_config( json_decode( json_encode( $engine ), true ) );
	}

	/**
	 * Installation routine.
	 *     - Adds autoload options.
	 *
	 * @since 4.0
	 * @return void
	 */
	private static function install( $network_wide = false ) {
		$default_engine_config  = json_decode( json_encode( new Engine( 'default' ) ), true );

		// Keyword stemming is more useful than not.
		$default_engine_config['settings']['stemming'] = true;

		// Allow for custom initial Engine to be implemented.
		$initial_default_engine = apply_filters(
			'searchwp\install\engine\settings',
			$network_wide ? $default_engine_config : [], // Network-wide installation gets default Engine as starting point.
			$default_engine_config
		);

		if ( ! empty( $initial_default_engine ) ) {
			// Establish a Default Engine, which will in turn instantiate the Indexer.
			// Otherwise the user will need to review and save first.
			Settings::update_engines_config( [
				'default' => Utils::normalize_engine_config( $initial_default_engine )
			] );
		}
	}
}
