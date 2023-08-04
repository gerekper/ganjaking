<?php


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

use Themesquad\WC_Freshdesk\Utilities\Compat_Utils;

/**
 * Freshdesk Integration.
 *
 * @package  WC_Freshdesk_Integration
 * @category Integration
 * @author   WooThemes
 */
class WC_Freshdesk_Integration extends WC_Integration {

	/**
	 * Init and hook in the integration.
	 *
	 * @return void
	 */
	public function __construct() {
		$this->id                 = WC_Freshdesk::get_integration_id();
		$this->method_title       = __( 'Freshdesk', 'woocommerce-freshdesk' );
		$this->method_description = __( 'Freshdesk is a customer support solutions in the SaaS space.', 'woocommerce-freshdesk' );

		// Load the settings.
		$this->init_form_fields();
		$this->init_settings();

		// Define user set variables.
		$this->url        = 'https://' . $this->get_option( 'url' ) . '.freshdesk.com/api/v2/';
		$this->sso_url    = 'https://' . $this->get_option( 'url' ) . '.freshdesk.com/';
		$this->plan       = $this->get_option( 'plan' );
		$this->api_key    = $this->get_option( 'api_key' );
		$this->sso_secret = $this->get_option( 'sso_secret' );
		$this->debug      = $this->get_option( 'debug' );

		// Active logs.
		if ( 'yes' === $this->debug ) {
			$this->log = WC_Freshdesk::get_logger();
		}

		// Actions.
		add_action( 'woocommerce_update_options_integration_' .  $this->id, array( $this, 'process_admin_options' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_scripts' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'frontend_scripts' ) );

		// Customer "My Orders" actions.
		add_action( 'woocommerce_view_order', array( $this, 'view_order_create_ticket' ), 40 );
		add_action( 'woocommerce_my_account_my_orders_actions', array( $this, 'orders_actions' ), 10, 2 );
		add_action( 'woocommerce_after_my_account', array( $this, 'support_tickets' ), 10 );

		// Login.
		add_filter( 'woocommerce_login_redirect', array( $this, 'sso_login_redirect' ), 50, 2 );
		add_filter( 'allowed_redirect_hosts', array( $this, 'allow_freshdesk_host' ), 10, 1 );

		// SSO reply-to ticket redirect handler.
		add_action( 'init', array( $this, 'reply_to_ticket_redirect' ) );

		add_action( 'admin_init', array( $this, 'admin_init' ) );
		add_action( 'admin_notices', array( $this, 'admin_notices' ) );

		if ( is_admin() ) {
			// Product admin actions.
			add_filter( 'woocommerce_product_data_tabs', array( $this, 'product_data_tabs' ) );
			add_action( 'woocommerce_product_data_panels', array( $this, 'product_panel' ), 10 );
			add_action( 'woocommerce_process_product_meta', array( $this, 'save_product_panel' ), 20, 2 );

			// Orders.
			add_action( 'add_meta_boxes', array( $this, 'add_order_tickets_metabox' ) );

			// Comments to ticket.
			add_filter( 'manage_edit-comments_columns', array( $this, 'comments_columns' ) );
			add_action( 'manage_comments_custom_column', array( $this, 'custom_comment_column' ), 10, 2 );

			// Product reviews to ticket.
			add_filter( 'woocommerce_product_reviews_table_columns', array( $this, 'comments_columns' ) );
			add_action( 'woocommerce_product_reviews_table_column_' . $this->id . '_comment_actions', array( $this, 'custom_review_column' ) );
		}
	}

	/**
	 * Initializes admin.
	 *
	 * @since 1.3.0
	 */
	public function admin_init() {
		$this->log_downloader();
		$this->comment_to_ticket();
	}

	/**
	 * Gets if the specified screen ID belongs to the comments or product reviews screen.
	 *
	 * @since 1.3.0
	 *
	 * @param string $screen_id Screen ID.
	 * @return bool
	 */
	protected function is_comments_screen( $screen_id ) {
		return in_array( $screen_id, array( 'edit-comments', Compat_Utils::get_reviews_admin_screen() ), true );
	}

	/**
	 * Initialize integration settings form fields.
	 *
	 * @return void
	 */
	public function init_form_fields() {

		$debug_description = sprintf( __( 'Log Freshdesk events, such as API requests, inside %s', 'woocommerce-freshdesk' ), '<code>' . wc_get_log_file_path( $this->id ) . '</code>' ) . '<br /><br /><a href="' . esc_url( add_query_arg( array( 'page' => 'wc-status', 'tab' => 'logs' ), 'admin.php' ) ) . '">' . __( 'View Log', 'woocommerce-freshdesk' ) . '</a>';

		$this->form_fields = array(
			'url' => array(
				'title'       => __( 'Freshdesk URL', 'woocommerce-freshdesk' ),
				'type'        => 'freshdesk_url',
				'description' => sprintf( __( 'Enter your Freshdesk URL, example %s.', 'woocommerce-freshdesk' ), '<code>https://example.freshdesk.com/</code>' ),
				'default'     => ''
			),
			'plan' => array(
				'title'       => __( 'Plan', 'woocommerce-freshdesk' ),
				'type'        => 'select',
				'description' => __( 'Enter with the plan that you use in Freshdesk.', 'woocommerce-freshdesk' ),
				'desc_tip'    => true,
				'default'     => 'sprout',
				'options'     => array(
					'sprout'  => __( 'Sprout (Free)', 'woocommerce-freshdesk' ),
					'blossom' => __( 'Blossom', 'woocommerce-freshdesk' ),
					'garden'  => __( 'Garden', 'woocommerce-freshdesk' ),
					'estate'  => __( 'Estate', 'woocommerce-freshdesk' ),
					'forest'  => __( 'Forest', 'woocommerce-freshdesk' ),
				)
			),
			'api_key' => array(
				'title'       => __( 'API Key', 'woocommerce-freshdesk' ),
				'type'        => 'text',
				'description' => __( 'Enter your Freshdesk API Key. You can find this in "User Profile" drop-down (top right corner of your helpdesk) > Profile Settings > Your API Key.', 'woocommerce-freshdesk' ),
				'desc_tip'    => true,
				'default'     => ''
			),
			'testing' => array(
				'title'       => __( 'Testing', 'woocommerce-freshdesk' ),
				'type'        => 'title',
				'description' => ''
			),
			'debug' => array(
				'title'       => __( 'Debug Log', 'woocommerce-freshdesk' ),
				'type'        => 'checkbox',
				'label'       => __( 'Enable logging. Note: this may log personal information. We recommend using this for debugging purposes only and deleting the logs when finished.', 'woocommerce-freshdesk' ),
				'default'     => 'no',
				'description' => $debug_description
			)
		);
	}

	/**
	 * Build the log downloader URL.
	 *
	 * @return string
	 */
	protected function log_downloader_url() {

		$settings = 'wc-settings';

		$params = array(
			'page'    => $settings,
			'tab'     => 'integration',
			'section' => $this->id,
			'viewlog' => '1'
		);

		$url = add_query_arg( $params, admin_url( 'admin.php' ) );

		return $url;
	}

	/**
	 * Validate the Freshdesk URL.
	 *
	 * @param  mixed $key
	 *
	 * @return string
	 */
	public function validate_freshdesk_url_field( $key ) {
		$text = $this->get_option( $key );

		if ( isset( $_POST[ $this->plugin_id . $this->id . '_' . $key ] ) ) {
			$text = sanitize_text_field( $_POST[ $this->plugin_id . $this->id . '_' . $key ] );
		}

		return $text;
	}

	/**
	 * Generate the Freshdesk URL field.
	 *
	 * @param  mixed $key
	 * @param  array $data
	 *
	 * @return string
	 */
	public function generate_freshdesk_url_html( $key, $data ) {
		$field = $this->plugin_id . $this->id . '_' . $key;
		$defaults = array(
			'title'             => '',
			'disabled'          => false,
			'class'             => '',
			'css'               => '',
			'placeholder'       => '',
			'desc_tip'          => false,
			'description'       => '',
			'custom_attributes' => array()
		);

		$data = wp_parse_args( $data, $defaults );

		ob_start();
		?>
		<tr valign="top">
			<th scope="row" class="titledesc">
				<label for="<?php echo esc_attr( $field ); ?>"><?php echo wp_kses_post( $data['title'] ); ?></label>
				<?php echo $this->get_tooltip_html( $data ); ?>
			</th>
			<td class="forminp">
				<fieldset>
					<legend class="screen-reader-text"><span><?php echo wp_kses_post( $data['title'] ); ?></span></legend>
					<code><?php _e( 'https://', 'woocommerce-freshdesk' ); ?></code><input class="input-text regular-input <?php echo esc_attr( $data['class'] ); ?>" type="text" name="<?php echo esc_attr( $field ); ?>" id="<?php echo esc_attr( $field ); ?>" style="<?php echo esc_attr( $data['css'] ); ?>" value="<?php echo esc_attr( $this->get_option( $key ) ); ?>" placeholder="<?php echo esc_attr( $data['placeholder'] ); ?>" <?php disabled( $data['disabled'], true ); ?> <?php echo $this->get_custom_attribute_html( $data ); ?> /><code><?php _e( '.freshdesk.com', 'woocommerce-freshdesk' ); ?></code>
					<?php echo $this->get_description_html( $data ); ?>
				</fieldset>
			</td>
		</tr>
		<?php
		return ob_get_clean();
	}

	/**
	 * Generate the SSO Login URL.
	 *
	 * @param  string $redirect_to URL that the user will access after logging in.
	 *
	 * @return string              SSO Login URL.
	 */
	protected function sso_login_url( $user_name, $user_email, $redirect_to = '' ) {
		$timestamp = time();
		$hash      = hash_hmac( 'md5', $user_name . $this->sso_secret . $user_email . $timestamp, $this->sso_secret );
		$redirect  = ! empty( $redirect_to ) ? '&redirect_to=' . urlencode( $redirect_to ) : '';
		$sso_url   = sprintf(
			'%slogin/sso/?name=%s&email=%s&timestamp=%s&hash=%s%s',
			esc_url( $this->sso_url ),
			urlencode( $user_name ),
			urlencode( $user_email ),
			$timestamp,
			$hash,
			$redirect
		);

		return $sso_url;
	}

	/**
	 * Admin scripts.
	 *
	 * @return void
	 */
	public function admin_scripts() {
		$screen = get_current_screen();
		$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

		if ( 'product' === $screen->id ) {
			wp_enqueue_script( $this->id . '-product-panel', plugins_url( 'assets/js/admin/product-panel' . $suffix . '.js', plugin_dir_path( __FILE__ ) ), array( 'jquery' ), WC_FRESHDESK_VERSION, true );
			wp_enqueue_style( $this->id . '-product-screen', plugins_url( 'assets/css/admin/product-screen.css', plugin_dir_path( __FILE__ ) ), array(), WC_FRESHDESK_VERSION, 'all' );
		}

		if ( $this->is_comments_screen( $screen->id ) ) {
			wp_enqueue_style( $this->id . '-comments-screen', plugins_url( 'assets/css/admin/comments-screen.css', plugin_dir_path( __FILE__ ) ), array(), WC_FRESHDESK_VERSION, 'all' );
		}

		if ( Compat_Utils::get_order_admin_screen() === $screen->id ) {
			wp_enqueue_style( $this->id . '-order-screen', plugins_url( 'assets/css/admin/order-screen.css', plugin_dir_path( __FILE__ ) ), array(), WC_FRESHDESK_VERSION, 'all' );
			wp_enqueue_script( $this->id . '-order-screen', plugins_url( 'assets/js/admin/order-screen' . $suffix . '.js', plugin_dir_path( __FILE__ ) ), array( 'jquery-blockui' ), WC_FRESHDESK_VERSION, true );
			wp_localize_script(
				$this->id . '-order-screen',
				'woocommerce_freshdesk_shop_order_params',
				array(
					'success'     => __( 'Ticket created successfully.', 'woocommerce-freshdesk' ),
					'view_ticket' => __( 'View ticket.', 'woocommerce-freshdesk' ),
					'ajax_url'    => admin_url( 'admin-ajax.php' ),
					'security'    => wp_create_nonce( 'woocommerce_freshdesk_proccess_ticket' ),
					'error'       => __( 'Failed to create the ticket, please try again.', 'woocommerce-freshdesk' ),
					'ticket_url'  => esc_url( 'https://' . $this->get_option( 'url' ) . '.freshdesk.com/helpdesk/tickets/' )
				)
			);
		}
	}

	/**
	 * Front-end scripts.
	 *
	 * @return void
	 */
	public function frontend_scripts() {
		if ( is_account_page() ) {
			$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

			wp_enqueue_style( $this->id . '-support-tickets', plugins_url( 'assets/css/frontend/tickets.css', plugin_dir_path( __FILE__ ) ), array(), WC_FRESHDESK_VERSION, 'all' );
			wp_enqueue_script( $this->id . '-support-tickets', plugins_url( 'assets/js/frontend/tickets' . $suffix . '.js', plugin_dir_path( __FILE__ ) ), array( 'jquery', 'jquery-blockui' ), WC_FRESHDESK_VERSION, true );

			wp_localize_script(
				$this->id . '-support-tickets',
				'woocommerce_freshdesk_params',
				array(
					'processing' => __( 'Please, wait a few moments, sending your request...', 'woocommerce-freshdesk' ),
					'success'    => __( 'Thank you for your contact, we will respond as soon as possible.', 'woocommerce-freshdesk' ),
					'ajax_url'   => admin_url( 'admin-ajax.php' ),
					'security'   => wp_create_nonce( 'woocommerce_freshdesk_proccess_ticket' ),
					'error'      => __( 'There was an error in the request, please try again or contact us for assistance.', 'woocommerce-freshdesk' )
				)
			);
		}
	}

	/**
	 * Adds the Freshdesk product data tab.
	 *
	 * @param  array $tabs Product data tabs.
	 *
	 * @return array       Adds the Freshdesk tab.
	 */
	public function product_data_tabs( $tabs ) {
		$tabs[ $this->id ] = array(
			'label'  => __( 'Freshdesk', 'woocommerce-freshdesk' ),
			'target' => 'freshdesk_product_data',
			'class'  => array(),
		);

		return $tabs;
	}

	/**
	 * Adds the Freshdesk product data tab.
	 * Backwards compatibility for WooCommerce 2.0.x.
	 *
	 * @deprecated 1.2.0
	 *
	 * @return string Adds the Freshdesk tab.
	 */
	public function product_data_tabs_legacy() {
		wc_deprecated_function( __FUNCTION__, '1.2.0' );
		echo '<li class="freshdesk_options freshdesk_tab advanced_options"><a href="#freshdesk_product_data">' . __( 'Freshdesk', 'woocommerce-freshdesk' ) . '</a></li>';
	}

	/**
	 * Freshdesk tab panel.
	 *
	 * @return string Tab panel content.
	 */
	public function product_panel() {
		global $post;

		// Forum category data.
		$forum             = get_post_meta( $post->ID, '_forum_category', true );
		$forum_enable      = isset( $forum['enable'] )      ? $forum['enable']      : '';
		$forum_title       = isset( $forum['title'] )       ? $forum['title']       : $post->post_title;
		$forum_description = isset( $forum['description'] ) ? $forum['description'] : '';
		$forum_id          = get_post_meta( $post->ID, '_forum_category_id', true );

		// Solutions category data.
		$solutions             = get_post_meta( $post->ID, '_solutions_category', true );
		$solutions_enable      = isset( $solutions['enable'] )      ? $solutions['enable']      : '';
		$solutions_title       = isset( $solutions['title'] )       ? $solutions['title']       : $post->post_title;
		$solutions_description = isset( $solutions['description'] ) ? $solutions['description'] : '';
		$solutions_id          = get_post_meta( $post->ID, '_solutions_category_id', true );

		wp_nonce_field( basename( __FILE__ ), 'woocommerce_freshdesk_nonce' );

		echo '<div id="freshdesk_product_data" class="panel woocommerce_options_panel">';

			if ( 'sprout' !== $this->plan ) {
				echo '<div class="options_group">';
				echo '<h4>' . esc_html__( 'Forum Category', 'woocommerce-freshdesk' ) . '</h4>';

					if ( 'yes' != $forum_enable ) {
						woocommerce_wp_select(
							array(
								'id'          => '_forum_category',
								'label'       => __( 'Add/get Category', 'woocommerce-freshdesk' ),
								'cbvalue'     => 'yes',
								'value'       => '',
								'desc_tip'    => 'true',
								'description' => __( 'Create or synchronize a Freshdesk Forum Category.', 'woocommerce-freshdesk' ),
								'options'     => array(
									''       => __( 'Select an option&hellip;', 'woocommerce-freshdesk' ),
									'create' => __( 'Create', 'woocommerce-freshdesk' ),
									'sync'   => __( 'Synchronize', 'woocommerce-freshdesk' )
								)
							)
						);

						woocommerce_wp_text_input(
							array(
								'id'          => '_forum_category_id',
								'label'       => __( 'Category ID', 'woocommerce-freshdesk' ),
								'value'       => esc_attr( $forum_id ),
								'desc_tip'    => 'true',
								'description' => __( 'Enter the Forum Category ID to synchronize.', 'woocommerce-freshdesk' )
							)
						);
					} else {
						echo '<p class="category-id">' . esc_html__( 'Forum Category ID:', 'woocommerce-freshdesk' ) . ' <code>' . esc_attr( $forum_id ) . '</code></p>';

						woocommerce_wp_hidden_input(
							array(
								'id'    => '_forum_category_enable',
								'value' => sanitize_text_field( $forum_enable )
							)
						);

						woocommerce_wp_checkbox(
							array(
								'id'          => '_forum_category_delete',
								'label'       => __( 'Remove Relationship?', 'woocommerce-freshdesk' ),
								'cbvalue'     => 'yes',
								'value'       => '',
								'description' => __( 'This option remove the synchronization with this Forum Category and your Freshdesk.', 'woocommerce-freshdesk' )
							)
						);
					}

					woocommerce_wp_text_input(
						array(
							'id'          => '_forum_category_title',
							'label'       => __( 'Category Title', 'woocommerce-freshdesk' ),
							'value'       => sanitize_text_field( $forum_title ),
							'desc_tip'    => 'true',
							'description' => __( 'Enter with the Forum Category title.', 'woocommerce-freshdesk' )
						)
					);

					woocommerce_wp_textarea_input(
						array(
							'id'          => '_forum_category_description',
							'label'       => __( 'Category Description', 'woocommerce-freshdesk' ),
							'value'       => esc_attr( $forum_description ),
							'desc_tip'    => 'true',
							'description' => __( 'Enter with the Forum Category description.', 'woocommerce-freshdesk' )
						)
					);

				echo '</div>';
			}

			echo '<div class="options_group">';
			echo '<h4>' . esc_html__( 'Solution Category', 'woocommerce-freshdesk' ) . '</h4>';

				if ( 'yes' !== $solutions_enable ) {
					woocommerce_wp_select(
						array(
							'id'          => '_solutions_category',
							'label'       => __( 'Add/get Category', 'woocommerce-freshdesk' ),
							'cbvalue'     => 'yes',
							'value'       => '',
							'desc_tip'    => 'true',
							'description' => __( 'Create or synchronize a Freshdesk Solutions Category.', 'woocommerce-freshdesk' ),
							'options'     => array(
								''       => __( 'Select an option&hellip;', 'woocommerce-freshdesk' ),
								'create' => __( 'Create', 'woocommerce-freshdesk' ),
								'sync'   => __( 'Synchronize', 'woocommerce-freshdesk' )
							)
						)
					);

					woocommerce_wp_text_input(
						array(
							'id'          => '_solutions_category_id',
							'label'       => __( 'Category ID', 'woocommerce-freshdesk' ),
							'value'       => esc_attr( $solutions_id ),
							'desc_tip'    => 'true',
							'description' => __( 'Enter the Solutions Category ID to synchronize.', 'woocommerce-freshdesk' )
						)
					);
				} else {
					echo '<p class="category-id">' . esc_html__( 'Solution Category ID:', 'woocommerce-freshdesk' ) . ' <code>' . esc_attr( $solutions_id ) . '</code></p>';

					woocommerce_wp_hidden_input(
						array(
							'id'    => '_solutions_category_enable',
							'value' => sanitize_text_field( $solutions_enable )
						)
					);

					woocommerce_wp_checkbox(
						array(
							'id'          => '_solutions_category_delete',
							'label'       => __( 'Remove Relationship?', 'woocommerce-freshdesk' ),
							'cbvalue'     => 'yes',
							'value'       => '',
							'description' => __( 'This option remove the synchronization with this Solutions Category and your Freshdesk.', 'woocommerce-freshdesk' )
						)
					);
				}

				woocommerce_wp_text_input(
					array(
						'id'          => '_solutions_category_title',
						'label'       => __( 'Category Title', 'woocommerce-freshdesk' ),
						'value'       => sanitize_text_field( $solutions_title ),
						'desc_tip'    => 'true',
						'description' => __( 'Enter with the Solutions Category title.', 'woocommerce-freshdesk' )
					)
				);

				woocommerce_wp_textarea_input(
					array(
						'id'          => '_solutions_category_description',
						'label'       => __( 'Category Description', 'woocommerce-freshdesk' ),
						'value'       => esc_attr( $solutions_description ),
						'desc_tip'    => 'true',
						'description' => __( 'Enter with the Solutions Category description.', 'woocommerce-freshdesk' )
					)
				);

			echo '</div>';

		echo '</div>';
	}

	/**
	 * Save the product data panel content.
	 *
	 * @param  int    $post_id Post ID.
	 * @param  object $post    Post data.
	 *
	 * @return null
	 */
	public function save_product_panel( $post_id, $post ) {
		// Verify nonce.
		if ( ! isset( $_POST[ 'woocommerce_freshdesk_nonce' ] ) || ! wp_verify_nonce( $_POST[ 'woocommerce_freshdesk_nonce' ], basename( __FILE__ ) ) ) {
			return;
		}

		// Verify if this is an auto save routine.
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return $post_id;
		}

		$forum     = new WC_Freshdesk_Forum_Category( $this->url, $this->api_key, $this->debug );
		$solutions = new WC_Freshdesk_Solutions_Category( $this->url, $this->api_key, $this->debug );

		// Forum category.
		if ( isset( $_POST['_forum_category'] ) && 'sync' === $_POST['_forum_category'] ) {
			$category_id = isset( $_POST['_forum_category_id'] ) ? $_POST['_forum_category_id'] : '';

			if ( ! empty( $category_id ) ) {
				$category_data = $forum->get_category( $post_id, $category_id );
			} else {
				update_post_meta(
					$post_id,
					'_integration_messages',
					__( 'Forum Category ID is required.', 'woocommerce-freshdesk' )
				);
			}

		// Delete the forum category.
		} else if ( isset( $_POST['_forum_category_delete'] ) && 'yes' === $_POST['_forum_category_delete'] ) {
			delete_post_meta( $post_id, '_forum_category' );
			delete_post_meta( $post_id, '_forum_category_id' );
		} else {
			// Save or update the forum category.
			if (
				isset( $_POST['_forum_category_enable'] ) && 'yes' === $_POST['_forum_category_enable']
				||
				isset( $_POST['_forum_category'] ) && 'create' === $_POST['_forum_category']
			) {
				$name        = isset( $_POST['_forum_category_title'] ) ? $_POST['_forum_category_title'] : '';
				$description = isset( $_POST['_forum_category_description'] ) ? $_POST['_forum_category_description'] : '';

				if ( ! empty( $name ) ) {
					$forum->sync_category( $post_id, $name, $description );
				} else {
					update_post_meta(
						$post_id,
						'_integration_messages',
						__( 'Forum Category Title is required.', 'woocommerce-freshdesk' )
					);
				}
			}
		}

		// Solutions category
		if ( isset( $_POST['_solutions_category'] ) && 'sync' === $_POST['_solutions_category'] ) {
			$category_id = isset( $_POST['_solutions_category_id'] ) ? $_POST['_solutions_category_id'] : '';

			if ( ! empty( $category_id ) ) {
				$category_data = $solutions->get_category( $post_id, $category_id );
			} else {
				update_post_meta(
					$post_id,
					'_integration_messages',
					__( 'Forum Category ID is required.', 'woocommerce-freshdesk' )
				);
			}

		// Delete the solutions category.
		} else if ( isset( $_POST['_solutions_category_delete'] ) && 'yes' === $_POST['_solutions_category_delete'] ) {
			delete_post_meta( $post_id, '_solutions_category' );
			delete_post_meta( $post_id, '_solutions_category_id' );
		} else {
			// Save or update the solutions category.
			if (
				isset( $_POST['_solutions_category_enable'] ) && 'yes' === $_POST['_solutions_category_enable']
				||
				isset( $_POST['_solutions_category'] ) && 'create' === $_POST['_solutions_category']
			) {
				$name        = isset( $_POST['_solutions_category_title'] ) ? $_POST['_solutions_category_title'] : '';
				$description = isset( $_POST['_solutions_category_description'] ) ? $_POST['_solutions_category_description'] : '';

				if ( ! empty( $name ) ) {
					$solutions->sync_category( $post_id, $name, $description );
				} else {
					update_post_meta(
						$post_id,
						'_integration_messages',
						__( 'Solutions Category Title is required.', 'woocommerce-freshdesk' )
					);
				}
			}
		}
	}

	/**
	 * Displays notices in admin.
	 *
	 * @return string Error notices.
	 */
	public function admin_notices() {
		global $post;

		if ( ! isset( $_GET['ticket_status'] ) ) {
			return;
		}

		$screen = get_current_screen();

		if ( 'product' === $screen->id ) {
			$messages = get_post_meta( $post->ID, '_integration_messages', true );

			if ( ! empty( $messages ) ) {
				echo '<div class="error"><p><strong>' . __( 'Freshdesk Error', 'woocommerce-freshdesk' ) . ':</strong> ' . esc_attr( $messages ) . '</p></div>';
				delete_post_meta( $post->ID, '_integration_messages' );
			}
		}

		if ( $this->is_comments_screen( $screen->id ) ) {
			$titles = array(
				'success' => esc_html__( 'Freshdesk', 'woocommerce-freshdesk' ),
				'error'   => esc_html__( 'Freshdesk Error', 'woocommerce-freshdesk' ),
			);

			// Messages.
			$messages = array(
				0 => array(
					'title'   => $titles['error'],
					'message' => esc_html__( 'Failed to create the ticket, please try again.', 'woocommerce-freshdesk' ),
				),
				1 => array(
					'title'   => $titles['success'],
					'message' => esc_html__( 'Ticket created successfully.', 'woocommerce-freshdesk' ),
				),
				2 => array(
					'title'   => $titles['error'],
					'message' => esc_html__( 'This comment has not a valid email address.', 'woocommerce-freshdesk' ),
				),
				3 => array(
					'title'   => $titles['error'],
					'message' => esc_html__( 'This review/comment is empty, needs some content to create the ticket!', 'woocommerce-freshdesk' ),
				),
			);

			$ticket_status = intval( $_GET['ticket_status'] );

			if ( isset( $messages[ $ticket_status ] ) ) {
				$ticket_id  = isset( $_GET['ticket_id'] ) ? intval( $_GET['ticket_id'] ) : 0;
				$ticket_url = ( $ticket_id > 0 ) ? ' <a href="https://' . esc_url( $this->get_option( 'url' ) ) . '.freshdesk.com/helpdesk/tickets/' . esc_attr( $ticket_id ) . '">' . esc_html__( 'View ticket.', 'woocommerce-freshdesk' ) . '</a>' : '';

				$message = ( 1 === $ticket_status ) ? $messages[ $ticket_status ]['message'] . $ticket_url : $messages[ $ticket_status ]['message'];
				$class   = ( 1 === $ticket_status ) ? 'updated' : 'error';

				echo '<div class="' . esc_attr( $class ) . '"><p><strong>' . esc_html( $messages[ $ticket_status ]['title'] ) . ':</strong> ' . esc_html( $message ) . '</p></div>';
			}
		}
	}

	/**
	 * Create ticket form.
	 *
	 * @param  int    $order_id Order ID.
	 *
	 * @return string           Ticket HTML form.
	 */
	public function view_order_create_ticket( $order_id ) {
		echo '<h2 id="open-ticket">' . esc_html__( 'Need Help?', 'woocommerce-freshdesk' ) . '</h2>';

		echo '<p>' . apply_filters( 'woocommerce_freshdesk_ticket_form_description', esc_html__( 'Do you have a query about your order, or need a hand with getting your products set up? If so, please fill in the form below.', 'woocommerce-freshdesk' ) ) . '</p>';

		echo '<form method="post" id="wc-freshdesk-ticket-form">';
			do_action( 'woocommerce_freshdesk_ticket_form_start' );

			echo '<p class="form-row form-row-wide">';
				echo '<label for="ticket-subject">' . esc_html__( 'Subject', 'woocommerce-freshdesk' ) . ' <span class="required">*</span></label>';
				echo '<input type="text" class="input-text ticket-field" name="ticket_subject" id="ticket-subject" required="required" />';
			echo '</p>';

			echo '<p class="form-row form-row-wide">';
				echo '<label for="ticket-description">' . esc_html__( 'Description', 'woocommerce-freshdesk' ) . ' <span class="required">*</span></label>';
				echo '<textarea name="ticket_description" class="ticket-field" id="ticket-description" rows="10" cols="50" required="required"></textarea>';
			echo '</p>';

			do_action( 'woocommerce_freshdesk_ticket_form' );

			echo '<p class="form-row">';
				echo '<input type="hidden" class="ticket-field" name="ticket_order_id" id="ticket-order-id" value="' . intval( $order_id ) . '" />';
				echo '<input type="submit" class="button" name="ticket_send" value="' . esc_html__( 'Send', 'woocommerce-freshdesk' ) . '" />';
			echo '</p>';

			do_action( 'woocommerce_freshdesk_ticket_form_end' );
		echo '</form>';
	}

	/**
	 * Added support button in order actions.
	 *
	 * @param  array    $actions Order actions.
	 * @param  WC_Order $order   Order data.
	 *
	 * @return array
	 */
	public function orders_actions( $actions, $order ) {
		$order_url = $order->get_view_order_url();

		$actions[ $this->id ] = array(
			'url'  => $order_url . '#open-ticket',
			'name' => __( 'Get Help', 'woocommerce-freshdesk' )
		);

		return $actions;
	}

	/**
	 * Display the support tickets in myaccount page.
	 *
	 * @return string
	 */
	public function support_tickets() {
		if ( ! apply_filters( 'woocommerce_freshdesk_hide_my_support_tickets', false ) ) {
			global $current_user;

			$email = $current_user->user_email;
			$tickets = new WC_Freshdesk_Tickets( $this->url, $this->api_key, $this->debug );

			echo '<h2 id="support-tickets-title">' . esc_html__( 'My Support Tickets', 'woocommerce-freshdesk' ) . '</h2>';

			echo $tickets->tickets_table( $email );
		}
	}

	/**
	 * Remote Authentication in Freshdesk.
	 *
	 * @param  string $redirect Redirect url.
	 * @param  object $user     Current user data.
	 *
	 * @return string           Make the login and Freshdesk and return to WooCommerce.
	 */
	public function sso_login_redirect( $redirect, $user ) {
		if ( ! empty( $this->sso_secret ) ) {
			// Make sure redirect contains absolute URL.
			$redirect = home_url( str_replace( home_url(), '', $redirect ) );
			return $this->sso_login_url( $user->display_name, $user->user_email, $redirect );
		}

		return $redirect;
	}

	/**
	 * Handler for Reply to ticket URL redirection
	 */
	public function reply_to_ticket_redirect() {
		if ( empty( $_POST['reply-ticket-freshdesk'] ) ) {
			return;
		}

		$user = wp_get_current_user();

		// In order to reply to a ticket, require that the user is already logged on the instance.
		if ( empty( $user->ID ) ) {
			return;
		}

		$ticket_id = absint( $_POST['reply-ticket-freshdesk'] );
		$ticket_url = 'https://' . $this->get_option( 'url' ) . '.freshdesk.com/helpdesk/tickets/' . $ticket_id;

		// If SSO is enabled, use the redirect url for it
		if ( ! empty( $this->sso_secret ) ) {
			$ticket_url = $this->sso_login_url( $user->display_name, $user->user_email, $ticket_url );
		}

		wp_redirect( $ticket_url );
		exit;
	}

	/**
	 * Add our SSO host to list of valid WP redirect hosts.
	 *
	 * @param  array $hosts
	 *
	 * @return array
	 */
	public function allow_freshdesk_host( $hosts ) {
		$hosts[] = $this->sso_url;
		$hosts[] = parse_url( $this->sso_url, PHP_URL_HOST );
		return $hosts;
	}

	/**
	 * Adds a metabox for create tickets for orders.
	 *
	 * @return void
	 */
	public function add_order_tickets_metabox() {
		add_meta_box(
			$this->id . '-tickets',
			__( 'Report an issue', 'woocommerce-freshdesk' ),
			array( $this, 'order_tickets_metabox_content' ),
			Compat_Utils::get_order_admin_screen(),
			'side',
			'low'
		);
	}

	/**
	 * Order tickets metabox content.
	 *
	 * @param  int    $post_id Current order/post ID.
	 *
	 * @return string          Ticket fields.
	 */
	public function order_tickets_metabox_content( $post_id ) {
		echo '<div id="order-tickets-fields">';
			echo '<p>' . esc_html__( 'Discuss an issue with your customer with a support ticket.', 'woocommerce-freshdesk' ) . '</p>';

			do_action( 'woocommerce_freshdesk_ticket_admin_form_start' );

			echo '<p>';
				echo '<label for="ticket-subject">' . esc_html__( 'Subject', 'woocommerce-freshdesk' ) . ' <span class="required">' . esc_html__( '(required)', 'woocommerce-freshdesk' ) . '</span></label>';
				echo '<input type="text" class="ticket-field" id="ticket-subject" name="ticket_subject" />';
			echo '</p>';

			echo '<p>';
				echo '<label for="ticket-description">' . esc_html__( 'Description', 'woocommerce-freshdesk' ) . ' <span class="required">' . esc_html__( '(required)', 'woocommerce-freshdesk' ) . '</span></label>';
				echo '<textarea id="ticket-description" class="ticket-field" name="ticket_description" cols="25" rows="5"></textarea>';
			echo '</p>';

			do_action( 'woocommerce_freshdesk_ticket_admin_form' );

			echo '<p>';
				echo '<a id="open-ticket" href="#" class="button button-primary">' . esc_html__( 'Open ticket', 'woocommerce-freshdesk' ) . '</a>';
			echo '</p>';

			do_action( 'woocommerce_freshdesk_ticket_admin_form_end' );

		echo '</div>';
	}

	/**
	 * Add actions item in comments columns.
	 *
	 * @param  array $columns Default columns.
	 * @return array Add actions column.
	 */
	public function comments_columns( $columns ) {
		$columns[ $this->id . '_comment_actions' ] = __( 'Actions', 'woocommerce-freshdesk' );

		return $columns;
	}

	/**
	 * Add actions content to comments columns.
	 *
	 * @param string $column     Column name.
	 * @param int    $comment_id Comment ID.
	 */
	public function custom_comment_column( $column, $comment_id ) {
		if ( $this->id . '_comment_actions' === $column ) {
			$this->output_comment_actions_column( $comment_id );
		}
	}

	/**
	 * Add actions content to comments columns.
	 *
	 * @since 1.3.0
	 *
	 * @param WP_Comment $comment Comment.
	 */
	public function custom_review_column( $comment ) {
		$this->output_comment_actions_column( $comment->comment_ID );
	}

	/**
	 * Outputs the content for the actions column in the comments screen.
	 *
	 * @since 1.3.0
	 *
	 * @param int $comment_id Comment ID.
	 */
	protected function output_comment_actions_column( $comment_id ) {
		$url = add_query_arg(
			array(
				'comment_to_ticket' => $comment_id,
				'ticket_id'         => false,
				'ticket_status'     => false,
			)
		);

		printf(
			'<p><a class="button" href="%1$s">%2$s</a></p>',
			esc_url( $url ),
			esc_html__( 'Convert to Ticket', 'woocommerce-freshdesk' )
		);
	}

	/**
	 * Retrieves the last order of a customer from a comment.
	 *
	 * @param  string $customer_email Customer email.
	 * @param  int    $user_id        User ID.
	 * @param  int    $product_id     Product ID.
	 * @return int                    Last order ID.
	 */
	protected function get_order_from_comment( $customer_email, $user_id, $product_id ) {
		global $wpdb;

		$emails = array();

		if ( $user_id ) {
			$user     = get_user_by( 'id', $user_id );
			$emails[] = $user->user_email;
		}

		if ( is_email( $customer_email ) ) {
			$emails[] = $customer_email;
		}

		if ( sizeof( $emails ) == 0 ) {
			return 0;
		}

		if ( Compat_Utils::is_custom_order_tables_enabled() ) {
			$orders_meta_table     = $wpdb->prefix . 'wc_orders_meta';
			$orders_meta_id_column = 'order_id';
		} else {
			$orders_meta_table     = $wpdb->postmeta;
			$orders_meta_id_column = 'post_id';
		}

		return $wpdb->get_var(
			$wpdb->prepare(
				"SELECT order_items.order_id
				FROM {$wpdb->prefix}woocommerce_order_items as order_items
				LEFT JOIN {$wpdb->prefix}woocommerce_order_itemmeta AS itemmeta ON order_items.order_item_id = itemmeta.order_item_id
				LEFT JOIN $orders_meta_table AS orders_meta ON order_items.order_id = orders_meta.$orders_meta_id_column
				WHERE
					itemmeta.meta_value = %s AND
					itemmeta.meta_key IN ( '_variation_id', '_product_id' ) AND
					orders_meta.meta_key IN ( '_billing_email', '_customer_user' ) AND
					(
						orders_meta.meta_value  IN ( '" . implode( "','", array_unique( $emails ) ) . "' ) OR
						(
							orders_meta.meta_value = %s AND
							orders_meta.meta_value > 0
						)
					)
				ORDER BY order_items.order_id DESC
				LIMIT 1",
				$product_id,
				$user_id
			)
		);
	}

	/**
	 * Comments to ticket.
	 */
	public function comment_to_ticket() {
		if ( ! isset( $_GET['comment_to_ticket'] ) ) {
			return;
		}

		global $wpdb;
		$comment_id = intval( $_GET['comment_to_ticket'] );

		// Get the post ID from the comment ID.
		$post_id = $wpdb->get_var(
			$wpdb->prepare( "
				SELECT comment_post_ID
				FROM $wpdb->comments
				WHERE comment_ID = %d
			", $comment_id )
		);

		// Post not found.
		if ( empty( $post_id ) ) {
			return;
		}

		// Get the post type.
		$post_type = get_post_type( $post_id );

		// Sets the ticket params.
		$ticket = new WC_Freshdesk_Tickets( $this->url, $this->api_key, $this->debug );

		// Sets the ticket data.
		$comment     = get_comment( $comment_id );
		$email       = $comment->comment_author_email;
		$subject     = sprintf( __( 'Comment in %s at %s', 'woocommerce-freshdesk' ), get_the_title( $post_id ), $comment->comment_date );
		$subject     = apply_filters( 'woocommerce_freshdesk_ticket_from_comment_subject', $subject, $comment );
		$description = $comment->comment_content;

		$redirect_url = remove_query_arg( array( 'comment_to_ticket', 'ticket_id' ) );

		// Test the email.
		if ( ! is_email( $email ) ) {
			wp_safe_redirect( esc_url_raw( add_query_arg( array( 'ticket_status' => 2 ), $redirect_url ) ) );
			exit;
		}

		// Test the content.
		if ( empty( $description ) ) {
			wp_safe_redirect( esc_url_raw( add_query_arg( array( 'ticket_status' => 3 ), $redirect_url ) ) );
			exit;
		}

		// Get post data.
		if ( 'product' == $post_type ) {
			$order    = null;
			$is_order = false;
			$order_id = $this->get_order_from_comment( $comment->comment_author_email, $comment->user_id, $comment->comment_post_ID );
			if ( $order_id ) {
				$order    = wc_get_order( $order_id );
				$is_order = true;
			}

			$response = $ticket->open_ticket_from_comment( $email, $subject, $description, $is_order, $order );
		} else {
			$response = $ticket->open_ticket_from_comment( $email, $subject, $description );
		}

		// Redirects to avoid reloads and displaying the success message.
		wp_safe_redirect(
			esc_url_raw(
				add_query_arg(
					array(
						'ticket_status' => $response['status'],
						'ticket_id'     => $response['id'],
					),
					$redirect_url
				)
			)
		);
		exit;
	}

	/**
	 * Download the log file.
	 *
	 * @return file
	 */
	public function log_downloader() {
		if (
			isset( $_GET['page'] ) && ( 'wc-settings' == $_GET['page'] || 'woocommerce_settings' == $_GET['page'] )
			&& isset( $_GET['tab'] ) && 'integration' == $_GET['tab']
			&& isset( $_GET['section'] ) && $this->id == $_GET['section']
			&& isset( $_GET['viewlog'] ) && '1' == $_GET['viewlog']
			&& current_user_can( 'manage_woocommerce' )
		) {
			$wc_path  = plugin_dir_path( WC_PLUGIN_FILE );
			$log_name = $this->id . '-' . sanitize_file_name( wp_hash( $this->id ) ) . '.txt';
			$log_file = $wc_path . 'logs/' . $log_name;

			if ( file_exists( $log_file ) ) {
				header( 'Cache-Control: public' );
				header( 'Content-Description: File Transfer' );
				header( 'Content-Disposition: attachment; filename=' . $log_name );
				header( 'Content-Type: text/plain' );
				header( 'Content-Transfer-Encoding: binary' );
				readfile( $log_file );
				exit;
			} else {
				wp_die( __( 'The Freshdesk log file was not created yet!', 'woocommerce-freshdesk' ) );
			}
		}

	}
}
