<?php
/**
* The admin-specific functionality of the plugin.
*
* @link       https://wpdeveloper.net
* @since      1.0.0
*
* @package    BetterDocs
* @subpackage BetterDocs/admin
* @author     WPDeveloper <support@wpdeveloper.net>
*/

class BetterDocs_Admin {
	
	/**
	* The ID of this plugin.
	*
	* @since    1.0.0
	* @access   private
	* @var      string    $plugin_name    The ID of this plugin.
	*/
	private $plugin_name;
	private $menu_slug;
	/**
	* All builder args
	*
	* @var array
	*/
	private $builder_args;
	/**
	* Builder Metabox ID
	*
	* @var string
	*/
	private $metabox_id;
	
	/**
	* The version of this plugin.
	*
	* @since    1.0.0
	* @access   private
	* @var      string    $version    The current version of this plugin.
	*/
	private $version;
	
	/**
	* The type.
	*
	* @since    1.0.0
	* @access   public
	* @var string the post type of betterdocs.
	*/
	public $type = 'docs';
	
	public $metabox;
	
	public static $prefix = 'betterdocs_meta_';
	
	public static $settings;
	
	/**
	* Initialize the class and set its properties.
	*
	* @since    1.0.0
	* @param      string    $plugin_name       The name of this plugin.
	* @param      string    $version    The version of this plugin.
	*/
	public static $counts;
	
	public static $enabled_types = [];
	public static $active_items = [];

	public function __construct( $plugin_name, $version ) {
		
		$this->plugin_name = $plugin_name;
		$this->version = $version;
		$this->metabox = new BetterDocs_MetaBox();
		self::$settings = BetterDocs_DB::get_settings();
	}
	
	/**
	* Register the stylesheets for the admin area.
	*
	* @since    1.0.0
	*/
	public function enqueue_styles( $hook ) {
		$page_status = false;
		wp_enqueue_style( 
			$this->plugin_name . '-admin-global', 
			BETTERDOCS_ADMIN_URL . 'assets/css/betterdocs-global.css', 
			array(), $this->version, 'all' 
		);
		if( $hook == 'betterdocs_page_betterdocs-settings' || $hook == 'toplevel_page_betterdocs-admin' ) {
			$page_status = true;
		}
		
		if( ! $page_status ) {
			return;
		}
		
		wp_enqueue_style( 
			$this->plugin_name . '-select2', 
			BETTERDOCS_ADMIN_URL . 'assets/css/select2.min.css', 
			array(), $this->version, 'all' 
		);
		wp_enqueue_style( 
			$this->plugin_name, 
			BETTERDOCS_ADMIN_URL . 'assets/css/betterdocs-admin.css', 
			array(), $this->version, 'all' 
		);
	}
	/**
	* Register the JavaScript for the admin area.
	*
	* @since    1.0.0
	*/
	public function enqueue_scripts( $hook ) {
		wp_enqueue_script( 'wp-color-picker' );
		// wp_enqueue_script( 'jquery-ui-datepicker' );
		// wp_enqueue_media();
		wp_enqueue_script(
			$this->plugin_name . '-select2', 
			BETTERDOCS_ADMIN_URL . 'assets/js/select2.min.js', 
			array( 'jquery' ), $this->version, true 
		);
		wp_enqueue_script(
			$this->plugin_name . '-swal', 
			BETTERDOCS_ADMIN_URL . 'assets/js/sweetalert.min.js', 
			array( 'jquery' ), $this->version, true 
		);
		wp_enqueue_script( 
			$this->plugin_name, 
			BETTERDOCS_ADMIN_URL . 'assets/js/betterdocs-admin.js', 
			array( 'jquery' ), $this->version, true 
		);
		wp_localize_script( $this->plugin_name, 'betterdocsAdminConfig', self::toggleFields() );
		
	}

