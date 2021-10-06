<?php

/**
 * The class for displaying conditional content blocks.
 * This is where the content block rules are tested and content displayed in the hook as defined in the content block.
 */
class WC_Conditional_Content_Display {

	private static $instance = null;
	private static $contents = array();
	private static $loop_contents = array();

	/**
	 * Registers a single instance of the WC_Conditional_Content_Display class
	 */
	public static function register() {
		if ( self::$instance == null ) {
			self::$instance = new WC_Conditional_Content_Display();
		}
	}

	/**
	 * Gets the single instance of the WC_Conditional_Content_Display class.
	 *
	 * @return WC_Conditional_Content_Display
	 */
	public static function instance() {
		self::register();

		return self::$instance;
	}

	private $locations;

	/**
	 * Creates a new instance of the WC_Conditional_Content_Display class.
	 */
	public function __construct() {
		add_action( 'init', array( $this, 'load_locations' ), 0 );
		add_action( 'template_redirect', array( $this, 'bind_display' ), -1 );
	}

	public function load_locations() {
		$this->locations = apply_filters( 'wc_conditional_content_get_locations', array(), 0 );
	}

	/**
	 * Hooked into the template_redirect function.  Loads the configured content blocks and binds them based on
	 * the rules which have been configured for the content block.
	 *
	 * Content is stored internally and is displayed using the __call method.
	 */
	public function bind_display() {
		$contents = get_posts( array( 'post_type' => 'wccc', 'post_status' => 'publish', 'nopaging' => true ) );


		if ( $contents && count( $contents ) ) {

			foreach ( $contents as $content ) {
				$display = false;
				$loop    = false;

				$settings = get_post_meta( $content->ID, '_wccc_settings', true );

				$custom_hook     = false;
				$custom_priority = false;
				$accepted_args   = 0;
				if ( $settings['hook'] == 'custom' ) {
					$custom_hook     = $settings['custom_hook'];
					$custom_priority = $settings['custom_priority'];
					$accepted_args   = isset( $settings['custom_accepted_args'] ) ? $settings['custom_accepted_args'] : 0;
				}

				if ( $settings['location'] == 'single-product' && ! is_product() ) {
					//We can skip checking if the rule matches. 
					continue;
				}

				if ( isset( $settings['type'] ) && $settings['type'] == 'loop' ) {
					$display = true;
					$loop    = true;
					//We need to run the content block check each time the action or filter is fired. 
				} else {
					$display = true;
				}

				$display = apply_filters( 'woocommerce_conditional_content_get_should_display', $display, $content );

				if ( $display ) {

					if ( $custom_hook ) {
						$hook = apply_filters( 'woocommerce_conditional_content_get_output_hook', array(
							'action'        => $custom_hook,
							'priority'      => $custom_priority,
							'accepted_args' => $accepted_args
						), $settings, $content );
						if ( $hook && isset( $hook['action'] ) && isset( $hook['priority'] ) ) {

							$tag = 'display_' . str_replace( '-', '_', sanitize_title( $hook['action'] ) ) . '_' . $hook['priority'];
							if ( $loop ) {
								self::$loop_contents[ $tag ][] = $content;
							} else {
								self::$contents[ $tag ][] = $content;
							}

							add_action( $hook['action'], array( $this, $tag ), $hook['priority'], $accepted_args );
						}
					} elseif ( isset( $this->locations[ $settings['location'] ]['hooks'][ $settings['hook'] ] ) ) {
						$hook_setting = $this->locations[ $settings['location'] ]['hooks'][ $settings['hook'] ];
						$hook         = apply_filters( 'woocommerce_conditional_content_get_output_hook', array(
							'action'        => $hook_setting['action'],
							'priority'      => $hook_setting['priority'],
							'accepted_args' => isset( $hook_setting['custom_accepted_args'] ) ? $hook_setting['custom_accepted_args'] : 1
						), $settings, $content );

						if ( $hook && isset( $hook['action'] ) && isset( $hook['priority'] ) ) {

							$tag                      = 'display_' . str_replace( '-', '_', sanitize_title( $hook['action'] ) ) . '_' . $hook['priority'];
							self::$contents[ $tag ][] = $content;
							$accepted_args = isset( $hook_setting['custom_accepted_args'] ) ? $hook_setting['custom_accepted_args'] : 0;
							add_action( $hook['action'], array(
								$this,
								$tag
							), $hook['priority'], $accepted_args );
						}
					}
				}
			}
		}
	}

