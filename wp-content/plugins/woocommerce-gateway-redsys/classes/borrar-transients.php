<?php

// Delete expired transients
function delete_expired_transients() {
  global $wpdb;

  // Delete transients with no expiration date set
  $sql = "DELETE FROM $wpdb->options WHERE option_name LIKE '_transient_%' AND option_value NOT LIKE '%transient_timeout%'";
  $wpdb->query($sql);

  // Delete transients with an expiration date in the past
  $sql = "DELETE a, b FROM $wpdb->options a, $wpdb->options b WHERE a.option_name LIKE '_transient_%' AND a.option_value LIKE '%transient_timeout%' AND b.option_name = REPLACE(a.option_name, '_transient_timeout_', '') AND b.option_value < UNIX_TIMESTAMP()";
  $wpdb->query($sql);
}
add_action('init', 'delete_expired_transients');