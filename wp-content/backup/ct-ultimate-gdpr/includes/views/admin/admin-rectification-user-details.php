<?php

/**
 * The template for displaying rectification controller 'user details' action view in wp-admin
 *
 * You can overwrite this template by copying it to yourtheme/ct-ultimate-gdpr/admin folder
 *
 * @version 1.0
 *
 */

/** @var array $options */

?>

<h3><?php printf( esc_html__( "Rectified data for user: %s", 'ct-ultimate-gdpr' ), $options['email'] ); ?></h3>

<h4><?php echo esc_attr__( 'Current data:', 'ct-ultimate-gdpr' ) ?></h4>

<div>
    <pre><?php echo esc_html__( $options['current_data'] ); ?></pre>
</div>

<h4><?php echo esc_attr__( 'Rectified data:', 'ct-ultimate-gdpr' ) ?></h4>

<div>
    <pre><?php echo esc_html__( $options['rectified_data'] ); ?></pre>
</div>

<br>

<form method="post">
    <input type="submit" class="button button-primary" name=""
           value="<?php echo esc_html__( 'Go back', 'ct-ultimate-gdpr' ); ?>">
</form>

