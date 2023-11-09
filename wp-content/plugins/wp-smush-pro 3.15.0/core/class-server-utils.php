<?php

namespace Smush\Core;

class Server_Utils {
	/**
	 * @var string
	 */
	private $mysql_version;

	public function get_server_type() {
		if ( empty( $_SERVER['SERVER_SOFTWARE'] ) ) {
			return '';
		}

		$server_software = wp_unslash( $_SERVER['SERVER_SOFTWARE'] );
		if ( ! is_array( $server_software ) ) {
			$server_software = array( $server_software );
		}

		$server_software = array_map( 'strtolower', $server_software );
		$is_nginx        = $this->array_has_needle( $server_software, 'nginx' );
		if ( $is_nginx ) {
			return 'nginx';
		}

		$is_apache = $this->array_has_needle( $server_software, 'apache' );
		if ( $is_apache ) {
			return 'apache';
		}

		return '';
	}

	public function get_memory_limit() {
		if ( function_exists( 'ini_get' ) ) {
			$memory_limit = ini_get( 'memory_limit' );
		} else {
			// Sensible default.
			$memory_limit = '128M';
		}

		if ( ! $memory_limit || - 1 === $memory_limit ) {
			// Unlimited, set to 32GB.
			$memory_limit = '32000M';
		}

		return intval( $memory_limit ) * 1024 * 1024;
	}

	public function get_memory_usage() {
		return memory_get_usage( true );
	}

	private function array_has_needle( $array, $needle ) {
		foreach ( $array as $item ) {
			if ( strpos( $item, $needle ) !== false ) {
				return true;
			}
		}

		return false;
	}

	public function get_mysql_version() {
		if ( ! $this->mysql_version ) {
			global $wpdb;
			/**
			 * MariaDB version prefix 5.5.5- is not stripped when using $wpdb->db_version() to get the DB version:
			 * https://github.com/php/php-src/issues/7972
			 */
			$this->mysql_version = $wpdb->get_var( 'SELECT VERSION()' );
		}
		return $this->mysql_version;
	}

	public function get_max_execution_time() {
		return (int) ini_get( 'max_execution_time' );
	}

	public function get_user_agent() {
		return ! empty( $_SERVER['HTTP_USER_AGENT'] ) ? wp_unslash( $_SERVER['HTTP_USER_AGENT'] ) : '';
	}
}