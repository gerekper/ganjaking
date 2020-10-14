<?php

namespace SearchWP_Related;

use WP_Post;

/**
 * Class Meta_Box
 *
 * @package SearchWP_Related
 * @since 0.0.1
 */
class Meta_Box {

	private $related;
	private $nonce_key           = 'searchwp_related_nonce';
	private $nonce_action        = 'searchwp_related_keywords';
	private $excluded_post_types = array();
	private $existing_keywords   = '';

	/**
	 * Meta_Box constructor.
	 *
	 * @internal param SearchWP_Related $searchwp_related
	 *
	 * @internal param WP_Post $post
	 */
	public function __construct() {
		$this->related = new \SearchWP_Related();
	}

	/**
	 * Initialize
	 */
	public function init() {
		add_action( 'add_meta_boxes', array( $this, 'register_meta_box' ), 10, 2 );
		add_action( 'save_post', array( $this, 'save_post' ), 10, 3 );
		add_action( 'admin_footer', array( $this, 'preview_javascript' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'assets' ), 999, 1 );
		add_action( 'wp_ajax_searchwp_related_preview', array( $this, 'get_samples_as_json' ) );
		add_action( 'wp_ajax_searchwp_related_search_titles', array( $this, 'search_titles' ) );
		add_action( 'wp_ajax_searchwp_related_update_always_include', array( $this, 'update_always_include' ) );

		// Exclude non-public post types by default.
		$this->excluded_post_types = (array) apply_filters(
			'searchwp_related_excluded_post_types',
			array_merge(
				array( 'attachment' ),
				array_values(
					get_post_types( array(
						'public' => false,
					) )
				)
			)
		);

		$this->excluded_post_types = array_unique( $this->excluded_post_types );
	}

	/**
	 * Callback to persist the Always Include settings.
	 *
	 * @since 1.3
	 */
	public function update_always_include() {
		if ( ! isset( $_POST['nonce'] ) || ! isset( $_POST['post_id'] ) || ! wp_verify_nonce( $_POST['nonce'], 'searchwp_related_update_always_include_' . absint( $_POST['post_id'] ) ) ) {
			die();
		}

		$always_include = ! empty( $_POST['always_include'] ) ? json_decode( stripslashes( $_POST['always_include'] ) ) : array();
		$always_include = array_map( 'absint', $always_include );

		update_post_meta( absint( $_POST['post_id'] ), $this->related->meta_key . '_always_include', $always_include );

		wp_send_json_success();
	}

	/**
	 * Callback to search post titles when setting up Always Include.
	 *
	 * @since 1.3
	 */
	public function search_titles() {
		global $wpdb;

		if ( ! isset( $_POST['nonce'] ) || ! isset( $_POST['post_id'] ) || ! wp_verify_nonce( $_POST['nonce'], 'searchwp_related_search_titles_' . absint( $_POST['post_id'] ) ) ) {
			die();
		}

		if ( ! isset( $_POST['searchquery'] ) ) {
			wp_send_json_error();
		}

		$query   = sanitize_text_field( stripslashes( $_POST['searchquery'] ) );
		$post_id = absint( $_POST['post_id'] );

		$saved_always_include = get_post_meta( $post_id, $this->related->meta_key . '_always_include', true );

		// This is used to exclude from results, so we need an impossible array here.
		if ( empty( $saved_always_include ) ) {
			$saved_always_include = array( 0 );
		}

		$saved_always_include = array_map( 'absint', $saved_always_include );

		$like    = '%' . $wpdb->esc_like( $query ) . '%';
		$results = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT ID, post_title, post_type FROM {$wpdb->posts} WHERE post_title LIKE %s AND post_type NOT IN ('revision') AND post_status NOT IN ('trash', 'inherit') AND ID NOT IN (" . implode( ',', $saved_always_include ) . ')',
				$like
			)
		);

		wp_send_json_success( $results );

