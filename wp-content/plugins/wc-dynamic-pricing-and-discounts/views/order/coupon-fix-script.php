<?php

/**
 * Coupon fix script
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

?>

<script type="text/javascript" style="display: none;">
    jQuery(document).ready(function() {
        jQuery('a[data-code="<?php echo $code; ?>"]').closest('.code').html('<?php echo $rule_link; ?>').css('padding-right', '0.5em');
    });
</script>
