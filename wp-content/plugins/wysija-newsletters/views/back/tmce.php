<?php
defined('WYSIJA') or die('Restricted access');
class WYSIJA_view_back_tmce extends WYSIJA_view_back{

    var $title='Tiny';
    var $icon='icon-options-general';
    var $scripts=array();

    function __construct(){
        parent::__construct();
    }

    function getScriptsStyles(){
        ?>
        <link rel='stylesheet' href='<?php $urlblog=get_bloginfo('wpurl');echo $urlblog ?>/wp-admin/load-styles.php?c=1&amp;dir=ltr&amp;load=widgets,global,wp-admin' type='text/css' media='all' />
        <link rel='stylesheet' id='colors-css'  href='<?php echo $urlblog ?>/wp-includes/css/buttons.css' type='text/css' media='all' />
        <!--[if lte IE 7]>
        <link rel='stylesheet' id='ie-css'  href='<?php echo $urlblog ?>/wp-admin/css/ie.css' type='text/css' media='all' />
        <![endif]-->
        <link rel='stylesheet'  href='<?php echo $urlblog ?>/wp-content/plugins/wysija-newsletters/css/tmce/widget.css' type='text/css' media='all' />
        <?php wp_print_scripts('jquery'); ?>
        <script type="text/javascript" src="<?php echo $urlblog; ?>/wp-includes/js/tinymce/tiny_mce_popup.js"></script>
        <script type='text/javascript' src='<?php echo $urlblog ?>/wp-content/plugins/wysija-newsletters/js/admin-tmce.js'></script>
        <?php
    }


    function head(){
        // right to left language property
        $direction = 'ltr';
        if(function_exists('is_rtl')) {
            $direction = (is_rtl()) ? 'rtl' : 'ltr';
        }
        $direction = 'rtl';
        ?>
        <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"  dir="<?php echo $direction; ?>" lang="en-US">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title><?php echo $this->title; ?></title>
<?php $this->getScriptsStyles() ?>
<base target="_self" />
</head>
<body>

<?php

    }

    function foot(){
        ?>

        </body>
        </html>
        <?php
    }


    function registerAdd($datawidget = array()){
        $this->head();
        ?>
        <form id="formTable" action="" style="display:block;" class="wp-core-ui" method="post" >

                <div id="widget-form">

                    <?php
                    require_once(WYSIJA_WIDGETS.'wysija_nl.php');
                    $widgetNL=new WYSIJA_NL_Widget(true);
                    $widgetNL->form($datawidget);
                    ?>
                    <input type="hidden" name="widget_id" value="wysija-nl-<?php echo time(); ?>" />
                    <input type="submit" id="widget-insert" class="button-primary action" name="doaction" value="<?php echo esc_attr(__('Insert form', WYSIJA)); ?>">
                </div>

                <div style="clear:both;"></div>
         </form>
            <?php
        $this->foot();
    }

}
