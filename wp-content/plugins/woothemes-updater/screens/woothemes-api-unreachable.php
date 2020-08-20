<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly ?>

<?php
echo '<div class="error fade">' . wpautop( sprintf( __( 'There seems to be an error reaching the WooCommerce API at this time. Please try again later. Should this error persist, please %slog a ticket%s in our help desk.', 'woothemes-updater' ), '<a href="' . esc_url( 'https://support.woothemes.com/?utm_source=helper' ) . '" target="_blank">', '</a>' ) ) . '</div>' . "\n";
?>