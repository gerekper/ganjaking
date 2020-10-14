<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" <?php language_attributes(); ?>>
<head>
    <meta http-equiv="Content-type" content="text/html; charset=utf-8"/>
    <meta http-equiv="Imagetoolbar" content="No"/>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>
        <?php esc_html_e('Preview Table', 'ninja-tables') ?>
    </title>
    <?php
    wp_head();
    ?>
    <style type="text/css">

    </style>
</head>
<body>
<div class="nt_preview">
    <div class="nt_preview_header">
        <div class="nt_preview_header_title">
            <ul>
                <li>
                    [ninja_tables id="<?php echo $table_id; ?>"]
                </li>
            </ul>
        </div>
        <div class="nt_preview_header_action">
            <a href="<?php echo admin_url('admin.php?page=ninja_tables#/tables/' . $table_id) ?>">Edit</a>
        </div>
    </div>

    <div class="nt_preview_body">
        <div class="nt_preview_body_wrapper">
            <?php echo do_shortcode('[ninja_tables id="' . $table_id . '"]'); ?>
        </div>
    </div>
    <div class="nt_preview_fotter">
        <p class="nt_preview_fotter_text">You are seeing preview version of Ninja Tables. This table is only accessible for Admin users. Other users
            may not access this page. To use this for in a page please use the following shortcode: [ninja_tables
            id='<?php echo $table_id ?>']</p>
    </div>
</div>
<?php
wp_footer();
?>
</body>
</html>