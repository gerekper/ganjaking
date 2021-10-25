<?php

namespace MasterAddons\Inc\Templates\Sources;

use MasterAddons\Inc\Templates;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Master_Addons_Templates_Source_Api extends Master_Addons_Templates_Source_Base {

	private $_object_cache = array();

	public function get_slug() {
		return 'master-api';
	}

	public function get_version() {

		$key     = $this->get_slug() . '_version';
		$version = get_transient( $key );
		$version = false;

		if ( ! $version ) {
			$version = Templates\master_addons_templates()->api->get_info( 'api_version' );
			set_transient( $key, $version, DAY_IN_SECONDS );
		}

		return $version;
	}

	public function get_items( $tab = null ) {
        
		if ( ! $tab ) {
            
			return array();
		}
        
		$cached = $this->get_templates_cache();
        
		if ( ! empty( $cached[ $tab ] ) ) {
            
			return array_values( $cached[ $tab ] );
		}

		$templates = $this->remote_get_templates( $tab );

		if ( ! $templates ) {
			return array();
		}

		if ( empty( $cached ) ) {
			$cached = array();
		}

		$cached[ $tab ] = $templates;

		$this->set_templates_cache( $cached );

		return $templates;

	}

	public function prepare_items_tab( $tab = '' ) {

		if ( ! empty( $this->_object_cache[ $tab ] ) ) {
			return $this->_object_cache[ $tab ];
		}

		$result = array(
			'templates'  => array(),
			'categories' => array(),
			'keywords'   => array(),
		);

		$templates_cache  = $this->get_templates_cache();
		$categories_cache = $this->get_categories_cache();
		$keywords_cache   = $this->get_keywords_cache();

		if ( empty( $templates_cache ) ) {
			$templates_cache = array();
		}

		if ( empty( $categories_cache ) ) {
			$categories_cache = array();
		}

		if ( empty( $keywords_cache ) ) {
			$keywords_cache = array();
		}

		$result['templates'] = $this->remote_get_templates( $tab );
		$result['templates'] = $this->remote_get_categories( $tab );
		$result['templates'] = $this->remote_get_keywords( $tab );

		$templates_cache[ $tab ]  = $result['templates'];
		$categories_cache[ $tab ] = $result['categories'];
		$keywords_cache[ $tab ]   = $result['keywords'];

		$this->set_templates_cache( $templates_cache );
		$this->set_categories_cache( $categories_cache );
		$this->set_keywords_cache( $keywords_cache );

		$this->_object_cache[ $tab ] = $result;

		return $result;
	}

	public function remote_get_templates( $tab ) {
        
		$api_url = Templates\master_addons_templates()->api->api_url( 'templates' );
        
		if ( ! $api_url ) {
			return false;
		}

		$response = wp_remote_get( $api_url . $tab, array(
			'timeout'   => 60,
			'sslverify' => false
		) );

		$body = wp_remote_retrieve_body( $response );

		if ( ! $body ) {
			return false;
		}

		$body = json_decode( $body, true );

		if ( ! isset( $body['success'] ) || true !== $body['success'] ) {
			return false;
		}

		if ( empty( $body['templates'] ) ) {
			return false;
		}

		return $body['templates'];

	}

	public function remote_get_categories( $tab ) {

		$api_url = Templates\master_addons_templates()->api->api_url( 'categories' );

		if ( ! $api_url ) {
			return false;
		}

		$response = wp_remote_get( $api_url . $tab, array(
			'timeout'   => 60,
			'sslverify' => false
		) );

		$body = wp_remote_retrieve_body( $response );

		if ( ! $body ) {
			return false;
		}

		$body = json_decode( $body, true );

		if ( ! isset( $body['success'] ) || true !== $body['success'] ) {
			return false;
		}

		if ( empty( $body['terms'] ) ) {
			return false;
		}

		return $body['terms'];

	}

	public function remote_get_keywords( $tab ) {

		$api_url = Templates\master_addons_templates()->api->api_url( 'keywords' );

		if ( ! $api_url ) {
			return false;
		}

		$response = wp_remote_get( $api_url . $tab, array(
			'timeout'   => 60,
			'sslverify' => false
		) );

		$body = wp_remote_retrieve_body( $response );

		if ( ! $body ) {
			return false;
		}

		$body = json_decode( $body, true );

		if ( ! isset( $body['success'] ) || true !== $body['success'] ) {
			return false;
		}

		if ( empty( $body['terms'] ) ) {
			return false;
		}

		return $body['terms'];

	}

	public function get_categories( $tab = null ) {

		if ( ! $tab ) {
			return array();
		}
        
		$cached = $this->get_categories_cache();

		if ( ! empty( $cached[ $tab ] ) ) {
			return $this->prepare_categories( $cached[ $tab ] );
		}

		$categories = $this->remote_get_categories( $tab );

		if ( ! $categories ) {
			return array();
		}

		if ( empty( $cached ) ) {
			$cached = array();
		}

		$cached[ $tab ] = $categories;

		$this->set_categories_cache( $cached );

		return $this->prepare_categories( $categories );
	}

	public function prepare_categories( $categories ) {

		$result = array();

		foreach ( $categories as $slug => $title ) {
			$result[] = array(
				'slug'  => $slug,
				'title' => $title,
			);
		}

		return $result;
	}

	public function get_keywords( $tab = null ) {

		if ( ! $tab ) {
			return array();
		}

		$cached = $this->get_keywords_cache();

		if ( ! empty( $cached[ $tab ] ) ) {
			return $cached[ $tab ];
		}

		$keywords = $this->remote_get_keywords( $tab );

		if ( ! $keywords ) {
			return array();
		}

		if ( empty( $cached ) ) {
			$cached = array();
		}

		$cached[ $tab ] = $keywords;

		$this->set_keywords_cache( $cached );

		return $keywords;

	}

	public function get_item( $template_id, $tab = false ) {

		$id  = str_replace( $this->id_prefix(), '', $template_id );
        
		if ( ! $tab ) {
			$tab = isset( $_REQUEST['tab'] ) ? esc_attr( $_REQUEST['tab'] ) : false;
		}
        
        $license_key = Templates\master_addons_templates()->config->get( 'key' );
        
		$api_url = Templates\master_addons_templates()->api->api_url( 'template' );


		if ( ! $api_url ) {
			wp_send_json_success( array(
				'licenseError' => true,
			) );
		}
        
        $request =  add_query_arg(
			array(
				'license' => $license_key,
				'url'     => urlencode( home_url( '/' ) ),
			),
			$api_url . $id
		);

		$response = wp_remote_get( $request, array(
			'timeout'   => 60,
			'sslverify' => false
		) );

		$body = wp_remote_retrieve_body( $response );
		$body = json_decode( $body, true );

		if ( ! isset( $body['success'] ) ) {
			wp_send_json_error( array(
				'message' => 'Internal Error',
			) );
		}
        
		$content = isset( $body['content'] ) ? $body['content'] : '';
		$type    = isset( $body['type'] ) ? $body['type'] : '';
        $license = isset( $body['license'] ) ? $body['license'] : '';
        
		if ( ! empty( $content ) ) {
			$content = $this->replace_elements_ids( $content );
			$content = $this->process_export_import_content( $content, 'on_import' );
		}

		return array(
			'page_settings' => array(),
			'type'          => $type,
            'license'       => $license,
			'content'       => $content
		);

	}

	public function transient_lifetime() {
		return DAY_IN_SECONDS;
	}
}
