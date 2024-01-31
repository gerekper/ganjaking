<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

require_once dirname( __FILE__ ) . '/importer/class-vc-wp-import.php';
require_once dirname( __FILE__ ) . '/importer/class-vc-wxr-parser-plugin.php';

/**
 * Class Vc_Shared_Templates
 */
class Vc_Shared_Templates {
	/**
	 * @var bool
	 */
	protected $initialized = false;

	/**
	 * @var string
	 */
	protected $download_link_url = 'https://support.wpbakery.com/templates/download-link';

	/**
	 *
	 */
	public function init() {
		if ( $this->initialized ) {
			return;
		}
		$this->initialized = true;

		add_filter( 'vc_templates_render_category', array(
			$this,
			'renderTemplateBlock',
		), 10 );

		add_filter( 'vc_templates_render_frontend_template', array(
			$this,
			'renderFrontendTemplate',
		), 10, 2 );

		add_filter( 'vc_templates_render_backend_template', array(
			$this,
			'renderBackendTemplate',
		), 10, 2 );
		add_filter( 'vc_templates_render_backend_template_preview', array(
			$this,
			'renderBackendTemplate',
		), 10, 2 );
		add_action( 'vc_templates_delete_templates', array(
			$this,
			'delete',
		), 10, 2 );
		add_filter( 'wp_ajax_vc_shared_templates_download', array(
			$this,
			'ajaxDownloadTemplate',
		) );
		add_filter( 'vc_get_all_templates', array(
			$this,
			'addTemplatesTab',
		) );

		$this->registerPostType();
	}

	/**
	 * @param $templateId
	 * @param $templateType
	 * @return string
	 */
	public function renderBackendTemplate( $templateId, $templateType ) {
		if ( 'shared_templates' === $templateType ) {
			$templates = get_posts( array(
				'post_type' => 'vc4_templates',
				'include' => intval( $templateId ),
				'numberposts' => 1,
			) );
			if ( ! empty( $templates ) ) {
				$template = $templates[0];

				return $template->post_content;
			}
			wp_send_json_error( array(
				'code' => 'Wrong ID or no Template found',
			) );
		}

		return $templateId;
	}

	/**
	 * @param $templateId
	 * @param $templateType
	 * @return mixed
	 */
	public function renderFrontendTemplate( $templateId, $templateType ) {
		if ( 'shared_templates' === $templateType ) {
			$templates = get_posts( array(
				'post_type' => 'vc4_templates',
				'include' => intval( $templateId ),
				'numberposts' => 1,
			) );
			if ( ! empty( $templates ) ) {
				$template = $templates[0];

				vc_frontend_editor()->setTemplateContent( $template->post_content );
				vc_frontend_editor()->enqueueRequired();
				vc_include_template( 'editors/frontend_template.tpl.php', array(
					'editor' => vc_frontend_editor(),
				) );
				die();
			}
			wp_send_json_error( array(
				'code' => 'Wrong ID or no Template found #3',
			) );
		}

		return $templateId;
	}

	/**
	 * @param $templateId
	 * @param $templateType
	 * @return mixed
	 */
	public function delete( $templateId, $templateType ) {
		if ( 'shared_templates' === $templateType ) {
			$templates = get_posts( array(
				'post_type' => 'vc4_templates',
				'include' => intval( $templateId ),
				'numberposts' => 1,
			) );
			if ( ! empty( $templates ) ) {
				$template = $templates[0];
				if ( wp_delete_post( $template->ID ) ) {
					wp_send_json_success();
				}
			}
			wp_send_json_error( array(
				'code' => 'Wrong ID or no Template found #2',
			) );
		}

		return $templateId;
	}

	/**
	 * Post type from templates registration in WordPress
	 */
	private function registerPostType() {
		register_post_type( 'vc4_templates', array(
			'label' => 'Vc Templates',
			'public' => false,
			'publicly_queryable' => false,
			'exclude_from_search' => false,
			'show_ui' => false,
			'show_in_menu' => false,
			'menu_position' => 10,
			'menu_icon' => 'dashicons-admin-page',
			'hierarchical' => false,
			'taxonomies' => array(),
			'has_archive' => false,
			'rewrite' => false,
			'query_var' => false,
			'show_in_nav_menus' => false,
		) );
	}

	/**
	 * Ajax request processing from templates panel
	 */
	public function ajaxDownloadTemplate() {
		/** @var Vc_Current_User_Access $access */
		$access = vc_user_access()->checkAdminNonce()->validateDie( wp_json_encode( array(
			'success' => false,
			'message' => 'access denied',
		) ) )->part( 'templates' )->checkStateAny( true, null )->validateDie( wp_json_encode( array(
			'success' => false,
			'message' => 'part access denied',
		) ) )->check( array(
			vc_license(),
			'isActivated',
		) );
		$access->validateDie( wp_json_encode( array(
			'success' => false,
			'message' => 'license is not activated',
		) ) );

		$templateId = vc_request_param( 'id' );
		$requestUrl = $this->getTemplateDownloadLink( $templateId );
		$status = false;
		$file = $this->downloadTemplate( $requestUrl );
		$data = array();
		if ( is_string( $file ) && ! empty( $file ) ) {
			new Vc_WXR_Parser_Plugin();
			$importer = new Vc_WP_Import();
			ob_start();
			$importer->import( $file );
			if ( ! empty( $importer->processed_posts ) ) {
				$status = true;
				$postId = reset( $importer->processed_posts );
				$data['post_id'] = $postId;
			}
			ob_end_clean();
		}

		if ( $status ) {
			wp_send_json_success( $data );
		} else {
			wp_send_json_error( is_array( $file ) ? $file : null );
		}
	}

