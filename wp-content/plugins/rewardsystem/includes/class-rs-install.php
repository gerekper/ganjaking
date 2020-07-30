<?php
/*
 * Trigger this upon plugin install
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit ; // Exit if accessed directly.
}

if ( ! class_exists( 'RSInstall' ) ) {

    class RSInstall {

        private static $dbversion = '1.2.3' ;

        public static function init() {

            add_action( 'rscronjob' , array( __CLASS__ , 'send_mail_based_on_cron' ) ) ;

            add_action( 'rs_send_mail_before_expiry' , array( __CLASS__ , 'point_expiry_cron_callback' ) ) ;

            add_action( 'rs_restrict_product_purchase_for_time' , array( __CLASS__ , 'award_product_purchase_points_based_on_cron' ) , 10 , 1 ) ;
        }

        /* Assign Default Values for All Tab */

        public static function set_default_value_for_tab() {
            $tabs = array(
                'fprsgeneral' ,
                'fprsmodules' ,
                'fprsaddremovepoints' ,
                'fprsmessage' ,
                'fprslocalization' ,
                'fprsuserrewardpoints' ,
                'fprsmasterlog' ,
                'fprsadvanced' ,
                    ) ;

            $tabs = apply_filters( 'rs_default_value_tabs' , $tabs ) ;
            if ( ! srp_check_is_array( $tabs ) )
                return ;

            foreach ( $tabs as $tab ) {

                include_once SRP_PLUGIN_PATH . '/includes/admin/tabs/class-rs-' . $tab . '-tab.php' ;

                do_action( 'rs_default_settings_' . $tab ) ;
            }
        }

        /* Assign Default Values for All Modules */

        public static function set_default_value_for_modules() {
            $modules = array(
                'fpproductpurchase' ,
                'fpreferralsystem' ,
                'fpsocialreward' ,
                'fpactionreward' ,
                'fppointexpiry' ,
                'fpredeeming' ,
                'fppointprice' ,
                'fpsocialreward' ,
                'fpgiftvoucher' ,
                'fpsms'
                    ) ;

            $modules = apply_filters( 'rs_default_value_modules' , $modules ) ;
            if ( ! srp_check_is_array( $modules ) )
                return ;

            foreach ( $modules as $module ) {
                //include current page functionality.
                include_once SRP_PLUGIN_PATH . '/includes/admin/tabs/modules/class-rs-' . $module . '-module-tab.php' ;

                do_action( 'rs_default_settings_' . $module ) ;
            }
        }

        /* Award Points for Product Purchase based on Cron Time */

        public static function award_product_purchase_points_based_on_cron( $order_id ) {
            if ( get_post_meta( $order_id , 'rs_order_status_reached' , true ) == 'yes' )
                award_points_for_product_purchase_based_on_cron( $order_id ) ;
        }

        /* Point Expiry Cron callback. */

        public function point_expiry_cron_callback() {

            if ( 'yes' != get_option( 'rs_email_template_expire_activated' ) ) {
                return ;
            }

            $TemplateName = get_option( 'rs_select_template' ) ;
            if ( empty( $TemplateName ) ) {
                return ;
            }

            $no_of_days = ( int ) days_from_point_expiry_email() ;
            if ( ! $no_of_days ) {
                return ;
            }

            global $wpdb ;
            $tablename = $wpdb->prefix . 'rs_expiredpoints_email' ;
            $templates = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM $tablename WHERE template_name=%s AND rs_status='ACTIVE'" , $TemplateName ) , ARRAY_A ) ;
            if ( ! srp_check_is_array( $templates ) ) {
                return ;
            }

            $table_name         = $wpdb->prefix . 'rspointexpiry' ;
            $overall_point_data = $wpdb->get_results( $wpdb->prepare( "SELECT * , SUM(earnedpoints) as points FROM $table_name WHERE expirydate > %d AND expirydate NOT IN(999999999999) AND expiredpoints IN(0) GROUP BY expirydate" , time() ) , ARRAY_A ) ;
            if ( ! srp_check_is_array( $overall_point_data ) ) {
                return ;
            }

            $timestamp = array() ;

            foreach ( $overall_point_data as $value ) {

                $expiry_date = isset( $value[ 'expirydate' ] ) ? absint( $value[ 'expirydate' ] ) : 0 ;
                $user_id     = isset( $value[ 'userid' ] ) ? absint( $value[ 'userid' ] ) : 0 ;

                if ( ! $expiry_date || ! $user_id ) {
                    continue ;
                }

                if ( 'yes' == get_user_meta( $user_id , 'unsub_value' , true ) ) {
                    continue ;
                }

                $date_to_send_mail = strtotime( '-' . $no_of_days . 'days' , $expiry_date ) ;
                if ( in_array( $date_to_send_mail , ( array ) get_option( 'rs_point_expiry_email_send_based_on_date' ) ) ) {
                    continue ;
                }

                if ( time() >= $date_to_send_mail ) {

                    $timestamp[]     = $date_to_send_mail ;
                    $user_point_data = $wpdb->get_results( $wpdb->prepare( "SELECT *,SUM(earnedpoints) as points FROM $table_name WHERE expirydate > %d  AND expirydate NOT IN(999999999999) AND expiredpoints IN(0) AND userid = %d GROUP BY expirydate " , time() , $user_id ) , ARRAY_A ) ;
                    self::send_mail( $user_point_data , $user_id , $templates ) ;
                }
            }

            if ( srp_check_is_array( $timestamp ) ) {
                $timestamp = array_merge( $timestamp , ( array ) get_option( 'rs_check_expiry_email_send_based_on_date' ) ) ;
                update_option( 'rs_point_expiry_email_send_based_on_date' , $timestamp ) ;
            }
        }

        public static function send_mail( $newdata , $userid , $Templates ) {

            if ( ! srp_check_is_array( $newdata ) ) {
                return ;
            }

            $user              = get_userdata( $userid ) ;
            $user_wmpl_lang    = empty( $user_wmpl_lang ) ? 'en' : get_user_meta( $userid , 'rs_wpml_lang' , true ) ;
            $site_referral_url = get_option( 'rs_restrict_referral_points_for_same_ip' ) == 'yes' ? esc_url_raw( add_query_arg( array( 'ref' => $user->user_login , 'ip' => base64_encode( get_referrer_ip_address() ) ) , site_url() ) ) : esc_url_raw( add_query_arg( array( 'ref' => $user->user_login ) , site_url() ) ) ;
            $subject           = $Templates[ 0 ][ 'subject' ] ;
            $url_to_click      = "<a href=" . site_url() . ">" . site_url() . "</a>" ;
            $wpnonce           = wp_create_nonce( 'rs_unsubscribe_' . $userid ) ;
            $unsublink         = esc_url_raw( add_query_arg( array( 'userid' => $userid , 'unsub' => 'yes' , 'nonce' => $wpnonce ) , site_url() ) ) ;
            $message           = $Templates[ 0 ][ 'message' ] ;
            $message           = str_replace( array( '{rssitelink}' , '{rsfirstname}' , '{rslastname}' , '{site_referral_url}' , '{rs_points_expire}' ) , array( $url_to_click , $user->user_firstname , $user->user_lastname , $site_referral_url , self::email_content( $newdata ) ) , $message ) ;
            $message           = do_shortcode( $message ) ; //shortcode feature

            global $unsublink2 ;
            $unsublink2   = str_replace( '{rssitelinkwithid}' , $unsublink , get_option( 'rs_unsubscribe_link_for_email' ) ) ;
            add_filter( 'woocommerce_email_footer_text' , 'srp_footer_link' ) ;
            ob_start() ;
            wc_get_template( 'emails/email-header.php' , array( 'email_heading' => $subject ) ) ;
            echo $message ;
            wc_get_template( 'emails/email-footer.php' ) ;
            $woo_temp_msg = ob_get_clean() ;
            $headers      = "MIME-Version: 1.0\r\n" ;
            $headers      .= "Content-Type: text/html; charset=UTF-8\r\n" ;

            if ( $Templates[ 0 ][ 'sender_opt' ] == 'local' ) {
                FPRewardSystem::$rs_from_email_address = $Templates[ 0 ][ 'from_email' ] ;
                FPRewardSystem::$rs_from_name          = $Templates[ 0 ][ 'from_name' ] ;
            }
            add_filter( 'woocommerce_email_from_address' , 'rs_alter_from_email_of_woocommerce' , 10 , 2 ) ;
            add_filter( 'woocommerce_email_from_name' , 'rs_alter_from_name_of_woocommerce' , 10 , 2 ) ;

            if ( WC_VERSION <= ( float ) ('2.2.0') ) {
                if ( wp_mail( $user->user_email , $subject , $woo_temp_msg , $headers = '' ) ) {
                    
                }
            } else {
                $mailer = WC()->mailer() ;
                $mailer->send( $user->user_email , $subject , $woo_temp_msg , $headers ) ;
            }
            remove_filter( 'woocommerce_email_from_address' , 'rs_alter_from_email_of_woocommerce' , 10 , 2 ) ;
            remove_filter( 'woocommerce_email_from_name' , 'rs_alter_from_name_of_woocommerce' , 10 , 2 ) ;
            FPRewardSystem::$rs_from_email_address = false ;
            FPRewardSystem::$rs_from_name          = false ;
        }

        public static function email_content( $newdata ) {
            $sliced_array = array_slice( $newdata , 0 , 50 , true ) ;
            ob_start() ;
            ?>
            <table style="border: 1px solid #000;border-collapse: collapse;">
                <thead style="background: black;color:#fff;">
                    <tr>
                        <th><?php _e( 'S.No' , SRP_LOCALE ) ; ?></th>
                        <th><?php _e( 'Points' , SRP_LOCALE ) ; ?></th>
                        <th><?php _e( 'Expiry Date' , SRP_LOCALE ) ; ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $i            = 1 ;
                    foreach ( $sliced_array as $data ) {
                        ?>
                        <tr>
                            <td><?php echo $i ; ?></td>
                            <td><?php echo $data[ 'points' ] ; ?></td>
                            <td><?php echo date( 'd-m-Y H:i A' , $data[ 'expirydate' ] ) ; ?></td>
                        </tr>
                        <?php
                        $i ++ ;
                    }
                    ?>
                </tbody>
            </table>
            <?php
            $content = ob_get_clean() ;
            ob_end_clean() ;
            return $content ;
        }

        public static function get_charset_table() {
            global $wpdb ;
            $charset_collate = $wpdb->has_cap( 'collation' ) ? $wpdb->get_charset_collate() : '' ;
            return $charset_collate ;
        }

        public static function create_table_for_point_expiry() {
            global $wpdb ;
            $table_name = $wpdb->prefix . 'rspointexpiry' ;
            if ( self::rs_check_table_exists( $table_name ) ) {
                $charset_collate = self::get_charset_table() ;
                $sql             = "CREATE TABLE IF NOT EXISTS $table_name (
		id mediumint(9) NOT NULL AUTO_INCREMENT,
		earnedpoints FLOAT,
                usedpoints FLOAT,
                expiredpoints FLOAT,
                userid INT(99),
                earneddate VARCHAR(999) NOT NULL,
                expirydate VARCHAR(999) NOT NULL,
                checkpoints VARCHAR(999) NOT NULL,
                orderid INT(99),
                totalearnedpoints INT(99),
                totalredeempoints INT(99),
                reasonindetail VARCHAR(999),
         	UNIQUE KEY id (id)
                ) $charset_collate;" ;
                require_once( ABSPATH . 'wp-admin/includes/upgrade.php' ) ;
                dbDelta( $sql ) ;
                add_option( 'rs_point_expiry' , self::$dbversion ) ;
            }
            if ( ! self::rs_check_table_exists( $table_name ) ) {
                if ( ! self::rs_check_column_exists( $table_name , 'totalearnedpoints' ) ) {
                    $wpdb->query( "ALTER TABLE $table_name MODIFY totalearnedpoints FLOAT " ) ;
                }
                if ( ! self::rs_check_column_exists( $table_name , 'totalredeempoints' ) ) {
                    $wpdb->query( "ALTER TABLE $table_name MODIFY totalredeempoints FLOAT " ) ;
                }
            }
        }

        public static function rs_update_null_value_to_zero() {
            global $wpdb ;
            $table_name = $wpdb->prefix . 'rspointexpiry' ;
            $querys     = $wpdb->get_results( "SELECT id,usedpoints FROM $table_name WHERE usedpoints IS NULL" , ARRAY_A ) ;
            foreach ( $querys as $query ) {
                $wpdb->update( $table_name , array( 'usedpoints' => 0 ) , array( 'id' => $query[ 'id' ] ) ) ;
            }
        }

        public static function create_table_to_record_earned_points_and_redeem_points() {

            global $wpdb ;
            $getdbversiondata = get_option( "rs_record_points" ) != 'false' ? get_option( 'rs_record_points' ) : "0" ;
            $table_name       = $wpdb->prefix . 'rsrecordpoints' ;
            if ( self::rs_check_table_exists( $table_name ) ) {
                $charset_collate = self::get_charset_table() ;
                $sql             = "CREATE TABLE IF NOT EXISTS $table_name (
		id mediumint(9) NOT NULL AUTO_INCREMENT,
		earnedpoints FLOAT,
                redeempoints FLOAT,
                userid INT(99),
                earneddate VARCHAR(999) NOT NULL,
                expirydate VARCHAR(999) NOT NULL,
                checkpoints VARCHAR(999) NOT NULL,
                earnedequauivalentamount INT(99),
                redeemequauivalentamount INT(99),
                orderid INT(99),
                productid INT(99),
                variationid INT(99),
                refuserid INT(99),
                reasonindetail VARCHAR(999),
                totalpoints INT(99),
                showmasterlog VARCHAR(999),
                showuserlog VARCHAR(999),
                nomineeid INT(99),
                nomineepoints INT(99),
         	UNIQUE KEY id (id)
                ) $charset_collate;" ;
                require_once( ABSPATH . 'wp-admin/includes/upgrade.php' ) ;
                dbDelta( $sql ) ;
                add_option( 'rs_record_points' , self::$dbversion ) ;
            }
            if ( ! self::rs_check_table_exists( $table_name ) ) {
                if ( ! self::rs_check_column_exists( $table_name , 'redeemequauivalentamount' ) ) {
                    $wpdb->query( "ALTER TABLE $table_name MODIFY redeemequauivalentamount FLOAT " ) ;
                }
                if ( ! self::rs_check_column_exists( $table_name , 'totalpoints' ) ) {
                    $wpdb->query( "ALTER TABLE $table_name MODIFY totalpoints FLOAT " ) ;
                }
                if ( ! self::rs_check_column_exists( $table_name , 'earnedequauivalentamount' ) ) {
                    $wpdb->query( "ALTER TABLE $table_name MODIFY earnedequauivalentamount FLOAT " ) ;
                }
            }
        }

        public static function create_table_for_gift_voucher() {
            global $wpdb ;
            $table_name = $wpdb->prefix . 'rsgiftvoucher' ;
            if ( self::rs_check_table_exists( $table_name ) ) {
                $charset_collate = self::get_charset_table() ;
                $sql             = "CREATE TABLE IF NOT EXISTS $table_name (
		id mediumint(9) NOT NULL AUTO_INCREMENT,
		vouchercode VARCHAR(999) NOT NULL,
                points FLOAT,
                vouchercreated VARCHAR(999) NOT NULL,
                voucherexpiry VARCHAR(999) NOT NULL,
                memberused VARCHAR(999) NOT NULL,
         	UNIQUE KEY id (id)
                ) $charset_collate;" ;
                require_once( ABSPATH . 'wp-admin/includes/upgrade.php' ) ;
                dbDelta( $sql ) ;
            }
            if ( ! self::rs_check_table_exists( $table_name ) ) {
                if ( self::rs_check_column_exists( $table_name , 'voucher_code_usage' ) ) {
                    $wpdb->query( "ALTER TABLE $table_name ADD voucher_code_usage VARCHAR(20) NOT NULL" ) ;
                }
                if ( self::rs_check_column_exists( $table_name , 'voucher_code_usage_limit' ) ) {
                    $wpdb->query( "ALTER TABLE $table_name ADD voucher_code_usage_limit VARCHAR(20) NOT NULL" ) ;
                }
                if ( self::rs_check_column_exists( $table_name , 'voucher_code_usage_limit_val' ) ) {
                    $wpdb->query( "ALTER TABLE $table_name ADD voucher_code_usage_limit_val INT(20) NOT NULL" ) ;
                }
            }
        }

        public static function create_table_for_email_template() {
            global $wpdb ;
            $getdbversiondata = get_option( "rs_email_template_version" ) != 'false' ? get_option( 'rs_email_template_version' ) : "0" ;
            $table_name       = $wpdb->prefix . 'rs_templates_email' ;
            if ( self::rs_check_table_exists( $table_name ) ) {
                $charset_collate = self::get_charset_table() ;
                $sql             = "CREATE TABLE IF NOT EXISTS $table_name (
                id mediumint(9) NOT NULL AUTO_INCREMENT,
                template_name LONGTEXT NOT NULL,
                sender_opt VARCHAR(10) NOT NULL DEFAULT 'woo',
                from_name LONGTEXT NOT NULL,
                from_email LONGTEXT NOT NULL,
                subject LONGTEXT NOT NULL,
                message LONGTEXT NOT NULL,
                earningpoints LONGTEXT NOT NULL,
                redeemingpoints LONGTEXT NOT NULL,
                mailsendingoptions LONGTEXT NOT NULL,
                rsmailsendingoptions LONGTEXT NOT NULL,
                minimum_userpoints LONGTEXT NOT NULL,
                sendmail_options VARCHAR(10) NOT NULL DEFAULT '1',
                sendmail_to LONGTEXT NOT NULL,
                sending_type VARCHAR(20) NOT NULL,
                rs_status VARCHAR(20) NOT NULL DEFAULT 'DEACTIVATE',
                UNIQUE KEY id (id)
                ) $charset_collate;" ;
                require_once( ABSPATH . 'wp-admin/includes/upgrade.php' ) ;
                dbDelta( $sql ) ;
                add_option( 'rs_email_template_version' , self::$dbversion ) ;
            }
            if ( ! self::rs_check_table_exists( $table_name ) && self::rs_check_column_exists( $table_name , 'rs_status' ) ) {
                $wpdb->query( "ALTER TABLE $table_name ADD rs_status VARCHAR(20) NOT NULL DEFAULT 'DEACTIVATE' " ) ;
            }
        }

        public static function create_table_for_encash_reward_points() {
            global $wpdb ;
            $getdbversiondata = get_option( "rs_encash_version" ) != 'false' ? get_option( 'rs_encash_version' ) : "0" ;
            $table_name       = $wpdb->prefix . 'sumo_reward_encashing_submitted_data' ;
            if ( self::rs_check_table_exists( $table_name ) ) {
                $charset_collate = self::get_charset_table() ;
                $sql             = "CREATE TABLE IF NOT EXISTS $table_name (
                id mediumint(9) NOT NULL AUTO_INCREMENT,
                userid INT(225),
                userloginname VARCHAR(200),
                pointstoencash VARCHAR(200),
                pointsconvertedvalue VARCHAR(200),
                encashercurrentpoints VARCHAR(200),
                reasonforencash LONGTEXT,
                encashpaymentmethod VARCHAR(200),
                paypalemailid VARCHAR(200),
                otherpaymentdetails LONGTEXT,
                status VARCHAR(200),
                date VARCHAR(300),
                UNIQUE KEY id (id)
                ) $charset_collate;" ;
                require_once( ABSPATH . 'wp-admin/includes/upgrade.php' ) ;
                dbDelta( $sql ) ;
                add_option( 'rs_encash_version' , self::$dbversion ) ;
            }
        }

        public static function create_table_for_send_points() {
            global $wpdb ;
            $getdbversiondata = get_option( "rs_send_points_version" ) != 'false' ? get_option( 'rs_send_points_version' ) : "0" ;
            $table_name       = $wpdb->prefix . 'sumo_reward_send_point_submitted_data' ;
            if ( self::rs_check_table_exists( $table_name ) ) {
                $charset_collate = self::get_charset_table() ;
                $sql             = "CREATE TABLE IF NOT EXISTS $table_name (
                id mediumint(9) NOT NULL AUTO_INCREMENT,
                userid INT(225),
                userloginname VARCHAR(200),
                pointstosend VARCHAR(200),
                sendercurrentpoints VARCHAR(200),
                status VARCHAR(200),
                selecteduser LONGTEXT NOT NULL,
                date VARCHAR(300),
                UNIQUE KEY id (id)
                ) $charset_collate;" ;
                require_once( ABSPATH . 'wp-admin/includes/upgrade.php' ) ;
                dbDelta( $sql ) ;
                add_option( 'rs_send_points_version' , self::$dbversion ) ;
            }
        }

        public static function insert_data_for_email_template() {
            global $wpdb ;
            $table_name       = $wpdb->prefix . 'rs_templates_email' ;
            $email_temp_check = $wpdb->get_results( "SELECT * FROM $table_name" , ARRAY_A ) ;
            if ( srp_check_is_array( $email_temp_check ) )
                return ;

            return $wpdb->insert( $table_name , array(
                        'template_name'        => 'Default' ,
                        'sender_opt'           => 'woo' ,
                        'from_name'            => 'Admin' ,
                        'from_email'           => get_option( 'admin_email' ) ,
                        'subject'              => 'SUMO Rewards Point' ,
                        'message'              => 'Hi {rsfirstname} {rslastname}, <br><br> You have Earned Reward Points: {rspoints} on {rssitelink}  <br><br> You can use this Reward Points to make discounted purchases on {rssitelink} <br><br> Thanks' ,
                        'minimum_userpoints'   => '0' ,
                        'mailsendingoptions'   => '2' ,
                        'rsmailsendingoptions' => '3' ,
                    ) ) ;
        }

        public static function install() {
            self::set_default_value_for_tab() ;
            self::set_default_value_for_modules() ;
            self::create_table_for_point_expiry() ;
            self::create_table_to_record_earned_points_and_redeem_points() ;
            self::create_table_for_email_template() ;
            self::create_table_for_email_template_expired_point() ;
            self::create_table_for_gift_voucher() ;
            self::create_table_for_encash_reward_points() ;
            self::create_table_for_send_points() ;
            self::rs_update_null_value_to_zero() ;
            self::insert_data_for_email_template() ;
            self::insert_data_for_email_template_for_expiry() ;
            self::default_value_for_earning_and_redeem_points() ;
            self::enable_newly_added_module() ;
        }

        /*
         * Function for send mail based on cron time
         */

        public static function create_table_for_email_template_expired_point() {
            global $wpdb ;
            $table_name = $wpdb->prefix . 'rs_expiredpoints_email' ;
            if ( self::rs_check_table_exists( $table_name ) ) {
                $charset_collate = self::get_charset_table() ;
                $sql             = "CREATE TABLE IF NOT EXISTS $table_name (
                id mediumint(9) NOT NULL AUTO_INCREMENT,
                template_name LONGTEXT NOT NULL,
                sender_opt VARCHAR(10) NOT NULL DEFAULT 'woo',
                from_name LONGTEXT NOT NULL,
                from_email LONGTEXT NOT NULL,
                subject LONGTEXT NOT NULL,
                message LONGTEXT NOT NULL,
                noofdays FLOAT,
                rs_status VARCHAR(20) NOT NULL DEFAULT 'DEACTIVATE',
                UNIQUE KEY id (id)
                ) $charset_collate;" ;
                require_once( ABSPATH . 'wp-admin/includes/upgrade.php' ) ;
                dbDelta( $sql ) ;
            }
        }

        public static function insert_data_for_email_template_for_expiry() {
            global $wpdb ;
            $table_name       = $wpdb->prefix . 'rs_expiredpoints_email' ;
            $email_temp_check = $wpdb->get_results( "SELECT * FROM $table_name" , OBJECT ) ;
            if ( empty( $email_temp_check ) ) {
                return $wpdb->insert( $table_name , array(
                            'template_name' => 'Default' ,
                            'sender_opt'    => 'woo' ,
                            'from_name'     => 'Admin' ,
                            'from_email'    => get_option( 'admin_email' ) ,
                            'subject'       => 'SUMO Rewards Point' ,
                            'message'       => 'Hi {rsfirstname} {rslastname}, <br><br>Please check the below table which shows about your earned points with an expiry date. You can make use of those points to get discount on future purchases in {rssitelink} <br><br> {rs_points_expire} <br><br> Thanks' ,
                            'noofdays'      => '' ,
                            'rs_status'     => 'DEACTIVATE' ,
                        ) ) ;
            }
            return ;
        }

        public static function send_mail_based_on_cron() {
            if ( get_option( 'rs_email_activated' ) != "yes" )
                return ;

            global $wpdb ;
            $tablename       = $wpdb->prefix . 'rs_templates_email' ;
            $email_templates = $wpdb->get_results( "SELECT * FROM $tablename" ) ; //all email templates
            if ( ! srp_check_is_array( $email_templates ) )
                return ;

            foreach ( $email_templates as $emails ) {
                if ( $emails->rs_status != "ACTIVE" )
                    continue ;

                if ( $emails->rsmailsendingoptions != 3 )
                    continue ;

                $SiteUrl = "<a href=" . site_url() . ">" . site_url() . "</a>" ;
                if ( $emails->mailsendingoptions == '1' ) { //Send Mail Only Once
                    $maindata = ( int ) get_option( 'rscheckcronsafter' ) + 1 ;
                    update_option( 'rscheckcronsafter' , $maindata ) ;

                    if ( get_option( 'rscheckcronsafter' ) > 1 )
                        continue ;

                    if ( $emails->sendmail_options == '1' ) { //Send Mail for All User
                        foreach ( get_users() as $myuser ) {
                            if ( get_user_meta( $myuser->ID , 'unsub_value' , true ) == 'yes' )
                                continue ;

                            if ( get_option( 'rsemailtemplates' . $myuser->ID ) == 1 )
                                continue ;

                            $PointsData = new RS_Points_data( $myuser->ID ) ;
                            $userpoint  = $PointsData->total_available_points() ;

                            if ( empty( $userpoint ) )
                                continue ;

                            $minimumuserpoints = empty( $emails->minimum_userpoints ) ? 0 : $emails->minimum_userpoints ;
                            if ( $minimumuserpoints > $userpoint )
                                continue ;

                            self::available_points_mail_based_on_cron( $myuser->ID , $emails , $SiteUrl , $userpoint ) ;
                            update_option( 'rsemailtemplates' . $myuser->ID , '1' ) ;
                        }
                    } else { // Send Mail for Selected User
                        $selected_users = maybe_unserialize( $emails->sendmail_to ) ;
                        if ( ! srp_check_is_array( $selected_users ) )
                            continue ;

                        foreach ( $selected_users as $myuser ) {
                            if ( get_user_meta( $myuser , 'unsub_value' , true ) == 'yes' )
                                continue ;

                            if ( get_option( 'rsemailtemplates' . $myuser ) == 1 )
                                continue ;

                            $PointsData = new RS_Points_data( $myuser ) ;
                            $userpoint  = $PointsData->total_available_points() ;

                            if ( empty( $userpoint ) )
                                continue ;

                            $minimumuserpoints = empty( $emails->minimum_userpoints ) ? 0 : $emails->minimum_userpoints ;
                            if ( $minimumuserpoints > $userpoint )
                                continue ;

                            self::available_points_mail_based_on_cron( $myuser , $emails , $SiteUrl , $userpoint ) ;
                            update_option( 'rsemailtemplates' . $myuser , '1' ) ;
                        }
                    }
                } else { // Send Mail Always
                    if ( $emails->sendmail_options == '1' ) {//Send Mail for All User
                        foreach ( get_users() as $myuser ) {
                            if ( get_user_meta( $myuser->ID , 'unsub_value' , true ) == 'yes' )
                                continue ;

                            $PointsData = new RS_Points_data( $myuser->ID ) ;
                            $userpoint  = $PointsData->total_available_points() ;

                            if ( empty( $userpoint ) )
                                continue ;

                            $minimumuserpoints = empty( $emails->minimum_userpoints ) ? 0 : $emails->minimum_userpoints ;
                            if ( $minimumuserpoints > $userpoint )
                                continue ;

                            self::available_points_mail_based_on_cron( $myuser->ID , $emails , $SiteUrl , $userpoint ) ;
                        }
                    } else {//Send Mail for Selected User
                        $selected_users = maybe_unserialize( $emails->sendmail_to ) ;
                        if ( ! srp_check_is_array( $selected_users ) )
                            continue ;

                        foreach ( $selected_users as $myuser ) {
                            if ( get_user_meta( $myuser , 'unsub_value' , true ) == 'yes' )
                                continue ;

                            $PointsData = new RS_Points_data( $myuser ) ;
                            $userpoint  = $PointsData->total_available_points() ;

                            if ( empty( $userpoint ) )
                                continue ;

                            $minimumuserpoints = empty( $emails->minimum_userpoints ) ? 0 : $emails->minimum_userpoints ;
                            if ( $minimumuserpoints > $userpoint )
                                continue ;

                            self::available_points_mail_based_on_cron( $myuser , $emails , $SiteUrl , $userpoint ) ;
                        }
                    }
                }
            }
        }

        public static function available_points_mail_based_on_cron( $userid , $emails , $SiteUrl , $userpoint ) {
            $user              = get_userdata( $userid ) ;
            $user_wmpl_lang    = empty( get_user_meta( $userid , 'rs_wpml_lang' , true ) ) ? 'en' : get_user_meta( $userid , 'rs_wpml_lang' , true ) ;
            $subject           = RSWPMLSupport::fp_wpml_text( 'rs_template_' . $emails->id . '_subject' , $user_wmpl_lang , $emails->subject ) ;
            $PointsValue       = redeem_point_conversion( $userpoint , $userid , 'price') ;
            $PointsValue       = srp_formatted_price( round_off_type( $PointsValue ) ) ;
            $referral_url      = get_option( 'rs_referral_link_refer_a_friend_form' ) != '' ? get_option( 'rs_referral_link_refer_a_friend_form' ) : site_url() ;
            $site_referral_url = get_option( 'rs_restrict_referral_points_for_same_ip' ) == 'yes' ? esc_url_raw( add_query_arg( array( 'ref' => $user->user_login , 'ip' => base64_encode( get_referrer_ip_address() ) ) , $referral_url ) ) : esc_url_raw( add_query_arg( array( 'ref' => $user->user_login ) , $referral_url ) ) ;
            $site_referral_url = get_option( 'rs_referral_activated' ) == 'yes' ? "<a href=" . $site_referral_url . ">" . $site_referral_url . "</a>" : '' ;
            $message           = RSWPMLSupport::fp_wpml_text( 'rs_template_' . $emails->id . '_message' , $user_wmpl_lang , $emails->message ) ;
            $message           = str_replace( array( '{rssitelink}' , '{rsfirstname}' , '{rslastname}' , '{site_referral_url}' , '{rspoints}' , '{rs_points_in_currency}' ) , array( $SiteUrl , $user->user_firstname , $user->user_lastname , $site_referral_url , $userpoint , $PointsValue ) , $message ) ;
            $message           = do_shortcode( $message ) ; //shortcode feature
            if ( $emails->sender_opt == 'local' ) {
                FPRewardSystem::$rs_from_email_address = $emails->from_email ;
                FPRewardSystem::$rs_from_name          = $emails->from_name ;
            }
            add_filter( 'woocommerce_email_from_address' , 'rs_alter_from_email_of_woocommerce' , 10 , 2 ) ;
            add_filter( 'woocommerce_email_from_name' , 'rs_alter_from_name_of_woocommerce' , 10 , 2 ) ;
            send_mail( $user->user_email , $subject , $message ) ;
            remove_filter( 'woocommerce_email_from_address' , 'rs_alter_from_email_of_woocommerce' , 10 , 2 ) ;
            remove_filter( 'woocommerce_email_from_name' , 'rs_alter_from_name_of_woocommerce' , 10 , 2 ) ;
            FPRewardSystem::$rs_from_email_address = false ;
            FPRewardSystem::$rs_from_name          = false ;
        }

        public static function rs_check_column_exists( $table_name , $column_name ) {
            global $wpdb ;
            $data_base     = constant( 'DB_NAME' ) ;
            $column_exists = $wpdb->query( "select * from information_schema.columns where table_schema='$data_base' and table_name = '$table_name' and column_name = '$column_name'" ) ;
            return ( $column_exists === 0 ) ? true : false ;
        }

        public static function rs_check_table_exists( $table_name ) {
            global $wpdb ;
            $data_base     = constant( 'DB_NAME' ) ;
            $column_exists = $wpdb->query( "select * from information_schema.columns where table_schema='$data_base' and table_name = '$table_name'" ) ;
            if ( $column_exists === 0 ) {
                add_option( 'rs_new_update_user' , true ) ;
                return true ; //if not exists return true
            }
            return false ; // if it is exists return false
        }

        public static function default_value_for_earning_and_redeem_points() {
            add_option( 'rs_earn_point' , '1' ) ;
            add_option( 'rs_earn_point_value' , '1' ) ;
            add_option( 'rs_redeem_point' , '1' ) ;
            add_option( 'rs_redeem_point_value' , '1' ) ;
            add_option( 'rs_redeem_point_for_cash_back' , '1' ) ;
            add_option( 'rs_redeem_point_value_for_cash_back' , '1' ) ;
        }

        public static function enable_newly_added_module() {
            global $wpdb ;
            if ( self::rs_check_table_exists( $wpdb->prefix . 'rspointexpiry' ) )
                return ;

            $enabledcount = self::is_buying_enabled() ;
            if ( $enabledcount > 0 && ! (get_option( 'rs_buyingpoints_activated' )) )
                update_option( 'rs_buyingpoints_activated' , 'yes' ) ;
        }

        public static function is_buying_enabled() {
            global $wpdb ;
            $simple_product_ids   = $wpdb->get_col( "SELECT post_id FROM " . $wpdb->prefix . "postmeta WHERE meta_key = '_rewardsystem_buying_reward_points' AND meta_value = 'yes'" ) ;
            $variable_product_ids = $wpdb->get_col( "SELECT post_id FROM " . $wpdb->prefix . "postmeta WHERE meta_key = '_rewardsystem_buying_reward_points' AND meta_value = '1'" ) ;
            return count( $simple_product_ids ) + count( $variable_product_ids ) ;
        }

    }

    RSInstall::init() ;
}