		die();
	}

	/**
	 * Callback to save submitted keywords
	 *
	 * @param $post_id
	 *
	 * @return mixed
	 */
	public function save_post( $post_id, $post, $update ) {
		if ( ! isset( $_POST['post_type'] ) || ! isset( $_POST['searchwp_related_keywords'] ) ) {
			return $post_id;
		}

		if ( ! isset( $_POST[ $this->nonce_key ] ) || ! wp_verify_nonce( $_POST[ $this->nonce_key ], $this->nonce_action ) ) {
			return $post_id;
		}

		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return $post_id;
		}

		if ( in_array( $_POST['post_type'], $this->excluded_post_types, true ) || ! current_user_can( 'edit_post', $post_id ) ) {
			return $post_id;
		}

		$keywords = trim( $_POST['searchwp_related_keywords'] );

		$initial_post_save = isset( $_POST['searchwp_related_initial_save'] ) && ! empty( $_POST['searchwp_related_initial_save'] );

		if ( empty( $keywords ) && ( $initial_post_save || 'future' === $post->post_status ) ) {
			// This is the first save, so let's grab fallback keywords
			$keywords = apply_filters( 'searchwp_related_default_keywords', $this->related->clean_string( $post->post_title ) );
		}

		if ( empty( $keywords ) ) {
			// Intentionally left empty.
			update_post_meta( $post_id, $this->related->meta_key . '_skip', true );
		} else {
			delete_post_meta( $post_id, $this->related->meta_key . '_skip' );
		}

		update_post_meta( $post_id, $this->related->meta_key, sanitize_text_field( $keywords ) );

		return $post_id;
	}

	/**
	 * Register meta box
	 *
	 * @param string    $post_type  The current post type
	 * @param WP_Post   $post       The current post object
	 */
	public function register_meta_box( $post_type, $post ) {
		$this->excluded_post_types = (array) apply_filters( 'searchwp_related_excluded_post_types', $this->excluded_post_types );

		$this->related->set_post( $post );

		// Let developers omit meta box from post type(s)
		if ( in_array( $post_type, $this->excluded_post_types, true ) ) {
			return;
		}

		// Let developers omit meta box based on single post
		$exclude_post = apply_filters( 'searchwp_related_exclude_post', false, $post );
		if ( ! empty( $exclude_post ) ) {
			return;
		}

		// Ok. Add meta box.
		add_meta_box(
			'searchwp-related',
			apply_filters( 'searchwp_related_meta_box_title', __( 'SearchWP Related Content', 'searchwp-related' ) ),
			array( $this, 'render_meta_box' ),
			$post_type,
			apply_filters( 'searchwp_related_meta_box_context', 'normal' ),
			apply_filters( 'searchwp_related_meta_box_priority', 'high' )
		);
	}

	/**
	 * Get associative array of sample results for keywords
	 *
	 * @param string $existing_keywords
	 *
	 * @param int $post_id
	 *
	 * @return array
	 */
	public function get_samples( $existing_keywords = '', $post_id = 0 ) {
		$samples = array();

		if ( function_exists( 'SWP' ) ) {
			$engines = SWP()->settings['engines'];
		} else if ( class_exists( '\\SearchWP\\Settings' ) ) {
			$engines = \SearchWP\Settings::get_engines();
		}

		if ( empty( $engines ) ) {
			return $samples;
		}

		foreach ( $engines as $engine => $engine_settings ) {

			$post_data = $this->related->get( array(
				'engine'       => $engine,
				's'            => $existing_keywords,
				'post__not_in' => array( $post_id ),
			), $post_id );

			if ( ! empty( $post_data ) ) {
				foreach ( $post_data as $key => $val ) {
					$val = absint( $val );
					$post_data[ $key ] = array(
						'ID'         => $val,
						'post_title' => get_the_title( $val ),
						'permalink'  => get_permalink( $val ),
						'post_type'  => get_post_type( $val ),
					);
				}
			}

			if ( function_exists( 'SWP' ) ) {
				$label = isset( $engine_settings['searchwp_engine_label'] ) ? esc_html( $engine_settings['searchwp_engine_label'] ) : esc_html__( 'Default', 'searchwp-related' );
			} else {
				$label = $engine_settings->get_label();
			}

			$samples[] = array(
				'engine' => array(
					'name'  => $engine,
					'label' => $label,
				),
				'samples' => empty( $post_data ) ? array() : $post_data,
			);
		}

		return $samples;
	}

	/**
	 * Callback for AJAX action to retrieve samples
	 */
	public function get_samples_as_json() {

		if ( ! isset( $_POST['nonce'] ) || ! isset( $_POST['post_id'] ) || ! wp_verify_nonce( $_POST['nonce'], 'searchwp_related_preview_' . absint( $_POST['post_id'] ) ) ) {
			die();
		}

		if ( ! isset( $_POST['terms'] ) ) {
			die();
		}

		$terms   = sanitize_text_field( $_POST['terms'] );
		$post_id = absint( $_POST['post_id'] );

		echo wp_json_encode( $this->get_samples( $terms, $post_id ) );

		die();
	}

	/**
	 * Render meta box
	 */
	public function render_meta_box() {
		global $pagenow;

		$this->existing_keywords = get_post_meta( $this->related->post->ID, $this->related->meta_key, true );

		if ( empty( $this->existing_keywords ) ) {
			$this->existing_keywords = $this->related->maybe_get_fallback_keywords( $this->related->post->ID );
		}

		wp_nonce_field( $this->nonce_action, $this->nonce_key );

		// Flag whether this is the initial save.
		if ( 'post-new.php' === $pagenow ) {
			echo '<input type="hidden" name="searchwp_related_initial_save" value="1"/>';
		}

		?>
		<div class="searchwp-related">
			<div class="searchwp-related-post-input-wrapper">
				<p>
					<label for="searchwp_related_keywords"><?php esc_html_e( 'Keyword(s) to use when finding related content', 'searchwp-related' ); ?></label>
					<input class="widefat" type="text" name="searchwp_related_keywords" id="searchwp_related_keywords" value="<?php echo esc_attr( $this->existing_keywords ); ?>" size="30" />
				</p>
				<div>
					<button class="button searchwp-related-settings-toggle">
						<span>
							<span class="dashicons dashicons-admin-settings"></span>
							<span><?php esc_html_e( 'Settings', 'searchwp-related' ); ?> <span id="searchwp-settings-count">(0)</span></span>
						</span>
					</button>
				</div>
			</div>
			<div class="searchwp-related-post-settings-wrapper">
				<h3><?php esc_html_e( 'Always include', 'searchwp-related' ); ?></h3>
				<p class="description"><?php esc_html_e( 'Applies only to this entry', 'searchwp-related' ); ?></p>
				<div class="searchwp-related-always-include">
					<div class="searchwp-related-always-include__search">
						<input type="text" placeholder="<?php esc_attr_e( 'Search entry titles...', 'searchwp-related' ); ?>"/>
					</div>
					<div class="searchwp-related-always-include__results">
						<ul class="searchwp-related-always-include__results-pool">
						</ul>
						<ul class="searchwp-related-always-include__results-chosen">
						</ul>
					</div>
					<div class="spinner"></div>
				</div>
			</div>
			<div class="searchwp-related-previews-wrapper">
				<div class="searchwp-related-previews-wrapper-heading">
					<h3><?php esc_html_e( 'Results Sample', 'searchwp-related' ); ?></h3>
					<p class="description">The <a href="https://searchwp.com/extensions/related/#template-loader" target="_blank">template loader</a> determines how results appear on your site (this is just a sampling)</p>
				</div>
				<div class="searchwp-related-previews">
					<?php $skipped = get_post_meta( $this->related->post->ID, $this->related->meta_key . '_skip', true ); ?>
					<?php if ( ! empty( $skipped ) ) : ?>
						<p class="description"><?php echo esc_html( $this->get_message( 'skipped' ) ); ?></p>
					<?php else : ?>
						<?php $samples = $this->get_samples( $this->existing_keywords, $this->related->post->ID ); ?>
						<?php foreach ( $samples as $sample ) : ?>
							<div class="searchwp-related-preview">
								<?php if ( count( $samples ) > 1 ) : ?>
									<h4><?php echo esc_html( $sample['engine']['label'] ); ?></h4>
								<?php endif; ?>
								<?php $this->render_related( $sample['samples'] ); ?>
							</div>
						<?php endforeach; ?>
					<?php endif; ?>
					<div class="spinner"></div>
				</div>
			</div>
		</div>

		<style type="text/css">
			.searchwp-related-post-input-wrapper {
				display: flex;
				align-items: center;
			}

			.searchwp-related-post-input-wrapper > p {
				flex: 1;
				padding-right: 1em;
			}

			.searchwp-related-post-input-wrapper > div {
				padding-top: 1.9em; /* Match offset of keyword field */
			}

			.searchwp-related-post-input-wrapper button {
				display: block;
			}

			.searchwp-related-post-input-wrapper button span {
				margin: 0;
				line-height: 1;
			}

			.searchwp-related-post-input-wrapper button > span {
				display: flex;
				align-items: center;
				justify-content: center;
			}

			.searchwp-related-post-input-wrapper button > span span {
				display: block;
				margin: 0;
				padding: 0 0.25em 0 0;
			}

			.searchwp-related-post-input-wrapper button > span span.dashicons {
				margin-right: 0.25em;
			}

			.searchwp-related-post-input-wrapper button > span span#searchwp-settings-count {
				display: inline;
			}

			.searchwp-related-post-settings-wrapper {
				padding-bottom: 1em;
			}

			.searchwp-related-post-settings-wrapper > h3 {
				margin-top: 0.6em;
				margin-bottom: 0;
			}

			.searchwp-related-post-settings-wrapper > h3 + p.description {
				margin-bottom: 1em;
			}

			.searchwp-related-always-include {
				position: relative;
			}

			.searchwp-related-always-include.searchwp-related-loading .searchwp-related-always-include__results {
				opacity: 0.5;
			}

			.searchwp-related-always-include__search input {
				display: block;
				width: 100%;
				margin: 0 0 1em;
			}

			.searchwp-related-always-include__results {
				display: flex;
				background-color: #fff;
				border: 1px solid #ddd;
				position: relative;
			}

			.searchwp-related-always-include__results > ul {
				width: 50%;
				flex: 1;
				height: 10em;
				overflow: auto;
				margin: 0;
				padding: 0;
				list-style: none;
			}

			.searchwp-related-always-include__results > ul > li {
				padding: 0.5em;
				margin: 0;
			}

			.searchwp-related-always-include__results > ul > li:hover {
				background: #ebebeb;
			}

			.searchwp-related-always-include__results-pool {
				background: #f9f9f9;
				border-right: 1px solid #ddd;
			}

			.searchwp-related-always-include__results-chosen li {
				display: flex;
				justify-content: space-between;
				align-items: center;
			}

			.searchwp-related .searchwp-related-always-include__results-chosen span {
				display: block;
				margin-top: 0;
			}

			.searchwp-related-always-include__results-chosen span.searchwp-related-remove-chosen {
				line-height: 1;
				transform: scale(0.9);
				opacity: 0.7;
			}

			.searchwp-related-always-include__results-chosen span.searchwp-related-remove-chosen:hover {
				opacity: 0.9;
			}

			.searchwp-related-always-include__results-chosen span {
				cursor: move;
			}

			.searchwp-related-always-include__results-pool li,
			.searchwp-related-always-include__results-chosen span.searchwp-related-remove-chosen {
				cursor: pointer;
			}

			.searchwp-related-previews-wrapper-heading,
			.searchwp-related-previews {
				display: flex;
				width: 100%;
			}

			.searchwp-related-previews-wrapper-heading {
				align-items: baseline;
			}

			.searchwp-related-previews-wrapper-heading h3 {
				padding-right: 0.5em;
				margin-top: 0.6em;
				margin-bottom: 0.6em;
			}

			.searchwp-related-previews {
				background-color: #f9f9f9;
				border: 1px solid #ddd;
				position: relative;
			}

			.searchwp-related-previews > p.description {
				padding: 1em;
				margin: 0;
			}

			.searchwp-related-previews.searchwp-related-loading p.description,
			.searchwp-related-previews.searchwp-related-loading .searchwp-related-preview {
				opacity: 0.5;
			}

			.searchwp-related .spinner {
				opacity: 1;
				display: none;
				position: absolute;
				top: 50%;
				left: 50%;
				margin: -10px 0 0 -10px;
				visibility: visible;
			}

			.searchwp-related-loading .spinner {
				display: block;
			}

			.searchwp-related-preview {
				flex: 1;
				padding: 0.8em 1em 1em;
			}

			.searchwp-related-preview p {
				margin: 0.2em 0 0;
			}

			.searchwp-related-preview ol {
				margin-top: 0.5em;
				margin-bottom: 0;
			}

			.searchwp-related-preview h4 {
				margin-top: 0;
				margin-bottom: 0;
			}

			.searchwp-related label {
				display: block;
				line-height: 1.6;
				margin-bottom: 0.5em;
			}

			.searchwp-related span {
				display: block;
				line-height: 1.4;
				margin-top: 0.4em;
			}
		</style>
		<?php
	}

	/**
	 * Render the related content
	 *
	 * @param array $existing_related
	 * @param bool $template
	 */
	public function render_related( $existing_related = array(), $template = false ) {
		if ( ! empty( $template ) ) {
			echo "<% if (searchwp_related_engines>1) { %>\n";
			echo '<h4><%- searchwp_related.engine.label %></h4>';
			echo "\n<% } %>\n";
		}
		if ( empty( $existing_related ) || ! empty( $template ) ) {
			if ( ! empty( $template ) ) {
				echo "<% if (searchwp_related.samples.length<1) { %>\n";
			}
			echo '<p class="description">' . esc_html__( 'No results found', 'searchwp-related' ) . '</p>';
			if ( ! empty( $template ) ) {
				echo "\n<% } %>\n";
			}
		}
		if ( ! empty( $existing_related ) || ! empty( $template ) ) {
			if ( ! empty( $template ) ) {
				echo "<% if (searchwp_related.samples.length>0) { %>";
			}
			echo '<ol>';
			if ( ! empty( $template ) ) {
				echo "\n<% _.each(searchwp_related.samples, function(sample) { %>\n";
			}
			foreach ( $existing_related as $related ) { ?>
				<?php $related_id = absint( $related['ID'] ); ?>
				<li>
					<a href="<?php if ( empty( $template ) ) {
						echo esc_url( get_permalink( $related_id ) );
					} else {
						echo "<%- sample.permalink %>";
					} ?>"><?php if ( empty( $template ) ) {
							echo esc_html( html_entity_decode( get_the_title( $related_id ) ) );
						} else {
							echo "<%- sample.post_title %>";
						} ?></a>
					(<?php if ( empty( $template ) ) {
						echo esc_html( get_post_type( $related_id ) );
					} else {
						echo "<%- sample.post_type %>";
					} ?>)
				</li>
			<?php }
			if ( ! empty( $template ) ) {
				echo "\n<% }); %>\n";
			}
			echo '</ol>';
			if ( ! empty( $template ) ) {
				echo "\n<% } %>\n";
			}
		}
	}

	/**
	 * Output JavaScript in the footer
	 */
	public function preview_javascript() {
		// We store only post IDs but we want title and post type here too.
		$always_included_ids = get_post_meta( $this->related->post->ID, $this->related->meta_key . '_always_include', true );
		$always_included = array();
		if ( ! empty( $always_included_ids ) ) {
			$always_included_obj = get_posts( array(
				'nopaging'    => true,
				'post_type'   => 'any',
				'post_status' => 'any',
				'post__in'    => $always_included_ids,
				'orderby'     => 'post__in',
			) );

			if ( ! empty( $always_included_obj ) ) {
				$always_included = array_map( function( $entry ) {
					return array(
						'ID'         => $entry->ID,
						'post_title' => $entry->post_title . '(' . $entry->post_type . ')',
						'post_type'  => $entry->post_type,
					);
				}, $always_included_obj );
			}
		}

		?>
		<script type="text/template" id="tmpl-searchwp-related">
			<div class="searchwp-related-preview">
				<?php $this->render_related( array( 0 ), true ); ?>
			</div>
		</script>
		<script type="text/javascript" >
			jQuery(document).ready(function($) {
				// Settings functionality.
				var $settings = $('.searchwp-related-post-settings-wrapper');
				var $settingsToggle = $('.searchwp-related-settings-toggle');
				$settings.toggle();
				$settingsToggle.click(function(e) {
					e.preventDefault();
					$settings.toggle();
				});

				// Always include functionality.
				var $alwaysIncludeInput = $('.searchwp-related-always-include__search > input');
				var $alwaysIncludeResults = $('.searchwp-related-always-include__results-pool');
				var alwaysIncludeResults = [];
				var $alwaysIncludeChosen = $('.searchwp-related-always-include__results-chosen');
				var alwaysIncludeChosen = JSON.parse('<?php echo wp_json_encode( $always_included ); ?>');

				var searchArrayForObjectKeyValue = function(arr,objKey,keyVal) {
					var present = false;
					$.each((arr), function(index, arrObj) {
						if(arrObj.hasOwnProperty(objKey) && arrObj[objKey]==keyVal) {
							present = index + 1;
						}
					});
					return present;
				}

				var updateChosenEntries = function() {
					var alwaysIncludeChosenHtml = '';
					var title = '';
					if (alwaysIncludeChosen.length) {
						$.each((alwaysIncludeChosen), function(index, chosen) {
							title = $('<div/>').text(chosen.post_title).html();
							alwaysIncludeChosenHtml += '<li data-searchwp-always-include-post-id="' + parseInt(chosen.ID, 10) + '"><span>' + title + '</span><span class="dashicons dashicons-dismiss searchwp-related-remove-chosen"></span></li>';
						});
					}
					$alwaysIncludeChosen.html(alwaysIncludeChosenHtml);

					alwaysIncludeChosenPayload = [];
					$.each((alwaysIncludeChosen), function(index, value) {
						alwaysIncludeChosenPayload.push(value.ID);
					});
				};

				var updateResultsPool = function() {
					var alwaysIncludeResultsHtml = '';
					var title = '';
					var type = '';
					if (alwaysIncludeResults.length) {
						$.each((alwaysIncludeResults), function(index, result) {
							title = $('<div/>').text(result.post_title).html();
							type = $('<div/>').text(result.post_type).html();
							if (!searchArrayForObjectKeyValue(alwaysIncludeChosen, 'ID', result.ID)) {
								alwaysIncludeResultsHtml += '<li data-searchwp-always-include-post-id="' + parseInt(result.ID, 10) + '">' + title + ' (' + type + ')</li>';
							}
						});
					}
					$alwaysIncludeResults.html(alwaysIncludeResultsHtml);
				};

				if (alwaysIncludeChosen&&alwaysIncludeChosen.length) {
					updateChosenEntries();
				} else {
					alwaysIncludeChosen = [];
				}

				var alwaysIncludeInputTimer;
				var lastAlwaysIncludeSearch = '';
				var alwaysIncludeData = {
					'action': 'searchwp_related_search_titles',
					'post_id': <?php echo absint( $this->related->post->ID ); ?>,
					'nonce': '<?php echo esc_js( wp_create_nonce( 'searchwp_related_search_titles_' . absint( $this->related->post->ID ) ) ); ?>'
				};

				var updatePreview = function() {
					var $input = $('#searchwp_related_keywords');
					var $container = $('.searchwp-related-previews');
					var data = {
						'action': 'searchwp_related_preview',
						'post_id': <?php echo absint( $this->related->post->ID ); ?>,
						'nonce': '<?php echo esc_js( wp_create_nonce( 'searchwp_related_preview_' . absint( $this->related->post->ID ) ) ); ?>'
					};

					data.terms = $input.val();

					$container.addClass('searchwp-related-loading');

					if(data.terms){
						jQuery.post(ajaxurl, data, function (response) {

							var samples = jQuery.parseJSON(response);

							$container.empty();

							$.each((samples), function( index, value ) {
								$container.append(template({
									searchwp_related: value,
									searchwp_related_engines: samples.length
								}));
							});

							$container.removeClass('searchwp-related-loading');
						});
					} else {
						$container.html('<p class="description"><?php echo esc_html( $this->get_message( 'skipped' ) ); ?></p>');
						$container.removeClass('searchwp-related-loading');
					}
				};

				var implementSortable = function() {
					$('.searchwp-related-always-include__results-chosen').sortable({
						update: function(event, ui) {
							// Sorting has taken place so we need to re-order our chosen entries array.
							alwaysIncludeChosen = [];
							jQuery('.searchwp-related-always-include__results-chosen').children().each(function(i, v) {
								var postId = parseInt($(v).data('searchwp-always-include-post-id'), 10);
								var postTitle = $(v).text();

								alwaysIncludeChosen.push({
									ID: postId,
									post_title: postTitle
								});
							});

							updateAlwaysInclude();
						}
					});
					$('.searchwp-related-always-include__results-chosen').disableSelection();
				};

				implementSortable();

				var updateAlwaysInclude = function() {
					$('.searchwp-related-always-include').addClass('searchwp-related-loading');
					updateResultsPool();
					updateChosenEntries();

					// Persist the data.
					jQuery.post(ajaxurl, {
						action: 'searchwp_related_update_always_include',
						post_id: <?php echo absint( $this->related->post->ID ); ?>,
						always_include: JSON.stringify(alwaysIncludeChosenPayload),
						nonce: '<?php echo esc_js( wp_create_nonce( 'searchwp_related_update_always_include_' . absint( $this->related->post->ID ) ) ); ?>'
					}, function (response) {
						$('.searchwp-related-always-include').removeClass('searchwp-related-loading');

						implementSortable();

						// Also update the previews as those have now changed.
						updatePreview();

						// Update the settings counter for visual reference.
						$('#searchwp-settings-count').text('(' + parseInt(alwaysIncludeChosen.length, 10) + ')');
					});
				}

				// Add chosen entry when clicking an entry in the results pool.
				$alwaysIncludeResults.on('click', 'li', function() {
					var postId = parseInt($(this).data('searchwp-always-include-post-id'), 10);
					var postTitle = $(this).text();

					alwaysIncludeChosen.push({
						ID: postId,
						post_title: postTitle
					});

					updateAlwaysInclude();
				});

				// Remove chosen entry when clicking an entry in the chosen pool.
				$alwaysIncludeChosen.on('click', '.searchwp-related-remove-chosen', function() {
					// Instead of sifting through the post IDs to work backwards, we can just use the index.
					if (alwaysIncludeChosen.length === 1) {
						alwaysIncludeChosen = [];
					} else {
						alwaysIncludeChosen.splice($(this).index(), 1);
					}
					updateAlwaysInclude();
				});

				$alwaysIncludeInput.on("keyup paste triggerUpdate", function() {
					clearTimeout(alwaysIncludeInputTimer);
					alwaysIncludeInputTimer = setTimeout(function() {
						if ( lastAlwaysIncludeSearch !== $alwaysIncludeInput.val() ) {
							lastAlwaysIncludeSearch = $alwaysIncludeInput.val();

							alwaysIncludeData.searchquery = $alwaysIncludeInput.val();

							$('.searchwp-related-always-include').addClass('searchwp-related-loading');

							if(alwaysIncludeData.searchquery){
								jQuery.post(ajaxurl, alwaysIncludeData, function (response) {
									if (response.success) {
										alwaysIncludeResults = response.data;
									} else {
										alwaysIncludeResults = [];
									}

									updateAlwaysInclude();
								});
							} else {
								$('.searchwp-related-always-include').removeClass('searchwp-related-loading');
								alwaysIncludeResults = [];
							}
						}
					}, 500);
				});

				// If the page is refreshed, the input may have a value.
				if($alwaysIncludeInput.val()){
					$alwaysIncludeInput.trigger('triggerUpdate');
				}

				// Make sure the initial settings count is accurate.
				$('#searchwp-settings-count').text('(' + parseInt(alwaysIncludeChosen.length, 10) + ')');

				// Preview functionality.
				var timer;
				var last = '<?php echo esc_js( $this->existing_keywords ); ?>';
				var $input = $('#searchwp_related_keywords');
				var template = _.template($('#tmpl-searchwp-related').html());

				$input.on("keyup paste", function() {
					clearTimeout(timer);
					timer = setTimeout(function() {
						if ( last !== $input.val() ) {
							last = $input.val();
							updatePreview();
						}
					}, 500);
				});
			});
		</script>
		<?php
	}

	/**
	 * Output various messages
	 *
	 * @param $message
	 *
	 * @return string
	 */
	public function get_message( $message ) {

		$markup = '';

		switch ( $message ) {
			case 'skipped':
				$post_type_object = get_post_type_object( $this->related->post->post_type );
				$markup = sprintf(
					// Translators: the placeholder is the post type label singular name
					__( 'Keywords removed; Related content will be skipped for this %s', 'searchwp-related' ),
					$post_type_object->labels->singular_name
				);
				break;
		}

		return $markup;
	}

	/**
	 * Callback for on-page assets
	 *
	 * @param $hook
	 */
	public function assets( $hook ) {
		global $post;

		if ( in_array( $post->post_type, $this->excluded_post_types, true ) ) {
			return;
		}

		// Let developers omit meta box based on single post
		$exclude_post = apply_filters( 'searchwp_related_exclude_post', false, $post );
		if ( ! empty( $exclude_post ) ) {
			return;
		}

		if ( 'edit.php' !== $hook && 'post.php' !== $hook && 'post-new.php' !== $hook ) {
			return;
		}

		wp_enqueue_script( 'backbone' );
		wp_enqueue_script( 'jquery' );
		wp_enqueue_script( 'jquery-ui-sortable' );
	}
}
