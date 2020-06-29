<?php

/**
 * Check for command line constant
 */
function is_command_line() {
  return ( defined( 'WP_CLI' ) && WP_CLI );
}

/**
 * Activation process
 * @return bool Should we proceed with plugin activation?
 */
function db_reset_activate() {
  $status = db_reset_activation_checks();

  if ( is_string( $status ) ) {
    db_reset_cancel_activation( $status );
  }
}

/**
 * Check for minimum plugin requirements
 * @return string|bool Error message if fails or boolean true if passes
 */
function db_reset_activation_checks() {
  if ( ! function_exists( 'spl_autoload_register' ) &&
        version_compare( phpversion(), '5.3', '<' ) ) {
    return __( 'The WordPress Database Reset plugin requires at least PHP 5.3!', 'wordpress-database-reset' );
  }

  if ( version_compare( get_bloginfo( 'version' ), '3.0', '<' ) ) {
    return __( 'The WordPress Database Reset plugin requires at least WordPress 3.0!', 'wordpress-database-reset' );
  }

  return true;
}

/**
 * Cancel activation and kill process
 * @param  string $message Error message
 * @return void
 */
function db_reset_cancel_activation( $message ) {
  deactivate_plugins( __FILE__ );
  wp_die( $message );
}

// Ewww. Still need it though.
require_once( ABSPATH . '/wp-admin/includes/upgrade.php' );

/**
 * Installs the site.
 *
 * This is a copied and pasted version from /wp-admin/includes/upgrade.php.
 * Why am I doing this? Because there's no way of hooking into the WP install
 * method to stop the 'new blog' email from firing.
 *
 * Also, through the command line, the call to wp_guess_url() doesn't work
 * and returns an empty value. We must pass the siteurl option to this
 * function in order to be able to reset the options table without it
 * completely breaking the site.
 *
 * @since 2.1.0
 *
 * @param string $blog_title    Blog title.
 * @param string $user_name     User's username.
 * @param string $user_email    User's email.
 * @param bool   $public        Whether blog is public.
 * @param string $deprecated    Optional. Not used.
 * @param string $user_password Optional. User's chosen password. Default empty (random password).
 * @param string $language      Optional. Language chosen. Default empty.
 * @return array Array keys 'url', 'user_id', 'password', and 'password_message'.
 */
function db_reset_install( $blog_title, $user_name, $user_email, $public, $site_url, $deprecated = '', $user_password = '', $language = '' ) {
  if ( ! empty( $deprecated ) )
    _deprecated_argument( __FUNCTION__, '2.6' );

  wp_check_mysql_version();
  wp_cache_flush();
  make_db_current_silent();
  populate_options();
  populate_roles();

  update_option( 'blogname', $blog_title );
  update_option( 'admin_email', $user_email );
  update_option( 'blog_public', $public );

  if ( $language ) {
    update_option( 'WPLANG', $language );
  }

  $guessurl = ( wp_guess_url() !== 'http:' ) ? wp_guess_url() : $site_url;

  update_option( 'siteurl', $guessurl );
  update_option( 'home', $guessurl );

  // If not a public blog, don't ping.
  if ( ! $public )
    update_option( 'default_pingback_flag', 0 );

  /*
   * Create default user. If the user already exists, the user tables are
   * being shared among blogs. Just set the role in that case.
   */
  $user_id = username_exists( $user_name );
  $user_password = trim( $user_password );
  $email_password = false;
  if ( ! $user_id && empty( $user_password ) ) {
    $user_password = wp_generate_password( 12, false );
    $message = __( '<strong><em>Note that password</em></strong> carefully! It is a <em>random</em> password that was generated just for you.' );
    $user_id = wp_create_user( $user_name, $user_password, $user_email );
    $email_password = true;
  } elseif ( ! $user_id ) {
    // Password has been provided
    $message = '<em>'.__( 'Your chosen password.' ).'</em>';
    $user_id = wp_create_user( $user_name, $user_password, $user_email );
  } else {
    $message = __( 'User already exists. Password inherited.' );
  }

  $user = new WP_User( $user_id );
  $user->set_role( 'administrator' );

  wp_install_defaults( $user_id );

  wp_install_maybe_enable_pretty_permalinks();

  flush_rewrite_rules();

  wp_cache_flush();

  /**
   * Fires after a site is fully installed.
   *
   * @since 3.9.0
   *
   * @param WP_User $user The site owner.
   */
  do_action( 'wp_install', $user );

  return array( 'url' => $guessurl, 'user_id' => $user_id, 'password' => $user_password, 'password_message' => $message );
}