	public function toggleFields( $builder = false ){
		$args = BetterDocs_Settings::settings_args();

		$toggleFields = $hideFields = $conditions = array();

		$tabs = $args;
		if( ! empty( $tabs ) ) {
			foreach( $tabs as $tab_id => $tab ) {
				$sections = isset( $tab['sections'] ) ? $tab[ 'sections' ] : [];
				if( ! empty( $sections ) ) {
					foreach( $sections as $section_id => $section ) {
						$fields = isset( $section['fields'] ) ? $section[ 'fields' ] : [];
						if( isset( $section['tabs'] ) && ! empty( $section['tabs'] ) ) {
							foreach( $section['tabs'] as $inner_field_tab_key => $inner_field_tab ) {
								if( isset( $inner_field_tab['fields'] ) ) {
									foreach( $inner_field_tab['fields'] as $inner_tab_field_key => $inner_tab_field_tab ) {
										if( isset( $inner_tab_field_tab['hide'] ) && ! empty( $inner_tab_field_tab['hide'] ) && is_array( $inner_tab_field_tab['hide'] ) ) {
											$hideFields = $this->a_walk( $inner_tab_field_tab['hide'], $inner_tab_field_key, $hideFields );
										}
										if( isset( $inner_tab_field_tab['dependency'] ) && ! empty( $inner_tab_field_tab['dependency'] ) && is_array( $inner_tab_field_tab['dependency'] ) ) {
											$conditions = $this->a_walk( $inner_tab_field_tab['dependency'], $inner_tab_field_key, $conditions );
										}
									}
								}
							}
						}
						if( ! empty( $fields ) ) {
							foreach( $fields as $field_key => $field ) {
								if( isset( $field['fields'] ) ) {
									$iFields =  $field['fields'];
									foreach( $iFields as $inner_field_key => $inner_field ) {
										if( isset( $inner_field['hide'] ) && ! empty( $inner_field['hide'] ) && is_array( $inner_field['hide'] ) ) {
											$hideFields = $this->a_walk( $inner_field['hide'], $inner_field_key, $hideFields );
										}
										if( isset( $inner_field['dependency'] ) && ! empty( $inner_field['dependency'] ) && is_array( $inner_field['dependency'] ) ) {
											$conditions = $this->a_walk( $inner_field['dependency'], $inner_field_key, $conditions );
										}
									}
								}
								if( isset( $field['hide'] ) && ! empty( $field['hide'] ) && is_array( $field['hide'] ) ) {
									$hideFields = $this->a_walk( $field['hide'], $field_key, $hideFields );
								}
								if( isset( $field['dependency'] ) && ! empty( $field['dependency'] ) && is_array( $field['dependency'] ) ) {
									$conditions = $this->a_walk( $field['dependency'], $field_key, $conditions );
								}
							}
						}
					}
				}
			}
		}

		return array( 
			'toggleFields' => $conditions, // TODO: toggling system has to be more optimized! 
			'hideFields' => $hideFields, 
		);
	}
	public function a_walk( $array, $field_key, &$returned_array = [] ){
		array_walk( $array, function( $value, $key ) use ( $field_key, &$returned_array ) {
			$returned_array[ $field_key ][ $key ] = $value;
		} );

		return $returned_array;
	}
	/**
	* Admin Menu Page
	*
	* @return void
	*/
	public function menu_page(){		
		$settings_class = new betterdocs_settings();
		$singular_name = BetterDocs_DB::get_settings('breadcrumb_doc_title');

		$betterdocs_articles_caps = apply_filters( 'betterdocs_articles_caps', 'edit_posts', 'article_roles' );
		$betterdocs_terms_caps = apply_filters( 'betterdocs_terms_caps', 'delete_others_posts', 'article_roles' );
		$betterdocs_settings_caps = apply_filters( 'betterdocs_settings_caps', 'administrator', 'settings_roles' );

		$settings = apply_filters( 'betterdocs_admin_menu', array(
			'betterdocs-setup'   => array(
				'title'      => __('Quick Setup', 'betterdocs'),
				'capability' => 'delete_users',
				'callback'   => 'BetterDocsSetupWizard::plugin_setting_page'
			),
			'betterdocs-settings'   => array(
				'title'      => __('Settings', 'betterdocs'),
				'capability' => $betterdocs_settings_caps,
				'callback'   => array( $settings_class, 'settings_page' )
			),
		) );

		$this->menu_slug = apply_filters( 'betterdocs_menu_slug',  'edit.php?post_type=docs' );
		$betterdocs_admin_output = apply_filters( 'betterdocs_admin_output', array() );

		add_menu_page( 
			'BetterDocs', 'BetterDocs', 
			$betterdocs_articles_caps, $this->menu_slug, $betterdocs_admin_output, 
			BETTERDOCS_ADMIN_URL.'/assets/img/betterdocs-icon-white.svg', 5 
		);
		add_submenu_page( 
			$this->menu_slug, '', 
			__( 'All Articles', 'betterdocs' ), 
			$betterdocs_articles_caps, $this->menu_slug 
		);
		add_submenu_page( $this->menu_slug, __('Add New', 'betterdocs'), __('Add New', 'betterdocs'), $betterdocs_articles_caps, 'post-new.php?post_type=docs');
		add_submenu_page( 
			$this->menu_slug, __('Categories', 'betterdocs'), __('Categories', 'betterdocs'), 
			$betterdocs_terms_caps, 'edit-tags.php?taxonomy=doc_category&post_type=docs'
		);
		add_submenu_page(
			$this->menu_slug, __('Tags', 'betterdocs'), __('Tags', 'betterdocs'), 
			$betterdocs_terms_caps, 'edit-tags.php?taxonomy=doc_tag&post_type=docs'
		);
		
		foreach( $settings as $slug => $setting ) {
			$cap  = isset( $setting['capability'] ) ? $setting['capability'] : 'delete_users';
			$hook = add_submenu_page( $this->menu_slug, $setting['title'], $setting['title'], $cap, $slug, $setting['callback'] );
		}
	}

	public function highlight_admin_menu( $parent_file ){
		global $current_screen;
		if( $this->menu_slug === 'betterdocs-admin' && in_array( $current_screen->id, array( 'edit-doc_tag', 'edit-doc_category' ) ) ) {
			$parent_file = 'betterdocs-admin';
		} else {
			if( in_array( $current_screen->id, array( 'edit-doc_tag', 'edit-doc_category' ) ) ) {
				$parent_file = 'edit.php?post_type=docs';
			}
		}
        return $parent_file;
	}

