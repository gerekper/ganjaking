<?php
/**
 * Porto Dynamic Tags Content class
 *
 * @author     P-Themes
 * @since      2.3.0
 */

defined( 'ABSPATH' ) || die;

if ( ! class_exists( 'Porto_Func_Dynamic_Tags_Content' ) ) :

	class Porto_Func_Dynamic_Tags_Content {

		/**
		 * Global Instance Objects
		 *
		 * @var array $instances
		 * @since 2.3.0
		 * @access private
		 */
		private static $instance = null;

		public static function get_instance() {
			if ( ! self::$instance ) {
				self::$instance = new self();
			}
			return self::$instance;
		}

		/**
		 * Current Post for Elementor & Wpb
		 *
		 * @since 2.3.0
		 */
		protected $post;

		/**
		 * Type of Dynamic WPb Tags
		 *
		 * @since 2.3.0
		 */
		public $features = array( 'field', 'link', 'image' );

		/**
		 * Meta Box Types
		 *
		 * @since 2.3.0
		 */
		protected $metabox_types = array(
			'text'           => array( 'field', 'link' ),
			'input'          => array( 'field' ),
			'editor'         => array( 'field' ),
			'textarea'       => array( 'field', 'link' ),
			'number'         => array( 'field' ),
			'range'          => array( 'field' ),
			'date'           => array( 'field' ),
			'email'          => array( 'field' ),
			'url'            => array( 'link' ),
			'image'          => array( 'image' ),
			'image_advanced' => array( 'image' ),
			'video'          => array( 'image' ),
			'file_advanced'  => array( 'link' ),
			'link'           => array( 'link' ),
			'page_link'      => array( 'link' ),
			'post_object'    => array( 'field' ),
			'taxonomy'       => array( 'field' ),
			'attach'         => array( 'image' ),
			'upload'         => array( 'image' ),
		);

		/**
		 * Meta Box Terms
		 *
		 * @since 2.3.0
		 */
		protected $meta_terms = array(
			'product'   => 'product_cat',
			'portfolio' => 'portfolio_cat',
			'member'    => 'member_cat',
			'post'      => 'category',
		);
		/**
		 * Constructor
		 *
		 * @since 1.0
		 */
		public function __construct() {
			add_action( 'current_screen', array( $this, 'init' ) );

			add_filter( 'porto_dynamic_tags_content', array( $this, 'get_dynamic_content' ), 10, 4 );

			add_action( 'wp_ajax_porto_dynamic_tags_get_value', array( $this, 'get_value' ) );
			add_action( 'wp_ajax_porto_dynamic_tags_acf_fields', array( $this, 'get_acf_fields' ) );

			add_filter( 'porto_builder_get_current_object', array( $this, 'get_dynamic_content_data' ), 10, 2 );

			// Elementor & Wpb
			$this->metabox_types = apply_filters( 'porto_dynamic_meta_types', $this->metabox_types );
			add_action( 'porto_dynamic_before_render', array( $this, 'before_render' ), 10, 2 );
			add_action( 'porto_dynamic_after_render', array( $this, 'after_render' ), 10, 2 );
		}

		/**
		 * Init functions
		 *
		 * @since 2.3.0
		 */
		public function init() {
			if ( class_exists( 'ACF' ) ) {
				$screen = get_current_screen();
				if ( $screen && 'post' == $screen->base ) {
					// add ACF fields
					include_once PORTO_SHORTCODES_LIB . 'dynamic_tags/class-porto-func-acf.php';
				}
			}
		}

		/**
		 * Retrieve dynamic tags content according to its type
		 *
		 * @since 2.3.0
		 */
		public function get_dynamic_content( $default = false, $object = null, $type = 'post', $field = '' ) {
			if ( ! $object ) {
				if ( 'post' == $type ) {
					global $post;
					$object = $post;
				} else {
					if ( ( $current_object = get_queried_object() ) && isset( $current_object->term_id ) ) {
						$object = $current_object;
					} else {
						global $post;
						$object = $post;
					}
				}
			}
			if ( ! $object ) {
				return $default;
			}
			if ( 'post' == $type ) {
				if ( 'content' == $field ) {
					return do_shortcode( $object->post_content );
				} elseif ( 'like_count' == $field ) {
					return esc_html( get_post_meta( $object->ID, 'like_count', true ) );
				} elseif ( $field && isset( $object->{ 'post_' . $field } ) ) {
					return $object->{ 'post_' . $field };
				} elseif ( 'thumbnail' == $field ) {
					return esc_url( get_the_post_thumbnail_url( $object, 'full' ) );
				} elseif ( 'author_img' == $field ) {
					return esc_url( get_avatar_url( get_the_author_meta( 'email' ) ) );
				} elseif ( 'permalink' == $field ) {
					return esc_url( get_permalink( $object ) );
				} elseif ( 'author_posts_url' == $field ) {
					global $authordata;
					if ( is_object( $authordata ) ) {
						return esc_url( get_author_posts_url( $authordata->ID, $authordata->user_nicename ) );
					}
				} else {
					return (int) $object->ID;
				}
			} elseif ( 'metabox' == $type ) {
				if ( ! $field ) {
					$field = 'page_sub_title';
				}
				if ( $object->ID ) {
					return get_post_meta( $object->ID, $field, true );
				} else {
					$result = get_term_meta( $object->term_id, $field, true );
					if ( $result ) {
						return $result;
					}
					return get_metadata( $object->taxonomy, $object->term_id, $field, true );
				}
			} elseif ( 'acf' == $type && $field ) {
				$field_arr = explode( '-', $field );
				if ( 2 === count( $field_arr ) ) {
					if ( isset( $object->term_id ) ) {
						return get_term_meta( $object->term_id, $field_arr[1], true );
					}
					return get_post_meta( $object->ID, $field_arr[1], true );
				}
			} elseif ( 'meta' == $type ) {
				if ( $object->ID ) {
					return get_post_meta( $object->ID, $field, true );
				} else {
					$result = get_term_meta( $object->term_id, $field, true );
					if ( $result ) {
						return $result;
					}
					return get_metadata( $object->taxonomy, $object->term_id, $field, true );
				}
			} elseif ( 'tax' == $type ) {
				if ( $object->term_id ) {
					if ( 'id' == $field ) {
						return (int) $object->term_id;
					} elseif ( 'title' == $field ) {
						return esc_html( $object->name );
					} elseif ( 'desc' == $field ) {
						return $object->description;
					} elseif ( 'count' == $field ) {
						return (int) $object->count;
					} elseif ( 'term_link' == $field ) {
						return esc_url( get_term_link( $object ) );
					}
				}
			} elseif( 'woo' == $type && class_exists('WooCommerce') ) {
				if ( 'sale_date' == $field && function_exists('porto_woocommerce_sale_product_period') ) {
					$result = porto_woocommerce_sale_product_period( wc_get_product( $object->ID ) );
					return $result;
				}
			}

			return $default;
		}

		/**
		 * Returns the dynamic content data
		 *
		 * @since 2.3.0
		 */
		public function get_dynamic_content_data( $builder_id = false, $atts = array() ) {
			$content_type       = false;
			$content_type_value = false;

			if ( isset( $atts['content_type'] ) ) {
				$content_type = $atts['content_type'];
			}
			if ( isset( $atts['content_type_value'] ) ) {
				$content_type_value = $atts['content_type_value'];
			}

			if ( $builder_id ) {
				if ( ! $content_type ) {
					$content_type = get_post_meta( $builder_id, 'content_type', true );
				}
				if ( ! $content_type_value ) {
					if ( $content_type ) {
						$content_type_value = get_post_meta( $builder_id, 'content_type_' . $content_type, true );
					}
				}
			}
			$result = false;

			if ( 'term' == $content_type ) {
				$args = array(
					'hide_empty' => true,
					'number'     => 1,
				);
				if ( $content_type_value ) {
					$args['taxonomy'] = $content_type_value;
				}
				$terms = get_terms( $args );
				if ( is_array( $terms ) && ! empty( $terms ) ) {
					$terms = array_values( $terms );
					return $terms[0];
				}
			} elseif ( $content_type && $content_type_value ) {
				$result = get_post( $content_type_value );
			} else {
				$args = array( 'numberposts' => 1 );
				if ( $content_type ) {
					$args['post_type'] = $content_type;
				}

				$result = get_posts( $args );

				if ( is_array( $result ) && isset( $result[0] ) ) {
					return $result[0];
				}
			}

			return $result;
		}

		/**
		 * Retrieve dynamic tags content from editor
		 *
		 * @since 2.3.0
		 */
		public function get_value() {
			check_ajax_referer( 'porto-nonce', 'nonce' );
			if ( isset( $_POST['content_type'] ) && isset( $_POST['content_type_value'] ) && ! empty( $_POST['source'] ) && ! empty( $_POST['field_name'] ) ) {
				$atts   = array(
					'content_type'       => $_POST['content_type'],
					'content_type_value' => $_POST['content_type_value'],
				);
				$object = $this->get_dynamic_content_data( false, $atts );
				if ( $object ) {
					if ( 'term' == $atts['content_type'] && 'post' == $_POST['source'] ) {
					} elseif ( 'term' != $atts['content_type'] && $atts['content_type'] && 'tax' == $_POST['source'] ) {
					} else {
						$result = $this->get_dynamic_content( false, $object, $_POST['source'], $_POST['field_name'] );
						if ( false === $result ) {
							wp_send_json_error();
						}
						wp_send_json_success( $result );
					}
				}
			}
			wp_send_json_error();
		}

		/**
		 * Retrieve acf fields from selected content type
		 *
		 * @since 2.3.0
		 */
		public function get_acf_fields() {
			check_ajax_referer( 'porto-nonce', 'nonce' );
			if ( class_exists( 'ACF' ) && isset( $_POST['content_type'] ) && isset( $_POST['content_type_value'] ) && 'term' != $_POST['content_type'] ) {
				$atts   = array(
					'content_type'       => $_POST['content_type'],
					'content_type_value' => $_POST['content_type_value'],
				);
				$object = $this->get_dynamic_content_data( false, $atts );
				if ( $object ) {
					include_once PORTO_SHORTCODES_LIB . 'dynamic_tags/class-porto-func-acf.php';
					global $post;
					$post   = $object;
					$fields = apply_filters( 'porto_gutenberg_editor_vars', array() );
					if ( isset( $fields['acf'] ) ) {
						$fields = $fields['acf'];
					}
					wp_send_json_success( $fields );
				}
			}
			wp_send_json_error();
		}

		/**
		 * Retrieve Elementor & WPB dynamic data
		 *
		 * @since 2.3.0
		 */
		public function dynamic_get_data( $dynamic_source, $dynamic_content, $dynamic_field ) {
			if ( empty( $dynamic_source ) || empty( $dynamic_content ) || ! in_array( $dynamic_field, $this->features ) ) {
				return;
			}
			do_action( 'porto_dynamic_before_render' );
			if ( 'post_info' == $dynamic_source ) {
				if ( 'field' == $dynamic_field ) {
					$date_format = '';
					if ( is_array( $dynamic_content ) && isset( $dynamic_content['date_format'] ) ) {
						$date_format     = $dynamic_content['date_format'];
						$dynamic_content = $dynamic_content['field_dynamic_content'];
					}
					$result = (string) $this->get_dynamic_post_field_prop( $dynamic_content, $date_format );
					$result = $this->get_dynamic_post_field( $result );
				} elseif ( 'image' == $dynamic_field ) { // For Dynamic Tag Image
					$result = $this->get_dynamic_post_image( $dynamic_content );
				}
			} elseif ( 'post_link' == $dynamic_source ) {
				if ( 'link' == $dynamic_field ) {
					$result = $this->get_dynamic_post_link( $dynamic_content );
				}
			} elseif ( 'meta_field' == $dynamic_source ) {
				$result = $this->get_dynamic_content( false, '', 'meta', $dynamic_content );
			} elseif ( 'meta_box' == $dynamic_source ) {
				$result   = array();
				$meta_ids = get_post_meta( get_the_ID(), $dynamic_content );
				$result   = array_merge( $result, $meta_ids ? $meta_ids : array() );
				$result   = $this->get_dynamic_post_field( $result );

				if ( 'image' == $dynamic_field ) {
					$result = array( 'id' => $result );
				}
			} elseif ( 'term_meta' == $dynamic_source ) {
				$result     = array();
				$is_preview = porto_is_elementor_preview() || ( function_exists( 'vc_is_inline' ) && vc_is_inline() ) ||
				( isset( $_REQUEST['action'] ) && 'edit' == $_REQUEST['action'] && isset( $_REQUEST['post'] ) );
				if ( PortoBuilders::BUILDER_SLUG == get_post_type() ) {
					$porto_builder = get_post_meta( get_the_ID(), PortoBuilders::BUILDER_TAXONOMY_SLUG, true );
					if ( 'archive' == $porto_builder ) {
						PortoBuildersArchive::get_instance()->find_preview();
						$post_term = PortoBuildersArchive::get_instance()->edit_post_type;
					} elseif ( 'shop' == $porto_builder ) {
						$post_term = 'product';
					}
				}
				if ( ! $is_preview ) {
					$post_term = get_post_type();
				}
				if ( ! empty( $post_term ) && ! empty( $this->meta_terms[ $post_term ] ) ) {

					if ( $is_preview ) {
						$atts   = array(
							'content_type'       => 'term',
							'content_type_value' => $this->meta_terms[ $post_term ],
						);
						$object = $this->get_dynamic_content_data( false, $atts );
					} elseif ( is_tax() || is_category() || is_tag() ) {
						$object = get_queried_object();
					}
					if ( ! empty( $object ) ) {
						$result = $this->get_dynamic_content( false, $object, 'metabox', $dynamic_content );
					}
				}
			} elseif ( 'acf' == $dynamic_source ) {
				$result = Porto_Func_ACF::get_instance()->acf_get_meta( $dynamic_content );
				if ( 'image' == $dynamic_field ) {
					$result = array( 'id' => $result );
				}
			} elseif ( 'taxonomy' == $dynamic_source ) {
				$result = get_the_term_list( get_the_ID(), $dynamic_content, '', ', ', '' );
				if ( is_wp_error( $result ) ) {
					return '';
				}
				$result = $this->get_dynamic_post_field( $result );
				$result = porto_strip_script_tags( $result );
			} elseif ( 'woocommerce' == $dynamic_source ) {
				$product = wc_get_product();
				if ( ! $product ) {
					return $result;
				}
				if ( 'sales' == $dynamic_content ) {
					$result = $product->get_total_sales();
				} elseif ( 'excerpt' == $dynamic_content ) {
					$result = $product->get_short_description();
				} elseif ( 'sku' == $dynamic_content ) {
					$result = esc_html( $product->get_sku() );
				} elseif ( 'stock' == $dynamic_content ) {
					$result = $product->get_stock_quantity();
				} elseif ( 'sale_date' == $dynamic_content && function_exists('porto_woocommerce_sale_product_period') ) {
					$result = porto_woocommerce_sale_product_period( $product );
				}
			}
			do_action( 'porto_dynamic_after_render' );
			return $result;
		}

		/**
		 * Set current post type for Elementor & WP Bakery
		 *
		 * @since 2.3.0
		 */
		public function before_render( $post_type = '', $id = '' ) {

			global $post;
			if ( ! $post_type ) {
				$post_type = get_post_type();
			}
			if ( ! $id && $post ) {
				$id = $post->ID;
			};
			$this->post = $post;
			if ( PortoBuilders::BUILDER_SLUG == $post_type && isset( $id ) ) {
				$porto_builder_type = get_post_meta( $id, PortoBuilders::BUILDER_TAXONOMY_SLUG, true );
				if ( 'product' == $porto_builder_type && class_exists( 'PortoCustomProduct' ) ) {
					/**
					 * Set post Product in Single Product builder
					 */
					PortoCustomProduct::get_instance()->restore_global_product_variable();
				} elseif ( 'single' == $porto_builder_type && class_exists( 'PortoBuildersSingle' ) ) {

					/**
					 * Set post in Single builder
					 */
					PortoBuildersSingle::get_instance()->restore_global_single_variable();
				}
			}
		}

		/**
		 * Reset current post type for Elementor & WP Bakery
		 *
		 * @since 2.3.0
		 */
		public function after_render( $post_type = '', $id = '' ) {
			global $post;
			if ( ! $post_type ) {
				$post_type = get_post_type( $this->post );
			}
			if ( ! $id && isset( $this->post ) ) {
				$id = $this->post->ID;
			}
			if ( PortoBuilders::BUILDER_SLUG == $post_type && isset( $id ) ) {
				$porto_builder_type = get_post_meta( $id, PortoBuilders::BUILDER_TAXONOMY_SLUG, true );
				if ( 'product' == $porto_builder_type && class_exists( 'PortoCustomProduct' ) ) {

					/**
					 * Unset post Product in Single Product Builder
					 */
					PortoCustomProduct::get_instance()->reset_global_product_variable();

				} elseif ( 'single' == $porto_builder_type && class_exists( 'PortoBuildersSingle' ) ) {

					/**
					 * Unset post Product in Single Builder
					 */
					PortoBuildersSingle::get_instance()->reset_global_single_variable();
				}
			}
		}

		/**
		 * Get dynamic Post Field
		 *
		 * @since 2.3.0
		 */
		public function get_dynamic_post_object_fields() {
			$fields = array(
				array(
					'label'   => esc_html__( 'Post', 'porto-functionality' ),
					'options' => array(
						'post_id'       => esc_html__( 'Post ID', 'porto-functionality' ),
						'post_title'    => esc_html__( 'Title', 'porto-functionality' ),
						'post_date'     => esc_html__( 'Date', 'porto-functionality' ),
						'post_content'  => esc_html__( 'Content', 'porto-functionality' ),
						'post_excerpt'  => esc_html__( 'Excerpt', 'porto-functionality' ),
						'post_status'   => esc_html__( 'Post Status', 'porto-functionality' ),
						'comment_count' => esc_html__( 'Comments Count', 'porto-functionality' ),
						'like_count'    => esc_html__( 'Like Posts Count', 'porto-functionality' ),
					),
				),
				array(
					'label'   => esc_html__( 'Author', 'porto-functionality' ),
					'options' => array(
						'ID'    => esc_html__( 'Author ID', 'porto-functionality' ),
						'email' => esc_html__( 'Author E-mail', 'porto-functionality' ),
						'login' => esc_html__( 'Author Login', 'porto-functionality' ),
						'name'  => esc_html__( 'Author Name', 'porto-functionality' ),
					),
				),
			);

			return $fields;
		}

		/**
		 * Get dynamic Post Link
		 *
		 * @since 2.3.0
		 */
		public function get_dynamic_post_object_links() {
			$fields = array(
				array(
					'label'   => esc_html__( 'Post', 'porto-functionality' ),
					'options' => array(
						'post_url'           => esc_html__( 'Post Url', 'porto-functionality' ),
						'site_url'           => esc_html__( 'Site Url', 'porto-functionality' ),
						'author_archive_url' => esc_html__( 'Author Archive Url', 'porto-functionality' ),
						'author_website_url' => esc_html__( 'Author Website Url', 'porto-functionality' ),
						'comments_url'       => esc_html__( 'Comments Url', 'porto-functionality' ),
					),
				),
			);

			return $fields;
		}

		/**
		 * Get dynamic Post Image Field
		 *
		 * @since 2.3.0
		 */
		public function get_dynamic_taxonomy() {
			$option_fields  = array();
			$taxonomy_array = get_taxonomies();
			if ( $taxonomy_array && is_array( $taxonomy_array ) ) {
				$post_type = get_post_type();
				if ( count( $taxonomy_array ) > 1 ) {
					foreach ( $taxonomy_array as $value ) {
						$taxonomy_object = get_taxonomy( (string) $value );
						$taxonomy_type   = $taxonomy_object->object_type;
						if ( in_array( $post_type, $taxonomy_type ) ) {
							$key                   = $taxonomy_object->name;
							$option_fields[ $key ] = $taxonomy_object->label;
						} else {
							continue;
						}
					}
				} else {
					$taxonomy_object = get_taxonomy( (string) $taxonomy_array[0] );
					$taxonomy_type   = $taxonomy_object->object_type;

					if ( in_array( $post_type, $taxonomy_type ) ) {
						$key                   = $taxonomy_object->name;
						$option_fields[ $key ] = $taxonomy_object->label;
					}
				}
			}
			return $option_fields;
		}

		/**
		 * Get dynamic Post Image Field
		 *
		 * @since 2.3.0
		 */
		public function get_dynamic_post_object_image() {
			$objects = array(
				'featured'    => esc_html__( 'Featured Image', 'porto-functionality' ),
				'user_avatar' => esc_html__( 'User Avatar', 'porto-functionality' ),
			);

			return $objects;
		}

		/**
		 * Get dynamic Post Field
		 *
		 * @var string $ret Post Field Key
		 * @since 2.3.0
		 */
		public function get_dynamic_post_field( $ret ) {
			if ( is_array( $ret ) ) {
				$temp_content = '';
				if ( count( $ret ) >= 1 ) {
					foreach ( $ret as $value ) {
						$temp_content .= (string) $value . ' ';
					}
				}
				$ret = $temp_content;
			}
			return $ret;
		}

		/**
		 * Get dynamic Post Link
		 *
		 * @var string $ret Post Link Key
		 * @since 2.3.0
		 */
		public function get_dynamic_post_link( $property ) {
			switch ( $property ) {
				case 'post_url':
					$ret = get_permalink();
					break;
				case 'site_url':
					$ret = home_url();
					break;
				case 'author_archive_url':
					global $authordata;
					if ( $authordata ) {
						$ret = get_author_posts_url( $authordata->ID, $authordata->user_nicename );
					}
					break;
				case 'author_website_url':
					$ret = get_the_author_meta( 'url' );
					break;
				case 'comments_url':
					$ret = get_comments_link();
					break;
				default:
					$ret = '';
					break;
			}
			return $ret;
		}

		/**
		 * Get dynamic Post Field
		 *
		 * @var string $property post_field_key
		 * @since 2.3.0
		 */
		public function get_dynamic_post_field_prop( $property = null, $date_format = null ) {

			if ( ! $property ) {
				return false;
			}
			$author_properties = array(
				'ID',
				'email',
				'login',
				'name',
			);

			if ( $author_properties && in_array( $property, $author_properties ) ) {
				if ( 'name' == $property ) {
					$value = get_the_author();
				} else {
					$value = get_the_author_meta( $property );
				}
				return wp_kses_post( $value );
			} else {
				
				if ( ( ! porto_is_elementor_preview() && ! is_preview() && ! porto_is_vc_preview() ) && 'post_title' == $property && defined( 'PORTO_VERSION' ) ) {
					return porto_page_title();
				}

				$this->is_term_archive = false;
				$object                = $this->get_dynamic_post_field_object();
				$vars                  = $object ? get_object_vars( $object ) : array();
				if ( $this->is_term_archive ) {
					if ( 'post_id' == $property ) {
						return isset( $vars['term_id'] ) ? $vars['term_id'] : false;
					}
				}
				if ( 'post_content' == $property && defined( 'WPB_VC_VERSION' ) ) {
					$vars['post_content'] = isset( $vars['post_content'] ) ? do_shortcode( $vars['post_content'] ) : false;
				}
				if ( 'post_id' === $property ) {
					$vars['post_id'] = isset( $vars['ID'] ) ? $vars['ID'] : false;
				}
				if ( ! empty( $date_format ) ) {
					$vars['post_date'] = get_the_date( esc_html( $date_format ) );
				}
			}
			return isset( $vars[ $property ] ) ? $vars[ $property ] : false;
		}

		/**
		 * Get dynamic Post Field Object
		 *
		 * @since 2.3.0
		 */
		public function get_dynamic_post_field_object() {
			global $post;
			$post_object = false;
			if ( is_singular() ) {
				$post_object = $post;
			} elseif ( is_tax() || is_category() || is_tag() || is_author() ) {
				$post_object           = get_queried_object();
				$this->is_term_archive = true;
			} elseif ( wp_doing_ajax() ) {
				$post_object = get_post( $this->post_id );
			} elseif ( class_exists( 'Woocommerce' ) && is_shop() ) {
				$post_object = get_post( (int) get_option( 'woocommerce_shop_page_id' ) );
			} elseif ( is_archive() || is_post_type_archive() || is_home() ) {
				$post_object      = get_queried_object();
			}
			return $post_object;
		}

		/**
		 * Get dynamic Post Field Object
		 *
		 * @since 2.3.0
		 */
		public function get_dynamic_post_image( $dynamic_content ) {
			$image_id  = '';
			$image_url = '';
			switch ( $dynamic_content ) {
				case 'featured':
					global $post;
					if ( class_exists( 'woocommerce' ) && is_shop() ) {
						$id = (int) get_option( 'woocommerce_shop_page_id' );
					} elseif ( is_tax() || is_category() || is_tag() || is_author() || is_home() ) {
						$id = get_queried_object_id();
					} else {
						$id = $post->ID;
					}
					$image_id = get_post_thumbnail_id( $id );

					if ( ! $image_id ) {
						$gallery = get_post_meta( $id, 'supported_images' );
						if ( is_array( $gallery ) && count( $gallery ) ) {
							$image_id = $gallery[0];
						}
					}
					break;
				case 'user_avatar':
					$current_user = wp_get_current_user();
					if ( $current_user ) {
						$image_url = get_avatar_url( $current_user->ID );
					}
					break;
			}

			return array(
				'id'  => $image_id,
				'url' => $image_id ? wp_get_attachment_image_src( $image_id, 'full' )[0] : $image_url,
			);
		}

		/**
		 * Add dynamic field vars
		 *
		 * @since 2.3.0
		 */
		public function get_dynamic_metabox_fields( $widget, $type = 'meta' ) {
			$post_type = '';
			$post_term = '';
			$fn_name   = '';
			$backup    = '';
			if ( PortoBuilders::BUILDER_SLUG == get_post_type() ) {
				$porto_builder = get_post_meta( get_the_ID(), PortoBuilders::BUILDER_TAXONOMY_SLUG, true );
				if ( 'product' == $porto_builder ) {
					$post_type = 'product';
				} elseif ( 'single' == $porto_builder ) {
					$post_type = PortoBuildersSingle::get_instance()->edit_post_type;
				} elseif ( 'archive' == $porto_builder ) {
					$post_term = PortoBuildersArchive::get_instance()->edit_post_type;
				} elseif ( 'shop' == $porto_builder ) {
					$post_term = 'product';
				}
			} else {
				$post_type = get_post_type();
			}
			if ( 'meta' == $type && ! empty( $post_type ) ) {
				$fn_name = 'porto_' . $post_type . '_meta_fields';
			}
			if ( 'term' == $type && ! empty( $post_term ) && ! empty( $this->meta_terms[ $post_term ] ) ) {
				global $porto_settings;
				if ( isset( $porto_settings['show-category-skin'] ) ) {
					$backup                               = $porto_settings['show-category-skin'];
					$porto_settings['show-category-skin'] = false;
				}
				$fn_name = 'porto_' . $this->meta_terms[ $post_term ] . '_meta_fields';
			}
			$meta_fields = array();
			if ( ! empty( $fn_name ) && function_exists( $fn_name ) ) {
				$post_fields = $fn_name();
				foreach ( $post_fields as $key => $arr ) {
					if ( array_key_exists( $arr['type'], $this->metabox_types ) && in_array( $widget, $this->metabox_types[ $arr['type'] ] ) ) {
						$meta_fields[ $key ] = array( esc_js( $arr['title'] ) );
					}
				}
			}
			if ( ! empty( $backup ) ) {
				global $porto_settings;
				$porto_settings['show-category-skin'] = $backup;
			}
			return $meta_fields;
		}

		/**
		 * Retrieve Woo fields for each group
		 *
		 * @since 2.3.0
		 */
		public function get_woo_fields() {

			$fields = array(
				'excerpt'   => esc_html__( 'Product Short Description', 'porto-functionality' ),
				'sku'       => esc_html__( 'Product SKU', 'porto-functionality' ),
				'sales'     => esc_html__( 'Product Sales', 'porto-functionality' ),
				'stock'     => esc_html__( 'Product Stock', 'porto-functionality' ),
				'sale_date' => esc_html__( 'Product Sale End Date', 'porto-functionality' ),
			);
			return $fields;
		}
	}
endif;

Porto_Func_Dynamic_Tags_Content::get_instance();
