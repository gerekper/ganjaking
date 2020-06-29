<?php
/**
 * Action Bar slogan area
 *
 * @package Betheme
 * @author Muffin group
 * @link https://muffingroup.com
 */

?>

<ul class="contact_details">
  <?php
    if ($header_slogan = mfn_opts_get('header-slogan')) {
      echo '<li class="slogan">'. wp_kses($header_slogan, mfn_allowed_html('desc')) .'</li>';
    }
    if ($header_phone = mfn_opts_get('header-phone')) {
      echo '<li class="phone"><i class="icon-phone"></i><a href="tel:'. esc_attr(str_replace(' ', '', $header_phone)) .'">'. esc_html($header_phone) .'</a></li>';
    }
    if ($header_phone_2 = mfn_opts_get('header-phone-2')) {
      echo '<li class="phone"><i class="icon-phone"></i><a href="tel:'. esc_attr(str_replace(' ', '', $header_phone_2)) .'">'. esc_html($header_phone_2) .'</a></li>';
    }
    if ($header_email = mfn_opts_get('header-email')) {
      echo '<li class="mail"><i class="icon-mail-line"></i><a href="mailto:'. sanitize_email($header_email) .'">'. esc_html($header_email) .'</a></li>';
    }
  ?>
</ul>
