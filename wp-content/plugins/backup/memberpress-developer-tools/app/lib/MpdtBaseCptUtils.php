<?php
if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');}

/** Utilities to be used with APIs, Webhooks and the like **/
abstract class MpdtBaseCptUtils extends MpdtBaseUtils {
  public function __construct() {
    // The map array holds all of the mapping of
    // variable names from the model to api interface
    if(!isset($this->map) || !is_array($this->map)) {
      $this->map = array();
    }

    $this->map = array_merge(
      array(
        'ID'                    => 'id',
        'post_title'            => 'title',
        'post_content'          => 'content',
        'post_title'            => 'title',
        'post_excerpt'          => 'excerpt',
        'post_name'             => 'name',
        'post_date'             => 'date',
        'post_status'           => 'status',
        'post_parent'           => 'parent',
        'post_type'             => 'type',
        'post_author'           => 'author',
        'post_date_gmt'         => 'date_gmt',
        'comment_status'        => false,
        'ping_status'           => false,
        'post_password'         => 'password',
        'to_ping'               => false,
        'pinged'                => false,
        'post_modified'         => 'modified',
        'post_modified_gmt'     => 'modified_gmt',
        'post_content_filtered' => 'content_filtered',
        'menu_order'            => false,
        'post_mime_type'        => 'mime_type',
        'comment_count'         => false,
        'filter'                => false
      ),
      $this->map
    );

    parent::__construct();
  }
}

