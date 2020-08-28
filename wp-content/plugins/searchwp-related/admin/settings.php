<?php

/**
 * Class SearchWP_Related
 *
 * This class powers the settings screen UI within the SearchWP settings screen
 * allowing users to determine which post types have Related content auto-appended
 */
class SearchWP_Related_Settings {

	public $public                = true;
	public $slug                  = 'related';
	public $name                  = 'Related';
	public $min_searchwp_version  = '2.8.7';

	private $settings = array();

	/**
	 * SearchWP_Related_Settings constructor.
	 */
	function __construct() {}

	/**
	 * Initializer
	 */
	function init() {
		add_filter( 'searchwp\extensions', array( $this, 'register' ), 10 );
		add_filter( 'searchwp_extensions', array( $this, 'register' ), 10 );
		add_action( 'admin_enqueue_scripts', array( $this, 'assets' ), 999 );
	}

	/**
	 * Validate settings array against expected values
	 *
	 * @param $dirty_settings
	 *
	 * @return array
	 */
	function validate( $dirty_settings ) {
		// Defaults
		$settings = array(
			'auto_append'   => array(),
			'post__not_in'  => array(),
			'post__in'      => array(),
		);

		// Validate auto-append
		if ( ! empty( $dirty_settings['auto_append'] ) && is_array( $dirty_settings['auto_append'] ) ) {
			foreach ( $dirty_settings['auto_append'] as $post_type => $val ) {
				if ( post_type_exists( $post_type ) ) {
					$settings['auto_append'][] = $post_type;
				}
			}
		}

		// Validate post__not_in
		if ( ! empty( $dirty_settings['post__not_in'] ) && is_array( $dirty_settings['post__not_in'] ) ) {
			foreach ( $dirty_settings['post__not_in'] as $post_type => $val ) {
				if ( ! post_type_exists( $post_type ) ) {
					continue;
				}
				if ( function_exists( 'SWP' ) ) {
					$settings['post__not_in'][ $post_type ] = SWP()->get_integer_csv_string_from_string_or_array( $val );
				} else if ( class_exists( '\\SearchWP\\Utils' ) ) {
					$settings['post__not_in'][ $post_type ] = \SearchWP\Utils::get_integer_csv_string_from( $val );
				}
			}
		}

		// Validate post_in
		if ( ! empty( $dirty_settings['post__in'] ) && is_array( $dirty_settings['post__in'] ) ) {
			foreach ( $dirty_settings['post__in'] as $post_type => $val ) {
				if ( ! post_type_exists( $post_type ) ) {
					continue;
				}

				if ( function_exists( 'SWP' ) ) {
					$settings['post__in'][ $post_type ] = SWP()->get_integer_csv_string_from_string_or_array( $val );
				} else if ( class_exists( '\\SearchWP\\Utils' ) ) {
					$settings['post__in'][ $post_type ] = \SearchWP\Utils::get_integer_csv_string_from( $val );
				}
			}
		}

		return $settings;
	}

	/**
	 * Check for settings update, validate, save
	 */
	private function maybe_update_settings() {
		if ( isset( $_POST['searchwp_related_nonce'] ) && ! wp_verify_nonce( $_POST['searchwp_related_nonce'], 'searchwp_related_settings' ) ) {
			return;
		}

		if ( empty( $_POST['searchwp_related'] ) ) {
			return;
		}

		$settings = $_POST['searchwp_related'];

		$this->settings = $this->validate( $settings );

		update_option( 'searchwp_related', $settings );
	}

	/**
     * Getter for settings
     *
	 * @return array
	 */
	public function get() {
	    if ( empty( $this->settings ) ) {
		    $this->settings = $this->validate( searchwp_related_get_settings() );
        }

        return $this->settings;
    }

