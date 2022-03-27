<?php
if (!defined('ABSPATH')) exit;
if ( ! class_exists( 'WP_Async_Request' ) ) {
 abstract class WP_Async_Request {
 protected $prefix = 'wp';
 protected $action = 'async_request';
 protected $identifier;
 protected $data = array();
 public function __construct() {
 $this->identifier = $this->prefix . '_' . $this->action;
 add_action( 'wp_ajax_' . $this->identifier, array( $this, 'maybe_handle' ) );
 add_action( 'wp_ajax_nopriv_' . $this->identifier, array( $this, 'maybe_handle' ) );
 }
 public function data( $data ) {
 $this->data = $data;
 return $this;
 }
 public function dispatch() {
 $url = add_query_arg( $this->get_query_args(), $this->get_query_url() );
 $args = $this->get_post_args();
 return wp_remote_post( esc_url_raw( $url ), $args );
 }
 protected function get_query_args() {
 if ( property_exists( $this, 'query_args' ) ) {
 return $this->query_args;
 }
 return array(
 'action' => $this->identifier,
 'nonce' => wp_create_nonce( $this->identifier ),
 );
 }
 protected function get_query_url() {
 if ( property_exists( $this, 'query_url' ) ) {
 return $this->query_url;
 }
 return admin_url( 'admin-ajax.php' );
 }
 protected function get_post_args() {
 if ( property_exists( $this, 'post_args' ) ) {
 return $this->post_args;
 }
 return array(
 'timeout' => 0.01,
 'blocking' => false,
 'body' => $this->data,
 'cookies' => $_COOKIE,
 'sslverify' => apply_filters( 'https_local_ssl_verify', false ),
 );
 }
 public function maybe_handle() {
 // Don't lock up other requests while processing
 session_write_close();
 check_ajax_referer( $this->identifier, 'nonce' );
 $this->handle();
 wp_die();
 }
 abstract protected function handle();
 }
}