	/**
	 * @param $requestUrl
	 *
	 * @return bool|string
	 */
	private function downloadTemplate( $requestUrl ) {
		// FIX SSL SNI
		$filter_add = true;
		if ( function_exists( 'curl_version' ) ) {
			$version = curl_version();
			if ( version_compare( $version['version'], '7.18', '>=' ) ) {
				$filter_add = false;
			}
		}
		if ( $filter_add ) {
			add_filter( 'https_ssl_verify', '__return_false' );
		}
		$downloadUrlRequest = wp_remote_get( $requestUrl, array(
			'timeout' => 30,
		) );

		if ( $filter_add ) {
			remove_filter( 'https_ssl_verify', '__return_false' );
		}
		if ( is_array( $downloadUrlRequest ) && 200 === $downloadUrlRequest['response']['code'] ) {
			return $this->parseRequest( $downloadUrlRequest );
		}

		return false;
	}

	/**
	 * @param $request
	 *
	 * @return bool|string|array
	 */
	private function parseRequest( $request ) {
		$body = json_decode( $request['body'], true );
		if ( isset( $body['status'], $body['url'] ) && 1 === $body['status'] ) {
			$downloadUrl = $body['url'];
			$downloadedTemplateFile = download_url( $downloadUrl );
			if ( is_wp_error( $downloadedTemplateFile ) || ! $downloadedTemplateFile ) {
				return false;
			}

			return $downloadedTemplateFile;
		} elseif ( isset( $body['error'] ) ) {
			//new flow for error messages
			if ( ! empty( [ 'errorHtml' ] ) ) {
				return [
					'code' => 1,
					'message' => $body['errorHtml'],
				];
			}

			return array(
				'code' => 1,
				'message' => $body['error'],
			);
		}

		return false;
	}

	/**
	 * @param $data
	 *
	 * @return array
	 */
	public function addTemplatesTab( $data ) {
		if ( vc_user_access()->part( 'templates' )->checkStateAny( true, null, 'add' )->get() ) {
			$templates = $this->getTemplates();
			if ( ! empty( $templates ) || vc_user_access()->part( 'templates' )->checkStateAny( true, null )->get() ) {
				$newCategory = array(
					'category' => 'shared_templates',
					'category_name' => esc_html__( 'Template library', 'js_composer' ),
					'category_weight' => 10,
					'templates' => $this->getTemplates(),
				);
				$data[] = $newCategory;
			}
		}

		return $data;
	}

	/**
	 * @param $category
	 *
	 * @return mixed
	 */
	public function renderTemplateBlock( $category ) {
		if ( 'shared_templates' === $category['category'] ) {
			$category['output'] = $this->getTemplateBlockTemplate();
		}

		return $category;
	}

	/**
	 * @return string
	 */
	private function getTemplateBlockTemplate() {
		ob_start();
		vc_include_template( 'editors/popups/shared-templates/category.tpl.php', array(
			'controller' => $this,
			'templates' => $this->getTemplates(),
		) );

		return ob_get_clean();
	}

	/**
	 * @return array
	 */
	public function getTemplates() {
		$posts = get_posts( 'post_type=vc4_templates&numberposts=-1' );
		$templates = array();
		if ( ! empty( $posts ) ) {
			foreach ( $posts as $post ) {
				/** @var WP_Post $post */
				$id = get_post_meta( $post->ID, '_vc4_templates-id', true );
				$template = array();
				$template['title'] = $post->post_title;
				$template['version'] = get_post_meta( $post->ID, '_vc4_templates-version', true );
				$template['id'] = $id;
				$template['post_id'] = $post->ID;
				$template['name'] = $post->post_title; // For Settings
				$template['type'] = 'shared_templates'; // For Settings
				$template['unique_id'] = $id; // For Settings
				$templates[] = $template;
			}
		}

		return $templates;
	}

	/**
	 * Create url for request to download
	 * It requires a license key, product and version
	 *
	 * @param $id
	 *
	 * @return string
	 */
	private function getTemplateDownloadLink( $id ) {
		$url = esc_url( vc_license()->getSiteUrl() );
		$key = rawurlencode( vc_license()->getLicenseKey() );

		$url = $this->download_link_url . '?product=vc&url=' . $url . '&key=' . $key . '&version=' . WPB_VC_VERSION . '&id=' . esc_attr( $id );

		return $url;
	}
}
