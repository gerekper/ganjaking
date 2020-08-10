<?php
/**
Template Name: Wooinstant Checkout Template
 */
?>

<!DOCTYPE html>
<html <?php language_attributes(); ?> class="no-js">
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta name="viewport" content="width=device-width">
    <link rel="profile" href="http://gmpg.org/xfn/11">
    <link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>">

    <?php wp_head(); ?>
<style type="text/css" media="print">#wpadminbar { display:none; }</style>
<style type="text/css" media="screen">
    html { margin-top: 0px !important; }
    * html body { margin-top: 0px !important; }
    @media screen and ( max-width: 782px ) {
        html { margin-top: 0px !important; }
        * html body { margin-top: 0px !important; }
    }

    body,html{
        background-color: transparent !important;
    }
</style>
</head>

<body <?php body_class(); ?>>
<?php echo do_shortcode('[woocommerce_checkout]');  ?>
<div style="display: none;">
    <?php wp_footer(); ?>
</div>
<script>
jQuery(function($){
    $(document).ready(function(){
        // Init select2
        jQuery('#billing_country, #shipping_country, .country_to_state, .state_select').select2({
            width: '100%'               
        });
    });
})
</script>
</body>
</html>
