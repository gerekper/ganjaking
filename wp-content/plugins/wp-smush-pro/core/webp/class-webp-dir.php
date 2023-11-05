<?php

namespace Smush\Core\Webp;

use Smush\Core\Upload_Dir;

class Webp_Dir extends Upload_Dir {
	private $webp_path;

	private $webp_rel_path;

	private $webp_url;

	/**
	 * @return string
	 */
	public function get_webp_path() {
		if ( is_null( $this->webp_path ) ) {
			$this->webp_path = $this->prepare_webp_path();
		}

		return $this->webp_path;
	}

	/**
	 * @return string
	 */
	public function get_webp_rel_path() {
		if ( is_null( $this->webp_rel_path ) ) {
			$this->webp_rel_path = $this->prepare_webp_rel_path();
		}

		return $this->webp_rel_path;
	}

	/**
	 * @return string
	 */
	public function get_webp_url() {
		if ( is_null( $this->webp_url ) ) {
			$this->webp_url = $this->prepare_webp_url();
		}

		return $this->webp_url;
	}

	private function prepare_webp_path() {
		return dirname( $this->get_upload_path() ) . '/smush-webp';
	}

	private function prepare_webp_rel_path() {
		return dirname( $this->get_upload_rel_path() ) . '/smush-webp';
	}

	private function prepare_webp_url() {
		return dirname( $this->get_upload_url() ) . '/smush-webp';
	}

	protected function prepare_root_path() {
		return apply_filters( 'smush_webp_rules_root_path_base', parent::prepare_root_path() );
	}
}