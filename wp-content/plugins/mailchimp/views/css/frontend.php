<?php ?>
    .mc_error_msg {
        color: red;
        margin-bottom: 1.0em;
    }
    .mc_success_msg {
        color: green;
        margin-bottom: 1.0em;
    }
    .mc_merge_var{
        padding:0;
        margin:0;
    }
<?php
// If we're utilizing custom styles
if (get_option('mc_custom_style')=='on'){
    ?>
    #mc_signup_form {
        padding:5px;
        border-width: <?php echo get_option('mc_form_border_width'); ?>px;
        border-style: <?php echo (get_option('mc_form_border_width')==0) ? 'none' : 'solid'; ?>;
        border-color: #<?php echo get_option('mc_form_border_color'); ?>;
        color: #<?php echo get_option('mc_form_text_color'); ?>;
        background-color: #<?php echo get_option('mc_form_background'); ?>;
    }


    .mc_custom_border_hdr {
        border-width: <?php echo get_option('mc_header_border_width'); ?>px;
        border-style: <?php echo (get_option('mc_header_border_width')==0) ? 'none' : 'solid'; ?>;
        border-color: #<?php echo get_option('mc_header_border_color'); ?>;
        color: #<?php echo get_option('mc_header_text_color'); ?>;
        background-color: #<?php echo get_option('mc_header_background'); ?>;
        <!--    font-size: 1.2em;-->
        padding:5px 10px;
        width: 100%;
    }
    <?php
}
?>
    #mc_signup_container {}
    #mc_signup_form {}
    #mc_signup_form .mc_var_label {}
    #mc_signup_form .mc_input {}
    #mc-indicates-required {
        width:100%;
    }
    #mc_display_rewards {}
    .mc_interests_header {
        font-weight:bold;
    }
    div.mc_interest{
        width:100%;
    }
    #mc_signup_form input.mc_interest {}
    #mc_signup_form select {}
    #mc_signup_form label.mc_interest_label {
        display:inline;
    }
    .mc_signup_submit {
        text-align:center;
    }
    ul.mc_list {
        list-style-type: none;
    }
    ul.mc_list li {
        font-size: 12px;
    }
    #ui-datepicker-div .ui-datepicker-year {
        display: none;
    }
    #ui-datepicker-div.show .ui-datepicker-year {
        display: inline;
        padding-left: 3px
    }