	/**
	 * Display or retrieve conditional content.  This is used with the template tag woocommerce_conditional_content()
	 *
	 * @since 1.0.0
	 *
	 * @param int  $content_id Optional. The content to process.
	 * @param bool $echo       Optional, default to true.Whether to display or return.
	 *
	 * @return null|string Null if no content rules match. String if $echo parameter is false and content rules match.
	 */
	public function template_display( $content_id = 0, $echo = true ) {
		if ( $content_id ) {
			$contents = get_posts( array(
				'p'           => $content_id,
				'post_type'   => 'wccc',
				'post_status' => 'publish',
				'nopaging'    => true
			) );
		} else {
			$contents = get_posts( array( 'post_type' => 'wccc', 'post_status' => 'publish', 'nopaging' => true ) );
		}

		$display = false;

		if ( $contents && count( $contents ) ) {

			foreach ( $contents as $content ) {
				$settings = get_post_meta( $content->ID, '_wccc_settings', true );

				if ( $settings['location'] == 'single-product' && ! is_product() ) {
					//We can skip checking if the rule matches. 
					continue;
				}


				$groups = get_post_meta( $content->ID, 'wccc_rule', true );

				if ( $groups && count( $groups ) ) {
					foreach ( $groups as $group_id => $group ) {
						$result = 'start';

						foreach ( $group as $rule_id => $rule ) {
							$rule_object = woocommerce_conditional_content_get_rule_object( $rule['rule_type'] );
							$result      = ( $result != 'start' ? ( $result & $rule_object->is_match( $rule ) ) : $rule_object->is_match( $rule ) );
						}

						if ( $result ) {
							$display = true;
						}
					}
				}

				$display = apply_filters( 'woocommerce_conditional_content_get_should_display', $display, $content );
				if ( $display ) {

					$content_result = apply_filters( 'woocommerce_conditional_content_the_content', apply_filters( 'the_content', $content->post_content ) );
					if ( $echo ) {
						wc_get_template( 'contentblock.php', array(
							'content' => $content
						), 'woocommerce-conditional-content', WC_Conditional_Content::plugin_path() . '/templates/' );
					}

					return $content_result;
				}
			}
		}
	}

	/*
	 * Helper function to render the content which is bound to each hook. 
	 */

	public function __call( $name, $arguments ) {
		$rebind_filter = false;
		if ( has_filter( 'the_content', 'sharing_display' ) ) {
			$rebind_filter = true;
			remove_filter( 'the_content', 'sharing_display', 19 );
		}

		if ( isset( self::$contents[ $name ] ) ) {
			foreach ( self::$contents[ $name ] as $content ) {
				if ( $this->match_groups( $content, $arguments ) ) {
					wc_get_template( 'contentblock.php', array( 'content' => $content ), 'woocommerce-conditional-content', WC_Conditional_Content::plugin_path() . '/templates/' );
				}
			}
		}

		if ( isset( self::$loop_contents[ $name ] ) ) {
			foreach ( self::$loop_contents[ $name ] as $content ) {
				if ( $this->match_groups( $content, $arguments ) ) {
					wc_get_template( 'contentblock.php', array( 'content' => $content ), 'woocommerce-conditional-content', WC_Conditional_Content::plugin_path() . '/templates/' );
				}
			}
		}

		if ( $rebind_filter ) {
			add_filter( 'the_content', 'sharing_display', 19 );
		}
	}

	public function match_groups( $content, $arguments = null ) {
		$display = false;

		$groups = get_post_meta( $content->ID, 'wccc_rule', true );
		if ( $groups && count( $groups ) ) {
			foreach ( $groups as $group_id => $group ) {
				$result = null;

				foreach ( $group as $rule_id => $rule ) {
					$rule_object = woocommerce_conditional_content_get_rule_object( $rule['rule_type'] );
					if ( is_object( $rule_object ) ) {
						$match  = $rule_object->is_match( $rule, $arguments );
						$result = ( $result !== null ? ( $result & $match ) : $match );
					}
				}

				if ( $result ) {
					$display = true;
				}
			}
		} else {
			$display = true; //Always display the content if no rules have been configured.
		}

		return $display;
	}

}
