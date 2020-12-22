<?php

namespace WBCR\Factory_439\Entities;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/*
 * @author        Alex Kovalev <alex.kovalevv@gmail.com>, repo: https://github.com/alexkovalevv
 * @author        Webcraftic <wordpress.webraftic@gmail.com>, site: https://webcraftic.com
 * @since 4.1.1
 */

class Support {

	protected $plugin_name;
	protected $site_url;

	protected $features_page_slug = 'premium-features';
	protected $pricing_page_slug = 'pricing';
	protected $support_page_slug = 'support';
	protected $docs_page_slug = 'docs';

	/**
	 * Plugin_Site constructor.
	 *
	 * @param array $data
	 */
	public function __construct( array $data ) {
		$this->site_url = isset( $data['url'] ) ? $data['url'] : null;

		if ( isset( $data['pages_map'] ) && is_array( $data['pages_map'] ) ) {
			foreach ( $data['pages_map'] as $key => $def_value ) {
				$attr          = $key . '_page_slug';
				$this->{$attr} = isset( $data[ $key ] ) ? $data[ $key ] : $def_value;
			}
		}
	}

	/**
	 * @return string
	 */
	public function get_site_url( $track = false, $utm_content = null ) {
		if ( $track ) {
			return $this->get_tracking_page_url( $this->site_url, $utm_content );
		}

		return $this->site_url;
	}


	/**
	 * @return string
	 */
	public function get_features_url( $track = false, $utm_content = null ) {
		if ( $track ) {
			return $this->get_tracking_page_url( $this->features_page_slug, $utm_content );
		}

		return $this->get_site_url() . '/' . $this->features_page_slug;
	}


	/**
	 * @return string
	 */
	public function get_pricing_url( $track = false, $utm_content = null ) {
		if ( $track ) {
			return $this->get_tracking_page_url( $this->pricing_page_slug, $utm_content );
		}

		return $this->get_site_url() . '/' . $this->pricing_page_slug;
	}


	/**
	 * @return string
	 */
	public function get_contacts_url( $track = false, $utm_content = null ) {
		if ( $track ) {
			return $this->get_tracking_page_url( $this->support_page_slug, $utm_content );
		}

		return $this->get_site_url() . '/' . $this->support_page_slug;
	}


	/**
	 * @return string
	 */
	public function get_docs_url( $track = false, $utm_content = null ) {
		if ( $track ) {
			return $this->get_tracking_page_url( $this->docs_page_slug, $utm_content );
		}

		return $this->get_site_url() . '/' . $this->docs_page_slug;
	}


	/**
	 * @param null   $page
	 * @param null   $utm_content
	 * @param string $urm_source
	 *
	 * @return string
	 */
	public function get_tracking_page_url( $page = null, $utm_content = null, $urm_source = 'wordpress.org' ) {

		$args = [ 'utm_source' => $urm_source ];

		if ( ! empty( $plugin_name ) ) {
			$args['utm_campaign'] = $plugin_name;
		}

		if ( ! empty( $utm_content ) ) {
			$args['utm_content'] = $utm_content;
		}

		$raw_url = add_query_arg( $args, $this->get_site_url() . '/' . $page . '/' );

		return esc_url( $raw_url );
	}
}
