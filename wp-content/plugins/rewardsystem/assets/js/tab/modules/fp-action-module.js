/*
 * Action Reward Points - Module
 */
jQuery( function ( $ ) {
    var ActionModule = {
        init : function () {
            this.trigger_on_page_load() ;
            this.show_or_hide_for_enable_signup() ;
            this.show_or_hide_for_enable_customreg_fields() ;
            this.show_or_hide_for_enable_social_acc_linking() ;
            this.show_or_hide_for_enable_product_review() ;
            this.show_or_hide_for_enable_blog_post_creation() ;
            this.show_or_hide_for_enable_blog_post_comment() ;
            this.show_or_hide_for_enable_product_creation() ;
            this.show_or_hide_for_enable_page_comment() ;
            this.show_or_hide_for_enable_daily_login() ;
            this.show_or_hide_for_mail_coupon_reward() ;
            this.show_or_hide_for_reward_success_msg() ;
            this.show_or_hide_for_waitlist_subscription() ;
            this.show_or_hide_for_waitlist_converstion() ;
            $( document ).on( 'change' , '#_rs_enable_signup' , this.enable_signup ) ;
            $( document ).on( 'change' , '#rs_enable_points_for_cus_field_reg' , this.enable_customreg_fields ) ;
            $( document ).on( 'change' , '#rs_enable_for_social_account_linking' , this.enable_social_acc_linking ) ;
            $( document ).on( 'change' , '#rs_enable_product_review_points' , this.enable_product_review ) ;
            $( document ).on( 'change' , '#rs_reward_for_Creating_Post' , this.enable_blog_post_creation ) ;
            $( document ).on( 'change' , '#rs_reward_for_comment_Post' , this.enable_blog_post_comment ) ;
            $( document ).on( 'change' , '#rs_reward_for_comment_Page' , this.enable_page_comment ) ;
            $( document ).on( 'change' , '#rs_enable_reward_points_for_login' , this.enable_daily_login ) ;
            $( document ).on( 'change' , '#rs_send_mail_coupon_reward' , this.mail_coupon_reward ) ;
            $( document ).on( 'change' , '#rs_enable_coupon_reward_success_msg' , this.reward_success_msg ) ;
            $( document ).on( 'change' , '#rs_enable_points_for_bp_post_create' , this.toggle_bp_post_create ) ;
            $( document ).on( 'change' , '#rs_enable_points_for_bp_postcomment' , this.toggle_bp_page_comment ) ;
            $( document ).on( 'change' , '#rs_enable_points_for_bp_group_create' , this.toggle_bp_group_create ) ;
            $( document ).on( 'change' , '#rs_enable_for_waitlist_subscribing' , this.show_or_hide_for_waitlist_subscription ) ;
            $( document ).on( 'change' , '#rs_enable_for_waitlist_subscribing' , this.show_or_hide_for_waitlist_converstion ) ;

            $( document ).on( 'click' , '.rs_add_rule_for_custom_reg_field' , this.append_rule_for_cus_reg_field ) ;
            $( document ).on( 'click' , '.rs_remove_rule_for_custom_reg_field' , this.remove_rule_for_cus_reg_field ) ;
            $( document ).on( 'change' , '.rs_search_custom_field' , this.get_selected_field_type ) ;
            $( document ).on( 'click' , '.add' , this.add_rule_for_coupon_usage_reward ) ;
            $( document ).on( 'click' , '.remove' , this.remove_rule_for_coupon_usage_reward ) ;
        } ,
        enable_signup : function () {
            ActionModule.show_or_hide_for_enable_signup() ;
        } ,
        show_or_hide_for_enable_signup : function () {
            if ( jQuery( '#_rs_enable_signup' ).is( ':checked' ) == true ) {
                jQuery( '#rs_select_account_signup_points_award' ).closest( 'tr' ).show() ;
                jQuery( '#rs_reward_signup' ).closest( 'tr' ).show() ;
                jQuery( '#rs_reward_signup_after_first_purchase' ).closest( 'tr' ).show() ;
                if ( jQuery( '#rs_reward_signup_after_first_purchase' ).is( ':checked' ) == true ) {
                    jQuery( '#rs_signup_points_with_purchase_points' ).closest( 'tr' ).show() ;
                } else {
                    jQuery( '#rs_signup_points_with_purchase_points' ).closest( 'tr' ).hide() ;
                }

                jQuery( '#rs_reward_signup_after_first_purchase' ).change( function () {
                    if ( jQuery( '#rs_reward_signup_after_first_purchase' ).is( ':checked' ) == true ) {
                        jQuery( '#rs_signup_points_with_purchase_points' ).closest( 'tr' ).show() ;
                    } else {
                        jQuery( '#rs_signup_points_with_purchase_points' ).closest( 'tr' ).hide() ;
                    }
                } ) ;

                jQuery( '#rs_send_mail_account_signup' ).closest( 'tr' ).show() ;
                if ( jQuery( '#rs_send_mail_account_signup' ).is( ':checked' ) == true ) {
                    jQuery( '#rs_email_subject_account_signup' ).closest( 'tr' ).show() ;
                    jQuery( '#rs_email_message_account_signup' ).closest( 'tr' ).show() ;
                } else {
                    jQuery( '#rs_email_subject_account_signup' ).closest( 'tr' ).hide() ;
                    jQuery( '#rs_email_message_account_signup' ).closest( 'tr' ).hide() ;
                }

                jQuery( '#rs_send_mail_account_signup' ).change( function () {
                    if ( jQuery( '#rs_send_mail_account_signup' ).is( ':checked' ) == true ) {
                        jQuery( '#rs_email_subject_account_signup' ).closest( 'tr' ).show() ;
                        jQuery( '#rs_email_message_account_signup' ).closest( 'tr' ).show() ;
                    } else {
                        jQuery( '#rs_email_subject_account_signup' ).closest( 'tr' ).hide() ;
                        jQuery( '#rs_email_message_account_signup' ).closest( 'tr' ).hide() ;
                    }
                } ) ;
            } else {
                jQuery( '#rs_select_account_signup_points_award' ).closest( 'tr' ).hide() ;
                jQuery( '#rs_reward_signup' ).closest( 'tr' ).hide() ;
                jQuery( '#rs_reward_signup_after_first_purchase' ).closest( 'tr' ).hide() ;
                jQuery( '#rs_signup_points_with_purchase_points' ).closest( 'tr' ).hide() ;
                jQuery( '#rs_send_mail_account_signup' ).closest( 'tr' ).hide() ;
                jQuery( '#rs_email_subject_account_signup' ).closest( 'tr' ).hide() ;
                jQuery( '#rs_email_message_account_signup' ).closest( 'tr' ).hide() ;
            }
        } ,
        enable_customreg_fields : function () {
            ActionModule.show_or_hide_for_enable_customreg_fields() ;
        } ,
        show_or_hide_for_enable_customreg_fields : function () {
            if ( jQuery( '#rs_enable_points_for_cus_field_reg' ).is( ':checked' ) == true ) {
                jQuery( '.rs_rule_creation_for_custom_reg_field' ).parent().show() ;
                jQuery( '#rs_send_mail_cus_field_reg' ).closest( 'tr' ).show() ;
                if ( jQuery( '#rs_send_mail_cus_field_reg' ).is( ':checked' ) == true ) {
                    jQuery( '#rs_email_subject_cus_field_reg' ).closest( 'tr' ).show() ;
                    jQuery( '#rs_email_message_cus_field_reg' ).closest( 'tr' ).show() ;
                } else {
                    jQuery( '#rs_email_subject_cus_field_reg' ).closest( 'tr' ).hide() ;
                    jQuery( '#rs_email_message_cus_field_reg' ).closest( 'tr' ).hide() ;
                }

                jQuery( '#rs_send_mail_cus_field_reg' ).change( function () {
                    if ( jQuery( '#rs_send_mail_cus_field_reg' ).is( ':checked' ) == true ) {
                        jQuery( '#rs_email_subject_cus_field_reg' ).closest( 'tr' ).show() ;
                        jQuery( '#rs_email_message_cus_field_reg' ).closest( 'tr' ).show() ;
                    } else {
                        jQuery( '#rs_email_subject_cus_field_reg' ).closest( 'tr' ).hide() ;
                        jQuery( '#rs_email_message_cus_field_reg' ).closest( 'tr' ).hide() ;
                    }
                } ) ;
            } else {
                jQuery( '.rs_rule_creation_for_custom_reg_field' ).parent().hide() ;
                jQuery( '#rs_send_mail_cus_field_reg' ).closest( 'tr' ).hide() ;
                jQuery( '#rs_email_subject_cus_field_reg' ).closest( 'tr' ).hide() ;
                jQuery( '#rs_email_message_cus_field_reg' ).closest( 'tr' ).hide() ;
            }
        } ,
        enable_social_acc_linking : function () {
            ActionModule.show_or_hide_for_enable_social_acc_linking() ;
        } ,
        show_or_hide_for_enable_social_acc_linking : function () {
            if ( jQuery( '#rs_enable_for_social_account_linking' ).is( ":checked" ) == false ) {
                jQuery( '#rs_reward_for_social_account_linking' ).parent().parent().hide() ;
                jQuery( '#rs_send_mail_for_social_account_linking' ).parent().parent().parent().parent().hide() ;
                jQuery( '#rs_email_subject_for_social_account_linking' ).parent().parent().hide() ;
                jQuery( '#rs_email_message_for_social_account_linking' ).parent().parent().hide() ;
            } else {
                jQuery( '#rs_reward_for_social_account_linking' ).parent().parent().show() ;
                jQuery( '#rs_send_mail_for_social_account_linking' ).parent().parent().parent().parent().show() ;
                if ( jQuery( '#rs_send_mail_for_social_account_linking' ).is( ':checked' ) == true ) {
                    jQuery( '#rs_email_subject_for_social_account_linking' ).parent().parent().show() ;
                    jQuery( '#rs_email_message_for_social_account_linking' ).parent().parent().show() ;
                } else {
                    jQuery( '#rs_email_subject_for_social_account_linking' ).parent().parent().hide() ;
                    jQuery( '#rs_email_message_for_social_account_linking' ).parent().parent().hide() ;
                }

                jQuery( '#rs_send_mail_for_social_account_linking' ).change( function () {
                    if ( jQuery( '#rs_send_mail_for_social_account_linking' ).is( ':checked' ) == true ) {
                        jQuery( '#rs_email_subject_for_social_account_linking' ).parent().parent().show() ;
                        jQuery( '#rs_email_message_for_social_account_linking' ).parent().parent().show() ;
                    } else {
                        jQuery( '#rs_email_subject_for_social_account_linking' ).parent().parent().hide() ;
                        jQuery( '#rs_email_message_for_social_account_linking' ).parent().parent().hide() ;
                    }
                } ) ;
            }
        } ,
        enable_product_review : function () {
            ActionModule.show_or_hide_for_enable_product_review() ;
        } ,
        show_or_hide_for_enable_product_review : function () {
            if ( jQuery( '#rs_enable_product_review_points' ).is( ':checked' ) == true ) {
                jQuery( '.rs_review_reward_status' ).closest( 'tr' ).show() ;
                jQuery( '#rs_reward_product_review' ).closest( 'tr' ).show() ;
                jQuery( '#rs_restrict_reward_product_review' ).closest( 'tr' ).show() ;
                jQuery( '#rs_reward_for_comment_product_review' ).closest( 'tr' ).show() ;
                jQuery( '#rs_send_mail_product_review' ).closest( 'tr' ).show() ;
                if ( jQuery( '#rs_send_mail_product_review' ).is( ':checked' ) == true ) {
                    jQuery( '#rs_email_subject_product_review' ).closest( 'tr' ).show() ;
                    jQuery( '#rs_email_message_product_review' ).closest( 'tr' ).show() ;
                } else {
                    jQuery( '#rs_email_subject_product_review' ).closest( 'tr' ).hide() ;
                    jQuery( '#rs_email_message_product_review' ).closest( 'tr' ).hide() ;
                }

                jQuery( '#rs_send_mail_product_review' ).change( function () {
                    if ( jQuery( '#rs_send_mail_product_review' ).is( ':checked' ) == true ) {
                        jQuery( '#rs_email_subject_product_review' ).closest( 'tr' ).show() ;
                        jQuery( '#rs_email_message_product_review' ).closest( 'tr' ).show() ;
                    } else {
                        jQuery( '#rs_email_subject_product_review' ).closest( 'tr' ).hide() ;
                        jQuery( '#rs_email_message_product_review' ).closest( 'tr' ).hide() ;
                    }
                } ) ;
            } else {
                jQuery( '.rs_review_reward_status' ).closest( 'tr' ).hide() ;
                jQuery( '#rs_reward_product_review' ).closest( 'tr' ).hide() ;
                jQuery( '#rs_restrict_reward_product_review' ).closest( 'tr' ).hide() ;
                jQuery( '#rs_reward_for_comment_product_review' ).closest( 'tr' ).hide() ;
                jQuery( '#rs_send_mail_product_review' ).closest( 'tr' ).hide() ;
                jQuery( '#rs_email_subject_product_review' ).closest( 'tr' ).hide() ;
                jQuery( '#rs_email_message_product_review' ).closest( 'tr' ).hide() ;
            }
        } ,
        enable_blog_post_creation : function () {
            ActionModule.show_or_hide_for_enable_blog_post_creation() ;
        } ,
        show_or_hide_for_enable_blog_post_creation : function () {
            if ( jQuery( '#rs_reward_for_Creating_Post' ).is( ":checked" ) == false ) {
                jQuery( '#rs_reward_post' ).parent().parent().hide() ;
                jQuery( '#rs_reward_post_review' ).parent().parent().hide() ;
                jQuery( '#rs_send_mail_blog_post_create' ).parent().parent().parent().parent().hide() ;
                jQuery( '#rs_email_subject_blog_post_create' ).parent().parent().hide() ;
                jQuery( '#rs_email_message_blog_post_create' ).parent().parent().hide() ;
                jQuery( '#rs_post_visible_for' ).parent().parent().hide() ;
            } else {
                jQuery( '#rs_reward_post' ).parent().parent().show() ;
                jQuery( '#rs_send_mail_blog_post_create' ).parent().parent().parent().parent().show() ;
                jQuery( '#rs_post_visible_for' ).parent().parent().show() ;
                if ( jQuery( '#rs_send_mail_blog_post_create' ).is( ':checked' ) == true ) {
                    jQuery( '#rs_email_subject_blog_post_create' ).parent().parent().show() ;
                    jQuery( '#rs_email_message_blog_post_create' ).parent().parent().show() ;
                } else {
                    jQuery( '#rs_email_subject_blog_post_create' ).parent().parent().hide() ;
                    jQuery( '#rs_email_message_blog_post_create' ).parent().parent().hide() ;
                }

                jQuery( '#rs_send_mail_blog_post_create' ).change( function () {
                    if ( jQuery( '#rs_send_mail_blog_post_create' ).is( ':checked' ) == true ) {
                        jQuery( '#rs_email_subject_blog_post_create' ).parent().parent().show() ;
                        jQuery( '#rs_email_message_blog_post_create' ).parent().parent().show() ;
                    } else {
                        jQuery( '#rs_email_subject_blog_post_create' ).parent().parent().hide() ;
                        jQuery( '#rs_email_message_blog_post_create' ).parent().parent().hide() ;
                    }
                } ) ;
            }
        } ,
        enable_blog_post_comment : function () {
            ActionModule.show_or_hide_for_enable_blog_post_comment() ;
        } ,
        show_or_hide_for_enable_blog_post_comment : function () {
            if ( jQuery( '#rs_reward_for_comment_Post' ).is( ":checked" ) == false ) {
                jQuery( '#rs_restrict_reward_post_comment' ).parent().parent().parent().parent().hide() ;
                jQuery( '.rs_post_comment_reward_status' ).closest( 'tr' ).hide() ;
                jQuery( '#rs_reward_post_review' ).parent().parent().hide() ;
                jQuery( '#rs_send_mail_blog_post_comment' ).parent().parent().parent().parent().hide() ;
                jQuery( '#rs_email_subject_blog_post_comment' ).parent().parent().hide() ;
                jQuery( '#rs_email_message_blog_post_comment' ).parent().parent().hide() ;
            } else {
                jQuery( '#rs_reward_post_review' ).parent().parent().show() ;
                jQuery( '.rs_post_comment_reward_status' ).closest( 'tr' ).show() ;
                jQuery( '#rs_restrict_reward_post_comment' ).parent().parent().parent().parent().show() ;
                jQuery( '#rs_send_mail_blog_post_comment' ).parent().parent().parent().parent().show() ;
                if ( jQuery( '#rs_send_mail_blog_post_comment' ).is( ':checked' ) == true ) {
                    jQuery( '#rs_email_subject_blog_post_comment' ).parent().parent().show() ;
                    jQuery( '#rs_email_message_blog_post_comment' ).parent().parent().show() ;
                } else {
                    jQuery( '#rs_email_subject_blog_post_comment' ).parent().parent().hide() ;
                    jQuery( '#rs_email_message_blog_post_comment' ).parent().parent().hide() ;
                }

                jQuery( '#rs_send_mail_blog_post_comment' ).change( function () {
                    if ( jQuery( '#rs_send_mail_blog_post_comment' ).is( ':checked' ) == true ) {
                        jQuery( '#rs_email_subject_blog_post_comment' ).parent().parent().show() ;
                        jQuery( '#rs_email_message_blog_post_comment' ).parent().parent().show() ;
                    } else {
                        jQuery( '#rs_email_subject_blog_post_comment' ).parent().parent().hide() ;
                        jQuery( '#rs_email_message_blog_post_comment' ).parent().parent().hide() ;
                    }
                } ) ;
            }
        } ,
        enable_product_creation : function () {
            ActionModule.show_or_hide_for_enable_product_creation() ;
        } ,
        show_or_hide_for_enable_product_creation : function () {
            if ( jQuery( '#rs_reward_for_enable_product_create' ).is( ":checked" ) == false ) {
                jQuery( '#rs_reward_Product_create' ).parent().parent().hide() ;
                jQuery( '#rs_send_mail_product_create' ).parent().parent().parent().parent().hide() ;
                jQuery( '#rs_email_subject_product_create' ).parent().parent().hide() ;
                jQuery( '#rs_email_message_product_create' ).parent().parent().hide() ;
            } else {
                jQuery( '#rs_reward_Product_create' ).parent().parent().show() ;
                jQuery( '#rs_send_mail_product_create' ).parent().parent().parent().parent().show() ;
                if ( jQuery( '#rs_send_mail_product_create' ).is( ':checked' ) == true ) {
                    jQuery( '#rs_email_subject_product_create' ).parent().parent().show() ;
                    jQuery( '#rs_email_message_product_create' ).parent().parent().show() ;
                } else {
                    jQuery( '#rs_email_subject_product_create' ).parent().parent().hide() ;
                    jQuery( '#rs_email_message_product_create' ).parent().parent().hide() ;
                }

                jQuery( '#rs_send_mail_product_create' ).change( function () {
                    if ( jQuery( '#rs_send_mail_product_create' ).is( ':checked' ) == true ) {
                        jQuery( '#rs_email_subject_product_create' ).parent().parent().show() ;
                        jQuery( '#rs_email_message_product_create' ).parent().parent().show() ;
                    } else {
                        jQuery( '#rs_email_subject_product_create' ).parent().parent().hide() ;
                        jQuery( '#rs_email_message_product_create' ).parent().parent().hide() ;
                    }
                } ) ;
            }
        } ,
        enable_page_comment : function () {
            ActionModule.show_or_hide_for_enable_page_comment() ;
        } ,
        show_or_hide_for_enable_page_comment : function () {
            if ( jQuery( '#rs_reward_for_comment_Page' ).is( ":checked" ) == false ) {
                jQuery( '#rs_reward_page_review' ).parent().parent().hide() ;
                jQuery( '.rs_page_comment_reward_status' ).closest( 'tr' ).hide() ;
                jQuery( '#rs_restrict_reward_page_comment' ).parent().parent().parent().parent().hide() ;
                jQuery( '#rs_send_mail_page_comment' ).parent().parent().parent().parent().hide() ;
                jQuery( '#rs_email_subject_page_comment' ).parent().parent().hide() ;
                jQuery( '#rs_email_message_page_comment' ).parent().parent().hide() ;
            } else {
                jQuery( '#rs_reward_page_review' ).parent().parent().show() ;
                jQuery( '.rs_page_comment_reward_status' ).closest( 'tr' ).show() ;
                jQuery( '#rs_restrict_reward_page_comment' ).parent().parent().parent().parent().show() ;
                jQuery( '#rs_send_mail_page_comment' ).parent().parent().parent().parent().show() ;
                if ( jQuery( '#rs_send_mail_page_comment' ).is( ':checked' ) == true ) {
                    jQuery( '#rs_email_subject_page_comment' ).parent().parent().show() ;
                    jQuery( '#rs_email_message_page_comment' ).parent().parent().show() ;
                } else {
                    jQuery( '#rs_email_subject_page_comment' ).parent().parent().hide() ;
                    jQuery( '#rs_email_message_page_comment' ).parent().parent().hide() ;
                }

                jQuery( '#rs_send_mail_page_comment' ).change( function () {
                    if ( jQuery( '#rs_send_mail_page_comment' ).is( ':checked' ) == true ) {
                        jQuery( '#rs_email_subject_page_comment' ).parent().parent().show() ;
                        jQuery( '#rs_email_message_page_comment' ).parent().parent().show() ;
                    } else {
                        jQuery( '#rs_email_subject_page_comment' ).parent().parent().hide() ;
                        jQuery( '#rs_email_message_page_comment' ).parent().parent().hide() ;
                    }
                } ) ;
            }
        } ,
        enable_daily_login : function () {
            ActionModule.show_or_hide_for_enable_daily_login() ;
        } ,
        show_or_hide_for_enable_daily_login : function () {
            if ( jQuery( '#rs_enable_reward_points_for_login' ).is( ':checked' ) ) {
                jQuery( '#rs_reward_points_for_login' ).closest( 'tr' ).show() ;
                jQuery( '#rs_reward_for_social_network_login' ).closest( 'tr' ).show() ;
                jQuery( '#rs_send_mail_login' ).closest( 'tr' ).show() ;
                jQuery( '#rs_enable_for_social_network_login' ).closest( 'tr' ).show() ;
                if ( jQuery( '#rs_send_mail_login' ).is( ':checked' ) ) {
                    jQuery( '#rs_email_subject_login' ).closest( 'tr' ).show() ;
                    jQuery( '#rs_email_message_login' ).closest( 'tr' ).show() ;
                } else {
                    jQuery( '#rs_email_subject_login' ).closest( 'tr' ).hide() ;
                    jQuery( '#rs_email_message_login' ).closest( 'tr' ).hide() ;
                }

                jQuery( '#rs_send_mail_login' ).change( function () {
                    if ( jQuery( '#rs_send_mail_login' ).is( ':checked' ) ) {
                        jQuery( '#rs_email_subject_login' ).closest( 'tr' ).show() ;
                        jQuery( '#rs_email_message_login' ).closest( 'tr' ).show() ;
                    } else {
                        jQuery( '#rs_email_subject_login' ).closest( 'tr' ).hide() ;
                        jQuery( '#rs_email_message_login' ).closest( 'tr' ).hide() ;
                    }
                } ) ;
            } else {
                jQuery( '#rs_reward_points_for_login' ).closest( 'tr' ).hide() ;
                jQuery( '#rs_reward_for_social_network_login' ).closest( 'tr' ).hide() ;
                jQuery( '#rs_send_mail_login' ).closest( 'tr' ).hide() ;
                jQuery( '#rs_email_subject_login' ).closest( 'tr' ).hide() ;
                jQuery( '#rs_email_message_login' ).closest( 'tr' ).hide() ;
                jQuery( '#rs_enable_for_social_network_login' ).closest( 'tr' ).hide() ;
            }
        } ,
        mail_coupon_reward : function () {
            ActionModule.show_or_hide_for_mail_coupon_reward() ;
        } ,
        show_or_hide_for_mail_coupon_reward : function () {
            if ( jQuery( '#rs_send_mail_coupon_reward' ).is( ':checked' ) == true ) {
                jQuery( '#rs_email_subject_coupon_reward' ).closest( 'tr' ).show() ;
                jQuery( '#rs_email_message_coupon_reward' ).closest( 'tr' ).show() ;
            } else {
                jQuery( '#rs_email_subject_coupon_reward' ).closest( 'tr' ).hide() ;
                jQuery( '#rs_email_message_coupon_reward' ).closest( 'tr' ).hide() ;
            }
        } ,
        reward_success_msg : function () {
            ActionModule.show_or_hide_for_reward_success_msg() ;
        } ,
        show_or_hide_for_reward_success_msg : function () {
            if ( jQuery( '#rs_enable_coupon_reward_success_msg' ).is( ':checked' ) ) {
                jQuery( '#rs_coupon_applied_reward_success' ).closest( 'tr' ).show() ;
            } else {
                jQuery( '#rs_coupon_applied_reward_success' ).closest( 'tr' ).hide() ;
            }
        } ,
        trigger_on_page_load : function () {
            this.toggle_bp_post_create_action( '#rs_enable_points_for_bp_post_create' ) ;
            this.toggle_bp_page_comment_action( '#rs_enable_points_for_bp_postcomment' ) ;
            this.toggle_bp_group_create_action( '#rs_enable_points_for_bp_group_create' ) ;
        } ,

        toggle_bp_post_create : function ( event ) {
            event.preventDefault() ;
            var $this = $( event.currentTarget ) ;
            ActionModule.toggle_bp_post_create_action( $this ) ;
        } ,

        toggle_bp_page_comment : function ( event ) {
            event.preventDefault() ;
            var $this = $( event.currentTarget ) ;
            ActionModule.toggle_bp_page_comment_action( $this ) ;
        } ,
        toggle_bp_group_create : function ( event ) {
            event.preventDefault() ;
            var $this = $( event.currentTarget ) ;
            ActionModule.toggle_bp_group_create_action( $this ) ;
        } ,

        toggle_bp_post_create_action : function ( $this ) {
            var bool = $( $this ).is( ':checked' ) ;
            if ( bool == true ) {
                $( '#rs_points_for_bp_post_create' ).parent().parent().show() ;
            } else {
                $( '#rs_points_for_bp_post_create' ).parent().parent().hide() ;
            }
        } ,

        toggle_bp_page_comment_action : function ( $this ) {
            var bool = $( $this ).is( ':checked' ) ;
            if ( bool == true ) {
                $( '#rs_points_for_bp_postcomment' ).parent().parent().show() ;
            } else {
                $( '#rs_points_for_bp_postcomment' ).parent().parent().hide() ;
            }
        } ,

        toggle_bp_group_create_action : function ( $this ) {
            var bool = $( $this ).is( ':checked' ) ;
            if ( bool == true ) {
                $( '#rs_points_for_bp_group_create' ).parent().parent().show() ;
                $( '#rs_points_for_bp_group_create_limit' ).parent().parent().show() ;
            } else {
                $( '#rs_points_for_bp_group_create' ).parent().parent().hide() ;
                $( '#rs_points_for_bp_group_create_limit' ).parent().parent().hide() ;
            }
        } ,
        show_or_hide_for_waitlist_subscription : function () {
            if ( jQuery( '#rs_enable_for_waitlist_subscribing' ).is( ":checked" ) == false ) {
                jQuery( '#rs_reward_for_waitlist_subscribing' ).parent().parent().hide() ;
                jQuery( '#rs_send_mail_for_waitlist_subscribing' ).parent().parent().parent().parent().hide() ;
                jQuery( '#rs_email_subject_for_waitlist_subscribing' ).parent().parent().hide() ;
                jQuery( '#rs_email_message_for_waitlist_subscribing' ).parent().parent().hide() ;
            } else {
                jQuery( '#rs_reward_for_waitlist_subscribing' ).parent().parent().show() ;
                jQuery( '#rs_send_mail_for_waitlist_subscribing' ).parent().parent().parent().parent().show() ;
                if ( jQuery( '#rs_send_mail_for_waitlist_subscribing' ).is( ':checked' ) == true ) {
                    jQuery( '#rs_email_subject_for_waitlist_subscribing' ).parent().parent().show() ;
                    jQuery( '#rs_email_message_for_waitlist_subscribing' ).parent().parent().show() ;
                } else {
                    jQuery( '#rs_email_subject_for_waitlist_subscribing' ).parent().parent().hide() ;
                    jQuery( '#rs_email_message_for_waitlist_subscribing' ).parent().parent().hide() ;
                }

                jQuery( '#rs_send_mail_for_waitlist_subscribing' ).change( function () {
                    if ( jQuery( '#rs_send_mail_for_waitlist_subscribing' ).is( ':checked' ) == true ) {
                        jQuery( '#rs_email_subject_for_waitlist_subscribing' ).parent().parent().show() ;
                        jQuery( '#rs_email_message_for_waitlist_subscribing' ).parent().parent().show() ;
                    } else {
                        jQuery( '#rs_email_subject_for_waitlist_subscribing' ).parent().parent().hide() ;
                        jQuery( '#rs_email_message_for_waitlist_subscribing' ).parent().parent().hide() ;
                    }
                } ) ;
            }
        } ,
        show_or_hide_for_waitlist_converstion : function () {
            if ( jQuery( '#rs_enable_for_waitlist_sale_conversion' ).is( ":checked" ) == false ) {
                jQuery( '#rs_reward_for_waitlist_sale_conversion' ).parent().parent().hide() ;
                jQuery( '#rs_send_mail_for_waitlist_sale_conversion' ).parent().parent().parent().parent().hide() ;
                jQuery( '#rs_email_subject_for_waitlist_sale_conversion' ).parent().parent().hide() ;
                jQuery( '#rs_email_message_for_waitlist_sale_conversion' ).parent().parent().hide() ;
            } else {
                jQuery( '#rs_reward_for_waitlist_sale_conversion' ).parent().parent().show() ;
                jQuery( '#rs_send_mail_for_waitlist_sale_conversion' ).parent().parent().parent().parent().show() ;
                if ( jQuery( '#rs_send_mail_for_waitlist_sale_conversion' ).is( ':checked' ) == true ) {
                    jQuery( '#rs_email_subject_for_waitlist_sale_conversion' ).parent().parent().show() ;
                    jQuery( '#rs_email_message_for_waitlist_sale_conversion' ).parent().parent().show() ;
                } else {
                    jQuery( '#rs_email_subject_for_waitlist_sale_conversion' ).parent().parent().hide() ;
                    jQuery( '#rs_email_message_for_waitlist_sale_conversion' ).parent().parent().hide() ;
                }

                jQuery( '#rs_send_mail_for_waitlist_sale_conversion' ).change( function () {
                    if ( jQuery( '#rs_send_mail_for_waitlist_sale_conversion' ).is( ':checked' ) == true ) {
                        jQuery( '#rs_email_subject_for_waitlist_sale_conversion' ).parent().parent().show() ;
                        jQuery( '#rs_email_message_for_waitlist_sale_conversion' ).parent().parent().show() ;
                    } else {
                        jQuery( '#rs_email_subject_for_waitlist_sale_conversion' ).parent().parent().hide() ;
                        jQuery( '#rs_email_message_for_waitlist_sale_conversion' ).parent().parent().hide() ;
                    }
                } ) ;
            }
        } ,
        append_rule_for_cus_reg_field : function ( event ) {
            event.preventDefault() ;
            ActionModule.block( '.rs_rule_for_custom_reg_field' ) ;
            var count = parseInt( $( 'input#rs_rule_id_for_custom_reg_field:last' ).val() ) ;
            count = count + 1 || 0 ;
            var data = {
                action : 'add_wcf_fields' ,
                count : count ,
                sumo_security : fp_action_params.cus_reg_fields_nonce
            } ;
            $.post( fp_action_params.ajaxurl , data , function ( response ) {
                if ( true === response.success ) {
                    $( '#rs_append_rule_for_custom_reg_field' ).append( response.data.content ) ;
                    ActionModule.unblock( '.rs_rule_for_custom_reg_field' ) ;
                    jQuery( 'body' ).trigger( 'wc-enhanced-select-init' ) ;
                } else {
                    window.alert( response.data.error ) ;
                    ActionModule.unblock( '.rs_rule_for_custom_reg_field' ) ;
                }
            } ) ;
        } ,
        get_selected_field_type : function ( event ) {
            event.preventDefault() ;
            var $this = $( event.currentTarget ) ;
            var count = parseInt( $( $this ).closest( 'tr' ).find( '#rs_rule_id_for_custom_reg_field' ).val() ) ;
            var data = {
                action : 'wcf_field_type' ,
                field_id : $( $this ).val() ,
                sumo_security : fp_action_params.cus_reg_fields_nonce
            } ;
            $.post( fp_action_params.ajaxurl , data , function ( response ) {
                if ( true === response.success ) {
                    $( $this ).closest( 'tr' ).find( '.rs_label_for_cus_field_type' ).html( response.data.content ) ;
                    $( $this ).closest( 'tr' ).find( '.rs_label_for_cus_field_type_hidden' ).val( response.data.content ) ;
                    if ( response.data.content == 'DATEPICKER' ) {
                        $( $this ).closest( 'tr' ).find( '.rs_label_for_datepicker_type' ).html( '<select style="width:50% !important;" name="rs_rule_for_custom_reg_field[' + count + '][repeat_points]"><option value="no">No</option><option value="yes">Yes</option></select>' ) ;
                        $( $this ).closest( 'tr' ).find( '.rs_label_award_points_for_filling_datepicker' ).html( '<input type="checkbox" name="rs_rule_for_custom_reg_field[' + count + '][award_points_for_filling]"/>' ) ;
                    } else {
                        $( $this ).closest( 'tr' ).find( '.rs_label_for_datepicker_type' ).html( 'N/A' ) ;
                        $( $this ).closest( 'tr' ).find( '.rs_label_award_points_for_filling_datepicker' ).html( 'N/A' ) ;
                    }
                } else {
                    window.alert( response.data.error ) ;
                    ActionModule.unblock( '.rs_rule_for_custom_reg_field' ) ;
                }
            } ) ;
        } ,
        remove_rule_for_cus_reg_field : function ( event ) {
            event.preventDefault() ;
            var $this = $( event.currentTarget ) ;
            $( $this ).parent().parent().remove() ;
        } ,

        add_rule_for_coupon_usage_reward : function ( event ) {
            event.preventDefault( ) ;
            var $this = $( event.currentTarget ) ;
            var usage_count = parseInt( $( 'input#rs_rule_id_for_coupon_usage_reward:last' ).val( ) ) ;
            var count = usage_count + 1 || 1 ;
            var data = {
                action : 'add_coupon_usage_reward_rule' ,
                rule_count : count ,
                sumo_security : fp_action_params.add_coupon_usage_rule_nonce
            } ;
            $.post( fp_action_params.ajaxurl , data , function ( response ) {
                if ( true == response.success && response.data.html ) {
                    $( $this ).closest( '.rsdynamicrulecreation_coupon_usage' ).find( 'tbody' ).append( response.data.html ) ;
                    $( $this ).trigger( 'srp-enhanced-init' ) ;
                } else {
                    alert( response.data.error ) ;
                }
            } ) ;
        } ,
        remove_rule_for_coupon_usage_reward : function ( event ) {
            event.preventDefault( ) ;
            var $this = $( event.currentTarget ) ;
            $( $this ).closest( "tr" ).remove() ;
        } ,
        block : function ( id ) {
            $( id ).block( {
                message : null ,
                overlayCSS : {
                    background : '#fff' ,
                    opacity : 0.6
                }
            } ) ;
        } ,
        unblock : function ( id ) {
            $( id ).unblock() ;
        } ,
    } ;
    ActionModule.init() ;
} ) ;