	public function highlight_admin_submenu( $parent_file, $submenu_file ){
		global $current_screen, $pagenow;
        if ( $current_screen->post_type == 'docs' ) {
            if ( $pagenow == 'post.php' ) {
                $submenu_file = 'edit.php?post_type=docs';
			}
			if ( $pagenow == 'post-new.php' ) {
                $submenu_file = 'post-new.php?post_type=docs';
			}
			if( $current_screen->id === 'edit-doc_category' ) {
				$submenu_file = 'edit-tags.php?taxonomy=doc_category&post_type=docs';
			}
			if( $current_screen->id === 'edit-doc_tag' ) {
				$submenu_file = 'edit-tags.php?taxonomy=doc_tag&post_type=docs';
			}
			if( $current_screen->id === 'edit-knowledge_base' ) {
				$submenu_file = 'edit-tags.php?taxonomy=knowledge_base&post_type=docs';
			}
		}
		if( 'betterdocs_page_betterdocs-settings' == $current_screen->id ) {
			$submenu_file = 'betterdocs-settings';
		}
		if( 'betterdocs_page_betterdocs-setup' == $current_screen->id ) {
			$submenu_file = 'betterdocs-setup';
		}

        return $submenu_file;
	}

				
	public function quick_builder() {
		$builder_args = $this->builder_args;
		$tabs         = $this->builder_args['tabs'];
		$prefix       = self::$prefix;
		$metabox_id   = $this->metabox_id;
		/**
		* This lines of code is for editing a notification in simple|quick builder
		*
		* @var  [type]
		*/
		$idd = null;
		if( isset( $_GET['post_id'] ) && ! empty( $_GET['post_id'] )) {
			$idd = intval( $_GET['post_id'] );
		}
		include_once BETTERDOCS_ADMIN_DIR_PATH . 'partials/betterdocs-quick-builder-display.php';
	}
	/**
	* Generate the builder data acording to default meta data
	*
	* @param array $data
	* @return array
	*/
	protected function builder_data( $data ) {
		$post_data   = [];
		$prefix      = self::$prefix;
		$meta_fields = BetterDocs_MetaBox::get_metabox_fields( $prefix );
		foreach( $meta_fields as $meta_key => $meta_field ) {
			if( in_array( $meta_key, array_keys($data) ) ) {
				$post_data[ $meta_key ] = $data[ $meta_key ];
			} else {
				$post_data[ $meta_key ] = '';
				
				if( isset( $meta_field['defaults'] ) ) {
					$post_data[ $meta_key ] = $meta_field['defaults'];
				}
				if( isset( $meta_field['default'] ) ) {
					$post_data[ $meta_key ] = $meta_field['default'];
				}
			}
		}
		
		return array_merge( $post_data, $data );
	}
				
	public static function get_form_action( $query_var = '', $builder_form = false ) {
		$page = '/edit.php?post_type=docs&page=betterdocs-settings';
		
		if ( is_network_admin() ) {
			return network_admin_url( $page . $query_var );
		} else {
			return admin_url( $page . $query_var );
		}
	}
				
	
	/**
	 * Admin Init For User Interactions
	 * @return void
	 */
	public function admin_init( $hook ){
		/**
		 * BetterDocs Admin URL
		 */
		$current_url = admin_url('edit.php?post_type=docs&page=betterdocs-settings');
	}
	public function toolbar_menu( $admin_bar ){
		if ( ! is_admin() || ! is_admin_bar_showing() ) {
			return;
		}

		// Show only when the user is a member of this site, or they're a super admin.
		if ( ! is_user_member_of_blog() && ! is_super_admin() ) {
			return;
		}

		$saved_settings = BetterDocs_DB::get_settings();
		if( isset( $saved_settings['builtin_doc_page'] ) && intval( $saved_settings['builtin_doc_page'] ) ) {
			$docs_url = isset( $saved_settings['docs_slug'] ) && ! empty( $saved_settings['docs_slug'] ) ? home_url( $saved_settings['docs_slug'] ) : false;
			if( $docs_url ) {
				// Add an option to visit the store.
				$admin_bar->add_node(
					array(
						'parent' => 'site-name',
						'id'     => 'view-docs',
						'title'  => __( 'Visit Documentation', 'betterdocs' ),
						'href'   => $docs_url,
					)
				);
			}
		} elseif (isset( $saved_settings['docs_page'] ) && intval( $saved_settings['docs_page'] )) {
			$docs_page_url = isset( $saved_settings['docs_page'] ) && ! empty( $saved_settings['docs_page'] ) ? get_page_link($saved_settings['docs_page']) : false;
			$admin_bar->add_node(
				array(
					'parent' => 'site-name',
					'id'     => 'view-docs',
					'title'  => __( 'Visit Documentation', 'betterdocs' ),
					'href'   => $docs_page_url,
				)
			);
		}

	}

}
