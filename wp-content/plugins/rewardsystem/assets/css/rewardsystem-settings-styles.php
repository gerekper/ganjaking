<?php
add_action( 'admin_head' , 'rs_function_to_include_css' ) ;

function rs_function_to_include_css() {
    if ( get_option( 'rs_color_scheme' ) == '1' ) {
        ?><style>
            @import url('https://fonts.googleapis.com/css?family=Roboto+Condensed');
            .rs_main_wrapper{
                margin-top:30px !important;
                margin-right:0px !important;
                margin-bottom:0px !important;
                margin-left:-20px !important;
                /*     overflow: hidden;*/
            }
            .rs_main{
                background:#fff !important;
            }

            .rs_tab_design{
                margin-bottom:0px !important;
                padding-top:0px !important;
                border-bottom:1px solid rgba(255, 255, 255, 0.2) !important;
                box-shadow: 0 2px 2px rgba(0, 0, 0, 0.5) !important;
                background: -webkit-linear-gradient(to bottom, rgba(76,76,76,1) 0%,rgba(102,102,102,1) 0%,rgba(89,89,89,1) 0%,rgba(71,71,71,1) 0%,rgba(17,17,17,1) 0%,rgba(0,0,0,1) 0%,rgba(0,0,0,1) 0%,rgba(29,29,33,1) 50%,rgba(29,29,33,1) 50%,rgba(29,29,33,1) 50%,rgba(0,0,0,1) 100%) !important;  
                background: -o-linear-gradient(to bottom, rgba(76,76,76,1) 0%,rgba(102,102,102,1) 0%,rgba(89,89,89,1) 0%,rgba(71,71,71,1) 0%,rgba(17,17,17,1) 0%,rgba(0,0,0,1) 0%,rgba(0,0,0,1) 0%,rgba(29,29,33,1) 50%,rgba(29,29,33,1) 50%,rgba(29,29,33,1) 50%,rgba(0,0,0,1) 100%) !important;  
                background: -moz-linear-gradient(to bottom, rgba(76,76,76,1) 0%,rgba(102,102,102,1) 0%,rgba(89,89,89,1) 0%,rgba(71,71,71,1) 0%,rgba(17,17,17,1) 0%,rgba(0,0,0,1) 0%,rgba(0,0,0,1) 0%,rgba(29,29,33,1) 50%,rgba(29,29,33,1) 50%,rgba(29,29,33,1) 50%,rgba(0,0,0,1) 100%) !important;  
                background: linear-gradient(to bottom, rgba(76,76,76,1) 0%,rgba(102,102,102,1) 0%,rgba(89,89,89,1) 0%,rgba(71,71,71,1) 0%,rgba(17,17,17,1) 0%,rgba(0,0,0,1) 0%,rgba(0,0,0,1) 0%,rgba(29,29,33,1) 50%,rgba(29,29,33,1) 50%,rgba(29,29,33,1) 50%,rgba(0,0,0,1) 100%) !important;  
            }

            .rs_tab_design a{
                border-bottom:none !important;
                border-right:1px solid rgba(255, 255, 255, 0.1) !important;
                background: -webkit-linear-gradient(to bottom, rgba(76,76,76,1) 0%,rgba(102,102,102,1) 0%,rgba(89,89,89,1) 0%,rgba(71,71,71,1) 0%,rgba(17,17,17,1) 0%,rgba(0,0,0,1) 0%,rgba(0,0,0,1) 0%,rgba(29,29,33,1) 50%,rgba(29,29,33,1) 50%,rgba(29,29,33,1) 50%,rgba(0,0,0,1) 100%) !important;  
                background: -o-linear-gradient(to bottom, rgba(76,76,76,1) 0%,rgba(102,102,102,1) 0%,rgba(89,89,89,1) 0%,rgba(71,71,71,1) 0%,rgba(17,17,17,1) 0%,rgba(0,0,0,1) 0%,rgba(0,0,0,1) 0%,rgba(29,29,33,1) 50%,rgba(29,29,33,1) 50%,rgba(29,29,33,1) 50%,rgba(0,0,0,1) 100%) !important;  
                background: -moz-linear-gradient(to bottom, rgba(76,76,76,1) 0%,rgba(102,102,102,1) 0%,rgba(89,89,89,1) 0%,rgba(71,71,71,1) 0%,rgba(17,17,17,1) 0%,rgba(0,0,0,1) 0%,rgba(0,0,0,1) 0%,rgba(29,29,33,1) 50%,rgba(29,29,33,1) 50%,rgba(29,29,33,1) 50%,rgba(0,0,0,1) 100%) !important;  
                background: linear-gradient(to bottom, rgba(76,76,76,1) 0%,rgba(102,102,102,1) 0%,rgba(89,89,89,1) 0%,rgba(71,71,71,1) 0%,rgba(17,17,17,1) 0%,rgba(0,0,0,1) 0%,rgba(0,0,0,1) 0%,rgba(29,29,33,1) 50%,rgba(29,29,33,1) 50%,rgba(29,29,33,1) 50%,rgba(0,0,0,1) 100%) !important;  
                font-family: 'Roboto', sans-serif !important;
                font-weight: 500 !important;
                float:left !important;
                color:#eee !important;
                font-size: 14px;
                /*letter-spacing: 0.5px;*/
                margin:0px !important;
                height: 65px;
                width: 8.5%;
                padding:12px 5px 0px 5px!important;
                border-left:none !important;
                border-top:none !important;
                text-align: center;
                word-wrap: break-word !important;
                white-space: normal !important;
                box-shadow: none !important;
            }
            .rs_tab_design ul{
                margin:0px !important;
                width:100%;
                float:left;
                background:linear-gradient(to bottom, rgba(76,76,76,1) 0%,rgba(102,102,102,1) 0%,rgba(89,89,89,1) 0%,rgba(71,71,71,1) 0%,rgba(17,17,17,1) 0%,rgba(0,0,0,1) 0%,rgba(0,0,0,1) 0%,rgba(29,29,33,1) 50%,rgba(29,29,33,1) 50%,rgba(29,29,33,1) 50%,rgba(0,0,0,1) 100%) !important;
            }
            .rs_tab_design ul li{
                margin:0px !important;
            }
            .rs_tab_design ul li:last-child a{
                border-right:none !important;
            }
            .rs_tab_design a:hover{
                color:#fff !important;
                border-top:3px solid #f55b11 !important;
                border-bottom:none !important;
                background:#222 !important;
                font-weight: 500 !important;
                color:#f55b11 !important;
            }
            .rs_tab_design .nav-tab-active:hover{
                border-bottom:1px solid #222 !important;
                background:#222 !important;
                border-top:3px solid #f55b11 !important;
                font-weight: 600 !important;
                color:#f55b11 !important;
            }
            .rs_tab_design .nav-tab-active {
                color:#f55b11 !important;
                background:#222 !important;
                margin-bottom:-1px !important;
                border-bottom:1px solid #222 !important;
                border-top:3px solid #f55b11 !important;
                font-weight: 600 !important;
            }
            .rs_tab_design .rs_sub_tab_design{
                margin-top:-2px !important;
                width:100% !important;
                background:#222 !important;
                border:none !important;
                box-shadow: 0 2px 2px rgba(0, 0, 0, 0.5) !important;
            }
            .rs_tab_design ul .rs_sub_tab_li a{
                display:block !important;
                padding:5px !important;
                color:#fff !important;
                font-weight: 500 !important;
                font-size:13px !important;
                font-family: 'Roboto', sans-serif !important;
                width:auto !important;
                height:auto !important;
                background:#222 !important;
                border:none !important;
                box-shadow: none !important;
            }
            .rs_tab_design ul .rs_sub_tab_li a:hover{
                color:#f55b11 !important;
            }
            .rs_tab_design ul .rs_sub_tab_li .current{
                font-weight: 600 !important;
                color:#f55b11 !important;
                padding:5px !important;
                display:block;
                font-size:13px !important;
            }
            /*******module check settings design***********/
            .rs_modulecheck_wrapper{
                width:95%;
                margin:20px auto;
                border: 1px solid #dfdfdf;
                border-radius:5px !important;
                background:#f9f9f9 !important;
            }
            .rs_modulecheck_wrapper h2{
                background:#ddd;
                border:0px !important;
                font-size:16px !important;
                color:#000 !important;
                font-family: 'Roboto', sans-serif !important;
                margin: 0;
                letter-spacing: 0.5px;
                padding: 12px 0 12px 15px;
            }
            .rs_modulecheck_wrapper p{
                font-family: 'Roboto', sans-serif !important;
                margin-left:10px;
                font-size:14px !important;
                color:#8a929e !important;
            }
            .rs_modulecheck_wrapper p a{
                font-style:italic;
            }
            .rs_modulecheck_wrapper h3{
                font-family: 'Roboto', sans-serif !important;
                padding-left:10px !important;
            }
            .rs_modulecheck_wrapper p b{
                color:#666 !important;
                font-size:14px !important;
            }
            .rs_modulecheck_wrapper .search-box{
                margin:10px !important;
            }
            .rs_modulecheck_wrapper .displaying-num{
                padding-right:10px !important;
            }

            .rs_modulecheck_wrapper .tablenav-pages{
                padding-right:10px !important;
                padding-bottom:10px !important;
            }
            .rs_modulecheck_wrapper input[type="text"]{
                box-shadow: 0 -5px 10px 2px rgba(0, 0, 0, 0.03) inset;
                border:none ;
                width:350px !important;
                border:1px solid #ccc ;
                border-radius:5px;
                height:30px !important;
            }        
            .rs_modulecheck_wrapper textarea{
                box-shadow: 0 -5px 10px 2px rgba(0, 0, 0, 0.03) inset;
                border:none ;
                width:350px !important;
                border:1px solid #ccc ;
                border-radius:5px;
                height:auto;
            }
            .rs_modulecheck_wrapper select{
                box-shadow: 0 -5px 10px 2px rgba(0, 0, 0, 0.03) inset;
                border:none ;
                width:350px !important;
                border:1px solid #ccc ;
                border-radius:5px;
                height:30px;
            }
            .rs_modulecheck_wrapper input[type="number"]{
                box-shadow: 0 -5px 10px 2px rgba(0, 0, 0, 0.03) inset;
                border:none ;
                width:350px !important;
                border:1px solid #ccc ;
                border-radius:5px;
                height:30px !important;
            } 
            .rs_modulecheck_wrapper label{
                font-weight:500 !important;
            }
            .rs_modulecheck_wrapper table th{
                font-weight:500 !important;
            }
            .rs_modulecheck_wrapper table{
                width:98%;
                margin-left:15px !important;
                font-family: 'Roboto', sans-serif !important;
            }
            .rs_modulecheck_wrapper table label{
                font-family: 'Roboto', sans-serif !important;
            }
            .rs_modulecheck_wrapper table td{
                color:#8a929e !important;
            }
            /* Membership wrapper*/

            .rs_membership_compatible_wrapper{
                width:95%;
                margin:20px auto;
                border: 1px solid #dfdfdf;
                border-radius:5px !important;
                background:#f9f9f9 !important;
            }
            .rs_membership_compatible_wrapper h2{
                background:#ddd url(<?php echo SRP_PLUGIN_DIR_URL . "assets/images/arrow-216-12.png" ?>) right 15px center no-repeat !important;
                border:0px !important;
                font-size:16px !important;
                color:#000 !important;
                font-family: 'Roboto', sans-serif !important;
                margin: 0;
                letter-spacing: 0.5px;
                padding: 12px 0 12px 15px;
                cursor:pointer;
            }
            .rs_membership_compatible_wrapper p{
                font-family: 'Roboto', sans-serif !important;
                margin-left:10px;
                font-size:14px !important;
                color:#8a929e !important;
            }
            .rs_membership_compatible_wrapper p a{
                font-style:italic;
            }
            .rs_membership_compatible_wrapper h3{
                font-family: 'Roboto', sans-serif !important;
                padding-left:10px !important;
            }
            .rs_membership_compatible_wrapper p b{
                color:#666 !important;
                font-size:14px !important;
            }
            .rs_membership_compatible_wrapper .search-box{
                margin:10px !important;
            }
            .rs_membership_compatible_wrapper .displaying-num{
                padding-right:10px !important;
            }

            .rs_membership_compatible_wrapper .tablenav-pages{
                padding-right:10px !important;
                padding-bottom:10px !important;
            }
            .rs_membership_compatible_wrapper input[type="text"]{
                box-shadow: 0 -5px 10px 2px rgba(0, 0, 0, 0.03) inset;
                border:none ;
                width:350px !important;
                border:1px solid #ccc ;
                border-radius:5px;
                height:30px !important;
            }        
            .rs_membership_compatible_wrapper textarea{
                box-shadow: 0 -5px 10px 2px rgba(0, 0, 0, 0.03) inset;
                border:none ;
                width:350px !important;
                border:1px solid #ccc ;
                border-radius:5px;
                height:auto;
            }
            .rs_membership_compatible_wrapper select{
                box-shadow: 0 -5px 10px 2px rgba(0, 0, 0, 0.03) inset;
                border:none ;
                width:350px !important;
                border:1px solid #ccc ;
                border-radius:5px;
                height:30px;
            }
            .rs_membership_compatible_wrapper input[type="number"]{
                box-shadow: 0 -5px 10px 2px rgba(0, 0, 0, 0.03) inset;
                border:none ;
                width:350px !important;
                border:1px solid #ccc ;
                border-radius:5px;
                height:30px !important;
            } 
            .rs_membership_compatible_wrapper label{
                font-weight:500 !important;
            }
            .rs_membership_compatible_wrapper table th{
                font-weight:500 !important;
            }
            .rs_membership_compatible_wrapper table{
                width:98%;
                margin-left:15px !important;
                font-family: 'Roboto', sans-serif !important;
            }
            .rs_membership_compatible_wrapper table label{
                font-family: 'Roboto', sans-serif !important;
            }
            .rs_membership_compatible_wrapper table td{
                color:#8a929e !important;
            }

            /* Membership wrapper end*/

            /* Waiting List wrapper */

            .rs_bsn_compatible_wrapper{
                width:95%;
                margin:20px auto;
                border: 1px solid #dfdfdf;
                border-radius:5px !important;
                background:#f9f9f9 !important;
            }
            .rs_bsn_compatible_wrapper h2{
                background:#ddd url(<?php echo SRP_PLUGIN_DIR_URL . "assets/images/arrow-216-12.png" ?>) right 15px center no-repeat !important;
                border:0px !important;
                font-size:16px !important;
                color:#000 !important;
                font-family: 'Roboto', sans-serif !important;
                margin: 0;
                letter-spacing: 0.5px;
                padding: 12px 0 12px 15px;
                cursor:pointer;
            }
            .rs_bsn_compatible_wrapper p{
                font-family: 'Roboto', sans-serif !important;
                margin-left:10px;
                font-size:14px !important;
                color:#8a929e !important;
            }
            .rs_bsn_compatible_wrapper p a{
                font-style:italic;
            }
            .rs_bsn_compatible_wrapper h3{
                font-family: 'Roboto', sans-serif !important;
                padding-left:10px !important;
            }
            .rs_bsn_compatible_wrapper p b{
                color:#666 !important;
                font-size:14px !important;
            }
            .rs_bsn_compatible_wrapper .search-box{
                margin:10px !important;
            }
            .rs_bsn_compatible_wrapper .displaying-num{
                padding-right:10px !important;
            }

            .rs_bsn_compatible_wrapper .tablenav-pages{
                padding-right:10px !important;
                padding-bottom:10px !important;
            }
            .rs_bsn_compatible_wrapper input[type="text"]{
                box-shadow: 0 -5px 10px 2px rgba(0, 0, 0, 0.03) inset;
                border:none ;
                width:350px !important;
                border:1px solid #ccc ;
                border-radius:5px;
                height:30px !important;
            }        
            .rs_bsn_compatible_wrapper textarea{
                box-shadow: 0 -5px 10px 2px rgba(0, 0, 0, 0.03) inset;
                border:none ;
                width:350px !important;
                border:1px solid #ccc ;
                border-radius:5px;
                height:auto;
            }
            .rs_bsn_compatible_wrapper select{
                box-shadow: 0 -5px 10px 2px rgba(0, 0, 0, 0.03) inset;
                border:none ;
                width:350px !important;
                border:1px solid #ccc ;
                border-radius:5px;
                height:30px;
            }
            .rs_bsn_compatible_wrapper input[type="number"]{
                box-shadow: 0 -5px 10px 2px rgba(0, 0, 0, 0.03) inset;
                border:none ;
                width:350px !important;
                border:1px solid #ccc ;
                border-radius:5px;
                height:30px !important;
            } 
            .rs_bsn_compatible_wrapper label{
                font-weight:500 !important;
            }
            .rs_bsn_compatible_wrapper table th{
                font-weight:500 !important;
            }
            .rs_bsn_compatible_wrapper table{
                width:98%;
                margin-left:15px !important;
                font-family: 'Roboto', sans-serif !important;
            }
            .rs_bsn_compatible_wrapper table label{
                font-family: 'Roboto', sans-serif !important;
            }
            .rs_bsn_compatible_wrapper table td{
                color:#8a929e !important;
            }

            /*Waiting List wrapper end*/

            /* Affiliate Pro wrapper */

            .rs_affs_compatible_wrapper{
                width:95%;
                margin:20px auto;
                border: 1px solid #dfdfdf;
                border-radius:5px !important;
                background:#f9f9f9 !important;
            }
            .rs_affs_compatible_wrapper h2{
                background:#ddd url(<?php echo SRP_PLUGIN_DIR_URL . "admin/images/arrow-216-12.png" ?>) right 15px center no-repeat !important;
                border:0px !important;
                font-size:16px !important;
                color:#000 !important;
                font-family: 'Roboto', sans-serif !important;
                margin: 0;
                letter-spacing: 0.5px;
                padding: 12px 0 12px 15px;
                cursor:pointer;
            }
            .rs_affs_compatible_wrapper p{
                font-family: 'Roboto', sans-serif !important;
                margin-left:10px;
                font-size:14px !important;
                color:#8a929e !important;
            }
            .rs_affs_compatible_wrapper p a{
                font-style:italic;
            }
            .rs_affs_compatible_wrapper h3{
                font-family: 'Roboto', sans-serif !important;
                padding-left:10px !important;
            }
            .rs_affs_compatible_wrapper p b{
                color:#666 !important;
                font-size:14px !important;
            }
            .rs_affs_compatible_wrapper .search-box{
                margin:10px !important;
            }
            .rs_affs_compatible_wrapper .displaying-num{
                padding-right:10px !important;
            }

            .rs_affs_compatible_wrapper .tablenav-pages{
                padding-right:10px !important;
                padding-bottom:10px !important;
            }
            .rs_affs_compatible_wrapper input[type="text"]{
                box-shadow: 0 -5px 10px 2px rgba(0, 0, 0, 0.03) inset;
                border:none ;
                width:350px !important;
                border:1px solid #ccc ;
                border-radius:5px;
                height:30px !important;
            }        
            .rs_affs_compatible_wrapper textarea{
                box-shadow: 0 -5px 10px 2px rgba(0, 0, 0, 0.03) inset;
                border:none ;
                width:350px !important;
                border:1px solid #ccc ;
                border-radius:5px;
                height:auto;
            }
            .rs_affs_compatible_wrapper select{
                box-shadow: 0 -5px 10px 2px rgba(0, 0, 0, 0.03) inset;
                border:none ;
                width:350px !important;
                border:1px solid #ccc ;
                border-radius:5px;
                height:30px;
            }
            .rs_affs_compatible_wrapper input[type="number"]{
                box-shadow: 0 -5px 10px 2px rgba(0, 0, 0, 0.03) inset;
                border:none ;
                width:350px !important;
                border:1px solid #ccc ;
                border-radius:5px;
                height:30px !important;
            } 
            .rs_affs_compatible_wrapper label{
                font-weight:500 !important;
            }
            .rs_affs_compatible_wrapper table th{
                font-weight:500 !important;
            }
            .rs_affs_compatible_wrapper table{
                width:98%;
                margin-left:15px !important;
                font-family: 'Roboto', sans-serif !important;
            }
            .rs_affs_compatible_wrapper table label{
                font-family: 'Roboto', sans-serif !important;
            }
            .rs_affs_compatible_wrapper table td{
                color:#8a929e !important;
            }

            /*Affiliate Pro wrapper End*/

            /* Social Login wrapper */

            .rs_fpwcrs_compatible_wrapper{
                width:95%;
                margin:20px auto;
                border: 1px solid #dfdfdf;
                border-radius:5px !important;
                background:#f9f9f9 !important;
            }
            .rs_fpwcrs_compatible_wrapper h2{
                background:#ddd url(<?php echo SRP_PLUGIN_DIR_URL . "assets/images/arrow-216-12.png" ?>) right 15px center no-repeat !important;
                border:0px !important;
                font-size:16px !important;
                color:#000 !important;
                font-family: 'Roboto', sans-serif !important;
                margin: 0;
                letter-spacing: 0.5px;
                padding: 12px 0 12px 15px;
                cursor:pointer;
            }
            .rs_fpwcrs_compatible_wrapper p{
                font-family: 'Roboto', sans-serif !important;
                margin-left:10px;
                font-size:14px !important;
                color:#8a929e !important;
            }
            .rs_fpwcrs_compatible_wrapper p a{
                font-style:italic;
            }
            .rs_fpwcrs_compatible_wrapper h3{
                font-family: 'Roboto', sans-serif !important;
                padding-left:10px !important;
            }
            .rs_fpwcrs_compatible_wrapper p b{
                color:#666 !important;
                font-size:14px !important;
            }
            .rs_fpwcrs_compatible_wrapper .search-box{
                margin:10px !important;
            }
            .rs_fpwcrs_compatible_wrapper .displaying-num{
                padding-right:10px !important;
            }

            .rs_fpwcrs_compatible_wrapper .tablenav-pages{
                padding-right:10px !important;
                padding-bottom:10px !important;
            }
            .rs_fpwcrs_compatible_wrapper input[type="text"]{
                box-shadow: 0 -5px 10px 2px rgba(0, 0, 0, 0.03) inset;
                border:none ;
                width:350px !important;
                border:1px solid #ccc ;
                border-radius:5px;
                height:30px !important;
            }        
            .rs_fpwcrs_compatible_wrapper textarea{
                box-shadow: 0 -5px 10px 2px rgba(0, 0, 0, 0.03) inset;
                border:none ;
                width:350px !important;
                border:1px solid #ccc ;
                border-radius:5px;
                height:auto;
            }
            .rs_fpwcrs_compatible_wrapper select{
                box-shadow: 0 -5px 10px 2px rgba(0, 0, 0, 0.03) inset;
                border:none ;
                width:350px !important;
                border:1px solid #ccc ;
                border-radius:5px;
                height:30px;
            }
            .rs_fpwcrs_compatible_wrapper input[type="number"]{
                box-shadow: 0 -5px 10px 2px rgba(0, 0, 0, 0.03) inset;
                border:none ;
                width:350px !important;
                border:1px solid #ccc ;
                border-radius:5px;
                height:30px !important;
            } 
            .rs_fpwcrs_compatible_wrapper label{
                font-weight:500 !important;
            }
            .rs_fpwcrs_compatible_wrapper table th{
                font-weight:500 !important;
            }
            .rs_fpwcrs_compatible_wrapper table{
                width:98%;
                margin-left:15px !important;
                font-family: 'Roboto', sans-serif !important;
            }
            .rs_fpwcrs_compatible_wrapper table label{
                font-family: 'Roboto', sans-serif !important;
            }
            .rs_fpwcrs_compatible_wrapper table td{
                color:#8a929e !important;
            }

            /*Social Login wrapper end*/


            /*        subscriptions wrapper*/

            .rs_subscription_compatible_wrapper{
                width:95%;
                margin:20px auto;
                border: 1px solid #dfdfdf;
                border-radius:5px !important;
                background:#f9f9f9 !important;
            }
            .rs_subscription_compatible_wrapper h2{
                background:#ddd url(<?php echo SRP_PLUGIN_DIR_URL . "assets/images/arrow-216-12.png" ?>) right 15px center no-repeat !important;
                border:0px !important;
                font-size:16px !important;
                color:#000 !important;
                font-family: 'Roboto', sans-serif !important;
                margin: 0;
                letter-spacing: 0.5px;
                padding: 12px 0 12px 15px;
                cursor:pointer;
            }
            .rs_subscription_compatible_wrapper p{
                font-family: 'Roboto', sans-serif !important;
                margin-left:10px;
                font-size:14px !important;
                color:#8a929e !important;
            }
            .rs_subscription_compatible_wrapper p a{
                font-style:italic;
            }
            .rs_subscription_compatible_wrapper h3{
                font-family: 'Roboto', sans-serif !important;
                padding-left:10px !important;
            }
            .rs_subscription_compatible_wrapper p b{
                color:#666 !important;
                font-size:14px !important;
            }
            .rs_subscription_compatible_wrapper .search-box{
                margin:10px !important;
            }
            .rs_subscription_compatible_wrapper .displaying-num{
                padding-right:10px !important;
            }

            .rs_subscription_compatible_wrapper .tablenav-pages{
                padding-right:10px !important;
                padding-bottom:10px !important;
            }
            .rs_subscription_compatible_wrapper input[type="text"]{
                box-shadow: 0 -5px 10px 2px rgba(0, 0, 0, 0.03) inset;
                border:none ;
                width:350px !important;
                border:1px solid #ccc ;
                border-radius:5px;
                height:30px !important;
            }        
            .rs_subscription_compatible_wrapper textarea{
                box-shadow: 0 -5px 10px 2px rgba(0, 0, 0, 0.03) inset;
                border:none ;
                width:350px !important;
                border:1px solid #ccc ;
                border-radius:5px;
                height:auto;
            }
            .rs_subscription_compatible_wrapper select{
                box-shadow: 0 -5px 10px 2px rgba(0, 0, 0, 0.03) inset;
                border:none ;
                width:350px !important;
                border:1px solid #ccc ;
                border-radius:5px;
                height:30px;
            }
            .rs_subscription_compatible_wrapper input[type="number"]{
                box-shadow: 0 -5px 10px 2px rgba(0, 0, 0, 0.03) inset;
                border:none ;
                width:350px !important;
                border:1px solid #ccc ;
                border-radius:5px;
                height:30px !important;
            } 
            .rs_subscription_compatible_wrapper label{
                font-weight:500 !important;
            }
            .rs_subscription_compatible_wrapper table th{
                font-weight:500 !important;
            }
            .rs_subscription_compatible_wrapper table{
                width:98%;
                margin-left:15px !important;
                font-family: 'Roboto', sans-serif !important;
            }
            .rs_subscription_compatible_wrapper table label{
                font-family: 'Roboto', sans-serif !important;
            }
            .rs_subscription_compatible_wrapper table td{
                color:#8a929e !important;
            }

            /*        subscriptions wrapper end*/
            /***********subscription wrapper show/hide************/
            /*            .rs_subscription_compatible_wrapper p{
                            display:none;
                        }
                        .rs_subscription_compatible_wrapper table{
                            display:none;
                        }
                        .rs_subscription_compatible_wrapper div{
                            display:none;
                        }*/
            /***********subscription wrapper show/hide end ************/



            /*        paymentplan wrapper*/

            .rs_payment_plan_compatible_wrapper{
                width:95%;
                margin:20px auto;
                border: 1px solid #dfdfdf;
                border-radius:5px !important;
                background:#f9f9f9 !important;
            }
            .rs_payment_plan_compatible_wrapper h2{
                background:#ddd url(<?php echo SRP_PLUGIN_DIR_URL . "assets/images/arrow-216-12.png" ?>) right 15px center no-repeat !important;
                border:0px !important;
                font-size:16px !important;
                color:#000 !important;
                font-family: 'Roboto', sans-serif !important;
                margin: 0;
                letter-spacing: 0.5px;
                padding: 12px 0 12px 15px;
                cursor:pointer;
            }
            .rs_payment_plan_compatible_wrapper p{
                font-family: 'Roboto', sans-serif !important;
                margin-left:10px;
                font-size:14px !important;
                color:#8a929e !important;
            }
            .rs_payment_plan_compatible_wrapper p a{
                font-style:italic;
            }
            .rs_payment_plan_compatible_wrapper h3{
                font-family: 'Roboto', sans-serif !important;
                padding-left:10px !important;
            }
            .rs_payment_plan_compatible_wrapper p b{
                color:#666 !important;
                font-size:14px !important;
            }
            .rs_payment_plan_compatible_wrapper .search-box{
                margin:10px !important;
            }
            .rs_payment_plan_compatible_wrapper .displaying-num{
                padding-right:10px !important;
            }

            .rs_payment_plan_compatible_wrapper .tablenav-pages{
                padding-right:10px !important;
                padding-bottom:10px !important;
            }
            .rs_payment_plan_compatible_wrapper input[type="text"]{
                box-shadow: 0 -5px 10px 2px rgba(0, 0, 0, 0.03) inset;
                border:none ;
                width:350px !important;
                border:1px solid #ccc ;
                border-radius:5px;
                height:30px !important;
            }        
            .rs_payment_plan_compatible_wrapper textarea{
                box-shadow: 0 -5px 10px 2px rgba(0, 0, 0, 0.03) inset;
                border:none ;
                width:350px !important;
                border:1px solid #ccc ;
                border-radius:5px;
                height:auto;
            }
            .rs_payment_plan_compatible_wrapper select{
                box-shadow: 0 -5px 10px 2px rgba(0, 0, 0, 0.03) inset;
                border:none ;
                width:350px !important;
                border:1px solid #ccc ;
                border-radius:5px;
                height:30px;
            }
            .rs_payment_plan_compatible_wrapper input[type="number"]{
                box-shadow: 0 -5px 10px 2px rgba(0, 0, 0, 0.03) inset;
                border:none ;
                width:350px !important;
                border:1px solid #ccc ;
                border-radius:5px;
                height:30px !important;
            } 
            .rs_payment_plan_compatible_wrapper label{
                font-weight:500 !important;
            }
            .rs_payment_plan_compatible_wrapper table th{
                font-weight:500 !important;
            }
            .rs_payment_plan_compatible_wrapper table{
                width:98%;
                margin-left:15px !important;
                font-family: 'Roboto', sans-serif !important;
            }
            .rs_payment_plan_compatible_wrapper table label{
                font-family: 'Roboto', sans-serif !important;
            }
            .rs_payment_plan_compatible_wrapper table td{
                color:#8a929e !important;
            }

            /*        subscriptions wrapper end*/
            /***********subscription wrapper show/hide************/
            /*            .rs_payment_plan_compatible_wrapper p{
                            display:none;
                        }
                        .rs_payment_plan_compatible_wrapper table{
                            display:none;
                        }
                        .rs_payment_plan_compatible_wrapper div{
                            display:none;
                        }*/
            /***********subscription wrapper show/hide end ************/
            /*        coupons wrapper*/

            .rs_coupon_compatible_wrapper{
                width:95%;
                margin:20px auto;
                border: 1px solid #dfdfdf;
                border-radius:5px !important;
                background:#f9f9f9 !important;
            }
            .rs_coupon_compatible_wrapper h2{
                background:#ddd url(<?php echo SRP_PLUGIN_DIR_URL . "assets/images/arrow-216-12.png" ?>) right 15px center no-repeat !important;
                border:0px !important;
                font-size:16px !important;
                color:#000 !important;
                font-family: 'Roboto', sans-serif !important;
                margin: 0;
                letter-spacing: 0.5px;
                padding: 12px 0 12px 15px;
                cursor:pointer;
            }
            .rs_coupon_compatible_wrapper p{
                font-family: 'Roboto', sans-serif !important;
                margin-left:10px;
                font-size:14px !important;
                color:#8a929e !important;
            }
            .rs_coupon_compatible_wrapper p a{
                font-style:italic;
            }
            .rs_coupon_compatible_wrapper h3{
                font-family: 'Roboto', sans-serif !important;
                padding-left:10px !important;
            }
            .rs_coupon_compatible_wrapper p b{
                color:#666 !important;
                font-size:14px !important;
            }
            .rs_coupon_compatible_wrapper .search-box{
                margin:10px !important;
            }
            .rs_coupon_compatible_wrapper .displaying-num{
                padding-right:10px !important;
            }

            .rs_coupon_compatible_wrapper .tablenav-pages{
                padding-right:10px !important;
                padding-bottom:10px !important;
            }
            .rs_coupon_compatible_wrapper input[type="text"]{
                box-shadow: 0 -5px 10px 2px rgba(0, 0, 0, 0.03) inset;
                border:none ;
                width:350px !important;
                border:1px solid #ccc ;
                border-radius:5px;
                height:30px !important;
            }        
            .rs_coupon_compatible_wrapper textarea{
                box-shadow: 0 -5px 10px 2px rgba(0, 0, 0, 0.03) inset;
                border:none ;
                width:350px !important;
                border:1px solid #ccc ;
                border-radius:5px;
                height:auto;
            }
            .rs_coupon_compatible_wrapper select{
                box-shadow: 0 -5px 10px 2px rgba(0, 0, 0, 0.03) inset;
                border:none ;
                width:350px !important;
                border:1px solid #ccc ;
                border-radius:5px;
                height:30px;
            }
            .rs_coupon_compatible_wrapper input[type="number"]{
                box-shadow: 0 -5px 10px 2px rgba(0, 0, 0, 0.03) inset;
                border:none ;
                width:350px !important;
                border:1px solid #ccc ;
                border-radius:5px;
                height:30px !important;
            } 
            .rs_coupon_compatible_wrapper label{
                font-weight:500 !important;
            }
            .rs_coupon_compatible_wrapper table th{
                font-weight:500 !important;
            }
            .rs_coupon_compatible_wrapper table{
                width:98%;
                margin-left:15px !important;
                font-family: 'Roboto', sans-serif !important;
            }
            .rs_coupon_compatible_wrapper table label{
                font-family: 'Roboto', sans-serif !important;
            }
            .rs_coupon_compatible_wrapper table td{
                color:#8a929e !important;
            }

            /*        coupons wrapper end*/

            /***********coupons wrapper show/hide************/

            /*       Administrator Wrapper*/

            .rs_adminstrator_wrapper{
                width:95%;
                margin:20px auto;
                border: 1px solid #dfdfdf;
                border-radius:5px !important;
                background:#f9f9f9 !important;
            }
            .rs_adminstrator_wrapper h2{
                background:#ddd url(<?php echo SRP_PLUGIN_DIR_URL . "assets/images/arrow-216-12.png" ?>) right 15px center no-repeat !important;
                border:0px !important;
                font-size:16px !important;
                color:#000 !important;
                font-family: 'Roboto', sans-serif !important;
                margin: 0;
                letter-spacing: 0.5px;
                padding: 12px 0 12px 15px;
                cursor:pointer;
            }
            .rs_adminstrator_wrapper p{
                font-family: 'Roboto', sans-serif !important;
                margin-left:10px;
                font-size:14px !important;
                color:#8a929e !important;
            }
            .rs_adminstrator_wrapper p a{
                font-style:italic;
            }
            .rs_adminstrator_wrapper h3{
                font-family: 'Roboto', sans-serif !important;
                padding-left:10px !important;
            }
            .rs_adminstrator_wrapper p b{
                color:#666 !important;
                font-size:14px !important;
            }
            .rs_adminstrator_wrapper .search-box{
                margin:10px !important;
            }
            .rs_adminstrator_wrapper .displaying-num{
                padding-right:10px !important;
            }

            .rs_adminstrator_wrapper .tablenav-pages{
                padding-right:10px !important;
                padding-bottom:10px !important;
            }
            .rs_adminstrator_wrapper input[type="text"]{
                box-shadow: 0 -5px 10px 2px rgba(0, 0, 0, 0.03) inset;
                border:none ;
                width:350px !important;
                border:1px solid #ccc ;
                border-radius:5px;
                height:30px !important;
            }        
            .rs_adminstrator_wrapper textarea{
                box-shadow: 0 -5px 10px 2px rgba(0, 0, 0, 0.03) inset;
                border:none ;
                width:350px !important;
                border:1px solid #ccc ;
                border-radius:5px;
                height:auto;
            }
            .rs_adminstrator_wrapper select{
                box-shadow: 0 -5px 10px 2px rgba(0, 0, 0, 0.03) inset;
                border:none ;
                width:350px !important;
                border:1px solid #ccc ;
                border-radius:5px;
                height:30px;
            }
            .rs_adminstrator_wrapper input[type="number"]{
                box-shadow: 0 -5px 10px 2px rgba(0, 0, 0, 0.03) inset;
                border:none ;
                width:350px !important;
                border:1px solid #ccc ;
                border-radius:5px;
                height:30px !important;
            } 
            .rs_adminstrator_wrapper label{
                font-weight:500 !important;
            }
            .rs_adminstrator_wrapper table th{
                font-weight:500 !important;
            }
            .rs_adminstrator_wrapper table{
                width:98%;
                margin-left:15px !important;
                font-family: 'Roboto', sans-serif !important;
            }
            .rs_adminstrator_wrapper table label{
                font-family: 'Roboto', sans-serif !important;
            }
            .rs_adminstrator_wrapper table td{
                color:#8a929e !important;
            }

            /*        Administrator wrapper end*/

            /***********Administrator wrapper show/hide************/
            /***********coupons wrapper show/hide end ************/

            /**********all section hide*************/
            /*            .rs_section_wrapper p{
                            display:none;
                        }
                        .rs_section_wrapper table{
                            display:none;
                        }
                        .rs_section_wrapper .tablenav, .top{
                            display:none;
                        }
            
                        .rs_section_wrapper .tablenav, .bottom{
                            display:none;
                        }
            
                        .rs_section_wrapper .rs_pagination{
                            display:none;
                        }*/

            /******* settings design wrapper***********/
            .rs_section_wrapper{
                width:95%;
                margin:20px auto;
                border: 1px solid #dfdfdf;
                border-radius:5px !important;
                background:#f9f9f9 !important;
            }

            .rs_section_wrapper p{
                font-family: 'Roboto', sans-serif !important;
                font-style:italic;
                margin-left:10px;
                font-size:14px !important;
                color:#8a929e !important;
            }
            .rs_section_wrapper label{
                font-weight:500 !important;
            }
            .rs_section_wrapper table th{
                font-weight:500 !important;
            }
            .rs_section_wrapper h2{
                background:#ddd url(<?php echo SRP_PLUGIN_DIR_URL . "assets/images/arrow-216-12.png" ?>) right 15px center no-repeat !important;
                border:0px !important;
                font-size:16px !important;
                color:#000 !important;
                font-family: 'Roboto', sans-serif !important;
                margin: 0;
                letter-spacing: 0.5px;
                padding: 12px 0 12px 15px;
                cursor: pointer;
            }

            .rs_section_wrapper table{
                width:98%;
                margin-left:15px !important;
                font-family: 'Roboto', sans-serif !important;
            }
            .rs_section_wrapper table label{
                font-family: 'Roboto', sans-serif !important;
            }
            .rs_section_wrapper table td{
                color:#8a929e !important;
            }
            .rs_section_wrapper input[type="text"]{
                box-shadow: 0 -5px 10px 2px rgba(0, 0, 0, 0.03) inset;
                border:none ;
                width:350px !important;
                border:1px solid #ccc ;
                border-radius:5px;
                height:30px !important;
            }
            .rs_section_wrapper input[type="number"]{
                box-shadow: 0 -5px 10px 2px rgba(0, 0, 0, 0.03) inset;
                border:none ;
                width:350px !important;
                border:1px solid #ccc ;
                border-radius:5px;
                height:30px !important;
            }
            .rs_section_wrapper textarea{
                box-shadow: 0 -5px 10px 2px rgba(0, 0, 0, 0.03) inset;
                border:none ;
                width:350px !important;
                border:1px solid #ccc ;
                border-radius:5px;
                height:auto;
            }
            .rs_section_wrapper select{
                box-shadow: 0 -5px 10px 2px rgba(0, 0, 0, 0.03) inset;
                border:none ;
                width:350px !important;
                border:1px solid #ccc ;
                border-radius:5px;
                height:30px;
            }
            .rs_section_wrapper .displaying-num{
                padding-right:10px !important;
            }
            .rs_section_wrapper .widefat{
                width:98% !important;
                margin:7px !important;
            }
            .rs_section_wrapper .search-box{
                margin:10px !important;
            }
            .widefat input[type="text"]{
                box-shadow: none;
                width:auto !important;
                /*            border:1px solid #ccc !important;*/
                /*border-radius:0px;*/
                height:30px !important;
            }
            .widefat input[type="number"]{
                box-shadow: none;
                width:auto !important;
                /*border:1px solid #ccc !important;*/
                /*border-radius:0px;*/
                height:30px !important;
            }
            .widefat select{
                box-shadow: none;
                width:150px !important;
                /*border:1px solid #ccc !important;*/
                /*border-radius:0px;*/
                height:30px !important;
            }
            .rs_save{
                margin-left:25px;
            }
            .rs_section_wrapper h3{
                padding-left:10px !important;
            }
            .rs_section_wrapper p b{
                color:#666 !important;
                font-size:14px !important;
            }
            /***************** settings button design **********************/
            .rs_refresh_button, .rs_upload_button{

                padding: 7px !important;
                border-radius:5px !important;
                color:#000;
                background:#dedee0;
                font-size:14px;
                font-weight: 500 !important;
                border: none !important;
                cursor: pointer !important;
            }
            .rs_refresh_button:hover, .rs_upload_button:hover{
                box-shadow: 0 0 3px #000;
            }
            .rs_imgupload_button{

                padding: 7px !important;
                border-radius:5px !important;
                color:#000;
                background:#dedee0;
                font-size:14px;
                font-weight: 500 !important;
                border: none !important;
                cursor: pointer !important;
            }
            .rs_imgupload_button:hover{
                box-shadow: 0 0 3px #000;
            }
            .rs_export_button{
                padding: 7px 12px !important;
                border-radius:5px !important;
                color:#000;
                background:#dedee0;
                font-size:14px;
                font-weight: 500 !important;
                border: none !important;
                cursor: pointer !important;   
            }
            .rs_export_button:hover{
                box-shadow: 0 0 3px #000;
            }
            .rs_oldpoints_button{
                padding: 7px 12px !important;
                border-radius:5px !important;
                color:#000;
                background:#dedee0;
                font-size:14px;
                font-weight: 500 !important;
                border: none !important;
                cursor: pointer !important;   
            }
            .rs_oldpoints_button:hover{
                box-shadow: 0 0 3px #000;
            }
            .rs_save_btn{
                background:#f55b11 !important;
                color:#fff !important;
                border:none !important;
                font-size:14px !important;
                text-shadow:none !important;
                box-shadow:none !important;
                border-radius:5px !important;
                padding:5px 10px !important;
                height:auto !important;
                font-weight:bold !important;
                font-family: 'Roboto', sans-serif !important;
                margin-left:30px !important;
            }
            .rs_save_btn:hover{
                box-shadow:0 0 3px #000 !important;
            }
            .rs_reset{

                background:#fff !important;
                border:1px solid #f55b11 !important;
                color:#000 !important;
                height:37px !important;
                padding:0px 20px !important;
                font-weight:bold !important;
                box-shadow:none !important;
                font-family: 'Roboto', sans-serif !important;
                /*            background:#fff !important;
                            color:#000 !important;
                            border:1px solid #ccc !important;
                            font-size:14px !important;
                            text-shadow:none !important;
                            box-shadow:none !important;
                            border-radius:5px !important;
                            padding:3px 20px !important;
                            height:30px !important;
                            font-weight:bold !important;
                            font-family: 'Roboto', sans-serif !important;    */
            }
            .rs_reset:hover{
                box-shadow:0 0 3px #000 !important;
                background:#f55b11 !important;
                color:#fff !important;
                border:none !important;
            }
            .rs_email_button{
                padding: 5px 12px !important;
                border-radius:5px !important;
                color:#fff !important;
                height:auto !important;
                line-height: 1.2 !important;
                background:#040633 !important;
                font-size:14px;
                font-weight: 500 !important;
                border: none !important;
                cursor: pointer !important;
                margin-left:10px !important;
            }
            .rs_email_button:hover{
                box-shadow:0 0 3px #000 !important;
            }
            .rs_voucher_codes_btn{
                margin-left:10px !important
            }
            .rs_voucher_codes_btn:hover{
                box-shadow:0 0 3px #000 !important;
            }
            /*****************grid method css**********************/
            .rs_Grid_wrapper{
                float:left;
                margin-top:0px;
                width:100%; 
                background:#fff;
            }
            .rs_Grid_wrapper_inner{
                width:98%;
                margin:0 auto;
            }
            .rs_module_title{
                color: #555;
                font-size: 20px !important;
                text-transform: uppercase;
                font-weight:600 !important;
                padding:20px 20px 10px 20px !important;
                letter-spacing: 1px !important;
                font-family: 'Roboto', sans-serif;
            }
            .rs_module{
                font-weight:400 !important;
                font-family: 'Roboto', sans-serif;
            }
            .rs_grid{
                box-sizing: border-box;
                color: #fff;
                float: left;
                height: 200px;
                max-width: 465px;
                min-width: 340px;
                margin:10px;
                width: 20%;
            }
            .inactive_rs_box {
                background-color:#e7e7e7;
                display: block;
                border-radius: 5px;
                height: 100%;
                overflow: hidden;
                position: relative;
                width: 100%;
                border: 1px solid #bbb;
            }
            .active_rs_box{
                background:#f55b11;
                display: block;
                border-radius: 5px;
                height: 100%;
                overflow: hidden;
                position: relative;
                width: 100%;
                border: 1px solid #bbb;
                box-shadow:0px 0px 8px 0px #ccc;
            }
            .active_rs_box p{
                color:#fff !important;
                font-size:14px !important;
                font-weight:500 !important;
                padding:0px 10px 10px 10px !important;
                margin-top:5px !important;
                line-height: 1.8 !important;
                opacity: 0 !important;
                text-align: center !important;
                letter-spacing: 0.5px !important;
                transition:opacity 0.5s linear !important;
                font-family: 'Roboto', sans-serif !important;
            }
            .active_rs_box:hover p{
                opacity: 1 !important;
            }

            .rs_inactive_hyperlink {
                float:left !important;
                width:100% !important;
                border-bottom:1px solid #bbb !important;
                min-height:150px !important;
                text-align:center !important;
                font-family: 'Roboto', sans-serif !important;
            }
            .rs_active_hyperlink {
                float:left !important;
                width:100% !important;
                border-bottom: 1px solid #ddd !important;
                min-height:150px !important;
                text-align:center !important;
            }
            .rs_inactive_hyperlink h1{
                font-size:18px !important;
                color:#000 !important;
                text-align:left !important;
                padding:12px !important;
                font-weight:bold !important;
                font-family: 'Roboto', sans-serif !important;
            }
            .rs_active_hyperlink h1{
                font-size:18px !important;;
                color:#000 !important;
                text-align:left !important;
                padding:12px !important;
                font-family: 'Roboto', sans-serif !important;
                font-weight:bold !important;
            }
            .bottom_sec{
                margin-top:10px;
                float:left;
                min-width: 340px;
            }
            .bottom_sec label{
                margin-left:10px !important;
            }
            .bottom_sec a{
                color:#fff !important;
                float:right !important;
                text-decoration:none !important;
                font-size:14px !important;
                font-weight: 600 !important;
                margin-right:10px !important;
                background:#2d2d2d !important;
                border:none !important;
                border-radius:15px !important;
                color:#fff !important;
                padding:6px 13px !important;
                font-family: 'Roboto', sans-serif !important;
            }    
            .bottom_sec a:hover{
                color:#fff !important;
                box-shadow:0 0 3px #2d2d2d;
            }
            .bottom_sec label{
                text-align:left;
            }
            /********** Module check box button design************/
            .rs_switch_round{
                width:70px !important;
                height:30px !important;
                position:relative !important;
                display:inline-block !important;
                -moz-box-shadow:    inset 0 0 6px #eee !important;
                -webkit-box-shadow: inset 0 0 6px #eee !important;
                box-shadow: 0 0 2px #777 inset !important;
                background: #fff !important;
                -webkit-border-radius: 50px !important;
                -moz-border-radius: 50px !important;
                border-radius: 50px !important;
            }
            .rs_switch_round:before{
                content:"ON" !important;
                font-size:10px !important;
                font-weight:bold !important;
                line-height: 1.8 !important;
                top:4px !important;
                left:4px !important;
                position:absolute !important;
                color:#000 !important;
                padding:3px !important;
                font-family: 'Roboto', sans-serif !important;
            }
            .rs_switch_round:after{
                content:"OFF" !important;
                font-size:10px !important;
                top:4px !important;
                line-height: 1.8 !important;
                right:4px !important;
                position:absolute !important;
                font-weight:bold !important;
                color:#000 !important;
                padding:3px !important;
                z-index:1 !important;
                font-family: 'Roboto', sans-serif !important;
            }
            .rs_slider_round{
                position:absolute !important;
                z-index:111 !important;
                height:22px !important;
                width:33px !important;
                left:5px !important;
                top:4px !important;
                background:#999 !important;
                -webkit-box-shadow: 0px 2px 5px 0px rgba(0,0,0,0.3) !important;
                -moz-box-shadow: 0px 2px 5px 0px rgba(0,0,0,0.3) !important;
                box-shadow: 0px 2px 5px 0px rgba(0,0,0,0.3) !important;
                -webkit-transition: all .4s ease !important;
                -moz-transition: all .4s ease !important;
                -o-transition: all .4s ease !important;
                -ms-transition: all .4s ease !important;
                transition: all .4s ease !important;
                -webkit-border-radius: 50px !important;
                -moz-border-radius: 50px !important;
                border-radius: 50px !important;
            }
            .rs_switch_round input{
                display:none !important;
            }

            .rs_switch_round input:checked + .rs_slider_round {
                -webkit-transform: translateX(27px) !important;
                -ms-transform: translateX(27px) !important;
                transform: translateX(27px) !important;
                background-color: #f55b11 !important;
            }
            @media only screen and (max-width:1200px){
                .rs_tab_design a{
                    font-size:11px !important;
                }
            }
            @media only screen and (max-width:1070px){
                .rs_tab_design a{  
                    height: 80px !important;  
                }
            }
            @media only screen and (max-width:782px){
                .rs_section_wrapper input[type="text"]{
                    width:100% !important;
                    height:35px !important;
                }
                .rs_section_wrapper input[type="number"]{
                    width:100% !important;
                    height:35px !important;
                }
                .rs_section_wrapper textarea{
                    width:100% !important;
                }
                .rs_section_wrapper select{
                    width:100% !important;
                    height:35px !important;
                }
                .rs_modulecheck_wrapper input[type="text"]{
                    width:100% !important;
                    height:35px !important;
                }
                .rs_modulecheck_wrapper textarea{
                    width:100% !important;
                }
                .rs_modulecheck_wrapper select{
                    width:100% !important;
                    height:35px !important;
                }
                .rs_modulecheck_wrapper input[type="number"]{
                    width:100% !important;
                    height:35px !important;
                }
                /*            membership wrapper mobile responsive start*/
                .rs_membership_compatible_wrapper input[type="text"]{
                    width:100% !important;
                    height:35px !important;
                }
                .rs_membership_compatible_wrapper textarea{
                    width:100% !important;
                }
                .rs_membership_compatible_wrapper select{
                    width:100% !important;
                    height:35px !important;
                }
                .rs_membership_compatible_wrapper input[type="number"]{
                    width:100% !important;
                    height:35px !important;
                }
                /* Membership wrapper mobile responsive end*/

                /* Waiting List wrapper mobile responsive start*/
                .rs_bsn_compatible_wrapper input[type="text"]{
                    width:100% !important;
                    height:35px !important;
                }
                .rs_bsn_compatible_wrapper textarea{
                    width:100% !important;
                }
                .rs_bsn_compatible_wrapper select{
                    width:100% !important;
                    height:35px !important;
                }
                .rs_bsn_compatible_wrapper input[type="number"]{
                    width:100% !important;
                    height:35px !important;
                }
                /* Waiting List wrapper mobile responsive end*/

                /* Affilaite Pro wrapper mobile responsive start*/
                .rs_affs_compatible_wrapper input[type="text"]{
                    width:100% !important;
                    height:35px !important;
                }
                .rs_affs_compatible_wrapper textarea{
                    width:100% !important;
                }
                .rs_affs_compatible_wrapper select{
                    width:100% !important;
                    height:35px !important;
                }
                .rs_affs_compatible_wrapper input[type="number"]{
                    width:100% !important;
                    height:35px !important;
                }
                /*Affilaite Pro wrapper mobile responsive end*/

                /*            subscriptions wrapper mobile responsive start*/
                .rs_subscription_compatible_wrapper input[type="text"]{
                    width:100% !important;
                    height:35px !important;
                }
                .rs_subscription_compatible_wrapper textarea{
                    width:100% !important;
                }
                .rs_subscription_compatible_wrapper select{
                    width:100% !important;
                    height:35px !important;
                }
                .rs_subscription_compatible_wrapper input[type="number"]{
                    width:100% !important;
                    height:35px !important;
                }
                /*            subscriptions wrapper mobile responsive end*/


                /*            payment plan wrapper mobile responsive start*/
                .rs_payment_plan_compatible_wrapper input[type="text"]{
                    width:100% !important;
                    height:35px !important;
                }
                .rs_payment_plan_compatible_wrapper textarea{
                    width:100% !important;
                }
                .rs_payment_plan_compatible_wrapper select{
                    width:100% !important;
                    height:35px !important;
                }
                .rs_payment_plan_compatible_wrapper input[type="number"]{
                    width:100% !important;
                    height:35px !important;
                }
                /*            paymentplan wrapper mobile responsive end*/
                /*            coupon wrapper mobile responsive start*/
                .rs_coupon_compatible_wrapper input[type="text"]{
                    width:100% !important;
                    height:35px !important;
                }
                .rs_coupon_compatible_wrapper textarea{
                    width:100% !important;
                }
                .rs_coupon_compatible_wrapper select{
                    width:100% !important;
                    height:35px !important;
                }
                .rs_coupon_compatible_wrapper input[type="number"]{
                    width:100% !important;
                    height:35px !important;
                }

                /*            Administrator wrapper mobile responsive start*/
                .rs_adminstrator_wrapper input[type="text"]{
                    width:100% !important;
                    height:35px !important;
                }
                .rs_adminstrator_wrapper textarea{
                    width:100% !important;
                }
                .rs_adminstrator_wrapper select{
                    width:100% !important;
                    height:35px !important;
                }
                .rs_adminstrator_wrapper input[type="number"]{
                    width:100% !important;
                    height:35px !important;
                }

                .rs_sub_tab_design{
                    text-align:left !important;
                }
                .rs_tab_design ul .rs_sub_tab_li a{
                    text-align:left !important;
                    font-size:13px !important;
                    padding-left:10px !important;
                }
                .rs_modulecheck_wrapper .widefat td{
                    width:87% !important;
                    float:left;
                }
            }
            @media only screen and (max-width:767px){
                .rs_main{
                    background:#fff !important;
                    width:98% !important;
                }
                .rs_tab_design{
                    margin-top:0px !important;
                    width:100% !important;
                }
                .rs_tab_design{
                    margin-top:0px !important;
                }
                .rs_tab_design a:before{
                    content:'';
                    padding-left:15px !important;
                }

                .rs_section_wrapper h2{
                    font-size:16px !important;
                }
                .rs_modulecheck_wrapper h2{
                    font-size:16px !important;
                }
                .rs_tab_design{
                    margin-top:0px !important;
                }
                .rs_tab_design a{
                    border-right: none !important;
                    border-top: 1px solid rgba(255, 255, 255, 0.1) !important;
                    background:none !important;
                    text-transform: capitalize;
                    font-weight: 500;
                    float:left !important;
                    color:#EEEEEE !important;
                    font-size: 14px !important;
                    letter-spacing: 0.5px;
                    margin:0px !important;
                    height: auto !important;
                    width: 100% !important;
                    padding:5px 0px 5px 0px!important;
                    border-left:none !important;
                    border-bottom:none !important;
                    text-align: left !important;
                    word-wrap: break-word !important;
                    white-space: normal !important;
                }
                .rs_tab_design a:hover{
                    border-bottom:none !important;
                    border-top:1px solid #f55b11 !important;
                }
                .rs_tab_design .nav-tab-active:hover{
                    border-bottom:none !important;
                    border-top:1px solid #f55b11 !important;
                }
                .rs_tab_design .nav-tab-active{
                    border-bottom:none !important;
                    border-top:1px solid #f55b11 !important;
                }
                .rs_main_wrapper{
                    margin-top:0px !important;
                    margin-right:0px !important;
                    margin-bottom:0px !important;
                    margin-left:0px !important;
                }
                .rs_tab_design .rs_sub_tab_design{
                    border-top:1px solid #fff !important;
                }
                .rs_sub_tab_design{
                    text-align:left !important; 
                }
                .rs_sub_tab_li a{
                    text-align:left !important; 
                    font-size:13px !important;
                }
                .rs_save_btn{
                    margin-left:15px !important;
                }
                .rs_modulecheck_wrapper table{
                    margin-left:5px !important;
                }
                .rs_modulecheck_wrapper .search-box{
                    position:relative !important;
                    padding:10px !important;
                    margin:0px !important;
                }
                .rs_tab_design ul .rs_sub_tab_li a{
                    padding-left:5px !important;
                }
                .rs_tab_design ul .rs_sub_tab_li a:before{
                    padding-left:0px !important;
                }
            }
            @media only screen and (max-width:479px){
                .rs_section_wrapper input[type="text"]{
                    width:250px !important;
                    height:35px !important;
                }
                .rs_section_wrapper input[type="number"]{
                    width:250px !important;
                    height:35px !important;
                }
                .rs_section_wrapper textarea{
                    width:250px !important;
                }
                .rs_section_wrapper select{
                    width:250px !important;
                    height:35px !important;
                }
            }
            @media only screen and (max-width:380px){
                .rs_grid{
                    box-sizing: border-box;
                    color: #fff;
                    float: left;
                    height: 170px;
                    max-width: 465px;
                    min-width: 280px;
                    margin:10px;
                    width: 20%;
                }
                .rs_active_hyperlink{
                    min-height:120px !important;
                }
                .rs_inactive_hyperlink{
                    min-height:120px !important;
                }
                .bottom_sec{
                    margin-top:10px;
                    float:left;
                    min-width: 280px;
                }
            }
        </style>
    <?php } else { ?>
        <style>
            @import url('https://fonts.googleapis.com/css?family=Roboto+Condensed');
            .rs_main_wrapper{
                margin-top:30px !important;
                margin-right:0px !important;
                margin-bottom:0px !important;
                margin-left:-20px !important;
            }
            .rs_main{
                background:#f1f1f1 !important;
            }
            .rs_tab_design{
                margin-bottom:0px !important;
                padding-top:0px !important;
                border-bottom:none !important;
                border-top:0px solid #000 !important;
                box-shadow: 0 2px 2px #111 !important;
                background:linear-gradient(to bottom, rgba(240,240,240,1) 0%,rgba(255,255,255,1) 50%,rgba(240,240,240,1) 100%) !important;
            }
            .rs_tab_design a{
                border-right:1px solid #e8e7e5 !important;
                background:linear-gradient(to bottom, rgba(240,240,240,1) 0%,rgba(255,255,255,1) 50%,rgba(240,240,240,1) 100%) !important;
                font-family: 'Roboto', sans-serif !important;
                font-weight: 500 !important;
                float:left !important;
                color:#333 !important;
                font-size: 14px;
                /*                letter-spacing: 0.5px;*/
                margin:0px !important;
                height: 65px;
                width: 8.5%;
                padding:12px 5px 0px 5px!important;
                border-left:none !important;
                border-top:none !important;
                text-align: center;
                word-wrap: break-word !important;
                white-space: normal !important;
                box-shadow: none !important;
            }
            .rs_tab_design ul{
                margin:0px !important;
                width:100%;
                float:left;
                background:linear-gradient(to bottom, rgba(240,240,240,1) 0%,rgba(255,255,255,1) 50%,rgba(240,240,240,1) 100%) !important;
            }
            .rs_tab_design ul li{
                margin:0px !important;
            }
            .rs_tab_design ul li:last-child a{
                border-right:none !important;
            }
            .rs_tab_design a:hover{
                color:#f55b11 !important;
                border-top:3px solid #f55b11 !important;
                /*border-bottom:none !important;*/
                background:#fff !important;
                font-weight: 500 !important;
                color:#f55b11 !important;
            }
            .rs_tab_design .nav-tab-active:hover{
                background:#fff !important;
                border-top:3px solid #f55b11 !important;
                font-weight: 600 !important;
                color:#f55b11 !important;
            }
            .rs_tab_design .nav-tab-active {
                color:#f55b11 !important;
                background:#fff !important;
                margin-bottom:0px !important;
                border-top:3px solid #f55b11 !important;
                border-bottom:0px solid #000 !important;
                font-weight: 600 !important;
                /*border-bottom:none !important;*/
            }
            .rs_tab_design .rs_sub_tab_design{
                margin-top:-2px !important;
                border-top:0px solid #f55b11 !important;
                border-bottom:none !important;
                width:100% !important;
                background:#fff !important;
            }
            .rs_tab_design ul .rs_sub_tab_li a{
                display:block !important;
                padding:5px !important;
                color:#000 !important;
                font-size:13px !important;
                font-weight: 500 !important;
                font-family: 'Roboto', sans-serif !important;
                width:auto !important;
                height:auto !important;
                background:none !important;
                border:none !important;
                box-shadow: none !important;
            }
            .rs_tab_design ul .rs_sub_tab_li a:hover{
                color:#f55b11 !important;
            }
            .rs_tab_design ul .rs_sub_tab_li .current{
                font-weight: 600 !important;
                color:#f55b11 !important;
                padding:5px !important;
                display:block;
                font-size:13px !important;
            }
            .rs_modulecheck_wrapper{
                width:95%;
                margin:20px auto;
                border: 1px solid #dfdfdf;
                background:#fff !important;
            }
            .rs_modulecheck_wrapper h2{
                background:linear-gradient(to bottom, rgba(240,240,240,1) 0%,rgba(255,255,255,1) 50%,rgba(240,240,240,1) 100%) !important;
                font-size:16px !important;
                color:#333 !important;
                font-family: 'Roboto', sans-serif !important;
                margin: 0;
                letter-spacing: 0.5px;
                padding: 12px 0 12px 15px;

            }
            .rs_modulecheck_wrapper p{
                font-family: 'Roboto', sans-serif !important;
                margin-left:10px;
                font-size:14px !important;
                color:#8a929e !important;
            }
            .rs_modulecheck_wrapper p a{
                font-style:italic;
            }
            .rs_modulecheck_wrapper h3{
                font-family: 'Roboto', sans-serif !important;
                padding-left:10px !important;
            }
            .rs_modulecheck_wrapper p b{
                color:#666 !important;
                font-size:14px !important;
            }
            .rs_modulecheck_wrapper .displaying-num{
                padding-right:10px !important;
            }
            .rs_modulecheck_wrapper .search-box{
                margin:10px !important;
            }
            .rs_modulecheck_wrapper .tablenav-pages{
                padding-right:10px !important;
                padding-bottom:10px !important;
            }
            .rs_modulecheck_wrapper input[type="text"]{
                box-shadow: 0 -5px 10px 2px rgba(0, 0, 0, 0.03) inset;
                border:none ;
                width:350px !important;
                border:1px solid #ccc ;
                border-radius:5px;
                height:30px !important;
            }        
            .rs_modulecheck_wrapper textarea{
                box-shadow: 0 -5px 10px 2px rgba(0, 0, 0, 0.03) inset;
                border:none ;
                width:350px !important;
                border:1px solid #ccc ;
                border-radius:5px;
                height:auto;
            }
            .rs_modulecheck_wrapper input[type="number"]{
                box-shadow: 0 -5px 10px 2px rgba(0, 0, 0, 0.03) inset;
                border:none ;
                width:350px !important;
                border:1px solid #ccc ;
                border-radius:5px;
                height:30px !important;
            }
            .rs_modulecheck_wrapper select{
                box-shadow: 0 -5px 10px 2px rgba(0, 0, 0, 0.03) inset;
                border:none ;
                width:350px !important;
                border:1px solid #ccc ;
                border-radius:5px;
                height:30px;
            }
            .rs_modulecheck_wrapper label{
                font-weight:500 !important;
            }
            .rs_modulecheck_wrapper table th{
                font-weight:500 !important;
            }
            .rs_modulecheck_wrapper table{
                width:98%;
                margin-left:15px !important;
                font-family: 'Roboto', sans-serif !important;
            }
            .rs_modulecheck_wrapper table label{
                font-family: 'Roboto', sans-serif !important;
            }
            .rs_modulecheck_wrapper table td{
                color:#8a929e !important;
            }
            /* Membership wrapper*/

            .rs_membership_compatible_wrapper{
                width:95%;
                margin:20px auto;
                border: 1px solid #dfdfdf;
                border-radius:5px !important;
                background:#fff !important;
            }
            .rs_membership_compatible_wrapper h2{
                background: url(<?php echo SRP_PLUGIN_DIR_URL . "assets/images/arrow-216-12.png" ?>) right 15px center no-repeat, linear-gradient(to bottom, rgba(240,240,240,1) 0%,rgba(255,255,255,1) 50%,rgba(240,240,240,1) 100%) !important;
                border:0px !important;
                font-size:16px !important;
                color:#333 !important;
                font-family: 'Roboto', sans-serif !important;
                margin: 0;
                letter-spacing: 0.5px;
                padding: 12px 0 12px 15px;
                cursor:pointer;
            }
            .rs_membership_compatible_wrapper p{
                font-family: 'Roboto', sans-serif !important;
                margin-left:10px;
                font-size:14px !important;
                color:#8a929e !important;
            }
            .rs_membership_compatible_wrapper p a{
                font-style:italic;
            }
            .rs_membership_compatible_wrapper h3{
                font-family: 'Roboto', sans-serif !important;
                padding-left:10px !important;
            }
            .rs_membership_compatible_wrapper p b{
                color:#666 !important;
                font-size:14px !important;
            }
            .rs_membership_compatible_wrapper .search-box{
                margin:10px !important;
            }
            .rs_membership_compatible_wrapper .displaying-num{
                padding-right:10px !important;
            }

            .rs_membership_compatible_wrapper .tablenav-pages{
                padding-right:10px !important;
                padding-bottom:10px !important;
            }
            .rs_membership_compatible_wrapper input[type="text"]{
                box-shadow: 0 -5px 10px 2px rgba(0, 0, 0, 0.03) inset;
                border:none ;
                width:350px !important;
                border:1px solid #ccc ;
                border-radius:5px;
                height:30px !important;
            }        
            .rs_membership_compatible_wrapper textarea{
                box-shadow: 0 -5px 10px 2px rgba(0, 0, 0, 0.03) inset;
                border:none ;
                width:350px !important;
                border:1px solid #ccc ;
                border-radius:5px;
                height:auto;
            }
            .rs_membership_compatible_wrapper select{
                box-shadow: 0 -5px 10px 2px rgba(0, 0, 0, 0.03) inset;
                border:none ;
                width:350px !important;
                border:1px solid #ccc ;
                border-radius:5px;
                height:30px;
            }
            .rs_membership_compatible_wrapper input[type="number"]{
                box-shadow: 0 -5px 10px 2px rgba(0, 0, 0, 0.03) inset;
                border:none ;
                width:350px !important;
                border:1px solid #ccc ;
                border-radius:5px;
                height:30px !important;
            } 
            .rs_membership_compatible_wrapper label{
                font-weight:500 !important;
            }
            .rs_membership_compatible_wrapper table th{
                font-weight:500 !important;
            }
            .rs_membership_compatible_wrapper table{
                width:98%;
                margin-left:15px !important;
                font-family: 'Roboto', sans-serif !important;
            }
            .rs_membership_compatible_wrapper table label{
                font-family: 'Roboto', sans-serif !important;
            }
            .rs_membership_compatible_wrapper table td{
                color:#8a929e !important;
            }            

            /* Waiting List wrapper*/

            .rs_bsn_compatible_wrapper{
                width:95%;
                margin:20px auto;
                border: 1px solid #dfdfdf;
                border-radius:5px !important;
                background:#fff !important;
            }
            .rs_bsn_compatible_wrapper h2{
                background: url(<?php echo SRP_PLUGIN_DIR_URL . "assets/images/arrow-216-12.png" ?>) right 15px center no-repeat, linear-gradient(to bottom, rgba(240,240,240,1) 0%,rgba(255,255,255,1) 50%,rgba(240,240,240,1) 100%) !important;
                border:0px !important;
                font-size:16px !important;
                color:#333 !important;
                font-family: 'Roboto', sans-serif !important;
                margin: 0;
                letter-spacing: 0.5px;
                padding: 12px 0 12px 15px;
                cursor:pointer;
            }
            .rs_bsn_compatible_wrapper p{
                font-family: 'Roboto', sans-serif !important;
                margin-left:10px;
                font-size:14px !important;
                color:#8a929e !important;
            }
            .rs_bsn_compatible_wrapper p a{
                font-style:italic;
            }
            .rs_bsn_compatible_wrapper h3{
                font-family: 'Roboto', sans-serif !important;
                padding-left:10px !important;
            }
            .rs_bsn_compatible_wrapper p b{
                color:#666 !important;
                font-size:14px !important;
            }
            .rs_bsn_compatible_wrapper .search-box{
                margin:10px !important;
            }
            .rs_bsn_compatible_wrapper .displaying-num{
                padding-right:10px !important;
            }

            .rs_bsn_compatible_wrapper .tablenav-pages{
                padding-right:10px !important;
                padding-bottom:10px !important;
            }
            .rs_bsn_compatible_wrapper input[type="text"]{
                box-shadow: 0 -5px 10px 2px rgba(0, 0, 0, 0.03) inset;
                border:none ;
                width:350px !important;
                border:1px solid #ccc ;
                border-radius:5px;
                height:30px !important;
            }        
            .rs_bsn_compatible_wrapper textarea{
                box-shadow: 0 -5px 10px 2px rgba(0, 0, 0, 0.03) inset;
                border:none ;
                width:350px !important;
                border:1px solid #ccc ;
                border-radius:5px;
                height:auto;
            }
            .rs_bsn_compatible_wrapper select{
                box-shadow: 0 -5px 10px 2px rgba(0, 0, 0, 0.03) inset;
                border:none ;
                width:350px !important;
                border:1px solid #ccc ;
                border-radius:5px;
                height:30px;
            }
            .rs_bsn_compatible_wrapper input[type="number"]{
                box-shadow: 0 -5px 10px 2px rgba(0, 0, 0, 0.03) inset;
                border:none ;
                width:350px !important;
                border:1px solid #ccc ;
                border-radius:5px;
                height:30px !important;
            } 
            .rs_bsn_compatible_wrapper label{
                font-weight:500 !important;
            }
            .rs_bsn_compatible_wrapper table th{
                font-weight:500 !important;
            }
            .rs_bsn_compatible_wrapper table{
                width:98%;
                margin-left:15px !important;
                font-family: 'Roboto', sans-serif !important;
            }
            .rs_bsn_compatible_wrapper table label{
                font-family: 'Roboto', sans-serif !important;
            }
            .rs_bsn_compatible_wrapper table td{
                color:#8a929e !important;
            }

            /* Waiting List wrapper end*/

            /* Affiliate Pro wrapper*/

            .rs_affs_compatible_wrapper{
                width:95%;
                margin:20px auto;
                border: 1px solid #dfdfdf;
                border-radius:5px !important;
                background:#fff !important;
            }
            .rs_affs_compatible_wrapper h2{
                background: url(<?php echo SRP_PLUGIN_DIR_URL . "admin/images/arrow-216-12.png" ?>) right 15px center no-repeat, linear-gradient(to bottom, rgba(240,240,240,1) 0%,rgba(255,255,255,1) 50%,rgba(240,240,240,1) 100%) !important;
                border:0px !important;
                font-size:16px !important;
                color:#333 !important;
                font-family: 'Roboto', sans-serif !important;
                margin: 0;
                letter-spacing: 0.5px;
                padding: 12px 0 12px 15px;
                cursor:pointer;
            }
            .rs_affs_compatible_wrapper p{
                font-family: 'Roboto', sans-serif !important;
                margin-left:10px;
                font-size:14px !important;
                color:#8a929e !important;
            }
            .rs_affs_compatible_wrapper p a{
                font-style:italic;
            }
            .rs_affs_compatible_wrapper h3{
                font-family: 'Roboto', sans-serif !important;
                padding-left:10px !important;
            }
            .rs_affs_compatible_wrapper p b{
                color:#666 !important;
                font-size:14px !important;
            }
            .rs_affs_compatible_wrapper .search-box{
                margin:10px !important;
            }
            .rs_affs_compatible_wrapper .displaying-num{
                padding-right:10px !important;
            }

            .rs_affs_compatible_wrapper .tablenav-pages{
                padding-right:10px !important;
                padding-bottom:10px !important;
            }
            .rs_affs_compatible_wrapper input[type="text"]{
                box-shadow: 0 -5px 10px 2px rgba(0, 0, 0, 0.03) inset;
                border:none ;
                width:350px !important;
                border:1px solid #ccc ;
                border-radius:5px;
                height:30px !important;
            }        
            .rs_affs_compatible_wrapper textarea{
                box-shadow: 0 -5px 10px 2px rgba(0, 0, 0, 0.03) inset;
                border:none ;
                width:350px !important;
                border:1px solid #ccc ;
                border-radius:5px;
                height:auto;
            }
            .rs_affs_compatible_wrapper select{
                box-shadow: 0 -5px 10px 2px rgba(0, 0, 0, 0.03) inset;
                border:none ;
                width:350px !important;
                border:1px solid #ccc ;
                border-radius:5px;
                height:30px;
            }
            .rs_affs_compatible_wrapper input[type="number"]{
                box-shadow: 0 -5px 10px 2px rgba(0, 0, 0, 0.03) inset;
                border:none ;
                width:350px !important;
                border:1px solid #ccc ;
                border-radius:5px;
                height:30px !important;
            } 
            .rs_affs_compatible_wrapper label{
                font-weight:500 !important;
            }
            .rs_affs_compatible_wrapper table th{
                font-weight:500 !important;
            }
            .rs_affs_compatible_wrapper table{
                width:98%;
                margin-left:15px !important;
                font-family: 'Roboto', sans-serif !important;
            }
            .rs_affs_compatible_wrapper table label{
                font-family: 'Roboto', sans-serif !important;
            }
            .rs_affs_compatible_wrapper table td{
                color:#8a929e !important;
            }

            /* Affiliate Pro wrapper end*/


            /* Social Login wrapper*/

            .rs_fpwcrs_compatible_wrapper{
                width:95%;
                margin:20px auto;
                border: 1px solid #dfdfdf;
                border-radius:5px !important;
                background:#fff !important;
            }
            .rs_fpwcrs_compatible_wrapper h2{
                background: url(<?php echo SRP_PLUGIN_DIR_URL . "assets/images/arrow-216-12.png" ?>) right 15px center no-repeat, linear-gradient(to bottom, rgba(240,240,240,1) 0%,rgba(255,255,255,1) 50%,rgba(240,240,240,1) 100%) !important;
                border:0px !important;
                font-size:16px !important;
                color:#333 !important;
                font-family: 'Roboto', sans-serif !important;
                margin: 0;
                letter-spacing: 0.5px;
                padding: 12px 0 12px 15px;
                cursor:pointer;
            }
            .rs_fpwcrs_compatible_wrapper p{
                font-family: 'Roboto', sans-serif !important;
                margin-left:10px;
                font-size:14px !important;
                color:#8a929e !important;
            }
            .rs_fpwcrs_compatible_wrapper p a{
                font-style:italic;
            }
            .rs_fpwcrs_compatible_wrapper h3{
                font-family: 'Roboto', sans-serif !important;
                padding-left:10px !important;
            }
            .rs_fpwcrs_compatible_wrapper p b{
                color:#666 !important;
                font-size:14px !important;
            }
            .rs_fpwcrs_compatible_wrapper .search-box{
                margin:10px !important;
            }
            .rs_fpwcrs_compatible_wrapper .displaying-num{
                padding-right:10px !important;
            }

            .rs_fpwcrs_compatible_wrapper .tablenav-pages{
                padding-right:10px !important;
                padding-bottom:10px !important;
            }
            .rs_fpwcrs_compatible_wrapper input[type="text"]{
                box-shadow: 0 -5px 10px 2px rgba(0, 0, 0, 0.03) inset;
                border:none ;
                width:350px !important;
                border:1px solid #ccc ;
                border-radius:5px;
                height:30px !important;
            }        
            .rs_fpwcrs_compatible_wrapper textarea{
                box-shadow: 0 -5px 10px 2px rgba(0, 0, 0, 0.03) inset;
                border:none ;
                width:350px !important;
                border:1px solid #ccc ;
                border-radius:5px;
                height:auto;
            }
            .rs_fpwcrs_compatible_wrapper select{
                box-shadow: 0 -5px 10px 2px rgba(0, 0, 0, 0.03) inset;
                border:none ;
                width:350px !important;
                border:1px solid #ccc ;
                border-radius:5px;
                height:30px;
            }
            .rs_fpwcrs_compatible_wrapper input[type="number"]{
                box-shadow: 0 -5px 10px 2px rgba(0, 0, 0, 0.03) inset;
                border:none ;
                width:350px !important;
                border:1px solid #ccc ;
                border-radius:5px;
                height:30px !important;
            } 
            .rs_fpwcrs_compatible_wrapper label{
                font-weight:500 !important;
            }
            .rs_fpwcrs_compatible_wrapper table th{
                font-weight:500 !important;
            }
            .rs_fpwcrs_compatible_wrapper table{
                width:98%;
                margin-left:15px !important;
                font-family: 'Roboto', sans-serif !important;
            }
            .rs_fpwcrs_compatible_wrapper table label{
                font-family: 'Roboto', sans-serif !important;
            }
            .rs_fpwcrs_compatible_wrapper table td{
                color:#8a929e !important;
            }

            /* Social Login wrapper end*/


            /*subscriptions wrapper*/

            .rs_subscription_compatible_wrapper{
                width:95%;
                margin:20px auto;
                border: 1px solid #dfdfdf;
                border-radius:5px !important;
                background:#fff !important;
            }
            .rs_subscription_compatible_wrapper h2{
                background:url(<?php echo SRP_PLUGIN_DIR_URL . "assets/images/arrow-216-12.png" ?>) right 15px center no-repeat, linear-gradient(to bottom, rgba(240,240,240,1) 0%,rgba(255,255,255,1) 50%,rgba(240,240,240,1) 100%) !important;
                border:0px !important;
                font-size:16px !important;
                color:#333 !important;
                font-family: 'Roboto', sans-serif !important;
                margin: 0;
                letter-spacing: 0.5px;
                padding: 12px 0 12px 15px;
                cursor:pointer;
            }
            .rs_subscription_compatible_wrapper p{
                font-family: 'Roboto', sans-serif !important;
                margin-left:10px;
                font-size:14px !important;
                color:#8a929e !important;
            }
            .rs_subscription_compatible_wrapper p a{
                font-style:italic;
            }
            .rs_subscription_compatible_wrapper h3{
                font-family: 'Roboto', sans-serif !important;
                padding-left:10px !important;
            }
            .rs_subscription_compatible_wrapper p b{
                color:#666 !important;
                font-size:14px !important;
            }
            .rs_subscription_compatible_wrapper .search-box{
                margin:10px !important;
            }
            .rs_subscription_compatible_wrapper .displaying-num{
                padding-right:10px !important;
            }

            .rs_subscription_compatible_wrapper .tablenav-pages{
                padding-right:10px !important;
                padding-bottom:10px !important;
            }
            .rs_subscription_compatible_wrapper input[type="text"]{
                box-shadow: 0 -5px 10px 2px rgba(0, 0, 0, 0.03) inset;
                border:none ;
                width:350px !important;
                border:1px solid #ccc ;
                border-radius:5px;
                height:30px !important;
            }        
            .rs_subscription_compatible_wrapper textarea{
                box-shadow: 0 -5px 10px 2px rgba(0, 0, 0, 0.03) inset;
                border:none ;
                width:350px !important;
                border:1px solid #ccc ;
                border-radius:5px;
                height:auto;
            }
            .rs_subscription_compatible_wrapper select{
                box-shadow: 0 -5px 10px 2px rgba(0, 0, 0, 0.03) inset;
                border:none ;
                width:350px !important;
                border:1px solid #ccc ;
                border-radius:5px;
                height:30px;
            }
            .rs_subscription_compatible_wrapper input[type="number"]{
                box-shadow: 0 -5px 10px 2px rgba(0, 0, 0, 0.03) inset;
                border:none ;
                width:350px !important;
                border:1px solid #ccc ;
                border-radius:5px;
                height:30px !important;
            } 
            .rs_subscription_compatible_wrapper label{
                font-weight:500 !important;
            }
            .rs_subscription_compatible_wrapper table th{
                font-weight:500 !important;
            }
            .rs_subscription_compatible_wrapper table{
                width:98%;
                margin-left:15px !important;
                font-family: 'Roboto', sans-serif !important;
            }
            .rs_subscription_compatible_wrapper table label{
                font-family: 'Roboto', sans-serif !important;
            }
            .rs_subscription_compatible_wrapper table td{
                color:#8a929e !important;
            }

            /*        subscriptions wrapper end*/


            /***********subscription wrapper show/hide************/
            /*            .rs_subscription_compatible_wrapper p{
                            display:none;
                        }
                        .rs_subscription_compatible_wrapper table{
                            display:none;
                        }
                        .rs_subscription_compatible_wrapper div{
                            display:none;
                        }*/
            /***********subscription wrapper show/hide end ************/


            /*        payment wrapper*/

            .rs_payment_plan_compatible_wrapper{
                width:95%;
                margin:20px auto;
                border: 1px solid #dfdfdf;
                border-radius:5px !important;
                background:#fff !important;
            }
            .rs_payment_plan_compatible_wrapper h2{
                background:url(<?php echo SRP_PLUGIN_DIR_URL . "assets/images/arrow-216-12.png" ?>) right 15px center no-repeat, linear-gradient(to bottom, rgba(240,240,240,1) 0%,rgba(255,255,255,1) 50%,rgba(240,240,240,1) 100%) !important;
                border:0px !important;
                font-size:16px !important;
                color:#333 !important;
                font-family: 'Roboto', sans-serif !important;
                margin: 0;
                letter-spacing: 0.5px;
                padding: 12px 0 12px 15px;
                cursor:pointer;
            }
            .rs_payment_plan_compatible_wrapper p{
                font-family: 'Roboto', sans-serif !important;
                margin-left:10px;
                font-size:14px !important;
                color:#8a929e !important;
            }
            .rs_payment_plan_compatible_wrapper p a{
                font-style:italic;
            }
            .rs_payment_plan_compatible_wrapper h3{
                font-family: 'Roboto', sans-serif !important;
                padding-left:10px !important;
            }
            .rs_payment_plan_compatible_wrapper p b{
                color:#666 !important;
                font-size:14px !important;
            }
            .rs_payment_plan_compatible_wrapper .search-box{
                margin:10px !important;
            }
            .rs_payment_plan_compatible_wrapper .displaying-num{
                padding-right:10px !important;
            }

            .rs_payment_plan_compatible_wrapper .tablenav-pages{
                padding-right:10px !important;
                padding-bottom:10px !important;
            }
            .rs_payment_plan_compatible_wrapper input[type="text"]{
                box-shadow: 0 -5px 10px 2px rgba(0, 0, 0, 0.03) inset;
                border:none ;
                width:350px !important;
                border:1px solid #ccc ;
                border-radius:5px;
                height:30px !important;
            }        
            .rs_payment_plan_compatible_wrapper textarea{
                box-shadow: 0 -5px 10px 2px rgba(0, 0, 0, 0.03) inset;
                border:none ;
                width:350px !important;
                border:1px solid #ccc ;
                border-radius:5px;
                height:auto;
            }
            .rs_payment_plan_compatible_wrapper select{
                box-shadow: 0 -5px 10px 2px rgba(0, 0, 0, 0.03) inset;
                border:none ;
                width:350px !important;
                border:1px solid #ccc ;
                border-radius:5px;
                height:30px;
            }
            .rs_payment_plan_compatible_wrapper input[type="number"]{
                box-shadow: 0 -5px 10px 2px rgba(0, 0, 0, 0.03) inset;
                border:none ;
                width:350px !important;
                border:1px solid #ccc ;
                border-radius:5px;
                height:30px !important;
            } 
            .rs_payment_plan_compatible_wrapper label{
                font-weight:500 !important;
            }
            .rs_payment_plan_compatible_wrapper table th{
                font-weight:500 !important;
            }
            .rs_payment_plan_compatible_wrapper table{
                width:98%;
                margin-left:15px !important;
                font-family: 'Roboto', sans-serif !important;
            }
            .rs_payment_plan_compatible_wrapper table label{
                font-family: 'Roboto', sans-serif !important;
            }
            .rs_payment_plan_compatible_wrapper table td{
                color:#8a929e !important;
            }

            /*        paymentplan wrapper end*/


            /***********subscription wrapper show/hide************/
            /*            .rs_payment_plan_compatible_wrapper p{
                            display:none;
                        }
                        .rs_payment_plan_compatible_wrapper table{
                            display:none;
                        }
                        .rs_payment_plan_compatible_wrapper div{
                            display:none;
                        }*/
            /***********payment wrapper show/hide end ************/
            /*        coupons wrapper*/

            .rs_coupon_compatible_wrapper{
                width:95%;
                margin:20px auto;
                border: 1px solid #dfdfdf;
                border-radius:5px !important;
                background:#fff !important;
            }
            .rs_coupon_compatible_wrapper h2{
                background: url(<?php echo SRP_PLUGIN_DIR_URL . "assets/images/arrow-216-12.png" ?>) right 15px center no-repeat, linear-gradient(to bottom, rgba(240,240,240,1) 0%,rgba(255,255,255,1) 50%,rgba(240,240,240,1) 100%) !important;
                border:0px !important;
                font-size:16px !important;
                color:#333 !important;
                font-family: 'Roboto', sans-serif !important;
                margin: 0;
                letter-spacing: 0.5px;
                padding: 12px 0 12px 15px;
                cursor:pointer;
            }
            .rs_coupon_compatible_wrapper p{
                font-family: 'Roboto', sans-serif !important;
                margin-left:10px;
                font-size:14px !important;
                color:#8a929e !important;
            }
            .rs_coupon_compatible_wrapper p a{
                font-style:italic;
            }
            .rs_coupon_compatible_wrapper h3{
                font-family: 'Roboto', sans-serif !important;
                padding-left:10px !important;
            }
            .rs_coupon_compatible_wrapper p b{
                color:#666 !important;
                font-size:14px !important;
            }
            .rs_coupon_compatible_wrapper .search-box{
                margin:10px !important;
            }
            .rs_coupon_compatible_wrapper .displaying-num{
                padding-right:10px !important;
            }

            .rs_coupon_compatible_wrapper .tablenav-pages{
                padding-right:10px !important;
                padding-bottom:10px !important;
            }
            .rs_coupon_compatible_wrapper input[type="text"]{
                box-shadow: 0 -5px 10px 2px rgba(0, 0, 0, 0.03) inset;
                border:none ;
                width:350px !important;
                border:1px solid #ccc ;
                border-radius:5px;
                height:30px !important;
            }        
            .rs_coupon_compatible_wrapper textarea{
                box-shadow: 0 -5px 10px 2px rgba(0, 0, 0, 0.03) inset;
                border:none ;
                width:350px !important;
                border:1px solid #ccc ;
                border-radius:5px;
                height:auto;
            }
            .rs_coupon_compatible_wrapper select{
                box-shadow: 0 -5px 10px 2px rgba(0, 0, 0, 0.03) inset;
                border:none ;
                width:350px !important;
                border:1px solid #ccc ;
                border-radius:5px;
                height:30px;
            }
            .rs_coupon_compatible_wrapper input[type="number"]{
                box-shadow: 0 -5px 10px 2px rgba(0, 0, 0, 0.03) inset;
                border:none ;
                width:350px !important;
                border:1px solid #ccc ;
                border-radius:5px;
                height:30px !important;
            } 
            .rs_coupon_compatible_wrapper label{
                font-weight:500 !important;
            }
            .rs_coupon_compatible_wrapper table th{
                font-weight:500 !important;
            }
            .rs_coupon_compatible_wrapper table{
                width:98%;
                margin-left:15px !important;
                font-family: 'Roboto', sans-serif !important;
            }
            .rs_coupon_compatible_wrapper table label{
                font-family: 'Roboto', sans-serif !important;
            }
            .rs_coupon_compatible_wrapper table td{
                color:#8a929e !important;
            }

            /*        coupons wrapper end*/
            /***********coupons wrapper show/hide************/
            /*            .rs_coupon_compatible_wrapper p{
                            display:none;
                        }
                        .rs_coupon_compatible_wrapper table{
                            display:none;
                        }
                        .rs_coupon_compatible_wrapper div{
                            display:none;
                        }*/
            /***********coupons wrapper show/hide end ************/
            /*        Administrator wrapper*/

            .rs_adminstrator_wrapper{
                width:95%;
                margin:20px auto;
                border: 1px solid #dfdfdf;
                border-radius:5px !important;
                background:#fff !important;
            }
            .rs_adminstrator_wrapper h2{
                background: url(<?php echo SRP_PLUGIN_DIR_URL . "assets/images/arrow-216-12.png" ?>) right 15px center no-repeat, linear-gradient(to bottom, rgba(240,240,240,1) 0%,rgba(255,255,255,1) 50%,rgba(240,240,240,1) 100%) !important;
                border:0px !important;
                font-size:16px !important;
                color:#333 !important;
                font-family: 'Roboto', sans-serif !important;
                margin: 0;
                letter-spacing: 0.5px;
                padding: 12px 0 12px 15px;
                cursor:pointer;
            }
            .rs_adminstrator_wrapper p{
                font-family: 'Roboto', sans-serif !important;
                margin-left:10px;
                font-size:14px !important;
                color:#8a929e !important;
            }
            .rs_adminstrator_wrapper p a{
                font-style:italic;
            }
            .rs_adminstrator_wrapper h3{
                font-family: 'Roboto', sans-serif !important;
                padding-left:10px !important;
            }
            .rs_adminstrator_wrapper p b{
                color:#666 !important;
                font-size:14px !important;
            }
            .rs_adminstrator_wrapper .search-box{
                margin:10px !important;
            }
            .rs_adminstrator_wrapper .displaying-num{
                padding-right:10px !important;
            }

            .rs_adminstrator_wrapper .tablenav-pages{
                padding-right:10px !important;
                padding-bottom:10px !important;
            }
            .rs_adminstrator_wrapper input[type="text"]{
                box-shadow: 0 -5px 10px 2px rgba(0, 0, 0, 0.03) inset;
                border:none ;
                width:350px !important;
                border:1px solid #ccc ;
                border-radius:5px;
                height:30px !important;
            }        
            .rs_adminstrator_wrapper textarea{
                box-shadow: 0 -5px 10px 2px rgba(0, 0, 0, 0.03) inset;
                border:none ;
                width:350px !important;
                border:1px solid #ccc ;
                border-radius:5px;
                height:auto;
            }
            .rs_adminstrator_wrapper select{
                box-shadow: 0 -5px 10px 2px rgba(0, 0, 0, 0.03) inset;
                border:none ;
                width:350px !important;
                border:1px solid #ccc ;
                border-radius:5px;
                height:30px;
            }
            .rs_adminstrator_wrapper input[type="number"]{
                box-shadow: 0 -5px 10px 2px rgba(0, 0, 0, 0.03) inset;
                border:none ;
                width:350px !important;
                border:1px solid #ccc ;
                border-radius:5px;
                height:30px !important;
            } 
            .rs_adminstrator_wrapper label{
                font-weight:500 !important;
            }
            .rs_adminstrator_wrapper table th{
                font-weight:500 !important;
            }
            .rs_adminstrator_wrapper table{
                width:98%;
                margin-left:15px !important;
                font-family: 'Roboto', sans-serif !important;
            }
            .rs_adminstrator_wrapper table label{
                font-family: 'Roboto', sans-serif !important;
            }
            .rs_adminstrator_wrapper table td{
                color:#8a929e !important;
            }

            /*        Adminstrator wrapper end*/
            /***********Administrator wrapper show/hide************/
            /*            .rs_adminstrator_wrapper p{
                            display:none;
                        }
                        .rs_adminstrator_wrapper table{
                            display:none;
                        }
                        .rs_adminstrator_wrapper div{
                            display:none;
                        }*/
            /**********all section hide*************/
            /*            .rs_section_wrapper p{
                            display:none;
                        }
                        .rs_section_wrapper table{
                            display:none;
                        }
                        .rs_section_wrapper .tablenav, .top{
                            display:none;
                        }
            
                        .rs_section_wrapper .tablenav, .bottom{
                            display:none;
                        }
                        .rs_section_wrapper .rs_pagination{
                            display:none;
                        }*/

            /******* settings design wrapper***********/
            .rs_section_wrapper{
                width:95%;
                margin:20px auto;
                border:1px solid #ddd !important;
                background:#fff !important;
            }
            .rs_section_wrapper p{
                font-family: 'Roboto', sans-serif !important;
                font-style:italic;
                margin-left:10px;
                color:#8a929e !important;
                font-size:14px !important;
            }
            .rs_section_wrapper label{
                font-weight:500 !important;
            }
            .rs_section_wrapper table th{
                font-weight:500 !important;
            }
            .rs_section_wrapper h2{
                background:url(<?php echo SRP_PLUGIN_DIR_URL . "assets/images/arrow-216-12.png" ?>) right 15px center no-repeat, linear-gradient(to bottom, rgba(240,240,240,1) 0%,rgba(255,255,255,1) 50%,rgba(240,240,240,1) 100%) !important;
                font-size:16px !important;
                color:#333 !important;
                border-bottom:none !important;
                font-family: 'Roboto', sans-serif !important;
                margin: 0;
                letter-spacing: 0.5px;
                padding: 12px 0 12px 15px;
                cursor: pointer;
            }
            .rs_section_wrapper table{
                width:98%;
                margin-left:15px !important;
                font-family: 'Roboto', sans-serif !important;
            }
            .rs_section_wrapper table label{
                font-family: 'Roboto', sans-serif !important;
            }
            .rs_section_wrapper table th{
                font-weight:500 !important;
            }
            .rs_section_wrapper table td{
                color:#8a929e !important;
            }
            .rs_section_wrapper input[type="text"]{
                box-shadow: 0 -5px 10px 2px rgba(0, 0, 0, 0.03) inset;
                border:none ;
                width:350px !important;
                border:1px solid #ccc ;
                border-radius:5px;
                height:30px !important;
            }
            .rs_section_wrapper input[type="number"]{
                box-shadow: 0 -5px 10px 2px rgba(0, 0, 0, 0.03) inset;
                border:none ;
                width:350px !important;
                border:1px solid #ccc ;
                border-radius:5px;
                height:30px !important;
            }
            .rs_section_wrapper textarea{
                box-shadow: 0 -5px 10px 2px rgba(0, 0, 0, 0.03) inset;
                border:none ;
                width:350px !important;
                border:1px solid #ccc ;
                border-radius:5px;
                height:auto;
            }
            .rs_section_wrapper select{
                box-shadow: 0 -5px 10px 2px rgba(0, 0, 0, 0.03) inset;
                border:none ;
                width:350px !important;
                border:1px solid #ccc ;
                border-radius:5px;
                height:30px;
            }
            .rs_section_wrapper .displaying-num{
                padding-right:10px !important;
            }
            .rs_section_wrapper .widefat{
                width:98% !important;
                margin:7px !important;
            }
            .rs_section_wrapper .search-box{
                margin:10px !important;
            }
            .widefat input[type="text"]{
                box-shadow: none;
                width:auto !important;
                /*            border:1px solid #ccc !important;*/
                /*border-radius:0px;*/
                height:30px !important;
            }
            .widefat input[type="number"]{
                box-shadow: none;
                width:auto !important;
                /*border:1px solid #ccc !important;*/
                /*border-radius:0px;*/
                height:30px !important;
            }
            .widefat select{
                box-shadow: none;
                width:150px !important;
                /*border:1px solid #ccc !important;*/
                /*border-radius:0px;*/
                height:30px !important;
            }
            .rs_save{
                margin-left:25px;
            }
            .rs_section_wrapper h3{
                padding-left:10px !important;
            }
            .rs_section_wrapper p b{
                font-family: 'Roboto', sans-serif !important;
                color:#666 !important;
                font-size:14px !important;
            }
            /***************** settings button design **********************/
            .rs_refresh_button, .rs_upload_button{
                padding: 7px !important;
                border-radius:5px !important;
                color:#000;
                background:#dedee0;
                font-size:14px;
                font-weight: 500 !important;
                border: none !important;
                cursor: pointer !important;
            }
            .rs_refresh_button:hover, .rs_upload_button:hover{
                box-shadow: 0 0 3px #000;
            }
            .rs_imgupload_button{
                padding: 7px !important;
                border-radius:5px !important;
                color:#000;
                background:#dedee0;
                font-size:14px;
                font-weight: 500 !important;
                border: none !important;
                cursor: pointer !important;
            }
            .rs_imgupload_button:hover{
                box-shadow: 0 0 3px #000;
            }
            .rs_export_button{
                padding: 7px 12px !important;
                border-radius:5px !important;
                color:#000;
                background:#dedee0;
                font-size:14px;
                font-weight: 500 !important;
                border: none !important;
                cursor: pointer !important;   
            }
            .rs_export_button:hover{
                box-shadow: 0 0 3px #000;
            }
            .rs_oldpoints_button{
                padding: 7px 12px !important;
                border-radius:5px !important;
                color:#000;
                background:#dedee0;
                font-size:14px;
                font-weight: 500 !important;
                border: none !important;
                cursor: pointer !important;   
            }
            .rs_oldpoints_button:hover{
                box-shadow: 0 0 3px #000;
            }
            .rs_save_btn{
                background:#f55b11 !important;
                color:#fff !important;
                border:none !important;
                font-size:14px !important;
                text-shadow:none !important;
                box-shadow:none !important;
                border-radius:5px !important;
                padding:5px 10px !important;
                height:auto !important;
                font-weight:bold !important;
                font-family: 'Roboto', sans-serif !important;
                margin-left:30px !important;
            }
            .rs_save_btn:hover{
                box-shadow:0 0 3px #000 !important;
            }
            .rs_reset{

                background:#fff !important;
                border:1px solid #f55b11 !important;
                color:#000 !important;
                height:37px !important;
                padding:0px 20px !important;
                font-weight:bold !important;
                box-shadow:none !important;
                font-family: 'Roboto', sans-serif !important;
                /*            background:#fff !important;
                            color:#000 !important;
                            border:1px solid #ccc !important;
                            font-size:14px !important;
                            text-shadow:none !important;
                            box-shadow:none !important;
                            border-radius:5px !important;
                            padding:3px 20px !important;
                            height:30px !important;
                            font-weight:bold !important;
                            font-family: 'Roboto', sans-serif !important;    */
            }
            .rs_reset:hover{
                box-shadow:0 0 3px #000 !important;
                background:#f55b11 !important;
                color:#fff !important;
                border:none !important;
            }
            .rs_email_button{
                padding: 5px 12px !important;
                border-radius:5px !important;
                color:#fff !important;
                height:auto !important;
                line-height: 1.2 !important;
                background:#040633 !important;
                font-size:14px;
                font-weight: 500 !important;
                border: none !important;
                cursor: pointer !important;
                margin-left:10px !important;
            }
            .rs_email_button:hover{
                box-shadow:0 0 3px #000 !important;
            }
            .rs_voucher_codes_btn{
                margin-left:10px !important
            }
            .rs_voucher_codes_btn:hover{
                box-shadow:0 0 3px #000 !important;
            }
            /*****************grid method css**********************/
            .rs_Grid_wrapper{
                float:left;
                margin-top:5px;
                width:100%; 
                background:#fff;
            }
            .rs_Grid_wrapper_inner{
                width:98%;
                margin:0 auto;
            }
            .rs_module_title{
                color: #000;
                font-size: 20px !important;
                text-transform: uppercase;
                font-weight:600 !important;
                padding:20px 20px 10px 20px !important;
                letter-spacing: 1px !important;
                font-family: 'Roboto', sans-serif;
            }
            .rs_module{
                font-weight:400 !important;
                font-family: 'Roboto', sans-serif;
            }
            .rs_grid{
                box-sizing: border-box;
                color: #fff;
                float: left;
                height: 200px;
                max-width: 465px;
                min-width: 340px;
                margin:10px;
                width: 20%;
            }
            .inactive_rs_box {
                background-color:#f5f5f5;
                display: block;
                border-radius: 5px;
                height: 100%;
                overflow: hidden;
                position: relative;
                width: 100%;
                border: 1px solid #bbb;
            }
            .active_rs_box{
                background:#f55b11;
                display: block;
                border-radius: 5px;
                height: 100%;
                overflow: hidden;
                position: relative;
                width: 100%;
                border: 1px solid #bbb;
                box-shadow:0px 0px 8px 0px #ccc;
            }
            .active_rs_box p{
                color:#fff !important;
                font-size:14px !important;
                font-weight:500 !important;
                padding:0px 10px 10px 10px !important;
                margin-top:5px !important;
                line-height: 1.8 !important;
                opacity: 0 !important;
                text-align: center !important;
                letter-spacing: 0.5px !important;
                transition:opacity 0.5s linear !important;
                font-family: 'Roboto', sans-serif !important;
            }
            .active_rs_box:hover p{
                opacity: 1 !important;
            }
            .rs_inactive_hyperlink {
                float:left !important;
                width:100% !important;
                border-bottom:1px solid #bbb !important;
                min-height:150px !important;
                text-align:center !important;
                font-family: 'Roboto', sans-serif !important;
            }
            .rs_active_hyperlink {
                float:left !important;
                width:100% !important;
                border-bottom: 1px solid #ddd !important;
                min-height:150px !important;
                text-align:center !important;
            }
            .rs_inactive_hyperlink h1{
                font-size:18px !important;
                color:#000 !important;
                text-align:left !important;
                padding:12px !important;
                font-weight:bold !important;
                font-family: 'Roboto', sans-serif !important;
            }
            .rs_active_hyperlink h1{
                font-size:18px !important;;
                color:#000 !important;
                text-align:left !important;
                padding:12px !important;
                font-family: 'Roboto', sans-serif !important;
                font-weight:bold !important;
            }
            .bottom_sec{
                margin-top:10px;
                float:left;
                min-width: 340px;
            }
            .bottom_sec label{
                margin-left:10px !important;
            }
            .bottom_sec a{
                color:#fff !important;
                float:right !important;
                text-decoration:none !important;
                font-size:14px !important;
                font-weight: 600 !important;
                margin-right:10px !important;
                background:#2d2d2d !important;
                border:none !important;
                border-radius:15px !important;
                color:#fff !important;
                padding:6px 13px !important;
                font-family: 'Roboto', sans-serif !important;
            }    
            .bottom_sec a:hover{
                color:#fff !important;
                box-shadow:0 0 3px #2d2d2d;
            }
            .bottom_sec label{
                text-align:left;
            }
            /******************module check box button design***********************/
            .rs_switch_round{
                width:70px !important;
                height:30px !important;
                position:relative !important;
                display:inline-block !important;
                -moz-box-shadow:    inset 0 0 6px #eee !important;
                -webkit-box-shadow: inset 0 0 6px #eee !important;
                box-shadow: 0 0 2px #777 inset !important;
                background: #fff !important;
                -webkit-border-radius: 50px !important;
                -moz-border-radius: 50px !important;
                border-radius: 50px !important;
            }
            .rs_switch_round:before{
                content:"ON" !important;
                font-size:10px !important;
                font-weight:bold !important;
                line-height: 1.8 !important;
                top:4px !important;
                left:4px !important;
                position:absolute !important;
                color:#000 !important;
                padding:3px !important;
                font-family: 'Roboto', sans-serif !important;
            }
            .rs_switch_round:after{
                content:"OFF" !important;
                font-size:10px !important;
                top:4px !important;
                line-height: 1.8 !important;
                right:4px !important;
                position:absolute !important;
                font-weight:bold !important;
                color:#000 !important;
                padding:3px !important;
                z-index:1 !important;
                font-family: 'Roboto', sans-serif !important;
            }
            .rs_slider_round{
                position:absolute !important;
                z-index:111 !important;
                height:22px !important;
                width:33px !important;
                left:5px !important;
                top:4px !important;
                background:#999 !important;
                -webkit-box-shadow: 0px 2px 5px 0px rgba(0,0,0,0.3) !important;
                -moz-box-shadow: 0px 2px 5px 0px rgba(0,0,0,0.3) !important;
                box-shadow: 0px 2px 5px 0px rgba(0,0,0,0.3) !important;
                -webkit-transition: all .4s ease !important;
                -moz-transition: all .4s ease !important;
                -o-transition: all .4s ease !important;
                -ms-transition: all .4s ease !important;
                transition: all .4s ease !important;
                -webkit-border-radius: 50px !important;
                -moz-border-radius: 50px !important;
                border-radius: 50px !important;
            }
            .rs_switch_round input{
                display:none !important;
            }

            .rs_switch_round input:checked + .rs_slider_round {
                -webkit-transform: translateX(27px) !important;
                -ms-transform: translateX(27px) !important;
                transform: translateX(27px) !important;
                background-color: #f55b11 !important;
            }
            @media only screen and (max-width:1200px){
                .rs_tab_design a{
                    font-size:11px !important;
                }
            }
            @media only screen and (max-width:1070px){
                .rs_tab_design a{  
                    height: 80px !important;  
                }
            }
            @media only screen and (max-width:782px){
                .rs_section_wrapper input[type="text"]{
                    width:100% !important;
                    height:35px !important;
                }
                .rs_section_wrapper input[type="number"]{
                    width:100% !important;
                    height:35px !important;
                }
                .rs_section_wrapper textarea{
                    width:100% !important;
                }
                .rs_section_wrapper select{
                    width:100% !important;
                    height:35px !important;
                }
                .rs_modulecheck_wrapper input[type="text"]{
                    width:100% !important;
                    height:35px !important;
                }
                .rs_modulecheck_wrapper textarea{
                    width:100% !important;
                }
                .rs_modulecheck_wrapper select{
                    width:100% !important;
                    height:35px !important;
                }
                .rs_modulecheck_wrapper input[type="number"]{
                    width:100% !important;
                    height:35px !important;
                }
                /*            membership wrapper mobile responsive start*/
                .rs_membership_compatible_wrapper input[type="text"]{
                    width:100% !important;
                    height:35px !important;
                }
                .rs_membership_compatible_wrapper textarea{
                    width:100% !important;
                }
                .rs_membership_compatible_wrapper select{
                    width:100% !important;
                    height:35px !important;
                }
                .rs_membership_compatible_wrapper input[type="number"]{
                    width:100% !important;
                    height:35px !important;
                }
                /* Membership wrapper mobile responsive end*/

                /* Waiting List wrapper mobile responsive start*/
                .rs_bsn_compatible_wrapper input[type="text"]{
                    width:100% !important;
                    height:35px !important;
                }
                .rs_bsn_compatible_wrapper textarea{
                    width:100% !important;
                }
                .rs_bsn_compatible_wrapper select{
                    width:100% !important;
                    height:35px !important;
                }
                .rs_bsn_compatible_wrapper input[type="number"]{
                    width:100% !important;
                    height:35px !important;
                }
                /* Waiting List wrapper mobile responsive end*/

                /* Affiliate Pro wrapper mobile responsive start*/
                .rs_affs_compatible_wrapper input[type="text"]{
                    width:100% !important;
                    height:35px !important;
                }
                .rs_affs_compatible_wrapper textarea{
                    width:100% !important;
                }
                .rs_affs_compatible_wrapper select{
                    width:100% !important;
                    height:35px !important;
                }
                .rs_affs_compatible_wrapper input[type="number"]{
                    width:100% !important;
                    height:35px !important;
                }
                /* Affiliate Pro wrapper mobile responsive end*/

                /*            subscriptions wrapper mobile responsive start*/
                .rs_subscription_compatible_wrapper input[type="text"]{
                    width:100% !important;
                    height:35px !important;
                }
                .rs_subscription_compatible_wrapper textarea{
                    width:100% !important;
                }
                .rs_subscription_compatible_wrapper select{
                    width:100% !important;
                    height:35px !important;
                }
                .rs_subscription_compatible_wrapper input[type="number"]{
                    width:100% !important;
                    height:35px !important;
                }
                /*            payment wrapper mobile responsive end*/

                /* subscriptions wrapper mobile responsive start*/
                .rs_payment_plan_compatible_wrapper input[type="text"]{
                    width:100% !important;
                    height:35px !important;
                }
                .rs_payment_plan_compatible_wrapper textarea{
                    width:100% !important;
                }
                .rs_payment_plan_compatible_wrapper select{
                    width:100% !important;
                    height:35px !important;
                }
                .rs_payment_plan_compatible_wrapper input[type="number"]{
                    width:100% !important;
                    height:35px !important;
                }
                /*            payment wrapper mobile responsive end*/
                /*            coupon wrapper mobile responsive start*/
                .rs_coupon_compatible_wrapper input[type="text"]{
                    width:100% !important;
                    height:35px !important;
                }
                .rs_coupon_compatible_wrapper textarea{
                    width:100% !important;
                }
                .rs_coupon_compatible_wrapper select{
                    width:100% !important;
                    height:35px !important;
                }
                .rs_coupon_compatible_wrapper input[type="number"]{
                    width:100% !important;
                    height:35px !important;
                }

                /*            Adminsitrator wrapper mobile responsive start*/
                .rs_adminstrator_wrapper input[type="text"]{
                    width:100% !important;
                    height:35px !important;
                }
                .rs_adminstrator_wrapper textarea{
                    width:100% !important;
                }
                .rs_adminstrator_wrapper select{
                    width:100% !important;
                    height:35px !important;
                }
                .rs_adminstrator_wrapper input[type="number"]{
                    width:100% !important;
                    height:35px !important;
                }
                .rs_sub_tab_design{
                    text-align:left !important;
                }
                .rs_tab_design ul .rs_sub_tab_li a{
                    text-align:left !important;
                    font-size:13px !important;
                    padding-left:10px !important;
                }
                .rs_modulecheck_wrapper .widefat td{
                    width:87% !important;
                    float:left;
                }
            }
            @media only screen and (max-width:767px){
                .rs_main{
                    background:#f1f1f1 !important;
                    width:98% !important;
                }
                .rs_tab_design{
                    margin-top:0px !important;
                    width:100% !important;
                }
                .rs_tab_design{
                    margin-top:0px !important;
                }
                .rs_tab_design a:before{
                    content:'';
                    padding-left:15px !important;
                }

                .rs_section_wrapper h2{
                    font-size:16px !important;
                }
                .rs_modulecheck_wrapper h2{
                    font-size:16px !important;
                }
                .rs_tab_design{
                    margin-top:0px !important;
                    box-shadow: 0 2px 2px rgba(0, 0, 0, 0.5) !important;
                }
                .rs_tab_design a{
                    border-right:none !important;
                    border-top: 1px solid #e8e7e5 !important;
                    background: none !important;
                    text-transform: capitalize;
                    font-weight: 500;
                    float:left !important;
                    color:#333 !important;
                    font-size: 14px !important;
                    letter-spacing: 0.5px;
                    margin:0px !important;
                    height: auto !important;
                    width: 100% !important;
                    padding:5px 0px 5px 0px!important;
                    border-left:none !important;
                    border-bottom:none !important;
                    text-align: left !important;
                    word-wrap: break-word !important;
                    white-space: normal !important;
                }
                .rs_tab_design a:hover{
                    border-bottom:none !important;
                    border-top:1px solid #f55b11 !important;
                }
                .rs_tab_design .nav-tab-active:hover{
                    border-bottom:none !important;
                    border-top:1px solid #f55b11 !important;
                }
                .rs_tab_design .nav-tab-active{
                    border-bottom:none !important;
                    border-top:1px solid #f55b11 !important;
                }
                .rs_main_wrapper{
                    margin-top:0px !important;
                    margin-right:0px !important;
                    margin-bottom:0px !important;
                    margin-left:0px !important;
                }
                .rs_tab_design .rs_sub_tab_design{

                    border-top:1px solid #fff !important;

                }
                .rs_sub_tab_design{
                    text-align:left !important;
                    box-shadow: 0 2px 2px rgba(0, 0, 0, 0.5) !important;
                }
                .rs_sub_tab_li a{
                    text-align:left !important; 
                    font-size:13px !important;
                }
                .rs_save_btn{
                    margin-left:15px !important;
                }
                .rs_modulecheck_wrapper table{
                    margin-left:5px !important;
                }
                .rs_modulecheck_wrapper .search-box{
                    position:relative !important;
                    padding:10px !important;
                    margin:0px !important;
                }
                .rs_tab_design ul .rs_sub_tab_li a{
                    padding-left:5px !important;
                }
                .rs_tab_design ul .rs_sub_tab_li a:before{
                    padding-left:0px !important;
                }
            }
            @media only screen and (max-width:479px){
                .rs_section_wrapper input[type="text"]{
                    width:250px !important;
                    height:35px !important;
                }
                .rs_section_wrapper input[type="number"]{
                    width:250px !important;
                    height:35px !important;
                }
                .rs_section_wrapper textarea{
                    width:250px !important;
                }
                .rs_section_wrapper select{
                    width:250px !important;
                    height:35px !important;
                }
            }
            @media only screen and (max-width:380px){
                .rs_grid{
                    box-sizing: border-box;
                    color: #fff;
                    float: left;
                    height: 170px;
                    max-width: 465px;
                    min-width: 280px;
                    margin:10px;
                    width: 20%;
                }
                .rs_active_hyperlink{
                    min-height:120px !important;
                }
                .rs_inactive_hyperlink{
                    min-height:120px !important;
                }
                .bottom_sec{
                    margin-top:10px;
                    float:left;
                    min-width: 280px;
                }
            }
        </style>
        <?php
    }
    ?>
    <style>
        .welcome_header{
            float:left;
            width:100%;
            border-top:2px solid #f55b11 !important;
            border-bottom: 1px solid #ededed !important;
            /*    box-shadow: 0 1px 0 #fff;*/
            background:#fff !important;
            padding-bottom:10px;
        }
        .welcome_title{
            float:left;
        }
        .welcome_title h1{
            padding:15px 5px 15px 25px !important;
            font-size:24px !important;
            margin: 0.67em 0 !important;
            font-family:sans-serif !important;
            font-weight:normal;
            font-family: 'Roboto', sans-serif !important;
        }
        .branding_logo{
            float:right;
            margin-right:30px !important;
        }
        .branding_logo a{
            background:#fff !important;
            border:none !important;
            box-shadow:none !important;
        }
        .branding_logo a:hover{
            border:none !important;
            background:#fff !important;
        }
        .rs_exp_col input[type='checkbox']{
            display:none;
        }
        .rs_exp_col{
            width:95%;
            margin:20px auto 0px auto;
            height:30px;
        }
        .rs_exp_col label{
            float:right;
            padding:5px 10px;
            border-radius:3px;
            background: linear-gradient(to top left, #ff9400 0%, #ff5f00 100%);
            box-shadow: inset 0 0 3px #f55b11;
            opacity: 1;
            color:#fff;
            font-weight:600;
            font-size:14px;
        }
        .rs_exp_col label:hover{
            box-shadow: 0 0 3px #000;
        }
        @media only screen and (max-width:767px){
            .welcome_title h1{
                padding:15px 0px !important;
                font-size:30px !important;
                background-repeat: no-repeat;
                background-position: right 10px center!important;
                font-family:sans-serif !important;
                font-weight:normal;
                text-align: center !important;
                font-family: 'Roboto', sans-serif !important;
                letter-spacing: 1px !important;
            }
            .welcome_title {
                float: none;
                text-align: center;
            }
            .branding_logo{
                float:none;
                text-align: center;
            }
            .branding_logo a{
                float:none !important;
                text-align: center !important;
            }
        }
    </style>
    <?php
}
