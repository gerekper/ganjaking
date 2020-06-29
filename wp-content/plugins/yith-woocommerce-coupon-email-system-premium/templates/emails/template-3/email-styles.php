<?php

if ( !defined( 'ABSPATH' ) ) {
    exit;
} // Exit if accessed directly

// !important; is a gmail hack to prevent styles being stripped if it doesn't like something.

$site_url   = get_option( 'siteurl' );
$assets_url = untrailingslashit( YWCES_ASSETS_URL );

if ( strpos( $assets_url, $site_url ) === false ) {
    $assets_url = $site_url . $assets_url;
}

?>
    body {
    background-color: #ffffff;
    -webkit-text-size-adjust: none !important;
    width: 100%;
    margin: 0;
    padding: 0;
    min-width: 100%!important;
    font-family: 'Raleway', sans-serif;
    }

    #content_table{
    width: 100%;
    max-width: 600px;
    }

    #overheader{
    padding:30px 40px;
    font-size: 30px;
    text-transform: uppercase;
    color: #495361;
    font-weight: 800;
    line-height: 35px;
    text-align: center;
    }

    #header{
    padding: 10px;
    height: 240px;
    background: #d75957 url(<?php echo $assets_url ?>/images/header-bg.jpg) no-repeat center center;
    line-height: 40px;
    font-size: 30px;
    text-align: center;
    color: #ffffff;
    }

    #mailbody{
    padding: 50px 40px;
    border-left: 2px solid #cfcfcf;
    border-right: 2px solid #cfcfcf;
    font-size: 14px;
    color: #656565;
    line-height: 25px;
    background: #ffffff;
    }

    #footer{
    padding: 25px 10px;
    border-left: 2px solid #cfcfcf;
    border-right: 2px solid #cfcfcf;
    border-bottom: 2px solid #cfcfcf;
    text-align: center;
    line-height: 20px;
    font-size: 13px;
    background: #f5f5f5;
    }

    #footer a{
    text-decoration: none;
    color: #616161;
    font-weight: bold;
    }

    #subfooter{
    padding: 10px;
    text-align: center;
    line-height: 20px;
    font-size: 12px;
    color: #ee6563;
    }

    h2{
    margin: 16px 0 0 0;
    color:#ee6563;
    font-family: 'Raleway', sans-serif;
    }

    i{
    font-size: small;
    margin: 0 0 16px 0;
    display: block;
    }

    a{
    color:#ee6563;
    }

    span{
    font-size: small;
    font-style: italic;
    }

<?php
