<?php
/**
 * Shortcodes table class
 *
 * @package YITH\FAQPluginForWordPress\Admin\ListTables
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'YITH_FAQ_Shortcodes_Table' ) ) {

	/**
	 * Displays the shortcodes table in YITH FAQ plugin admin tab
	 *
	 * @class   YITH_FAQ_Shortcodes_Table
	 * @since   2.0.0
	 * @author  YITH <plugins@yithemes.com>
	 * @package YITH\FAQPluginForWordPress\Admin\ListTables
	 */
	class YITH_FAQ_Shortcodes_Table {

		/**
		 * Constructor
		 *
		 * @return  void
		 * @since   2.0.0
		 */
		public function __construct() {
			add_action( 'init', array( $this, 'init' ), 15 );
			add_action( 'admin_enqueue_scripts', array( $this, 'admin_scripts' ), 20 );
		}

		/**
		 * Init page
		 *
		 * @return  void
		 * @since   2.0.0
		 */
		public function init() {
			add_action( 'yith_faq_shortcodes', array( $this, 'output' ) );
			add_filter( 'shortcodes_per_page', array( $this, 'shortcodes_per_page' ), 10 );
			add_filter( 'yith_plugin_fw_icons_field_icons_' . YITH_FWP_SLUG, array( $this, 'filter_icons' ) );
			add_action( 'admin_action_yfwp_delete_shortcode', array( $this, 'delete_shortcode' ) );
			add_action( 'admin_action_yfwp_clone_shortcode', array( $this, 'clone_shortcode' ) );
		}

		/**
		 * Add scripts and styles
		 *
		 * @param string $hook The hook name.
		 *
		 * @return  void
		 * @since   2.0.0
		 */
		public function admin_scripts( $hook ) {

			if ( 'yith-plugins_page_yith-faq-plugin-for-wordpress' === $hook ) {

				if ( ! wp_script_is( 'jquery-blockui', 'enqueued' ) ) {
					wp_register_script( 'jquery-blockui', yit_load_css_file( YITH_FWP_ASSETS_URL . '/js/jquery-blockui/jquery.blockUI.js' ), array( 'jquery' ), '2.70', false );
				}

				wp_enqueue_script( 'jquery-blockui' );
				wp_enqueue_style( 'wp-jquery-ui-dialog' );
				wp_enqueue_style( 'yith-faq-shortcode-panel', yit_load_css_file( YITH_FWP_ASSETS_URL . '/css/admin-panel.css' ), array(), YITH_FWP_VERSION );
				wp_enqueue_style( 'yith-faq-shortcode-icons', yit_load_css_file( YITH_FWP_ASSETS_URL . '/css/icons.css' ), array(), YITH_FWP_VERSION );
				wp_enqueue_script( 'yith-faq-shortcode-panel', yit_load_js_file( YITH_FWP_ASSETS_URL . '/js/admin-panel.js' ), array( 'jquery', 'jquery-ui-dialog', 'jquery-blockui', 'jquery-tiptip' ), YITH_FWP_VERSION, true );

				wp_localize_script(
					'yith-faq-shortcode-panel',
					'yfwp_admin',
					array(
						'title_new'       => esc_html__( 'Create shortcode', 'yith-faq-plugin-for-wordpress' ),
						'title_edit'      => esc_html__( 'Edit shortcode', 'yith-faq-plugin-for-wordpress' ),
						'create_btn_text' => esc_html__( 'Create shortcode', 'yith-faq-plugin-for-wordpress' ),
						'save_btn_text'   => esc_html__( 'Save shortcode', 'yith-faq-plugin-for-wordpress' ),
						'errors'          => array(
							'missing_field'    => esc_html__( 'This field is required.', 'yith-faq-plugin-for-wordpress' ),
							'missing_category' => esc_html__( 'Please, select a least one category.', 'yith-faq-plugin-for-wordpress' ),
							'missing_page'     => esc_html__( 'Please, select a page.', 'yith-faq-plugin-for-wordpress' ),
						),
						'ajax_url'        => admin_url( 'admin-ajax.php' ),
					)
				);

			}

		}

		/**
		 * Outputs the shortcodes table template with insert form in plugin options panel
		 *
		 * @return  void
		 * @since   2.0.0
		 */
		public function output() {

			global $wpdb;

			$table = new YITH_Custom_Table(
				array(
					'singular' => 'shortcode',
					'plural'   => 'shortcodes',
					'id'       => 'shortcode',
				)
			);

			$values = $this->get_values();
			$edit   = is_array( $values );

			$fields  = $this->get_fields( $edit, $values );
			$message = '';

			if ( ! empty( $_POST['nonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['nonce'] ) ), basename( __FILE__ ) ) ) {
				$action = isset( $_POST['action'] ) ? sanitize_text_field( wp_unslash( $_POST['action'] ) ) : false;
				$values = isset( $_POST['yfwp_shortcode'] ) ? wp_unslash( $_POST['yfwp_shortcode'] ) : array(); //phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized

				if ( $action && ! empty( $values ) ) {
					$title = isset( $values['shortcode_title'] ) ? sanitize_text_field( wp_unslash( $values['shortcode_title'] ) ) : 'Shortcode';
					if ( 'insert' === $action ) {
						$shortcode_data = array(
							'post_title'   => $title,
							'post_content' => '',
							'post_excerpt' => '',
							'post_status'  => 'publish',
							'post_author'  => 0,
							'post_type'    => YITH_FWP_SHORTCODE_POST_TYPE,
						);
						$shortcode_id   = wp_insert_post( $shortcode_data );
					} else {
						$shortcode_id = isset( $_POST['shortcode_id'] ) ? (int) $_POST['shortcode_id'] : 0;

						wp_update_post(
							array(
								'ID'         => $shortcode_id,
								'post_title' => $title,
							)
						);
					}

					$saving_values = array(
						'search_box'       => isset( $values['search_box'] ) ? 'on' : 'off',
						'category_filters' => isset( $values['category_filters'] ) ? 'on' : 'off',
						'style'            => $values['style'],
						'title'            => $values['title'],
						'title_type'       => $values['title_type'],
						'page_size'        => $values['page_size'],
						'show_pagination'  => isset( $values['show_pagination'] ) ? 'on' : 'off',
						'faq_to_show'      => $values['faq_to_show'],
						'categories'       => 'all' === $values['faq_to_show'] ? '' : $values['categories'],
						'expand_faq'       => $values['expand_faq'],
						'show_icon'        => $values['show_icon'],
						'icon_size'        => $values['icon_size'],
						'icon'             => $values['icon'],
						'page_id'          => $values['page_id'],
					);
					if ( 'insert' === $action ) {
						$saving_values['shortcode_type'] = $values['shortcode_type'];
					}

					foreach ( $saving_values as $key => $value ) {
						update_post_meta( $shortcode_id, 'yfwp_' . $key, $value );
					}

					if ( 'insert' === $action ) {
						$message = esc_html__( 'Shortcode added successfully.', 'yith-faq-plugin-for-wordpress' );
					} else {
						$message = esc_html__( 'Shortcode updated successfully.', 'yith-faq-plugin-for-wordpress' );
					}
				}
			}

			$table->options = array(
				'select_table'       => $wpdb->posts,
				'select_columns'     => array(
					'ID',
					'post_title',
				),
				'select_where'       => 'post_type = "' . YITH_FWP_SHORTCODE_POST_TYPE . '" AND post_status = "publish"',
				'select_group'       => '',
				'select_order'       => 'post_title',
				'select_order_dir'   => 'ASC',
				'per_page_option'    => 'shortcodes_per_page',
				'search_where'       => '',
				'count_table'        => $wpdb->posts,
				'count_where'        => 'post_type = "' . YITH_FWP_SHORTCODE_POST_TYPE . '" AND post_status = "publish"',
				'key_column'         => 'ID',
				'view_columns'       => array(
					'post_title' => esc_html__( 'Name', 'yith-faq-plugin-for-wordpress' ),
					'type'       => esc_html__( 'Type', 'yith-faq-plugin-for-wordpress' ),
					'shortcode'  => esc_html__( 'Shortcode', 'yith-faq-plugin-for-wordpress' ),
					'actions'    => '',
				),
				'hidden_columns'     => array(),
				'sortable_columns'   => array(),
				'custom_columns'     => array(
					'column_shortcode' => function ( $item ) {
						yith_plugin_fw_get_field(
							array(
								'id'          => 'shortcode',
								'name'        => '',
								'type'        => 'copy-to-clipboard',
								'force_value' => '[yith_faq_preset id="' . $item['ID'] . '"]',
							),
							true
						);
					},
					'column_type'      => function ( $item ) {
						$shortcode_type = get_post_meta( $item['ID'], 'yfwp_shortcode_type', true );

						if ( 'faqs' === $shortcode_type ) {
							esc_html_e( 'FAQ List', 'yith-faq-plugin-for-wordpress' );
						} else {
							esc_html_e( 'FAQ Summary', 'yith-faq-plugin-for-wordpress' );
						}

					},
					'column_actions'   => function ( $item ) {
						$paged = isset( $_GET['paged'] ) ? (int) $_GET['paged'] : false; //phpcs:ignore WordPress.Security.NonceVerification.Recommended

						echo sprintf(
							'<a class="show-on-hover delete yith-plugin-fw__tips" href="%s" data-tip="%s" onclick="return confirm(\'%s\');"><i class="yith-icon yith-icon-trash"></i></a>',
							esc_url( yfwp_action_link( 'delete_shortcode', $item['ID'], false, $paged ) ),
							esc_html__( 'Delete', 'yith-faq-plugin-for-wordpress' ),
							esc_html__( 'Are you sure you want to delete this shortcode?', 'yith-faq-plugin-for-wordpress' )
						);
						echo sprintf(
							'<a class="show-on-hover clone yith-plugin-fw__tips" href="%s" data-tip="%s"><i class="yith-icon yith-icon-clone"></i></a>',
							esc_url( yfwp_action_link( 'clone_shortcode', $item['ID'], false, $paged ) ),
							esc_html__( 'Clone', 'yith-faq-plugin-for-wordpress' )
						);
						echo sprintf(
							'<a class="show-on-hover edit yith-plugin-fw__tips" href="%s" data-shortcode_id="%s" data-tip="%s"><i class="yith-icon yith-icon-edit"></i></a>',
							esc_url( yfwp_action_link( 'edit_shortcode', $item['ID'], YITH_FWP()->get_panel_page(), $paged ) ),
							esc_attr( $item['ID'] ),
							esc_html__( 'Edit', 'yith-faq-plugin-for-wordpress' )
						);

					},
				),
				'bulk_actions'       => array(
					'actions'   => array(),
					'functions' => array(),
				),
				'wp_cache_option'    => 'yith_faq_shortcodes',
				'custom_css_classes' => array( 'widefat', 'fixed', 'yith-plugin-fw__boxed-table', 'shortcodes' ),
			);
			$table->prepare_items();

			$action = isset( $_GET['action'] ) ? sanitize_text_field( wp_unslash( $_GET['action'] ) ) : false; //phpcs:ignore WordPress.Security.NonceVerification.Recommended
			if ( 'deleted' === $action ) {
				$message = esc_html__( 'Shortcode deleted successfully.', 'yith-faq-plugin-for-wordpress' );
			} elseif ( 'cloned' === $action ) {
				$message = esc_html__( 'Shortcode cloned successfully.', 'yith-faq-plugin-for-wordpress' );
			}

			$this->print_template( $table, $fields, $message );

		}

		/**
		 * Print table template
		 *
		 * @param YITH_Custom_Table $table   The table object.
		 * @param array             $fields  Fields array.
		 * @param string            $message Messages.
		 *
		 * @return  void
		 * @since   2.0.0
		 */
		private function print_template( $table, $fields, $message ) {

			$is_empty_list = ! $table->has_items();
			?>
			<div class="yith-plugin-fw-list-table">
				<div class="yith-plugin-fw-list-table-container">
					<div class="list-table-title">
						<h2><?php echo esc_html__( 'FAQ Shortcodes', 'yith-faq-plugin-for-wordpress' ); ?></h2>
						<?php if ( ! $is_empty_list ) : ?>
							<a class="yith-add-button" href="<?php esc_url( yfwp_action_link( 'add_shortcode', false, YITH_FWP()->get_panel_page(), false ) ); ?>"><?php echo esc_html__( 'Create shortcode', 'yith-faq-plugin-for-wordpress' ); ?></a>
						<?php endif; ?>
					</div>
					<div class="yith-faq-shortcodes">
						<?php if ( $message ) : ?>
							<?php include YITH_FWP_DIR . 'includes/admin/views/list-table/list-table-notice.php'; ?>
						<?php endif; ?>
						<?php
						if ( $is_empty_list ) {
							$attrs = array(
								'icon'            => YITH_FWP_ASSETS_URL . '/images/empty-preset.svg',
								'message'         => esc_html__( "You don't have any shortcode presets yet.", 'yith-faq-plugin-for-wordpress' ),
								'submessage'      => esc_html__( "But don't worry, you can create the first one here!", 'yith-faq-plugin-for-wordpress' ),
								'cta_button_text' => esc_html__( 'Create shortcode', 'yith-faq-plugin-for-wordpress' ),
								'cta_button_href' => esc_url( yfwp_action_link( 'add_shortcode', false, YITH_FWP()->get_panel_page(), false ) ),
							);
							include YITH_FWP_DIR . 'includes/admin/views/list-table/list-table-blank-state.php';
						} else {
							include YITH_FWP_DIR . 'includes/admin/views/list-table/list-table-form.php';
						}
						?>
						<div class="yith-faq-shortcodes-list-popup-wrapper">
							<?php
							$this->get_form( $fields );
							?>
						</div>
						<div class="clear"></div>
					</div>
				</div>
			</div>
			<?php

		}

		/**
		 * Get field option for current screen
		 *
		 * @param array $fields Fields array.
		 *
		 * @return  void
		 * @since   2.0.0
		 */
		private function get_form( $fields ) {
			?>
			<form id="form" method="POST" action="">
				<input type="hidden" name="nonce" value="<?php echo esc_attr( wp_create_nonce( basename( __FILE__ ) ) ); ?>"/>
				<input type="hidden" name="action"/>
				<input type="hidden" name="shortcode_id"/>
				<table class="form-table">
					<tbody>
					<?php foreach ( $fields as $field ) : ?>
						<tr valign="top" class="yith-plugin-fw-panel-wc-row <?php echo esc_attr( $field['type'] ); ?> <?php echo esc_attr( $field['id'] ); ?> <?php echo isset( $field['main-dep'] ) ? esc_attr( $field['main-dep'] ) : ''; ?> <?php echo isset( $field['required'] ) && $field['required'] ? 'yith-plugin-fw--required' : ''; ?>">
							<th scope="row" class="titledesc">
								<label for="<?php echo esc_attr( $field['id'] ); ?>"><?php echo esc_attr( $field['name'] ); ?></label>
							</th>
							<td>
								<?php
								$this->render_field( $field );
								?>
							</td>
						</tr>
					<?php endforeach; ?>
					</tbody>
				</table>
			</form>
			<?php
		}

		/**
		 * Render the option field.
		 *
		 * @param array $option The options of the filed.
		 *
		 * @return void
		 * @since  2.0.0
		 */
		private function render_field( $option ) {
			if ( ! empty( $option ) ) {

				$custom_attributes = array();

				if ( ! empty( $option['custom_attributes'] ) && is_array( $option['custom_attributes'] ) ) {
					foreach ( $option['custom_attributes'] as $attribute => $attribute_value ) {
						$custom_attributes[] = esc_attr( $attribute ) . '="' . esc_attr( $attribute_value ) . '"';
					}
				}

				$custom_attributes = implode( ' ', $custom_attributes );
				$std               = isset( $option['std'] ) ? $option['std'] : '';
				$db_value          = isset( $option['value'] ) ? $option['value'] : $std;

				if ( isset( $option['deps'] ) ) {
					$deps = $option['deps'];
				}

				if ( 'on-off' === $option['type'] ) {
					$option['type'] = 'onoff';
				}

				$field_template_path = yith_plugin_fw_get_field_template_path( $option );
				if ( $field_template_path ) {
					$field_container_path = apply_filters( 'yith_plugin_fw_panel_field_container_template_path', YIT_CORE_PLUGIN_TEMPLATE_PATH . '/panel/panel-field-container.php', $option );
					file_exists( $field_container_path ) && include $field_container_path;
				} else {
					do_action( "yit_panel_{$option['type']}", $option, $db_value, $custom_attributes );
				}
			}
		}

		/**
		 * Get field option for current screen
		 *
		 * @param boolean $edit   Is shortcode editing?.
		 * @param array   $values Saved shortcode values.
		 *
		 * @return  array
		 * @since   2.0.0
		 */
		private function get_fields( $edit = false, $values = array() ) {

			$fields = array(
				'shortcode_title'  => array(
					'id'       => 'shortcode_title',
					'name'     => esc_html__( 'Name', 'yith-faq-plugin-for-wordpress' ),
					'type'     => 'text',
					'required' => true,
				),
				'shortcode_type'   => array(
					'id'      => 'shortcode_type',
					'name'    => esc_html__( 'Shortcode type', 'yith-faq-plugin-for-wordpress' ),
					'type'    => 'select',
					'class'   => 'yfwp-select',
					'options' => array(
						'faqs'    => esc_html__( 'FAQ List', 'yith-faq-plugin-for-wordpress' ),
						'summary' => esc_html__( 'FAQ Summary', 'yith-faq-plugin-for-wordpress' ),
					),
					'std'     => 'faqs',
				),
				'search_box'       => array(
					'id'       => 'search_box',
					'name'     => esc_html__( 'Show search box', 'yith-faq-plugin-for-wordpress' ),
					'desc'     => '',
					'type'     => 'on-off',
					'std'      => yfwp_get_shortcode_defaults( 'search_box' ),
					'main-dep' => 'faqs',
				),
				'category_filters' => array(
					'id'       => 'category_filters',
					'name'     => esc_html__( 'Show category filters', 'yith-faq-plugin-for-wordpress' ),
					'desc'     => '',
					'type'     => 'on-off',
					'std'      => yfwp_get_shortcode_defaults( 'category_filters' ),
					'main-dep' => 'faqs',
				),
				'style'            => array(
					'id'       => 'style',
					'name'     => esc_html__( 'Choose the style', 'yith-faq-plugin-for-wordpress' ),
					'type'     => 'select',
					'class'    => 'yfwp-select',
					'options'  => array(
						'list'      => esc_html__( 'List', 'yith-faq-plugin-for-wordpress' ),
						'accordion' => esc_html__( 'Accordion', 'yith-faq-plugin-for-wordpress' ),
						'toggle'    => esc_html__( 'Toggle', 'yith-faq-plugin-for-wordpress' ),
					),
					'std'      => yfwp_get_shortcode_defaults( 'style' ),
					'main-dep' => 'faqs',
				),
				'title'            => array(
					'id'       => 'title',
					'name'     => esc_html__( 'Block title', 'yith-faq-plugin-for-wordpress' ),
					'type'     => 'text',
					'main-dep' => 'summary',
				),
				'title_type'       => array(
					'id'       => 'title_type',
					'name'     => esc_html__( 'Title type', 'yith-faq-plugin-for-wordpress' ),
					'type'     => 'select',
					'class'    => 'yfwp-select',
					'options'  => array(
						'h1' => 'h1',
						'h2' => 'h2',
						'h3' => 'h3',
						'h4' => 'h4',
						'h5' => 'h5',
						'h6' => 'h6',
					),
					'std'      => yfwp_get_shortcode_defaults( 'title_type' ),
					'main-dep' => 'summary',
				),
				'show_pagination'  => array(
					'id'       => 'show_pagination',
					'name'     => esc_html__( 'Show pagination', 'yith-faq-plugin-for-wordpress' ),
					'desc'     => '',
					'type'     => 'on-off',
					'std'      => yfwp_get_shortcode_defaults( 'show_pagination' ),
					'main-dep' => 'faqs',
				),
				'page_size'        => array(
					'id'       => 'page_size',
					'name'     => esc_html__( 'FAQs per page', 'yith-faq-plugin-for-wordpress' ),
					'type'     => 'slider',
					'option'   => array(
						/**
						 * APPLY_FILTERS: yith_faq_minimum_page
						 *
						 * Set minimum number of items in a page.
						 *
						 * @param integer $value Minimum faq number.
						 *
						 * @return integer
						 */
						'min' => apply_filters( 'yith_faq_minimum_page', 5 ),
						/**
						 * APPLY_FILTERS: yith_faq_maximum_page
						 *
						 * Set maximum number of items in a page.
						 *
						 * @param integer $value Maximum faq number.
						 *
						 * @return integer
						 */
						'max' => apply_filters( 'yith_faq_maximum_page', 20 ),
					),
					'std'      => yfwp_get_shortcode_defaults( 'page_size' ),
					'main-dep' => 'faqs',
					'deps'     => array(
						'ids'    => 'show_pagination',
						'values' => 'yes',
					),
				),
				'faq_to_show'      => array(
					'id'      => 'faq_to_show',
					'name'    => esc_html__( 'FAQs to show', 'yith-faq-plugin-for-wordpress' ),
					'type'    => 'select',
					'class'   => 'yfwp-select',
					'options' => array(
						'all'       => esc_html__( 'All', 'yith-faq-plugin-for-wordpress' ),
						'selection' => esc_html__( 'Specific FAQs categories', 'yith-faq-plugin-for-wordpress' ),
					),
					'std'     => yfwp_get_shortcode_defaults( 'faq_to_show' ),
				),
				'categories'       => array(
					'id'       => 'categories',
					'name'     => esc_html__( 'Categories to display', 'yith-faq-plugin-for-wordpress' ),
					'type'     => 'ajax-terms',
					'multiple' => true,
					'data'     => array(
						'placeholder' => esc_html__( 'Search FAQs categories', 'yith-faq-plugin-for-wordpress' ),
						'taxonomy'    => YITH_FWP_FAQ_TAXONOMY,
					),
					'deps'     => array(
						'ids'    => 'faq_to_show',
						'values' => 'selection',
					),
					'required' => true,
				),
				'expand_faq'       => array(
					'id'       => 'expand_faq',
					'name'     => esc_html__( 'Expand FAQs', 'yith-faq-plugin-for-wordpress' ),
					'type'     => 'select',
					'class'    => 'yfwp-select',
					'options'  => array(
						'all-closed' => esc_html__( 'Show all FAQs closed', 'yith-faq-plugin-for-wordpress' ),
						'all-open'   => esc_html__( 'Show all FAQs expanded', 'yith-faq-plugin-for-wordpress' ),
						'first-only' => esc_html__( 'Show first FAQ expanded', 'yith-faq-plugin-for-wordpress' ),
					),
					'std'      => yfwp_get_shortcode_defaults( 'expand_faq' ),
					'deps'     => array(
						'ids'    => 'style',
						'values' => 'accordion,toggle',
					),
					'main-dep' => 'faqs',
				),
				'show_icon'        => array(
					'id'       => 'show_icon',
					'name'     => esc_html__( 'Show icon', 'yith-faq-plugin-for-wordpress' ),
					'type'     => 'select',
					'class'    => 'yfwp-select',
					'options'  => array(
						'off'   => esc_html__( 'Off', 'yith-faq-plugin-for-wordpress' ),
						'left'  => esc_html__( 'Left', 'yith-faq-plugin-for-wordpress' ),
						'right' => esc_html__( 'Right', 'yith-faq-plugin-for-wordpress' ),
					),
					'std'      => yfwp_get_shortcode_defaults( 'show_icon' ),
					'deps'     => array(
						'ids'    => 'style',
						'values' => 'accordion,toggle',
					),
					'main-dep' => 'faqs',
				),
				'icon_size'        => array(
					'id'       => 'icon_size',
					'name'     => esc_html__( 'Icon size (px)', 'yith-faq-plugin-for-wordpress' ),
					'type'     => 'slider',
					'option'   => array(
						'min' => '8',
						'max' => '40',
					),
					'std'      => yfwp_get_shortcode_defaults( 'icon_size' ),
					'deps'     => array(
						'ids'    => 'style',
						'values' => 'accordion,toggle',
					),
					'main-dep' => 'faqs',
				),
				'icon'             => array(
					'id'           => 'icon',
					'name'         => esc_html__( 'Choose the icon', 'yith-faq-plugin-for-wordpress' ),
					'type'         => 'icons',
					'std'          => yfwp_get_shortcode_defaults( 'icon' ),
					'filter_icons' => YITH_FWP_SLUG,
					'deps'         => array(
						'ids'    => 'style',
						'values' => 'accordion,toggle',
					),
					'main-dep'     => 'faqs',
				),
				'page_id'          => array(
					'id'       => 'page_id',
					'name'     => esc_html__( 'FAQ page', 'yith-faq-plugin-for-wordpress' ),
					'type'     => 'select',
					'class'    => 'yfwp-select',
					'options'  => yfwp_get_pages(),
					'main-dep' => 'summary',
					'required' => true,
				),
			);

			if ( $edit ) {
				$fields['shortcode_title']['value'] = $values['shortcode_title'];
				$fields['shortcode_type']['value']  = $values['shortcode_type'];
				foreach ( yfwp_get_shortcode_allowed_params( $values['shortcode_type'] ) as $field ) {
					if ( 'search_box' === $field || 'category_filters' === $field || 'show_pagination' === $field ) {
						$value = 'on' === $values[ $field ] ? 'yes' : 'no';
					} else {
						$value = $values[ $field ];
					}

					$fields[ $field ]['value'] = $value;
				}
			}

			return $fields;

		}

		/**
		 * Set field name
		 *
		 * @param string $name The field name.
		 *
		 * @return string
		 * @since  2.0.0
		 */
		public function get_name_field( $name = '' ) {
			return 'yfwp_shortcode[' . $name . ']';
		}

		/**
		 * Set field ID
		 *
		 * @param string $id The field ID.
		 *
		 * @return string
		 * @since  2.0.0
		 */
		public function get_id_field( $id ) {
			return 'yfwp_shortcode_' . $id;
		}

		/**
		 * Get values of selected shortcode.
		 *
		 * @return array|false
		 * @since  2.0.0
		 */
		private function get_values() {
			$shortcode = isset( $_GET['shortcode'] ) ? (int) $_GET['shortcode'] : false;

			if ( ! $shortcode || ! isset( $_GET['_wpnonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_GET['_wpnonce'] ) ), 'edit_shortcode' ) ) {
				return false;
			}

			$shortcode_type   = get_post_meta( $shortcode, 'yfwp_shortcode_type', true );
			$allowed_params   = yfwp_get_shortcode_allowed_params( $shortcode_type );
			$shortcode_values = array(
				'shortcode_title' => get_the_title( $shortcode ),
				'shortcode_type'  => $shortcode_type,
			);

			foreach ( $allowed_params as $param ) {
				$value                      = get_post_meta( $shortcode, 'yfwp_' . $param, true );
				$value                      = is_array( $value ) ? implode( ',', $value ) : $value;
				$shortcode_values[ $param ] = $value;
			}

			return $shortcode_values;

		}

		/**
		 * Get panel page URL
		 *
		 * @param string  $action The action performed.
		 * @param integer $page   The page number.
		 *
		 * @return string
		 * @since  2.0.0
		 */
		private function get_panel_page_url( $action, $page = false ) {
			$args = array(
				'page'   => YITH_FWP()->get_panel_page(),
				'action' => $action,
			);
			if ( $page ) {
				$args['paged'] = $page;
			}

			return add_query_arg( $args, admin_url( 'admin.php' ) );
		}

		/**
		 * Shortcode deletion
		 *
		 * @return void
		 * @since  2.0.0
		 */
		public function delete_shortcode() {
			$shortcode  = isset( $_GET['shortcode'] ) ? (int) $_GET['shortcode'] : false;
			$page       = isset( $_GET['paged'] ) ? (int) $_GET['paged'] : false;
			$return_url = $this->get_panel_page_url( 'deleted', $page );

			if ( ! $shortcode || ! isset( $_GET['_wpnonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_GET['_wpnonce'] ) ), 'delete_shortcode' ) ) {
				wp_safe_redirect( $return_url );
				die;
			}

			wp_delete_post( $shortcode );
			wp_safe_redirect( $return_url );
			die;
		}

		/**
		 * Shortcode clonation
		 *
		 * @return void
		 * @since  2.0.0
		 */
		public function clone_shortcode() {
			$shortcode  = isset( $_GET['shortcode'] ) ? (int) $_GET['shortcode'] : false;
			$page       = isset( $_GET['paged'] ) ? (int) $_GET['paged'] : false;
			$return_url = $this->get_panel_page_url( 'cloned', $page );

			if ( ! $shortcode || ! isset( $_GET['_wpnonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_GET['_wpnonce'] ) ), 'clone_shortcode' ) ) {
				wp_safe_redirect( $return_url );
				die;
			}

			$shortcode_data = array(
				'post_title'   => sprintf( '%s %s', get_the_title( $shortcode ), esc_html__( '(Copy)', 'yith-faq-plugin-for-wordpress' ) ),
				'post_content' => '',
				'post_excerpt' => '',
				'post_status'  => 'publish',
				'post_author'  => 0,
				'post_type'    => YITH_FWP_SHORTCODE_POST_TYPE,
			);
			$shortcode_id   = wp_insert_post( $shortcode_data );
			$post_meta      = get_post_custom( $shortcode );

			// Set unique key and correct post id.
			$post_meta['_key'][0] = uniqid();
			$post_meta['id'][0]   = $shortcode_id;

			if ( is_array( $post_meta ) ) {
				foreach ( $post_meta as $k => $v ) {
					update_post_meta( $shortcode_id, $k, maybe_unserialize( $v[0] ) );
				}
			}
			wp_safe_redirect( $return_url );
			die;
		}

		/**
		 * Removes unnecessary icons
		 *
		 * @return  array
		 * @since   1.0.0
		 */
		public function filter_icons() {

			return array(
				'yfwp' => array(
					'\e800' => 'plus',
					'\e801' => 'plus-circle',
					'\f0fe' => 'plus-square',
					'\f196' => 'plus-square-o',
					'\e804' => 'chevron-down',
					'\f13a' => 'chevron-circle-down',
					'\e806' => 'arrow-circle-o-down',
					'\e80a' => 'arrow-down',
					'\f0ab' => 'arrow-circle-down',
					'\f103' => 'angle-double-down',
					'\f107' => 'angle-down',
					'\e808' => 'caret-down',
					'\f150' => 'caret-square-o-down',
				),
			);

		}

		/**
		 * Set screen options for exclusions list table template
		 *
		 * @return  integer
		 * @since   2.0.0
		 */
		public function shortcodes_per_page() {
			return 20;
		}

	}

	new YITH_FAQ_Shortcodes_Table();
}
