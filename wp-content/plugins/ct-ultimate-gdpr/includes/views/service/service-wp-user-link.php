<?php

/**
 * The template for displaying WP User service view in wp-admin
 *
 * You can overwrite this template by copying it to yourtheme/ct-ultimate-gdpr/service folder
 *
 * @version 1.0
 *
 */

?>

<?php if ($options['link']): ?>

<a href="<?php echo $options['link']; ?>" id="ct-ultimate-gdpr-service-wp-user-link" class="">
    <?php echo esc_html__( 'Parent or guard authorization required', 'ct-ultimate-gdpr' ); ?>
</a>

<?php endif;