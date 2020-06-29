<?php
require_once 'color-functions.php';
$advanced_bg_color   = ( isset( $advanced_bg_color ) ) ? $advanced_bg_color : null;
$advanced_text_color = ( isset( $advanced_text_color ) ) ? $advanced_text_color : null;
$id_advanced_badge   = ( isset( $id_advanced_badge ) ) ? '-' . $id_advanced_badge : '-advanced';
$id_badge_style      = ( isset( $id_badge_style ) ) ? $id_badge_style : null;

if ( isset ( $_POST[ 'color' ] ) ) {
    $col = $_POST[ 'color' ];
    if ( strlen( $col ) == 6 ) {
        $advanced_bg_color = '#' . $col;
    }
}

if ( isset ( $_POST[ 'text_color' ] ) ) {
    $col = $_POST[ 'text_color' ];
    if ( strlen( $col ) == 6 ) {
        $advanced_text_color = '#' . $col;
    }
}

if ( isset ( $_POST[ 'id_badge_style' ] ) ) {
    $id_badge_style = $_POST[ 'id_badge_style' ];
}

$url                         = '';
if ( isset ( $_POST[ 'assets_url' ] ) ) {
    $url = $_POST[ 'assets_url' ] . "/images/advanced-on-sale-bg/";
}
if ( defined( 'YITH_WCBM_ASSETS_URL' ) ) {
    $url = YITH_WCBM_ASSETS_URL . "/images/advanced-on-sale-bg/";
}


if ( $flip_text_horizontally ) {
    echo ".yith-wcbm-on-sale-badge$id_advanced_badge .yith-wcbm-badge-text-advanced{-ms-transform: scaleX(-1); -webkit-transform: scaleX(-1); transform:scaleX(-1)}";
}

if ( $flip_text_vertically ) {
    echo ".yith-wcbm-on-sale-badge$id_advanced_badge .yith-wcbm-badge-text-advanced *{-ms-transform: scaleY(-1); -webkit-transform: scaleY(-1); transform:scaleY(-1)}";
}

