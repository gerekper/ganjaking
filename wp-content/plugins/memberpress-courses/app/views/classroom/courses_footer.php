<?php
/**
 * This template can be overidden in the theme
 */
if ( ! defined( 'ABSPATH' ) ) {
  exit; // Exit if accessed directly.
}
use memberpress\courses as base; ?>
  <footer class="site-footer section is-clearfix">
    <?php do_action(base\SLUG_KEY . '_courses_footer'); ?>
  </footer>
</body>

</html>