	/**
	 * Output the view for the settings screen
	 */
	function view() {
		$action_url = add_query_arg( array(
			'page'      => 'searchwp',
			'tab'       => 'extensions',
			'extension' => 'related',
		), admin_url( 'options-general.php' ) );

		$this->maybe_update_settings();

		$this->settings = $this->get();

		?>
		<div class="searchwp-related-settings">
			<form action="<?php echo esc_url( $action_url ); ?>" method="POST">
				<?php wp_nonce_field( 'searchwp_related_settings', 'searchwp_related_nonce' ); ?>
				<table>
					<thead>
						<tr>
							<th>
								<?php esc_html_e( 'Auto-append', 'searchwp-related' ); ?>
								<a href="#swp-related-tooltip-auto-append" class="swp-tooltip">?</a>
							</th>
							<th>
								<?php esc_html_e( 'Excluded from results', 'searchwp-related' ); ?>
								<a href="#swp-related-tooltip-csv" class="swp-tooltip">?</a>
							</th>
							<th>
								<?php esc_html_e( 'Limit potential results to', 'searchwp-related' ); ?>
								<a href="#swp-related-tooltip-csv" class="swp-tooltip">?</a>
							</th>
							<?php do_action( 'searchwp_related_settings_table_headings' ); ?>
						</tr>
					</thead>
					<tbody>
						<?php
						if ( function_exists( 'SWP' ) ) {
							$post_types = SWP()->get_indexed_post_types();
						} else if ( class_exists( '\\SearchWP\\Utils' ) ) {
							$post_types = \SearchWP\Utils::get_post_types();
						}

						foreach( $post_types as $post_type ) : ?>
							<tr>
								<td>
									<?php
									$post_type_obj = get_post_type_object( $post_type );
									$this->input(
										'auto_append',
										$post_type,
										$post_type_obj->label,
										false,
										'checkbox',
										1,
										is_array( $this->settings['auto_append'] ) && in_array( $post_type, $this->settings['auto_append'], true )
									);
									?>
								</td>
								<td>
									<?php
									// Excluded
									$val = '';
									if ( isset( $this->settings['post__not_in'] ) && isset( $this->settings['post__not_in'][ $post_type ] ) ) {
										if ( function_exists( 'SWP' ) ) {
											$val = SWP()->get_integer_csv_string_from_string_or_array( $this->settings['post__not_in'][ $post_type ] );
										} else if ( class_exists( '\SearchWP\Utils' ) ) {
											$val = \SearchWP\Utils::get_integer_csv_string_from( $this->settings['post__not_in'][ $post_type ] );
										}
									}
									$this->input(
										'post__not_in',
										$post_type,
										'Included',
										true,
										'text',
										$val
									);
									?>
								</td>
								<td>
									<?php
									// Included
									$val = '';
									if ( isset( $this->settings['post__not_in'] ) && isset( $this->settings['post__in'][ $post_type ] ) ) {
										if ( function_exists( 'SWP' ) ) {
											$val = SWP()->get_integer_csv_string_from_string_or_array( $this->settings['post__in'][ $post_type ] );
										} else if ( class_exists( '\SearchWP\Utils' ) ) {
											$val = \SearchWP\Utils::get_integer_csv_string_from( $this->settings['post__in'][ $post_type ] );
										}
									}
									$this->input(
										'post__in',
										$post_type,
										'Excluded',
										true,
										'text',
										$val
									);
									?>
								</td>
							</tr>
						<?php endforeach; ?>
						<?php do_action( 'searchwp_related_settings_table_body' ); ?>
					</tbody>
					<?php do_action( 'searchwp_related_settings_table_footer' ); ?>
				</table>

				<p class="description"><?php echo wp_kses( sprintf( __(
					'<strong>Note:</strong> Customize auto-append output using the <a href="%s">template loader</a>' ,
					'searchwp-related' ),
					'https://searchwp.com/extensions/related/#template-loader' ),
					array(
						'a' => array(
							'href' => array()
						),
						'strong' => array()
					)
				); ?></p>

				<div class="swp-tooltip-content" id="swp-related-tooltip-csv">
					<?php esc_html_e( 'Enter comma separated IDs', 'searchwp-related' ); ?>
				</div>

				<div class="swp-tooltip-content" id="swp-related-tooltip-auto-append">
					<?php echo wp_kses( sprintf( __(
						'Automatically append Related content to <code>the_content</code>' ,
						'searchwp-related' ),
						'https://searchwp.com/' ),
						array(
							'code'
						)
					); ?>
				</div>

				<p class="submit">
					<input type="submit" name="submit" id="submit" class="button button-primary" value="<?php esc_attr_e( 'Save Settings', 'searchwp-related' ); ?>">
				</p>
			</form>
		</div>
		<style type="text/css">
			.searchwp-related-settings table {
				width: 100%;
			}
			.searchwp-related-settings td {
				padding: 0.4em 1em 0.4em 0;
			}
			.searchwp-related-settings th {
				text-align: left;
				padding-bottom: 0.5em;
			}
			.searchwp-related-settings input[type="text"] {
				display: inline-block;
				width: 100%;
				max-width: 400px;
			}
		</style>
		<script type="text/javascript">
			jQuery(document).ready(function($){
                $(document).tooltip({
                    items: ".swp-tooltip,.swp-tooltip-alt",
                    content: function(){
                        return $($(this).attr('href')).html();
                    }
                });
			});
		</script>
		<?php
	}

	/**
	 * Output an <input>
	 *
	 * @param string $section
	 * @param string $name
	 * @param string $label
	 * @param bool $hidden_label
	 * @param string $type
	 * @param string $value
	 * @param bool $checked
	 */
	public function input( $section = '', $name = '', $label = '', $hidden_label = true, $type = 'text', $value = '', $checked = false ) {
		?>
		<input type="<?php echo esc_attr( $type ); ?>"
		       value="<?php echo esc_attr( $value ); ?>"
		       name="searchwp_related[<?php echo esc_attr( $section ); ?>][<?php echo esc_attr( $name ); ?>]"
		       id="searchwp_related[<?php echo esc_attr( $section ); ?>][<?php echo esc_attr( $name ); ?>]"
		       <?php if ( ! empty( $checked ) ) : ?>
			       checked="checked"
			   <?php endif; ?>
		>
		<?php if ( ! empty( $hidden_label ) ) : ?>
			<div class="screen-reader-text">
		<?php endif; ?>
		<label for="searchwp_related[<?php echo esc_attr( $section ); ?>][<?php echo esc_attr( $name ); ?>]"><?php echo esc_html( $label ); ?></label>
		<?php if ( ! empty( $hidden_label ) ) : ?>
			</div>
		<?php endif; ?>
		<?php
	}

	/**
	 * Callback for SearchWP Extension register
	 *
	 * @param $extensions
	 *
	 * @return mixed
	 */
	function register( $extensions ) {

		// When instantiating, SearchWP core forces a prefix of 'SearchWP' and it needs
		// to match the name of this class right here, so we need to get creative :boo:
		$extensions['_Related_Settings'] = __FILE__;

		return $extensions;
	}

	/**
	 * Output assets needed for settings screen
	 */
	function assets( $hook ) {

		if ( 'settings_page_searchwp' !== $hook ) {
			return;
		}

		if ( empty( $_GET['extension'] ) ) {
			return;
		}

		if ( 'related' !== $_GET['extension'] ) {
			return;
		}

		wp_enqueue_script( 'jquery' );
		wp_enqueue_script( 'jquery-ui-tooltip' );
	}
}