switch ( $id_badge_style ) {
    case '1':
        $advanced_bg_color = ( isset( $advanced_bg_color ) ) ? $advanced_bg_color : '#66b909';
        $advanced_text_color = ( isset( $advanced_text_color ) ) ? $advanced_text_color : '#ffffff';

        $advanced_bg_color2 = '#' . yith_wcbm_color_with_factor( substr( $advanced_bg_color, 1 ), 0.7 );
        $advanced_bg_color3 = '#' . yith_wcbm_color_with_factor( substr( $advanced_bg_color, 1 ), 0.4 );
        ?>
        .yith-wcbm-on-sale-badge<?php echo $id_advanced_badge ?>{
        color: <?php echo $advanced_text_color ?>;
        font-family: "Open Sans",sans-serif;
        width:91px;
        height:60px;
        position:relative;
        box-sizing: border-box;
        position: absolute;
        background-color: transparent;
        }
        .yith-wcbm-on-sale-badge<?php echo $id_advanced_badge ?> .yith-wcbm-shape1{
        width:91px;
        height:0;
        border-top: 28px solid <?php echo $advanced_bg_color ?>;
        border-left: 18px solid transparent;
        position:relative;
        box-sizing: border-box;
        position: absolute;
        top:28px;
        right:0px;
        }
        .yith-wcbm-on-sale-badge<?php echo $id_advanced_badge ?> .yith-wcbm-shape1:before{
        content: ' ';
        width:91px;
        height:0;
        border-bottom: 28px solid <?php echo $advanced_bg_color ?>;
        border-left: 18px solid transparent;
        position:absolute;
        left:-18px;
        top:-56px;
        box-sizing: border-box;
        }
        .yith-wcbm-on-sale-badge<?php echo $id_advanced_badge ?> .yith-wcbm-shape1:after{
        content: ' ';
        border-left: 4px solid <?php echo $advanced_bg_color3 ?>;
        border-bottom: 4px solid transparent;
        position:absolute;
        right:1px;
        bottom:-4px;
        }
        .yith-wcbm-on-sale-badge<?php echo $id_advanced_badge ?> .yith-wcbm-shape2{
        width:85px;
        height:0;
        border-top: 19px solid <?php echo $advanced_bg_color2 ?>;
        border-left: 12px solid transparent;
        position:relative;
        box-sizing: border-box;
        position: absolute;
        bottom:4px;
        right:0px;
        }
        .yith-wcbm-on-sale-badge<?php echo $id_advanced_badge ?> .yith-wcbm-sale-percent{
        position: absolute;
        font-family: 'Open Sans', sans-serif;
        font-size: 28px;
        font-weight: 700;
        line-height: 0px;
        top:18px;
        <?php echo !$flip_text_horizontally ? 'right:30px;' : 'left:5px;'; ?>
        }
        .yith-wcbm-on-sale-badge<?php echo $id_advanced_badge ?> .yith-wcbm-save{
        position: absolute;
        font-family: 'Open Sans', sans-serif;
        font-size: 10px;
        line-height: 0px;
        bottom: 14px;
        <?php echo !$flip_text_horizontally ? 'left:30px;' : 'left:5px;'; ?>
        }
        .yith-wcbm-on-sale-badge<?php echo $id_advanced_badge ?> .yith-wcbm-simbol-percent{
        position: absolute;
        font-family: 'Open Sans', sans-serif;
        font-size: 12px;
        line-height: 0px;
        top:15px;
        <?php echo !$flip_text_horizontally ? 'right:15px;' : 'right:38px;'; ?>
        }
        .yith-wcbm-on-sale-badge<?php echo $id_advanced_badge ?> .yith-wcbm-simbol-off{
        position: absolute;
        font-family: 'Open Sans', sans-serif;
        font-size: 10px;
        line-height: 0px;
        top:25px;
        <?php echo !$flip_text_horizontally ? 'right:7px;' : 'right:30px;'; ?>
        text-transform: uppercase;
        }
        .yith-wcbm-on-sale-badge<?php echo $id_advanced_badge ?> .yith-wcbm-simbol-sale,
        .yith-wcbm-on-sale-badge<?php echo $id_advanced_badge ?> .yith-wcbm-simbol-sale-exclamation,
        .yith-wcbm-on-sale-badge<?php echo $id_advanced_badge ?> .yith-wcbm-saved-money,
        .yith-wcbm-on-sale-badge<?php echo $id_advanced_badge ?> .yith-wcbm-saved-money-value,
        .yith-wcbm-on-sale-badge<?php echo $id_advanced_badge ?> .yith-wcbm-saved-money-currency{
        display: none;
        }


        <?php
        break;

    case '2':
        $advanced_bg_color = ( isset( $advanced_bg_color ) ) ? $advanced_bg_color : '#FF6621';
        $advanced_text_color = ( isset( $advanced_text_color ) ) ? $advanced_text_color : '#ffffff';
        ?>
        .yith-wcbm-on-sale-badge<?php echo $id_advanced_badge ?>{
        color: <?php echo $advanced_text_color ?>;
        font-family: "Open Sans",sans-serif;
        width:70px;
        height:70px;
        position:absolute;
        box-sizing: border-box;
        position: absolute;
        background: transparent;
        }
        .yith-wcbm-on-sale-badge<?php echo $id_advanced_badge ?> .yith-wcbm-shape1{
        width:70px;
        height:70px;
        position:relative;
        box-sizing: border-box;
        position: absolute;
        top:0px;
        right:0px;
        border-radius: 50%;
        background: <?php echo $advanced_bg_color ?>;
        }
        .yith-wcbm-on-sale-badge<?php echo $id_advanced_badge ?> .yith-wcbm-shape1:before{
        content: '';
        width:0;
        height: 0;
        position: absolute;
        top:55px;
        left:0;
        border-top: 8px solid transparent;
        border-right: 15px solid <?php echo $advanced_bg_color ?>;
        border-bottom: 6px solid transparent;
        -webkit-transform: rotate(-35deg);
        -ms-transform: rotate(-35deg);
        transform: rotate(-35deg);
        }
        .yith-wcbm-on-sale-badge<?php echo $id_advanced_badge ?> .yith-wcbm-shape1:after{

        }
        .yith-wcbm-on-sale-badge<?php echo $id_advanced_badge ?>.yith-wcbm-advanced-display-percentage .yith-wcbm-sale-percent{
        position: absolute;
        font-family: 'Open Sans', sans-serif;
        font-weight: 600;
        font-size: 20px;
        line-height: 0px;
        top:32px;
        right:30px;
        }
        .yith-wcbm-on-sale-badge<?php echo $id_advanced_badge ?>.yith-wcbm-advanced-display-percentage .yith-wcbm-simbol-percent{
        position: absolute;
        font-family: 'Open Sans', sans-serif;
        font-size: 20px;
        font-weight: 500;
        line-height: 0px;
        top:32px;
        right:14px;
        }
        .yith-wcbm-on-sale-badge<?php echo $id_advanced_badge ?> .yith-wcbm-simbol-off{
        position: absolute;
        font-family: 'Open Sans', sans-serif;
        font-size: 10px;
        line-height: 0px;
        top:48px;
        width:100%;
        text-align:center;
        text-transform: uppercase;
        }

        .yith-wcbm-on-sale-badge<?php echo $id_advanced_badge ?>.yith-wcbm-advanced-display-amount .yith-wcbm-saved-money{
        position: absolute;
        font-family: 'Open Sans', sans-serif;
        font-weight: 600;
        font-size: 20px;
        line-height: 0px;
        top:32px;
        left:0;
        width:100%;
        text-align: center;
        }

        .yith-wcbm-on-sale-badge<?php echo $id_advanced_badge ?>.yith-wcbm-advanced-display-amount .yith-wcbm-sale-percent,
        .yith-wcbm-on-sale-badge<?php echo $id_advanced_badge ?>.yith-wcbm-advanced-display-amount .yith-wcbm-simbol-percent,
        .yith-wcbm-on-sale-badge<?php echo $id_advanced_badge ?>.yith-wcbm-advanced-display-percentage .yith-wcbm-saved-money,
        .yith-wcbm-on-sale-badge<?php echo $id_advanced_badge ?> .yith-wcbm-save,
        .yith-wcbm-on-sale-badge<?php echo $id_advanced_badge ?> .yith-wcbm-simbol-sale,
        .yith-wcbm-on-sale-badge<?php echo $id_advanced_badge ?> .yith-wcbm-simbol-sale-exclamation,
        .yith-wcbm-on-sale-badge<?php echo $id_advanced_badge ?> .yith-wcbm-saved-money-value,
        .yith-wcbm-on-sale-badge<?php echo $id_advanced_badge ?> .yith-wcbm-saved-money-currency{
        display: none;
        }
        <?php
        break;

    case '3':
        $advanced_bg_color = ( isset( $advanced_bg_color ) ) ? $advanced_bg_color : '#FF6621';
        $advanced_text_color = ( isset( $advanced_text_color ) ) ? $advanced_text_color : '#ffffff';
        ?>
        .yith-wcbm-on-sale-badge<?php echo $id_advanced_badge ?>{
        color: <?php echo $advanced_text_color ?>;
        font-family: "Open Sans",sans-serif;
        width:51px;
        height:32px;
        position:relative;
        box-sizing: border-box;
        position: absolute;
        background: #transparent;
        }
        .yith-wcbm-on-sale-badge<?php echo $id_advanced_badge ?> .yith-wcbm-shape1{
        width:51px;
        height:25px;
        position:relative;
        box-sizing: border-box;
        position: absolute;
        top:0px;
        right:0px;
        background: <?php echo $advanced_bg_color ?>;
        }
        .yith-wcbm-on-sale-badge<?php echo $id_advanced_badge ?> .yith-wcbm-shape1:before{
        content: '';
        width:0;
        height: 0;
        position: absolute;
        bottom: -6px;
        left:22px;
        border-left: 3px solid transparent;
        border-top: 6px solid <?php echo $advanced_bg_color ?>;
        border-right: 3px solid transparent;
        }
        .yith-wcbm-on-sale-badge<?php echo $id_advanced_badge ?> .yith-wcbm-shape1:after{

        }
        .yith-wcbm-on-sale-badge<?php echo $id_advanced_badge ?>.yith-wcbm-advanced-display-percentage .yith-wcbm-sale-percent{
        position: absolute;
        font-family: 'Open Sans', sans-serif;
        font-size: 13px;
        font-weight: 600;
        line-height: 0px;
        top:12px;
        right:21px;
        }
        .yith-wcbm-on-sale-badge<?php echo $id_advanced_badge ?>.yith-wcbm-advanced-display-percentage .yith-wcbm-sale-percent:before{
        content: '-';
        }

        .yith-wcbm-on-sale-badge<?php echo $id_advanced_badge ?>.yith-wcbm-advanced-display-percentage .yith-wcbm-simbol-percent{
        position: absolute;
        font-family: 'Open Sans', sans-serif;
        font-size: 12px;
        line-height: 0px;
        top:13px;
        right:10px;
        }

        .yith-wcbm-on-sale-badge<?php echo $id_advanced_badge ?>.yith-wcbm-advanced-display-amount .yith-wcbm-saved-money{
        position: absolute;
        font-family: 'Open Sans', sans-serif;
        font-weight: 600;
        font-size: 13px;
        line-height: 0px;
        top:12px;
        left:0;
        width:100%;
        text-align: center;
        }

        .yith-wcbm-on-sale-badge<?php echo $id_advanced_badge ?>.yith-wcbm-advanced-display-amount .yith-wcbm-saved-money:before{
        content: '-';
        }

        .yith-wcbm-on-sale-badge<?php echo $id_advanced_badge ?>.yith-wcbm-advanced-display-amount .yith-wcbm-sale-percent,
        .yith-wcbm-on-sale-badge<?php echo $id_advanced_badge ?>.yith-wcbm-advanced-display-amount .yith-wcbm-simbol-percent,
        .yith-wcbm-on-sale-badge<?php echo $id_advanced_badge ?>.yith-wcbm-advanced-display-percentage .yith-wcbm-saved-money,
        .yith-wcbm-on-sale-badge<?php echo $id_advanced_badge ?> .yith-wcbm-simbol-off,
        .yith-wcbm-on-sale-badge<?php echo $id_advanced_badge ?> .yith-wcbm-save,
        .yith-wcbm-on-sale-badge<?php echo $id_advanced_badge ?> .yith-wcbm-simbol-sale,
        .yith-wcbm-on-sale-badge<?php echo $id_advanced_badge ?> .yith-wcbm-simbol-sale-exclamation,
        .yith-wcbm-on-sale-badge<?php echo $id_advanced_badge ?> .yith-wcbm-saved-money-value,
        .yith-wcbm-on-sale-badge<?php echo $id_advanced_badge ?> .yith-wcbm-saved-money-currency{
        display: none;
        }
        <?php
        break;

    case '4':
        $advanced_bg_color = ( isset( $advanced_bg_color ) ) ? $advanced_bg_color : '#00DDBF';
        $advanced_text_color = ( isset( $advanced_text_color ) ) ? $advanced_text_color : '#ffffff';

        $flip_code = '';

        if ( !$flip_text_vertically && !$flip_text_horizontally ) {
            $flip_code = "-webkit-transform: rotate(45deg); -ms-transform: rotate(45deg); transform: rotate(45deg);";
        } elseif ( $flip_text_horizontally ) {
            $flip_code = "-webkit-transform: rotateX(180deg) rotateZ(135deg); -ms-transform: rotateX(180deg) rotateZ(135deg); transform: rotateX(180deg) rotateZ(135deg);";
        } elseif ( $flip_text_vertically ) {
            $flip_code = "-webkit-transform: rotate(45deg); -ms-transform: rotate(45deg); transform: rotate(45deg);";
        }

        ?>
        .yith-wcbm-on-sale-badge<?php echo $id_advanced_badge ?>{
        color: <?php echo $advanced_text_color ?>;
        font-family: "Open Sans",sans-serif;
        width:92px;
        height:92px;
        position:absolute;
        box-sizing: border-box;
        position: absolute;
        background: transparent;
        overflow: hidden;
        }

        .yith-wcbm-on-sale-badge<?php echo $id_advanced_badge ?> .yith-wcbm-shape1{
        width:145px;
        height:0;
        position:relative;
        box-sizing: border-box;
        position: absolute;
        top:17px;
        right:-42px;
        border-bottom: 37px solid <?php echo $advanced_bg_color ?>;
        border-left: 37px solid transparent;
        border-right: 37px solid transparent;
        -webkit-transform: rotate(45deg);
        -ms-transform: rotate(45deg);
        transform: rotate(45deg);
        }

        .yith-wcbm-on-sale-badge<?php echo $id_advanced_badge ?> .yith-wcbm-badge-text-advanced{
        width: 100%;
        height: 100%;
        <?php echo $flip_code; ?>
        }

        .yith-wcbm-on-sale-badge<?php echo $id_advanced_badge ?> .yith-wcbm-sale-percent{
        position: absolute;
        font-family: 'Open Sans', sans-serif;
        font-size: 15px;
        font-weight: 700;
        line-height: 0px;
        top:27px;
        right:42px;
        }

        .yith-wcbm-on-sale-badge<?php echo $id_advanced_badge ?> .yith-wcbm-sale-percent:before{
        content: '-';
        }

        .yith-wcbm-on-sale-badge<?php echo $id_advanced_badge ?> .yith-wcbm-simbol-percent{
        position: absolute;
        font-family: 'Open Sans', sans-serif;
        font-size: 14px;
        font-weight: 600;
        line-height: 0px;
        top:27px;
        right:28px;
        }

        .yith-wcbm-on-sale-badge<?php echo $id_advanced_badge ?>.yith-wcbm-advanced-display-amount .yith-wcbm-saved-money{
        position: absolute;
        font-family: 'Open Sans', sans-serif;
        font-weight: 600;
        font-size: 15px;
        line-height:0;
        top: 27px;
        right: -28px;
        width: 145px;
        text-align: center;
        }

        .yith-wcbm-on-sale-badge<?php echo $id_advanced_badge ?>.yith-wcbm-advanced-display-amount .yith-wcbm-saved-money:before{
        content: '-';
        }


        .yith-wcbm-on-sale-badge<?php echo $id_advanced_badge ?>.yith-wcbm-advanced-display-amount .yith-wcbm-sale-percent,
        .yith-wcbm-on-sale-badge<?php echo $id_advanced_badge ?>.yith-wcbm-advanced-display-amount .yith-wcbm-simbol-percent,
        .yith-wcbm-on-sale-badge<?php echo $id_advanced_badge ?>.yith-wcbm-advanced-display-percentage .yith-wcbm-saved-money,
        .yith-wcbm-on-sale-badge<?php echo $id_advanced_badge ?> .yith-wcbm-simbol-off,
        .yith-wcbm-on-sale-badge<?php echo $id_advanced_badge ?> .yith-wcbm-save,
        .yith-wcbm-on-sale-badge<?php echo $id_advanced_badge ?> .yith-wcbm-simbol-sale,
        .yith-wcbm-on-sale-badge<?php echo $id_advanced_badge ?> .yith-wcbm-simbol-sale-exclamation,
        .yith-wcbm-on-sale-badge<?php echo $id_advanced_badge ?> .yith-wcbm-saved-money-value,
        .yith-wcbm-on-sale-badge<?php echo $id_advanced_badge ?> .yith-wcbm-saved-money-currency{
        display: none;
        }
        <?php
        break;

    case '5':
        $image_url = $url . "5.png";
        ?>
        .yith-wcbm-on-sale-badge<?php echo $id_advanced_badge ?>{
        background: url('<?php echo $image_url ?>') no-repeat; font-family: "Open Sans",sans-serif; width: 71px; height: 71px; position: absolute; z-index:10;
        }
        .yith-wcbm-on-sale-badge<?php echo $id_advanced_badge ?> .yith-wcbm-sale-percent,
        .yith-wcbm-on-sale-badge<?php echo $id_advanced_badge ?> .yith-wcbm-saved-money-value{
        position: absolute;
        color: #fff;
        font-size: 23px;
        line-height: 0px;
        top:28px;
        right:29px;
        font-weight: 700;
        }
        .yith-wcbm-on-sale-badge<?php echo $id_advanced_badge ?> .yith-wcbm-simbol-percent,
        .yith-wcbm-on-sale-badge<?php echo $id_advanced_badge ?> .yith-wcbm-saved-money-currency{
        position: absolute;
        color: #fff;
        font-size: 13px;
        line-height: 0px;
        top: 28px;
        right: 15px;
        font-weight: 400;
        }
        .yith-wcbm-on-sale-badge<?php echo $id_advanced_badge ?> .yith-wcbm-simbol-off{
        position: absolute;
        color: #fff;
        font-size: 11px;
        line-height: 0px;
        top: 48px;
        right: 0;
        font-weight: 400;
        width: 100%;
        text-align: center;
        text-transform: uppercase;
        }

        .yith-wcbm-on-sale-badge<?php echo $id_advanced_badge ?>.yith-wcbm-advanced-display-amount .yith-wcbm-sale-percent,
        .yith-wcbm-on-sale-badge<?php echo $id_advanced_badge ?>.yith-wcbm-advanced-display-amount .yith-wcbm-simbol-percent,
        .yith-wcbm-on-sale-badge<?php echo $id_advanced_badge ?>.yith-wcbm-advanced-display-percentage .yith-wcbm-saved-money-value,
        .yith-wcbm-on-sale-badge<?php echo $id_advanced_badge ?>.yith-wcbm-advanced-display-percentage .yith-wcbm-saved-money-currency,
        .yith-wcbm-on-sale-badge<?php echo $id_advanced_badge ?> .yith-wcbm-saved-money,
        .yith-wcbm-on-sale-badge<?php echo $id_advanced_badge ?> .yith-wcbm-save,
        .yith-wcbm-on-sale-badge<?php echo $id_advanced_badge ?> .yith-wcbm-simbol-sale,
        .yith-wcbm-on-sale-badge<?php echo $id_advanced_badge ?> .yith-wcbm-simbol-sale-exclamation{
        display: none;
        }
        <?php
        break;

    case '6':
        $advanced_bg_color = ( isset( $advanced_bg_color ) ) ? $advanced_bg_color : '#A527F0';
        $advanced_text_color = ( isset( $advanced_text_color ) ) ? $advanced_text_color : '#ffffff';
        $advanced_bg_color2  = '#' . yith_wcbm_color_with_factor( substr( $advanced_bg_color, 1 ), 0.6 );
        ?>
        .yith-wcbm-on-sale-badge<?php echo $id_advanced_badge ?> {
        color: <?php echo $advanced_text_color ?>;
        font-family: "Open Sans",sans-serif;
        width:65px;
        height:70px;
        position:absolute;
        box-sizing: border-box;
        position: absolute;
        background: transparent;
        }

        .yith-wcbm-on-sale-badge<?php echo $id_advanced_badge ?> .yith-wcbm-shape1{
        width:0;
        height:70px;
        border-left: 25px solid <?php echo $advanced_bg_color ?>;
        border-bottom: 10px solid transparent;
        position:relative;
        box-sizing: border-box;
        position: absolute;
        top:0px;
        right:7px;
        }

        .yith-wcbm-on-sale-badge<?php echo $id_advanced_badge ?> .yith-wcbm-shape1:before{
        content:' ';
        width:0;
        height:70px;
        border-right: 25px solid <?php echo $advanced_bg_color ?>;
        border-bottom: 10px solid transparent;
        position:relative;
        box-sizing: border-box;
        position: absolute;
        top:0;
        right:25px;
        }
        .yith-wcbm-on-sale-badge<?php echo $id_advanced_badge ?> .yith-wcbm-shape1:after{

        }

        .yith-wcbm-on-sale-badge<?php echo $id_advanced_badge ?> .yith-wcbm-shape2{
        width: 100%;
        position:absolute;
        top:32px;
        left:0px;
        color:white;
        border-top: 8px solid <?php echo $advanced_bg_color2 ?>;
        border-left: 6px solid transparent;
        border-right: 6px solid transparent;
        box-sizing: border-box;
        }

        .yith-wcbm-on-sale-badge<?php echo $id_advanced_badge ?> .yith-wcbm-shape2:before{
        content: '';
        width: 100%;
        position:absolute;
        top:0;
        left:-6px;
        color:white;
        border-bottom: 8px solid <?php echo $advanced_bg_color2 ?>;
        border-left: 6px solid transparent;
        border-right: 6px solid transparent;
        box-sizing: content-box;
        }

        .yith-wcbm-on-sale-badge<?php echo $id_advanced_badge ?> .yith-wcbm-shape2:after{
        content: "<?php _e( 'DISCOUNT', 'yith-woocommerce-badges-management' ) ?>";
        color: <?php echo $advanced_text_color ?>;
        font-family: 'Open Sans', sans-serif;
        font-size: 10px;
        position: absolute;
        left:2px;
        top:-5px;
        line-height:10px;
        <?php
        $flip_text_css = '';
        $flip_text_css .= $flip_text_horizontally ? ' scaleX(-1)' : '';
        $flip_text_css .= $flip_text_vertically ? ' scaleY(-1)' : '';
        if ( $flip_text_css )
            echo "-webkit-transform: $flip_text_css; -ms-transform: $flip_text_css; transform: $flip_text_css;";
        ?>
        }

        .yith-wcbm-on-sale-badge<?php echo $id_advanced_badge ?> .yith-wcbm-sale-percent{
        position: absolute;
        font-family: 'Open Sans', sans-serif;
        font-size: 19px;
        font-weight: 600;
        line-height: 0px;
        top:17px;
        right:28px;
        }

        .yith-wcbm-on-sale-badge<?php echo $id_advanced_badge ?> .yith-wcbm-simbol-percent{
        position: absolute;
        font-family: 'Open Sans', sans-serif;
        font-size: 15px;
        font-weight: 500;
        line-height: 0px;
        top:18px;
        right:16px;
        }

        .yith-wcbm-on-sale-badge<?php echo $id_advanced_badge ?> .yith-wcbm-simbol-off{
        display: none;
        }

        .yith-wcbm-on-sale-badge<?php echo $id_advanced_badge ?> .yith-wcbm-save{
        position: absolute;
        font-family: 'Open Sans', sans-serif;
        font-size: 9px;
        width: 100%;
        text-align: center;
        left: 0;
        top: 55px;
        line-height: 0px;
        white-space: nowrap;
        }

        .yith-wcbm-on-sale-badge<?php echo $id_advanced_badge ?> .yith-wcbm-simbol-sale,
        .yith-wcbm-on-sale-badge<?php echo $id_advanced_badge ?> .yith-wcbm-saved-money,
        .yith-wcbm-on-sale-badge<?php echo $id_advanced_badge ?> .yith-wcbm-saved-money-value,
        .yith-wcbm-on-sale-badge<?php echo $id_advanced_badge ?> .yith-wcbm-saved-money-currency,
        .yith-wcbm-on-sale-badge<?php echo $id_advanced_badge ?> .yith-wcbm-simbol-sale-exclamation{
        display: none;
        }
        <?php
        break;

    case '7':
        $advanced_bg_color = ( isset( $advanced_bg_color ) ) ? $advanced_bg_color : '#7428F9';
        $advanced_text_color = ( isset( $advanced_text_color ) ) ? $advanced_text_color : '#ffffff';
        $advanced_bg_color2  = '#' . yith_wcbm_color_with_factor( substr( $advanced_bg_color, 1 ), 0.6 );
        $advanced_bg_color3  = '#' . yith_wcbm_color_with_factor( substr( $advanced_bg_color, 1 ), 0.4 );
        ?>
        .yith-wcbm-on-sale-badge<?php echo $id_advanced_badge ?> {
        color: <?php echo $advanced_text_color ?>;
        font-family: "Open Sans",sans-serif;
        width:80px;
        height:70px;
        position:absolute;
        box-sizing: border-box;
        position: absolute;
        overflow: hidden;
        background: transparent;
        }
        .yith-wcbm-on-sale-badge<?php echo $id_advanced_badge ?> .yith-wcbm-shape1{
        width: 51px;
        height: 0px;
        border-top: 56px solid <?php echo $advanced_bg_color ?>;
        border-left: 29px solid transparent;
        position: absolute;
        top:4px;
        right:-3px;
        z-index: 14;
        -ms-transform: rotate(5deg);
        -webkit-transform: rotate(5deg);
        transform: rotate(5deg);
        box-sizing: content-box;
        }

        .yith-wcbm-on-sale-badge<?php echo $id_advanced_badge ?> .yith-wcbm-shape1:before{
        content: ' ';
        width: 51px;
        height: 0px;
        border-top: 8px solid <?php echo $advanced_bg_color2 ?>;
        border-left: 4px solid transparent;
        position: absolute;
        bottom:0;
        right:0;
        z-index: 14;
        box-sizing: content-box;
        }

        .yith-wcbm-on-sale-badge<?php echo $id_advanced_badge ?> .yith-wcbm-shape1:after{
        content: ' ';
        width: 0px;
        height: 0px;
        border-bottom: 23px solid <?php echo $advanced_bg_color2 ?>;
        border-left: 55px solid transparent;
        position: absolute;
        bottom:8px;
        right:0;
        z-index: 14;
        box-sizing: content-box;
        }

        .yith-wcbm-on-sale-badge<?php echo $id_advanced_badge ?> .yith-wcbm-shape2{
        width: 0px;
        height: 0px;
        border-left: 5px solid <?php echo $advanced_bg_color3 ?>;
        border-bottom: 6px solid transparent;
        position: absolute;
        bottom:3px;
        right:0;
        z-index: 12;
        box-sizing: content-box;
        }
        .yith-wcbm-on-sale-badge<?php echo $id_advanced_badge ?> .yith-wcbm-sale-percent,
        .yith-wcbm-on-sale-badge<?php echo $id_advanced_badge ?> .yith-wcbm-saved-money-value{
        position: absolute;
        font-family: 'Open Sans', sans-serif;
        font-size: 28px;
        font-weight: 600;
        line-height: 0;
        top:25px;
        <?php echo !$flip_text_horizontally ? 'right:22px;' : 'left:10px;'; ?>
        z-index: 16;
        }
        .yith-wcbm-on-sale-badge<?php echo $id_advanced_badge ?> .yith-wcbm-simbol-percent,
        .yith-wcbm-on-sale-badge<?php echo $id_advanced_badge ?> .yith-wcbm-saved-money-currency{
        position: absolute;
        font-family: 'Open Sans', sans-serif;
        font-size: 12px;
        line-height: 0px;
        top:26px;
        <?php echo !$flip_text_horizontally ? 'right:9px;' : 'left:47px;'; ?>
        z-index: 16;
        }
        .yith-wcbm-on-sale-badge<?php echo $id_advanced_badge ?> .yith-wcbm-simbol-off{
        position: absolute;
        font-family: 'Open Sans', sans-serif;
        font-size: 10px;
        line-height: 0px;
        top:53px;
        <?php echo !$flip_text_horizontally ? 'right:17px;' : 'left:10px;'; ?>
        z-index: 16;
        text-transform: uppercase;
        }

        .yith-wcbm-on-sale-badge<?php echo $id_advanced_badge ?>.yith-wcbm-advanced-display-amount .yith-wcbm-sale-percent,
        .yith-wcbm-on-sale-badge<?php echo $id_advanced_badge ?>.yith-wcbm-advanced-display-amount .yith-wcbm-simbol-percent,
        .yith-wcbm-on-sale-badge<?php echo $id_advanced_badge ?>.yith-wcbm-advanced-display-percentage .yith-wcbm-saved-money-value,
        .yith-wcbm-on-sale-badge<?php echo $id_advanced_badge ?>.yith-wcbm-advanced-display-percentage .yith-wcbm-saved-money-currency,
        .yith-wcbm-on-sale-badge<?php echo $id_advanced_badge ?> .yith-wcbm-saved-money,
        .yith-wcbm-on-sale-badge<?php echo $id_advanced_badge ?> .yith-wcbm-save,
        .yith-wcbm-on-sale-badge<?php echo $id_advanced_badge ?> .yith-wcbm-simbol-sale,
        .yith-wcbm-on-sale-badge<?php echo $id_advanced_badge ?> .yith-wcbm-simbol-sale-exclamation{
        display: none;
        }
        <?php
        break;

    case '8':
        $advanced_bg_color = ( isset( $advanced_bg_color ) ) ? $advanced_bg_color : '#4090FF';
        $advanced_text_color = ( isset( $advanced_text_color ) ) ? $advanced_text_color : '#ffffff';
        $advanced_bg_color2  = '#' . yith_wcbm_color_with_factor( substr( $advanced_bg_color, 1 ), 0.5 );
        ?>
        .yith-wcbm-on-sale-badge<?php echo $id_advanced_badge ?> {
        color: <?php echo $advanced_text_color ?>;
        font-family: "Open Sans",sans-serif;
        width:65px;
        height:71px;
        position:absolute;
        box-sizing: border-box;
        position: absolute;
        background: transparent;
        }

        .yith-wcbm-on-sale-badge<?php echo $id_advanced_badge ?> .yith-wcbm-shape1{
        width:0;
        height:70px;
        border-left: 25px solid <?php echo $advanced_bg_color2 ?>;
        border-bottom: 10px solid transparent;
        position:relative;
        box-sizing: border-box;
        position: absolute;
        top:0px;
        left: 8px;
        }

        .yith-wcbm-on-sale-badge<?php echo $id_advanced_badge ?> .yith-wcbm-shape1:before{
        content:' ';
        width:0;
        height:70px;
        border-right: 25px solid <?php echo $advanced_bg_color2 ?>;
        border-bottom: 10px solid transparent;
        position:relative;
        box-sizing: border-box;
        position: absolute;
        top:0;
        right:-25px;
        }
        .yith-wcbm-on-sale-badge<?php echo $id_advanced_badge ?> .yith-wcbm-shape1:after{

        }

        .yith-wcbm-on-sale-badge<?php echo $id_advanced_badge ?> .yith-wcbm-shape2{
        width: 100%;
        position:absolute;
        top:32px;
        left:0px;
        color:white;
        border-top: 8px solid <?php echo $advanced_bg_color ?>;
        border-left: 6px solid transparent;
        border-right: 6px solid transparent;
        box-sizing: border-box;
        }

        .yith-wcbm-on-sale-badge<?php echo $id_advanced_badge ?> .yith-wcbm-shape2:before{
        content: '';
        width: 100%;
        position:absolute;
        top:0;
        left:-6px;
        color:white;
        border-bottom: 8px solid <?php echo $advanced_bg_color ?>;
        border-left: 6px solid transparent;
        border-right: 6px solid transparent;
        box-sizing: content-box;
        }

        .yith-wcbm-on-sale-badge<?php echo $id_advanced_badge ?> .yith-wcbm-shape2:after{
        content: "<?php _e( 'DISCOUNT', 'yith-woocommerce-badges-management' ) ?>";
        color: <?php echo $advanced_text_color ?>;
        font-family: 'Open Sans', sans-serif;
        font-size: 10px;
        position: absolute;
        left:2px;
        top:-5px;
        line-height:10px;
        <?php
        $flip_text_css = '';
        $flip_text_css .= $flip_text_horizontally ? ' scaleX(-1)' : '';
        $flip_text_css .= $flip_text_vertically ? ' scaleY(-1)' : '';
        if ( $flip_text_css )
            echo "-webkit-transform: $flip_text_css; -ms-transform: $flip_text_css; transform: $flip_text_css;";
        ?>
        }

        .yith-wcbm-on-sale-badge<?php echo $id_advanced_badge ?> .yith-wcbm-sale-percent{
        position: absolute;
        font-family: 'Open Sans', sans-serif;
        font-size: 19px;
        font-weight: 600;
        line-height: 0px;
        top:17px;
        right:28px;
        }

        .yith-wcbm-on-sale-badge<?php echo $id_advanced_badge ?> .yith-wcbm-simbol-percent{
        position: absolute;
        font-family: 'Open Sans', sans-serif;
        font-size: 15px;
        font-weight: 500;
        line-height: 0px;
        top:18px;
        right:16px;
        }

        .yith-wcbm-on-sale-badge<?php echo $id_advanced_badge ?> .yith-wcbm-simbol-off{
        display: none;
        }

        .yith-wcbm-on-sale-badge<?php echo $id_advanced_badge ?> .yith-wcbm-save{
        position: absolute;
        font-family: 'Open Sans', sans-serif;
        font-size: 9px;
        width: 100%;
        text-align: center;
        left: 0;
        top: 55px;
        line-height: 0px;
        white-space: nowrap;
        }

        .yith-wcbm-on-sale-badge<?php echo $id_advanced_badge ?> .yith-wcbm-simbol-sale,
        .yith-wcbm-on-sale-badge<?php echo $id_advanced_badge ?> .yith-wcbm-saved-money,
        .yith-wcbm-on-sale-badge<?php echo $id_advanced_badge ?> .yith-wcbm-saved-money-value,
        .yith-wcbm-on-sale-badge<?php echo $id_advanced_badge ?> .yith-wcbm-saved-money-currency,
        .yith-wcbm-on-sale-badge<?php echo $id_advanced_badge ?> .yith-wcbm-simbol-sale-exclamation{
        display: none;
        }
        <?php
        break;

    case '9':
        $advanced_bg_color = ( isset( $advanced_bg_color ) ) ? $advanced_bg_color : '#FF6621';
        $advanced_text_color = ( isset( $advanced_text_color ) ) ? $advanced_text_color : '#ffffff';
        $advanced_bg_color2  = '#' . yith_wcbm_color_with_factor( substr( $advanced_bg_color, 1 ), 0.6 );
        ?>
        .yith-wcbm-on-sale-badge<?php echo $id_advanced_badge ?> {
        color: <?php echo $advanced_text_color ?>;
        font-family: "Open Sans",sans-serif;
        width:70px;
        height:53px;
        background-color: transparent;
        position:relative;
        box-sizing: border-box;
        position: absolute;
        }

        .yith-wcbm-on-sale-badge<?php echo $id_advanced_badge ?> .yith-wcbm-on-sale-badge:before{

        }
        .yith-wcbm-on-sale-badge<?php echo $id_advanced_badge ?> .yith-wcbm-on-sale-badge:after{

        }

        .yith-wcbm-on-sale-badge<?php echo $id_advanced_badge ?> .yith-wcbm-shape1{
        content: ' ';
        width:46px;
        height:53px;
        background-color: #656565;
        position:relative;
        box-sizing: border-box;
        position: absolute;
        top:0px;
        left:13px;
        z-index: 14;
        }

        .yith-wcbm-on-sale-badge<?php echo $id_advanced_badge ?> .yith-wcbm-simbol-sale{
        font-family: 'Open Sans', sans-serif;
        font-size: 10px;
        position: absolute;
        top:27px;
        left:9px;
        text-transform: uppercase;
        text-align: center;
        width: 53px;
        background-color: <?php echo $advanced_bg_color ?>;
        height: 18px;
        line-height: 18px;
        z-index: 16;
        }

        .yith-wcbm-on-sale-badge<?php echo $id_advanced_badge ?> .yith-wcbm-shape2{
        width:60px;
        height: 0;
        border-bottom: 10px solid <?php echo $advanced_bg_color2 ?>;
        border-left: 5px solid transparent;
        border-right: 5px solid transparent;
        position: absolute;
        bottom:3px;
        left: 0;
        z-index: 12;
        box-sizing: content-box;
        }

        .yith-wcbm-on-sale-badge<?php echo $id_advanced_badge ?> .yith-wcbm-shape2:before{
        content: ' ';
        width:60px;
        height: 0;
        border-top: 10px solid <?php echo $advanced_bg_color2 ?>;
        border-left: 5px solid transparent;
        border-right: 5px solid transparent;
        position: absolute;
        top:-7px;
        left: -5px;
        z-index: 12;
        box-sizing: content-box;
        }

        .yith-wcbm-on-sale-badge<?php echo $id_advanced_badge ?> .yith-wcbm-sale-percent,
        .yith-wcbm-on-sale-badge<?php echo $id_advanced_badge ?> .yith-wcbm-saved-money-value{
        position: absolute;
        font-family: 'Open Sans', sans-serif;
        font-size: 19px;
        line-height: 0px;
        top:15px;
        right:29px;
        z-index: 16;
        font-weight: 500;
        }

        .yith-wcbm-on-sale-badge<?php echo $id_advanced_badge ?> .yith-wcbm-simbol-percent,
        .yith-wcbm-on-sale-badge<?php echo $id_advanced_badge ?> .yith-wcbm-saved-money-currency{
        position: absolute;
        font-family: 'Open Sans', sans-serif;
        font-size: 12px;
        line-height: 0px;
        top:18px;
        right:19px;
        z-index: 16;
        }

        .yith-wcbm-on-sale-badge<?php echo $id_advanced_badge ?>.yith-wcbm-advanced-display-amount .yith-wcbm-sale-percent,
        .yith-wcbm-on-sale-badge<?php echo $id_advanced_badge ?>.yith-wcbm-advanced-display-amount .yith-wcbm-simbol-percent,
        .yith-wcbm-on-sale-badge<?php echo $id_advanced_badge ?>.yith-wcbm-advanced-display-percentage .yith-wcbm-saved-money-value,
        .yith-wcbm-on-sale-badge<?php echo $id_advanced_badge ?>.yith-wcbm-advanced-display-percentage .yith-wcbm-saved-money-currency,
        .yith-wcbm-on-sale-badge<?php echo $id_advanced_badge ?> .yith-wcbm-saved-money,
        .yith-wcbm-on-sale-badge<?php echo $id_advanced_badge ?> .yith-wcbm-simbol-off,
        .yith-wcbm-on-sale-badge<?php echo $id_advanced_badge ?> .yith-wcbm-save,
        .yith-wcbm-on-sale-badge<?php echo $id_advanced_badge ?> .yith-wcbm-save,
        .yith-wcbm-on-sale-badge<?php echo $id_advanced_badge ?> .yith-wcbm-simbol-sale-exclamation{
        display: none;
        }
        <?php
        break;

    case '10':
        $image_url = $url . "10.png";
        ?>
        .yith-wcbm-on-sale-badge<?php echo $id_advanced_badge ?> {
        background: url('<?php echo $image_url ?>') no-repeat; font-family: "Open Sans",sans-serif; width: 59px; height: 59px; position: absolute; z-index:10;
        }

        .yith-wcbm-on-sale-badge<?php echo $id_advanced_badge ?> .yith-wcbm-sale-percent,
        .yith-wcbm-on-sale-badge<?php echo $id_advanced_badge ?> .yith-wcbm-saved-money-value{
        position: absolute;
        color: #fff;
        font-size: 15px;
        line-height: 0px;
        top:37px;
        right:25px;
        }
        .yith-wcbm-on-sale-badge<?php echo $id_advanced_badge ?> .yith-wcbm-sale-percent:before,
        .yith-wcbm-on-sale-badge<?php echo $id_advanced_badge ?> .yith-wcbm-saved-money-value:before{
        content: '-';
        }
        .yith-wcbm-on-sale-badge<?php echo $id_advanced_badge ?> .yith-wcbm-simbol-percent,
        .yith-wcbm-on-sale-badge<?php echo $id_advanced_badge ?> .yith-wcbm-saved-money-currency{
        font-size: 10px;
        font-weight: 400;
        position: absolute;
        color: #fff;
        line-height: 0px;
        top:37px;
        right:16px;
        }

        .yith-wcbm-on-sale-badge<?php echo $id_advanced_badge ?> .yith-wcbm-simbol-sale-exclamation{
        font-size: 8px;
        text-transform: uppercase;
        position: absolute;
        top: 17px;
        <?php echo !$flip_text_horizontally ? 'right:15px;' : 'left:14px;'; ?>
        color: #fff;
        line-height: 0;
        }


        .yith-wcbm-on-sale-badge<?php echo $id_advanced_badge ?>.yith-wcbm-advanced-display-amount .yith-wcbm-sale-percent,
        .yith-wcbm-on-sale-badge<?php echo $id_advanced_badge ?>.yith-wcbm-advanced-display-amount .yith-wcbm-simbol-percent,
        .yith-wcbm-on-sale-badge<?php echo $id_advanced_badge ?>.yith-wcbm-advanced-display-percentage .yith-wcbm-saved-money-value,
        .yith-wcbm-on-sale-badge<?php echo $id_advanced_badge ?>.yith-wcbm-advanced-display-percentage .yith-wcbm-saved-money-currency,
        .yith-wcbm-on-sale-badge<?php echo $id_advanced_badge ?> .yith-wcbm-saved-money,
        .yith-wcbm-on-sale-badge<?php echo $id_advanced_badge ?> .yith-wcbm-simbol-sale,
        .yith-wcbm-on-sale-badge<?php echo $id_advanced_badge ?> .yith-wcbm-simbol-off,
        .yith-wcbm-on-sale-badge<?php echo $id_advanced_badge ?> .yith-wcbm-save{
        display: none;
        }
        <?php
        break;

    default:

        break;
}
?>