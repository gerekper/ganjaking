jQuery(document).ready(function ($) {
    // quick setup wizard tab handle
    if(jQuery('.betterdocs-setup-wizard').length){
        betterdocsQuickSetupTabs();
    }
    function betterdocsQuickSetupTabs(){
        var skipEmailStep = false;
        // tab click handler
        jQuery('#betterdocs-prev-option').on('click', function(e){
            e.preventDefault();
            betterdocsQswNextPrev(-1);
            betterdocsQuickSetupWizardTabTracking(-1);
        });
        jQuery('#betterdocs-next-option').on('click', function(e){
            e.preventDefault();
            betterdocsQswNextPrev(1);
            if(betterdocsQswValidateForm()){
                if( betterdocsQuickSetupGetTrackNumber() === 0 ) {
                    betterdocsQswOptinSubmit();
                }
                betterdocsQuickSetupWizardTabTracking(1);
            }
        });
        jQuery('#betterdocsqswemailskipbutton').on('click', function(e){
            e.preventDefault();
            skipEmailStep = true;
            betterdocsQswNextPrev(1);
            betterdocsQuickSetupWizardTabTracking(1);
        });

        var currentTab = betterdocsQuickSetupGetTrackNumber(); // Current tab is set to be the first tab (0)
        showTab(currentTab); // Display the current tab
        showTabNav(currentTab); // Display the current tab Nav
        function showTab(n) {
            // This function will display the specified tab of the form...
            var tabList = jQuery(".tab-content"); 
            for(i = 0; i <= tabList.length; i++ ){
                if(i === n){
                    jQuery(tabList[i]).addClass('active');
                } else {
                    jQuery(tabList[i]).removeClass('active');
                }
            }
            //... and fix the Previous/Next buttons:
            if (n == 0) {
                document.getElementById("betterdocs-prev-option").style.display = "none";
                jQuery('.bottom-notice-left').show();
                jQuery('#betterdocsqswemailskipbutton').show();
            } else {
                document.getElementById("betterdocs-prev-option").style.display = "inline";
                jQuery('.bottom-notice-left').hide();
                jQuery('#betterdocsqswemailskipbutton').hide();
            }
            if (n == (tabList.length - 1)) {
                document.getElementById("betterdocs-next-option").innerHTML = "Finish";
                // document.getElementById("betterdocs-next-option").classList.add("betterdocs-quick-finish");
                return false;
            } else {
                document.getElementById("betterdocs-next-option").innerHTML = "Next";
            }
        }
        function showTabNav(n){
            var tabNavList = jQuery('.nav-item');
            for(i = 0; i <= tabNavList.length; i++ ){
                if(i <= n){
                    jQuery(tabNavList[i]).addClass('tab-active');
                }else {
                    if(jQuery(tabNavList[i]).hasClass('tab-active')){
                        jQuery(tabNavList[i]).removeClass('tab-active');
                    }
                }
            }
        }

        function betterdocsQswNextPrev(n) {
            // This function will figure out which tab to display
            var x = document.getElementsByClassName("tab-content"); 
           
            // Exit the function if any field in the current tab is invalid:
            if (n == 1 && !betterdocsQswValidateForm() && !skipEmailStep) return false;

            // Increase or decrease the current tab by 1:
            currentTab = currentTab + n;
            if(currentTab < x.length){
                // Otherwise, display the correct tab:
                showTab(currentTab);
                // display tab nav
                showTabNav(currentTab);
            } else {
                // ajax call after false
                betterdocsQswOptionSubmit();
                swal({
                    title: "Good job!",
                    text: "Setup is Complete.",
                    icon: "success",
                }).then(function() {
                    // currentTab = ( x.length - 1);
                    // document.cookie = "currenttab=" + currentTab;
                    // document.cookie = "tracking=" + betterdocsQuickSetupWizardTabTracking(currentTab);
                    window.location = "admin.php?page=betterdocs-settings";
                });
                currentTab = ( x.length - 1);
                if(localStorage.getItem('betterdocsQswTabNumberTracking') <= 5){
                    betterdocsQuickSetupWizardTabTracking(-2);
                } else {
                    betterdocsQuickSetupWizardTabTracking(-1);
                }
            }
        }

        function betterdocsQswValidateForm() {
            var valid = true;
            // if(jQuery('#betterdocs_user_email_address').hasClass('invalid')){
            //     valid = false;
            // } else {
            //     valid = true;
            // }
            return valid; 
        }
    }

    // Quick Setup Wizard Save
    function betterdocsQswOptionSubmit(){
        var ajaxnonce  = $('.betterdocs-setup-wizard input[name="betterdocsqswnonce"]').val();
        var builtin_doc_page  = $('.betterdocs-setup-wizard input[name="builtin_doc_page"]').attr("checked") ? 1 : 'off';
        var enable_disable  = $('.betterdocs-setup-wizard input[name="enable_disable"]').attr("checked") ? 1 : 'off';
        var docs_slug  = $('.betterdocs-setup-wizard input[name="docs_slug"]').val();
        
        if($(".betterdocs-setup-wizard .activate_plugin").is(':checked')){
            var activate_plugin  = $('.betterdocs-setup-wizard .activate_plugin').val();
        } else {
            var activate_plugin  = '';
        }
        
        var data = {
			'action': 'better_docs_quick_setup_wizard_data_save',
			'security': ajaxnonce,
			'activate_plugin': activate_plugin,
			'builtin_doc_page': builtin_doc_page,
			'docs_slug': docs_slug,
			'enable_disable': enable_disable,
		};
		// since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
		jQuery.post(ajaxurl, data, function(response) {});
        
    };

    function betterdocsQswOptinSubmit(){
        var ajaxnonce  = $('.betterdocs-setup-wizard input[name="betterdocsqswnonce"]').val();
        var data = {
            "nonce" : ajaxnonce,
            'action': 'optin_wizard_action_betterdocs',
            "admin_email" : $('#betterdocs_user_email_address').val(),
        };
        jQuery.post(ajaxurl, data, function(response) {});
    }

    // popup modal showing for error message
    $('.betterdocs-pro-feature-checkbox label').on('click', function(){
        var premium_content = document.createElement("p");
        var premium_anchor = document.createElement("a");

        premium_anchor.setAttribute('href', 'https://betterdocs.co/');
        premium_anchor.innerText = 'Premium';
        premium_anchor.style.color = 'red';
        var pro_label = $(this).find('.nx-pro-label');
        if (pro_label.hasClass('has-to-update')) {
            premium_anchor.innerText = 'Latest Pro v' + pro_label.text().toString().replace(/[ >=<]/g, '');
        }
        premium_content.innerHTML = 'You need to upgrade to the <strong>' + premium_anchor.outerHTML + ' </strong> Version to use this module.';

        swal({
            title: "Opps...",
            content: premium_content,
            icon: "warning",
            buttons: [false, "Close"],
            dangerMode: true,
        });
   });
   
    /**
     * quick setup wizard tab tracking
     */
    function betterdocsQuickSetupWizardTabTracking(tabNumber){
        var allTabs = jQuery('.betterdocs-tabnav-wrap ul.tab-nav li.nav-item');
        var existing = localStorage.getItem('betterdocsQswTabNumberTracking');
        existing = parseInt(existing) ? parseInt(existing) : 0;
        if(parseInt(existing) < allTabs.length){
            existing = existing + tabNumber;
            localStorage.setItem('betterdocsQswTabNumberTracking', existing);
        } else if(parseInt(existing) >= allTabs.length) {
            localStorage.setItem('betterdocsQswTabNumberTracking', allTabs.length - 1);
        }
        return parseInt(existing);
    }
    /***
     * Get Current Number
     */
    function betterdocsQuickSetupGetTrackNumber(){
        var oldNumber = localStorage.getItem('betterdocsQswTabNumberTracking');
        var allTabs = jQuery('.betterdocs-tabnav-wrap ul.tab-nav li.nav-item');
        if(parseInt(oldNumber) >= allTabs.length) {
            localStorage.setItem('betterdocsQswTabNumberTracking', allTabs.length - 1);
        }
        return (oldNumber ? parseInt(oldNumber) : 0);
    }

    // email input field length checking
    jQuery('#betterdocs_user_email_address').on('click', function(e){
        if(jQuery(this).prop('checked') === true){
            $(this).removeClass('invalid');
        }else {
            $(this).addClass('invalid');
        }
    });

    // collect Toggle
    jQuery('.btn-collect').on('click', function(){
        jQuery('p.whatwecollecttext').toggle();
    });

    
    // change url
    betterdocsSetupWizardSetCustomizerUrl();
    function betterdocsSetupWizardSetCustomizerUrl(){
        jQuery('input[name="docs_slug"]').on('change', function(){
            var data = {
                'action': 'better_docs_setup_generate_live_url',
                'docs_slug':  jQuery(this).val(),
            };
            // since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
            jQuery.post(ajaxurl, data, function(response) {
                var allResponse = JSON.parse(response);
                jQuery('#bdgotocustomize').attr('href', allResponse.customizerurl);
                jQuery('#bdgotodocspage').attr('href', allResponse.siteurl);
            });
        });
    }
});

